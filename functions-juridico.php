<?php

/**
 * Enfileira e localiza o script dashboard-tarefas.js
 */
function enqueue_dashboard_juridico_script() {
    // Só carrega se o usuário estiver logado e for admin ou gerente
    if (is_user_logged_in() && (current_user_can('administrator') || current_user_can('gerente'))) {
        
        // Enfileira o script
        wp_enqueue_script(
            'dashboard-juridico', 
            get_template_directory_uri() . '/assets/js/dashboard-juridico.js', 
            array('jquery'), 
            time(), // versão baseada no timestamp para evitar cache
            true // carrega no footer
        );
        
        // Localiza as variáveis necessárias para o AJAX
        wp_localize_script('dashboard-juridico', 'dashboardAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            //'nonce' => wp_create_nonce('criar_tarefa_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_dashboard_juridico_script');

add_action('wp_ajax_search_associados', 'handle_search_associados');
add_action('wp_ajax_nopriv_search_associados', 'handle_search_associados');

function handle_search_associados() {
    check_ajax_referer('search_associados_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search']);
    $associados = obter_dados_associados();
    $filtered_results = '';
    
    if (!empty($associados)) {
        foreach ($associados as $associado) {
            $data = get_associado_display_data($associado->ID);
            extract($data);
            
            // Se busca vazia, mostra todos
            if (empty($search_term) || 
                stripos($nome_completo, $search_term) !== false || 
                stripos($email, $search_term) !== false || 
                stripos($cpf, $search_term) !== false || 
                stripos($telefone, $search_term) !== false) {
                
                $filtered_results .= render_patient_card($data);
            }
        }
    }
    
    if (empty($filtered_results)) {
        $filtered_results = '<div class="col-span-full text-center py-12"><p class="text-gray-500">Nenhum resultado encontrado.</p></div>';
    }
    
    wp_send_json_success($filtered_results);
}


function render_patient_card($data) {
    extract($data);
    ob_start();
    ?>
    <div class="patient-card bg-white p-6 rounded-xl shadow-sm border border-gray-200/50 flex flex-col transition-all duration-300" data-status="<?php echo $associado_ativo ? 'ativo' : 'inativo'; ?>">
        <div class="flex-grow">
            <!-- BADGE DE TIPO DE ASSOCIADO -->
            <?php if (!empty($text_tipo_assoc)) : ?>
                <span class="inline-block mb-2 px-3 py-1 rounded-full text-xs font-semibold
                    <?php
                        // Cores diferentes para cada tipo, ajuste conforme necessário
                        echo ($text_tipo_assoc === 'Paciente') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                    ?>
                    shadow-sm border border-gray-200 uppercase tracking-wide"
                    title="Tipo de associado">
                    <?php echo esc_html($text_tipo_assoc); ?>
                </span>
            <?php endif; ?>
            <!-- FIM BADGE -->

            <h4 class="font-semibold text-lg text-gray-800" data-searchable><?php echo esc_html($nome_completo); ?></h4>
            <p class="text-sm text-gray-500 mt-1" data-searchable><?php echo esc_html($email); ?></p>
            <?php if (!empty($telefone)) : ?>
            <p class="text-sm text-gray-500" data-searchable><?php echo esc_html($telefone); ?></p>
            <?php endif; ?>
            <?php if (!empty($cpf)) : ?>
            <p class="text-sm text-gray-500" data-searchable>CPF: <?php echo esc_html($cpf); ?></p>
            <?php endif; ?>
            <?php if (!empty($cidade) || !empty($estado)) : ?>
                <div class="flex gap-2 mt-1">
                    <?php if (!empty($cidade)) : ?>
                        <span class="text-sm text-gray-500" data-searchable><?php echo esc_html($cidade); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($estado)) : ?>
                        <span class="text-sm text-gray-500" data-searchable><?php echo esc_html($estado); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <p class="text-sm text-gray-500">ID: <?php echo $user_id; ?></p>
            <div class="flex items-center gap-2 mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $associado_ativo_cor; ?>"><?php echo $associado_ativo_texto; ?></span>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="button" class="w-full text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5">Saiba mais</button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Paginacao card
add_action('wp_ajax_paginacao_associados', 'handle_paginacao_associados');
add_action('wp_ajax_nopriv_paginacao_associados', 'handle_paginacao_associados');

function handle_paginacao_associados() {
    // Parâmetros recebidos via AJAX
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8; // 8 por página, ajuste se quiser
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    $all_associados = obter_dados_associados();
    $filtered = [];

    // Filtro de busca e status
    foreach ($all_associados as $associado) {
        $data = get_associado_display_data($associado->ID);

        // Filtro de busca
        $match_search = empty($search) ||
            stripos($data['nome_completo'], $search) !== false ||
            stripos($data['email'], $search) !== false ||
            stripos($data['cpf'], $search) !== false ||
            stripos($data['telefone'], $search) !== false;

        // Filtro de status
        $match_status = empty($status) ||
            ($status === 'ativo' && $data['associado_ativo']) ||
            ($status === 'inativo' && !$data['associado_ativo']);

        if ($match_search && $match_status) {
            $filtered[] = $data;
        }
    }

    $total = count($filtered);
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    $page_items = array_slice($filtered, $offset, $per_page);

    // Render cards
    ob_start();
    if (!empty($page_items)) {
        foreach ($page_items as $data) {
            extract($data); // <-- Adicione esta linha!
            ?>
<div class="patient-card bg-white p-6 rounded-xl shadow-sm border border-gray-200/50 flex flex-col transition-all duration-300" data-status="<?php echo $associado_ativo ? 'ativo' : 'inativo'; ?>">


<div class="flex-grow">
    <!-- BADGE DE TIPO DE ASSOCIADO ALINHADA À DIREITA -->
    <?php
        // Mapeamento dos valores para texto e cor
        $badge_map = [
            'assoc_paciente' => [
                'label' => 'Paciente',
                'class' => 'bg-green-100 text-green-800'
            ],
            'assoc_respon' => [
                'label' => 'Responsável pelo Paciente',
                'class' => 'bg-blue-100 text-blue-800'
            ],
            'assoc_tutor' => [
                'label' => 'Tutor de Animal',
                'class' => 'bg-purple-100 text-purple-800'
            ],
            'assoc_colab' => [
                'label' => 'Colaborador',
                'class' => 'bg-gray-100 text-gray-800'
            ],
        ];

        // O campo correto é $tipo_associacao
        $tipo_associacao = isset($tipo_associacao) ? $tipo_associacao : ($data['tipo_associacao'] ?? '');

        if (!empty($tipo_associacao) && isset($badge_map[$tipo_associacao])) :
            $badge_label = $badge_map[$tipo_associacao]['label'];
            $badge_classes = $badge_map[$tipo_associacao]['class'];
    ?>
        <div class="flex justify-end">
            <span class="inline-block mb-2 px-3 py-1 rounded-full text-[9px] font-semibold
                <?php echo $badge_classes; ?>
                shadow-sm border border-gray-200 uppercase tracking-wide"
                title="Tipo de associado">
                <?php echo esc_html($badge_label); ?>
            </span>
        </div>
    <?php endif; ?>
    <!-- FIM BADGE -->

    <h4 class="font-semibold text-md text-gray-700 uppercase" data-searchable><?php echo esc_html($nome_completo); ?></h4>
    <p class="text-sm text-gray-500 mt-1" data-searchable><?php echo esc_html($email); ?></p>
    <?php if (!empty($telefone)) : ?>
    <p class="text-sm text-gray-500" data-searchable><?php echo esc_html($telefone); ?></p>
    <?php endif; ?>
    <?php if (!empty($cpf)) : ?>
    <p class="text-sm text-gray-500" data-searchable>CPF: <?php echo esc_html($cpf); ?></p>
<?php endif; ?>
<?php if (!empty($cidade) || !empty($estado)) : ?>
    <div class="flex gap-2 my-1 uppercase">
        <?php if (!empty($cidade)) : ?>
            <span class="text-xs text-gray-500" data-searchable><?php echo esc_html($cidade); ?></span>
        <?php endif; ?>
        <?php if (!empty($estado)) : ?>
            <span class="text-xs text-gray-500" data-searchable><?php echo esc_html($estado); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>    
    <p class="text-sm text-gray-500">ID: <?php echo $user_id; ?></p>
    <div class="flex items-center gap-2 mt-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $associado_ativo_cor; ?>"><?php echo $associado_ativo_texto; ?></span>
        <?php if (!empty($doc_rg_icon) && $doc_rg_icon_class == 'text-green-500') : ?>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Docs OK</span>
        <?php endif; ?>
        <?php if (!empty($doc_termo_icon) && $doc_termo_icon_class == 'text-green-500') : ?>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Termos OK</span>
        <?php endif; ?>
    </div>
</div>


<div class="flex gap-3 mt-6">
    
    <div class="flex gap-1 items-center justify-center w-full">
        <!-- RG/CPF -->
        <div class="flex items-center justify-center w-8 h-8 rounded border border-gray-300 hover:bg-gray-50 transition-colors" title="RG/CPF">
            <?php if (!empty($comprova_rg_paciente_url)) : ?>
                <a href="<?php echo esc_url($comprova_rg_paciente_url); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_rg_icon_class); ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Z" />
                    </svg>
                </a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_rg_icon_class); ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Z" />
                </svg>
            <?php endif; ?>
        </div>
        <!-- Comprovante Endereço -->
        <div class="flex items-center justify-center w-8 h-8 rounded border border-gray-300 hover:bg-gray-50 transition-colors" title="Comprovante Endereço">
            <?php if (!empty($comprova_end_paciente_url)) : ?>
                <a href="<?php echo esc_url($comprova_end_paciente_url); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_end_icon_class); ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_end_icon_class); ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            <?php endif; ?>
        </div>
        <!-- Laudo Médico -->
        <div class="flex items-center justify-center w-8 h-8 rounded border border-gray-300 hover:bg-gray-50 transition-colors" title="Laudo Médico">
            <?php if (!empty($laudo_paciente_url)) : ?>
                <a href="<?php echo esc_url($laudo_paciente_url); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_laudo_icon_class); ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6v6a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5V4.5a2.25 2.25 0 0 1 2.25-2.25h7.5a2.25 2.25 0 0 1 2.25 2.25v6ZM12 9a3.75 3.75 0 0 0-3.75 3.75H9a6 6 0 0 1 6-6V9Z" />
                    </svg>
                </a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_laudo_icon_class); ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6v6a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5V4.5a2.25 2.25 0 0 1 2.25-2.25h7.5a2.25 2.25 0 0 1 2.25 2.25v6ZM12 9a3.75 3.75 0 0 0-3.75 3.75H9a6 6 0 0 1 6-6V9Z" />
                </svg>
            <?php endif; ?>
        </div>
        <!-- Termo Associativo -->
        <div class="flex items-center justify-center w-8 h-8 rounded border border-gray-300 hover:bg-gray-50 transition-colors" title="Termo Associativo">
            <?php if (!empty($termo_associativo_url)) : ?>
                <a href="<?php echo esc_url($termo_associativo_url); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_termo_icon_class); ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 <?php echo esc_attr($doc_termo_icon_class); ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            <?php endif; ?>
        </div>
    </div>
    <div class="flex items-center justify-center w-full space-x-1">
        <button type="button"
            class="abrir-modal-paciente w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
            data-user-id="<?php echo $user_id; ?>">
            Detalhes
        </button>
        <button type="button" data-modal-target="nova-tarefa-modal" data-modal-toggle="nova-tarefa-modal" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-user-id="<?php echo $user_id; ?>">+</button>        
    </div>
</div>

    </div>

<?php
        }
    } else {
        echo '<div class="col-span-full text-center py-12"><p class="text-gray-500">Nenhum usuário associado encontrado.</p></div>';
    }
    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'total' => $total,
        'total_pages' => $total_pages,
        'current_page' => $page,
    ]);
}

add_action('wp_ajax_buscar_dados_paciente', function() {
    $user_id = intval($_POST['user_id']);
    $data = get_associado_display_data($user_id);
    wp_send_json_success($data);
});