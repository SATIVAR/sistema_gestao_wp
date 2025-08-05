<?php
    /**
     * Registra o Custom Post Type 'Tarefa'.
     */
    function registrar_cpt_tarefa() {
        $labels = array(
            'name'                  => _x('Tarefas', 'Post Type General Name', 'text_domain'),
            'singular_name'         => _x('Tarefa', 'Post Type Singular Name', 'text_domain'),
            'menu_name'             => __('Tarefas', 'text_domain'),
            'name_admin_bar'        => __('Tarefa', 'text_domain'),
            'archives'              => __('Arquivo de Tarefas', 'text_domain'),
            'attributes'            => __('Atributos da Tarefa', 'text_domain'),
            'parent_item_colon'     => __('Tarefa Pai:', 'text_domain'),
            'all_items'             => __('Todas as Tarefas', 'text_domain'),
            'add_new_item'          => __('Adicionar Nova Tarefa', 'text_domain'),
            'add_new'               => __('Adicionar Nova', 'text_domain'),
            'new_item'              => __('Nova Tarefa', 'text_domain'),
            'edit_item'             => __('Editar Tarefa', 'text_domain'),
            'update_item'           => __('Atualizar Tarefa', 'text_domain'),
            'view_item'             => __('Ver Tarefa', 'text_domain'),
            'view_items'            => __('Ver Tarefas', 'text_domain'),
            'search_items'          => __('Procurar Tarefa', 'text_domain'),
        );
        $args = array(
            'label'                 => __('Tarefa', 'text_domain'),
            'description'           => __('Gerenciamento de tarefas internas e externas.', 'text_domain'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'author', 'comments'), // 'comments' é essencial para o histórico
            'taxonomies'            => array('tipos_de_tarefa', 'setor', 'status_tarefa'), 
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-clipboard', // Ícone mais adequado
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
        );
        register_post_type('tarefa', $args);
    }
    add_action('init', 'registrar_cpt_tarefa', 0);

    /**
     * Registra a Taxonomia 'Tipos de Tarefa'.
     */
    function registrar_taxonomia_tipo_tarefa() {
        $labels = array(
            'name'                       => _x('Tipos de Tarefa', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('Tipo de Tarefa', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('Tipos de Tarefa', 'text_domain'),
            'all_items'                  => __('Todos os Tipos', 'text_domain'),
            'parent_item'                => __('Tipo Pai', 'text_domain'),
            'parent_item_colon'          => __('Tipo Pai:', 'text_domain'),
            'new_item_name'              => __('Novo Tipo de Tarefa', 'text_domain'),
            'add_new_item'               => __('Adicionar Novo Tipo', 'text_domain'),
            'edit_item'                  => __('Editar Tipo', 'text_domain'),
            'update_item'                => __('Atualizar Tipo', 'text_domain'),
            'view_item'                  => __('Ver Tipo', 'text_domain'),
            'separate_items_with_commas' => __('Separe os tipos com vírgulas', 'text_domain'),
            'add_or_remove_items'        => __('Adicionar ou remover tipos', 'text_domain'),
            'choose_from_most_used'      => __('Escolher dos mais usados', 'text_domain'),
            'popular_items'              => __('Tipos Populares', 'text_domain'),
            'search_items'               => __('Procurar Tipos', 'text_domain'),
            'not_found'                  => __('Nenhum tipo encontrado.', 'text_domain'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true, // Permite criar sub-tipos (ex: Documentos > Procuração)
            'public'                     => false,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
        );
        register_taxonomy('tipos_de_tarefa', array('tarefa'), $args); // Aplica esta taxonomia apenas ao CPT 'tarefa'
    }
    add_action('init', 'registrar_taxonomia_tipo_tarefa', 0);


/**
 * Registra a Taxonomia 'Setor'.
 * Esta taxonomia organiza as tarefas por departamento (ex: Jurídico, Financeiro).
 */
function registrar_taxonomia_setor() {

    $labels = array(
        'name'                       => _x('Setores', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Setor', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Setores', 'text_domain'),
        'all_items'                  => __('Todos os Setores', 'text_domain'),
        'parent_item'                => __('Setor Pai', 'text_domain'),
        'parent_item_colon'          => __('Setor Pai:', 'text_domain'),
        'new_item_name'              => __('Nome do Novo Setor', 'text_domain'),
        'add_new_item'               => __('Adicionar Novo Setor', 'text_domain'),
        'edit_item'                  => __('Editar Setor', 'text_domain'),
        'update_item'                => __('Atualizar Setor', 'text_domain'),
        'view_item'                  => __('Ver Setor', 'text_domain'),
        'separate_items_with_commas' => __('Separe os setores com vírgulas', 'text_domain'),
        'add_or_remove_items'        => __('Adicionar ou remover setores', 'text_domain'),
        'choose_from_most_used'      => __('Escolher dos setores mais usados', 'text_domain'),
        'popular_items'              => __('Setores Populares', 'text_domain'),
        'search_items'               => __('Procurar Setores', 'text_domain'),
        'not_found'                  => __('Nenhum setor encontrado.', 'text_domain'),
        'no_terms'                   => __('Nenhum setor', 'text_domain'),
        'items_list'                 => __('Lista de setores', 'text_domain'),
        'items_list_navigation'      => __('Navegação da lista de setores', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, // true = formato de "categoria" com checkboxes. Ideal para setores.
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true, // MUITO útil! Mostra o setor na lista de tarefas no painel.
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'show_in_rest'               => true, // Importante para o editor de blocos (Gutenberg).
    );
    register_taxonomy('setor', array('tarefa'), $args); // Aplica esta taxonomia apenas ao CPT 'tarefa'.

}
add_action('init', 'registrar_taxonomia_setor', 0);
/**
 * Registra a Taxonomia 'Status da Tarefa'.
 * Esta taxonomia permite categorizar as tarefas por status (ex: Pendente, Em Andamento, Concluída).
 */
function registrar_taxonomia_status_tarefa() {
    $labels = array(
        'name'                       => _x('Status das Tarefas', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Status', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Status', 'text_domain'),
        'all_items'                  => __('Todos os Status', 'text_domain'),
        'parent_item'                => __('Status Pai', 'text_domain'),
        'parent_item_colon'          => __('Status Pai:', 'text_domain'),
        'new_item_name'              => __('Nome do Novo Status', 'text_domain'),
        'add_new_item'               => __('Adicionar Novo Status', 'text_domain'),
        'edit_item'                  => __('Editar Status', 'text_domain'),
        'update_item'                => __('Atualizar Status', 'text_domain'),
        'view_item'                  => __('Ver Status', 'text_domain'),
        'separate_items_with_commas' => __('Separe os status com vírgulas', 'text_domain'),
        'add_or_remove_items'        => __('Adicionar ou remover status', 'text_domain'),
        'choose_from_most_used'      => __('Escolher dos status mais usados', 'text_domain'),
        'popular_items'              => __('Status Populares', 'text_domain'),
        'search_items'               => __('Procurar Status', 'text_domain'),
        'not_found'                  => __('Nenhum status encontrado.', 'text_domain'),
        'no_terms'                   => __('Nenhum status', 'text_domain'),
        'items_list'                 => __('Lista de status', 'text_domain'),
        'items_list_navigation'      => __('Navegação da lista de status', 'text_domain'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, // MUDANÇA PRINCIPAL: true = checkboxes como categoria
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true, // Mostra na lista de tarefas
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'show_in_rest'               => true, // Para Gutenberg
    );
    
    register_taxonomy('status_tarefa', array('tarefa'), $args);
}
add_action('init', 'registrar_taxonomia_status_tarefa', 0);


/**
 * Enfileira e localiza o script dashboard-tarefas.js
 */
function enqueue_dashboard_tarefas_script() {
    // Só carrega se o usuário estiver logado e for admin ou gerente
    if (is_user_logged_in() && (current_user_can('administrator') || current_user_can('gerente'))) {
        
        // Verificar se estamos em uma página de tarefas específica
        global $template;
        $template_name = basename($template);
        
        // Lista de templates que precisam do script de tarefas
        $task_templates = array(
            'dashboard-juridico-tarefas.php',
            'dashboard-modais-tarefas.php',
            'dashboard-juridico-dashboard.php'
        );
        
        // Só carrega o script se estivermos em uma página de tarefas
        if (in_array($template_name, $task_templates)) {
            // Enfileira o script
            wp_enqueue_script(
                'dashboard-tarefas', 
                get_template_directory_uri() . '/assets/js/dashboard-tarefas.js', 
                array('jquery'), 
                time(), // versão baseada no timestamp para evitar cache
                true // carrega no footer
            );
            
            // Localiza as variáveis necessárias para o AJAX
            wp_localize_script('dashboard-tarefas', 'dashboardAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('criar_tarefa_nonce')
            ));
        }
    }
}
// DISABLED - Using isolated system instead
// add_action('wp_enqueue_scripts', 'enqueue_dashboard_tarefas_script');

/**
 * AJAX: Buscar usuários para responsáveis
 */
// Adicionar no functions-tarefas.php - SUBSTITUIR a função existente
// SUBSTITUIR a função buscar_usuarios_responsaveis_ajax existente
function buscar_usuarios_responsaveis_ajax() {
    if (!check_ajax_referer('criar_tarefa_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Erro de segurança']);
    }
    
    $termo = sanitize_text_field($_POST['termo']);
    
    // Buscar apenas colaboradores (assoc_colab)
    $usuarios = get_users([
        'meta_key' => 'tipo_associacao',
        'meta_value' => 'assoc_colab',
        'meta_compare' => '=',
        'search' => "*{$termo}*",
        'search_columns' => ['display_name', 'user_email'],
        'number' => 10
    ]);
    
    $usuarios_formatados = array_map(function($user) {
        return [
            'id' => $user->ID,
            'name' => $user->display_name,
            'email' => $user->user_email,
            'avatar' => get_avatar_url($user->ID, ['size' => 32])
        ];
    }, $usuarios);
    
    wp_send_json_success(['usuarios' => $usuarios_formatados]);
}

add_action('wp_ajax_buscar_usuarios_responsaveis', 'buscar_usuarios_responsaveis_ajax');

/**
 * Salva responsáveis múltiplos e define o criador como dono
 */
function salvar_responsaveis_tarefa($post_id, $responsaveis_ids = []) {
    $criador_id = get_post_field('post_author', $post_id);
    
    // Garantir que o criador sempre seja responsável (dono)
    if (!in_array($criador_id, $responsaveis_ids)) {
        array_unshift($responsaveis_ids, $criador_id);
    }
    
    // Remover duplicatas e IDs inválidos
    $responsaveis_ids = array_unique(array_filter($responsaveis_ids, function($id) {
        return is_numeric($id) && $id > 0 && get_user_by('id', $id);
    }));
    
    // Salvar como array serializado
    update_post_meta($post_id, '_responsaveis_tarefa', array_values($responsaveis_ids));
    update_post_meta($post_id, '_dono_tarefa', $criador_id);
    
    // Log para debug (remover em produção)
    error_log('Responsáveis salvos para tarefa ' . $post_id . ': ' . implode(', ', $responsaveis_ids));
}


/**
 * Adiciona a meta box de detalhes da tarefa na tela de edição.
 */
function tarefas_registrar_meta_boxes() {
    add_meta_box(
        'tarefa_detalhes_meta_box',
        __('Detalhes da Tarefa', 'text_domain'),
        'tarefas_meta_box_callback',
        'tarefa',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_tarefa', 'tarefas_registrar_meta_boxes');
/**
 * Renderiza o HTML dos campos personalizados.
 */
function tarefas_meta_box_callback($post) {
    wp_nonce_field('salvar_detalhes_tarefa', 'tarefa_nonce');

    // Busca os valores salvos
    $associado_id = get_post_meta($post->ID, '_associado_relacionado', true);
    $prioridade = get_post_meta($post->ID, '_prioridade_tarefa', true);
    $data_prazo = get_post_meta($post->ID, '_data_prazo_tarefa', true);
    $checklist_json = get_post_meta($post->ID, '_tarefa_checklist_json', true);
    $tarefa_pessoal = get_post_meta($post->ID, '_tarefa_pessoal', true);
    
    // RESPONSÁVEIS MÚLTIPLOS
    $responsaveis_ids = get_post_meta($post->ID, '_responsaveis_tarefa', true) ?: [];
    $dono_id = get_post_meta($post->ID, '_dono_tarefa', true);

    $prioridade_options = ['Baixa' => 'Baixa', 'Média' => 'Média', 'Alta' => 'Alta', 'Urgente' => 'Urgente'];
    ?>
    <div class="inside">
        <table class="form-table">
            <!-- Responsáveis Múltiplos -->
            <tr>
                <th><label><?php _e('Responsáveis', 'text_domain'); ?></label></th>
                <td>
                    <?php if (!empty($responsaveis_ids)): ?>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                            <?php foreach ($responsaveis_ids as $user_id): 
                                $user = get_user_by('id', $user_id);
                                if ($user):
                                    $is_owner = ($user_id == $dono_id);
                            ?>
                                <span style="display: inline-flex; align-items: center; gap: 6px; background: <?php echo $is_owner ? '#dcfce7' : '#f3f4f6'; ?>; color: <?php echo $is_owner ? '#166534' : '#374151'; ?>; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                    <img src="<?php echo get_avatar_url($user_id, ['size' => 20]); ?>" style="width: 20px; height: 20px; border-radius: 50%;">
                                    <?php echo esc_html($user->display_name); ?>
                                    <?php if ($is_owner): ?>
                                        <strong>(Dono)</strong>
                                    <?php endif; ?>
                                </span>
                            <?php endif; endforeach; ?>
                        </div>
                        <small style="color: #666;">Total: <?php echo count($responsaveis_ids); ?> responsável(is)</small>
                    <?php else: ?>
                        <em>Nenhum responsável definido</em>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- Associado Relacionado -->
            <tr>
                <th><label for="associado_relacionado"><?php _e('Associado Relacionado', 'text_domain'); ?></label></th>
                <td>
                    <?php wp_dropdown_users([
                        'name' => 'associado_relacionado',
                        'id' => 'associado_relacionado',
                        'selected' => intval($associado_id),
                        'show_option_none' => 'Nenhum (Tarefa Interna)',
                        'class' => 'regular-text'
                    ]); ?>
                </td>
            </tr>

            <!-- Tarefa Pessoal -->
            <tr>
                <th><label for="tarefa_pessoal"><?php _e('Tarefa Pessoal?', 'text_domain'); ?></label></th>
                <td>
                    <input type="checkbox" id="tarefa_pessoal" name="tarefa_pessoal" value="yes" <?php checked($tarefa_pessoal, 'yes'); ?>>
                    <label for="tarefa_pessoal"><?php _e('Sim, ocultar das listagens públicas', 'text_domain'); ?></label>
                </td>
            </tr>
            <!-- Status Tarefas -->
            <tr>
                <th><label for="status_tarefa"><?php _e('Status', 'text_domain'); ?></label></th>
                <td>
                    <?php 
                    $status_terms = wp_get_post_terms($post->ID, 'status_tarefa');
                    $current_status = !empty($status_terms) ? $status_terms[0]->term_id : '';
                    
                    $all_status = get_terms(['taxonomy' => 'status_tarefa', 'hide_empty' => false]);
                    ?>
                    <select name="status_tarefa" id="status_tarefa" class="regular-text">
                        <option value="">Selecione o Status</option>
                        <?php foreach($all_status as $status): ?>
                            <option value="<?php echo esc_attr($status->term_id); ?>" <?php selected($current_status, $status->term_id); ?>>
                                <?php echo esc_html($status->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <!-- Prioridade -->
            <tr>
                <th><label for="prioridade_tarefa"><?php _e('Prioridade', 'text_domain'); ?></label></th>
                <td>
                    <select name="prioridade_tarefa" id="prioridade_tarefa">
                        <?php foreach ($prioridade_options as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($prioridade, $key); ?>>
                                <?php echo esc_html($value); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <!-- Data de Prazo -->
            <tr>
                <th><label for="data_prazo_tarefa"><?php _e('Data de Prazo', 'text_domain'); ?></label></th>
                <td>
                    <input type="date" id="data_prazo_tarefa" name="data_prazo_tarefa" value="<?php echo esc_attr($data_prazo); ?>" />
                </td>
            </tr>

            <!--  seção do checklist na meta box -->
            <tr>
                <th><label><?php _e('Checklist', 'text_domain'); ?></label></th>
                <td>
                    <?php 
                    $checklist_json = get_post_meta($post->ID, '_tarefa_checklist_json', true);
                    $checklist_items = json_decode($checklist_json, true);
                    
                    if (!empty($checklist_items) && is_array($checklist_items)): 
                        $total = count($checklist_items);
                        $completed = array_filter($checklist_items, function($item) { return $item['completed']; });
                        $progress = $total > 0 ? round((count($completed) / $total) * 100) : 0;
                    ?>
                        <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Progresso: <?php echo count($completed); ?>/<?php echo $total; ?> itens</strong>
                                <span style="color: #059669; font-weight: bold;"><?php echo $progress; ?>%</span>
                            </div>
                            
                            <div style="background: #e5e7eb; height: 8px; border-radius: 4px; margin-bottom: 15px;">
                                <div style="background: #10b981; height: 8px; border-radius: 4px; width: <?php echo $progress; ?>%;"></div>
                            </div>
                            
                            <?php foreach($checklist_items as $item): ?>
                                <div style="display: flex; align-items: center; gap: 8px; padding: 8px; background: white; margin-bottom: 5px; border-radius: 6px; border: 1px solid #d1d5db;">
                                    <input type="checkbox" <?php checked($item['completed'], true); ?> disabled>
                                    <span style="<?php echo $item['completed'] ? 'text-decoration: line-through; color: #6b7280;' : ''; ?>">
                                        <?php echo esc_html($item['text']); ?>
                                    </span>
                                    <?php if ($item['completed'] && !empty($item['completed_at'])): ?>
                                        <small style="color: #059669; margin-left: auto;">
                                            ✓ <?php echo date('d/m/Y H:i', strtotime($item['completed_at'])); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <em style="color: #6b7280;">Nenhum item no checklist</em>
                    <?php endif; ?>
                    
                    <!-- Campo JSON (oculto por padrão) -->
                    <details style="margin-top: 10px;">
                        <summary style="cursor: pointer; color: #6b7280; font-size: 12px;">Ver JSON (Debug)</summary>
                        <textarea name="tarefa_checklist_json" id="tarefa_checklist_json" style="width: 100%; height: 80px; font-family: monospace; font-size: 11px; margin-top: 5px;"><?php echo esc_textarea($checklist_json); ?></textarea>
                    </details>
                </td>
            </tr>

        </table>
    </div>
    <?php
}
/**
 * Adiciona a coluna de checklist na lista de tarefas.
 */
function adicionar_coluna_checklist_tarefas($columns) {
    $columns['checklist_progress'] = 'Checklist';
    return $columns;
}
add_filter('manage_tarefa_posts_columns', 'adicionar_coluna_checklist_tarefas');


/**
 * Exibe o progresso do checklist na coluna personalizada.
 */
function exibir_coluna_checklist_tarefas($column, $post_id) {
    if ($column === 'checklist_progress') {
        $checklist_json = get_post_meta($post_id, '_tarefa_checklist_json', true);
        $checklist_items = json_decode($checklist_json, true);
        
        if (!empty($checklist_items) && is_array($checklist_items)) {
            $total = count($checklist_items);
            $completed = array_filter($checklist_items, function($item) { return $item['completed']; });
            $progress = round((count($completed) / $total) * 100);
            
            echo '<div style="display: flex; align-items: center; gap: 5px;">';
            echo '<div style="background: #e5e7eb; width: 50px; height: 6px; border-radius: 3px;">';
            echo '<div style="background: #10b981; height: 6px; border-radius: 3px; width: ' . $progress . '%;"></div>';
            echo '</div>';
            echo '<small>' . count($completed) . '/' . $total . '</small>';
            echo '</div>';
        } else {
            echo '<span style="color: #9ca3af;">—</span>';
        }
    }
}
add_action('manage_tarefa_posts_custom_column', 'exibir_coluna_checklist_tarefas', 10, 2);

/**
 * Função de debug para verificar o JSON do checklist da tarefa.
 * Útil para identificar problemas de formatação ou erros de codificação.
 */
function debug_checklist_tarefa($post_id) {
    $checklist_json = get_post_meta($post_id, '_tarefa_checklist_json', true);
    error_log('DEBUG Checklist Tarefa ' . $post_id . ': ' . $checklist_json);
    
    $decoded = json_decode($checklist_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('ERRO JSON: ' . json_last_error_msg());
    }
    
    return $decoded;
}

/**
 * Salva os dados dos campos personalizados da tarefa.
 */

function tarefas_salvar_meta_data($post_id) {
    if (!isset($_POST['tarefa_nonce']) || !wp_verify_nonce($_POST['tarefa_nonce'], 'salvar_detalhes_tarefa')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if ('tarefa' !== get_post_type($post_id)) return;

    // Campos Meta
    $campos_para_salvar = [
        '_associado_relacionado' => isset($_POST['associado_relacionado']) ? intval($_POST['associado_relacionado']) : '',
        '_prioridade_tarefa'     => isset($_POST['prioridade_tarefa']) ? sanitize_text_field($_POST['prioridade_tarefa']) : '',
        '_data_prazo_tarefa'     => isset($_POST['data_prazo_tarefa']) ? sanitize_text_field($_POST['data_prazo_tarefa']) : '',
    ];

    foreach ($campos_para_salvar as $meta_key => $value) {
        if ($value) {
            update_post_meta($post_id, $meta_key, $value);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
    
    // Tarefa Pessoal
    if (isset($_POST['tarefa_pessoal']) && $_POST['tarefa_pessoal'] === 'yes') {
        update_post_meta($post_id, '_tarefa_pessoal', 'yes');
    } else {
        delete_post_meta($post_id, '_tarefa_pessoal');
    }

    // STATUS - Agora é taxonomia, não meta
    if (isset($_POST['status_tarefa']) && !empty($_POST['status_tarefa'])) {
        wp_set_post_terms($post_id, intval($_POST['status_tarefa']), 'status_tarefa');
    }

    // Checklist JSON
    if (isset($_POST['tarefa_checklist_json'])) {
        $json_string = wp_unslash($_POST['tarefa_checklist_json']);
        if (json_decode($json_string) !== null || empty($json_string)) {
            update_post_meta($post_id, '_tarefa_checklist_json', $json_string);
        }
    }
}

add_action('save_post_tarefa', 'tarefas_salvar_meta_data');

/**
 * Endpoint AJAX para criar uma nova tarefa a partir do modal do dashboard.
 */

function criar_nova_tarefa_ajax_callback() {
    // DEBUG: Verificar dados recebidos
    error_log('POST recebido: ' . print_r($_POST, true));
    error_log('Checklist JSON recebido: ' . ($_POST['tarefa_checklist_json'] ?? 'VAZIO'));
        
    // 1. Segurança
    if (!check_ajax_referer('criar_tarefa_nonce', 'nonce_nova_tarefa', false) || !current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Erro de segurança.'], 403);
        return;
    }

    // 2. Validação
    if (empty($_POST['tarefa_titulo'])) {
        wp_send_json_error(['message' => 'O título da tarefa é obrigatório.'], 400);
        return;
    }

    // 3. Sanitização
    $titulo = sanitize_text_field($_POST['tarefa_titulo']);
    $descricao = isset($_POST['tarefa_descricao']) ? wp_kses_post($_POST['tarefa_descricao']) : '';
    $setor_id = isset($_POST['tarefa_setor']) ? intval($_POST['tarefa_setor']) : 0;
    $tipo_id = isset($_POST['tarefa_tipo']) ? intval($_POST['tarefa_tipo']) : 0;
    $status_id = isset($_POST['status_tarefa']) ? intval($_POST['status_tarefa']) : 0;
    $prioridade = isset($_POST['prioridade_tarefa']) ? sanitize_text_field($_POST['prioridade_tarefa']) : 'Média';
    $data_prazo = isset($_POST['data_prazo_tarefa']) ? sanitize_text_field($_POST['data_prazo_tarefa']) : '';
    $associado_id = isset($_POST['associado_relacionado']) ? intval($_POST['associado_relacionado']) : 0;

    // 4. Criar Post
    $nova_tarefa_args = [
        'post_title'   => $titulo,
        'post_content' => $descricao,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_type'    => 'tarefa',
    ];

    $post_id = wp_insert_post($nova_tarefa_args);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Não foi possível criar a tarefa.'], 500);
        return;
    }

    // 5. Salvar Taxonomias
    if ($setor_id) wp_set_post_terms($post_id, $setor_id, 'setor');
    if ($tipo_id) wp_set_post_terms($post_id, $tipo_id, 'tipos_de_tarefa');
    
    // STATUS - Corrigido
    if ($status_id) {
        wp_set_post_terms($post_id, $status_id, 'status_tarefa');
    } else {
        // Status padrão se não selecionado
        $status_pendente = get_term_by('slug', 'pendente', 'status_tarefa');
        if($status_pendente) {
            wp_set_post_terms($post_id, $status_pendente->term_id, 'status_tarefa');
        }
    }

    // 6. Salvar Meta Dados
    if ($associado_id) update_post_meta($post_id, '_associado_relacionado', $associado_id);
    if ($prioridade) update_post_meta($post_id, '_prioridade_tarefa', $prioridade);
    if ($data_prazo) update_post_meta($post_id, '_data_prazo_tarefa', $data_prazo); // CORRIGIDO
    
    if (isset($_POST['tarefa_pessoal']) && $_POST['tarefa_pessoal'] === 'yes') {
        update_post_meta($post_id, '_tarefa_pessoal', 'yes');
    }

    // 7. Processar responsáveis múltiplos
    $criador_id = get_current_user_id();
    $responsaveis_enviados = [];
    
    if (isset($_POST['responsaveis']) && is_array($_POST['responsaveis'])) {
        $responsaveis_enviados = array_map('intval', $_POST['responsaveis']);
        $responsaveis_enviados = array_filter($responsaveis_enviados, function($id) {
            return $id > 0 && get_user_by('id', $id);
        });
    }
    
    if (!in_array($criador_id, $responsaveis_enviados)) {
        array_unshift($responsaveis_enviados, $criador_id);
    }
    
    salvar_responsaveis_tarefa($post_id, $responsaveis_enviados);

    // 8. Checklist JSON - CORRIGIDO
    if (isset($_POST['tarefa_checklist_json'])) {
        $checklist_json = wp_unslash($_POST['tarefa_checklist_json']);
        if (!empty($checklist_json) && json_decode($checklist_json) !== null) {
            update_post_meta($post_id, '_tarefa_checklist_json', $checklist_json);
            error_log('Checklist salvo para tarefa ' . $post_id . ': ' . $checklist_json); // DEBUG
        }
    }

    wp_send_json_success([
        'message' => 'Tarefa "' . $titulo . '" criada com sucesso!',
        'nova_contagem_pendentes' => obter_contagem_tarefas_pendentes(),
        'post_id' => $post_id
    ]);
}

add_action('wp_ajax_criar_nova_tarefa_ajax', 'criar_nova_tarefa_ajax_callback');

/**
 * Função auxiliar para obter a contagem de tarefas pendentes.
 */
function obter_contagem_tarefas_pendentes() {
    $query = new WP_Query([
        'post_type' => 'tarefa',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'pendente'
            ]
        ],
        'meta_query' => [
            [
                'key' => '_tarefa_pessoal',
                'compare' => 'NOT EXISTS'
            ]
        ]
    ]);
    return $query->found_posts;
}

/**
 * AJAX: Adicionar responsável à tarefa
 */
function adicionar_responsavel_tarefa_ajax() {
    if (!check_ajax_referer('tarefa_responsaveis_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Erro de segurança']);
    }
    
    $post_id = intval($_POST['post_id']);
    $user_id = intval($_POST['user_id']);
    
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => 'Sem permissão']);
    }
    
    $responsaveis = get_post_meta($post_id, '_responsaveis_tarefa', true) ?: [];
    
    if (!in_array($user_id, $responsaveis)) {
        $responsaveis[] = $user_id;
        update_post_meta($post_id, '_responsaveis_tarefa', $responsaveis);
        
        $user = get_user_by('id', $user_id);
        wp_send_json_success([
            'message' => 'Responsável adicionado com sucesso',
            'user' => [
                'id' => $user_id,
                'name' => $user->display_name,
                'avatar' => get_avatar_url($user_id, ['size' => 32])
            ]
        ]);
    } else {
        wp_send_json_error(['message' => 'Usuário já é responsável']);
    }
}
add_action('wp_ajax_adicionar_responsavel_tarefa', 'adicionar_responsavel_tarefa_ajax');

/**
 * AJAX: Remover responsável da tarefa
 */
function remover_responsavel_tarefa_ajax() {
    if (!check_ajax_referer('tarefa_responsaveis_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Erro de segurança']);
    }
    
    $post_id = intval($_POST['post_id']);
    $user_id = intval($_POST['user_id']);
    $dono_id = get_post_meta($post_id, '_dono_tarefa', true);
    
    // Não permitir remover o dono
    if ($user_id == $dono_id) {
        wp_send_json_error(['message' => 'Não é possível remover o criador da tarefa']);
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => 'Sem permissão']);
    }
    
    $responsaveis = get_post_meta($post_id, '_responsaveis_tarefa', true) ?: [];
    $responsaveis = array_diff($responsaveis, [$user_id]);
    
    update_post_meta($post_id, '_responsaveis_tarefa', array_values($responsaveis));
    
    wp_send_json_success(['message' => 'Responsável removido com sucesso']);
}
add_action('wp_ajax_remover_responsavel_tarefa', 'remover_responsavel_tarefa_ajax');

/**
 * Função para debug - verificar responsáveis salvos
 */
function debug_responsaveis_tarefa($post_id) {
    $responsaveis = get_post_meta($post_id, '_responsaveis_tarefa', true);
    $dono = get_post_meta($post_id, '_dono_tarefa', true);
    
    error_log('DEBUG Tarefa ' . $post_id . ':');
    error_log('- Responsáveis: ' . print_r($responsaveis, true));
    error_log('- Dono: ' . $dono);
    
    return [
        'responsaveis' => $responsaveis,
        'dono' => $dono,
        'total' => is_array($responsaveis) ? count($responsaveis) : 0
    ];
}

// Adicionar ao functions-tarefas.php
function obter_metricas_tarefas() {
    $hoje = date('Y-m-d');
    $trinta_dias_atras = date('Y-m-d', strtotime('-30 days'));
    
    // Tarefas Em Andamento
    $em_andamento = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'em-andamento'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'compare' => 'NOT EXISTS']
        ]
    ]);
    
    // Tarefas Concluídas (últimos 30 dias)
    $concluidas = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'date_query' => [['after' => $trinta_dias_atras, 'inclusive' => true]],
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'concluida'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'compare' => 'NOT EXISTS']
        ]
    ]);
    
    // Tarefas Atrasadas
    $atrasadas = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => ['pendente', 'em-andamento']
            ]
        ],
        'meta_query' => [
            'relation' => 'AND',
            ['key' => '_tarefa_pessoal', 'compare' => 'NOT EXISTS'],
            ['key' => '_data_prazo_tarefa', 'value' => $hoje, 'compare' => '<', 'type' => 'DATE']
        ]
    ]);
    
    return [
        'em_andamento' => $em_andamento->found_posts,
        'concluidas' => $concluidas->found_posts,
        'atrasadas' => $atrasadas->found_posts
    ];
}

function obter_metricas_tarefas_completas() {
    $user_id = get_current_user_id();
    $hoje = date('Y-m-d');
    $trinta_dias_atras = date('Y-m-d', strtotime('-30 days'));
    
    // TAREFAS GLOBAIS (não pessoais) - Pendentes
    $globais_pendentes = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'pendente'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'compare' => 'NOT EXISTS']
        ]
    ]);
    
    // TAREFAS GLOBAIS - Em Andamento
    $globais_andamento = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'em-andamento'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'compare' => 'NOT EXISTS']
        ]
    ]);
    
    // TAREFAS PESSOAIS - Pendentes
    $pessoais_pendentes = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'author' => $user_id,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'pendente'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'value' => 'yes', 'compare' => '=']
        ]
    ]);
    
    // TAREFAS PESSOAIS - Em Andamento
    $pessoais_andamento = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'author' => $user_id,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'em-andamento'
            ]
        ],
        'meta_query' => [
            ['key' => '_tarefa_pessoal', 'value' => 'yes', 'compare' => '=']
        ]
    ]);
    
    // TAREFAS CONCLUÍDAS (últimos 30 dias)
    $concluidas = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'date_query' => [['after' => $trinta_dias_atras, 'inclusive' => true]],
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => 'concluida'
            ]
        ]
    ]);
    
    // TAREFAS ATRASADAS
    $atrasadas = new WP_Query([
        'post_type' => 'tarefa',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'status_tarefa',
                'field' => 'slug',
                'terms' => ['pendente', 'em-andamento']
            ]
        ],
        'meta_query' => [
            ['key' => '_data_prazo_tarefa', 'value' => $hoje, 'compare' => '<', 'type' => 'DATE']
        ]
    ]);
    
    return [
        'globais' => [
            'pendentes' => $globais_pendentes->found_posts,
            'andamento' => $globais_andamento->found_posts
        ],
        'pessoais' => [
            'pendentes' => $pessoais_pendentes->found_posts,
            'andamento' => $pessoais_andamento->found_posts
        ],
        'totais' => [
            'pendentes' => $globais_pendentes->found_posts + $pessoais_pendentes->found_posts,
            'andamento' => $globais_andamento->found_posts + $pessoais_andamento->found_posts
        ],
        'concluidas' => $concluidas->found_posts,
        'atrasadas' => $atrasadas->found_posts
    ];
}

