<?php

// produtos

/**
 * Função utilitária para obter categorias de produto hierárquicas
 * Retorna array com categorias formatadas para select
 */
function get_hierarchical_product_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    
    if (is_wp_error($categories) || empty($categories)) {
        return array();
    }
    
    // Organizar categorias hierarquicamente
    $hierarchical = array();
    $parent_categories = array();
    $child_categories = array();
    
    // Separar pais e filhos
    foreach ($categories as $category) {
        if ($category->parent == 0) {
            $parent_categories[] = $category;
        } else {
            $child_categories[$category->parent][] = $category;
        }
    }
    
    // Montar array hierárquico
    foreach ($parent_categories as $parent) {
        $hierarchical[] = array(
            'id' => $parent->term_id,
            'name' => $parent->name,
            'level' => 0
        );
        
        // Adicionar filhos se existirem
        if (isset($child_categories[$parent->term_id])) {
            foreach ($child_categories[$parent->term_id] as $child) {
                $hierarchical[] = array(
                    'id' => $child->term_id,
                    'name' => '— ' . $child->name,
                    'level' => 1
                );
            }
        }
    }
    
    return $hierarchical;
}

/**
 * Função para garantir que existe a categoria padrão "Sem categoria"
 * Retorna o ID da categoria padrão
 */
function ensure_uncategorized_product_category() {
    $uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
    
    if (!$uncategorized) {
        // Criar categoria "Sem categoria" se não existir
        $result = wp_insert_term(
            'Sem categoria',
            'product_cat',
            array(
                'slug' => 'uncategorized',
                'description' => 'Categoria padrão para produtos sem categoria específica'
            )
        );
        
        if (!is_wp_error($result)) {
            $uncategorized = get_term($result['term_id'], 'product_cat');
            error_log('[DEBUG] Created uncategorized category with ID: ' . $result['term_id']);
        } else {
            error_log('[DEBUG] Failed to create uncategorized category: ' . $result->get_error_message());
            return false;
        }
    }
    
    return $uncategorized ? $uncategorized->term_id : false;
}


// PRODUTOS Funções para criação, atualização e exclusão de produtos simples do WooCommerce via AJAX
// Only load if WooCommerce is active
add_action('init', function() {
    if (class_exists('WooCommerce')) {

/**
 * Handle AJAX request to create a simple WooCommerce product.
 */
// AJAX para criar produto WooCommerce com upload moderno
add_action('wp_ajax_create_simple_product', 'handle_create_simple_product_ajax');
add_action('wp_ajax_nopriv_create_simple_product', 'handle_create_simple_product_ajax');

// Log para verificar se a função foi registrada
error_log('[DEBUG] AJAX handler create_simple_product registered successfully');

function handle_create_simple_product_ajax() {
    // Log para debug
    error_log('=== [DEBUG] handle_create_simple_product_ajax iniciado ===');
    error_log('[DEBUG] Current user ID: ' . get_current_user_id());
    error_log('[DEBUG] Current user capabilities: ' . print_r(wp_get_current_user()->allcaps, true));
    error_log('[DEBUG] POST data: ' . print_r($_POST, true));
    error_log('[DEBUG] FILES data: ' . print_r($_FILES, true));
    
    // Verificar se o WooCommerce está ativo
    if (!class_exists('WooCommerce')) {
        error_log('[DEBUG] WooCommerce not active');
        wp_send_json_error(array('message' => 'WooCommerce não está ativo.'));
        return;
    }
    
    // Verificar se o usuário está logado
    if (!is_user_logged_in()) {
        error_log('[DEBUG] User not logged in');
        wp_send_json_error(array('message' => 'Usuário não está logado.'));
        return;
    }
    
    // Verificar nonce
    if (!isset($_POST['create_product_nonce_field']) || !wp_verify_nonce($_POST['create_product_nonce_field'], 'create_product_nonce')) {
        error_log('[DEBUG] Nonce verification failed. Nonce received: ' . ($_POST['create_product_nonce_field'] ?? 'NOT_SET'));
        wp_send_json_error(array('message' => 'Erro de segurança: Nonce inválido.'));
        return;
    }
    
    error_log('[DEBUG] Nonce verification passed');

    // Verificar permissões do usuário
    if (!current_user_can('edit_products')) {
        error_log('[DEBUG] User does not have permission to edit products');
        wp_send_json_error(array('message' => 'Você não tem permissão para criar produtos.'));
        return;
    }

    // Dados do produto com validação
    $product_name = isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '';
    $regular_price = isset($_POST['regular_price']) ? floatval($_POST['regular_price']) : 0;
    $sale_price = isset($_POST['sale_price']) && !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : '';
    $manage_stock = isset($_POST['manage_stock']) ? true : false;
    $stock_quantity = $manage_stock && isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : null;
    $stock_status = isset($_POST['stock_status']) ? sanitize_text_field($_POST['stock_status']) : 'instock';
    $short_description = isset($_POST['short_description']) ? sanitize_textarea_field($_POST['short_description']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

    // Validações básicas
    if (empty($product_name)) {
        wp_send_json_error(array('message' => 'Nome do produto é obrigatório.'));
        return;
    }

    if ($regular_price <= 0) {
        wp_send_json_error(array('message' => 'Preço regular deve ser maior que zero.'));
        return;
    }

    error_log('[DEBUG] Product data validated successfully');

    try {
        // Criar produto
        $product = new WC_Product_Simple();
        $product->set_name($product_name);
        $product->set_regular_price($regular_price);
        
        if ($sale_price) {
            $product->set_sale_price($sale_price);
        }
        
        $product->set_manage_stock($manage_stock);
        
        if ($manage_stock && $stock_quantity !== null) {
            $product->set_stock_quantity($stock_quantity);
        }
        
        $product->set_stock_status($stock_status);
        $product->set_short_description($short_description);
        $product->set_description($description);
        $product->set_status('publish');

        // Processar upload de imagem usando a função existente
        if (isset($_FILES['product_image_file']) && !empty($_FILES['product_image_file']['tmp_name'])) {
            error_log('[DEBUG] Processing image upload');
            $image_id = upload_file_to_wordpress($_FILES['product_image_file']);
            if ($image_id) {
                $product->set_image_id($image_id);
                error_log('[DEBUG] Image uploaded successfully, attachment ID: ' . $image_id);
            } else {
                error_log('[DEBUG] Image upload failed');
            }
        }

        $product_id = $product->save();

        if (is_wp_error($product_id)) {
            error_log('[DEBUG] Product save error: ' . $product_id->get_error_message());
            wp_send_json_error(array('message' => 'Erro ao salvar o produto: ' . $product_id->get_error_message()));
            return;
        }

        if ($product_id) {
            // Processar categoria do produto
            if (isset($_POST['product_category']) && !empty($_POST['product_category'])) {
                $category_id = intval($_POST['product_category']);
                
                // Verificar se a categoria existe
                if (term_exists($category_id, 'product_cat')) {
                    wp_set_object_terms($product_id, array($category_id), 'product_cat', false);
                    error_log('[DEBUG] Category assigned successfully: ' . $category_id);
                } else {
                    error_log('[DEBUG] Category not found: ' . $category_id);
                }
            } else {
                // Se nenhuma categoria foi selecionada, usar a categoria padrão "Sem categoria"
                $uncategorized_id = ensure_uncategorized_product_category();
                if ($uncategorized_id) {
                    wp_set_object_terms($product_id, array($uncategorized_id), 'product_cat', false);
                    error_log('[DEBUG] Default uncategorized category assigned');
                }
            }
            
            error_log('[DEBUG] Product created successfully with ID: ' . $product_id);
            wp_send_json_success(array(
                'message' => 'Produto criado com sucesso!',
                'product_id' => $product_id
            ));
        } else {
            error_log('[DEBUG] Product creation failed - no ID returned');
            wp_send_json_error(array('message' => 'Erro ao criar produto: ID não retornado.'));
        }
        
    } catch (Exception $e) {
        error_log('[DEBUG] Exception caught: ' . $e->getMessage());
        error_log('[DEBUG] Exception trace: ' . $e->getTraceAsString());
        wp_send_json_error(array('message' => 'Erro interno: ' . $e->getMessage()));
        return;
    }
    
    wp_die(); // Garantir que a execução pare aqui
}



// add_action( 'wp_ajax_nopriv_create_simple_product', 'amedis_create_simple_product' ); // Uncomment if needed for non-logged-in users


/**
 * Handle AJAX request to update a simple WooCommerce product.
 */
// AJAX para editar produto WooCommerce com upload moderno
add_action('wp_ajax_update_simple_product', 'handle_update_simple_product_ajax');
add_action('wp_ajax_nopriv_update_simple_product', 'handle_update_simple_product_ajax');

// Log para verificar se a função foi registrada
error_log('[DEBUG] AJAX handler update_simple_product registered successfully');

function handle_update_simple_product_ajax() {
    // Log para debug
    error_log('=== [DEBUG] handle_update_simple_product_ajax iniciado ===');
    error_log('[DEBUG] Current user ID: ' . get_current_user_id());
    error_log('[DEBUG] Current user capabilities: ' . print_r(wp_get_current_user()->allcaps, true));
    error_log('[DEBUG] POST data: ' . print_r($_POST, true));
    error_log('[DEBUG] FILES data: ' . print_r($_FILES, true));
    
    // Verificar se o WooCommerce está ativo
    if (!class_exists('WooCommerce')) {
        error_log('[DEBUG] WooCommerce not active');
        wp_send_json_error(array('message' => 'WooCommerce não está ativo.'));
        return;
    }
    
    // Verificar se o usuário está logado
    if (!is_user_logged_in()) {
        error_log('[DEBUG] User not logged in');
        wp_send_json_error(array('message' => 'Usuário não está logado.'));
        return;
    }
    
    // Verificar nonce
    if (!isset($_POST['update_product_nonce_field']) || !wp_verify_nonce($_POST['update_product_nonce_field'], 'update_product_nonce')) {
        error_log('[DEBUG] Nonce verification failed. Nonce received: ' . ($_POST['update_product_nonce_field'] ?? 'NOT_SET'));
        wp_send_json_error(array('message' => 'Erro de segurança: Nonce inválido.'));
        return;
    }
    
    error_log('[DEBUG] Nonce verification passed');

    // Verificar permissões do usuário
    if (!current_user_can('edit_products')) {
        error_log('[DEBUG] User does not have permission to edit products');
        wp_send_json_error(array('message' => 'Você não tem permissão para editar produtos.'));
        return;
    }

    // Verificar se o ID do produto foi fornecido
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        error_log('[DEBUG] Product ID not provided');
        wp_send_json_error(array('message' => 'ID do produto não fornecido.'));
        return;
    }

    $product_id = intval($_POST['product_id']);
    error_log('[DEBUG] Product ID: ' . $product_id);
    
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('simple')) {
        error_log('[DEBUG] Product not found or not simple type');
        wp_send_json_error(array('message' => 'Produto não encontrado ou não é do tipo simples.'));
        return;
    }

    // Dados do produto com validação
    $product_name = isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '';
    $regular_price = isset($_POST['regular_price']) ? floatval($_POST['regular_price']) : 0;
    $sale_price = isset($_POST['sale_price']) && !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : '';
    $manage_stock = isset($_POST['manage_stock']) ? true : false;
    $stock_quantity = $manage_stock && isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : null;
    $stock_status = isset($_POST['stock_status']) ? sanitize_text_field($_POST['stock_status']) : 'instock';
    $short_description = isset($_POST['short_description']) ? sanitize_textarea_field($_POST['short_description']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $remove_current_image = isset($_POST['remove_current_image']);

    // Validações básicas
    if (empty($product_name)) {
        wp_send_json_error(array('message' => 'Nome do produto é obrigatório.'));
        return;
    }

    if ($regular_price <= 0) {
        wp_send_json_error(array('message' => 'Preço regular deve ser maior que zero.'));
        return;
    }

    error_log('[DEBUG] Product data validated successfully');

    try {
        // Atualizar produto
        $product->set_name($product_name);
        $product->set_regular_price($regular_price);
        
        if ($sale_price) {
            $product->set_sale_price($sale_price);
        } else {
            $product->set_sale_price('');
        }
        
        $product->set_manage_stock($manage_stock);
        
        if ($manage_stock && $stock_quantity !== null) {
            $product->set_stock_quantity($stock_quantity);
        }
        
        $product->set_stock_status($stock_status);
        $product->set_short_description($short_description);
        $product->set_description($description);

        $response_data = array('message' => 'Produto atualizado com sucesso!');

        // Processar remoção de imagem atual
        if ($remove_current_image) {
            $product->set_image_id('');
            $response_data['image_removed'] = true;
            error_log('[DEBUG] Current image removed');
        }

        // Processar upload de nova imagem
        if (isset($_FILES['product_image_file']) && !empty($_FILES['product_image_file']['tmp_name'])) {
            error_log('[DEBUG] Processing image upload');
            
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            $uploaded_file = wp_handle_upload($_FILES['product_image_file'], array('test_form' => false));
            
            if (isset($uploaded_file['error'])) {
                error_log('[DEBUG] Upload error: ' . $uploaded_file['error']);
                wp_send_json_error(array('message' => 'Erro no upload da imagem: ' . $uploaded_file['error']));
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
                
                if (is_wp_error($attachment_id)) {
                    error_log('[DEBUG] Attachment creation error: ' . $attachment_id->get_error_message());
                    wp_send_json_error(array('message' => 'Erro ao criar anexo da imagem.'));
                    return;
                }
                
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                $product->set_image_id($attachment_id);
                $response_data['new_image_url'] = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                
                error_log('[DEBUG] Image uploaded successfully, attachment ID: ' . $attachment_id);
            }
        }

        // Salvar o produto
        $saved_product_id = $product->save();
        
        if (is_wp_error($saved_product_id)) {
            error_log('[DEBUG] Product save error: ' . $saved_product_id->get_error_message());
            wp_send_json_error(array('message' => 'Erro ao salvar o produto: ' . $saved_product_id->get_error_message()));
            return;
        }
        
        // Processar categoria do produto
        if (isset($_POST['product_category'])) {
            if (!empty($_POST['product_category'])) {
                $category_id = intval($_POST['product_category']);
                
                // Verificar se a categoria existe
                if (term_exists($category_id, 'product_cat')) {
                    wp_set_object_terms($product_id, array($category_id), 'product_cat', false);
                    error_log('[DEBUG] Category updated successfully: ' . $category_id);
                } else {
                    error_log('[DEBUG] Category not found: ' . $category_id);
                }
            } else {
                // Se categoria vazia foi selecionada, usar a categoria padrão "Sem categoria"
                $uncategorized_id = ensure_uncategorized_product_category();
                if ($uncategorized_id) {
                    wp_set_object_terms($product_id, array($uncategorized_id), 'product_cat', false);
                    error_log('[DEBUG] Default uncategorized category assigned');
                } else {
                    // Se não conseguiu criar categoria padrão, limpar categorias
                    wp_set_object_terms($product_id, array(), 'product_cat', false);
                    error_log('[DEBUG] Categories cleared');
                }
            }
        }
        
        error_log('[DEBUG] Product saved successfully with ID: ' . $saved_product_id);
        
        wp_send_json_success($response_data);
        
    } catch (Exception $e) {
        error_log('[DEBUG] Exception caught: ' . $e->getMessage());
        error_log('[DEBUG] Exception trace: ' . $e->getTraceAsString());
        wp_send_json_error(array('message' => 'Erro interno: ' . $e->getMessage()));
        return;
    }
    
    wp_die(); // Garantir que a execução pare aqui
}

// add_action( 'wp_ajax_nopriv_update_simple_product', 'amedis_update_simple_product' ); // Uncomment if needed


/**
 * Handle AJAX request to delete a simple WooCommerce product.
 */
function amedis_delete_simple_product() {
    // Verify nonce
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'delete_product_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
        wp_die();
    }

    // Check user capabilities
    if ( ! current_user_can( 'delete_products' ) ) {
        wp_send_json_error( array( 'message' => 'You do not have permission to delete products.' ) );
        wp_die();
    }

    // Sanitize and validate input
    $product_id = intval( $_POST['product_id'] );

    if ( empty( $product_id ) ) {
        wp_send_json_error( array( 'message' => 'Product ID is required.' ) );
        wp_die();
    }

    $product = wc_get_product( $product_id );

    if ( ! $product || ! $product->is_type('simple') ) {
        wp_send_json_error( array( 'message' => 'Invalid product ID or product is not simple.' ) );
        wp_die();
    }

    // Delete the product (true for permanent delete)
    $deleted = wp_delete_post( $product_id, true );

    if ( $deleted ) {
        wp_send_json_success( array( 'message' => 'Produto excluído com sucesso!', 'product_id' => $product_id ) );
    } else {
        wp_send_json_error( array( 'message' => 'Erro ao excluir o produto.' ) );
    }

    wp_die();
}
add_action( 'wp_ajax_delete_simple_product', 'amedis_delete_simple_product' );
// add_action( 'wp_ajax_nopriv_delete_simple_product', 'amedis_delete_simple_product' ); // Uncomment if needed

    } // End WooCommerce check
}); // End plugins_loaded hook

/**
 * WooCommerce Functions
 * 
 * Only load if WooCommerce is active
 */

// Já verificamos se o WooCommerce está ativo em functions.php, então não precisamos verificar novamente aqui
defined('ABSPATH') || exit;

// Declara a função no escopo global para garantir que ela esteja disponível
if (!function_exists('get_order_display_data')) {
    /**
     * Função auxiliar para obter dados formatados de um pedido para exibição no dashboard.
     *
     * @param WC_Order $order O objeto WC_Order.
     * @return array Um array associativo com os dados do pedido.
     */
    function get_order_display_data(WC_Order $order) {
        global $_amedis_recipes_global_cache;
        // O resto da implementação continua igual...
        return $order ? array(
            'order_id' => $order->get_id(),
            'customer_id' => $order->get_customer_id(),
            'customer_name' => $order->get_formatted_billing_full_name(),
            'billing_phone' => $order->get_billing_phone(),
            'order_status' => $order->get_status(),
            'order_status_slug' => str_replace('wc-', '', $order->get_status()),
            'status_text' => wc_get_order_status_name($order->get_status()),
            'is_paid' => $order->is_paid(),
            'pay_url' => $order->get_checkout_payment_url(),
            'total' => $order->get_total(),
            'total_display' => wc_price($order->get_total()),
            'total_tax' => $order->get_total_tax(),
            'total_tax_display' => wc_price($order->get_total_tax()),
            'shipping_address' => $order->get_address('shipping'),
            'formatted_shipping_address' => $order->get_formatted_shipping_address()
        ) : array();
    }
}

// SISTEMA DE PEDIDOS COM INFORMAÇÕES EXTRAS - HANDLER PRINCIPAL

add_action('wp_ajax_criar_pedido_frontend','criar_pedido_frontend_callback');
add_action('wp_ajax_nopriv_criar_pedido_frontend','criar_pedido_frontend_callback');
function criar_pedido_frontend_callback() {
    error_log( 'CRIA_PEDIDO _POST: ' . print_r( $_POST, true ) );
    error_log( 'CRIA_PEDIDO _FILES: ' . print_r( $_FILES, true ) );
    
    // Verificar nonce
    if (!isset($_POST['criar_pedido_nonce_field']) || !wp_verify_nonce($_POST['criar_pedido_nonce_field'], 'criar_pedido_nonce')) {
        error_log('CRIA_PEDIDO: Nonce inválido - Field: ' . ($_POST['criar_pedido_nonce_field'] ?? 'NOT_SET'));
        wp_send_json_error(array('message' => 'Erro de segurança: Nonce inválido.'));
        return;
    }
    
    error_log('CRIA_PEDIDO: Nonce válido, continuando...');

  // 1) Produtos e cliente
  $produtos_json = isset($_POST['produtos_json']) ? sanitize_text_field( $_POST['produtos_json'] ) : '';
  $produtos      = json_decode( stripslashes($produtos_json), true );
  $user_id       = isset($_POST['associado']) ? intval( $_POST['associado'] ) : 0;
  
  // Validações básicas
  if (empty($produtos) || !is_array($produtos)) {
      wp_send_json_error(array('message' => 'Adicione pelo menos um produto ao pedido.'));
      return;
  }
  
  if ($user_id <= 0) {
      wp_send_json_error(array('message' => 'Selecione um associado válido.'));
      return;
  }
  
  // Validar número da transação
  if (empty($_POST['numero_transacao'])) {
      wp_send_json_error(array('message' => 'Número da transação é obrigatório.'));
      return;
  }

  // 2) Desconto e extra
  $desconto = isset($_POST['pedido_discount'])
               ? floatval( str_replace(',', '.', $_POST['pedido_discount']) )
               : 0;
  $extra    = isset($_POST['pedido_extra'])
               ? floatval( str_replace(',', '.', $_POST['pedido_extra']) )
               : 0;
  $frete    = isset($_POST['pedido_frete'])
               ? floatval( str_replace(',', '.', $_POST['pedido_frete']) )
               : 0;
  // 3) Cria pedido e associa ao cliente
  $order = wc_create_order();
  
  if (is_wp_error($order)) {
      wp_send_json_error(array('message' => 'Erro ao criar o pedido: ' . $order->get_error_message()));
      return;
  }
  
  if (!$order) {
      wp_send_json_error(array('message' => 'Erro ao criar o pedido WooCommerce.'));
      return;
  }
  
  $order->set_customer_id( $user_id );

  // 4) Busca nomes do usuário para billing/shipping
  $user  = get_userdata( $user_id );
  $first = sanitize_text_field( $user->first_name );
  $last  = sanitize_text_field( $user->last_name );

  // 5) Monta o array de endereço a partir do form
  $shipping = [
    'first_name' => $first,
    'last_name'  => $last,
    'address_1'  => sanitize_text_field( $_POST['shipping_address_1'] ),
    'address_2'  => sanitize_text_field( $_POST['shipping_address_2'] ),
    'city'       => sanitize_text_field( $_POST['shipping_city'] ),
    'postcode'   => sanitize_text_field( $_POST['shipping_postcode'] ),
    'country'    => 'BR',
    'state'      => sanitize_text_field( $_POST['shipping_state'] ),
  ];

  // 6) Set billing **e** shipping
  $order->set_address( $shipping, 'billing' );
  $order->set_address( $shipping, 'shipping' );

  // 7) Adiciona os produtos
  foreach ( $produtos as $p ) {
    $product = wc_get_product( $p['produto_id'] );
    if (!$product) {
        wp_send_json_error(array('message' => 'Produto não encontrado: ID ' . $p['produto_id']));
        return;
    }
    
    $quantity = intval($p['quantidade']);
    if ($quantity <= 0) {
        wp_send_json_error(array('message' => 'Quantidade inválida para o produto: ' . $product->get_name()));
        return;
    }
    
    $order->add_product( $product, $quantity );
  }

$desconto = floatval( str_replace(',', '.', $_POST['pedido_discount']) );
$extra    = floatval( str_replace(',', '.', $_POST['pedido_extra']) );
$frete    = floatval( str_replace(',', '.', $_POST['pedido_frete']) );

if ( $desconto > 0 ) {
  $fee = new WC_Order_Item_Fee();
  $fee->set_name('Desconto');
  $fee->set_amount( -$desconto );
  $fee->set_total( -$desconto );
  $fee->set_tax_status('none');
  $order->add_item( $fee );
}

if ( $extra > 0 ) {
  $fee = new WC_Order_Item_Fee();
  $fee->set_name('Extra Cartão');
  $fee->set_amount( $extra );
  $fee->set_total( $extra );
  $fee->set_tax_status('none');
  $order->add_item( $fee );
}

if ( $frete > 0 ) {
  $fee = new WC_Order_Item_Fee();
  $fee->set_name('Frete');
  $fee->set_amount( $frete );
  $fee->set_total( $frete );
  $fee->set_tax_status('none');
  $order->add_item( $fee );
}

$order->calculate_totals();

  // 10) Metadados custom (receitas, endereço livre etc.)
  if ( ! empty( $_POST['custom_delivery_address'] ) ) {
    $order->update_meta_data(
      'custom_delivery_address',
      sanitize_textarea_field( $_POST['custom_delivery_address'] )
    );
  }
  
  // Processar upload do comprovante
  if (isset($_FILES['comprovante_file']) && $_FILES['comprovante_file']['error'] === UPLOAD_ERR_OK) {
      $comprovante_id = upload_file_to_wordpress($_FILES['comprovante_file']);
      if ($comprovante_id) {
          $order->update_meta_data('comprovante_id', $comprovante_id);
          $order->update_meta_data('comprovante_url', wp_get_attachment_url($comprovante_id));
      }
  }
  
  // Salvar informações extras
  if (!empty($_POST['numero_transacao'])) {
      $order->update_meta_data('numero_transacao', sanitize_text_field($_POST['numero_transacao']));
  }
  
  if (!empty($_POST['data_pagamento'])) {
      $order->update_meta_data('data_pagamento', sanitize_text_field($_POST['data_pagamento']));
  }
  
  if (!empty($_POST['observacoes_pedido'])) {
      $order->update_meta_data('observacoes_pedido', sanitize_textarea_field($_POST['observacoes_pedido']));
  }
  
  // CORREÇÃO: Lógica para salvar os IDs das receitas corretamente
  $receitas_str = '';
  if ( ! empty( $_POST['selected_receitas'] ) ) {
      error_log('[DEBUG PHP] criar_pedido_frontend_callback: $_POST[selected_receitas]: ' . print_r($_POST['selected_receitas'], true));
      
      // Se for uma string (vinda do campo hidden), explode em array
      if (is_string($_POST['selected_receitas'])) {
          $receita_ids = array_filter(array_map('intval', explode(',', $_POST['selected_receitas'])));
      } else {
          // Se for array (vinda dos checkboxes), processa diretamente
          $receita_ids = array_map( 'intval', (array) $_POST['selected_receitas'] );
      }
      
      // Remove valores vazios e converte em string
      $receita_ids = array_filter($receita_ids);
      if (!empty($receita_ids)) {
          $receitas_str = implode( ',', $receita_ids );
      }
      
      error_log('[DEBUG PHP] criar_pedido_frontend_callback: $receitas_str to save: ' . $receitas_str);
  } else {
      error_log('[DEBUG PHP] criar_pedido_frontend_callback: $_POST[selected_receitas] is empty or not set.');
  }
  
  // Salva a string no metadado do pedido (mesmo que vazia)
  $order->update_meta_data( 'selected_receitas', $receitas_str );

  // 11) Recalcula tudo (incluindo fees)
  $order->calculate_totals( true );
  
  // Adicionar telefone se fornecido
  if (isset($_POST['billing_phone']) && !empty($_POST['billing_phone'])) {
      $order->set_billing_phone( sanitize_text_field( $_POST['billing_phone'] ) );
  }
  
  $order_id = $order->save();
  
  if (!$order_id) {
      wp_send_json_error(array('message' => 'Erro ao salvar o pedido.'));
      return;
  }


    // Depois de $order->save();
    $page_id     = 1719; // ID da página "Pagar Pedido"
    $page_url    = get_permalink( $page_id );
    $payment_url = add_query_arg( array(
    'order_id'  => $order->get_id(),
    'order_key' => $order->get_order_key(),
    ), $page_url );

    wp_send_json_success( array(
    'message'     => 'Pedido criado com sucesso!',
    //'payment_url' => $payment_url
    ) );


}

// AJAX para buscar receitas do paciente (HANDLER PRINCIPAL)
add_action('wp_ajax_get_receitas_paciente', 'ajax_get_receitas_paciente');
add_action('wp_ajax_nopriv_get_receitas_paciente', 'ajax_get_receitas_paciente');
function ajax_get_receitas_paciente() {
    // LOG DETALHADO PARA DEBUG
    error_log('=== [DEBUG] ajax_get_receitas_paciente chamado ===');
    error_log('[DEBUG] _POST: ' . print_r($_POST, true));
    error_log('[DEBUG] security recebido: ' . (isset($_POST['security']) ? $_POST['security'] : 'NÃO ENVIADO'));
    error_log('[DEBUG] wp_verify_nonce criar_pedido_nonce: ' . (isset($_POST['security']) ? (wp_verify_nonce($_POST['security'], 'criar_pedido_nonce') ? 'OK' : 'FALHOU') : 'N/A'));
    error_log('[DEBUG] wp_verify_nonce get_receitas_nonce: ' . (isset($_POST['security']) ? (wp_verify_nonce($_POST['security'], 'get_receitas_nonce') ? 'OK' : 'FALHOU') : 'N/A'));
    // segurança - aceita tanto o nonce de criar quanto o genérico de receitas
    $nonce_valid = false;
    if (isset($_POST['security'])) {
        if (wp_verify_nonce($_POST['security'], 'criar_pedido_nonce') || 
            wp_verify_nonce($_POST['security'], 'get_receitas_nonce')) {
            $nonce_valid = true;
        }
    }
    
    if (!$nonce_valid) {
        error_log('RECEITAS: Nonce inválido - Security: ' . ($_POST['security'] ?? 'NOT_SET'));
        echo '<p class="text-red-500 text-sm">Erro de segurança ao carregar receitas.</p>';
        wp_die();
    }

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    if ( ! $user_id ) {
        echo '<p class="text-gray-500 text-sm">Selecione um associado para visualizar as receitas.</p>';
        wp_die();
    }
    
    // Verificar se o usuário existe
    $user = get_userdata($user_id);
    if (!$user) {
        echo '<p class="text-gray-500 text-sm">Usuário não encontrado.</p>';
        wp_die();
    }

    // consulta receitas do paciente - limitado às 4 mais recentes
    $args = [
      'post_type'      => 'receitas',
      'posts_per_page' => 4,
      'meta_query'     => [[
         'key'     => 'id_paciente_receita',
         'value'   => $user_id,
         'compare' => '=',
         'type'    => 'NUMERIC',
      ]],
      'orderby' => 'date',
      'order'   => 'DESC'
    ];
    $receitas = get_posts($args);

    // IDs já salvos (campo ACF) - This part is for editing existing orders, not new ones.
    // For new orders, this should effectively be empty.
    $idsReceitasSalvas = get_post_meta($_POST['order_id'] ?? 0, 'selected_receitas', true); // Assuming order_id might be passed for context
    $arrayIdsSalvos    = $idsReceitasSalvas
                           ? explode(',', $idsReceitasSalvas)
                           : [];

    // monta o HTML de volta (VISUAL IDÊNTICO AO EDITAR-PEDIDO)
    if ($receitas) {
        echo '<div class="space-y-2">';
        foreach ($receitas as $r) {
            $idR = $r->ID;
            $t = get_the_title($idR);
            $ven = get_field('data_vencimento', $idR);
            $arquivo_receita_url = get_field('arquivo_receita', $idR);
            $arquivo_laudo_url = get_field('arquivo_laudo', $idR);
            $chec = in_array($idR, $arrayIdsSalvos);
            
            echo '<div class="p-2 border border-gray-200 rounded-md">';
            echo '  <div class="flex items-center justify-between">';
            echo '    <label class="flex items-center space-x-2">';
            echo '      <input type="checkbox" name="selected_receitas[]" value="' . esc_attr($idR) . '" class="form-checkbox rounded border border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"' . ($chec ? ' checked' : '') . '>';
            echo '      <span class="text-sm font-medium text-gray-800">' . esc_html($t) . '</span>';
            echo '    </label>';
            echo '    <div class="flex items-center space-x-2">';
            if ($arquivo_receita_url) {
                echo '<a href="' . esc_url($arquivo_receita_url) . '" target="_blank" class="text-xs text-green-600 hover:underline">Ver Receita</a>';
            }
            if ($arquivo_laudo_url) {
                echo '<a href="' . esc_url($arquivo_laudo_url) . '" target="_blank" class="text-xs text-blue-600 hover:underline">Ver Laudo</a>';
            }
            echo '    </div>';
            echo '  </div>';
            if ($ven) {
                echo '<div class="text-xs text-gray-500 mt-1 ml-6">Vencimento: ' . esc_html($ven) . '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-gray-500 text-sm">Nenhuma receita encontrada para este paciente.</p>';
    }
    wp_die();
}




// botao de pagar woo

/**
 * Retorna o HTML do botão "Pagar" para um pedido.
 *
 * @param WC_Order $order     Objeto do pedido.
 * @param int      $page_id   ID da página que usa o template "Pagar Pedido".
 * @return string             HTML do botão/link.
 */
function get_pay_order_button( WC_Order $order ) {
    if ( $order->is_paid() ) {
        return '<span class="text-green-600 font-medium">Pago</span>';
    }

    $url = add_query_arg(
        [
            'order_id'  => $order->get_id(),
            'order_key' => $order->get_order_key(),
        ],
        get_permalink( get_page_by_path( 'pagar-pedido' )->ID ) // ou ID fixo
    );

    return sprintf(
        '<a href="%1$s"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm
                   font-medium px-4 py-2 rounded transition">
           Pagar Pedido #%2$d
         </a>',
        esc_url( $url ),
        absint( $order->get_id() )
    );
}


// Atualiza o status do pedido para 'completed' automaticamente após o pagamento bem-sucedido
/*
add_action( 'woocommerce_payment_complete', function ( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order->has_status( ['completed', 'cancelled'] ) ) {
        $order->update_status( 'completed', 'Pagamento confirmado automaticamente.' );
    }
} );
*/
// Hook para o início do checkout no WooCommerce
add_action( 'woocommerce_checkout_init', 'handle_duplicate_order_check', 5 );

/**
 * Função para verificar e redirecionar pedidos duplicados ou inválidos.
 *
 * @param WC_Checkout $checkout O objeto de checkout do WooCommerce.
 */
function handle_duplicate_order_check( $checkout ) {
    // Verifica se a chave do pedido está presente na URL
    if ( isset( $_GET['key'] ) ) {
        // Sanitiza a chave do pedido para segurança
        $order_key = sanitize_text_field( $_GET['key'] );
        
        // Tenta obter o ID do pedido pela chave
        $order_id = wc_get_order_id_by_order_key( $order_key );
        
        // Obtém o objeto do pedido
        $order = wc_get_order( $order_id );

        // Verifica se o pedido não existe ou se não está no status 'pending'
        if ( ! $order || ! $order->has_status( 'pending' ) ) {
            // Mensagem de erro a ser exibida
            $error_message = 'Este pedido já foi pago ou é inválido/Não existe.';

            // URL da página de erro que você criou no Passo 1
            // Substitua 'erro-no-pedido' pelo slug da sua página
            $error_page_url = home_url( '/erro-no-pedido/' );

            // Adiciona a mensagem de erro como um parâmetro na URL
            // Usamos urlencode para garantir que a mensagem seja formatada corretamente na URL
            $redirect_url = add_query_arg( 'msg', urlencode( $error_message ), $error_page_url );

            // Redireciona o usuário para a página de erro
            wp_redirect( $redirect_url );
            exit; // Importante para parar a execução do script após o redirecionamento
        }
    }
}

// Hook para adicionar um shortcode que exibirá a mensagem de erro na página
add_shortcode( 'mensagem_erro_pedido', 'display_order_error_message' );

/**
 * Função do shortcode para exibir a mensagem de erro do pedido.
 *
 * @return string A mensagem de erro formatada, ou uma string vazia se não houver mensagem.
 */
function display_order_error_message() {
    // Verifica se a mensagem de erro está presente nos parâmetros da URL
    if ( isset( $_GET['msg'] ) ) {
        // Decodifica a mensagem (se ela foi urlencoded) e a sanitiza
        $message = sanitize_text_field( urldecode( $_GET['msg'] ) );
        
        // Retorna a mensagem dentro de uma div para estilização
        return '<div class="woocommerce-error" style="text-align: center; padding: 20px; background-color: #ffebe8; border: 1px solid #c00; color: #c00; margin: 20px auto; max-width: 600px;">' . esc_html( $message ) . '</div>';
    }
    return ''; // Retorna vazio se não houver mensagem
}

/**
 * Permite pagar um pedido mesmo estando deslogado.
 * Baseado em https://businessbloomer.com/woocommerce-allow-to-pay-for-order-without-login/
 */
add_filter( 'user_has_cap', 'liberar_pagamento_sem_login', 9999, 3 );
function liberar_pagamento_sem_login( $allcaps, $caps, $args ) {

    // Só altera quando a capacidade checada for "pay_for_order"
    if ( isset( $caps[0], $_GET['key'] ) && $caps[0] === 'pay_for_order' ) {
        // Se a URL possui order_key válida, libera o acesso
        $allcaps['pay_for_order'] = true;
    }

    return $allcaps;
}

/**
 * Remove a exigência de "verificar e‑mail" para pedidos de hóspedes.
 */
add_filter( 'woocommerce_order_email_verification_required', '__return_false', 9999 );


// redireciona apos pagamento
add_action( 'template_redirect', function() {
    if ( is_wc_endpoint_url( 'order-pay' ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        global $wp;
        $order_id  = intval( $wp->query_vars['order-pay'] );
        $order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
        $order     = wc_get_order( $order_id );

        if ( $order
            && $order->get_order_key() === $order_key
            && in_array( $order->get_status(), ['pending', 'failed', 'on-hold'], true )
        ) {
            wp_safe_redirect(
                home_url('/pedidos-processando-pagamento/') .
                '?order_id=' . $order_id .
                '&order_key=' . urlencode( $order_key )
            );
            exit;
        }
    }
});

// FUNÇÃO AJAX PARA EDITAR PEDIDOS WOOCOMMERCE

add_action('wp_ajax_editar_pedido_frontend','editar_pedido_frontend_callback');
add_action('wp_ajax_nopriv_editar_pedido_frontend','editar_pedido_frontend_callback');
function editar_pedido_frontend_callback() {
    error_log('EDITAR_PEDIDO _POST: ' . print_r($_POST, true));
    error_log('EDITAR_PEDIDO _FILES: ' . print_r($_FILES, true));
    
    // Verificar nonce
    if (!isset($_POST['editar_pedido_nonce_field']) || !wp_verify_nonce($_POST['editar_pedido_nonce_field'], 'editar_pedido_nonce')) {
        error_log('EDITAR_PEDIDO: Nonce inválido - Field: ' . ($_POST['editar_pedido_nonce_field'] ?? 'NOT_SET'));
        wp_send_json_error(array('message' => 'Erro de segurança: Nonce inválido.'));
        return;
    }
    
    error_log('EDITAR_PEDIDO: Nonce válido, continuando...');

    // 1) Obter dados do formulário
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $produtos_json = isset($_POST['produtos_json']) ? sanitize_text_field($_POST['produtos_json']) : '';
    $produtos = json_decode(stripslashes($produtos_json), true);
    $user_id = isset($_POST['associado']) ? intval($_POST['associado']) : 0;
    
    // Validações básicas
    if ($order_id <= 0) {
        wp_send_json_error(array('message' => 'ID do pedido inválido.'));
        return;
    }
    
    if (empty($produtos) || !is_array($produtos)) {
        wp_send_json_error(array('message' => 'Adicione pelo menos um produto ao pedido.'));
        return;
    }
    
    if ($user_id <= 0) {
        wp_send_json_error(array('message' => 'Selecione um associado válido.'));
        return;
    }

    // 2) Verificar se o pedido existe
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error(array('message' => 'Pedido não encontrado.'));
        return;
    }

    // 3) Desconto, extra e frete
    $desconto = isset($_POST['pedido_discount'])
                 ? floatval(str_replace(',', '.', $_POST['pedido_discount']))
                 : 0;
    $extra = isset($_POST['pedido_extra'])
              ? floatval(str_replace(',', '.', $_POST['pedido_extra']))
              : 0;
    $frete = isset($_POST['pedido_frete'])
              ? floatval(str_replace(',', '.', $_POST['pedido_frete']))
              : 0;

    // 4) Atualizar cliente do pedido
    $order->set_customer_id($user_id);

    // 5) Buscar dados do usuário para endereço
    $user = get_userdata($user_id);
    $first = sanitize_text_field($user->first_name);
    $last = sanitize_text_field($user->last_name);

    // 6) Montar array de endereço
    $shipping = [
        'first_name' => $first,
        'last_name'  => $last,
        'address_1'  => sanitize_text_field($_POST['shipping_address_1']),
        'address_2'  => sanitize_text_field($_POST['shipping_address_2']),
        'city'       => sanitize_text_field($_POST['shipping_city']),
        'postcode'   => sanitize_text_field($_POST['shipping_postcode']),
        'country'    => 'BR',
        'state'      => sanitize_text_field($_POST['shipping_state']),
    ];

    // 7) Atualizar endereços de billing e shipping
    $order->set_address($shipping, 'billing');
    $order->set_address($shipping, 'shipping');

    // 8) Remover todos os itens existentes do pedido
    foreach ($order->get_items() as $item_id => $item) {
        $order->remove_item($item_id);
    }

    // 9) Remover todas as taxas existentes
    foreach ($order->get_fees() as $fee_id => $fee) {
        $order->remove_item($fee_id);
    }

    // 10) Adicionar novos produtos
    if (!empty($produtos) && is_array($produtos)) {
        foreach ($produtos as $p) {
            if (isset($p['produto_id']) && isset($p['quantidade'])) {
                $product_id = intval($p['produto_id']);
                $quantity = intval($p['quantidade']);
                
                if ($product_id > 0 && $quantity > 0) {
                    $product = wc_get_product($product_id);
                    if (!$product) {
                        wp_send_json_error(array('message' => 'Produto não encontrado: ID ' . $product_id));
                        return;
                    }
                    $order->add_product($product, $quantity);
                } else {
                    wp_send_json_error(array('message' => 'Dados de produto inválidos.'));
                    return;
                }
            }
        }
    }

    // 11) Adicionar taxas (desconto, extra, frete)
    if ($desconto > 0) {
        $fee = new WC_Order_Item_Fee();
        $fee->set_name('Desconto');
        $fee->set_amount(-$desconto);
        $fee->set_total(-$desconto);
        $fee->set_tax_status('none');
        $order->add_item($fee);
    }

    if ($extra > 0) {
        $fee = new WC_Order_Item_Fee();
        $fee->set_name('Extra Cartão');
        $fee->set_amount($extra);
        $fee->set_total($extra);
        $fee->set_tax_status('none');
        $order->add_item($fee);
    }

    if ($frete > 0) {
        $fee = new WC_Order_Item_Fee();
        $fee->set_name('Frete');
        $fee->set_amount($frete);
        $fee->set_total($frete);
        $fee->set_tax_status('none');
        $order->add_item($fee);
    }

    // 12) Atualizar metadados customizados
    if (!empty($_POST['custom_delivery_address'])) {
        $order->update_meta_data(
            'custom_delivery_address',
            sanitize_textarea_field($_POST['custom_delivery_address'])
        );
    } else {
        $order->delete_meta_data('custom_delivery_address');
    }

    // CORREÇÃO: Lógica para salvar os IDs das receitas corretamente na edição
    $receitas_str = '';
    if ( ! empty( $_POST['selected_receitas'] ) ) {
        error_log('[DEBUG PHP] editar_pedido_frontend_callback: $_POST[selected_receitas]: ' . print_r($_POST['selected_receitas'], true));
        
        // Se for uma string (vinda do campo hidden), explode em array
        if (is_string($_POST['selected_receitas'])) {
            $receita_ids = array_filter(array_map('intval', explode(',', $_POST['selected_receitas'])));
        } else {
            // Se for array (vinda dos checkboxes), processa diretamente
            $receita_ids = array_map( 'intval', (array) $_POST['selected_receitas'] );
        }
        
        // Remove valores vazios e converte em string
        $receita_ids = array_filter($receita_ids);
        if (!empty($receita_ids)) {
            $receitas_str = implode( ',', $receita_ids );
        }
        
        error_log('[DEBUG PHP] editar_pedido_frontend_callback: $receitas_str to save: ' . $receitas_str);
    } else {
        error_log('[DEBUG PHP] editar_pedido_frontend_callback: $_POST[selected_receitas] is empty or not set.');
    }
    
    // Salva a string no metadado do pedido (mesmo que vazia)
    $order->update_meta_data( 'selected_receitas', $receitas_str );

    // 13) Atualizar telefone
    if ( isset( $_POST['billing_phone'] ) ) {
        $order->set_billing_phone( sanitize_text_field( $_POST['billing_phone'] ) );
    }

$order->update_meta_data('numero_transacao', sanitize_text_field($_POST['numero_transacao']));
$order->update_meta_data('data_pagamento', sanitize_text_field($_POST['data_pagamento']));
$order->update_meta_data('observacoes_pedido', sanitize_textarea_field($_POST['observacoes_pedido'])); 
// Processar comprovante com logs detalhados
error_log('[EDITAR] Comprovante ID recebido: ' . ($_POST['comprovante_id'] ?? 'VAZIO'));
error_log('[EDITAR] Comprovante URL recebido: ' . ($_POST['comprovante_url'] ?? 'VAZIO'));

if (isset($_POST['comprovante_id']) && !empty($_POST['comprovante_id'])) {
    $comprovante_id_value = sanitize_text_field($_POST['comprovante_id']);
    $order->update_meta_data('comprovante_id', $comprovante_id_value);
    error_log('[EDITAR] Comprovante ID SALVO: ' . $comprovante_id_value);
}
if (isset($_POST['comprovante_url']) && !empty($_POST['comprovante_url'])) {
    $comprovante_url_value = sanitize_text_field($_POST['comprovante_url']);
    $order->update_meta_data('comprovante_url', $comprovante_url_value);
    error_log('[EDITAR] Comprovante URL SALVO: ' . $comprovante_url_value);
}
    // 14) Recalcular totais
    $order->calculate_totals(true);

    // 15) Salvar o pedido
    $saved_order_id = $order->save();
    
    if (!$saved_order_id) {
        wp_send_json_error(array('message' => 'Erro ao salvar as alterações do pedido.'));
        return;
    }

    // 16) Adicionar extras ao pedido
    //$order->add_order_note('Pedido atualizado via formulário frontend.');
   

    // 17) Retornar sucesso
    wp_send_json_success(array(
        'message' => 'Pedido #' . $order_id . ' atualizado com sucesso!',
        'order_id' => $order_id,
        'new_total' => wc_price($order->get_total())
    ));
}


add_action('wp_ajax_upload_comprovante', 'upload_comprovante_callback');
function upload_comprovante_callback() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'editar_pedido_nonce')) {
        wp_send_json_error('Erro de segurança');
        return;
    }
    
    if (isset($_FILES['file'])) {
        $attachment_id = upload_file_to_wordpress($_FILES['file']);
        if ($attachment_id) {
            wp_send_json_success(array(
                'attachment_id' => $attachment_id,
                'url' => wp_get_attachment_url($attachment_id)
            ));
        }
    }
    wp_send_json_error('Erro no upload');
}

// FUNÇÃO AJAX PARA BUSCAR RECEITAS DO PACIENTE (PARA EDIÇÃO)
add_action('wp_ajax_get_receitas_paciente_edit', 'ajax_get_receitas_paciente_edit');
add_action('wp_ajax_nopriv_get_receitas_paciente_edit', 'ajax_get_receitas_paciente_edit');
function ajax_get_receitas_paciente_edit() {
    // Segurança - aceita tanto o nonce de editar quanto o genérico de receitas
    $nonce_valid = false;
    if (isset($_POST['security'])) {
        if (wp_verify_nonce($_POST['security'], 'editar_pedido_nonce') || 
            wp_verify_nonce($_POST['security'], 'get_receitas_nonce')) {
            $nonce_valid = true;
        }
    }
    
    if (!$nonce_valid) {
        error_log('RECEITAS EDIT: Nonce inválido - Security: ' . ($_POST['security'] ?? 'NOT_SET'));
        wp_send_json_error('Nonce inválido');
        return;
    }

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    
    if (!$user_id) {
        echo '<p class="text-gray-500 text-sm">Selecione um associado para visualizar as receitas.</p>';
        wp_die();
    }
    
    // Verificar se o usuário existe
    $user = get_userdata($user_id);
    if (!$user) {
        echo '<p class="text-gray-500 text-sm">Usuário não encontrado.</p>';
        wp_die();
    }

    // Buscar receitas salvas no pedido
    $receitas_salvas = '';
    if ($order_id) {
        $order = wc_get_order($order_id);
        if ($order) {
            $receitas_salvas = $order->get_meta('selected_receitas');
        }
    }

    // Consulta receitas do paciente - limitado às 4 mais recentes
    $args = [
        'post_type'      => 'receitas',
        'posts_per_page' => 4,
        'meta_query'     => [[
            'key'     => 'id_paciente_receita',
            'value'   => $user_id,
            'compare' => '=',
            'type'    => 'NUMERIC',
        ]],
        'orderby' => 'date',
        'order'   => 'DESC'
    ];
    $receitas = get_posts($args);

    // IDs já salvos
    $arrayIdsSalvos = $receitas_salvas ? explode(',', $receitas_salvas) : [];

    // Montar HTML (VISUAL IDÊNTICO AO NOVO-PEDIDO)
    if ($receitas) {
        echo '<div class="space-y-2">';
        foreach ($receitas as $r) {
            $idR = $r->ID;
            $t = get_the_title($idR);
            $ven = get_field('data_vencimento', $idR);
            $arquivo_receita_url = get_field('arquivo_receita', $idR);
            $arquivo_laudo_url = get_field('arquivo_laudo', $idR);
            $chec = in_array($idR, $arrayIdsSalvos);
            
            echo '<div class="p-2 border border-gray-200 rounded-md">';
            echo '  <div class="flex items-center justify-between">';
            echo '    <label class="flex items-center space-x-2">';
            echo '      <input type="checkbox" name="selected_receitas[]" value="' . esc_attr($idR) . '" class="form-checkbox rounded border border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"' . ($chec ? ' checked' : '') . '>';
            echo '      <span class="text-sm font-medium text-gray-800">' . esc_html($t) . '</span>';
            echo '    </label>';
            echo '    <div class="flex items-center space-x-2">';
            if ($arquivo_receita_url) {
                echo '<a href="' . esc_url($arquivo_receita_url) . '" target="_blank" class="text-xs text-green-600 hover:underline">Ver Receita</a>';
            }
            if ($arquivo_laudo_url) {
                echo '<a href="' . esc_url($arquivo_laudo_url) . '" target="_blank" class="text-xs text-blue-600 hover:underline">Ver Laudo</a>';
            }
            echo '    </div>';
            echo '  </div>';
            if ($ven) {
                echo '<div class="text-xs text-gray-500 mt-1 ml-6">Vencimento: ' . esc_html($ven) . '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-gray-500 text-sm">Nenhuma receita encontrada para este paciente.</p>';
    }

    wp_die();
}

// FUNÇÃO PARA OBTER DADOS DO PRODUTO VIA AJAX (PARA CÁLCULO DE PREÇOS)
add_action('wp_ajax_get_product_data', 'ajax_get_product_data');
add_action('wp_ajax_nopriv_get_product_data', 'ajax_get_product_data');
function ajax_get_product_data() {
    $product_id = intval($_POST['product_id']);
    
    if (!$product_id) {
        wp_send_json_error('ID do produto inválido');
        return;
    }

    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Produto não encontrado');
        return;
    }

    wp_send_json_success(array(
        'id' => $product->get_id(),
        'name' => $product->get_name(),
        'price' => $product->get_price(),
        'price_formatted' => wc_price($product->get_price())
    ));
}

// todos os pedidos

/**
 * Retorna a URL para a página de edição de pedidos do frontend.
 *
 * Esta função busca a página que utiliza o template 'dashboard-editar-pedido.php'
 * e constrói a URL de edição para um ID de pedido específico. É uma abordagem
 * mais robusta do que usar get_page_by_path(), pois não depende do slug da página.
 *
 * @param int $order_id O ID do pedido do WooCommerce.
 * @return string A URL completa para a página de edição, ou a home URL com o parâmetro como fallback.
 */
function amedis_get_edit_order_url($order_id) {
    return home_url('/editar-pedido/?order_id=' . $order_id);
}

/**
 * Normaliza um valor de meta de pedido:
 * – Gera slugs de todas as chaves do map.
 * – Slugifica o valor bruto antes de buscar no map.
 *
 * @param string $raw Valor bruto vindo do post_meta.
 * @param array  $map Array associativo [ raw_key => valor_destino ].
 * @return mixed Valor padronizado do map, ou null se não encontrado.
 */
function hg_normalize_order_meta_value( $raw, array $map ) {
    if ( empty( $raw ) ) {
        return null;
    }

    // 1) Constrói um novo mapa com chaves slugificadas
    $slugified_map = [];
    foreach ( $map as $raw_key => $dest ) {
        // Gera slug (minusculo, sem acentos, espaços → hífen)
        $slug = sanitize_title( (string) $raw_key );
        $slugified_map[ $slug ] = $dest;
    }

    // 2) Slugifica o valor bruto para fazer lookup
    $raw_slug = sanitize_title( (string) $raw );

    // 3) Retorna o valor correspondente ou null
    return $slugified_map[ $raw_slug ] ?? null;
}



/**
 * Função auxiliar para obter dados formatados de um pedido para exibição no dashboard.
 *
 * @param mixed $order O objeto que deve ser um WC_Order.
 * @return array Um array associativo com os dados do pedido.
 */
function get_order_display_data($order) {
    // Verificar se o objeto é realmente um WC_Order e não um refund ou outro tipo
    if (!is_object($order)) {
        error_log('[DEBUG] get_order_display_data: Não é um objeto - ' . gettype($order));
        return array();
    }
    
    $class_name = get_class($order);
    
    // Verifica se é um WC_Order válido (não refund, não outros tipos)
    if (!($order instanceof WC_Order) || 
        (class_exists('WC_Order_Refund') && $order instanceof WC_Order_Refund) ||
        strpos($class_name, 'Refund') !== false) {
        error_log('[DEBUG] get_order_display_data: Objeto inválido recebido - ' . $class_name);
        return array(); // Retorna array vazio para objetos inválidos
    }
    
    // Verificação adicional: se o método get_type() existe e retorna algo diferente de 'shop_order'
    if (method_exists($order, 'get_type') && $order->get_type() !== 'shop_order') {
        error_log('[DEBUG] get_order_display_data: Tipo de pedido inválido - ' . $order->get_type());
        return array();
    }
    
    global $_amedis_recipes_global_cache;
    // 0. DEFINIÇÃO DOS MAPAS DE NORMALIZAÇÃO
    // Mapeia todos os valores possíveis (antigos e novos) para uma única chave canônica.
    $forma_pagamento_map = [
        'gatewaypix'   => 'gatewaypix',
        'gatewaycard'  => 'gatewaycard',
        'dinheiro'     => 'dinheiro',
        'pix'          => 'pix',
        'pgtodividido' => 'pgtodividido',
        // --- Legado ---
        'GATEWAY PIX'        => 'gatewaypix',
        'GATEWAY CARTÃO'     => 'gatewaycard',
        'Dinheiro'           => 'dinheiro',
        'PIX'                => 'pix',
        'Pagamento Dividido' => 'pgtodividido',
    ];
    $forma_entrega_map = [
        'pabloPaciente' => 'pabloPaciente',
        'pabloCorreios' => 'pabloCorreios',
        'felipe'        => 'felipe',
        'uber'          => 'uber',
        'valentino'     => 'valentino',
        'cezar'         => 'cezar',
        'correios'      => 'correios',
        // --- Legado ---
        'Pablo - Paciente'      => 'pabloPaciente',
        'Pablo - Correios'      => 'pabloCorreios',
        'Felipe'                => 'felipe',
        'Uber'                  => 'uber',
        'Valentino'             => 'valentino',
        'Cezar Colômbia'        => 'cezar',
        'Entregador + Correios' => 'correios',
    ];
    $status_entrega_map = [
        'pendente' => 'pendente',
        'enviado'  => 'enviado',
        'entregue' => 'entregue',
        // --- Legado ---
        'Pendente' => 'pendente',
        'Enviado'  => 'enviado',
        'Entregue' => 'entregue',
    ];
    $extracao_map = [
        'informado'  => 'informado',
        'produzindo' => 'produzindo',
        'pronto'     => 'pronto',
        // --- Legado ---
        'Informado'           => 'informado',
        'Produzindo'          => 'produzindo',
        'Pronto para entrega' => 'pronto',
    ];

    // 1. DADOS BÁSICOS DO PEDIDO
    $order_id           = $order->get_id();
    $order_date_created = $order->get_date_created();
    $order_date         = $order_date_created ? $order_date_created->date('d/m/Y') : 'N/A';
    $order_date_sortable = $order_date_created ? $order_date_created->getTimestamp() : 0;
    $order_status_slug  = $order->get_status(); // 'pending', 'processing', etc.
    $order_total        = $order->get_total();
    $formatted_total    = $order->get_formatted_order_total();
    $pay_url            = $order->get_checkout_payment_url();
    $is_paid            = $order->is_paid();

    // 2. DADOS DO CLIENTE
    $customer_id       = $order->get_customer_id();
    $customer_name     = $order->get_formatted_billing_full_name();
    $billing_phone     = $order->get_billing_phone() ?: ''; // Garante que a variável seja sempre uma string

    // 3. CUSTOM META DO PEDIDO (LENDO VALOR BRUTO E NORMALIZANDO)
    $ids_receitas_str    = get_post_meta($order_id, 'selected_receitas', true);
    
    $raw_forma_pagamento = get_post_meta($order_id, '_forma_pagamento_woo', true);
    $raw_forma_entrega   = get_post_meta($order_id, '_forma_entrega_woo', true);
    $raw_status_entrega  = get_post_meta($order_id, '_status_entrega', true);
    $raw_extracao        = get_post_meta($order_id, '_extracao', true);

    $forma_pagamento_woo = hg_normalize_order_meta_value($raw_forma_pagamento, $forma_pagamento_map);
    $forma_entrega_woo   = hg_normalize_order_meta_value($raw_forma_entrega, $forma_entrega_map);
    $status_entrega      = hg_normalize_order_meta_value($raw_status_entrega, $status_entrega_map);
    $extracao            = hg_normalize_order_meta_value($raw_extracao, $extracao_map);
    
    // 4. DADOS DO USUÁRIO (se logado) e TIPOS DE ASSOCIAÇÃO
    $tipo_associacao = '';
    $text_tipo_assoc = 'Colaborador';
    $bg_badge        = 'bg-blue-50';
    $txt_color       = 'text-blue-600';
    $diagnostico     = '';
    $nome_completo_respon = '';
    $telefone_usuario_acf = '';
    $usa_medicacao = '';
    $qual_medicacao = '';
    $fez_uso_canabis_escolha = '';
    $observacoes_user = '';

    if ($customer_id > 0) {
        // Usar cache para user_meta para evitar consultas repetidas se o mesmo usuário tiver múltiplos pedidos
        $user_meta_cache_key = 'order_dashboard_user_meta_' . $customer_id;
        $user_meta_cached = wp_cache_get($user_meta_cache_key, 'user_meta');

        if (false === $user_meta_cached) {
            $user_info = get_userdata($customer_id);
            if ($user_info) {
                $tipo_associacao = get_user_meta($customer_id, 'tipo_associacao', true);
                $diagnostico = get_user_meta($customer_id, 'diagnostico', true);
                $nome_completo_respon = get_user_meta($customer_id, 'nome_completo_respon', true);
                $telefone_usuario_acf = get_user_meta($customer_id, 'telefone', true); // Busca o telefone do campo ACF 'telefone'
                $usa_medicacao = get_user_meta($customer_id, 'usa_medicacao', true);
                $qual_medicacao = get_user_meta($customer_id, 'qual_medicacao', true);
                $fez_uso_canabis_escolha = get_user_meta($customer_id, 'fez_uso_canabis_escolha', true);
                $observacoes_user = get_user_meta($customer_id, 'observacoes', true);
            }
            $user_meta_cached = [
                'tipo_associacao'           => $tipo_associacao,
                'diagnostico'               => $diagnostico,
                'nome_completo_respon'      => $nome_completo_respon,
                'telefone_usuario_acf'      => $telefone_usuario_acf,
                'usa_medicacao'             => $usa_medicacao,
                'qual_medicacao'            => $qual_medicacao,
                'fez_uso_canabis_escolha'   => $fez_uso_canabis_escolha,
                'observacoes_user'          => $observacoes_user,
            ];
            wp_cache_set($user_meta_cache_key, $user_meta_cached, 'user_meta', HOUR_IN_SECONDS); // Cache por 1 hora
        } else {
            extract($user_meta_cached); // Extrai as variáveis do cache
        }
    }

    // Prioriza o telefone do ACF do usuário, usa o do faturamento do pedido como fallback
    $final_phone = !empty($telefone_usuario_acf) ? $telefone_usuario_acf : $billing_phone;
    $telefone_formatado = !empty($final_phone) ? preg_replace('/[^0-9]/', '', $final_phone) : '';
    $mensagem_whatsapp  = urlencode("Olá, sobre o pedido #" . $order_id);

    // Definir tipo de associação e classes de badge
    switch ($tipo_associacao) {
        case 'assoc_paciente':
            $text_tipo_assoc = "Paciente";
            $bg_badge = "bg-sky-50";
            $txt_color = "text-sky-600";
            break;
        case 'assoc_respon':
            $text_tipo_assoc = "Responsável";
            $bg_badge = "bg-purple-50";
            $txt_color = "text-purple-600";
            break;
        case 'assoc_tutor':
            $text_tipo_assoc = "Tutor de Animal";
            $bg_badge = "bg-pink-50";
            $txt_color = "text-pink-600";
            break;
        default:
            // Já definido como 'Cliente' e suas cores padrão
            break;
    }

    // 5. LÓGICA DE APRESENTAÇÃO (BADGES, ETC) - STATUS WOOCOMMERCE
    $status_classes = '';
    $status_text = '';
    $status_dot_color = '';
    $row_bg_class = 'bg-white';

    switch ($order_status_slug) {
        case 'pending':
            $status_classes = 'bg-yellow-100 text-yellow-800';
            $status_text = 'Pgto. Pendente';
            $status_dot_color = 'bg-yellow-500';
            $row_bg_class = 'bg-yellow-50/50';
            break;
        case 'processing':
            $status_classes = 'bg-blue-100 text-blue-800';
            $status_text = 'Processando';
            $status_dot_color = 'bg-blue-500';
            $row_bg_class = 'bg-blue-50/50';
            break;
        case 'on-hold':
            $status_classes = 'bg-orange-100 text-orange-800';
            $status_text = 'Aguardando';
            $status_dot_color = 'bg-orange-500';
            $row_bg_class = 'bg-orange-50/50';
            break;
        case 'completed':
            $status_classes = 'bg-green-100 text-green-800';
            $status_text = 'Concluído';
            $status_dot_color = 'bg-green-500';
            break;
        case 'cancelled':
            $status_classes = 'bg-red-100 text-red-800';
            $status_text = 'Cancelado';
            $status_dot_color = 'bg-red-500';
            $row_bg_class = 'bg-red-50/50';
            break;
        case 'refunded':
            $status_classes = 'bg-purple-100 text-purple-800';
            $status_text = 'Reembolsado';
            $status_dot_color = 'bg-purple-500';
            $row_bg_class = 'bg-purple-50/50';
            break;
        case 'failed':
            $status_classes = 'bg-red-200 text-red-900';
            $status_text = 'Falhou';
            $status_dot_color = 'bg-red-700';
            $row_bg_class = 'bg-red-100';
            break;
        default:
            $status_classes = 'bg-gray-100 text-gray-800';
            $status_text = ucfirst($order_status_slug);
            $status_dot_color = 'bg-gray-400';
            break;
    }

    // 6. CLASSES PARA O STATUS DE ENTREGA CUSTOMIZADO (LÓGICA LEGADA, MANTIDA PARA CONSISTÊNCIA)
    $entrega_badge_class = 'bg-gray-100 text-gray-800';
    $entrega_text = 'Não Definido';
    // Nota: O valor de 'status_entrega' vem de um select com nomes, mas a lógica abaixo espera 'entregue', 'enviado', etc.
    // Esta lógica pode não ser mais usada visualmente, mas é mantida. O novo badge usará o valor direto do campo.
    if ($status_entrega) {
        switch ($status_entrega) {
            case 'entregue': $entrega_badge_class = 'bg-green-100 text-green-800'; $entrega_text = 'Entregue'; break;
            case 'enviado': $entrega_badge_class = 'bg-blue-100 text-blue-800'; $entrega_text = 'Enviado'; break;
            case 'pendente': $entrega_badge_class = 'bg-red-100 text-red-800'; $entrega_text = 'Pendente'; break;
        }
    }
    
    // 7. BADGES DE STATUS OPERACIONAIS (LÓGICA UNIFICADA)
    $operational_statuses = [];

    // Mapas de opções para obter os rótulos (labels)
    $opcoes_map = [
        '_forma_pagamento_woo' => [
            'gatewaypix'   => 'GATEWAY PIX', 'gatewaycard'  => 'GATEWAY CARTÃO', 'dinheiro'     => 'Dinheiro',
            'pix'          => 'PIX', 'pgtodividido' => 'Pagamento Dividido',
        ],
        '_extracao' => [
            'informado'  => 'Informado', 'produzindo' => 'Produzindo', 'pronto'     => 'Pronto entrega',
        ],
        '_forma_entrega_woo' => [
            'pabloPaciente' => 'Pablo - Paciente', 'pabloCorreios' => 'Pablo - Correios', 'felipe'        => 'Felipe',
            'uber'          => 'Uber', 'valentino'     => 'Valentino', 'cezar'         => 'Cezar Colômbia',
            'correios'      => 'Entregador + Correios',
        ],
        '_status_entrega' => [
            'pendente' => 'Pendente', 'enviado'  => 'Enviado', 'entregue' => 'Entregue',
        ],
    ];

    // Mapa de estilos (cores e ícones) - unificado a partir da função hg_display_meta_badge
    $badge_styles_map = [
        // Pagamento
        'gatewaypix'   => ['classes' => 'bg-blue-100 text-blue-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>'],
        'gatewaycard'  => ['classes' => 'bg-indigo-100 text-indigo-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>'],
        'dinheiro'     => ['classes' => 'bg-green-100 text-green-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75-.75v-.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V7.5c0-.621.504-1.125 1.125-1.125h1.5" /></svg>'],
        'pix'          => ['classes' => 'bg-cyan-100 text-cyan-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5v15a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V4.5A2.25 2.25 0 0 0 18.75 2.25h-15A2.25 2.25 0 0 0 3.75 4.5Z" /></svg>'],
        'pgtodividido' => ['classes' => 'bg-purple-100 text-purple-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" /></svg>'],
        // Entrega
        'pabloPaciente' => ['classes' => 'bg-slate-100 text-slate-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>'],
        'pabloCorreios' => ['classes' => 'bg-slate-200 text-slate-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 0 1 3.375-3.375h9.75a3.375 3.375 0 0 1 3.375 3.375v1.875M3.375 14.25A2.25 2.25 0 0 1 5.625 12h12.75c1.243 0 2.25.996 2.25 2.228v.022a2.25 2.25 0 0 1-2.25 2.25H5.625A2.25 2.25 0 0 1 3.375 14.25Z" /></svg>'],
        'felipe'        => ['classes' => 'bg-sky-100 text-sky-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>'],
        'uber'          => ['classes' => 'bg-gray-200 text-gray-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>'],
        'valentino'     => ['classes' => 'bg-pink-100 text-pink-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>'],
        'cezar'         => ['classes' => 'bg-amber-100 text-amber-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>'],
        'correios'      => ['classes' => 'bg-yellow-100 text-yellow-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>'],
        // Status Entrega
        'pendente' => ['classes' => 'bg-amber-100 text-amber-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'],
        'enviado'  => ['classes' => 'bg-blue-100 text-blue-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>'],
        'entregue' => ['classes' => 'bg-green-100 text-green-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>'],
        // Extração
        'informado'  => ['classes' => 'bg-gray-100 text-gray-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>'],
        'produzindo' => ['classes' => 'bg-orange-100 text-orange-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c.102-.134.22-.263.354-.386a2.25 2.25 0 0 1 3.292 0c.134.123.252.252.354.386m-3.998 0c0 .73.182 1.42.51 2.042m-1.022-.246a2.25 2.25 0 0 0-3.292 0c-.134.123-.252.252-.354.386m3.998 0A2.25 2.25 0 0 1 12 5.25v5.714m0 0a2.25 2.25 0 0 1-1.591 1.591L5 14.5M12 10.964c.54.223 1.053.492 1.508.811l1.962 1.143c.27.157.553.284.847.389M12 10.964V15c0 .73.182 1.42.51 2.042M5 14.5c-1.12 0-2.157-.27-3.134-.73m3.134.73A2.25 2.25 0 0 0 7.25 16.25h9.5A2.25 2.25 0 0 0 19 14.5m-14 0v-2.5a2.25 2.25 0 0 1 2.25-2.25h9.5A2.25 2.25 0 0 1 19 9.75v2.5" /></svg>'],
        'pronto'     => ['classes' => 'bg-teal-100 text-teal-800', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'],
    ];

    // Variáveis que contêm os valores normalizados
    $meta_values = [
        '_forma_pagamento_woo' => $forma_pagamento_woo,
        '_forma_entrega_woo'   => $forma_entrega_woo,
        '_status_entrega'      => $status_entrega,
        '_extracao'            => $extracao,
    ];

    foreach ($meta_values as $meta_key => $value) {
        $raw_value = get_post_meta($order_id, $meta_key, true);
        error_log("[DEBUG STATUS] Pedido $order_id | Meta: $meta_key | Bruto: '$raw_value' | Normalizado: '$value'");
        if (!empty($value)) {
            $label = $opcoes_map[$meta_key][$value] ?? ucfirst($value);
            $style = $badge_styles_map[$value] ?? ['classes' => 'bg-gray-100 text-gray-800', 'icon' => ''];
            error_log("[DEBUG STATUS] Pedido $order_id | Meta: $meta_key | Label final: '$label'");
            $operational_statuses[$meta_key] = [
                'label'   => $label,
                'icon'    => $style['icon'],
                'classes' => $style['classes'],
            ];
        } else if (!empty($raw_value)) {
            // Se o valor normalizado não existe, mas o bruto existe, exibe o bruto capitalizado
            $label = ucfirst(trim($raw_value));
            error_log("[DEBUG STATUS] Pedido $order_id | Meta: $meta_key | Label fallback: '$label'");
            $operational_statuses[$meta_key] = [
                'label'   => $label,
                'icon'    => '',
                'classes' => 'text-gray-700',
            ];
        }
    }

    // 8. ITENS DO PEDIDO
    $items = [];
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $items[] = [
            'name'      => $item->get_name(),
            'quantity'  => $item->get_quantity(),
            'subtotal'  => wc_price($order->get_item_subtotal($item)),
            'image_url' => $product ? esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')) : wc_placeholder_img_src(),
        ];
    }

    // 9. DADOS DE RECEITAS
    $receitas_data = [];
    $receitas_html = ''; // Nova variável para o HTML das receitas
    $ids_receitas_str = $order->get_meta('selected_receitas');
    $tem_receitas = false; // Inicializa como false

    if (!empty($ids_receitas_str)) {
        $ids_receitas = array_filter(array_map('intval', explode(',', $ids_receitas_str)));

        if (!empty($ids_receitas)) {
            $tem_receitas = true; // Marca que tem receitas
            $receitas_html .= '<ul class="space-y-2">';
            foreach ($ids_receitas as $recipe_id) {
                $receita = get_post($recipe_id);
                if ($receita) {
                    $data_vencimento_raw = get_post_meta($receita->ID, 'data_vencimento', true);
                    $arquivo_receita_id  = get_post_meta($receita->ID, 'arquivo_receita', true);
                    $arquivo_laudo_id    = get_post_meta($receita->ID, 'arquivo_laudo', true);

                    $data_vencimento = !empty($data_vencimento_raw) ? esc_html($data_vencimento_raw) : 'Não informada';
                    $receita_href    = !empty($arquivo_receita_id) ? esc_url(wp_get_attachment_url($arquivo_receita_id)) : '#!';
                    $laudo_href      = !empty($arquivo_laudo_id) ? esc_url(wp_get_attachment_url($arquivo_laudo_id)) : '#!';

                    $receitas_data[] = [ // Mantém para a função de cópia
                        'id'                => $receita->ID,
                        'post_title'        => esc_html($receita->post_title),
                        'data_vencimento'   => $data_vencimento,
                        'receita_cor'       => $receita_href === '#!' ? 'text-red-500' : 'text-green-500',
                        'receita_href'      => $receita_href,
                        'laudo_cor'         => $laudo_href === '#!' ? 'text-red-500' : 'text-green-500',
                        'laudo_href'        => $laudo_href,
                    ];

                    // Gera o HTML para cada receita
                    $receitas_html .= '
                        <li class="p-2 border border-gray-200 bg-white rounded-lg text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <span>' . esc_html($receita->post_title) . ' (Vence: ' . $data_vencimento . ')</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="' . $receita_href . '" target="_blank" class="flex items-center space-x-1 ' . ($receita_href === '#!' ? 'text-red-500' : 'text-green-500') . ' hover:text-opacity-80 hover:underline">
                                        <span>Receita</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" /></svg>
                                    </a>
                                    <a href="' . $laudo_href . '" target="_blank" class="flex items-center space-x-1 ' . ($laudo_href === '#!' ? 'text-red-500' : 'text-green-500') . ' hover:text-opacity-80 hover:underline">
                                        <span>Laudo</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" /></svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    ';
                }
            }
            $receitas_html .= '</ul>';
        } else {
            $receitas_html = '<p class="text-sm text-gray-500 bg-gray-100 p-3 rounded-lg border border-gray-200">Nenhuma receita encontrada para este pedido.</p>';
        }
    } else {
        $receitas_html = '<p class="text-sm text-gray-500 bg-gray-100 p-3 rounded-lg border border-gray-200">Nenhuma receita selecionada para este pedido.</p>';
    }

    // 10. DADOS DE ENDEREÇO E FINANCEIRO
    $shipping_address = $order->get_address('shipping');
    $formatted_shipping_address = $order->get_formatted_shipping_address();
    $subtotal_display = $order->get_subtotal_to_display();
    $total_tax        = $order->get_total_tax();
    
    // Montar cidade e estado para infos_extra
    $cidade_estado = '';
    if (!empty($shipping_address['city']) || !empty($shipping_address['state'])) {
        $cidade_parts = array_filter([
            $shipping_address['city'] ?? '',
            $shipping_address['state'] ?? ''
        ]);
        $cidade_estado = implode(' - ', $cidade_parts);
    }

    // Nova lógica para detalhar taxas, descontos e entrega
    $order_fees = [];

    // Adiciona o frete padrão do WooCommerce, se existir
    if ($order->get_shipping_total() > 0) {
        $shipping_method_title = $order->get_shipping_method();
        $order_fees[] = [
            'name'  => !empty($shipping_method_title) ? $shipping_method_title : 'Entrega',
            'value' => $order->get_shipping_to_display(),
        ];
    }

    // Adiciona todas as outras taxas (que podem ser positivas ou negativas, como descontos e frete customizado)
    foreach ($order->get_items('fee') as $item_fee) {
        $order_fees[] = [
            'name'  => $item_fee->get_name(),
            'value' => wc_price($item_fee->get_total(), ['currency' => $order->get_currency()]),
        ];
    }

    return [
        'order_id'                  => $order_id,
        'order_date'                => $order_date,
        'order_date_sortable'       => $order_date_sortable,
        'customer_name'             => $customer_name,
        'tipo_associacao'           => $tipo_associacao,
        'text_tipo_assoc'           => $text_tipo_assoc,
        'bg_badge'                  => $bg_badge,
        'txt_color'                 => $txt_color,
        'status_classes'            => $status_classes,
        'status_text'               => $status_text,
        'status_dot_color'          => $status_dot_color,
        'entrega_badge_class'       => $entrega_badge_class,
        'entrega_text'              => $entrega_text,
        'formatted_order_total'     => $formatted_total,
        'pay_url'                   => $pay_url,
        'is_paid'                   => $is_paid,
        'billing_phone'             => $final_phone,
        'telefone_formatado'        => $telefone_formatado,
        'mensagem_whatsapp'         => $mensagem_whatsapp,
        'diagnostico'               => $diagnostico,
        //'observacoes'               => $observacoes,
        'items'                     => $items,
        'receitas_data'             => $receitas_data, // Mantém para a função de cópia
        'receitas_html'             => $receitas_html, // Novo campo com o HTML pré-renderizado
        'shipping_address'          => $shipping_address,
        'formatted_shipping_address' => $formatted_shipping_address,
        'subtotal_display'          => $subtotal_display,
        'order_fees'                => $order_fees, // Substitui shipping_display
        'total_tax'                 => $total_tax,
        'operational_statuses'      => $operational_statuses,
        'forma_pagamento_woo'       => $forma_pagamento_woo,
        'forma_entrega_woo'         => $forma_entrega_woo,
        'status_entrega'            => $status_entrega,
        'extracao'                  => $extracao,
        // Dados para o container de cópia (já estão sendo buscados com user_meta_cached)
        'nome_completo_respon'      => $nome_completo_respon,
        'usa_medicacao'             => $usa_medicacao,
        'qual_medicacao'            => $qual_medicacao,
        'fez_uso_canabis_escolha'   => $fez_uso_canabis_escolha,
        'observacoes_user'          => $observacoes_user,
        'order_status_slug'         => $order_status_slug, // <-- Adicionado para o JS
        'cidade_estado'             => $cidade_estado, // <-- Adicionado para infos_extra
        'tem_receitas'              => $tem_receitas, // <-- Adicionado para infos_extra
    ];
}


/**
 * Manipulador AJAX para salvar os metadados customizados de um pedido WooCommerce.
 * Recebe os dados do formulário do modal de configurações.
 */
add_action('wp_ajax_save_order_custom_meta', 'save_order_custom_meta_callback');
function save_order_custom_meta_callback() {
    // 1. Segurança e Validação
    check_ajax_referer('save_order_meta_nonce', '_nonce');
    
    error_log('--- [SAVE ORDER META] CALLBACK INICIADO ---');
    error_log('[SAVE ORDER META] Dados recebidos via POST: ' . print_r($_POST, true));

    $user = wp_get_current_user();
    if ( !current_user_can('edit_shop_orders') && !in_array('gerente', (array) $user->roles) ) {
        error_log('[SAVE ORDER META] ERRO: Permissão negada. User needs "edit_shop_orders" capability or "gerente" role.');
        wp_send_json_error(['message' => 'Permissão negada.'], 403);
    }

    if (!isset($_POST['order_id'])) {
        error_log('[SAVE ORDER META] ERRO: ID do Pedido não fornecido.');
        wp_send_json_error(['message' => 'ID do Pedido não fornecido.'], 400);
    }

    $order_id = absint($_POST['order_id']);
    $order = wc_get_order($order_id);

    if (!$order) {
        error_log('[SAVE ORDER META] ERRO: Pedido não encontrado para o ID: ' . $order_id);
        wp_send_json_error(['message' => 'Pedido não encontrado.'], 404);
    }

    $meta_updated = false;
    $status_updated = false;

    // 2. Atualizar Metadados Nativos com Verificação de Mudança
    $fields_to_check = [
        '_forma_pagamento_woo' => isset($_POST['forma_pagamento_woo']) ? sanitize_text_field($_POST['forma_pagamento_woo']) : '',
        '_forma_entrega_woo'   => isset($_POST['forma_entrega_woo']) ? sanitize_text_field($_POST['forma_entrega_woo']) : '',
        '_status_entrega'      => isset($_POST['status_entrega']) ? sanitize_text_field($_POST['status_entrega']) : '',
        '_extracao'            => isset($_POST['extracao']) ? sanitize_text_field($_POST['extracao']) : '',
    ];

    foreach ($fields_to_check as $key => $new_value) {
        $old_value = get_post_meta($order_id, $key, true);
        if ($old_value !== $new_value) {
            update_post_meta($order_id, $key, $new_value);
            error_log("[SAVE ORDER META] Meta Update: Pedido ID {$order_id}, Chave {$key}, Valor: {$new_value}");
            $meta_updated = true; // Marca que houve uma atualização real
        }
    }
    
    // 3. Atualizar Status do Pedido WooCommerce
    if (isset($_POST['order_status']) && !empty($_POST['order_status'])) {
        $new_status_prefixed = sanitize_text_field($_POST['order_status']);
        
        // `update_status` espera o slug sem o prefixo 'wc-'
        $new_status_unprefixed = str_replace('wc-', '', $new_status_prefixed);

        // Apenas atualiza se o status for diferente para evitar ações desnecessárias
        if ('wc-' . $order->get_status() !== $new_status_prefixed) {
             $order->update_status($new_status_unprefixed, 'Status alterado via painel de configurações.');
             $status_updated = true;
             error_log("[SAVE ORDER META] Status Update: Pedido ID {$order_id} alterado para {$new_status_unprefixed}.");
        }
    } 
    
    // Adiciona uma nota genérica apenas se metadados foram mudados, mas o status não.
    if ($meta_updated && !$status_updated) {
        $order->add_order_note('Configurações operacionais do pedido foram atualizadas via painel.');
        error_log("[SAVE ORDER META] Nota Adicionada: Configurações operacionais atualizadas para o Pedido ID {$order_id}.");
    }

    // 4. Buscar dados atualizados para retornar ao frontend
    $order->save();
    clean_post_cache($order_id);
    wp_cache_delete($order_id, 'posts');
    wp_cache_delete($order_id, 'post_meta');
    wp_cache_flush(); // CUIDADO: flush global, use apenas para teste!
    delete_transient('wc_order_' . $order_id);
    $order = wc_get_order($order_id); // Recarrega o objeto do pedido para garantir dados atualizados
    $updated_data = get_order_display_data($order);
    // Debug: retornar valores brutos de meta
    $updated_data['debug_meta'] = [
        'forma_pagamento' => get_post_meta($order_id, '_forma_pagamento_woo', true),
        'extracao' => get_post_meta($order_id, '_extracao', true),
        'forma_entrega' => get_post_meta($order_id, '_forma_entrega_woo', true),
        'status_entrega' => get_post_meta($order_id, '_status_entrega', true),
        'order_status' => $order->get_status(),
    ];
    error_log('[SAVE ORDER META] Dados atualizados para o Pedido ID ' . $order_id . ' a serem enviados para o frontend.');

    // 5. Resposta
    wp_send_json_success([
        'message' => 'Configurações do pedido salvas com sucesso.',
        'updated_data' => $updated_data
    ]);

    // Limpar cache do pedido para garantir que os dados atualizados sejam buscados
    $order->save(); // Salva quaisquer alterações pendentes e limpa o cache interno do objeto
    wc_delete_product_transients(); // Limpa transients de produtos (pode afetar o pedido)
    clean_post_cache($order_id); // Limpa o cache do post (pedido é um post type)
}