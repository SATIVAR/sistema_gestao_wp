<?php
/**
 * ISOLATED TASKS DASHBOARD FUNCTIONS
 * Completely separated from reports system
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Tasks Dashboard System
 */
function hg_init_tasks_dashboard() {
    // Only initialize on task dashboard pages
    if (!hg_is_tasks_dashboard_page()) {
        return;
    }
    
    // Register AJAX handlers
    hg_register_tasks_ajax_handlers();
    
    // Enqueue scripts
    add_action('wp_enqueue_scripts', 'hg_enqueue_tasks_scripts');
}
add_action('init', 'hg_init_tasks_dashboard');

/**
 * Check if current page is tasks dashboard
 */
function hg_is_tasks_dashboard_page() {
    global $template;
    
    if (!$template) {
        return false;
    }
    
    $template_name = basename($template);
    
    // Lista de templates que precisam do sistema de tarefas
    $task_templates = array(
        'dashboard-juridico-tarefas.php',
        'dashboard-modais-tarefas.php',
        'dashboard-juridico-dashboard.php',
        'dashboard-tarefas.php'
    );
    
    return in_array($template_name, $task_templates);
}

/**
 * Register all AJAX handlers for tasks
 */
function hg_register_tasks_ajax_handlers() {
    // Task management
    add_action('wp_ajax_criar_tarefa', 'hg_ajax_criar_tarefa');
    add_action('wp_ajax_buscar_usuarios_responsaveis', 'hg_ajax_buscar_usuarios_responsaveis');
    add_action('wp_ajax_search_associados', 'hg_ajax_search_associados');
    add_action('wp_ajax_atualizar_status_tarefa', 'hg_ajax_atualizar_status_tarefa');
    add_action('wp_ajax_excluir_tarefa', 'hg_ajax_excluir_tarefa');
    add_action('wp_ajax_editar_tarefa', 'hg_ajax_editar_tarefa');
}

/**
 * AJAX Handler: Create Task
 */
function hg_ajax_criar_tarefa() {
    try {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'criar_tarefa_nonce')) {
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            return;
        }
        
        // Check permissions
        if (!current_user_can('administrator') && !current_user_can('gerente')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
            return;
        }
        
        // Sanitize input
        $titulo = sanitize_text_field($_POST['titulo']);
        $descricao = sanitize_textarea_field($_POST['descricao']);
        $responsavel = intval($_POST['responsavel']);
        $prazo = sanitize_text_field($_POST['prazo']);
        $prioridade = sanitize_text_field($_POST['prioridade']);
        $associado_id = intval($_POST['associado_id']);
        
        // Validate required fields
        if (empty($titulo) || empty($descricao)) {
            wp_send_json_error(array('message' => 'Título e descrição são obrigatórios'));
            return;
        }
        
        // Create task post
        $task_data = array(
            'post_title' => $titulo,
            'post_content' => $descricao,
            'post_status' => 'publish',
            'post_type' => 'tarefa',
            'post_author' => get_current_user_id()
        );
        
        $task_id = wp_insert_post($task_data);
        
        if (is_wp_error($task_id)) {
            wp_send_json_error(array('message' => 'Erro ao criar tarefa'));
            return;
        }
        
        // Save meta fields
        update_post_meta($task_id, 'responsavel', $responsavel);
        update_post_meta($task_id, 'prazo', $prazo);
        update_post_meta($task_id, 'prioridade', $prioridade);
        update_post_meta($task_id, 'associado_id', $associado_id);
        update_post_meta($task_id, 'status', 'pendente');
        update_post_meta($task_id, 'data_criacao', current_time('Y-m-d H:i:s'));
        
        wp_send_json_success(array(
            'message' => 'Tarefa criada com sucesso',
            'task_id' => $task_id
        ));
        
    } catch (Exception $e) {
        error_log('Task creation error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro interno do servidor'));
    }
}

/**
 * AJAX Handler: Search Users for Responsaveis
 */
function hg_ajax_buscar_usuarios_responsaveis() {
    try {
        $search_term = sanitize_text_field($_POST['search']);
        
        $users = get_users(array(
            'search' => '*' . $search_term . '*',
            'search_columns' => array('display_name', 'user_login', 'user_email'),
            'number' => 10,
            'role__in' => array('administrator', 'gerente', 'funcionario')
        ));
        
        $results = array();
        foreach ($users as $user) {
            $results[] = array(
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email
            );
        }
        
        wp_send_json_success($results);
        
    } catch (Exception $e) {
        error_log('User search error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro ao buscar usuários'));
    }
}

/**
 * AJAX Handler: Search Associados
 */
function hg_ajax_search_associados() {
    try {
        $search_term = sanitize_text_field($_POST['search']);
        
        $associados = get_posts(array(
            'post_type' => 'associado',
            'post_status' => 'publish',
            's' => $search_term,
            'posts_per_page' => 10
        ));
        
        $results = array();
        foreach ($associados as $associado) {
            $results[] = array(
                'id' => $associado->ID,
                'name' => $associado->post_title
            );
        }
        
        wp_send_json_success($results);
        
    } catch (Exception $e) {
        error_log('Associado search error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro ao buscar associados'));
    }
}

/**
 * AJAX Handler: Update Task Status
 */
function hg_ajax_atualizar_status_tarefa() {
    try {
        $task_id = intval($_POST['task_id']);
        $new_status = sanitize_text_field($_POST['status']);
        
        // Validate status
        $allowed_statuses = array('pendente', 'em_andamento', 'concluida', 'cancelada');
        if (!in_array($new_status, $allowed_statuses)) {
            wp_send_json_error(array('message' => 'Status inválido'));
            return;
        }
        
        // Check if task exists
        $task = get_post($task_id);
        if (!$task || $task->post_type !== 'tarefa') {
            wp_send_json_error(array('message' => 'Tarefa não encontrada'));
            return;
        }
        
        // Update status
        update_post_meta($task_id, 'status', $new_status);
        update_post_meta($task_id, 'data_atualizacao', current_time('Y-m-d H:i:s'));
        
        wp_send_json_success(array('message' => 'Status atualizado com sucesso'));
        
    } catch (Exception $e) {
        error_log('Task status update error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro ao atualizar status'));
    }
}

/**
 * AJAX Handler: Delete Task
 */
function hg_ajax_excluir_tarefa() {
    try {
        $task_id = intval($_POST['task_id']);
        
        // Check permissions
        if (!current_user_can('administrator') && !current_user_can('gerente')) {
            wp_send_json_error(array('message' => 'Permissões insuficientes'));
            return;
        }
        
        // Check if task exists
        $task = get_post($task_id);
        if (!$task || $task->post_type !== 'tarefa') {
            wp_send_json_error(array('message' => 'Tarefa não encontrada'));
            return;
        }
        
        // Delete task
        $result = wp_delete_post($task_id, true);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Tarefa excluída com sucesso'));
        } else {
            wp_send_json_error(array('message' => 'Erro ao excluir tarefa'));
        }
        
    } catch (Exception $e) {
        error_log('Task deletion error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro interno do servidor'));
    }
}

/**
 * AJAX Handler: Edit Task
 */
function hg_ajax_editar_tarefa() {
    try {
        $task_id = intval($_POST['task_id']);
        $titulo = sanitize_text_field($_POST['titulo']);
        $descricao = sanitize_textarea_field($_POST['descricao']);
        $responsavel = intval($_POST['responsavel']);
        $prazo = sanitize_text_field($_POST['prazo']);
        $prioridade = sanitize_text_field($_POST['prioridade']);
        
        // Check if task exists
        $task = get_post($task_id);
        if (!$task || $task->post_type !== 'tarefa') {
            wp_send_json_error(array('message' => 'Tarefa não encontrada'));
            return;
        }
        
        // Update task
        $task_data = array(
            'ID' => $task_id,
            'post_title' => $titulo,
            'post_content' => $descricao
        );
        
        $result = wp_update_post($task_data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => 'Erro ao atualizar tarefa'));
            return;
        }
        
        // Update meta fields
        update_post_meta($task_id, 'responsavel', $responsavel);
        update_post_meta($task_id, 'prazo', $prazo);
        update_post_meta($task_id, 'prioridade', $prioridade);
        update_post_meta($task_id, 'data_atualizacao', current_time('Y-m-d H:i:s'));
        
        wp_send_json_success(array('message' => 'Tarefa atualizada com sucesso'));
        
    } catch (Exception $e) {
        error_log('Task edit error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Erro interno do servidor'));
    }
}

/**
 * Enqueue scripts for tasks dashboard
 */
function hg_enqueue_tasks_scripts() {
    if (!hg_is_tasks_dashboard_page()) {
        return;
    }
    
    // Tasks dashboard script
    wp_enqueue_script(
        'dashboard-tasks-js', 
        get_template_directory_uri() . '/assets/js/dashboard-tarefas.js', 
        array('jquery'), 
        time(), 
        true
    );
    
    // Localize script
    wp_localize_script('dashboard-tasks-js', 'dashboardAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('criar_tarefa_nonce'),
        'site_url' => get_site_url(),
        'template_directory' => get_template_directory_uri(),
        'is_user_logged_in' => is_user_logged_in(),
        'current_user' => wp_get_current_user()->ID,
        'debug' => defined('WP_DEBUG') && WP_DEBUG,
        'isolated_system' => true
    ));
}