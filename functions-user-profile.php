<?php
/**
 * Funções para a página de perfil de usuário do dashboard.
 */

// Evita o acesso direto ao arquivo.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Funções para Login e Reset de Senha com CPF - VERSÃO CORRIGIDA
 * Design premium com ícones SVG inline
 * Sem dependências de arquivos externos
 */

/**
 * Função SIMPLIFICADA para buscar usuário por CPF
 * Converte input para o formato salvo no ACF: 982.316.343-04
 */
function amedis_find_user_by_cpf($cpf_input) {
    // Remove tudo que não é número
    $cpf_limpo = preg_replace('/\D/', '', $cpf_input);
    
    // Se não tem 11 dígitos, retorna vazio
    if (strlen($cpf_limpo) !== 11) {
        return [];
    }
    
    // Formata no padrão ACF: 982.316.343-04
    $cpf_formatado = substr($cpf_limpo, 0, 3) . '.' . 
                    substr($cpf_limpo, 3, 3) . '.' . 
                    substr($cpf_limpo, 6, 3) . '-' . 
                    substr($cpf_limpo, 9, 2);
    
    // Busca exatamente como está salvo no ACF
    $users = get_users([
        'meta_key'   => 'cpf',
        'meta_value' => $cpf_formatado,
        'number'     => 1
    ]);
    
    return $users;
}

/**
 * Função SIMPLIFICADA para buscar usuário por CPF e Celular
 * Converte inputs para os formatos salvos no ACF
 */
function amedis_find_user_by_cpf_celular($cpf_input, $celular_input) {
    // Limpa CPF e formata: 982.316.343-04
    $cpf_limpo = preg_replace('/\D/', '', $cpf_input);
    if (strlen($cpf_limpo) !== 11) {
        return [];
    }
    $cpf_formatado = substr($cpf_limpo, 0, 3) . '.' . 
                    substr($cpf_limpo, 3, 3) . '.' . 
                    substr($cpf_limpo, 6, 3) . '-' . 
                    substr($cpf_limpo, 9, 2);
    
    // Limpa celular e formata: (85) 98953-0974
    $celular_limpo = preg_replace('/\D/', '', $celular_input);
    if (strlen($celular_limpo) < 10 || strlen($celular_limpo) > 11) {
        return [];
    }
    
    // Formata celular conforme salvo no ACF
    if (strlen($celular_limpo) === 11) {
        // Celular com 9 dígitos: (85) 98953-0974
        $celular_formatado = '(' . substr($celular_limpo, 0, 2) . ') ' . 
                            substr($celular_limpo, 2, 5) . '-' . 
                            substr($celular_limpo, 7, 4);
    } else {
        // Celular com 8 dígitos: (85) 8953-0974
        $celular_formatado = '(' . substr($celular_limpo, 0, 2) . ') ' . 
                            substr($celular_limpo, 2, 4) . '-' . 
                            substr($celular_limpo, 6, 4);
    }
    
    // Busca exatamente como está salvo no ACF
    $users = get_users([
        'meta_query' => [
            'relation' => 'AND',
            ['key' => 'cpf', 'value' => $cpf_formatado, 'compare' => '='],
            ['key' => 'celular', 'value' => $celular_formatado, 'compare' => '=']
        ],
        'number' => 1
    ]);
    
    return $users;
}

// =================================================================================
// SHORTCODE PARA RESET DE SENHA [cpf_pwd_reset]
// =================================================================================
add_shortcode('cpf_pwd_reset', 'cpf_pwd_reset_shortcode');
function cpf_pwd_reset_shortcode() {
    // Se já estiver logado, não mostra o formulário.
    if (is_user_logged_in()) {
        return '<div class="form-message success">Você já está conectado.</div>';
    }

    ob_start();

    // Processa o envio do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // PASSO 1: Validação de CPF + Celular
        if (!empty($_POST['cpf_pwd_step']) && $_POST['cpf_pwd_step'] === '1') {
            if (!wp_verify_nonce($_POST['cpf_pwd_nonce'] ?? '', 'cpf_pwd_step1')) {
                echo '<div class="form-message error">Falha de segurança. Por favor, tente novamente.</div>';
            } else {
                $cpf_input = sanitize_text_field($_POST['cpf']);
                $celular_input = sanitize_text_field($_POST['telefone']);
                
                // Validação básica
                if (empty($cpf_input) || empty($celular_input)) {
                    echo '<div class="form-message error">Por favor, preencha todos os campos.</div>';
                    echo cpf_pwd_render_step1();
                    return ob_get_clean();
                }
                
                // Busca usuário com os dados formatados
                $users = amedis_find_user_by_cpf_celular($cpf_input, $celular_input);
                
                if (empty($users)) {
                    // Verifica se pelo menos o CPF existe
                    $users_cpf = amedis_find_user_by_cpf($cpf_input);
                    
                    if (empty($users_cpf)) {
                        echo '<div class="form-message error">CPF não encontrado.</div>';
                    } else {
                        echo '<div class="form-message error">Celular não confere com o CPF informado.</div>';
                    }
                    
                    echo cpf_pwd_render_step1();
                    return ob_get_clean();
                }

                // Usuário encontrado - prossegue para o passo 2
                $user = $users[0];
                $token = wp_generate_password(20, false, false);
                update_user_meta($user->ID, 'cpf_reset_token', $token);
                update_user_meta($user->ID, 'cpf_reset_time', time());
                echo cpf_pwd_render_step2($user->ID, $token, $user->display_name);
            }
        }
        // PASSO 2: Validação do token e troca de senha
        elseif (!empty($_POST['cpf_pwd_step']) && $_POST['cpf_pwd_step'] === '2') {
            if (!wp_verify_nonce($_POST['cpf_pwd_nonce'] ?? '', 'cpf_pwd_step2')) {
                echo '<div class="form-message error">Falha de segurança. Por favor, tente novamente.</div>';
            } else {
                $user_id = intval($_POST['user_id']);
                $token = sanitize_text_field($_POST['token']);
                $saved_token = get_user_meta($user_id, 'cpf_reset_token', true);
                $token_time = get_user_meta($user_id, 'cpf_reset_time', true);

                // Validação simples do token
                if (empty($saved_token) || $token !== $saved_token) {
                    echo '<div class="form-message error">Token inválido. Por favor, inicie o processo novamente.</div>';
                    echo cpf_pwd_render_step1();
                    return ob_get_clean();
                }
                
                $pass1 = $_POST['password'];
                $pass2 = $_POST['password2'];

                if (strlen($pass1) < 4) {
                    echo '<div class="form-message error">A senha deve ter no mínimo 4 caracteres.</div>';
                    echo cpf_pwd_render_step2($user_id, $token, get_userdata($user_id)->display_name);
                    return ob_get_clean();
                } elseif ($pass1 !== $pass2) {
                    echo '<div class="form-message error">As senhas não conferem.</div>';
                    echo cpf_pwd_render_step2($user_id, $token, get_userdata($user_id)->display_name);
                    return ob_get_clean();
                } else {
                    wp_set_password($pass1, $user_id);
                    delete_user_meta($user_id, 'cpf_reset_token');
                    delete_user_meta($user_id, 'cpf_reset_time');
                    echo '<div class="form-message success">Senha alterada com sucesso! Você já pode <a href="' . esc_url(site_url('/login')) . '" class="font-bold underline">fazer login</a>.</div>';
                }
            }
        }
    } else {
        // Se não for um POST, mostra o formulário inicial
        echo cpf_pwd_render_step1();
    }

    return ob_get_clean();
}

// Renderiza o formulário do Passo 1 (Pedir CPF e Celular)
function cpf_pwd_render_step1() {
    ob_start();
    ?>
    <div style="max-width: 400px; margin: 2rem auto; padding: 2rem; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="<?php echo hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes'); ?>" class="h-16 mb-5 mx-auto" alt="Logo ASSOC">

            <h1 style="font-size: 1.5rem; font-weight: 600; color: #777; margin: 0 0 0.5rem 0; line-height: 1.2;">Recuperar Senha</h1>
            <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin: 0;">Informe seu CPF e celular para continuar com a recuperação da sua senha.</p>
        </div>
        
        <form method="post" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php wp_nonce_field('cpf_pwd_step1', 'cpf_pwd_nonce'); ?>
            <input type="hidden" name="cpf_pwd_step" value="1">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="cpf" style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">CPF</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <div style="position: absolute; left: 12px; z-index: 2; color: #9ca3af; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required data-mask="cpf" style="width: 100%; height: 40px; padding: 0 12px 0 40px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; background: white; transition: all 0.2s ease; outline: none;" onfocus="this.style.borderColor='#16a249'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="celular" style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">Celular</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <div style="position: absolute; left: 12px; z-index: 2; color: #9ca3af; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <input type="text" id="celular" name="telefone" placeholder="(00) 00000-0000" required data-mask="celular" style="width: 100%; height: 40px; padding: 0 12px 0 40px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; background: white; transition: all 0.2s ease; outline: none;" onfocus="this.style.borderColor='#16a249'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                </div>
            </div>

            <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; height: 40px; padding: 0 1rem; background: #16a249; color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease; outline: none;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#16a249'" onfocus="this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'" onblur="this.style.boxShadow='none'">
                <span>Continuar</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

// Renderiza o formulário do Passo 2 (Nova Senha)
function cpf_pwd_render_step2($user_id, $token, $display_name) {
    ob_start();
    ?>
    <div style="max-width: 400px; margin: 2rem auto; padding: 2rem; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: #10b981; color: white; border-radius: 12px; margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 600; color: #1e293b; margin: 0 0 0.5rem 0; line-height: 1.2;">Olá, <?php echo esc_html($display_name); ?>!</h1>
            <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin: 0;">Agora você pode criar sua nova senha de acesso. Escolha uma senha segura com pelo menos 4 caracteres.</p>
        </div>
        
        <form method="post" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php wp_nonce_field('cpf_pwd_step2', 'cpf_pwd_nonce'); ?>
            <input type="hidden" name="cpf_pwd_step" value="2">
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
            <input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="password" style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">Nova Senha</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <div style="position: absolute; left: 12px; z-index: 2; color: #9ca3af; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" placeholder="••••••••" required style="width: 100%; height: 40px; padding: 0 40px 0 40px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; background: white; transition: all 0.2s ease; outline: none;" onfocus="this.style.borderColor='#16a249'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 12px; z-index: 2; background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; border-radius: 4px; transition: color 0.2s ease;" onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Mínimo de 4 caracteres</p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="password2" style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">Confirme a Nova Senha</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <div style="position: absolute; left: 12px; z-index: 2; color: #9ca3af; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <input type="password" id="password2" name="password2" placeholder="••••••••" required style="width: 100%; height: 40px; padding: 0 12px 0 40px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; background: white; transition: all 0.2s ease; outline: none;" onfocus="this.style.borderColor='#16a249'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                </div>
            </div>

            <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; height: 40px; padding: 0 1rem; background: #16a249; color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease; outline: none;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#16a249'" onfocus="this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'" onblur="this.style.boxShadow='none'">
                <span>Alterar Senha</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Manipula o login via AJAX
 */
function amedis_handle_ajax_login() {
    // Log para debug
    error_log('AJAX Login called with data: ' . print_r($_POST, true));
    
    // Verifica se os dados foram enviados
    if (!isset($_POST['nonce']) || !isset($_POST['cpf']) || !isset($_POST['password'])) {
        wp_send_json_error(['message' => 'Dados incompletos.']);
        return;
    }
    
    // Verifica o nonce
    if (!wp_verify_nonce($_POST['nonce'], 'cpf_login_action')) {
        wp_send_json_error(['message' => 'Falha de segurança. Por favor, tente novamente.']);
        return;
    }

    $cpf_input = sanitize_text_field($_POST['cpf']);
    $password = $_POST['password'];
    
    // Validação básica
    if (empty($cpf_input) || empty($password)) {
        wp_send_json_error(['message' => 'Por favor, preencha todos os campos.']);
        return;
    }
    
    // Busca usuário por CPF
    $users = amedis_find_user_by_cpf($cpf_input);

    if (empty($users)) {
        wp_send_json_error(['message' => 'CPF não cadastrado.']);
        return;
    }
    
    $user = $users[0];
    $creds = [
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => true
    ];
    $signon = wp_signon($creds, false);

    if (is_wp_error($signon)) {
        wp_send_json_error(['message' => 'Senha incorreta. <a href="' . esc_url(site_url('/reset-senha')) . '" class="text-primary hover:underline">Esqueceu a senha?</a>']);
        return;
    }
    
    // Login bem-sucedido
    wp_send_json_success([
        'message' => 'Login realizado com sucesso!',
        'redirect' => $_SERVER['HTTP_REFERER'] ?? home_url()
    ]);
}

// Registra as ações AJAX para usuários logados e não logados
add_action('wp_ajax_amedis_login', 'amedis_handle_ajax_login');
add_action('wp_ajax_nopriv_amedis_login', 'amedis_handle_ajax_login');

/**
 * Manipula o logout via AJAX para evitar dupla confirmação
 */
function amedis_handle_ajax_logout() {
    // Verifica o nonce
    if (!wp_verify_nonce($_POST['security'] ?? '', 'user_profile_nonce')) {
        wp_send_json_error(['message' => 'Falha de segurança.']);
        return;
    }
    
    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuário não está logado.']);
        return;
    }
    
    // Efetua o logout
    wp_logout();
    
    // Retorna sucesso com URL de redirecionamento
    wp_send_json_success([
        'message' => 'Logout realizado com sucesso!',
        'redirect' => wp_login_url()
    ]);
}

// Registra a ação AJAX para logout
add_action('wp_ajax_amedis_logout', 'amedis_handle_ajax_logout');

/**
 * Manipula a atualização de senha via AJAX
 */
function amedis_handle_password_update() {
    try {
        // Log da tentativa de atualização de senha para debug
        error_log('Password update attempt - User ID: ' . get_current_user_id());
        
        // Verificação de segurança - nonce
        if (!wp_verify_nonce($_POST['security'] ?? '', 'user_profile_nonce')) {
            error_log('Password update failed: Invalid nonce');
            wp_send_json_error([
                'message' => 'Falha de segurança. Por favor, recarregue a página e tente novamente.',
                'code' => 'invalid_nonce'
            ]);
            return;
        }
        
        // Verificação de autorização - usuário logado
        if (!is_user_logged_in()) {
            error_log('Password update failed: User not logged in');
            wp_send_json_error([
                'message' => 'Você precisa estar logado para alterar a senha.',
                'code' => 'unauthorized'
            ]);
            return;
        }
        
        $user_id = get_current_user_id();
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Validação de campos obrigatórios
        if (empty($password) || empty($password_confirm)) {
            error_log('Password update failed: Empty fields - User ID: ' . $user_id);
            wp_send_json_error([
                'message' => 'Por favor, preencha todos os campos de senha.',
                'code' => 'empty_fields'
            ]);
            return;
        }
        
        // Validação de confirmação de senha
        if ($password !== $password_confirm) {
            error_log('Password update failed: Password mismatch - User ID: ' . $user_id);
            wp_send_json_error([
                'message' => 'As senhas não coincidem. Por favor, verifique e tente novamente.',
                'code' => 'passwords_mismatch'
            ]);
            return;
        }
        
        // Validação de força da senha
        if (strlen($password) < 6) {
            error_log('Password update failed: Weak password - User ID: ' . $user_id);
            wp_send_json_error([
                'message' => 'A senha deve ter pelo menos 6 caracteres para maior segurança.',
                'code' => 'weak_password'
            ]);
            return;
        }
        
        // Validação adicional de força da senha
        if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)/', $password)) {
            error_log('Password update failed: Password too simple - User ID: ' . $user_id);
            wp_send_json_error([
                'message' => 'A senha deve conter pelo menos uma letra e um número.',
                'code' => 'password_too_simple'
            ]);
            return;
        }
        
        // Tentativa de atualização da senha
        $update_result = wp_set_password($password, $user_id);
        
        // Verifica se houve erro na atualização
        if (is_wp_error($update_result)) {
            error_log('Password update failed: Database error - User ID: ' . $user_id . ' - Error: ' . $update_result->get_error_message());
            wp_send_json_error([
                'message' => 'Erro interno do servidor. Por favor, tente novamente em alguns minutos.',
                'code' => 'database_error'
            ]);
            return;
        }
        
        // Log de sucesso
        error_log('Password update successful - User ID: ' . $user_id);
        
        // Retorna sucesso
        wp_send_json_success([
            'message' => 'Senha alterada com sucesso! Sua nova senha já está ativa.',
            'code' => 'success'
        ]);
        
    } catch (Exception $e) {
        // Log do erro inesperado
        error_log('Password update unexpected error - User ID: ' . get_current_user_id() . ' - Error: ' . $e->getMessage());
        
        wp_send_json_error([
            'message' => 'Ocorreu um erro inesperado. Por favor, tente novamente ou entre em contato com o suporte.',
            'code' => 'unknown_error'
        ]);
    }
}

// Registra a ação AJAX para atualização de senha (using the enhanced handler)
// add_action('wp_ajax_update_user_password', 'amedis_handle_password_update'); // Disabled - using enhanced version below

/**
 * Manipula o logging de erros do frontend
 */
function amedis_handle_frontend_error_logging() {
    // Verifica o nonce
    if (!wp_verify_nonce($_POST['security'] ?? '', 'user_profile_nonce')) {
        wp_send_json_error(['message' => 'Falha de segurança.']);
        return;
    }
    
    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Não autorizado.']);
        return;
    }
    
    $error_data = sanitize_text_field($_POST['error_data'] ?? '');
    
    if (!empty($error_data)) {
        // Log do erro do frontend
        error_log('Frontend Error - User ID: ' . get_current_user_id() . ' - Data: ' . $error_data);
    }
    
    wp_send_json_success(['message' => 'Error logged']);
}

// Registra a ação AJAX para logging de erros do frontend
add_action('wp_ajax_log_frontend_error', 'amedis_handle_frontend_error_logging');

/**
 * Enfileira os scripts e estilos necessários para a página de perfil.
 * Também localiza o script para passar dados do PHP para o JavaScript.
 */
function amedis_enqueue_user_profile_scripts() {
    // Verifica se estamos na página correta antes de enfileirar.
    // Adapte esta verificação se a sua lógica de carregamento de página for diferente.
    // Carrega em qualquer página que use o template dashboard-user-profile.php ou contenha 'dashboard' no nome
    if (is_page('dashboard') || is_page_template('dashboard-user-profile.php') || is_page_template('dashboard-profiles.php') || basename(get_page_template()) === 'dashboard-user-profile.php') {
        // Só enfileira se o usuário estiver logado (para o dashboard)
        if (is_user_logged_in()) {
            wp_enqueue_script(
                'amedis-user-profile',
                get_stylesheet_directory_uri() . '/assets/js/dashboard-user-profile.js',
                ['jquery'],
                filemtime(get_stylesheet_directory() . '/assets/js/dashboard-user-profile.js'),
                true
            );
            // Passa variáveis do PHP para o JavaScript de forma segura.
            wp_localize_script(
                'amedis-user-profile',
                'userProfileAjax',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('user_profile_nonce')
                ]
            );
            // Enfileira o CSS premium
            wp_enqueue_style(
                'amedis-custom-premium',
                get_stylesheet_directory_uri() . '/assets/custom.css',
                [],
                filemtime(get_stylesheet_directory() . '/assets/custom.css')
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'amedis_enqueue_user_profile_scripts');

/**
 * Força o carregamento dos scripts na página de dashboard
 */
function amedis_force_dashboard_scripts() {
    // Se estamos na página de dashboard e o script não foi carregado, força o carregamento
    if ((is_page('dashboard') || basename($_SERVER['PHP_SELF']) === 'dashboard-user-profile.php') && is_user_logged_in()) {
        if (!wp_script_is('amedis-user-profile', 'enqueued')) {
            wp_enqueue_script(
                'amedis-user-profile',
                get_stylesheet_directory_uri() . '/assets/js/dashboard-user-profile.js',
                ['jquery'],
                filemtime(get_stylesheet_directory() . '/assets/js/dashboard-user-profile.js'),
                true
            );
            
            wp_localize_script(
                'amedis-user-profile',
                'userProfileAjax',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('user_profile_nonce')
                ]
            );
        }
    }
}
add_action('wp_head', 'amedis_force_dashboard_scripts');

/**
 * Busca os dados do perfil do usuário logado.
 *
 * @return array Um array com os dados do usuário.
 */
function amedis_get_user_profile_data() {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return [];
    }

    $user_data = get_userdata($user_id);
    $user_meta = get_user_meta($user_id);

    // Obtém campos ACF se disponível
    $acf_data = [];
    if (function_exists('get_field')) {
        $acf_data = [
            'nome_completo' => get_field('nome_completo', 'user_'.$user_id),
            'email' => get_field('email', 'user_'.$user_id),
            'cpf' => get_field('cpf', 'user_'.$user_id),
            'telefone' => get_field('telefone', 'user_'.$user_id),
            'celular' => get_field('celular', 'user_'.$user_id),
            'cidade' => get_field('cidade', 'user_'.$user_id),
            'endereco' => get_field('endereco', 'user_'.$user_id),
            'bairro' => get_field('bairro', 'user_'.$user_id),
            'data_admissao' => get_field('data_admissao', 'user_'.$user_id),
            'observacoes' => get_field('observacoes', 'user_'.$user_id),
            'associado' => get_field('associado', 'user_'.$user_id),
            'associado_ativado' => get_field('associado_ativado', 'user_'.$user_id),
            'tipo_associacao' => get_field('tipo_associacao', 'user_'.$user_id),
            'idconjuge' => get_field('idconjuge', 'user_'.$user_id),
            'idfilho01' => get_field('idfilho01', 'user_'.$user_id),
            'idfilho02' => get_field('idfilho02', 'user_'.$user_id),
            'responsavel' => get_field('responsavel', 'user_'.$user_id),
            'responsavel_atendimento' => get_field('responsavel_atendimento', 'user_'.$user_id),
        ];
    }

    return array_merge([
        'user_login'      => $user_data->user_login,
        'user_email'      => $user_data->user_email,
        'first_name'      => $user_meta['first_name'][0] ?? '',
        'last_name'       => $user_meta['last_name'][0] ?? '',
        'description'     => $user_meta['description'][0] ?? '',
        'user_avatar'     => get_avatar_url($user_id),
        // Adicione outros campos que você precisar aqui.
        'telefone'        => $user_meta['billing_phone'][0] ?? ''
    ], $acf_data);
}

/**
 * Busca estatísticas do usuário (pedidos e valores).
 *
 * @return array Estatísticas do usuário.
 */
function amedis_get_user_stats() {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return [
            'total_orders' => 0,
            'total_value' => 0,
            'total_value_formatted' => 'R$ 0,00',
            'active_prescriptions' => 0,
            'account_status' => 'Inativo'
        ];
    }

    // Verifica se o WooCommerce está ativo
    if (!function_exists('wc_get_orders')) {
        return [
            'total_orders' => 0,
            'total_value' => 0,
            'total_value_formatted' => 'R$ 0,00',
            'active_prescriptions' => 0,
            'account_status' => 'Ativo'
        ];
    }

    // Busca todos os pedidos do usuário
    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'limit' => -1,
        'status' => ['completed', 'processing', 'on-hold'] // Apenas pedidos válidos
    ]);

    $total_orders = count($orders);
    $total_value = 0;

    // Calcula o valor total dos pedidos
    foreach ($orders as $order) {
        $total_value += $order->get_total();
    }

    // Busca receitas ativas (assumindo que são posts customizados ou meta do usuário)
    $active_prescriptions = amedis_get_user_active_prescriptions_count($user_id);

    return [
        'total_orders' => $total_orders,
        'total_value' => $total_value,
        'total_value_formatted' => wc_price($total_value),
        'active_prescriptions' => $active_prescriptions,
        'account_status' => 'Ativo'
    ];
}

/**
 * Conta receitas ativas do usuário.
 * Esta função pode ser adaptada conforme sua estrutura de receitas.
 *
 * @param int $user_id ID do usuário.
 * @return int Número de receitas ativas.
 */
function amedis_get_user_active_prescriptions_count($user_id) {
    // Busca posts do tipo 'receitas' vinculados ao usuário via ACF id_paciente_receita
    $prescriptions = get_posts([
        'post_type'   => 'receitas',
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_query'  => [
            [
                'key'     => 'id_paciente_receita',
                'value'   => $user_id,
                'compare' => '='
            ]
        ],
        'orderby' => 'date',
        'order'   => 'DESC'
    ]);

    if (empty($prescriptions)) {
        return 0;
    }

    // Considerar ATIVAS as receitas com data_vencimento hoje ou futura
    // Inclui as "expirando" (<= 5 dias) como ativas por definição aprovada
    $ativos = 0;

    // Obtém a data atual no timezone do WP
    $timezone = wp_timezone();
    $hoje = new DateTime('now', $timezone);
    $hoje->setTime(0, 0, 0);

    foreach ($prescriptions as $post) {
        $data_vencimento = get_field('data_vencimento', $post->ID);

        if (empty($data_vencimento)) {
            // Se não houver data, por segurança NÃO conta como ativa
            // Caso deseje considerar ativa quando vazio, troque para: $ativos++;
            continue;
        }

        // data_vencimento vem como dd/mm/aaaa
        $vencimento_parts = explode('/', $data_vencimento);
        if (count($vencimento_parts) !== 3) {
            continue;
        }

        try {
            $data_venc = new DateTime($vencimento_parts[2] . '-' . $vencimento_parts[1] . '-' . $vencimento_parts[0], $timezone);
            $data_venc->setTime(23, 59, 59);

            if ($data_venc >= $hoje) {
                $ativos++;
            }
        } catch (Exception $e) {
            // Data inválida, não conta
            continue;
        }
    }

    return $ativos;
}

/**
 * Manipula o upload de avatar via AJAX
 */
function amedis_handle_avatar_upload() {
    // Verifica o nonce de segurança
    if (!wp_verify_nonce($_POST['avatar_nonce'] ?? '', 'avatar_upload_nonce')) {
        wp_send_json_error(['message' => 'Falha de segurança. Por favor, tente novamente.']);
        return;
    }
    
    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuário não está logado.']);
        return;
    }
    
    // Verifica se um arquivo foi enviado
    if (empty($_FILES['avatar_file'])) {
        wp_send_json_error(['message' => 'Nenhum arquivo foi enviado.']);
        return;
    }
    
    $file = $_FILES['avatar_file'];
    
    // Validações básicas do arquivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'Erro no upload do arquivo.']);
        return;
    }
    
    // Verifica se é uma imagem
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error(['message' => 'Tipo de arquivo não permitido. Use apenas JPG, PNG, GIF ou WebP.']);
        return;
    }
    
    // Verifica o tamanho do arquivo (máximo 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB em bytes
    if ($file['size'] > $max_size) {
        wp_send_json_error(['message' => 'Arquivo muito grande. O tamanho máximo é 5MB.']);
        return;
    }
    
    // Verifica se é realmente uma imagem usando getimagesize
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        wp_send_json_error(['message' => 'O arquivo enviado não é uma imagem válida.']);
        return;
    }
    
    // Prepara o array para wp_handle_upload
    $upload_overrides = [
        'test_form' => false,
        'unique_filename_callback' => function($dir, $name, $ext) {
            $user_id = get_current_user_id();
            return 'avatar_user_' . $user_id . '_' . time() . $ext;
        }
    ];
    
    // Inclui as funções necessárias do WordPress
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    
    // Faz o upload do arquivo
    $uploaded_file = wp_handle_upload($file, $upload_overrides);
    
    if (isset($uploaded_file['error'])) {
        wp_send_json_error(['message' => 'Erro no upload: ' . $uploaded_file['error']]);
        return;
    }
    
    // Prepara os dados do attachment
    $attachment_data = [
        'post_title'     => 'Avatar do usuário ' . get_current_user_id(),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'post_mime_type' => $uploaded_file['type']
    ];
    
    // Insere o attachment na biblioteca de mídia
    $attachment_id = wp_insert_attachment($attachment_data, $uploaded_file['file']);
    
    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => 'Erro ao salvar o arquivo na biblioteca de mídia.']);
        return;
    }
    
    // Inclui as funções necessárias para gerar metadados
    if (!function_exists('wp_generate_attachment_metadata')) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    
    // Gera os metadados do attachment (thumbnails, etc.)
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);
    
    // Remove o avatar anterior se existir
    $user_id = get_current_user_id();
    $old_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    if ($old_avatar_id && wp_attachment_is_image($old_avatar_id)) {
        wp_delete_attachment($old_avatar_id, true);
    }
    
    // Salva o ID do novo avatar no perfil do usuário
    update_user_meta($user_id, 'custom_avatar', $attachment_id);
    
    // Retorna sucesso com a URL da nova imagem
    $avatar_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
    
    wp_send_json_success([
        'message' => 'Avatar atualizado com sucesso!',
        'avatar_url' => $avatar_url,
        'attachment_id' => $attachment_id
    ]);
}

// Registra a ação AJAX para upload de avatar
add_action('wp_ajax_amedis_avatar_upload', 'amedis_handle_avatar_upload');

/**
 * Manipula a remoção de avatar via AJAX
 */
function amedis_handle_avatar_remove() {
    // Verifica o nonce de segurança
    if (!wp_verify_nonce($_POST['avatar_nonce'] ?? '', 'avatar_upload_nonce')) {
        wp_send_json_error(['message' => 'Falha de segurança. Por favor, tente novamente.']);
        return;
    }
    
    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuário não está logado.']);
        return;
    }
    
    $user_id = get_current_user_id();
    $avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    
    // Remove o avatar se existir
    if ($avatar_id && wp_attachment_is_image($avatar_id)) {
        wp_delete_attachment($avatar_id, true);
    }
    
    // Remove o meta do usuário
    delete_user_meta($user_id, 'custom_avatar');
    
    wp_send_json_success([
        'message' => 'Avatar removido com sucesso!',
        'default_avatar' => true
    ]);
}

// Registra a ação AJAX para remoção de avatar
add_action('wp_ajax_amedis_avatar_remove', 'amedis_handle_avatar_remove');

/**
 * Manipula a requisição AJAX para buscar estatísticas do usuário.
 */
function amedis_handle_get_user_stats() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    $stats = amedis_get_user_stats();
    wp_send_json_success($stats);
}
add_action('wp_ajax_get_user_stats', 'amedis_handle_get_user_stats');

/**
 * Manipula a requisição AJAX para buscar receitas recentes do usuário.
 */
function amedis_handle_get_recent_receitas() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    // Busca as últimas 5 receitas do usuário
    $receitas = get_posts([
        'post_type'   => 'receitas',
        'post_status' => 'publish',
        'numberposts' => 5,
        'meta_query'  => [
            [
                'key'     => 'id_paciente_receita',
                'value'   => $user_id,
                'compare' => '='
            ]
        ],
        'orderby' => 'date',
        'order'   => 'DESC'
    ]);

    if (empty($receitas)) {
        wp_send_json_success(['receitas' => []]);
        return;
    }

    $receitas_data = [];
    $timezone = wp_timezone();
    $hoje = new DateTime('now', $timezone);
    $hoje->setTime(0, 0, 0);

    foreach ($receitas as $receita) {
        $data_emissao = get_field('data_emissao', $receita->ID);
        $data_vencimento = get_field('data_vencimento', $receita->ID);
        $desc_curta = get_field('desc_curta', $receita->ID);
        $arquivo_receita = get_field('arquivo_receita', $receita->ID);
        $arquivo_laudo = get_field('arquivo_laudo', $receita->ID);
        $nome_prescritor = get_field('nome_prescritor', $receita->ID);
        $prescritor_amedis = get_field('prescritor_amedis', $receita->ID);
        $cid_patologia = get_field('cid_patologia', $receita->ID);

        // Se for prescritor AMEDIS, busca dados do prescritor
        $prescritor_nome = $nome_prescritor;
        $prescritor_especialidade = '';
        
        if ($prescritor_amedis) {
            $prescritor_data = get_userdata($prescritor_amedis);
            if ($prescritor_data) {
                $nome_completo_prescritor = get_field('nome_completo_prescritor', 'user_' . $prescritor_amedis);
                $especialidade = get_field('especialidade', 'user_' . $prescritor_amedis);
                
                if ($nome_completo_prescritor) {
                    $prescritor_nome = $nome_completo_prescritor;
                }
                if ($especialidade) {
                    $prescritor_especialidade = $especialidade;
                }
            }
        }

        // Calcula status da receita
        $status = 'ativa';
        $status_text = 'Ativa';
        
        if (!empty($data_vencimento)) {
            $venc = DateTime::createFromFormat('d/m/Y', $data_vencimento, $timezone);
            if ($venc instanceof DateTime) {
                $venc->setTime(0, 0, 0);
                $diff = $hoje->diff($venc);
                
                if ($venc < $hoje) {
                    $status = 'expirada';
                    $status_text = 'Expirada';
                } elseif ($diff->days <= 30) {
                    $status = 'expirando';
                    $status_text = 'Expira em ' . $diff->days . ' dias';
                }
            }
        }

        $receitas_data[] = [
            'id' => $receita->ID,
            'title' => $receita->post_title,
            'url' => get_permalink($receita->ID),
            'data_emissao' => $data_emissao ?: '',
            'data_vencimento' => $data_vencimento ?: '',
            'status' => $status,
            'status_text' => $status_text,
            'desc_curta' => $desc_curta ?: '',
            'arquivo_receita' => $arquivo_receita ?: null,
            'arquivo_laudo' => $arquivo_laudo ?: null,
            'prescritor_nome' => $prescritor_nome ?: 'Prescritor não informado',
            'prescritor_especialidade' => $prescritor_especialidade ?: '',
            'cid_patologia' => $cid_patologia ?: ''
        ];
    }

    wp_send_json_success(['receitas' => $receitas_data]);
}
add_action('wp_ajax_get_recent_receitas', 'amedis_handle_get_recent_receitas');

/**
 * Busca os pedidos do WooCommerce do usuário logado.
 *
 * @return array Lista de pedidos do usuário.
 */
function amedis_get_user_orders($page = 1, $per_page = 10, $status = '') {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return ['orders' => [], 'total' => 0, 'pages' => 0];
    }

    // Verifica se o WooCommerce está ativo
    if (!function_exists('wc_get_orders')) {
        return ['orders' => [], 'total' => 0, 'pages' => 0];
    }

    $args = [
        'customer_id' => $user_id,
        'limit' => $per_page,
        'offset' => ($page - 1) * $per_page,
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    if (!empty($status)) {
        $args['status'] = $status;
    }

    $orders = wc_get_orders($args);
    
    // Contar total de pedidos
    $total_args = [
        'customer_id' => $user_id,
        'return' => 'ids',
        'limit' => -1,
    ];
    
    if (!empty($status)) {
        $total_args['status'] = $status;
    }
    
    $total_orders = count(wc_get_orders($total_args));
    $total_pages = ceil($total_orders / $per_page);

    $formatted_orders = [];
    foreach ($orders as $order) {
        $formatted_orders[] = [
            'id' => $order->get_id(),
            'number' => $order->get_order_number(),
            'status' => $order->get_status(),
            'status_name' => wc_get_order_status_name($order->get_status()),
            'date' => $order->get_date_created()->format('d/m/Y H:i'),
            'date_relative' => human_time_diff($order->get_date_created()->getTimestamp(), current_time('timestamp')) . ' atrás',
            'total' => $order->get_formatted_order_total(),
            'total_raw' => $order->get_total(),
            'items_count' => $order->get_item_count(),
            'payment_method' => $order->get_payment_method_title(),
            'billing_address' => $order->get_formatted_billing_address(),
            'shipping_address' => $order->get_formatted_shipping_address(),
        ];
    }

    return [
        'orders' => $formatted_orders,
        'total' => $total_orders,
        'pages' => $total_pages,
        'current_page' => $page
    ];
}

/**
 * Busca os 5 pedidos mais recentes do usuário, somente com status padrão pending, processing, completed.
 *
 * @param int $limit
 * @return array
 */
function amedis_get_recent_user_orders($limit = 5) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return [];
    }

    if (!function_exists('wc_get_orders')) {
        return [];
    }

    $args = [
        'customer_id' => $user_id,
        'status'      => ['pending', 'processing', 'completed'],
        'limit'       => $limit,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ];

    $orders = wc_get_orders($args);
    $recent = [];

    foreach ($orders as $order) {
        $recent[] = [
            'id'            => $order->get_id(),
            'number'        => $order->get_order_number(),
            'status'        => $order->get_status(),
            'status_name'   => wc_get_order_status_name($order->get_status()),
            'items_count'   => $order->get_item_count(),
            'total'         => $order->get_formatted_order_total(),
            'date'          => $order->get_date_created() ? $order->get_date_created()->format('d/m/Y H:i') : '',
            'date_relative' => $order->get_date_created() ? human_time_diff($order->get_date_created()->getTimestamp(), current_time('timestamp')) . ' atrás' : '',
        ];
    }

    return $recent;
}

/**
 * Busca receitas recentes do usuário para o dashboard.
 *
 * @param int $limit Número máximo de receitas a retornar.
 * @return array
 */
function amedis_get_recent_user_receitas($limit = 5) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return [];
    }

    $args = [
        'post_type' => 'receitas',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_query' => [
            [
                'key' => 'id_paciente_receita',
                'value' => $user_id,
                'compare' => '='
            ]
        ],
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    $query = new WP_Query($args);
    $receitas = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Busca os campos ACF
            $data_vencimento = get_field('data_vencimento', $post_id);
            $nome_prescritor = get_field('nome_prescritor', $post_id);
            $prescritor_amedis_check = get_field('prescritor_amedis_check', $post_id);
            $prescritor_amedis_id = get_field('prescritor_amedis', $post_id);
            
            // Se é prescritor da associação, busca os dados do prescritor
            $prescritor_nome = $nome_prescritor;
            $prescritor_especialidade = '';
            
            if ($prescritor_amedis_check && $prescritor_amedis_id) {
                $prescritor_nome = get_field('nome_completo_prescritor', 'user_' . $prescritor_amedis_id);
                $prescritor_especialidade = get_field('especialidade', 'user_' . $prescritor_amedis_id);
            }
            
            // Determina o status da receita
            $status = 'ativa';
            $status_text = 'Ativa';
            
            if ($data_vencimento) {
                $vencimento = DateTime::createFromFormat('d/m/Y', $data_vencimento);
                $hoje = new DateTime();
                
                if ($vencimento && $vencimento < $hoje) {
                    $status = 'expirada';
                    $status_text = 'Expirada';
                } elseif ($vencimento) {
                    $diff = $hoje->diff($vencimento);
                    if ($diff->days <= 5) {
                        $status = 'expirando';
                        $status_text = 'Expira em ' . $diff->days . ' dia' . ($diff->days > 1 ? 's' : '');
                    }
                }
            }
            
            $receitas[] = [
                'id' => $post_id,
                'prescritor_nome' => $prescritor_nome ?: 'Prescritor não informado',
                'prescritor_especialidade' => $prescritor_especialidade,
                'data_vencimento' => $data_vencimento,
                'status' => $status,
                'status_text' => $status_text,
                'permalink' => get_permalink($post_id)
            ];
        }
        wp_reset_postdata();
    }

    return $receitas;
}



/**
 * Endpoint AJAX para retornar os pedidos recentes do dashboard.
 */
function amedis_handle_get_recent_orders() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    // Se WooCommerce não estiver ativo
    if (!function_exists('wc_get_orders')) {
        wp_send_json_success(['orders' => []]);
        return;
    }

    $orders = amedis_get_recent_user_orders(5);
    wp_send_json_success(['orders' => $orders]);
}
add_action('wp_ajax_get_recent_orders', 'amedis_handle_get_recent_orders');

/**
 * Manipula a requisição AJAX para buscar pedidos do usuário.
 */
function amedis_handle_get_user_orders() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    $page = intval($_POST['page'] ?? 1);
    $per_page = intval($_POST['per_page'] ?? 10);
    $status = sanitize_text_field($_POST['status'] ?? '');

    $result = amedis_get_user_orders($page, $per_page, $status);
    wp_send_json_success($result);
}
add_action('wp_ajax_get_user_orders', 'amedis_handle_get_user_orders');

/**
 * Extrai fees personalizados de um pedido WooCommerce.
 * Centraliza a lógica de extração das taxas "Desconto", "Extra Cartão" e "Frete".
 *
 * IMPORTANTE: Este sistema usa fees personalizados para armazenar informações operacionais:
 * - "Desconto": Fee negativo, mas exibido como valor positivo para subtração visual
 * - "Extra Cartão": Fee positivo para taxas de cartão
 * - "Frete": Fee positivo, usado no lugar do shipping_total padrão do WooCommerce
 *
 * Esta abordagem permite maior flexibilidade operacional, mas requer que o modal
 * do usuário reflita exatamente os mesmos valores usados na tela de edição.
 *
 * @param WC_Order $order Objeto do pedido WooCommerce
 * @return array Array associativo com as fees extraídas
 */
function amedis_extract_custom_fees($order) {
    $fees = [
        'desconto' => 0,
        'extra_cartao' => 0,
        'frete' => 0
    ];

    if (!$order) {
        return $fees;
    }

    foreach ($order->get_fees() as $fee) {
        $fee_name = $fee->get_name();
        $fee_total = floatval($fee->get_total());

        switch ($fee_name) {
            case 'Desconto':
                $fees['desconto'] = abs($fee_total); // Sempre positivo para exibição
                break;
            case 'Extra Cartão':
                $fees['extra_cartao'] = $fee_total;
                break;
            case 'Frete':
                $fees['frete'] = $fee_total;
                break;
        }
    }

    return $fees;
}

/**
 * Busca detalhes completos de um pedido específico.
 *
 * @param int $order_id ID do pedido.
 * @return array|false Detalhes do pedido ou false se não encontrado.
 */
function amedis_get_order_details($order_id) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    // Verifica se o WooCommerce está ativo
    if (!function_exists('wc_get_order')) {
        return false;
    }

    $order = wc_get_order($order_id);
    if (!$order || $order->get_customer_id() != $user_id) {
        return false;
    }

    $items = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $items[] = [
            'name' => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'price' => $order->get_formatted_line_subtotal($item),
            'total' => $item->get_total(),
            'product_id' => $item->get_product_id(),
            'variation_id' => $item->get_variation_id(),
            'image' => $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : '',
        ];
    }

    // Determina o método de pagamento exibido para o usuário:
    // 1) Se existir o meta sem underscore 'forma_pagamento_woo', usar seu label mapeado
    // 2) Caso contrário, tentar meta operacional com underscore '_forma_pagamento_woo' como fallback adicional
    // 3) Por fim, usar o título nativo do WooCommerce.
    $payment_method_label = $order->get_payment_method_title();
    
    // 1) meta sem underscore (pedido do cliente)
    $forma_custom = get_post_meta($order->get_id(), 'forma_pagamento_woo', true);
    
    // 2) fallback adicional: meta com underscore (já usado no dashboard operacional)
    if (empty($forma_custom)) {
        $forma_custom = get_post_meta($order->get_id(), '_forma_pagamento_woo', true);
    }
    
    if (!empty($forma_custom)) {
        $forma_pagamento_map = [
            'gatewaypix'   => 'GATEWAY PIX',
            'gatewaycard'  => 'GATEWAY CARTÃO',
            'dinheiro'     => 'Dinheiro',
            'pix'          => 'PIX',
            'pgtodividido' => 'Pagamento Dividido',
        ];
        if (isset($forma_pagamento_map[$forma_custom])) {
            $payment_method_label = $forma_pagamento_map[$forma_custom];
        } else {
            // Se vier algum valor livre, mostre-o cru para evitar N/A
            $payment_method_label = is_string($forma_custom) ? $forma_custom : $payment_method_label;
        }
    }

    if (empty($payment_method_label)) {
        $payment_method_label = $order->get_payment_method_title();
    }

    // Extrair fees personalizados usando a função helper
    $custom_fees = amedis_extract_custom_fees($order);
    
    // Calcular subtotal dos itens (sem taxas)
    $subtotal_items = 0;
    foreach ($order->get_items() as $item) {
        $subtotal_items += $item->get_total();
    }
        
    return [
        'id' => $order->get_id(),
        'number' => $order->get_order_number(),
        'status' => $order->get_status(),
        'status_name' => wc_get_order_status_name($order->get_status()), // <<== CORRIGIDO AQUI
        'date' => $order->get_date_created()->format('d/m/Y H:i'),
        'total' => $order->get_formatted_order_total(),
        'subtotal' => wc_price($subtotal_items),
        'shipping_total' => $order->get_shipping_total() > 0 ? wc_price($order->get_shipping_total()) : 'Grátis',
        'tax_total' => wc_price($order->get_total_tax()),
        'payment_method' => $payment_method_label ?: '—',
        'billing_address' => $order->get_formatted_billing_address(),
        'shipping_address' => $order->get_formatted_shipping_address(),
        'customer_note' => $order->get_customer_note(),
        'items' => $items,
        'order_notes' => $order->get_customer_order_notes(),
        'fees' => [
            'desconto' => $custom_fees['desconto'] > 0 ? wc_price($custom_fees['desconto']) : null,
            'extra_cartao' => $custom_fees['extra_cartao'] > 0 ? wc_price($custom_fees['extra_cartao']) : null,
            'frete' => $custom_fees['frete'] > 0 ? wc_price($custom_fees['frete']) : null,
        ],
        'fees_raw' => $custom_fees,
    ];
}

/**
 * Manipula a requisição AJAX para buscar detalhes de um pedido.
 */
function amedis_handle_get_order_details() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    $order_id = intval($_POST['order_id'] ?? 0);
    if (!$order_id) {
        wp_send_json_error(['message' => 'ID do pedido não fornecido.']);
        return;
    }

    $order_details = amedis_get_order_details($order_id);
    if (!$order_details) {
        wp_send_json_error(['message' => 'Pedido não encontrado ou você não tem permissão para visualizá-lo.']);
        return;
    }

    wp_send_json_success($order_details);
}
add_action('wp_ajax_get_order_details', 'amedis_handle_get_order_details');

/**
 * Busca as receitas do usuário logado.
 *
 * @param int $user_id ID do usuário
 * @param int $page Página atual
 * @param int $per_page Itens por página
 * @param string $status_filter Filtro de status
 * @return array Lista de receitas do usuário
 */
function amedis_get_user_receitas($user_id = null, $page = 1, $per_page = 10, $status_filter = '') {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return ['receitas' => [], 'total' => 0, 'pages' => 0];
    }

    $args = [
        'post_type' => 'receitas',
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'meta_query' => [
            [
                'key' => 'id_paciente_receita',
                'value' => $user_id,
                'compare' => '='
            ]
        ],
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    $query = new WP_Query($args);
    $receitas = [];
    $receitas_filtradas = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Busca os campos ACF
            $data_receita = get_field('data_receita', $post_id);
            $data_vencimento = get_field('data_vencimento', $post_id);
            $cid_patologia = get_field('cid_patologia', $post_id);
            $desc_curta = get_field('desc_curta', $post_id);
            $nome_prescritor = get_field('nome_prescritor', $post_id);
            $prescritor_amedis_check = get_field('prescritor_amedis_check', $post_id);
            $prescritor_amedis_id = get_field('prescritor_amedis', $post_id);
            $arquivo_receita = get_field('arquivo_receita', $post_id);
            $arquivo_laudo = get_field('arquivo_laudo', $post_id);
            
            // Se é prescritor da associação, busca os dados do prescritor
            $prescritor_nome = $nome_prescritor;
            $prescritor_especialidade = '';
            
            if ($prescritor_amedis_check && $prescritor_amedis_id) {
                $prescritor_nome = get_field('nome_completo_prescritor', 'user_' . $prescritor_amedis_id);
                $prescritor_especialidade = get_field('especialidade', 'user_' . $prescritor_amedis_id);
                $n_id_prescritor = get_field('n_id_prescritor', 'user_' . $prescritor_amedis_id);
                $estado_id_conselho = get_field('estado_id_conselho', 'user_' . $prescritor_amedis_id);
                
                if ($n_id_prescritor && $estado_id_conselho) {
                    $prescritor_nome .= ' - ' . $n_id_prescritor . '/' . $estado_id_conselho;
                }
            }
            
            // Determina o status da receita
            $status = 'ativa';
            $status_class = 'bg-green-100 text-green-800';
            $status_text = 'Ativa';
            
            if ($data_vencimento) {
                $vencimento = DateTime::createFromFormat('d/m/Y', $data_vencimento);
                $hoje = new DateTime();
                
                if ($vencimento && $vencimento < $hoje) {
                    $status = 'expirada';
                    $status_class = 'bg-gray-100 text-gray-800';
                    $status_text = 'Expirada';
                } elseif ($vencimento) {
                    $diff = $hoje->diff($vencimento);
                    if ($diff->days <= 5) {
                        $status = 'expirando';
                        $status_class = 'bg-yellow-100 text-yellow-800';
                        $status_text = 'Expira em ' . $diff->days . ' dia' . ($diff->days > 1 ? 's' : '');
                    }
                }
            }
            
            $receita_data = [
                'id' => $post_id,
                'title' => get_the_title(),
                'data_receita' => $data_receita,
                'data_vencimento' => $data_vencimento,
                'cid_patologia' => $cid_patologia,
                'desc_curta' => $desc_curta,
                'prescritor_nome' => $prescritor_nome,
                'prescritor_especialidade' => $prescritor_especialidade,
                'status' => $status,
                'status_class' => $status_class,
                'status_text' => $status_text,
                'permalink' => get_permalink($post_id),
                'arquivo_receita' => $arquivo_receita ?: null,
                'arquivo_laudo' => $arquivo_laudo ?: null
            ];
            
            // Aplica filtro de status se especificado
            if (empty($status_filter) || $status === $status_filter) {
                $receitas_filtradas[] = $receita_data;
            }
            
            $receitas[] = $receita_data;
        }
        wp_reset_postdata();
    }

    // Se há filtro de status, usa as receitas filtradas
    $receitas_final = !empty($status_filter) ? $receitas_filtradas : $receitas;
    
    // Aplica paginação manual se há filtro
    if (!empty($status_filter)) {
        $total_filtradas = count($receitas_filtradas);
        $offset = ($page - 1) * $per_page;
        $receitas_final = array_slice($receitas_filtradas, $offset, $per_page);
        $total_pages = ceil($total_filtradas / $per_page);
        
        return [
            'receitas' => $receitas_final,
            'total' => $total_filtradas,
            'pages' => $total_pages,
            'current_page' => $page
        ];
    }

    return [
        'receitas' => $receitas_final,
        'total' => $query->found_posts,
        'pages' => $query->max_num_pages,
        'current_page' => $page
    ];
}

/**
 * Manipula a requisição AJAX para buscar receitas do usuário.
 */
function amedis_handle_get_user_receitas() {
    check_ajax_referer('user_profile_nonce', 'security');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Usuário não autenticado.']);
        return;
    }

    $page = intval($_POST['page'] ?? 1);
    $per_page = intval($_POST['per_page'] ?? 10);
    $status_filter = sanitize_text_field($_POST['status'] ?? '');

    $result = amedis_get_user_receitas($user_id, $page, $per_page, $status_filter);
    wp_send_json_success($result);
}
add_action('wp_ajax_get_user_receitas', 'amedis_handle_get_user_receitas');
//////////////////////////////////////////////////////////////////////////////////////////////////////q
/**
 * Manipula a requisição AJAX para atualizar o perfil do usuário.
 * Funciona com qualquer tipo de usuário do WordPress.
 */
function amedis_handle_user_profile_update() {
    // Log para debug
    error_log('AJAX Password Update called with data: ' . print_r($_POST, true));
    
    // 1. Verificação de Segurança
    if (!wp_verify_nonce($_POST['security'] ?? '', 'user_profile_nonce')) {
        error_log('Password Update: Nonce verification failed');
        wp_send_json_error(['message' => 'Falha de segurança. Recarregue a página e tente novamente.']);
        return;
    }

    // 2. Validação e Limpeza dos Dados
    $user_id = get_current_user_id();
    if (!$user_id) {
        error_log('Password Update: User not authenticated');
        wp_send_json_error(['message' => 'Erro: Usuário não autenticado.']);
        return;
    }

    // Verifica se o usuário tem permissão para alterar sua própria senha
    if (!current_user_can('edit_user', $user_id)) {
        error_log("Password Update: User {$user_id} doesn't have permission to edit their profile");
        wp_send_json_error(['message' => 'Você não tem permissão para alterar sua senha.']);
        return;
    }

    // Obtém apenas os campos de senha
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirm = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';

    error_log("Password Update: User {$user_id} attempting password change");

    // 3. Validação da Senha
    // Se ambos os campos estão vazios, não faz nada (mantém senha atual)
    if (empty($password) && empty($password_confirm)) {
        error_log('Password Update: Both fields empty, no changes made');
        wp_send_json_success(['message' => 'Nenhuma alteração foi feita.']);
        return;
    }

    // Se apenas um campo foi preenchido, retorna erro
    if (empty($password) || empty($password_confirm)) {
        error_log('Password Update: One field empty');
        wp_send_json_error(['message' => 'Preencha ambos os campos de senha.']);
        return;
    }

    // Verifica se as senhas coincidem
    if ($password !== $password_confirm) {
        error_log('Password Update: Passwords do not match');
        wp_send_json_error(['message' => 'As senhas não coincidem.']);
        return;
    }

    // Valida o comprimento mínimo da senha
    if (strlen($password) < 6) {
        error_log('Password Update: Password too short');
        wp_send_json_error(['message' => 'A nova senha deve ter pelo menos 6 caracteres.']);
        return;
    }

    // 4. Atualização da Senha
    try {
        // Verifica se o usuário existe e obtém dados atuais
        $user = get_userdata($user_id);
        if (!$user) {
            error_log("Password Update: User {$user_id} not found");
            wp_send_json_error(['message' => 'Usuário não encontrado.']);
            return;
        }

        // Salva a senha atual para comparação
        $old_password_hash = $user->user_pass;
        
        // Método 1: Usar wp_update_user (mais seguro e compatível)
        $user_data = [
            'ID' => $user_id,
            'user_pass' => $password
        ];
        
        $result = wp_update_user($user_data);
        
        if (is_wp_error($result)) {
            error_log("Password Update: wp_update_user failed - " . $result->get_error_message());
            wp_send_json_error(['message' => 'Erro ao atualizar senha: ' . $result->get_error_message()]);
            return;
        }
        
        // Verifica se a senha foi realmente alterada
        $updated_user = get_userdata($user_id);
        if ($updated_user && $updated_user->user_pass !== $old_password_hash) {
            error_log("Password Update: Password successfully updated for user {$user_id} using wp_update_user");
            
            // Limpa cache do usuário
            clean_user_cache($user_id);
            
            // Sucesso - mantém o usuário logado
            wp_send_json_success([
                'message' => 'Senha atualizada com sucesso!'
            ]);
        } else {
            // Fallback: Tentar com wp_set_password
            error_log("Password Update: Trying fallback method wp_set_password for user {$user_id}");
            
            wp_set_password($password, $user_id);
            
            // Verifica novamente
            $final_user = get_userdata($user_id);
            if ($final_user && wp_check_password($password, $final_user->user_pass, $user_id)) {
                error_log("Password Update: Password successfully updated for user {$user_id} using wp_set_password");
                
                // Limpa cache do usuário
                clean_user_cache($user_id);
                
                wp_send_json_success([
                    'message' => 'Senha atualizada com sucesso!'
                ]);
            } else {
                error_log("Password Update: Both methods failed for user {$user_id}");
                wp_send_json_error(['message' => 'Erro ao atualizar a senha. Tente novamente.']);
            }
        }
        
    } catch (Exception $e) {
        error_log("Password Update Exception: " . $e->getMessage());
        wp_send_json_error(['message' => 'Erro interno. Tente novamente.']);
    }
}
add_action('wp_ajax_update_user_profile', 'amedis_handle_user_profile_update');
///////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 Handle avatar upload with Cropper.js
 *
function amedis_handle_upload_user_avatar() {
    // Verify nonce
    check_ajax_referer('user_profile_nonce', 'security');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuário não está logado.']);
    }
    
    $user_id = get_current_user_id();
    
    // Check if file was uploaded
    if (!isset($_FILES['avatar_file']) || $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'Erro no upload do arquivo.']);
    }
    
    $file = $_FILES['avatar_file'];
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $file_type = wp_check_filetype($file['name']);
    
    if (!in_array($file_type['type'], $allowed_types)) {
        wp_send_json_error(['message' => 'Tipo de arquivo não permitido. Use JPEG, PNG ou WebP.']);
    }
    
    // Validate file size (3MB)
    if ($file['size'] > 3 * 1024 * 1024) {
        wp_send_json_error(['message' => 'Arquivo muito grande. Máximo 3MB.']);
    }
    
    // Remove old avatar if exists
    $old_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    if ($old_avatar_id && wp_attachment_is_image($old_avatar_id)) {
        wp_delete_attachment($old_avatar_id, true);
    }
    
    // Handle file upload
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Upload file
    $upload_overrides = [
        'test_form' => false,
        'unique_filename_callback' => function($dir, $name, $ext) use ($user_id) {
            return 'avatar-user-' . $user_id . '-' . time() . $ext;
        }
    ];
    
    $uploaded_file = wp_handle_upload($file, $upload_overrides);
    
    if (isset($uploaded_file['error'])) {
        wp_send_json_error(['message' => 'Erro no upload: ' . $uploaded_file['error']]);
    }
    
    // Create attachment
    $attachment = [
        'post_mime_type' => $uploaded_file['type'],
        'post_title'     => 'Avatar do usuário ' . $user_id,
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
    
    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => 'Erro ao criar attachment.']);
    }
    
    // Generate metadata
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);
    
    // Save avatar ID to user meta
    update_user_meta($user_id, 'custom_avatar', $attachment_id);
    
    // Get avatar URL
    $avatar_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
    if (!$avatar_url) {
        $avatar_url = $uploaded_file['url'];
    }
    
    wp_send_json_success([
        'attachment_id' => $attachment_id,
        'url' => $avatar_url
    ]);
}
add_action('wp_ajax_upload_user_avatar', 'amedis_handle_upload_user_avatar');
///////////////////////////////////////////////////////////////////////////////////////////////////////q

// ==
======================================
// AJAX Handler para Atualização de Senhas
// ========================================

/**
 * ACTIVE Enhanced AJAX handler registration for password updates
 * Registers for authenticated users only (security best practice)
 * This is the main handler used by the dashboard password form
 */
add_action('wp_ajax_update_user_password', 'handle_password_update_ajax');

/**
 * Handler AJAX para atualização de senhas
 * 
 * Processa requisições AJAX para alteração de senha do usuário logado
 * com validação de segurança, validação de dados e feedback padronizado
 */
function handle_password_update_ajax() {
    // Enhanced security verification - nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'user_profile_nonce')) {
        wp_send_json_error(array(
            'message' => 'Erro de segurança. Recarregue a página e tente novamente.',
            'code' => 'invalid_nonce'
        ));
        return;
    }

    // Enhanced authorization check - user must be logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => 'Você precisa estar logado para alterar a senha.',
            'code' => 'unauthorized'
        ));
        return;
    }

    // Enhanced data sanitization and validation
    $new_password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    // Initialize field errors array for specific field feedback
    $field_errors = array();

    // Enhanced validation: required fields with specific field errors
    if (empty($new_password)) {
        $field_errors['password'] = 'Nova senha é obrigatória';
    }
    
    if (empty($confirm_password)) {
        $field_errors['password_confirm'] = 'Confirmação de senha é obrigatória';
    }
    
    if (!empty($field_errors)) {
        wp_send_json_error(array(
            'message' => 'Preencha todos os campos obrigatórios.',
            'code' => 'missing_fields',
            'field_errors' => $field_errors
        ));
        return;
    }

    // Enhanced validation: password length (minimum 4 characters as per requirements)
    if (strlen($new_password) < 4) {
        wp_send_json_error(array(
            'message' => 'A senha deve ter pelo menos 4 caracteres.',
            'code' => 'weak_password',
            'field_errors' => array(
                'password' => 'Senha deve ter pelo menos 4 caracteres'
            )
        ));
        return;
    }

    // Enhanced validation: passwords must match
    if ($new_password !== $confirm_password) {
        wp_send_json_error(array(
            'message' => 'As senhas não coincidem. Verifique e tente novamente.',
            'code' => 'passwords_mismatch',
            'field_errors' => array(
                'password_confirm' => 'As senhas não coincidem'
            )
        ));
        return;
    }

    // Enhanced validation: password strength (optional - can be disabled for simpler requirements)
    $strength_check = validate_password_strength_enhanced($new_password);
    if (!$strength_check['valid']) {
        wp_send_json_error(array(
            'message' => $strength_check['message'],
            'code' => 'weak_password',
            'field_errors' => array(
                'password' => $strength_check['message']
            )
        ));
        return;
    }

    // Get current user ID
    $user_id = get_current_user_id();

    // Enhanced password update with better error handling
    try {
        // Use wp_set_password for secure hash and update
        wp_set_password($new_password, $user_id);
        
        // Log successful action for audit (optional)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Password updated successfully for user ID: {$user_id} at " . current_time('mysql'));
        }

        // Enhanced success response
        wp_send_json_success(array(
            'message' => 'Senha alterada com sucesso! Sua nova senha já está ativa.',
            'code' => 'success',
            'timestamp' => current_time('mysql')
        ));

    } catch (Exception $e) {
        // Enhanced error logging for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Password update failed for user ID {$user_id}: " . $e->getMessage() . " at " . current_time('mysql'));
        }

        // Enhanced error response
        wp_send_json_error(array(
            'message' => 'Erro interno do servidor. Tente novamente em alguns instantes.',
            'code' => 'database_error',
            'debug_info' => defined('WP_DEBUG') && WP_DEBUG ? $e->getMessage() : null
        ));
    }
}

/**
 * Enhanced password strength validation
 * 
 * @param string $password A senha a ser validada
 * @return array Array with 'valid' boolean and 'message' string
 */
function validate_password_strength_enhanced($password) {
    // Basic length check (minimum 4 characters as per requirements)
    if (strlen($password) < 4) {
        return array(
            'valid' => false,
            'message' => 'A senha deve ter pelo menos 4 caracteres.'
        );
    }
    
    // Check for common weak passwords
    $weak_passwords = array('1234', '12345', '123456', 'password', 'senha', 'admin', 'user');
    if (in_array(strtolower($password), $weak_passwords)) {
        return array(
            'valid' => false,
            'message' => 'Escolha uma senha mais segura. Evite senhas muito comuns.'
        );
    }
    
    // Check for repeated characters (more than 3 consecutive)
    if (preg_match('/(.)\1{3,}/', $password)) {
        return array(
            'valid' => false,
            'message' => 'Evite usar muitos caracteres repetidos consecutivos.'
        );
    }
    
    // For now, we'll keep it simple and just require minimum length
    // More complex rules can be added later if needed
    return array(
        'valid' => true,
        'message' => 'Senha válida.'
    );
}

/**
 * Legacy password strength validation (kept for compatibility)
 * 
 * @param string $password A senha a ser validada
 * @return bool True se a senha atender aos critérios, false caso contrário
 */
function validate_password_strength($password) {
    $result = validate_password_strength_enhanced($password);
    return $result['valid'];
}

/**
 * Enhanced nonce generation for password updates
 * Uses the same nonce as other user profile operations for consistency
 * Note: The nonce is already provided by the existing userProfileAjax localization
 */
/**
 * Handle avatar removal
 */
function amedis_handle_remove_user_avatar() {
    // Verify nonce
    check_ajax_referer('user_profile_nonce', 'security');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuário não está logado.']);
    }
    
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    
    // Get current avatar
    $avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    
    if ($avatar_id && wp_attachment_is_image($avatar_id)) {
        // Delete attachment
        $deleted = wp_delete_attachment($avatar_id, true);
        
        if (!$deleted) {
            wp_send_json_error(['message' => 'Erro ao remover avatar.']);
        }
    }
    
    // Remove user meta
    delete_user_meta($user_id, 'custom_avatar');
    
    // Generate initials
    $initials = strtoupper(substr($current_user->display_name, 0, 2));
    
    wp_send_json_success([
        'removed' => true,
        'initials' => $initials
    ]);
}
add_action('wp_ajax_remove_user_avatar', 'amedis_handle_remove_user_avatar');

/**
 * Helper function to get user avatar URL
 */
function amedis_get_user_avatar_url($user_id, $size = 'thumbnail') {
    $avatar_id = get_user_meta($user_id, 'custom_avatar', true);
    
    if ($avatar_id && wp_attachment_is_image($avatar_id)) {
        $avatar_url = wp_get_attachment_image_url($avatar_id, $size);
        if ($avatar_url) {
            return $avatar_url;
        }
    }
    
    return false;
}/**

 * Display user avatar with fallback to initials
 */
function amedis_display_user_avatar($user_id, $size = 'thumbnail', $classes = '') {
    $avatar_url = amedis_get_user_avatar_url($user_id, $size);
    
    if ($avatar_url) {
        return '<img src="' . esc_url($avatar_url) . '" alt="Avatar" class="' . esc_attr($classes) . ' w-full h-full object-cover">';
    } else {
        $user = get_userdata($user_id);
        $initials = $user ? strtoupper(substr($user->display_name, 0, 2)) : 'U';
        return $initials;
    }
}

/**
 * Get user avatar HTML for display
 */
function amedis_get_user_avatar_html($user_id, $size = 'thumbnail', $container_classes = '', $img_classes = '') {
    $avatar_url = amedis_get_user_avatar_url($user_id, $size);
    
    if ($avatar_url) {
        return '<div class="' . esc_attr($container_classes) . ' overflow-hidden">
                    <img src="' . esc_url($avatar_url) . '" alt="Avatar" class="' . esc_attr($img_classes) . ' w-full h-full object-cover">
                </div>';
    } else {
        $user = get_userdata($user_id);
        $initials = $user ? strtoupper(substr($user->display_name, 0, 2)) : 'U';
        return '<div class="' . esc_attr($container_classes) . ' flex items-center justify-center avatar-gradient text-primary-foreground font-bold">
                    ' . $initials . '
                </div>';
    }
}