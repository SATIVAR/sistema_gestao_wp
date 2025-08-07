<?php
/**
 * Restringe o acesso às páginas wp-login.php e wp-admin.php.
 *
 * Impede que usuários não logados e usuários logados que não são administradores
 * acessem as páginas wp-login.php e wp-admin.php. Eles serão redirecionados para a página inicial.
 * Apenas administradores logados podem acessar essas páginas.
 *
function restringir_wp_login() {
    // Verifica se a página atual é wp-login.php OU wp-admin.php e a ação não é 'logout'
    if ( 
        ($GLOBALS['pagenow'] === 'wp-login.php' || $GLOBALS['pagenow'] === 'wp-admin.php') 
        && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] !== 'logout')) 
    ) {
        // Cenário 1: Usuário NÃO ESTÁ LOGADO
        if (!is_user_logged_in()) {
            wp_redirect(home_url());
            exit();
        }
        
        // Cenário 2: Usuário ESTÁ LOGADO, mas NÃO É ADMINISTRADOR
        if (is_user_logged_in() && !current_user_can('manage_options')) {
            wp_redirect(home_url());
            exit();
        }
    }
}

// Adiciona a função para ser executada no carregamento inicial do WordPress
add_action('init', 'restringir_wp_login');
*/
// Carrega sistema isolado primeiro, pois contém funções base
function load_isolated_systems() {
    // Sistema isolado de relatórios (deve ser carregado primeiro)
    $reports_isolated_path = get_template_directory() . '/functions-relatorios-isolated.php';
    if (file_exists($reports_isolated_path)) {
        require_once $reports_isolated_path;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Sistema isolado de relatórios carregado com sucesso');
        }
    }
}

// Garante que as funções do WooCommerce sejam carregadas na ordem correta
function load_woocommerce_functions() {
    // Se o WooCommerce não estiver ativo, não faz nada
    if (!class_exists('WooCommerce')) {
        error_log('WooCommerce não está ativo - funções não foram carregadas');
        return;
    }

    // Carrega funções isoladas primeiro
    load_isolated_systems();
    
    // Funções do WooCommerce - carrega primeiro pois outras funções dependem dele
    $woo_functions_path = get_stylesheet_directory() . '/functions-woocommerce.php';
    if (file_exists($woo_functions_path)) {
        require_once $woo_functions_path;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Funções do WooCommerce carregadas com sucesso');
        }
    } else {
        error_log('Arquivo de funções do WooCommerce não encontrado: ' . $woo_functions_path);
        return;
    }

    // Por último carrega as funções principais de relatórios que dependem do WooCommerce
    $reports_functions_path = get_stylesheet_directory() . '/functions-relatorios.php';
    if (file_exists($reports_functions_path)) {
        require_once $reports_functions_path;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Funções de relatórios carregadas com sucesso');
        }
    } else {
        error_log('Arquivo de funções de relatórios não encontrado: ' . $reports_functions_path);
    }

    // Remove transients antigos para evitar problemas de cache
    delete_transient('hg_gross_sales_all');
    delete_transient('hg_gross_sales_month');
    delete_transient('hg_gross_sales_week');
    delete_transient('hg_gross_sales_year');
}

// Garante que o WordPress está carregado antes de qualquer outra coisa
if (!defined('ABSPATH')) {
    exit;
}

// Hook principal para carregar as funções do WooCommerce
add_action('plugins_loaded', 'load_woocommerce_functions', 10);

// Hook de backup para garantir que as funções sejam carregadas
add_action('init', 'load_woocommerce_functions', 5);

// Hook final para garantir que as funções estejam disponíveis para templates
add_action('wp', 'load_woocommerce_functions', 5);

// Limpar cache de relatórios quando um pedido é atualizado
add_action('woocommerce_order_status_changed', function($order_id) {
    delete_transient('hg_gross_sales_all');
    delete_transient('hg_gross_sales_month');
    delete_transient('hg_gross_sales_week');
    delete_transient('hg_gross_sales_year');
}, 10, 1);

// Include isolated dashboard systems
try {
    require_once get_template_directory() . '/functions-tarefas-isolated.php';
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Tasks isolated system loaded successfully');
    }
} catch (Exception $e) {
    error_log('Error loading tasks isolated system: ' . $e->getMessage());
}

// Load other function files
require_once( get_stylesheet_directory() . '/functions-juridico.php' );
require_once( get_stylesheet_directory() . '/functions-tarefas.php' );
require_once( get_stylesheet_directory() . '/functions-relatorios-associados.php' );
require_once( get_stylesheet_directory() . '/functions-user-profile.php' );

function enqueue_dashboard_scripts() {
    if (is_page_template('dashboard-relatorios-associados.php')) {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
        wp_enqueue_script('dashboard-relatorios-associados-js', get_template_directory_uri() . '/assets/js/dashboard-relatorios-associados.js', array('jquery', 'chart-js'), null, true);
        wp_enqueue_style('dashboard-relatorios-associados-css', get_template_directory_uri() . '/assets/css/dashboard-relatorios-associados.css', array(), null, 'all');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');

        // Passa a URL da API REST e o nonce para o JavaScript
        wp_localize_script('dashboard-relatorios-associados-js', 'dashboardData', array(
            'apiUrl' => esc_url_raw(rest_url('associados/v1/stats')),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_dashboard_scripts');





add_action('wp_ajax_salvar_configuracoes_app', 'hg_salvar_configuracoes_app');
add_action('wp_ajax_nopriv_salvar_configuracoes_app', 'hg_salvar_configuracoes_app');

function hg_salvar_configuracoes_app() {
    // Verificar o nonce para segurança
    if (!isset($_POST['form_configapp_nonce']) || !wp_verify_nonce($_POST['form_configapp_nonce'], 'form_configapp_action')) {
        wp_send_json_error(array('mensagem' => 'Erro de segurança.'));
        wp_die();
    }

    error_log('Dados POST recebidos (INÍCIO DA FUNÇÃO): ' . print_r($_POST, true));
    $page_id = 113;
    error_log('ID da página sendo usado para salvar: ' . $page_id);

    $sucesso = true;
    $mensagem_sucesso = 'Configurações salvas com sucesso!';
    $erros = array();

    // Atualizar outros campos ACF (incluindo o texto de apresentação)
    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $field_value) {
            // Evitar atualizar os campos de imagem aqui, vamos fazer isso separadamente
            if ($field_key !== 'field_67fc5f59b750d' && $field_key !== 'field_67fc5f78b750e') {
                update_field($field_key, $field_value, $page_id); // Use $page_id aqui
            }
        }
    }

   // Atualizar os IDs das logos (agora vindo dos campos hidden)
    if (isset($_POST['logo_horizontal_id']) && !empty($_POST['logo_horizontal_id'])) {
        error_log('Antes de atualizar logo horizontal - ID: ' . $_POST['logo_horizontal_id']);
        update_field('field_67fc5f59b750d', intval($_POST['logo_horizontal_id']), $page_id);
        error_log('Depois de atualizar logo horizontal - ID: ' . get_field('logo_horizontal', $page_id));
    } else {
        error_log('Antes de remover logo horizontal');
        update_field('field_67fc5f59b750d', '', $page_id);
        error_log('Depois de remover logo horizontal - Valor: ' . get_field('logo_horizontal', $page_id));
    }

    if (isset($_POST['logo_vertical_id']) && !empty($_POST['logo_vertical_id'])) {
        error_log('Antes de atualizar logo vertical - ID: ' . $_POST['logo_vertical_id']);
        update_field('field_67fc5f78b750e', intval($_POST['logo_vertical_id']), $page_id);
        error_log('Depois de atualizar logo vertical - ID: ' . get_field('logo_vertical', $page_id));
    } else {
        error_log('Antes de remover logo vertical');
        update_field('field_67fc5f78b750e', '', $page_id);
        error_log('Depois de remover logo vertical - Valor: ' . get_field('logo_vertical', $page_id));
    }

    // Processar checkbox de debug
    $show_debug = isset($_POST['show_debug_button']) ? 1 : 0;
    update_option('amedis_show_debug_button', $show_debug);
    error_log('Debug button setting salvo: ' . $show_debug);

    if ($sucesso) {
        wp_send_json_success(array('mensagem' => $mensagem_sucesso));
    } else {
        wp_send_json_error(array('erros' => $erros));
    }

    wp_die();
}


add_action('wp_ajax_get_attachment_id_from_url', 'get_attachment_id_from_url_callback');
add_action('wp_ajax_nopriv_get_attachment_id_from_url', 'get_attachment_id_from_url_callback');

add_action('wp_ajax_save_debug_button_setting', 'save_debug_button_setting');
add_action('wp_ajax_nopriv_save_debug_button_setting', 'save_debug_button_setting');

function save_debug_button_setting() {
    if (!wp_verify_nonce($_POST['nonce'], 'save_debug_button_setting')) {
        wp_die('Nonce inválido');
    }
    
    $show_debug = isset($_POST['show_debug']) && $_POST['show_debug'] === 'true' ? 1 : 0;
    update_option('amedis_show_debug_button', $show_debug);
    
    wp_send_json_success(array('message' => 'Configuração salva'));
}


function get_attachment_id_from_url_callback() {
    $logo_horizontal_url = isset($_POST['logo_horizontal_url']) ? sanitize_url($_POST['logo_horizontal_url']) : '';
    $logo_vertical_url = isset($_POST['logo_vertical_url']) ? sanitize_url($_POST['logo_vertical_url']) : '';

    $response = array('success' => false, 'data' => array());

    if ($logo_horizontal_url) {
        $logo_horizontal_id = attachment_url_to_postid($logo_horizontal_url);
        if ($logo_horizontal_id) {
            $response['data']['logo_horizontal_id'] = $logo_horizontal_id;
        }
    }

    if ($logo_vertical_url) {
        $logo_vertical_id = attachment_url_to_postid($logo_vertical_url);
        if ($logo_vertical_id) {
            $response['data']['logo_vertical_id'] = $logo_vertical_id;
        }
    }

    if (!empty($response['data'])) {
        $response['success'] = true;
    }

    wp_send_json($response);
}

add_action('wp_ajax_get_image_url_by_id', 'get_image_url_by_id_callback');
add_action('wp_ajax_nopriv_get_image_url_by_id', 'get_image_url_by_id_callback');

function get_image_url_by_id_callback() {
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
    $size = isset($_POST['size']) ? sanitize_text_field($_POST['size']) : 'thumbnail';

    $response = array('success' => false, 'data' => array());

    if ($attachment_id) {
        $image_data = wp_get_attachment_image_src($attachment_id, $size);
        if ($image_data) {
            $response['success'] = true;
            $response['data']['url'] = $image_data[0];
        } else {
            $response['data']['message'] = 'Imagem não encontrada com o ID fornecido.';
        }
    } else {
        $response['data']['message'] = 'ID do anexo não fornecido.';
    }

    wp_send_json($response);
}


// pagina de configuracoes do app

function hg_exibir_campo_acf($nome_do_campo, $tipo_campo = 'texto', $slug_pagina = '') {
    if ($slug_pagina) {
        $pagina = get_page_by_path($slug_pagina);
        if ($pagina) {
            $valor = get_field($nome_do_campo, $pagina->ID);

            if ($valor) {
                switch ($tipo_campo) {
                    case 'img':
                        // Se o campo for do tipo imagem
                        if ($valor) {
                            // Se for uma URL (configuração 'URL da Imagem' no ACF)
                            if (is_string($valor) && filter_var($valor, FILTER_VALIDATE_URL)) {
                                return esc_url($valor);
                            }
                            // Se for um ID (configuração 'ID do Anexo' no ACF)
                            elseif (is_numeric($valor)) {
                                $url = wp_get_attachment_image_src($valor, 'full')[0];
                                if ($url) {
                                    return esc_url($url);
                                }
                            }
                            // Se for um array (configuração 'Objeto de Imagem' no ACF)
                            elseif (is_array($valor) && isset($valor['url'])) {
                                return esc_url($valor['url']);
                            }
                            // Se for um objeto (configuração 'Objeto de Imagem' no ACF)
                            elseif (is_object($valor) && isset($valor->url)) {
                                return esc_url($valor->url);
                            }
                        }
                        break;

                    case 'texto':
                        return wp_kses_post($valor);
                        break;

                    case 'editor':
                        return apply_filters('the_content', $valor);
                        break;

                    default:
                        return wp_kses_post($valor); // Retorna o valor original com segurança por padrão
                        break;
                }
            }
        } else {
            error_log('Página com o slug "' . $slug_pagina . '" não encontrada.');
        }
    }
    return ''; // Retorna uma string vazia se o campo não tiver valor ou a página não for encontrada
}

// campos configuracao no tema

// app

add_theme_support('post-thumbnails');

function add_ajax_url() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var salvarMensagemNonce = "<?php echo wp_create_nonce('salvar_mensagem_nonce'); ?>";
        var editarEntradaNonce = "<?php echo wp_create_nonce('editar_entrada_action'); ?>";
        var criarPedidoNonce = "<?php echo wp_create_nonce('criar_pedido_nonce'); ?>";
        var editarPedidoNonce = "<?php echo wp_create_nonce('editar_pedido_nonce'); ?>";
        var getReceitasNonce = "<?php echo wp_create_nonce('get_receitas_nonce'); ?>";
        var createproductnonce = "<?php echo wp_create_nonce('create_product_nonce'); ?>";
        var updateproductnonce = "<?php echo wp_create_nonce('update_product_nonce'); ?>";
        
        // Objeto global para centralizar configurações AJAX
        window.AmedisAjax = {
            url: ajaxurl,
            nonces: {
                salvarMensagem: salvarMensagemNonce,
                editarEntrada: editarEntradaNonce,
                criarPedido: criarPedidoNonce,
                editarPedido: editarPedidoNonce,
                getReceitas: getReceitasNonce,
                createProduct: createproductnonce,
                updateProduct: updateproductnonce
            }
        };
        
        // Debug: Log para verificar se as variáveis estão sendo definidas
        console.log('AJAX URL:', ajaxurl);
        console.log('Update Product Nonce:', updateproductnonce);
        console.log('AmedisAjax Object:', window.AmedisAjax);
    </script>
    <?php
}
add_action('wp_head', 'add_ajax_url');

// Handler de teste para verificar se AJAX está funcionando
add_action('wp_ajax_test_ajax_connection', 'test_ajax_connection_callback');
add_action('wp_ajax_nopriv_test_ajax_connection', 'test_ajax_connection_callback');

function test_ajax_connection_callback() {
    wp_send_json_success(array('message' => 'AJAX está funcionando corretamente!'));
}


// Função para verificar se o usuário tem acesso a uma área específica
function user_has_area_access($area) {
    if (current_user_can('administrator')) {
        return true; // Admin sempre tem acesso
    }
    
    if (!is_user_logged_in()) {
        return false;
    }
    
    $user_id = get_current_user_id();
    $area_liberada = get_user_meta($user_id, 'area_liberada_' . $area, true);
    
    return $area_liberada === '1';
}


// bloquear api
/*
function bloquear_api_rest_para_usuarios_nao_autenticados($access) {
    // Permite acesso total à API para usuários autenticados
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', 'Você não tem permissão para acessar esta API.', array('status' => 403));
    }

    return $access;
}
add_filter('rest_authentication_errors', 'bloquear_api_rest_para_usuarios_nao_autenticados');
*/





// Função para redirecionar usuários após o login com base no tipo de usuário
/*
function custom_login_redirect($redirect_to, $requested_redirect_to, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles) || in_array('gerente', $user->roles)) {
            return wp_get_referer() ? wp_get_referer() : home_url();
        } elseif (in_array('associados', $user->roles)) {
            return home_url('/usuario/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'custom_login_redirect', 20, 3);

*/


// resetar funções

function reset_user_roles() {
    global $wp_roles;

    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    // Obtém todas as funções
    $all_roles = $wp_roles->roles;

    // Funções padrão do WordPress
    $default_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

    // Remove todas as funções, exceto as funções padrão
    foreach ($all_roles as $role => $details) {
        if (!in_array($role, $default_roles)) {
            remove_role($role);
        }
    }
}
add_action('init', 'reset_user_roles');

// Função para adicionar o papel "Associados" com capacidades do "Autor"
function add_custom_user_role() {
    // Obtém o papel "Autor"
    $author_role = get_role('author');
    
    // Adiciona o papel "Associados" com as mesmas capacidades do "Autor"
    add_role(
        'associados',
        __('Associados'),
        $author_role->capabilities
    );
}
add_action('init', 'add_custom_user_role');

// Função para atualizar capacidades do papel "Associados"
function update_custom_user_role() {
    $role = get_role('associados');

    // Adicionando capacidades, se necessário
    $role->add_cap('edit_others_posts');
    $role->add_cap('delete_others_posts');

    // Removendo capacidades, se necessário
    $role->remove_cap('publish_pages');
}
add_action('init', 'update_custom_user_role');


// Função para adicionar o papel "Gerente" com capacidades do "Autor"
function add_manager_user_role() {
    // Obtém o papel "Autor"
    $author_role = get_role('author');
    
    // Adiciona o papel "Gerente" com as mesmas capacidades do "Autor"
    add_role(
        'gerente',
        __('Gerente'),
        $author_role->capabilities
    );
}
add_action('init', 'add_manager_user_role');

// Função para atualizar capacidades do papel "Gerente"
function update_manager_user_role() {
    $role = get_role('gerente');

    // Adicionando capacidades, se necessário
    $role->add_cap('edit_others_posts');
    $role->add_cap('delete_others_posts');

    // Removendo capacidades, se necessário
    $role->remove_cap('publish_pages');
}
add_action('init', 'update_manager_user_role');


// funcao paciente_chat

// Função para criar a função de usuário "paciente_chat"
function create_paciente_chat_role() {
    // Verifica se a função já existe para evitar problemas
    if (!get_role('paciente_chat')) {
        // Adiciona a função de usuário com permissões específicas
        add_role(
            'paciente_chat', // Nome interno da função
            __('Paciente Chat'), // Nome exibido no admin
            array(
                'read' => true, // Permissão para ler posts/páginas
                'edit_posts' => false, // Não pode editar posts
                'delete_posts' => false, // Não pode deletar posts
                'upload_files' => true, // Pode fazer upload de arquivos, caso seja necessário
            )
        );
    }
}
add_action('init', 'create_paciente_chat_role');


// Função para remover a função de usuário "paciente_chat"
function remove_paciente_chat_role() {
    // Remove a função de usuário personalizada
    remove_role('paciente_chat');
}
register_deactivation_hook(__FILE__, 'remove_paciente_chat_role');

// prescritor

function criar_funcao_prescritor() {
    add_role(
        'prescritor', // Nome interno da função
        'Prescritor', // Nome exibido no painel do WP
        array(
            'read' => true, // Permitir que o prescritor leia posts
            'edit_posts' => true, // Permitir que edite seus próprios posts
            'delete_posts' => false, // Impedir que o prescritor delete posts
            'publish_posts' => true, // Permitir que publique seus próprios posts
            'edit_others_posts' => false, // Impedir que edite posts de outros usuários
            'delete_others_posts' => false, // Impedir que delete posts de outros usuários
            'edit_published_posts' => true, // Permitir editar seus posts publicados
            'delete_published_posts' => false, // Impedir de deletar seus próprios posts publicados
            'upload_files' => true, // Permitir o upload de arquivos
        )
    );
}
add_action('init', 'criar_funcao_prescritor');


function bloquear_exclusao_edicao_prescritor($allcaps, $cap, $args, $user) {
    // Verifica se o usuário é da função 'prescritor'
    if (in_array('prescritor', (array) $user->roles)) {
        // Remove capacidades de deletar ou editar posts de outros
        if (isset($allcaps['delete_others_posts'])) {
            $allcaps['delete_others_posts'] = false;
        }
        if (isset($allcaps['edit_others_posts'])) {
            $allcaps['edit_others_posts'] = false;
        }
        if (isset($allcaps['delete_post'])) {
            $allcaps['delete_post'] = false;
        }
    }
    return $allcaps;
}
add_filter('user_has_cap', 'bloquear_exclusao_edicao_prescritor', 10, 4);



// obter dados de usuarios associados
function obter_dados_associados() {
    $users = get_users(array('role' => 'associados'));
    return $users;
}


// Função para contar associados, geral ou por tipo
function contar_associados($tipo_associacao = null) {
    // Define a query base para usuários com a função 'associado'
    $meta_query = array(
        // Verifica se o campo 'associado' está marcado como verdadeiro
        array(
            'key' => 'associado',
            'value' => '1', // Verdadeiro
            'compare' => 'LIKE'
        )
    );

    // Se um tipo de associação for passado, adiciona essa condição à query
    if ($tipo_associacao) {
        $meta_query[] = array(
            'key' => 'tipo_associacao',
            'value' => $tipo_associacao,
            'compare' => '='
        );
    }

    // Busca os usuários com base na query
    $users = get_users(array(
        'role' => 'associados',
        'meta_query' => $meta_query
    ));
    
    // Retorna a contagem de usuários
    return count($users);
}

// Função do shortcode que utiliza a função contar_associados
function shortcode_contagem_associados($atts) {
    // Define o valor padrão do atributo 'tipo' como null (contagem geral)
    $atts = shortcode_atts(array(
        'tipo' => null
    ), $atts);

    // Chama a função contar_associados com o tipo, se fornecido
    return contar_associados($atts['tipo']);
}
add_shortcode('contagem_associados', 'shortcode_contagem_associados');


// Função para contar usuários onde 'associado' é verdadeiro e 'associado_ativado' é verdadeiro ou falso
// Envolva a função dentro do hook 'init'
add_action('init', function() {
    
    // Função para contar usuários onde 'associado' é verdadeiro e 'associado_ativado' é verdadeiro ou falso
    function contar_usuarios_associado_ativado($status) {
        // Define uma query para buscar usuários com o campo 'associado' verdadeiro
        $meta_query = array(
            // Primeiro, checa se o campo 'associado' é verdadeiro
            array(
                'key' => 'associado',
                'value' => '1', // Verdadeiro
                'compare' => '=' // Comparação exata
            ),
        );

        // Adiciona uma condição dependendo do valor de status (1 ou 0)
        if ($status == 1) {
            // Se estamos procurando usuários ativados (1)
            $meta_query[] = array(
                'key' => 'associado_ativado',
                'value' => '1', // Procuramos por usuários onde 'associado_ativado' é 1
                'compare' => '='
            );
        } else {
            // Se estamos procurando usuários não ativados (0)
            $meta_query[] = array(
                'relation' => 'OR',
                // Usuários onde 'associado_ativado' é explicitamente 0
                array(
                    'key' => 'associado_ativado',
                    'value' => '0',
                    'compare' => '='
                ),
                // OU usuários onde o campo 'associado_ativado' está ausente ou vazio (caso o campo não tenha sido preenchido)
                array(
                    'key' => 'associado_ativado',
                    'compare' => 'NOT EXISTS' // Campo não existe
                ),
                array(
                    'key' => 'associado_ativado',
                    'value' => '', // Campo vazio
                    'compare' => '='
                )
            );
        }

        // Busca os usuários com base na query
        $users = get_users(array(
            'role' => 'associados',
            'meta_query' => $meta_query
        ));

        // Retorna a contagem de usuários
        return count($users);
    }


    // Shortcode para contar usuários com 'associado_ativado' verdadeiro e 'associado' verdadeiro
    function shortcode_contagem_associado_ativado_verdadeiro() {
        return contar_usuarios_associado_ativado(1); // Verdadeiro
    }
    add_shortcode('contagem_associado_ativado_verdadeiro', 'shortcode_contagem_associado_ativado_verdadeiro');

    // Shortcode para contar usuários com 'associado_ativado' falso e 'associado' verdadeiro
    function shortcode_contagem_associado_ativado_falso() {
        return contar_usuarios_associado_ativado(0); // Falso
    }
    add_shortcode('contagem_associado_ativado_falso', 'shortcode_contagem_associado_ativado_falso');

});






// Função para redirecionar usuários não autorizados ao tentar acessar páginas específicas
/*
function restrict_pages_to_authorized_users() {
    if (is_page()) {
        global $post;
        $login_required = get_post_meta($post->ID, '_login_required', true);

        // Verifica se o usuário está logado e se a página requer login
        if ($login_required === 'yes') {
            // Verifica se o usuário está logado
            if (!is_user_logged_in()) {
                wp_redirect(home_url());
                exit;
            }

            // Verifica a função do usuário
            $user = wp_get_current_user();
            if (!in_array('administrator', $user->roles) && !in_array('gerente', $user->roles)) {
                wp_redirect(home_url());
                exit;
            }
        }
    }
}
add_action('template_redirect', 'restrict_pages_to_authorized_users');
*/


// logout header drop
// Função para exibir o nome do usuário logado 
/*
function exibir_nome_usuario_logado() {
    if ( is_user_logged_in() ) {
        $usuario_atual = wp_get_current_user();
        echo 'Olá, ' . esc_html( $usuario_atual->display_name ) . '!';
    } else {
        echo 'Olá, visitante!';
    }
}

// Adiciona um shortcode para exibir o nome do usuário
add_shortcode( 'nome_usuario_logado', 'exibir_nome_usuario_logado' );
*/

// Função para gerar o link de logout
function gerar_link_logout() {
    $logout_url = wp_logout_url( home_url() );
    echo '<a href="' . esc_url( $logout_url ) . '" class="">Sair</a>';
}

// Adiciona um shortcode para o link de logout
add_shortcode( 'link_logout', 'gerar_link_logout' );


// remove <p> tag contact form 7
add_filter('wpcf7_autop_or_not', '__return_false');


//Disable Admin Bar for All Users Except Administrators Using Code
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}


// enqueue styles
if( !function_exists("uikit_theme_styles") ) {
    function uikit_theme_styles() {
        // This is the compiled css file from LESS - this means you compile the LESS file locally and put it in the appropriate directory if you want to make any changes to the master

   wp_register_style( 'uikit-style', get_stylesheet_directory_uri() . '/assets/css/uikit.min.css', array(), null, 'all' );
   wp_enqueue_style( 'uikit-style' );

    //wp_register_style( 'main-style', get_stylesheet_directory_uri() . '/assets/main.css', array(), null, 'all' );
    //wp_enqueue_style( 'main-style' );

   wp_register_style( 'style-style', get_stylesheet_directory_uri() . '/style.css', array(), null, 'all' );
   wp_enqueue_style( 'style-style' );    

    // For child themes
    wp_register_style( 'custom-style', get_stylesheet_directory_uri() . '/assets/custom.css', array(), null, 'all' );
    wp_enqueue_style( 'custom-style' );


    }
}
add_action( 'wp_enqueue_scripts', 'uikit_theme_styles' );

// enqueue javascript
if( !function_exists( "uikit_theme_js" ) ) {
  function uikit_theme_js(){
    wp_enqueue_media();
    wp_enqueue_script('jquery');
 
  wp_register_script('jQuery-scripts', 'https://code.jquery.com/jquery-3.6.0.min.js', array('jquery'), null, false);
  wp_enqueue_script('jQuery-scripts'); 
  // Particles
    //   // jQuery
  //wp_register_script('particles-scripts', 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js', array('jquery'), null, false);
  //wp_enqueue_script('particles-scripts');  

  

 wp_enqueue_script( 'uikitjs', get_template_directory_uri() . '/assets/js/uikit.min.js', array ( 'jquery' ), 1.1, true);
 wp_enqueue_script( 'uikiticonjs', get_template_directory_uri() . '/assets/js/uikit-icons.min.js', array ( 'jquery' ), 1.1, true);
  
  // //masks
  // wp_enqueue_script( 'maskjs', get_template_directory_uri() . '/jquery.mask.js', array ( 'jquery' ), 1.1, true);

  // //custom
  wp_enqueue_script( 'mainjs', get_template_directory_uri() . '/assets/js/main.js', array ( 'jquery' ), 1.1, true);
  //* wp_enqueue_script( 'imagesjs', get_template_directory_uri() . '/assets/imageComparsion/BeerSlider.js', array ( 'jquery' ), 1.1, true);

    if ( !is_admin() ){
      if ( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) == 1) )
        wp_enqueue_script( 'comment-reply' );
    }




  }
}
add_action( 'wp_enqueue_scripts', 'uikit_theme_js' );

// add author table admin custom post

function add_author_support_to_posts() {
   add_post_type_support( 'atendimento', 'author' );
   add_post_type_support( 'itens_atendimento', 'author' ); 
}
add_action( 'init', 'add_author_support_to_posts' );


// Remove <p> tags from around images
function uikit_filter_ptags_on_images( $content ){
  return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content', 'uikit_filter_ptags_on_images' );

// Produtos
/*
function produtos_cpt() {

    $args = array(
        'label'  => 'Produtos',
        'public' => true,
        //'show_in_rest' => true,
        'supports' => array('title', 'thumbnail')
    );

    register_post_type( 'produtos', $args );
}

add_action( 'init', 'produtos_cpt' );

// Entradas
function entradas_cpt() {

    $args = array(
        'label'  => 'Entradas',
        'public' => true,
        //'show_in_rest' => true,
        'supports' => array('title')
    );

    register_post_type( 'entradas', $args );
}

add_action( 'init', 'entradas_cpt' );
*/
// Saidas
function saidas_cpt() {

    $args = array(
        'label'  => 'Saidas',
        'public' => true,
        //'show_in_rest' => true,
        'supports' => array('title')
    );

    register_post_type( 'saidas', $args );
}

add_action( 'init', 'saidas_cpt' );

// CUSTOM entradas e saidas

//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'tipoentradas_taxonomy', 0 );

function tipoentradas_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Tipo Entrada', 'taxonomy general name' ),
    'singular_name' => _x( 'Tipo Entrada', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar Tipo Entrada' ),
    'popular_items' => __( 'Tipo Entrada Recentes' ),
    'all_items' => __( 'Todos Tipo Entrada' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Editar Tipo Entrada' ),
    'update_item' => __( 'Atualizar Tipo Entrada' ),
    'add_new_item' => __( 'Novo Tipo Entrada' ),
    'new_item_name' => __( 'Novo Tipo Entrada Nome' ),
    'separate_items_with_commas' => __( 'Separate topics with commas' ),
    'add_or_remove_items' => __( 'Adc ou Remover Tipo Entrada' ),
    'choose_from_most_used' => __( 'Choose from the most used topics' ),
    'menu_name' => __( 'Tipo Entrada' ),
  );

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('tipo-entrada',array('entradas'),array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'tipo-entrada' ),
  ));
}

// Nova Taxonomia para Saídas: Categoria de Saída
add_action( 'init', 'categorias_saida_taxonomy', 0 );

function categorias_saida_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Categorias de Saída', 'taxonomy general name' ),
        'singular_name'              => _x( 'Categoria de Saída', 'taxonomy singular name' ),
        'search_items'               => __( 'Buscar Categorias de Saída' ),
        'popular_items'              => __( 'Categorias de Saída Populares' ),
        'all_items'                  => __( 'Todas as Categorias de Saída' ),
        'parent_item'                => __( 'Categoria de Saída Pai' ),
        'parent_item_colon'          => __( 'Categoria de Saída Pai:' ),
        'edit_item'                  => __( 'Editar Categoria de Saída' ),
        'update_item'                => __( 'Atualizar Categoria de Saída' ),
        'add_new_item'               => __( 'Adicionar Nova Categoria de Saída' ),
        'new_item_name'              => __( 'Novo Nome da Categoria de Saída' ),
        'separate_items_with_commas' => __( 'Separar categorias de saída com vírgulas' ),
        'add_or_remove_items'        => __( 'Adicionar ou remover categorias de saída' ),
        'choose_from_most_used'      => __( 'Escolher entre as categorias de saída mais usadas' ),
        'menu_name'                  => __( 'Categorias de Saída' ),
    );

    $args = array(
        'hierarchical'          => true, // Define como hierárquica (como categorias)
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'categoria-saida' ),
    );

    register_taxonomy( 'categoria-saida', array( 'saidas' ), $args );
}

// CUSTOM entradas e saidas

//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'pagentradas_taxonomy', 0 );

function pagentradas_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Status Pagamento', 'taxonomy general name' ),
    'singular_name' => _x( 'Status Pagamento', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar Status Pagamento' ),
    'popular_items' => __( 'Status Pagamento Recentes' ),
    'all_items' => __( 'Todos Status Pagamento' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Editar Status Pagamento' ),
    'update_item' => __( 'Atualizar Status Pagamento' ),
    'add_new_item' => __( 'Novo Status Pagamento' ),
    'new_item_name' => __( 'Novo Status Pagamento Nome' ),
    'separate_items_with_commas' => __( 'Separate topics with commas' ),
    'add_or_remove_items' => __( 'Adc ou Remover Status Pagamento' ),
    'choose_from_most_used' => __( 'Choose from the most used topics' ),
    'menu_name' => __( 'Status Pagamento' ),
  );

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('status-pagamento',array('entradas'),array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'status-pagamento' ),
  ));
}

//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'estregaentradas_taxonomy', 0 );

function estregaentradas_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Status Entrega', 'taxonomy general name' ),
    'singular_name' => _x( 'Status Entrega', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar Status Entrega' ),
    'popular_items' => __( 'Status Entrega Recentes' ),
    'all_items' => __( 'Todos Status Entrega' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Editar Status Entrega' ),
    'update_item' => __( 'Atualizar Status Entrega' ),
    'add_new_item' => __( 'Novo Status Entrega' ),
    'new_item_name' => __( 'Novo Status Entrega Nome' ),
    'separate_items_with_commas' => __( 'Separate topics with commas' ),
    'add_or_remove_items' => __( 'Adc ou Remover Status Entrega' ),
    'choose_from_most_used' => __( 'Choose from the most used topics' ),
    'menu_name' => __( 'Status Entrega' ),
  );

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('status-entrega',array('entradas'),array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'status-entrega' ),
  ));
}


//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'extracaoentradas_taxonomy', 0 );

function extracaoentradas_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Status Extração', 'taxonomy general name' ),
    'singular_name' => _x( 'Status Extração', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar Status Extração' ),
    'popular_items' => __( 'Status Extração Recentes' ),
    'all_items' => __( 'Todos Status Extração' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Editar Status Extração' ),
    'update_item' => __( 'Atualizar Status Extração' ),
    'add_new_item' => __( 'Novo Status Extração' ),
    'new_item_name' => __( 'Novo Status Extração Nome' ),
    'separate_items_with_commas' => __( 'Separate topics with commas' ),
    'add_or_remove_items' => __( 'Adc ou Remover Status Extração' ),
    'choose_from_most_used' => __( 'Choose from the most used topics' ),
    'menu_name' => __( 'Status Extração' ),
  );

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('status-extracao',array('entradas'),array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'status-extracao' ),
  ));
}

//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires

add_action( 'init', 'tipoitensfin_taxonomy', 0 );

function tipoitensfin_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Tipo Itens', 'taxonomy general name' ),
    'singular_name' => _x( 'Tipo Itens', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar Tipo Itens' ),
    'popular_items' => __( 'Tipo Itens Recentes' ),
    'all_items' => __( 'Todos Tipo Itens' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Editar Tipo Itens' ),
    'update_item' => __( 'Atualizar Tipo Itens' ),
    'add_new_item' => __( 'Novo Tipo Itens' ),
    'new_item_name' => __( 'Novo Tipo Itens Nome' ),
    'separate_items_with_commas' => __( 'Separate topics with commas' ),
    'add_or_remove_items' => __( 'Adc ou Remover Tipo Itens' ),
    'choose_from_most_used' => __( 'Choose from the most used topics' ),
    'menu_name' => __( 'Tipo Itens' ),
  );

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('tipo-itens',array('itens'),array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'tipo-itens' ),
  ));
}

// Banner Home
function itens_cpt() {

    $args = array(
        'label'  => 'Itens Entrada/Saida',
        'public' => true,
        //'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type( 'itens', $args );
}

add_action( 'init', 'itens_cpt' );





// Atendimento itens
function receitas_cpt() {

    $args = array(
        'label'  => 'Receitas',
        'public' => true,
        //'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type( 'receitas', $args );
}

add_action( 'init', 'receitas_cpt' );

// limitar excerpt

function custom_excerpt_length($text, $limit = 100) {
    if (strlen($text) <= $limit) {
        return $text;
    }
    $last_space = strrpos(substr($text, 0, $limit), ' ');
    return substr($text, 0, $last_space) . '...';
}

remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );


// LOGIN
function ajax_login_init() {
    // Registra e enfileira o script de login AJAX
    wp_register_script('ajax-login-script', get_template_directory_uri() . '/assets/ajax-login-script.js', array('jquery'), null, true); 
    wp_enqueue_script('ajax-login-script');

    // Localiza o script com a URL de AJAX do WordPress e outras variáveis necessárias
    wp_localize_script('ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url('admin-ajax.php'),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Enviando informações, aguarde...')
    ));

    // Permite que usuários sem privilégios executem ajax_login() via AJAX
    add_action('wp_ajax_nopriv_ajaxlogin', 'ajax_login');
}

// Executa a ação apenas se o usuário não estiver logado
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}

function ajax_login() {
    check_ajax_referer('ajax-login-nonce', 'security');

    $info = array();
    $info['user_login'] = sanitize_text_field($_POST['username']);
    $info['user_password'] = sanitize_text_field($_POST['password']);
    $info['remember'] = true;

    $user_signon = wp_signon($info, false);

    if (is_wp_error($user_signon)) {
        echo json_encode(array('loggedin' => false, 'message' => __('Nome de usuário ou senha errados!')));
    } else {
        // Obter o usuário logado
        $user = $user_signon;

        // Definir redirecionamento com base no papel do usuário
        if (in_array('administrator', $user->roles)) {
            $redirect_url = wp_get_referer() ? wp_get_referer() : home_url(); // Recarregar a página atual        
        } elseif (in_array('gerente', $user->roles)) {
            $redirect_url = home_url('/todos-associados/');
        } elseif (in_array('associados', $user->roles)) {
            $redirect_url = home_url('/usuario/'); // Redirecionar associados para /usuario/
        } else {
            $redirect_url = home_url(); // Redirecionar para a página inicial por padrão
        }

        echo json_encode(array('loggedin' => true, 'message' => __('Logando, aguarde...'), 'redirect' => $redirect_url));
    }

    wp_die();
}


// Registro Associados
add_action('wp_head', function() {
    ?>
    <script>
        window.uploadAssociadoNonce = "<?php echo wp_create_nonce('upload_associado_file_nonce'); ?>";
    </script>
    <?php
});
// Função para criar um usuário no WordPress e atualizar campos ACF
function process_file_upload($file, $acf_field_key, $user_id) {
    error_log('Conteúdo do $_POST: ' . print_r($_POST, true));
    if (!function_exists('wp_handle_upload')) {
        error_log('Função wp_handle_upload não está disponível.');
        return;
    }

    // Verificar se o arquivo está presente e não contém erros
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        error_log('Arquivo não encontrado ou erro no upload. Arquivo: ' . print_r($file, true));
        return;
    }

    // Manuseio do upload do arquivo
    $uploaded_file = wp_handle_upload($file, array('test_form' => false));

    if (isset($uploaded_file['error'])) {
        error_log('Erro no upload do arquivo: ' . $uploaded_file['error']);
        return;
    }

    if (isset($uploaded_file['file'])) {
        $attachment = array(
            'guid'           => $uploaded_file['url'],
            'post_mime_type' => $uploaded_file['type'],
            'post_title'     => sanitize_file_name(basename($uploaded_file['file'])),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);

            if ($acf_field_key && $user_id) {
                update_field($acf_field_key, $attachment_id, 'user_' . $user_id);
                error_log("Campo ACF atualizado com sucesso: $acf_field_key para o usuário ID: $user_id com o anexo ID: $attachment_id");
            } else {
                error_log("O campo ACF com a chave $acf_field_key ou o ID do usuário não são válidos.");
            }
        } else {
            error_log('Erro ao inserir o anexo: ' . $attachment_id->get_error_message());
        }
    } else {
        error_log('Erro no upload do arquivo: ' . print_r($uploaded_file, true));
    }
}

function handle_user_registration_or_edit_with_acf() {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Verificar se é uma edição, e pegar o ID do paciente pela URL
    $is_editing = isset($_GET['editar_paciente']) && !empty($_GET['editar_paciente']);
    $user_id = $is_editing ? intval($_GET['editar_paciente']) : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit_associado']) || $is_editing)) {
        $nonce_field = $is_editing ? 'editar_associado_nonce' : 'associado_nonce';
        $nonce_action = $is_editing ? 'editar_associado_action' : 'associado_action';

        if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $nonce_action)) {
            wp_die('<p>Nonce inválido. Tente novamente.</p>');
        }

        // Pegar os dados do formulário
        $name = sanitize_text_field($_POST['acf']['field_666c8794b6c48']);
        $email = sanitize_email($_POST['acf']['field_66b244e3d8b86']);

        // No cadastro, verificar se o email é válido e não existe, na edição isso não é necessário
        if (!$is_editing && (!is_email($email) || email_exists($email))) {
            wp_die('<p>Email inválido ou já existente.</p>');
        }

        if ($is_editing && $user_id) {
            // Atualizar o nome e outros campos do usuário (não o e-mail)
            $userdata = array(
                'ID'         => $user_id,  // Usar o ID correto do paciente
                'first_name' => $name,
                // Adicionar outros campos que precisam ser atualizados
            );
            $updated_user_id = wp_update_user($userdata);

            if (is_wp_error($updated_user_id)) {
                wp_die('<p>Erro ao atualizar o usuário: ' . $updated_user_id->get_error_message() . '</p>');
            }
        } else {
            // Cadastro de novo usuário
            $password = 'senha@amedis'; // Senha fixa
            $username = sanitize_user($name);

            if (username_exists($username)) {
                $username .= '_' . uniqid();
            }

            $userdata = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass'  => $password,
                'first_name' => $name,
                'role'       => 'associados'
            );

            $user_id = wp_insert_user($userdata);

            if (is_wp_error($user_id)) {
                wp_die('<p>Erro ao criar usuário: ' . $user_id->get_error_message() . '</p>');
            }
        }

        // Atualizar campos ACF (exceto campos do tipo arquivo)
        foreach ($_POST['acf'] as $field_key => $value) {
            if (!empty($value) && !preg_match('/^file_/', $field_key)) {
                $sanitized_value = is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
                update_field($field_key, $sanitized_value, 'user_' . $user_id);
            }
        }

        // Processa arquivos via campos hidden (já enviados via AJAX)
        if (isset($_POST['comprova_end_paciente_id']) && !empty($_POST['comprova_end_paciente_id'])) {
            update_field('field_66db4299e85db', intval($_POST['comprova_end_paciente_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['comprova_rg_paciente_id']) && !empty($_POST['comprova_rg_paciente_id'])) {
            update_field('field_66db42d30cfce', intval($_POST['comprova_rg_paciente_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['laudo_paciente_id']) && !empty($_POST['laudo_paciente_id'])) {
            update_field('field_66db434462bc6', intval($_POST['laudo_paciente_id']), 'user_' . $user_id);
        }

        // Processar termo_associativo
        if (isset($_POST['termo_associativo_id']) && !empty($_POST['termo_associativo_id'])) {
            update_field('field_686d6f7d2c1de', intval($_POST['termo_associativo_id']), 'user_' . $user_id);
        }

        // Redirecionar após o sucesso
        $redirect_url = $is_editing ? add_query_arg('user_edited', 'true', home_url('/editar-paciente/?editar_paciente=' . $user_id)) : add_query_arg('user_registered', 'true', home_url($_SERVER['REQUEST_URI']));
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('template_redirect', 'handle_user_registration_or_edit_with_acf');


// config de associados
function save_config_data() {
    if (isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);

        // Verifica se o checkbox de status foi marcado
        if (isset($_POST['acf']['field_66b252b04990d'])) {
            update_field('field_66b252b04990d', '1', 'user_' . $user_id);
        } else {
            update_field('field_66b252b04990d', '0', 'user_' . $user_id);
        }

        // Atualiza o campo de observações com a chave correta
        if (isset($_POST['acf']['field_666c87e6b6c4d'])) {
            update_field('field_666c87e6b6c4d', sanitize_textarea_field($_POST['acf']['field_666c87e6b6c4d']), 'user_' . $user_id);
        }

        // Processa as áreas liberadas usando user_meta nativo
        $areas_disponiveis = ['pacientes', 'receitas', 'prescritor', 'produtos', 'entradas', 'saidas', 'relatorios'];
        
        foreach ($areas_disponiveis as $area) {
            $valor = isset($_POST['areas_liberadas'][$area]) ? '1' : '0';
            update_user_meta($user_id, 'area_liberada_' . $area, $valor);
        }

        wp_send_json_success('Configurações salvas com sucesso!');
    } else {
        wp_send_json_error('ID de usuário não fornecido.');
    }
}

add_action('wp_ajax_save_config', 'save_config_data');
add_action('wp_ajax_nopriv_save_config', 'save_config_data');

// Salvar configurações do prescritor
function save_prescritor_config_data() {
    if (isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $acf_data = isset($_POST['acf']) ? $_POST['acf'] : [];

        // Usando o nome do campo: 'prescritor_amedis_ativo'
        $is_active = isset($acf_data['prescritor_amedis_ativo']) ? 1 : 0;
        update_field('prescritor_amedis_ativo', $is_active, 'user_' . $user_id);

        // Usando o nome do campo: 'prescritor_observacoes'
        if (isset($acf_data['prescritor_observacoes'])) {
            update_field('prescritor_observacoes', sanitize_textarea_field($acf_data['prescritor_observacoes']), 'user_' . $user_id);
        }

        wp_send_json_success('Dados salvos com sucesso!');
    } else {
        wp_send_json_error('ID de usuário não fornecido.');
    }
}
add_action('wp_ajax_save_prescritor_config', 'save_prescritor_config_data');
add_action('wp_ajax_nopriv_save_prescritor_config', 'save_prescritor_config_data');


// cadastro prescritor
function processar_formulario_usuario_prescritor_personalizado() {
    if (isset($_POST['submit_prescritor_form'])) {
        // Verifica o nonce para garantir segurança
        if (!isset($_POST['add_prescritor_nonce']) || !wp_verify_nonce($_POST['add_prescritor_nonce'], 'add_prescritor_action')) {
            wp_die('Ação não permitida. Nonce inválido.');
        }

        // Captura o nome completo do campo ACF
        $nome_completo = isset($_POST['acf']['field_66e5c2cd553d3']) ? sanitize_text_field($_POST['acf']['field_66e5c2cd553d3']) : '';

        // Verifica se o nome completo foi preenchido
        if (empty($nome_completo)) {
            wp_die('O campo Nome Completo está vazio.');
        }

        // Cria o nome de usuário único baseado no primeiro nome
        $primeiro_nome = explode(' ', trim($nome_completo))[0];
        $username = sanitize_user($primeiro_nome);
        $i = 1;
        while (username_exists($username)) {
            $username = $primeiro_nome . $i++;
        }

        // Captura o email do campo ACF e valida
        $email = isset($_POST['acf']['field_66e6494ba7957']) ? sanitize_email($_POST['acf']['field_66e6494ba7957']) : '';
        if (empty($email) || email_exists($email)) {
            wp_die('E-mail inválido ou já cadastrado.');
        }

        // Cria o novo usuário com uma senha padrão
        $password = 'amedis@senha'; 
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
        }

        // Define o primeiro nome do usuário
        wp_update_user(array('ID' => $user_id, 'first_name' => $nome_completo));

        // Adiciona a função de usuário "prescritor"
        $user = new WP_User($user_id);
        $user->set_role('prescritor');

        // Processa campos ACF (exceto arquivos)
        if (!empty($_POST['acf'])) {
            foreach ($_POST['acf'] as $key => $value) {
                update_field($key, $value, 'user_' . $user_id);
            }
        }

        // Processa arquivos via campos hidden (já enviados via AJAX)
        if (isset($_POST['doc_frente_conselho_id']) && !empty($_POST['doc_frente_conselho_id'])) {
            update_field('doc_frente_conselho', intval($_POST['doc_frente_conselho_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['doc_verso_conselho_id']) && !empty($_POST['doc_verso_conselho_id'])) {
            update_field('doc_verso_conselho', intval($_POST['doc_verso_conselho_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['foto_site_id']) && !empty($_POST['foto_site_id'])) {
            update_field('foto_site', intval($_POST['foto_site_id']), 'user_' . $user_id);
        }

        // Redireciona após o processamento
        $current_url = add_query_arg('prescritor_salvo', 'true', home_url($_SERVER['REQUEST_URI']));
        wp_redirect($current_url);
        exit;
    }
}
add_action('init', 'processar_formulario_usuario_prescritor_personalizado');


// Função para editar o formulário de prescritor
function processar_formulario_editar_prescritor() {
    if (isset($_POST['submit_edit_prescritor_form'])) {
        // Verifica o nonce para garantir segurança
        if (!isset($_POST['edit_prescritor_nonce']) || !wp_verify_nonce($_POST['edit_prescritor_nonce'], 'edit_prescritor_action')) {
            wp_die('Ação não permitida. Nonce inválido.');
        }

        // Captura o ID do usuário a ser editado
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        if ($user_id <= 0) {
            wp_die('ID de usuário inválido para edição.');
        }

        // Captura o nome completo do campo ACF
        $nome_completo = isset($_POST['acf']['field_66e5c2cd553d3']) ? sanitize_text_field($_POST['acf']['field_66e5c2cd553d3']) : '';

        // Verifica se o nome completo foi preenchido
        if (empty($nome_completo)) {
            wp_die('O campo Nome Completo está vazio.');
        }

        // Atualiza o primeiro nome do usuário
        wp_update_user(array('ID' => $user_id, 'first_name' => $nome_completo));

        // Captura e atualiza o email
        $email = isset($_POST['acf']['field_66e6494ba7957']) ? sanitize_email($_POST['acf']['field_66e6494ba7957']) : '';
        if (!empty($email) && is_email($email)) {
            wp_update_user(array('ID' => $user_id, 'user_email' => $email));
        } elseif (!empty($email)) {
            wp_die('E-mail inválido.');
        }

        // Processa campos ACF (exceto arquivos)
        if (!empty($_POST['acf'])) {
            foreach ($_POST['acf'] as $key => $value) {
                update_field($key, $value, 'user_' . $user_id);
            }
        }

        // Processa arquivos via campos hidden (já enviados via AJAX)
        if (isset($_POST['doc_frente_conselho_id']) && !empty($_POST['doc_frente_conselho_id'])) {
            update_field('doc_frente_conselho', intval($_POST['doc_frente_conselho_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['doc_verso_conselho_id']) && !empty($_POST['doc_verso_conselho_id'])) {
            update_field('doc_verso_conselho', intval($_POST['doc_verso_conselho_id']), 'user_' . $user_id);
        }
        
        if (isset($_POST['foto_site_id']) && !empty($_POST['foto_site_id'])) {
            update_field('foto_site', intval($_POST['foto_site_id']), 'user_' . $user_id);
        }


        // Redireciona após o processamento
        $current_url = add_query_arg('prescritor_salvo', 'true', home_url($_SERVER['REQUEST_URI']));
        wp_redirect($current_url);
        exit;
    }
}
add_action('init', 'processar_formulario_editar_prescritor');

add_action('wp_ajax_upload_prescritor_file', 'handle_upload_prescritor_file');
add_action('wp_ajax_nopriv_upload_prescritor_file', 'handle_upload_prescritor_file');

function handle_upload_prescritor_file() {
    // Log para debug
    error_log('AJAX Upload iniciado: ' . print_r($_FILES, true));
    
    // Verificar se arquivo foi enviado
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        error_log('Erro: Arquivo não enviado ou com erro');
        wp_send_json_error('Arquivo não enviado ou inválido');
        return;
    }
    
    // Incluir arquivos necessários
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    if (!function_exists('wp_insert_attachment')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
    }
    if (!function_exists('wp_generate_attachment_metadata')) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    
    // Fazer upload
    $uploaded_file = wp_handle_upload($_FILES['file'], array('test_form' => false));
    
    if (isset($uploaded_file['error'])) {
        error_log('Erro no wp_handle_upload: ' . $uploaded_file['error']);
        wp_send_json_error($uploaded_file['error']);
        return;
    }
    
    // Criar attachment
    $attachment = array(
        'guid' => $uploaded_file['url'],
        'post_mime_type' => $uploaded_file['type'],
        'post_title' => sanitize_file_name(basename($uploaded_file['file'])),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
    
    if (is_wp_error($attachment_id)) {
        error_log('Erro no wp_insert_attachment: ' . $attachment_id->get_error_message());
        wp_send_json_error($attachment_id->get_error_message());
        return;
    }
    
    // Gerar metadata
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);
    
    error_log('Upload bem-sucedido. ID: ' . $attachment_id);
    
    wp_send_json_success(array(
        'attachment_id' => $attachment_id,
        'attachment_url' => $uploaded_file['url']
    ));
}

add_action('wp_ajax_upload_associado_file', 'upload_associado_file_callback');
add_action('wp_ajax_nopriv_upload_associado_file', 'upload_associado_file_callback');

function upload_associado_file_callback() {
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'upload_associado_file_nonce')) {
        wp_send_json_error('Falha de segurança no upload.');
        return;
    }    
    if (!isset($_FILES['file'])) {
        wp_send_json_error('Nenhum arquivo enviado.');
        return;
    }

    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('Erro no upload do arquivo.');
        return;
    }

    $uploaded_file = wp_handle_upload($file, array('test_form' => false));

    if (isset($uploaded_file['error'])) {
        wp_send_json_error($uploaded_file['error']);
        return;
    }

    $attachment = array(
        'guid'           => $uploaded_file['url'],
        'post_mime_type' => $uploaded_file['type'],
        'post_title'     => sanitize_file_name(basename($uploaded_file['file'])),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

    if (!is_wp_error($attachment_id)) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        wp_send_json_success(array(
            'attachment_id' => $attachment_id,
            'attachment_url' => wp_get_attachment_url($attachment_id)
        ));
    } else {
        wp_send_json_error('Erro ao criar anexo.');
    }
}



// Função auxiliar para processar o upload de arquivos (você já deve ter isso)
if (!function_exists('process_file_upload')) {
    function process_file_upload($file, $field_key, $user_id) {
        $uploaded_file = wp_handle_upload($file, array('test_form' => false));

        if ($uploaded_file && !isset($uploaded_file['error'])) {
            $attachment_id = wp_insert_attachment(array(
                'guid'           => $uploaded_file['url'],
                'post_mime_type' => $uploaded_file['type'],
                'post_title'     => sanitize_file_name(pathinfo($uploaded_file['file'], PATHINFO_FILENAME)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ), $uploaded_file['file']);

            if ($attachment_id) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Atualiza o campo ACF do usuário com o ID do anexo
                update_field($field_key, $attachment_id, 'user_' . $user_id);
            }
        } else {
            // Log do erro no upload (para depuração)
            error_log('Erro no upload do arquivo para o campo ' . $field_key . ': ' . $uploaded_file['error']);
            // Você pode adicionar uma mensagem de erro para o usuário aqui
        }
    }
}



// chamada select2

function enqueue_select2_assets() {
    // Adiciona o CSS do select2
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    
    // Adiciona o JS do select2
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);
    
    // Adiciona seu script personalizado
    //wp_enqueue_script('custom-select2', get_template_directory_uri() . '/js/custom-select2.js', array('jquery', 'select2-js'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_select2_assets');

// define campo acf associado_ativo no front

function ajax_update_associado_ativado() {
    if (!current_user_can('edit_user', $_POST['user_id'])) {
        error_log('Permissão negada para o usuário ID: ' . $_POST['user_id']);
        wp_send_json_error(['message' => 'Permissão negada.']);
        return;
    }

    $user_id = intval($_POST['user_id']);
    $associado_ativado = $_POST['associado_ativado'] ? 1 : 0;

    // Log para verificar os dados recebidos
    error_log('Atualizando associado ID: ' . $user_id . ' para o valor: ' . $associado_ativado);

    if (update_field('field_66b252b04990d', $associado_ativado, 'user_' . $user_id)) {
        error_log('Campo ACF atualizado com sucesso para o usuário ID: ' . $user_id);
        wp_send_json_success(['message' => 'Atualização bem-sucedida']);
    } else {
        error_log('Erro ao salvar o campo ACF para o usuário ID: ' . $user_id);
        wp_send_json_error(['message' => 'Erro ao salvar no banco de dados.']);
    }
}
// Hook da ação AJAX (usuários logados)
add_action('wp_ajax_update_associado_ativado', 'ajax_update_associado_ativado');




// mensagens jquery ajax
function salvar_mensagem_via_ajax() {
    check_ajax_referer('salvar_mensagem_nonce', 'security');

    if (isset($_POST['mensagem']) && isset($_POST['categoria'])) {
        $mensagem = sanitize_text_field($_POST['mensagem']);
        $categoria = sanitize_text_field($_POST['categoria']);
        $id_mensagem = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : uniqid(); // Gera um ID único se não houver ID

        $arquivo = get_template_directory() . '/mensagens.json';

        if (!file_exists($arquivo)) {
            file_put_contents($arquivo, json_encode([]));
        }

        $mensagens = json_decode(file_get_contents($arquivo), true);
        if ($mensagens === null) {
            $mensagens = [];
        }

        // Verifica se é uma edição
        $editado = false;
        foreach ($mensagens as &$msg) {
            if ($msg['id'] == $id_mensagem) {
                $msg['texto'] = $mensagem;
                $msg['categoria'] = $categoria;
                $editado = true;
                break;
            }
        }

        // Se não for edição, adiciona uma nova mensagem
        if (!$editado) {
            $mensagens[] = ['id' => $id_mensagem, 'texto' => $mensagem, 'categoria' => $categoria];
        }

        if (file_put_contents($arquivo, json_encode($mensagens, JSON_PRETTY_PRINT))) {
            echo json_encode(['status' => 'success', 'message' => 'Mensagem salva com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a mensagem!']);
        }
    }
    wp_die();
}

add_action('wp_ajax_salvar_mensagem', 'salvar_mensagem_via_ajax');
add_action('wp_ajax_nopriv_salvar_mensagem', 'salvar_mensagem_via_ajax');



// Função para excluir mensagem ajax
function excluir_mensagem_via_ajax() {
    check_ajax_referer('salvar_mensagem_nonce', 'security');

    if (isset($_POST['id'])) {
        $id_mensagem = sanitize_text_field($_POST['id']);
        $arquivo = get_template_directory() . '/mensagens.json';

        $mensagens = json_decode(file_get_contents($arquivo), true);
        if ($mensagens === null) {
            $mensagens = [];
        }

        $mensagens = array_filter($mensagens, function($msg) use ($id_mensagem) {
            return $msg['id'] !== $id_mensagem;
        });

        if (file_put_contents($arquivo, json_encode($mensagens, JSON_PRETTY_PRINT))) {
            echo json_encode(['status' => 'success', 'message' => 'Mensagem excluída com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir a mensagem!']);
        }
    }
    wp_die();
}

add_action('wp_ajax_excluir_mensagem', 'excluir_mensagem_via_ajax');
add_action('wp_ajax_nopriv_excluir_mensagem', 'excluir_mensagem_via_ajax');



// Usuarios documentos pendentes

add_action('wp_ajax_criar_atendimento', 'criar_atendimento_callback');

function criar_atendimento_callback() {
    // Verificar se o usuário está logado ou tem permissão
    if (!is_user_logged_in()) {
        wp_send_json_error('Usuário não logado.');
        return;
    }

    $user_id = intval($_POST['user_id']);
    $post_title = sanitize_text_field($_POST['post_title']);

    // Criar o post no tipo 'atendimento'
    $new_post = array(
        'post_title'   => $post_title,
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'atendimento',
    );

    // Inserir o post no banco de dados
    $post_id = wp_insert_post($new_post);

    if ($post_id) {
        // Atualizar o campo ACF 'id_associado'
        update_field('id_associado', $user_id, $post_id);
        update_post_meta($post_id, 'atendimento_criado', true);
        wp_send_json_success('Atendimento criado com sucesso.');
    } else {
        wp_send_json_error('Erro ao criar o atendimento.');
    }

    wp_die(); // Sempre chamar wp_die() ao final de uma chamada AJAX
}






// Produtos

function salvar_produto() {
    // Verifica o nonce para criação ou edição
    if (isset($_POST['novo_produto_nonce']) && !wp_verify_nonce($_POST['novo_produto_nonce'], 'novo_produto_action')) {
        wp_send_json_error('Erro de segurança: nonce inválido para criação.');
        return;
    }

    if (isset($_POST['editar_produto_nonce']) && !wp_verify_nonce($_POST['editar_produto_nonce'], 'editar_produto_action')) {
        wp_send_json_error('Erro de segurança: nonce inválido para edição.');
        return;
    }

    // Captura o título do post diretamente
    $post_title = isset($_POST['post_title']) ? sanitize_text_field($_POST['post_title']) : '';

    // Verifica se o ID do post foi enviado, caso contrário, cria um novo post
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    // Se $post_id for 0, cria um novo post, senão, atualiza o existente
    if ($post_id > 0) {
        // Atualiza o post existente
        $post_data = array(
            'ID'         => $post_id,
            'post_title' => $post_title,
            'post_status' => 'publish',
        );
        $post_id = wp_update_post($post_data);
    } else {
        // Cria um novo post
        $post_data = array(
            'post_title'  => $post_title,
            'post_type'   => 'produtos',
            'post_status' => 'publish',
        );
        $post_id = wp_insert_post($post_data);
    }

    if (is_wp_error($post_id)) {
        wp_send_json_error('Erro ao salvar a entrada no CPT: ' . $post_id->get_error_message());
        return;
    }

    // Verifica se uma imagem foi enviada
    if (!empty($_FILES['imagem_destacada']['name'])) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $uploaded_file = $_FILES['imagem_destacada'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            $attachment_id = wp_insert_attachment(array(
                'guid' => $movefile['url'], 
                'post_mime_type' => $movefile['type'],
                'post_title' => sanitize_file_name($uploaded_file['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            ), $movefile['file'], $post_id);

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attachment_id, $movefile['file']);
            wp_update_attachment_metadata($attachment_id, $attach_data);

            set_post_thumbnail($post_id, $attachment_id);
        } else {
            wp_send_json_error('Erro ao fazer o upload da imagem: ' . $movefile['error']);
            return;
        }
    }

    // **Salvar campos ACF**
    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $key => $value) {
            // Garantir que o valor não é vazio e existe no banco de dados
            $existing_value = get_field($key, $post_id);
            if ($value !== $existing_value) {
                // Atualiza o campo ACF apenas se houver alteração
                $update_result = update_field($key, $value, $post_id);
                if (!$update_result) {
                    wp_send_json_error('Erro ao salvar o campo ACF: ' . $key);
                    return;
                }
            }
        }
    }

    // Envio bem-sucedido com o permalink do post
    $permalink = get_permalink($post_id); // Recupera o permalink do post criado ou atualizado
    wp_send_json_success(array('message' => 'Produto salva com sucesso.', 'permalink' => $permalink));
}

add_action('wp_ajax_salvar_produto', 'salvar_produto');
add_action('wp_ajax_nopriv_salvar_produto', 'salvar_produto');

// ENTRADAS OLD

function salvar_nova_entrada() {
    // Verifica se o nonce é válido
    if ( ! isset($_POST['nova_entrada_nonce']) || ! wp_verify_nonce($_POST['nova_entrada_nonce'], 'nova_entrada_action') ) {
        wp_send_json_error('Erro de segurança: nonce inválido.');
        return;
    }

    // Verifica se o campo 'tipo_entrada_taxonomia' foi passado
    if ( empty($_POST['tipo_entrada_taxonomia']) ) {
        wp_send_json_error('Erro: Seleção de tipo de entrada está vazia.');
        return;
    }

    // Tenta criar o post no CPT 'entradas'
    $post_data = array(
        'post_title'  => 'Entrada - ' . current_time('Y-m-d H:i:s'),
        'post_type'   => 'entradas',
        'post_status' => 'publish',
    );
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error('Erro ao criar a entrada no CPT: ' . $post_id->get_error_message());
        return;
    }

    // Tenta associar o termo da taxonomia 'tipo-entrada'
    $termo_associado = wp_set_object_terms($post_id, sanitize_text_field($_POST['tipo_entrada_taxonomia']), 'tipo-entrada');
    if (is_wp_error($termo_associado)) {
        wp_send_json_error('Erro ao associar a taxonomia: ' . $termo_associado->get_error_message());
        return;
    }

    // Loop para salvar os campos ACF dinamicamente
    foreach ($_POST['acf'] as $key => $value) {
        if (!empty($value)) {
            $update_result = update_field($key, sanitize_text_field($value), $post_id);
            if (!$update_result) {
                wp_send_json_error('Erro ao salvar o campo ACF: ' . $key);
                return;
            }
        }
    }

    // Envio bem-sucedido com o permalink do post
    $permalink = get_permalink($post_id); // Recupera o permalink do post criado
    wp_send_json_success(array('message' => 'Entrada salva com sucesso.', 'permalink' => $permalink));
}

add_action('wp_ajax_salvar_nova_entrada', 'salvar_nova_entrada');
add_action('wp_ajax_nopriv_salvar_nova_entrada', 'salvar_nova_entrada');


// add produtos entradas
add_action('wp_ajax_create_item_entrada', 'create_item_entrada');
add_action('wp_ajax_nopriv_create_item_entrada', 'create_item_entrada');

function create_item_entrada() {
    $id_produto = intval($_POST['id_produto']);
    $id_entrada = intval($_POST['id_entrada']);
    $qtd_produto = sanitize_text_field($_POST['qtd_produto']);
    $vlr_uni_produto = sanitize_text_field($_POST['vlr_uni_produto']);
    $vlr_total_produto = sanitize_text_field($_POST['vlr_total_produto']);

    if (!$id_produto) {
        echo json_encode(array('success' => false, 'message' => 'ID do produto não recebido.'));
        wp_die();
    }

    $post_data = array(
        'post_title'  => 'Item de entrada - ' . $id_entrada . ' - ' . $id_produto,
        'post_type'   => 'itens',
        'post_status' => 'publish',
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id) || !$post_id) {
        echo json_encode(array('success' => false, 'message' => 'Falha ao criar o item.'));
        wp_die();
    }

    wp_set_object_terms($post_id, 'entrada', 'tipo-itens');

    update_field('id_produto', $id_produto, $post_id);
    update_field('id_entrada', $id_entrada, $post_id);
    update_field('qtd_produto', $qtd_produto, $post_id);
    update_field('vlr_uni_produto', $vlr_uni_produto, $post_id);
    update_field('vlr_total_produto', $vlr_total_produto, $post_id);

    echo json_encode(array('success' => true, 'post_id' => $post_id));
    wp_die();
}

// Função para deletar item
add_action('wp_ajax_delete_item', 'delete_item');

function delete_item() {
    // Verifica se a chave "post_id" está definida
    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        if (wp_delete_post($post_id)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'ID do post não recebido.'));
    }
    wp_die();
}


// editar a entrada
add_action('wp_ajax_editar_entrada', 'editar_entrada_callback');
add_action('wp_ajax_nopriv_editar_entrada', 'editar_entrada_callback');
function editar_entrada_callback() {
    if (!check_ajax_referer('editar_entrada_action', 'editar_entrada_nonce', false)) {
        wp_send_json_error('Nonce inválido.');
        return;
    }

    if (empty($_POST['id_post_entrada'])) {
        wp_send_json_error('ID do post não encontrado.');
        return;
    }

    $post_id = intval($_POST['id_post_entrada']);

    // Log do que foi recebido (para debug)
    error_log(print_r($_POST, true));

    // Atualiza os campos ACF
    if (isset($_POST['acf'])) {
        foreach ($_POST['acf'] as $key => $value) {
            update_field($key, sanitize_text_field($value), $post_id);
        }
    }

    // Função auxiliar para verificar e associar termos às taxonomias
    function associar_termo_taxonomia($post_id, $valor, $taxonomia) {
        if (!empty($valor)) {
            $termo = sanitize_text_field($valor);
            // Verifica se o termo existe na taxonomia
            $termo_obj = term_exists($termo, $taxonomia);
            if ($termo_obj !== 0 && $termo_obj !== null) {
                wp_set_object_terms($post_id, intval($termo_obj['term_id']), $taxonomia);
            } else {
                wp_send_json_error("O termo '{$termo}' não existe na taxonomia '{$taxonomia}'.");
            }
        }
    }

    // Atualiza as taxonomias com os valores corretos
    // Certifique-se de que cada termo seja associado à sua taxonomia correspondente
    if (!empty($_POST['tipo-entrada'])) {
        associar_termo_taxonomia($post_id, $_POST['tipo-entrada'], 'tipo-entrada');
    }

    if (!empty($_POST['status-pagamento'])) {
        associar_termo_taxonomia($post_id, $_POST['status-pagamento'], 'status-pagamento');
    }

    if (!empty($_POST['status-entrega'])) {
        associar_termo_taxonomia($post_id, $_POST['status-entrega'], 'status-entrega');
    }

    error_log(print_r($_POST['acf'], true));

    wp_send_json_success('Dados atualizados com sucesso.');
}


// configs entradas
add_action('wp_ajax_save_config_entrada', 'save_config_entrada');
add_action('wp_ajax_nopriv_save_config_entrada', 'save_config_entrada');

function save_config_entrada() {
    // Verifica se os dados necessários foram recebidos
    if (isset($_POST['status_pagamento'], $_POST['status_entrega'], $_POST['obs_entrada'], $_POST['forma_pagamento'], $_POST['forma_entrega_woo'], $_POST['post_id'])) {
        // Sanitização dos dados recebidos
        $status_pagamento = sanitize_text_field($_POST['status_pagamento']);
        $status_entrega = sanitize_text_field($_POST['status_entrega']);
        $forma_pagamento = sanitize_text_field($_POST['forma_pagamento']);
        $forma_entrega_woo = sanitize_text_field($_POST['forma_entrega_woo']);
        $valor_entrega_final = sanitize_text_field($_POST['valor_entrega_final']);
        $obs_entrada = sanitize_textarea_field($_POST['obs_entrada']);
        $post_id = intval($_POST['post_id']);

        // Log para depuração
        error_log("Post ID: " . $post_id);
        error_log("Status Pagamento: " . $status_pagamento);
        error_log("Status Entrega: " . $status_entrega);
        error_log("Forma Pagamento: " . $forma_pagamento);
        error_log("Forma Entrega: " . $forma_entrega_woo);
        error_log("Valor Entrega Final: " . $valor_entrega_final);
        error_log("Observação: " . $obs_entrada);

        // Atualiza o campo de observações
        if (!empty($obs_entrada)) {
            update_field('field_66eeccb4b9868', $obs_entrada, $post_id);
            error_log("Observação atualizada com sucesso.");
        }

        // Atualiza o campo ACF de forma de pagamento
        if (!empty($forma_pagamento)) {
            update_field('field_66eeaa1a406f3', $forma_pagamento, $post_id);
            error_log("Forma pagamento atualizada com sucesso.");
        }

        // Atualiza o campo ACF de forma de entrega
        if (!empty($forma_entrega_woo)) {
            update_field('field_66f6f364bf16d', $forma_entrega_woo, $post_id);
            error_log("Forma entrega atualizada com sucesso.");
        }


        if (!empty($valor_entrega_final)) {
            update_field('field_670e66554c3a3', $valor_entrega_final, $post_id);
            error_log("Valor entrega atualizada com sucesso.");
        }

        // Função para atualizar a taxonomia
        function update_taxonomy($post_id, $taxonomy, $term) {
            // Verifica se o termo já existe
            $term_exists = term_exists($term, $taxonomy);
            if (!$term_exists) {
                // Se o termo não existe, tenta criar
                $new_term = wp_insert_term($term, $taxonomy);
                if (is_wp_error($new_term)) {
                    error_log("Erro ao criar termo '{$taxonomy}': " . $new_term->get_error_message());
                    return false; // Retorna falso se houve erro
                } else {
                    error_log("Termo '{$taxonomy}' criado: " . $term . ", Term ID: " . $new_term['term_id']);
                }
                $term_id = $new_term['term_id']; // Captura o novo ID do termo
            } else {
                $term_id = $term_exists['term_id']; // Captura o ID do termo existente
                error_log("Termo '{$taxonomy}' já existe: " . print_r($term_exists, true));
            }

            // Tenta atualizar os termos no post
            $result = wp_set_post_terms($post_id, array($term_id), $taxonomy, false);
            if (is_wp_error($result)) {
                error_log("Erro ao atualizar '{$taxonomy}': " . $result->get_error_message());
                return false; // Retorna falso se houve erro
            } else {
                error_log("Resultado ao atualizar '{$taxonomy}': " . print_r($result, true));
            }
            return true; // Retorna verdadeiro se tudo funcionou
        }

        // Atualiza os status de pagamento e entrega
        $pagamento_result = update_taxonomy($post_id, 'status-pagamento', $status_pagamento);
        $entrega_result = update_taxonomy($post_id, 'status-entrega', $status_entrega);
        

        // Verifica se houve sucesso nas atualizações
        if ($pagamento_result && $entrega_result) {
            wp_send_json_success('Dados salvos com sucesso!');
        } else {
            wp_send_json_error('Falha ao atualizar um ou mais status!');
        }
    } else {
        wp_send_json_error('Alguns campos estão faltando ou estão inválidos!');
    }

    wp_die(); // Finaliza a execução da requisição AJAX
}



// Criar Receitas 

add_action('wp_ajax_salvar_receita_ajax', 'handle_salvar_receita_ajax');
add_action('wp_ajax_nopriv_salvar_receita_ajax', 'handle_salvar_receita_ajax');

function handle_salvar_receita_ajax() {
    // Verificar nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'nova_receita_action')) {
        wp_send_json_error(array('message' => 'Erro de segurança.'));
    }

    // Processar upload de arquivos
    $arquivo_receita_id = '';
    $arquivo_laudo_id = '';

    if (isset($_FILES['arquivo_receita_file']) && $_FILES['arquivo_receita_file']['error'] === UPLOAD_ERR_OK) {
        $arquivo_receita_id = upload_file_to_wordpress($_FILES['arquivo_receita_file']);
        if (!$arquivo_receita_id) {
            wp_send_json_error(array('message' => 'Erro ao fazer upload da receita.'));
        }
    }

    if (isset($_FILES['arquivo_laudo_file']) && $_FILES['arquivo_laudo_file']['error'] === UPLOAD_ERR_OK) {
        $arquivo_laudo_id = upload_file_to_wordpress($_FILES['arquivo_laudo_file']);
        if (!$arquivo_laudo_id) {
            wp_send_json_error(array('message' => 'Erro ao fazer upload do laudo.'));
        }
    }

    // Criar post da receita
    $post_data = array(
        'post_title' => sanitize_text_field($_POST['titulo_receita']),
        'post_type' => 'receitas',
        'post_status' => 'publish'
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Erro ao criar receita.'));
    }

    // Salvar campos ACF
    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $field_value) {
            update_field($field_key, sanitize_text_field($field_value), $post_id);
        }
    }

    // Salvar IDs dos arquivos nos campos ACF
    if ($arquivo_receita_id) {
        update_field('field_67e2dc89349de', $arquivo_receita_id, $post_id);
    }

    if ($arquivo_laudo_id) {
        update_field('field_680960af4d5de', $arquivo_laudo_id, $post_id);
    }

    wp_send_json_success(array('message' => 'Receita salva com sucesso!'));
}

function upload_file_to_wordpress($file) {
    // Validações
    $allowed_types = array('image/jpeg', 'image/png', 'image/jpg', 'application/pdf');
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB
        return false;
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $uploaded_file = wp_handle_upload($file, array('test_form' => false));

    if (isset($uploaded_file['error'])) {
        return false;
    }

    // Criar attachment
    $attachment = array(
        'guid' => $uploaded_file['url'],
        'post_mime_type' => $uploaded_file['type'],
        'post_title' => sanitize_file_name(basename($uploaded_file['file'])),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

    if (is_wp_error($attachment_id)) {
        return false;
    }

    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    return $attachment_id;
}


// EDITAR RECEITAS
// Handler AJAX para editar receita
add_action('wp_ajax_editar_receita_ajax', 'handle_editar_receita_ajax');
add_action('wp_ajax_nopriv_editar_receita_ajax', 'handle_editar_receita_ajax');

function handle_editar_receita_ajax() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['security'], 'editar_receita_action')) {
        wp_send_json_error(array('message' => 'Erro de segurança.'));
    }

    $post_id = intval($_POST['post_id']);
    
    // Processar upload de receita
    if (isset($_FILES['receita_file'])) {
        $receita_id = upload_file_to_wordpress($_FILES['receita_file']);
        if ($receita_id) {
            update_field('field_67e2dc89349de', $receita_id, $post_id);
        }
    }

    // Processar upload de laudo
    if (isset($_FILES['laudo_file'])) {
        $laudo_id = upload_file_to_wordpress($_FILES['laudo_file']);
        if ($laudo_id) {
            update_field('field_680960af4d5de', $laudo_id, $post_id);
        }
    }

    // Atualizar outros campos ACF
    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $field_value) {
            update_field($field_key, $field_value, $post_id);
        }
    }

    // Atualizar título se fornecido
    if (isset($_POST['titulo_receita'])) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => sanitize_text_field($_POST['titulo_receita'])
        ));
    }

    wp_send_json_success(array('message' => 'Receita atualizada com sucesso!'));
}


// funções auxiluiares receitas

// Função para contar receitas de um usuário
function count_user_receitas($user_id) {
    $args_receitas = array(
        'post_type' => 'receitas',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'id_paciente_receita',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
        'fields' => 'ids', // Só precisamos dos IDs para contar
    );

    $receitas_query = new WP_Query($args_receitas);
    $count = $receitas_query->found_posts;
    wp_reset_postdata();
    
    return $count;
}

// Função para gerar HTML das receitas diretamente
function get_receitas_html_direct($user_id) {
    $args_receitas = array(
        'post_type' => 'receitas',
        'posts_per_page' => 4, // Limita a 4 receitas
        'orderby' => 'date',
        'order' => 'DESC', // Ordena pelas mais recentes primeiro
        'meta_query' => array(
            array(
                'key' => 'id_paciente_receita',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );

    $receitas_query = new WP_Query($args_receitas);
    
    if (!$receitas_query->have_posts()) {
        return '<p class="text-sm text-gray-500">Nenhuma receita encontrada.</p>';
    }

    $html = '';
    while ($receitas_query->have_posts()) {
        $receitas_query->the_post();
        $receitaID = get_the_ID();
        $data_emissao = get_field('data_emissao');
        $data_vencimento = get_field('data_vencimento');
        $arquivo_receita = get_field('arquivo_receita');
        $arquivo_laudo = get_field('arquivo_laudo');

        $receita_cor = empty($arquivo_receita) ? 'text-gray-500' : 'text-green-500';
        $receita_href = empty($arquivo_receita) ? '#!' : esc_url($arquivo_receita);
        $laudo_cor = empty($arquivo_laudo) ? 'text-gray-500' : 'text-green-500';
        $laudo_href = empty($arquivo_laudo) ? '#!' : esc_url($arquivo_laudo);

        $html .= '<div class="receita-item bg-white border border-gray-200 rounded-lg p-3 mb-2">';
        $html .= '<div class="flex justify-between items-start mb-2">';
        $html .= '<h5 class="font-medium text-gray-900">' . get_the_title() . '</h5>';
        $html .= '<span class="text-xs text-gray-500">#' . $receitaID . '</span>';
        $html .= '</div>';
        $html .= '<div class="grid grid-cols-2 gap-3 text-sm mb-3">';
        $html .= '<div><span class="font-medium">Emissão:</span> ' . esc_html($data_emissao) . '</div>';
        $html .= '<div><span class="font-medium">Vencimento:</span> ' . esc_html($data_vencimento) . '</div>';
        $html .= '</div>';
        $html .= '<div class="flex gap-3">';
        $html .= '<a href="' . $receita_href . '" target="_blank" class="inline-flex items-center gap-1 ' . $receita_cor . ' text-sm hover:underline">📄 Receita</a>';
        $html .= '<a href="' . $laudo_href . '" target="_blank" class="inline-flex items-center gap-1 ' . $laudo_cor . ' text-sm hover:underline">📋 Laudo</a>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    wp_reset_postdata();
    return $html;
}





/**
 * Funções para o novo dashboard de associados.
 */

// Função para obter um ícone SVG do diretório de assets.
function amedis_get_icon_svg($icon_name, $classes = 'w-5 h-5') {
    $icon_path = get_template_directory() . "/assets/images/icons/{$icon_name}.svg";
    if (file_exists($icon_path)) {
        $svg_content = file_get_contents($icon_path);
        // Adiciona classes ao SVG.
        return str_replace('<svg', '<svg class="' . esc_attr($classes) . '"', $svg_content);
    }
    return ''; // Retorna string vazia se o ícone não for encontrado.
}

// Função principal para coletar e formatar dados de um associado para exibição.
function get_associado_display_data($user_id) {
    $user_data = get_userdata($user_id);
    if (!$user_data) {
        return [];
    }

    $data = [
        'user_id' => $user_id,
        'nome_completo' => get_user_meta($user_id, 'nome_completo', true) ?: $user_data->display_name,
        'email' => get_user_meta($user_id, 'email', true) ?: $user_data->user_email,
        'telefone' => get_user_meta($user_id, 'telefone', true),
        'data_criacao' => date_i18n('d/m/Y', strtotime($user_data->user_registered)),
        'tipo_associacao' => get_user_meta($user_id, 'tipo_associacao', true),
        'nome_completo_respon' => get_user_meta($user_id, 'nome_completo_respon', true),
        'associado_ativo' => get_user_meta($user_id, 'associado_ativado', true),
        'comprova_end_paciente' => get_user_meta($user_id, 'comprova_end_paciente', true),
        'comprova_rg_paciente' => get_user_meta($user_id, 'comprova_rg_paciente', true),
        'laudo_paciente' => get_user_meta($user_id, 'laudo_paciente', true),
        'termo_associativo' => get_user_meta($user_id, 'termo_associativo', true),
        'diagnostico' => get_user_meta($user_id, 'diagnostico', true),
        'usa_medicacao' => get_user_meta($user_id, 'usa_medicacao', true),
        'qual_medicacao' => get_user_meta($user_id, 'qual_medicacao', true),
        'fez_uso_canabis_escolha' => get_user_meta($user_id, 'fez_uso_canabis_escolha', true),
        'medico_canabis_escolhas' => get_user_meta($user_id, 'medico_canabis_escolhas', true),
        'nome_profissional' => get_user_meta($user_id, 'nome_profissional', true),
        'crm_profi' => get_user_meta($user_id, 'crm_profi', true),
        //'observacoes' => get_user_meta($user_id, 'observacoes', true),
        'observacoes' => get_field('observacoes', 'user_' . $user_id) ?: get_user_meta($user_id, 'observacoes', true),
        'data_nascimento' => get_user_meta($user_id, 'data_nascimento', true),
        'cpf' => get_user_meta($user_id, 'cpf', true),
        'rg' => get_user_meta($user_id, 'rg', true),
        'cidade' => get_user_meta($user_id, 'cidade', true),
        'estado' => get_user_meta($user_id, 'estado', true),
        
// Áreas liberadas
'areas_liberadas' => [
    'pacientes' => get_user_meta($user_id, 'area_liberada_pacientes', true),
    'receitas' => get_user_meta($user_id, 'area_liberada_receitas', true),
    'prescritor' => get_user_meta($user_id, 'area_liberada_prescritor', true),
    'produtos' => get_user_meta($user_id, 'area_liberada_produtos', true),
    'entradas' => get_user_meta($user_id, 'area_liberada_entradas', true),
    'saidas' => get_user_meta($user_id, 'area_liberada_saidas', true),
    'relatorios' => get_user_meta($user_id, 'area_liberada_relatorios', true),
],        
    ];

    // Lógica de apresentação (cores, textos, ícones).
    $type_map = [
        'assoc_paciente' => ['text' => 'Paciente', 'bg' => 'bg-sky-50', 'text_color' => 'text-sky-600', 'icon' => amedis_get_icon_svg('user')],
        'assoc_respon' => ['text' => 'Responsável', 'bg' => 'bg-purple-50', 'text_color' => 'text-purple-600', 'icon' => amedis_get_icon_svg('users')],
        'assoc_tutor' => ['text' => 'Tutor de Animal', 'bg' => 'bg-pink-50', 'text_color' => 'text-pink-600', 'icon' => amedis_get_icon_svg('heart')],
        'assoc_colab' => ['text' => 'Colaborador', 'bg' => 'bg-gray-50', 'text_color' => 'text-gray-600', 'icon' => amedis_get_icon_svg('briefcase')],
    ];
    $type_info = $type_map[$data['tipo_associacao']] ?? ['text' => 'N/D', 'bg' => 'bg-gray-100', 'text_color' => 'text-gray-500', 'icon' => amedis_get_icon_svg('user')];

    $data['text_tipo_assoc'] = $type_info['text'];
    $data['bg_badge'] = $type_info['bg'];
    $data['txt_color'] = $type_info['text_color'];
    $data['tipo_associacao_icon'] = $type_info['icon'];

    $data['associado_ativo_texto'] = $data['associado_ativo'] ? 'Ativo' : 'Inativo';
    $data['associado_ativo_cor'] = $data['associado_ativo'] ? 'text-green-700' : 'text-red-700';
    $data['associado_ativo_dot'] = $data['associado_ativo'] ? 'bg-green-500' : 'bg-red-500';

    // Ícones de status de documento.
    $data['doc_end_icon'] = $data['comprova_end_paciente'] ? amedis_get_icon_svg('check_circle_solid', 'w-4 h-4') : amedis_get_icon_svg('x_circle_solid', 'w-4 h-4');
    $data['doc_end_icon_class'] = $data['comprova_end_paciente'] ? 'text-green-500' : 'text-gray-500';
    $data['doc_rg_icon'] = $data['comprova_rg_paciente'] ? amedis_get_icon_svg('check_circle_solid', 'w-4 h-4') : amedis_get_icon_svg('x_circle_solid', 'w-4 h-4');
    $data['doc_rg_icon_class'] = $data['comprova_rg_paciente'] ? 'text-green-500' : 'text-gray-500';
    $data['doc_laudo_icon'] = $data['laudo_paciente'] ? amedis_get_icon_svg('check_circle_solid', 'w-4 h-4') : amedis_get_icon_svg('x_circle_solid', 'w-4 h-4');
    $data['doc_laudo_icon_class'] = $data['laudo_paciente'] ? 'text-green-500' : 'text-gray-500';
    $data['doc_termo_icon'] = $data['termo_associativo'] ? amedis_get_icon_svg('check_circle_solid', 'w-4 h-4') : amedis_get_icon_svg('x_circle_solid', 'w-4 h-4');
    $data['doc_termo_icon_class'] = $data['termo_associativo'] ? 'text-green-500' : 'text-gray-500';

    // URLs de documentos.
    $data['comprova_end_paciente_url'] = $data['comprova_end_paciente'] ? wp_get_attachment_url($data['comprova_end_paciente']) : '';
    $data['comprova_rg_paciente_url'] = $data['comprova_rg_paciente'] ? wp_get_attachment_url($data['comprova_rg_paciente']) : '';
    $data['laudo_paciente_url'] = $data['laudo_paciente'] ? wp_get_attachment_url($data['laudo_paciente']) : '';
    $data['termo_associativo_url'] = $data['termo_associativo'] ? wp_get_attachment_url($data['termo_associativo']) : '';

    // Endereço completo.
    $endereco_parts = [
        get_user_meta($user_id, 'endereco', true),
        get_user_meta($user_id, 'numero', true),
        get_user_meta($user_id, 'complemento', true),
        get_user_meta($user_id, 'bairro', true),
        get_user_meta($user_id, 'cidade', true),
        get_user_meta($user_id, 'estado', true),
        get_user_meta($user_id, 'cep', true),
    ];
    $data['full_address'] = implode(', ', array_filter($endereco_parts));

// Receitas (HTML e contagem).
$data['receitas_html'] = get_receitas_html_direct($user_id);
$data['receitas_count'] = count_user_receitas($user_id);

    // Adiciona ícones SVG para uso no JavaScript.
    $data['icon_svg_x_circle'] = amedis_get_icon_svg('x_circle', 'w-4 h-4');
    $data['icon_svg_check_circle'] = amedis_get_icon_svg('check_circle', 'w-4 h-4');

    return $data;
}

// Callback AJAX para salvar o status do associado.
add_action('wp_ajax_save_associado_status', 'save_associado_status_callback');
function save_associado_status_callback() {
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'], 'save_associado_status_nonce')) {
        wp_send_json_error(['message' => 'Erro de segurança.']);
        return;
    }

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    if (!$user_id || !current_user_can('edit_user', $user_id)) {
        wp_send_json_error(['message' => 'Permissão negada.']);
        return;
    }

    $associado_ativo = isset($_POST['associado_ativo']) && $_POST['associado_ativo'] === 'on' ? '1' : '0';
    update_user_meta($user_id, 'associado_ativado', $associado_ativo);

    // Retorna os dados atualizados para a UI.
    $updated_data = get_associado_display_data($user_id);

    wp_send_json_success([
        'message' => 'Status do associado atualizado com sucesso!',
        'updated_data' => $updated_data
    ]);
}



/**
 * Handle AJAX request to create a new 'saida' (output) and its 'itens'.
 */
function criar_saida_frontend_callback() {
    // Verify nonce for security
    if ( ! isset( $_POST['criar_saida_nonce_field'] ) || ! wp_verify_nonce( $_POST['criar_saida_nonce_field'], 'criar_saida_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Erro de segurança: Nonce inválido.' ) );
        wp_die();
    }

    // Sanitize and validate main 'saida' data
    $saida_categoria_id = isset( $_POST['saida_categoria'] ) ? intval( $_POST['saida_categoria'] ) : 0;
    $saida_mes          = isset( $_POST['saida_mes'] ) ? sanitize_text_field( $_POST['saida_mes'] ) : '';
    $saida_titulo_extra = isset( $_POST['saida_titulo_extra'] ) ? sanitize_text_field( $_POST['saida_titulo_extra'] ) : '';
    // Removed $saida_discount, $saida_extra, $saida_frete from main saída data
    $saida_total        = isset( $_POST['saida_total'] ) ? floatval( $_POST['saida_total'] ) : 0.00; // This will be the sum of item totals

    $itens_json         = isset( $_POST['itens_json'] ) ? stripslashes( $_POST['itens_json'] ) : '[]';
    $itens_data         = json_decode( $itens_json, true );

    // Basic validation
    if ( empty( $saida_categoria_id ) ) {
        wp_send_json_error( array( 'message' => 'Por favor, selecione a categoria da saída.' ) );
        wp_die();
    }
    if ( empty( $saida_mes ) ) {
        wp_send_json_error( array( 'message' => 'Por favor, selecione o mês da saída.' ) );
        wp_die();
    }
    if ( empty( $saida_titulo_extra ) ) {
        wp_send_json_error( array( 'message' => 'Por favor, insira o título da saída.' ) );
        wp_die();
    }
    if ( empty( $itens_data ) || ! is_array( $itens_data ) ) {
        wp_send_json_error( array( 'message' => 'Adicione pelo menos um item à saída.' ) );
        wp_die();
    }

    // Construct the main 'saida' title
    $saida_title = $saida_mes . ' - ' . $saida_titulo_extra;

    // Create the main 'saida' post
    $saida_post_data = array(
        'post_title'  => $saida_title,
        'post_status' => 'publish',
        'post_type'   => 'saidas',
    );

    $saida_id = wp_insert_post( $saida_post_data );

    if ( is_wp_error( $saida_id ) ) {
        wp_send_json_error( array( 'message' => 'Erro ao criar a saída: ' . $saida_id->get_error_message() ) );
        wp_die();
    }

    // Associate the category with the 'saida' post
    wp_set_object_terms( $saida_id, $saida_categoria_id, 'categoria-saida' );

    // Save main 'saida' meta field (only total, as other amount fields moved to items)
    update_post_meta( $saida_id, 'saida_total', $saida_total );

    // Process and create 'itens' posts
    foreach ( $itens_data as $item ) {
        $item_title      = sanitize_text_field( $item['item_title'] );
        $item_preco_unit = floatval( $item['preco_unit'] );
        $item_quantidade = intval( $item['quantidade'] );
        $item_discount   = isset( $item['item_discount'] ) ? floatval( $item['item_discount'] ) : 0.00;
        $item_extra      = isset( $item['item_extra'] ) ? floatval( $item['item_extra'] ) : 0.00;
        $item_frete      = isset( $item['item_frete'] ) ? floatval( $item['item_frete'] ) : 0.00;
        $temp_item_uuid  = isset( $item['temp_uuid'] ) ? sanitize_text_field( $item['temp_uuid'] ) : ''; // New: Temporary UUID for file linking

        $item_post_data = array(
            'post_title'  => $item_title,
            'post_status' => 'publish',
            'post_type'   => 'itens',
        );

        $item_id = wp_insert_post( $item_post_data );

        if ( is_wp_error( $item_id ) ) {
            error_log( 'Erro ao criar item de saída (' . $item_title . '): ' . $item_id->get_error_message() );
            continue; 
        }

        // Set 'tipo-itens' taxonomy term for 'entrada'
        wp_set_object_terms($item_id, 'saida', 'tipo-itens');


        // Save 'item' meta fields, including relationship to 'saida' and new amount fields
        update_post_meta( $item_id, 'saida_id',        $saida_id );
        update_post_meta( $item_id, 'item_preco_unit', $item_preco_unit );
        update_post_meta( $item_id, 'item_quantidade', $item_quantidade );
        update_post_meta( $item_id, 'item_discount',   $item_discount );
        update_post_meta( $item_id, 'item_extra',      $item_extra );
        update_post_meta( $item_id, 'item_frete',      $item_frete );

        // Handle file upload for the item
        if ( ! empty( $temp_item_uuid ) && isset( $_FILES[ 'item_file_upload_' . $temp_item_uuid ] ) ) {
            $file_data = $_FILES[ 'item_file_upload_' . $temp_item_uuid ];
            
            // Call the existing process_file_upload function
            // This function expects $file, $acf_field_key, $user_id (which for CPTs becomes $post_id)
            // For general post meta, we'll adapt by using a custom post meta key
            // We need to load the necessary WordPress media functions if not already loaded
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            if ( ! function_exists( 'wp_insert_attachment' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
            }
            if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
            }

            $uploaded_file = wp_handle_upload( $file_data, array( 'test_form' => false ) );

            if ( isset( $uploaded_file['error'] ) ) {
                error_log( 'Erro no upload do arquivo para o item ' . $item_title . ': ' . $uploaded_file['error'] );
            } elseif ( isset( $uploaded_file['file'] ) ) {
                $attachment = array(
                    'guid'           => $uploaded_file['url'],
                    'post_mime_type' => $uploaded_file['type'],
                    'post_title'     => sanitize_file_name( basename( $uploaded_file['file'] ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attachment_id = wp_insert_attachment( $attachment, $uploaded_file['file'], $item_id );

                if ( ! is_wp_error( $attachment_id ) ) {
                    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $uploaded_file['file'] );
                    wp_update_attachment_metadata( $attachment_id, $attachment_data );
                    
                    // Save the attachment ID as post meta for the item
                    update_post_meta( $item_id, 'item_attachment_id', $attachment_id );
                } else {
                    error_log( 'Erro ao inserir anexo para o item ' . $item_title . ': ' . $attachment_id->get_error_message() );
                }
            }
        }
    }

    wp_send_json_success( array( 'message' => 'Saída criada com sucesso!', 'saida_id' => $saida_id ) );
    wp_die();
}
add_action('wp_ajax_criar_saida_frontend', 'criar_saida_frontend');
function criar_saida_frontend() {
    if (!isset($_POST['criar_saida_nonce_field']) || !wp_verify_nonce($_POST['criar_saida_nonce_field'], 'criar_saida_nonce')) {
        wp_send_json_error(['message' => 'Falha na verificação de segurança.']);
        return;
    }

    $saida_categoria = sanitize_text_field($_POST['saida_categoria']);
    $saida_mes = sanitize_text_field($_POST['saida_mes']);
    $saida_titulo_extra = sanitize_text_field($_POST['saida_titulo_extra']);
    $post_title = $saida_mes . ' - ' . $saida_titulo_extra;

    $post_data = array(
        'post_title'  => $post_title,
        'post_type'   => 'saidas',
        'post_status' => 'publish',
    );
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Erro ao criar a saída.']);
        return;
    }

    wp_set_object_terms($post_id, intval($saida_categoria), 'categoria-saida');
    update_post_meta($post_id, 'saida_total', sanitize_text_field($_POST['saida_total']));

    $itens = isset($_POST['itens_json']) ? json_decode(stripslashes($_POST['itens_json']), true) : [];

    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    foreach ($itens as &$item) {
        $uuid = $item['temp_uuid'];
        $file_keys = ['file', 'comprovante', 'recibo'];

        foreach ($file_keys as $key) {
            $input_name = "item_{$key}_upload_{$uuid}";
            if (isset($_FILES[$input_name])) {
                $uploaded_file = wp_handle_upload($_FILES[$input_name], array('test_form' => false));
                if ($uploaded_file && !isset($uploaded_file['error'])) {
                    $item[$key . '_url'] = $uploaded_file['url'];
                }
            }
        }
    }

    update_post_meta($post_id, 'itens_saida', json_encode($itens, JSON_UNESCAPED_UNICODE));

    wp_send_json_success(['message' => 'Saída criada com sucesso!']);
    wp_die();
}
// If non-logged-in users should also be able to create salidas, uncomment the line below:
// add_action( 'wp_ajax_nopriv_criar_saida_frontend', 'criar_saida_frontend_callback' );


add_action('wp_ajax_editar_saida_frontend', 'editar_saida_frontend_callback');
function editar_saida_frontend_callback() {
    if (!isset($_POST['editar_saida_nonce_field']) || !wp_verify_nonce($_POST['editar_saida_nonce_field'], 'editar_saida_nonce')) {
        wp_send_json_error(['message' => 'Falha na verificação de segurança.']);
        return;
    }

    $post_id = isset($_POST['saida_id']) ? intval($_POST['saida_id']) : 0;
    if (!$post_id) {
        wp_send_json_error(['message' => 'ID da saída não fornecido.']);
        return;
    }

    $saida_categoria = sanitize_text_field($_POST['saida_categoria']);
    $saida_mes = sanitize_text_field($_POST['saida_mes']);
    $saida_titulo_extra = sanitize_text_field($_POST['saida_titulo_extra']);
    $post_title = $saida_mes . ' - ' . $saida_titulo_extra;

    wp_update_post(array('ID' => $post_id, 'post_title' => $post_title));
    wp_set_object_terms($post_id, intval($saida_categoria), 'categoria-saida');
    update_post_meta($post_id, 'saida_total', sanitize_text_field($_POST['saida_total']));

    $itens_data = isset($_POST['itens_json']) ? json_decode(stripslashes($_POST['itens_json']), true) : [];
    $updated_itens = [];

    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    foreach ($itens_data as $item) {
        if (isset($item['is_deleted']) && $item['is_deleted']) {
            continue; // Pula itens marcados para exclusão
        }

        $uuid = $item['temp_uuid'];
        $file_keys = ['file', 'comprovante', 'recibo'];

        foreach ($file_keys as $key) {
            $input_name = "item_{$key}_upload_{$uuid}";
            if (isset($_FILES[$input_name])) {
                $uploaded_file = wp_handle_upload($_FILES[$input_name], array('test_form' => false));
                if ($uploaded_file && !isset($uploaded_file['error'])) {
                    $item[$key . '_url'] = $uploaded_file['url'];
                }
            }
        }
        $updated_itens[] = $item;
    }

    update_post_meta($post_id, 'itens_saida', json_encode($updated_itens, JSON_UNESCAPED_UNICODE));

    wp_send_json_success(['message' => 'Saída atualizada com sucesso!']);
    wp_die();
}

// ... existing code ...
add_action( 'init', 'saidas_cpt' );

// AJAX para excluir uma saída e seus itens relacionados
function salvar_nova_saida_frontend_callback() {
    // 1. Nonce Verification
    if (!isset($_POST['nova_saida_nonce_field']) || !wp_verify_nonce($_POST['nova_saida_nonce_field'], 'nova_saida_action')) {
        wp_send_json_error(['message' => 'Falha na verificação de segurança.']);
        wp_die();
    }

    // 2. Data Sanitization
    $saida_categoria = sanitize_text_field($_POST['saida_categoria']);
    $saida_mes = sanitize_text_field($_POST['saida_mes']);
    $saida_titulo_extra = sanitize_text_field($_POST['saida_titulo_extra']);
    $post_title = $saida_mes . ' - ' . $saida_titulo_extra;

    // 3. Post Creation
    $new_post_data = array(
        'post_title'   => $post_title,
        'post_status'  => 'publish',
        'post_type'    => 'saidas',
    );
    $post_id = wp_insert_post($new_post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Erro ao criar a nova saída: ' . $post_id->get_error_message()]);
        wp_die();
    }

    // 4. Taxonomy Assignment
    if (!empty($saida_categoria)) {
        wp_set_object_terms($post_id, intval($saida_categoria), 'categoria-saida');
    }

    // 5. Meta Data Update (saida_total and itens_saida)
    update_post_meta($post_id, 'saida_total', sanitize_text_field($_POST['saida_total']));

    $itens_data = isset($_POST['itens_json']) ? json_decode(stripslashes($_POST['itens_json']), true) : [];
    $updated_itens = [];

    // Ensure wp_handle_upload is available
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    // 6. File Upload Handling
    foreach ($itens_data as $item) {
        // If an item is marked for deletion, skip it.
        // For new entries, all items are "new" so this check is less critical,
        // but it's good practice if this function were ever to handle updates.
        if (isset($item['is_deleted']) && $item['is_deleted']) {
            continue;
        }

        $uuid = $item['temp_uuid'];
        $file_keys = ['file', 'comprovante', 'recibo'];

        foreach ($file_keys as $key) {
            $input_name = "item_{$key}_upload_{$uuid}";
            if (isset($_FILES[$input_name])) {
                $uploaded_file = wp_handle_upload($_FILES[$input_name], array('test_form' => false));
                if ($uploaded_file && !isset($uploaded_file['error'])) {
                    $item[$key . '_url'] = $uploaded_file['url'];
                }
            }
        }
        $updated_itens[] = $item;
    }

    update_post_meta($post_id, 'itens_saida', json_encode($updated_itens, JSON_UNESCAPED_UNICODE));

    // 7. Response
    wp_send_json_success(['message' => 'Nova saída criada com sucesso!', 'saida_id' => $post_id]);
    wp_die();
}

// Add the AJAX hook for the new function
add_action('wp_ajax_salvar_nova_saida_frontend', 'salvar_nova_saida_frontend_callback');

add_action('wp_ajax_delete_saida_and_related_items', 'delete_saida_and_related_items_callback');
function delete_saida_and_related_items_callback() {
    // 1. Verificar o nonce de segurança
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'delete_saida_nonce')) {
        error_log('Falha na verificação de segurança (nonce inválido) ao tentar excluir saída.');
        wp_send_json_error(array('message' => 'Erro de segurança: Nonce inválido.'));
        wp_die();
    }
    // 2. Obter o ID da saída
    $saida_id = isset($_POST['saida_id']) ? intval($_POST['saida_id']) : 0;
    if ($saida_id <= 0) {
        error_log('ID da saída inválido ou ausente: ' . $saida_id);
        wp_send_json_error(array('message' => 'ID da saída inválido.'));
        wp_die();
    }
    // 3. Verificar se o post é realmente do tipo 'saidas'
    if (get_post_type($saida_id) !== 'saidas') {
        error_log('Tentativa de exclusão de um post que não é do tipo "saidas". ID: ' . $saida_id);
        wp_send_json_error(array('message' => 'Não é possível excluir: O ID fornecido não corresponde a uma Saída.'));
        wp_die();
    }
    // 4. Coletar e excluir todos os 'itens' associados a esta 'saida'
    $itens_args = array(
        'post_type'      => 'itens',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key'   => 'saida_id',
                'value' => $saida_id,
                'compare' => '=',
            ),
        ),
        'fields'         => 'ids', // Apenas IDs para otimização
    );
    $itens_query = new WP_Query($itens_args);
    $deleted_items_count = 0;
    if ($itens_query->have_posts()) {
        foreach ($itens_query->posts as $item_id) {
            // Excluir anexo do item, se houver
            $attachment_id = get_post_meta($item_id, 'item_attachment_id', true);
            if ($attachment_id) {
                if (wp_delete_attachment($attachment_id, true)) {
                    error_log('Anexo do item ' . $item_id . ' excluído com sucesso. ID Anexo: ' . $attachment_id);
                } else {
                    error_log('Falha ao excluir anexo do item ' . $item_id . '. ID Anexo: ' . $attachment_id);
                }
            }
            // Excluir o post do item
            if (wp_delete_post($item_id, true)) { // 'true' para exclusão permanente
                $deleted_items_count++;
                error_log('Item associado ' . $item_id . ' excluído com sucesso.');
            } else {
                error_log('Falha ao excluir item associado ' . $item_id . '.');
            }
        }
        wp_reset_postdata();
    }
    error_log('Total de itens associados excluídos para a Saída ' . $saida_id . ': ' . $deleted_items_count);
    // 5. Excluir o post principal da 'saida'
    if (wp_delete_post($saida_id, true)) { // 'true' para exclusão permanente
        error_log('Saída ' . $saida_id . ' excluída com sucesso.');
        wp_send_json_success(array('message' => 'Saída e seus itens relacionados excluídos com sucesso!'));
    } else {
        error_log('Falha ao excluir a Saída ' . $saida_id . '.');
        wp_send_json_error(array('message' => 'Erro ao excluir a saída.'));
    }
    wp_die();
}
// ... existing code ...

// Edição AJAX individual de item de saída
add_action('wp_ajax_editar_item_saida_individual', 'editar_item_saida_individual_callback');
function editar_item_saida_individual_callback() {
    error_log('=== [DEBUG] Endpoint editar_item_saida_individual_callback chamado ===');
    error_log('POST: ' . print_r($_POST, true));
    error_log('FILES: ' . print_r($_FILES, true));
    // Validação de segurança
    if (!isset($_POST['editar_item_nonce']) || !wp_verify_nonce($_POST['editar_item_nonce'], 'editar_item_saida_individual')) {
        error_log('Nonce inválido ou ausente: ' . print_r($_POST['editar_item_nonce'], true));
        wp_send_json_error(['message' => 'Falha na verificação de segurança.']);
        wp_die();
    }
    $saida_id = isset($_POST['saida_id']) ? intval($_POST['saida_id']) : 0;
    $item_uuid = isset($_POST['item_uuid']) ? sanitize_text_field($_POST['item_uuid']) : '';
    error_log('Recebido saida_id: ' . $saida_id . ' | item_uuid: ' . $item_uuid);
    if (!$saida_id || !$item_uuid) {
        error_log('Dados insuficientes para editar o item.');
        wp_send_json_error(['message' => 'Dados insuficientes para editar o item.']);
        wp_die();
    }
    // Buscar itens existentes
    $itens_json = get_post_meta($saida_id, 'itens_saida', true);
    $itens = !empty($itens_json) ? json_decode($itens_json, true) : [];
    $found = false;
    foreach ($itens as &$item) {
        if ($item['temp_uuid'] === $item_uuid) {
            error_log('Item encontrado para edição: ' . print_r($item, true));
            // Atualizar campos
            $item['item_title'] = sanitize_text_field($_POST['item_title'] ?? $item['item_title']);
            $item['preco_unit'] = floatval($_POST['preco_unit'] ?? $item['preco_unit']);
            $item['item_discount'] = floatval($_POST['item_discount'] ?? $item['item_discount']);
            $item['item_extra'] = floatval($_POST['item_extra'] ?? $item['item_extra']);
            $item['item_frete'] = floatval($_POST['item_frete'] ?? $item['item_frete']);
            // Upload de arquivos
            $file_keys = ['file', 'comprovante', 'recibo'];
            foreach ($file_keys as $key) {
                $input_name = "item_{$key}_upload_{$item_uuid}";
                if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
                    error_log('Recebido arquivo para ' . $key . ': ' . print_r($_FILES[$input_name], true));
                    if (!function_exists('wp_handle_upload')) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                    }
                    $uploaded_file = wp_handle_upload($_FILES[$input_name], array('test_form' => false));
                    if ($uploaded_file && !isset($uploaded_file['error'])) {
                        $item[$key . '_url'] = $uploaded_file['url'];
                        error_log('Upload bem-sucedido para ' . $key . ': ' . $uploaded_file['url']);
                    } else {
                        error_log('Erro no upload para ' . $key . ': ' . print_r($uploaded_file, true));
                    }
                }
            }
            $found = true;
            break;
        }
    }
    if (!$found) {
        error_log('Item não encontrado para edição. UUID: ' . $item_uuid);
        wp_send_json_error(['message' => 'Item não encontrado para edição.']);
        wp_die();
    }
    // Salvar de volta
    $salvo = update_post_meta($saida_id, 'itens_saida', json_encode($itens, JSON_UNESCAPED_UNICODE));
    error_log('Salvamento do array de itens: ' . ($salvo ? 'OK' : 'FALHOU'));
    error_log('Item final retornado: ' . print_r($item, true));
    wp_send_json_success(['message' => 'Item atualizado com sucesso!', 'item' => $item]);
    wp_die();
}
// ... existing code ...

// Endpoint AJAX para debug visual do error_log
add_action('wp_ajax_get_last_error_log', function() {
    if (!current_user_can('administrator')) {
        wp_send_json_error(['message' => 'Acesso negado.']);
        wp_die();
    }
    $log_path = ini_get('error_log');
    if (!$log_path || !file_exists($log_path)) {
        wp_send_json_error(['message' => 'Arquivo de log não encontrado: ' . $log_path]);
        wp_die();
    }
    $lines = file($log_path);
    $last_lines = array_slice($lines, -50);
    wp_send_json_success(['log' => implode('', $last_lines)]);
    wp_die();
});
// Processar remoção de arquivos na edição de saídas
add_action('wp_ajax_editar_saida_frontend', 'processar_remocao_arquivos_saida');
function processar_remocao_arquivos_saida() {
    $itens_data = isset($_POST['itens_json']) ? json_decode(stripslashes($_POST['itens_json']), true) : [];
    
    foreach ($itens_data as &$item) {
        $uuid = $item['temp_uuid'];
        
        // Processar flags de remoção
        $file_keys = ['file', 'comprovante', 'recibo'];
        foreach ($file_keys as $key) {
            $remove_flag = "remove_{$key}_upload_{$uuid}";
            if (isset($_POST[$remove_flag]) && $_POST[$remove_flag] === 'REMOVE') {
                $item[$key . '_url'] = null; // Remove a URL do arquivo
            }
        }
    }
    
    return $itens_data;
}
