<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage CAJU
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Todos Produtos WooCommerce
*/
if (!current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');
get_template_part('header', 'user') ?>
<style>
    /* Add any specific styles for the products table here */
    #produtosTable_wrapper .dataTables_filter {
        position: relative;
        z-index: 10;
    }

    #produtosTable_wrapper .dataTables_length,
    #produtosTable_wrapper .dataTables_info,
    #produtosTable_wrapper .dataTables_paginate {
        position: relative;
        z-index: 5;
    }

    #produtosTable tbody tr.odd {
        background-color: #ffffff; /* Cor de bg-white */
    }
    #produtosTable tbody tr.even {
        background-color: #f8fafc; /* Cor de bg-slate-50 */
    }
    #produtosTable tbody tr:hover {
        background-color: #f0fdf4; /* Cor de bg-green-50 para o hover */
    }
</style>
<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container">
        <div class="flex">
            <div class="md:w-[100%]">

                <div class="bg-white text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
                    <div class="px-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <h1 class="leading-none font-semibold text-xl">Todos os Produtos</h1>
                                <p class="text-muted-foreground text-sm">Gerencie todos os produtos do sistema!</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?php echo bloginfo("url"); ?>/novo-produtowoo/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Novo Produto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-border">
                    <div class="card-body p-0">
                        <div class="md:overflow-x-hidden">

                    <table id="produtosTable" class="amedis-datatable min-w-full divide-y divide-gray-200 md:overflow-x-hidden">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                        </svg>
                                    </span>
                                    <span>Produto</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                    </span>
                                    <span>Preço</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                    </span>
                                    <span>Estoque</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-right">
                                    <div class="flex items-center justify-end">
                                    <span class="header-icon">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </span>
                                    <span>Ações</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">



                                    <?php
                                    $args = array(
                                        'post_type'      => 'product',
                                        'post_status'    => 'publish',
                                        'posts_per_page' => -1, // Get all products
                                        'orderby'        => 'title',
                                        'order'          => 'ASC',
                                        'tax_query'      => array(
                                            array(
                                                'taxonomy' => 'product_type',
                                                'field'    => 'slug',
                                                'terms'    => 'simple', // Only get simple products
                                            ),
                                        ),
                                    );
                                    $products_query = new WP_Query($args);

                                    // Collect product data for modals
                                    $all_products_data = [];

                                    if ($products_query->have_posts()) :
                                        while ($products_query->have_posts()) : $products_query->the_post();
                                            $product = wc_get_product(get_the_ID());
                                            if ($product && $product->is_type('simple')) :
                                                $product_id = $product->get_id();
                                                $product_name = $product->get_name();
                                                $product_price = $product->get_price();
                                                $product_stock = $product->get_stock_quantity();
                                                $product_image = has_post_thumbnail() ? get_the_post_thumbnail_url($product_id, 'thumbnail') : wc_placeholder_img_src();
                                                
                                                // Get product categories
                                                $product_categories = $product->get_category_ids();
                                                $category_name = 'Sem categoria';
                                                $category_color = 'bg-gray-100 text-gray-700'; // Default color
                                                
                                                if (!empty($product_categories)) {
                                                    $category = get_term($product_categories[0], 'product_cat');
                                                    if ($category && !is_wp_error($category)) {
                                                        $category_name = $category->name;
                                                        
                                                        // Define pastel colors for categories
                                                        $category_colors = array(
                                                            'bg-blue-100 text-blue-700',
                                                            'bg-green-100 text-green-700', 
                                                            'bg-purple-100 text-purple-700',
                                                            'bg-pink-100 text-pink-700',
                                                            'bg-yellow-100 text-yellow-700',
                                                            'bg-indigo-100 text-indigo-700',
                                                            'bg-red-100 text-red-700',
                                                            'bg-orange-100 text-orange-700',
                                                            'bg-teal-100 text-teal-700',
                                                            'bg-cyan-100 text-cyan-700'
                                                        );
                                                        
                                                        // Use category ID to consistently assign colors
                                                        $color_index = $category->term_id % count($category_colors);
                                                        $category_color = $category_colors[$color_index];
                                                    }
                                                }

                                                // Store product data for modals later
                                                $all_products_data[$product_id] = [
                                                    'name' => $product_name,
                                                    'price' => wc_price($product_price),
                                                ];
                                    ?>
                                            <tr class="transition-colors duration-200" data-product-id="<?php echo esc_attr($product_id); ?>">
                                                <td class="px-6 py-4 align-middle">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-12 w-12">
                                                            <img class="h-12 w-12 rounded-lg object-cover ring-1 ring-gray-200" src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>">
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-base font-medium text-gray-600 leading-tight mb-1">
                                                                <?php echo esc_html($product_name); ?>
                                                            </div>
                                                            <div class="flex items-center gap-2 mt-1">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo esc_attr($category_color); ?>">
                                                                    <?php echo esc_html($category_name); ?>
                                                                </span>
                                                                <span class="text-xs text-gray-500">
                                                                    ID: #<?php echo $product_id; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 align-middle text-left">
                                                    <span class="text-sm font-medium text-gray-700"><?php echo wc_price($product_price); ?></span>
                                                </td>
                                                <td class="px-6 py-4 align-middle text-left">
                                                    <span class="text-sm text-gray-700"><?php echo is_numeric($product_stock) ? esc_html($product_stock) : 'N/A'; ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                                    <div class="flex items-center justify-end flex-wrap gap-2">
                                                        
                                                        <button type="button" data-product-id="<?php echo esc_attr($product_id); ?>" title="Excluir Produto" class="delete-product-button-trigger inline-flex items-center justify-center p-2 border border-red-300 shadow-sm rounded-full text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200" uk-toggle="target: #modal-excluir-produto-<?php echo $product_id; ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.141-2.07-2.201a51.964 51.964 0 0 0-3.32 0c-1.16.06-2.07 1.02-2.07 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                        </button>

                                                        <a href="<?php echo bloginfo("url"); ?>/editar-produtowoo/?product_id=<?php echo esc_attr($product_id); ?>" title="Editar Produto" class="inline-flex items-center justify-center p-2 border border-blue-300 shadow-sm rounded-full text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                                        </a>

                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                            endif;
                                        endwhile;
                                        wp_reset_postdata();
                                    else :
                                    ?>
                                        <tr class="bg-white">
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mb-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4m-4 4.5h4m-9.75-4.5H5.625c-.621 0-1.125-.504-1.125-1.125V4.875c0-.621.504-1.125 1.125-1.125h12.75c.621 0 1.125.504 1.125 1.125v5.392c0 .621-.504 1.125-1.125 1.125H14.5m-9.75 0h9.75" />
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                                                    <p class="text-gray-500">Não há produtos simples para exibir no momento.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals de Exclusão de Produto -->
<?php
if (!empty($all_products_data)) {
    foreach ($all_products_data as $product_id => $product_data) {
?>
<div id="modal-excluir-produto-<?php echo $product_id; ?>" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg p-4">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-red-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Confirmar Exclusão</h2>
            <p class="text-gray-600 mb-6">Você tem certeza que deseja excluir o produto <br><strong>#<?php echo $product_id; ?> - <?php echo esc_html($product_data['name']); ?> </strong>? <br>Esta ação não pode ser desfeita.</p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default uk-modal-close rounded-md" type="button">Cancelar</button>
                <button class="uk-button bg-red-700 text-white hover:bg-red-800 rounded-md delete-product-confirm-button" type="button" data-product-id="<?php echo $product_id; ?>">Sim, Excluir</button>
            </div>
        </div>
    </div>
</div>
<?php
    }
}
?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize DataTable
        var table = $('#produtosTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            dom: 'lfrtip',
            language: {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            order: [[0, 'asc']], // Order by product name initially
            columnDefs: [
                { targets: [0, 1, 2, 3], orderable: true }, // Enable sorting on all columns
                { targets: 3, searchable: false } // Disable search on actions column
            ]
        });

        // Handle delete button click (opens the modal)
        $('#produtosTable tbody').on('click', '.delete-product-button-trigger', function(e) {
            // The modal is opened by uk-toggle, no need for JS here
            // We just prevent default to be safe, though uk-toggle handles it
            e.preventDefault();
        });

        // Handle confirmation button click inside the modal (triggers AJAX deletion)
        $(document).on('click', '.delete-product-confirm-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var productId = button.data('product-id');
            var modal = button.closest('.uk-modal');

            // Find the corresponding row in the DataTable using the product ID
            var row = $('#produtosTable').find('tr[data-product-id="' + productId + '"]');

            // Disable the confirm button and show spinner
            button.prop('disabled', true).html('<span uk-spinner="ratio: 0.5"></span> Excluindo...');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'delete_simple_product',
                    product_id: productId,
                    _nonce: '<?php echo wp_create_nonce('delete_product_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        UIkit.notification({ message: '<span uk-icon=\'icon: check\'></span> ' + response.data.message, status: 'success', pos: 'top-center', timeout: 2500 });
                        // Remove the row from the DataTable
                        if (row.length) {
                             $('#produtosTable').DataTable().row(row).remove().draw(false);
                        }
                        UIkit.modal(modal).hide(); // Close the modal
                    } else {
                        UIkit.notification({ message: '<span uk-icon=\'icon: warning\'></span> ' + (response.data.message || 'Erro ao excluir o produto.'), status: 'danger', pos: 'top-center' });
                        button.prop('disabled', false).html('Sim, Excluir'); // Re-enable button
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                    UIkit.notification({ message: '<span uk-icon=\'icon: close\'></span> Erro de conexão ao excluir.', status: 'danger', pos: 'top-center' });
                    button.prop('disabled', false).html('Sim, Excluir'); // Re-enable button
                }
            });
        });
    });
</script>
<?php get_footer(); ?>
