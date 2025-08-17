<?php
/**
 * Sistema de Controle de Usuários
 * Gerencia níveis de acesso personalizados: Super Admin, Gerente, Atendente
 * 
 * @package SativarApp
 * @version 1.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adiciona campos personalizados no perfil do usuário
 */
add_action('show_user_profile', 'sativar_add_user_role_control_fields');
add_action('edit_user_profile', 'sativar_add_user_role_control_fields');

function sativar_add_user_role_control_fields($user) {
    // Verifica se o usuário atual tem permissão para editar
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Obtém o controle de função atual do usuário
    $user_role_control = get_user_meta($user->ID, 'user_role_control', true);
    
    // Define valores padrão se não existir
    if (empty($user_role_control) || !is_array($user_role_control)) {
        $user_role_control = array(
            'super_admin' => false,
            'gerente' => false,
            'atendente' => false
        );
    }
    ?>
    <h3>Controle de Função do Usuário</h3>
    <table class="form-table">
        <tr>
            <th><label>Nível de Acesso</label></th>
            <td>
                <div id="user-role-control-switcher">
                    <div class="role-option">
                        <input type="radio" 
                               id="role_super_admin" 
                               name="user_role_control" 
                               value="super_admin" 
                               <?php checked($user_role_control['super_admin'], true); ?>>
                        <label for="role_super_admin">Super Admin</label>
                    </div>
                    
                    <div class="role-option">
                        <input type="radio" 
                               id="role_gerente" 
                               name="user_role_control" 
                               value="gerente" 
                               <?php checked($user_role_control['gerente'], true); ?>>
                        <label for="role_gerente">Gerente</label>
                    </div>
                    
                    <div class="role-option">
                        <input type="radio" 
                               id="role_atendente" 
                               name="user_role_control" 
                               value="atendente" 
                               <?php checked($user_role_control['atendente'], true); ?>>
                        <label for="role_atendente">Atendente</label>
                    </div>
                    
                    <div class="role-option">
                        <input type="radio" 
                               id="role_none" 
                               name="user_role_control" 
                               value="none" 
                               <?php checked(!$user_role_control['super_admin'] && !$user_role_control['gerente'] && !$user_role_control['atendente'], true); ?>>
                        <label for="role_none">Nenhum</label>
                    </div>
                </div>
                
                <div id="role-control-status" style="margin-top: 10px;"></div>
                
                <p class="description">
                    Selecione o nível de acesso para este usuário. Apenas um nível pode estar ativo por vez.
                </p>
            </td>
        </tr>
    </table>
    
    <style>
        #user-role-control-switcher {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .role-option {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .role-option:hover {
            border-color: #0073aa;
        }
        
        .role-option input[type="radio"]:checked + label {
            font-weight: bold;
            color: #0073aa;
        }
        
        .role-option input[type="radio"]:checked {
            accent-color: #0073aa;
        }
        
        #role-control-status {
            padding: 8px;
            border-radius: 4px;
            display: none;
        }
        
        #role-control-status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        #role-control-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    <?php
}

/**
 * Salva os campos personalizados do usuário
 */
add_action('personal_options_update', 'sativar_save_user_role_control_fields');
add_action('edit_user_profile_update', 'sativar_save_user_role_control_fields');

function sativar_save_user_role_control_fields($user_id) {
    // Verifica permissões
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    // Verifica nonce para segurança
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return false;
    }
    
    // Processa o controle de função
    if (isset($_POST['user_role_control'])) {
        $selected_role = sanitize_text_field($_POST['user_role_control']);
        
        // Define a estrutura padrão
        $user_role_control = array(
            'super_admin' => false,
            'gerente' => false,
            'atendente' => false
        );
        
        // Ativa apenas a função selecionada
        if ($selected_role !== 'none' && array_key_exists($selected_role, $user_role_control)) {
            $user_role_control[$selected_role] = true;
        }
        
        // Salva no banco de dados
        update_user_meta($user_id, 'user_role_control', $user_role_control);
        
        // Log da ação para auditoria
        error_log("User role control updated for user ID {$user_id}: " . json_encode($user_role_control));
        
        return true;
    }
    
    return false;
}

/**
 * Função para verificar se o usuário tem uma função específica
 */
function sativar_user_has_role($user_id, $role) {
    $user_role_control = get_user_meta($user_id, 'user_role_control', true);
    
    if (empty($user_role_control) || !is_array($user_role_control)) {
        return false;
    }
    
    return isset($user_role_control[$role]) && $user_role_control[$role] === true;
}

/**
 * Função para obter a função ativa do usuário
 */
function sativar_get_user_active_role($user_id) {
    $user_role_control = get_user_meta($user_id, 'user_role_control', true);
    
    if (empty($user_role_control) || !is_array($user_role_control)) {
        return false;
    }
    
    foreach ($user_role_control as $role => $is_active) {
        if ($is_active === true) {
            return $role;
        }
    }
    
    return false;
}

/**
 * Função helper para obter o role do usuário com fallback
 * Retorna 'none' se o usuário não tiver role definido
 */
function sativar_get_user_role_safe($user_id) {
    $role = sativar_get_user_active_role($user_id);
    return $role ?: 'none';
}

/**
 * Função centralizada para verificar permissões por tipo de usuário
 */
function sativar_check_user_access($required_role = null) {
    if (!is_user_logged_in()) {
        return false;
    }
    
    $current_user_id = get_current_user_id();
    $user_role = sativar_get_user_active_role($current_user_id);
    
    // Super Admin tem acesso total
    if ($user_role === 'super_admin') {
        return true;
    }
    
    // Verifica role específico se fornecido
    if ($required_role && $user_role !== $required_role) {
        return false;
    }
    
    return $user_role ? true : false;
}

/**
 * Função helper para verificar se um usuário pode editar outro usuário
 */
function sativar_can_edit_user_role($editor_role, $target_role) {
    // Super Admin pode editar todos
    if ($editor_role === 'super_admin') {
        return true;
    }
    
    // Gerente pode editar apenas Atendentes e usuários sem função
    if ($editor_role === 'gerente' && ($target_role === 'atendente' || !$target_role || $target_role === 'none')) {
        return true;
    }
    
    // Atendente não pode editar ninguém
    return false;
}

/**
 * Função helper para verificar se um usuário pode visualizar outro usuário
 */
function sativar_can_view_user($viewer_role, $target_role, $viewer_id, $target_id) {
    // Super Admin pode ver todos
    if ($viewer_role === 'super_admin') {
        return true;
    }
    
    // Gerente pode ver gerentes e atendentes (não super admins)
    if ($viewer_role === 'gerente' && $target_role !== 'super_admin') {
        return true;
    }
    
    // Atendente pode ver apenas próprio perfil
    if ($viewer_role === 'atendente' && $viewer_id === $target_id) {
        return true;
    }
    
    return false;
}

/**
 * Função helper para obter histórico de alterações de senha
 */
function sativar_get_password_change_history($user_id) {
    $changed_by = get_user_meta($user_id, 'password_changed_by', true);
    $changed_at = get_user_meta($user_id, 'password_changed_at', true);
    
    if (!$changed_by || !$changed_at) {
        return false;
    }
    
    $changed_by_user = get_user_by('ID', $changed_by);
    
    return array(
        'changed_by_id' => $changed_by,
        'changed_by_name' => $changed_by_user ? $changed_by_user->display_name : 'Usuário removido',
        'changed_at' => $changed_at,
        'changed_at_formatted' => date_i18n('d/m/Y H:i', strtotime($changed_at))
    );
}

/**
 * Função helper para verificar se um Super Admin pode alterar senha de outro usuário
 */
function sativar_can_change_password($editor_role, $target_user_id) {
    // Apenas Super Admin pode alterar senhas
    if ($editor_role !== 'super_admin') {
        return false;
    }
    
    // Verifica se o usuário alvo existe e é administrador
    $target_user = get_user_by('ID', $target_user_id);
    if (!$target_user || !in_array('administrator', $target_user->roles)) {
        return false;
    }
    
    return true;
}

/**
 * AJAX handler para alterar senha do usuário (apenas Super Admin)
 */
add_action('wp_ajax_change_user_password_premium', 'sativar_ajax_change_user_password');

function sativar_ajax_change_user_password() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'user_role_control_nonce')) {
        wp_send_json_error('Erro de segurança');
    }
    
    // Verifica se o usuário atual é Super Admin
    $current_user_role = sativar_get_user_active_role(get_current_user_id());
    if ($current_user_role !== 'super_admin') {
        wp_send_json_error('Apenas Super Admins podem alterar senhas');
    }
    
    $user_id = intval($_POST['user_id']);
    $new_password = sanitize_text_field($_POST['new_password']);
    
    // Validações
    if (!$user_id) {
        wp_send_json_error('ID do usuário inválido');
    }
    
    if (strlen($new_password) < 6) {
        wp_send_json_error('A senha deve ter pelo menos 6 caracteres');
    }
    
    // Verifica se o usuário existe
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        wp_send_json_error('Usuário não encontrado');
    }
    
    // Verifica se o usuário alvo é administrador
    if (!in_array('administrator', $user->roles)) {
        wp_send_json_error('Só é possível alterar senhas de administradores');
    }
    
    // Altera a senha
    $result = wp_set_password($new_password, $user_id);
    
    if ($result === false) {
        wp_send_json_error('Erro ao alterar senha no banco de dados');
    }
    
    // Log da ação para auditoria
    error_log("Password changed by Super Admin (ID: " . get_current_user_id() . ") for user ID: {$user_id}");
    
    // Adiciona meta de última alteração de senha
    update_user_meta($user_id, 'password_changed_by', get_current_user_id());
    update_user_meta($user_id, 'password_changed_at', current_time('mysql'));
    
    wp_send_json_success(array(
        'message' => 'Senha alterada com sucesso!',
        'user_id' => $user_id,
        'changed_by' => get_current_user_id(),
        'changed_at' => current_time('mysql')
    ));
}

/**
 * AJAX handler para atualizar função do usuário
 */
add_action('wp_ajax_update_user_role_control', 'sativar_ajax_update_user_role_control');

function sativar_ajax_update_user_role_control() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'user_role_control_nonce')) {
        wp_die('Erro de segurança');
    }
    
    // Verifica permissões básicas
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada');
    }
    
    $user_id = intval($_POST['user_id']);
    $selected_role = sanitize_text_field($_POST['role']);
    
    if (!$user_id) {
        wp_send_json_error('ID do usuário inválido');
    }
    
    // Verifica permissões específicas por role
    $current_user_role = sativar_get_user_active_role(get_current_user_id());
    $target_user_role = sativar_get_user_active_role($user_id);
    
    if (!sativar_can_edit_user_role($current_user_role, $target_user_role)) {
        wp_send_json_error('Permissão insuficiente para editar este usuário');
    }
    
    // Define a estrutura padrão
    $user_role_control = array(
        'super_admin' => false,
        'gerente' => false,
        'atendente' => false
    );
    
    // Ativa apenas a função selecionada
    if ($selected_role !== 'none' && array_key_exists($selected_role, $user_role_control)) {
        $user_role_control[$selected_role] = true;
    }
    
    // Salva no banco de dados
    $result = update_user_meta($user_id, 'user_role_control', $user_role_control);
    
    if ($result !== false) {
        wp_send_json_success(array(
            'message' => 'Função do usuário atualizada com sucesso!',
            'role' => $selected_role,
            'user_id' => $user_id
        ));
    } else {
        wp_send_json_error('Erro ao salvar no banco de dados');
    }
}

/**
 * Adiciona scripts necessários na página de perfil do usuário e dashboard premium
 */
add_action('admin_enqueue_scripts', 'sativar_enqueue_user_role_control_scripts');
add_action('wp_enqueue_scripts', 'sativar_enqueue_premium_dashboard_scripts');

function sativar_enqueue_user_role_control_scripts($hook) {
    // Carrega apenas nas páginas de perfil do usuário
    if ($hook !== 'profile.php' && $hook !== 'user-edit.php') {
        return;
    }
    
    // Registra e enfileira o script
    wp_enqueue_script(
        'user-role-control',
        get_template_directory_uri() . '/assets/js/dashboard-funcao-usuarios.js',
        array('jquery'),
        '2.0.0',
        true
    );
    
    // Localiza o script com dados necessários
    wp_localize_script('user-role-control', 'userRoleControl', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('user_role_control_nonce'),
        'user_id' => isset($_GET['user_id']) ? intval($_GET['user_id']) : get_current_user_id()
    ));
}

function sativar_enqueue_premium_dashboard_scripts() {
    // Carrega apenas na página do dashboard premium
    if (is_page_template('dashboard-usuarios-sistema.php')) {
        // Enfileira Tailwind CSS
        wp_enqueue_style(
            'tailwind-css',
            'https://cdn.tailwindcss.com',
            array(),
            '3.3.0'
        );
        
        // Enfileira o script premium
        wp_enqueue_script(
            'premium-user-control',
            get_template_directory_uri() . '/assets/js/dashboard-funcao-usuarios.js',
            array('jquery'),
            '2.0.0',
            true
        );
        
        // Localiza dados para o dashboard premium
        $admin_users = get_users(array('role' => 'administrator'));
        wp_localize_script('premium-user-control', 'premiumDashboard', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('user_role_control_nonce'),
            'current_user_id' => get_current_user_id(),
            'current_user_role' => sativar_get_user_active_role(get_current_user_id()),
            'is_admin' => current_user_can('administrator'),
            'users_count' => count($admin_users)
        ));
    }
}

/**
 * Função helper para exibir o nível de acesso do usuário
 */
function sativar_display_user_role($user_id) {
    $active_role = sativar_get_user_active_role($user_id);
    
    if (!$active_role) {
        return '<span class="user-role-none">Nenhum nível definido</span>';
    }
    
    $role_labels = array(
        'super_admin' => 'Super Admin',
        'gerente' => 'Gerente',
        'atendente' => 'Atendente'
    );
    
    $label = isset($role_labels[$active_role]) ? $role_labels[$active_role] : $active_role;
    
    return '<span class="user-role-' . esc_attr($active_role) . '">' . esc_html($label) . '</span>';
}

/**
 * Adiciona coluna de função na lista de usuários do admin
 */
add_filter('manage_users_columns', 'sativar_add_user_role_column');

function sativar_add_user_role_column($columns) {
    $columns['user_role_control'] = 'Nível de Acesso';
    return $columns;
}

add_action('manage_users_custom_column', 'sativar_show_user_role_column_content', 10, 3);

function sativar_show_user_role_column_content($value, $column_name, $user_id) {
    if ($column_name === 'user_role_control') {
        return sativar_display_user_role($user_id);
    }
    return $value;
}

/**
 * Adiciona endpoint REST API para estatísticas do dashboard premium
 */
add_action('rest_api_init', 'sativar_register_premium_dashboard_endpoints');

function sativar_register_premium_dashboard_endpoints() {
    register_rest_route('sativar/v1', '/users-stats', array(
        'methods' => 'GET',
        'callback' => 'sativar_get_users_stats',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));
    
    register_rest_route('sativar/v1', '/users-list', array(
        'methods' => 'GET',
        'callback' => 'sativar_get_users_list',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));
}

/**
 * Retorna estatísticas dos usuários para o dashboard premium (apenas administradores)
 */
function sativar_get_users_stats($request) {
    // Busca apenas usuários com função administrator
    $users = get_users(array('role' => 'administrator'));
    $stats = array(
        'total' => count($users),
        'super_admin' => 0,
        'gerente' => 0,
        'atendente' => 0,
        'none' => 0,
        'by_role' => array(),
        'recent_changes' => array()
    );
    
    foreach ($users as $user) {
        $active_role = sativar_get_user_active_role($user->ID);
        
        if ($active_role) {
            $stats[$active_role]++;
        } else {
            $stats['none']++;
        }
        
        // Conta por função WordPress também
        $wp_roles = $user->roles;
        foreach ($wp_roles as $wp_role) {
            if (!isset($stats['by_role'][$wp_role])) {
                $stats['by_role'][$wp_role] = 0;
            }
            $stats['by_role'][$wp_role]++;
        }
    }
    
    return rest_ensure_response($stats);
}

/**
 * Retorna lista completa de usuários para o dashboard premium (apenas administradores)
 */
function sativar_get_users_list($request) {
    // Busca apenas usuários com função administrator
    $users = get_users(array('role' => 'administrator'));
    $users_data = array();
    
    foreach ($users as $user) {
        $active_role = sativar_get_user_active_role($user->ID);
        
        $users_data[] = array(
            'id' => $user->ID,
            'login' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
            'wp_roles' => $user->roles,
            'custom_role' => $active_role,
            'avatar_url' => get_avatar_url($user->ID, array('size' => 80)),
            'registered' => $user->user_registered,
            'last_login' => get_user_meta($user->ID, 'last_login', true)
        );
    }
    
    return rest_ensure_response($users_data);
}

/**
 * Função para obter estatísticas rápidas (helper) - apenas administradores
 */
function sativar_get_quick_stats() {
    // Busca apenas usuários com função administrator
    $users = get_users(array('role' => 'administrator'));
    $stats = array(
        'total' => count($users),
        'super_admin' => 0,
        'gerente' => 0,
        'atendente' => 0,
        'none' => 0
    );
    
    foreach ($users as $user) {
        $active_role = sativar_get_user_active_role($user->ID);
        
        if ($active_role) {
            $stats[$active_role]++;
        } else {
            $stats['none']++;
        }
    }
    
    return $stats;
}

/**
 * Adiciona meta tags necessárias para o dashboard premium
 */
add_action('wp_head', 'sativar_add_premium_dashboard_meta');

function sativar_add_premium_dashboard_meta() {
    if (is_page_template('dashboard-usuarios-sistema.php')) {
        echo '<meta name="wp-nonce" content="' . wp_create_nonce('user_role_control_nonce') . '">' . "\n";
        echo '<meta name="ajax-url" content="' . admin_url('admin-ajax.php') . '">' . "\n";
    }
}

/**
 * Adiciona suporte para busca de usuários via AJAX
 */
add_action('wp_ajax_search_users_premium', 'sativar_ajax_search_users');

function sativar_ajax_search_users() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'user_role_control_nonce')) {
        wp_die('Erro de segurança');
    }
    
    // Verifica permissões
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada');
    }
    
    $search_term = sanitize_text_field($_POST['search_term']);
    $filter = sanitize_text_field($_POST['filter']);
    $current_user_role = sativar_get_user_active_role(get_current_user_id());
    
    // Busca apenas usuários com função administrator
    $args = array(
        'role' => 'administrator',
        'search' => '*' . $search_term . '*',
        'search_columns' => array('user_login', 'user_email', 'display_name'),
        'number' => 50 // Limita resultados
    );
    
    $users = get_users($args);
    $results = array();
    
    foreach ($users as $user) {
        $active_role = sativar_get_user_active_role($user->ID);
        
        // Aplica filtros de permissão baseado no role do usuário atual
        if ($current_user_role === 'gerente') {
            // Gerente só vê gerentes e atendentes
            if ($active_role === 'super_admin') {
                continue;
            }
        } elseif ($current_user_role === 'atendente') {
            // Atendente só vê próprio perfil
            if ($user->ID !== get_current_user_id()) {
                continue;
            }
        }
        
        // Aplica filtro se especificado
        if ($filter !== 'all' && $active_role !== $filter) {
            continue;
        }
        
        $results[] = array(
            'id' => $user->ID,
            'login' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
            'role' => $active_role,
            'avatar_url' => get_avatar_url($user->ID, array('size' => 80))
        );
    }
    
    wp_send_json_success($results);
}

/**
 * Inclui arquivo de testes apenas em desenvolvimento
 */
if (WP_DEBUG && file_exists(get_template_directory() . '/test-permissoes.php')) {
    require_once get_template_directory() . '/test-permissoes.php';
}

/**
 * Inclui arquivo de debug para roles apenas em desenvolvimento
 */
if (WP_DEBUG && file_exists(get_template_directory() . '/debug-roles.php')) {
    require_once get_template_directory() . '/debug-roles.php';
}