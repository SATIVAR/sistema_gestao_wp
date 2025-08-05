<?php
/**
 * The template for displaying all 'Saídas' (Outputs/Expenses)
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage CAJU
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Todas as Saídas
*/
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');
?>
<?php get_template_part('header', 'user') ?>

<style>
    /* Correção para o z-index dos controles do DataTables */
    #saidasTable_wrapper .dataTables_filter {
        position: relative;
        z-index: 10;
    }

    #saidasTable_wrapper .dataTables_length,
    #saidasTable_wrapper .dataTables_info,
    #saidasTable_wrapper .dataTables_paginate {
        position: relative;
        z-index: 5;
    }

    /* ************************************************************ */
    /* Ajustes para a Scrollbar Horizontal na Tabela - INÍCIO */
    /* ************************************************************ */

    /* Garante que o contêiner de overflow funcione corretamente */
    .overflow-x-auto {
        min-width: 0;
        overflow-x: auto;
    }

    #saidasTable {
        width: 100% !important;
        table-layout: fixed;
    }

    /* Definir larguras para as colunas. Ajuste estas porcentagens conforme a necessidade do seu conteúdo */
    #saidasTable th:nth-child(1),
    #saidasTable td:nth-child(1) { /* Coluna Saída */
        width: 35%;
    }
    #saidasTable th:nth-child(2),
    #saidasTable td:nth-child(2) { /* Coluna Categoria */
        width: 25%;
        white-space: nowrap;
    }
    #saidasTable th:nth-child(3),
    #saidasTable td:nth-child(3) { /* Coluna Valor */
        width: 15%;
    }
    #saidasTable th:nth-child(4),
    #saidasTable td:nth-child(4) { /* Coluna Ações */
        width: 25%;
    }

    /* Importante: Forçar quebra de texto dentro das células para evitar overflow */
    #saidasTable td {
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Especialmente para a coluna de ações, que tem muitos botões */
    #saidasTable td:nth-child(4) > div {
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    /* Estilização Zebra-Striping e Hover para o DataTables */
    #saidasTable tbody tr.odd {
        background-color: #ffffff;
    }
    #saidasTable tbody tr.even {
        background-color: #f8fafc;
    }
    #saidasTable tbody tr:hover {
        background-color: #f0fdf4;
    }

    /* Estilos para child rows (detalhes) */
    .child-row-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .details-shown + tr > td > div.child-row-content {
        max-height: 1000px; /* Um valor grande o suficiente para conter o conteúdo */
        transition: max-height 0.5s ease-in;
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
                                <h1 class="leading-none font-semibold text-xl">Todas as Saídas Registradas</h1>
                                <p class="text-muted-foreground text-sm">Visualize e gerencie todas as despesas da sua empresa.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?php echo home_url("/nova-saida"); ?>"" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Nova Saída
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="saida-mensagem" class="hidden flex items-center w-full max-w-full p-4 mb-4 text-green-600 bg-green-200 rounded-lg shadow" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                    </div>
                    <div class="ms-3 text-sm font-normal" id="saida-mensagem-text"></div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-200 text-green-600 hover:text-green-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8" id="saida-mensagem-close" aria-label="Close"></button>
                </div>

                <div class="card card-border">
                    <div class="card-body p-0">
                        <div class="md:overflow-x-hidden">

                <table id="saidasTable" class="amedis-datatable min-w-full divide-y divide-gray-200 md:overflow-x-hidden">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </span>
                                    <span>Saída</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                    <span>Categoria</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                    </span>
                                    <span>Valor</span>
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
                                    // Inicializa o array para coletar todos os detalhes das saídas
                                    $all_saida_details = [];

                                    $args = array(
                                        'post_type'      => 'saidas',
                                        'post_status'    => 'publish',
                                        'posts_per_page' => -1, // Obter todas as saídas para processamento no JS
                                        'orderby'        => 'date',
                                        'order'          => 'DESC',
                                    );

                                    $saidas_query = new WP_Query($args);

                                    if ($saidas_query->have_posts()) :
                                        while ($saidas_query->have_posts()) : $saidas_query->the_post();
                                            $saida_id = get_the_ID();
                                            $saida_title = get_the_title();
                                            $saida_date = get_the_date('d/m/Y');
                                            $saida_date_sortable = get_the_date('Y-m-d H:i:s'); // Para ordenação do DataTables
                                            $saida_total = get_post_meta($saida_id, 'saida_total', true);

                                            $saida_category_terms = get_the_terms($saida_id, 'categoria-saida');
                                            $saida_category_name = !empty($saida_category_terms) ? esc_html($saida_category_terms[0]->name) : 'N/A';

                                            // Coletar itens associados a esta saída
                                            $current_saida_items = [];

                                            // 1. Buscar itens do CPT 'itens' (modelo antigo)
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
                                            );
                                            $itens_query = new WP_Query($itens_args);
                                            if ($itens_query->have_posts()) {
                                                while ($itens_query->have_posts()) {
                                                    $itens_query->the_post();
                                                    $item_id = get_the_ID();
                                                    $item_attachment_id = get_post_meta($item_id, 'item_attachment_id', true);
                                                    $attachment_url = $item_attachment_id ? wp_get_attachment_url($item_attachment_id) : '';
                                                    $current_saida_items[] = [
                                                        'item_title' => get_the_title(),
                                                        'item_preco_unit' => get_post_meta($item_id, 'item_preco_unit', true),
                                                        'item_discount' => get_post_meta($item_id, 'item_discount', true),
                                                        'item_extra' => get_post_meta($item_id, 'item_extra', true),
                                                        'item_frete' => get_post_meta($item_id, 'item_frete', true),
                                                        'file_url' => $attachment_url,
                                                        'comprovante_url' => '',
                                                        'recibo_url' => '',
                                                    ];
                                                }
                                                wp_reset_postdata();
                                            }
                                            // 2. Se não houver itens do CPT, buscar meta 'itens_saida' (modelo novo)
                                            if (empty($current_saida_items)) {
                                                $itens_saida_json = get_post_meta($saida_id, 'itens_saida', true);
                                                $itens_saida = !empty($itens_saida_json) ? json_decode($itens_saida_json, true) : [];
                                                foreach ($itens_saida as $item) {
                                                    $current_saida_items[] = [
                                                        'item_title' => $item['item_title'] ?? '',
                                                        'item_preco_unit' => $item['preco_unit'] ?? 0,
                                                        'item_discount' => $item['item_discount'] ?? 0,
                                                        'item_extra' => $item['item_extra'] ?? 0,
                                                        'item_frete' => $item['item_frete'] ?? 0,
                                                        'file_url' => $item['file_url'] ?? '',
                                                        'comprovante_url' => $item['comprovante_url'] ?? '',
                                                        'recibo_url' => $item['recibo_url'] ?? '',
                                                    ];
                                                }
                                            }

                                            // Adiciona os dados ao array para o JS
                                            $all_saida_details[$saida_id] = [
                                                'saida_id' => $saida_id,
                                                'saida_title' => $saida_title,
                                                'saida_date' => $saida_date,
                                                'saida_date_sortable' => $saida_date_sortable,
                                                'saida_total' => $saida_total,
                                                'saida_category_name' => $saida_category_name,
                                                'itens' => $current_saida_items,
                                            ];
                                    ?>
                                            <tr class="master-row transition-colors duration-200" data-saida-id="<?php echo esc_attr($saida_id); ?>">
                                                <td class="px-6 py-4 align-middle">
                                                    <span class="uk-hidden"><?php echo esc_attr($saida_date_sortable); ?></span>
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-12 w-12">
                                                            <div class="h-12 w-12 bg-gray-50 text-indigo-600 rounded-lg flex items-center justify-center shadow-sm ring-1 ring-gray-200">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="w-7 h-7 text-indigo-600"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M384 480l48 0c11.4 0 21.9-6 27.6-15.9l112-192c5.8-9.9 5.8-22.1 .1-32.1S555.5 224 544 224l-400 0c-11.4 0-21.9 6-27.6 15.9L48 357.1 48 96c0-8.8 7.2-16 16-16l117.5 0c4.2 0 8.3 1.7 11.3 4.7l26.5 26.5c21 21 49.5 32.8 79.2 32.8L416 144c8.8 0 16 7.2 16 16l0 32 48 0 0-32c0-35.3-28.7-64-64-64L298.5 96c-17 0-33.3-6.7-45.3-18.7L226.7 50.7c-12-12-28.3-18.7-45.3-18.7L64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l23.7 0L384 480z"/></svg>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-xs text-gray-500 mt-1.5">
                                                                Saída #<?php echo $saida_id; ?>
                                                            </div>
                                                            <div class="text-base font-medium text-gray-600 leading-tight mb-0.5">
                                                                <?php echo esc_html($saida_title); ?>
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1.5">
                                                                Data: <?php echo $saida_date; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 align-middle text-left space-y-2 uppercase">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-medium text-gray-500">CATEGORIA:</span>
                                                        <span class="text-xs font-semibold text-gray-700">
                                                            <?php echo $saida_category_name; ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 align-middle">
                                                    <div class="text-xs space-y-1 w-full min-w-[150px]">
                                                        <div class="flex justify-between font-medium text-sm">
                                                            <span class="text-gray-800">Total:</span>
                                                            <span class="text-gray-900">R$ <?php echo number_format((float)$saida_total, 2, ',', '.'); ?></span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                                    <div class="flex items-center justify-end flex-wrap gap-2 min-w-[150px]">
                                                        <button type="button" class="toggle-details-btn inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" title="Ver Detalhes">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                                        </button>

                                                        <button type="button" class="delete-saida-btn inline-flex items-center justify-center p-2 border border-red-300 shadow-sm rounded-full text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200" data-saida-id="<?php echo $saida_id; ?>" title="Excluir Saída">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                        </button>

                                                        <!-- Ação de Edição: Aponte para um template de edição real quando estiver pronto -->
                                                        <a href="<?php echo esc_url( home_url( '/editar-saida/?saida_id=' . $saida_id ) ); ?>" title="Editar Saída" class="inline-flex items-center justify-center p-2 border border-blue-300 shadow-sm rounded-full text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                                        </a>

                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        endwhile;
                                    else :
                                    ?>
                                        <tr class="bg-white">
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mb-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375 0 0 1 .75 0Zm7.5 0a.375 3.75 0 1 1-.75 0 .375 0 0 1 .75 0Z" />
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma saída registrada</h3>
                                                    <p class="text-gray-500">Não há despesas para exibir no momento.</p>
                                                    <a href="<?php echo home_url("/nova-saida"); ?>" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 hover:text-white transition-all duration-300">Registrar Nova Saída</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    endif;
                                    wp_reset_postdata();
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

<!-- Modal de Confirmação de Exclusão -->
<div id="modal-confirm-delete-saida" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-red-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Confirmar Exclusão</h2>
            <p class="text-gray-600 mb-6">Você tem certeza que deseja excluir a saída <br><strong id="delete-saida-title"></strong> (ID: <span id="delete-saida-id"></span>)? <br>Esta ação não pode ser desfeita.</p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Cancelar</button>
                <a href="#" id="confirm-delete-saida-link" class="uk-button bg-red-800 rounded-md text-white hover:opacity-80">Sim, Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    // JSON com todos os dados dos detalhes das saídas, passado do PHP
    var allSaidaDetails = <?php echo json_encode($all_saida_details); ?>;
    console.log('[DEBUG JS] allSaidaDetails (after PHP encode):', allSaidaDetails);
</script>

<script>
    jQuery(document).ready(function($) {
        // Inicialização do DataTable
        var table = $('#saidasTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            dom: "<'flex items-center justify-between flex-wrap gap-2'lf>" +
                 "<'flex justify-center items-center md:absolute md:left-[-50%] md:right-[-50%] md:top-[-5px]'p>" +
                 "<'overflow-x-auto'tr>" +
                 "<'mt-1'ip'>",
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
            order: [[0, 'desc']], // Ordena pela primeira coluna (data de criação implícita)
        });

        // =====================================================================
        // Handlers da Tabela Principal
        // =====================================================================

        // Lógica do Acordeão (DataTables Child Rows)
        $('#saidasTable tbody').on('click', 'button.toggle-details-btn', function () {
            var button = $(this);
            var tr = button.closest('tr');
            var row = table.row(tr);
            var saidaId = tr.data('saida-id');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('details-shown');
                button.find('svg').removeClass('rotate-180');
            } else {
                row.child(format(saidaId)).show();
                tr.addClass('details-shown');
                button.find('svg').addClass('rotate-180');
                // Adiciona a classe global trAcordeon à <tr> do child row (acordeão)
                var childTr = tr.next('tr'); // A child row é sempre a próxima tr após a principal
                childTr.addClass('trAcordeon');
            }
        });

        // Evento para garantir que as child rows sejam escondidas ao redesenhar a tabela
        table.on('draw.dt', function() {
            table.rows().every(function() {
                var r = this;
                if (r.child.isShown()) {
                    r.child.hide();
                    $(r.node()).removeClass('details-shown');
                    $(r.node()).find('button.toggle-details-btn svg').removeClass('rotate-180');
                }
            });
        });

        // Close alert message
        $('#saida-mensagem-close').on('click', function() {
            $('#saida-mensagem').addClass('hidden');
        });

        // =====================================================================
        // Lógica para o Modal de Exclusão
        // =====================================================================
        $(document).on('click', '.delete-saida-btn', function(e) {
            e.preventDefault(); // Previne qualquer ação padrão do botão
            const saidaId = $(this).data('saida-id');
            const saidaData = allSaidaDetails[saidaId];

            if (saidaData) {
                // Preenche o modal com os dados da saída
                $('#delete-saida-title').text(`"${saidaData.saida_title}"`);
                $('#delete-saida-id').text(saidaId);
                // Salva o ID da saída no botão de confirmação
                $('#confirm-delete-saida-link').data('saida-id', saidaId);
                // Abre o modal
                UIkit.modal('#modal-confirm-delete-saida').show();
            } else {
                alert('Dados da saída não encontrados para exclusão.');
            }
        });

        // Handler para confirmação de exclusão
        $('#confirm-delete-saida-link').off('click').on('click', function(e) {
            e.preventDefault();
            const saidaId = $(this).data('saida-id');
            if (!saidaId) {
                alert('ID da saída não encontrado.');
                return;
            }
            // Desabilita o botão para evitar múltiplos envios
            $(this).addClass('uk-disabled').text('Excluindo...');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'delete_saida_and_related_items',
                    saida_id: saidaId,
                    security: '<?php echo wp_create_nonce("delete_saida_nonce"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Fecha o modal
                        UIkit.modal('#modal-confirm-delete-saida').hide();
                        // Remove a linha da tabela
                        const row = $(`#saidasTable tr[data-saida-id='${saidaId}']`);
                        table.row(row).remove().draw();
                        // Mensagem de sucesso
                        $('#saida-mensagem-text').text(response.data.message || 'Saída excluída com sucesso!');
                        $('#saida-mensagem').removeClass('hidden');
                    } else {
                        alert(response.data && response.data.message ? response.data.message : 'Erro ao excluir a saída.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao comunicar com o servidor para excluir a saída.');
                },
                complete: function() {
                    $('#confirm-delete-saida-link').removeClass('uk-disabled').text('Sim, Excluir');
                }
            });
        });

        // Função para formatar a linha filha (child row)
// Substituir a função format() existente por uma versão melhorada
function format(saidaId) {
    const data = allSaidaDetails[saidaId];
    if (!data || !data.itens || data.itens.length === 0) {
        return `
            <div class="child-row-content p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 border-t border-gray-200">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 backdrop-blur-sm">
                    <div class="flex items-center justify-center flex-col space-y-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h5 class="text-lg font-semibold text-gray-800 mb-2">Detalhes das Despesas</h5>
                            <p class="text-gray-500">Nenhum detalhe de nota fiscal/gasto encontrado para esta saída.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Resumo da saída com design melhorado
    const resumoHtml = `
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-100 mb-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-10 h-10 text-white"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M184 48l144 0c4.4 0 8 3.6 8 8l0 40L176 96l0-40c0-4.4 3.6-8 8-8zm-56 8l0 40L64 96C28.7 96 0 124.7 0 160l0 96 192 0 128 0 192 0 0-96c0-35.3-28.7-64-64-64l-64 0 0-40c0-30.9-25.1-56-56-56L184 0c-30.9 0-56 25.1-56 56zM512 288l-192 0 0 32c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32-14.3-32-32l0-32L0 288 0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-128z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-xl">${data.saida_title}</h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="px-3 py-1 bg-white rounded-full text-xs font-semibold text-indigo-600 shadow-sm border border-indigo-100">${data.saida_category_name}</span>
                            <span class="px-3 py-1 bg-white rounded-full text-xs font-semibold text-gray-600 shadow-sm border border-gray-100">${data.saida_date}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-1">Total Geral</div>
                    <div class="text-3xl font-bold text-green-600">R$ ${parseFloat(data.saida_total).toLocaleString('pt-BR', {minimumFractionDigits:2})}</div>
                    <div class="text-sm text-gray-500 mt-1">${data.itens.length} nota${data.itens.length !== 1 ? 's' : ''} fiscal${data.itens.length !== 1 ? 'is' : ''}</div>
                </div>
            </div>
        </div>
    `;

    // Cards dos itens com design moderno
    const itensHtml = data.itens.map((item, index) => {
        const totalItem = (parseFloat(item.item_preco_unit) || 0) + (parseFloat(item.item_extra) || 0) + (parseFloat(item.item_frete) || 0) - (parseFloat(item.item_discount) || 0);
        const formatBRL = v => parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        
        // Cores alternadas para os cards
        const cardColors = [
            'from-blue-50 to-indigo-50 border-blue-200',
            'from-green-50 to-emerald-50 border-green-200',
            'from-purple-50 to-violet-50 border-purple-200',
            'from-orange-50 to-amber-50 border-orange-200',
            'from-pink-50 to-rose-50 border-pink-200',
            'from-cyan-50 to-teal-50 border-cyan-200'
        ];
        const cardColor = cardColors[index % cardColors.length];
        
        // Status dos anexos
        const anexoStatus = {
            nf: item.file_url ? 'disponivel' : 'ausente',
            comp: item.comprovante_url ? 'disponivel' : 'ausente',
            recibo: item.recibo_url ? 'disponivel' : 'ausente'
        };
        
        const anexosHtml = `
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-600 mr-2">Anexos:</span>
                <div class="flex gap-1">
                    ${item.file_url 
                        ? `<a href="${item.file_url}" target="_blank" class="group relative">
                             <div class="w-8 h-8 bg-green-500 hover:bg-green-600 rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                               <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                               </svg>
                             </div>
                             <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">NF</div>
                           </a>`
                        : `<div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center opacity-50 cursor-not-allowed">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                             </svg>
                           </div>`}
                    
                    ${item.comprovante_url 
                        ? `<a href="${item.comprovante_url}" target="_blank" class="group relative">
                             <div class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                               <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                               </svg>
                             </div>
                             <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Comp.</div>
                           </a>`
                        : `<div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center opacity-50 cursor-not-allowed">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                             </svg>
                           </div>`}
                    
                    ${item.recibo_url 
                        ? `<a href="${item.recibo_url}" target="_blank" class="group relative">
                             <div class="w-8 h-8 bg-purple-500 hover:bg-purple-600 rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                               <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                               </svg>
                             </div>
                             <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Recibo</div>
                           </a>`
                        : `<div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center opacity-50 cursor-not-allowed">
                             <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                             </svg>
                           </div>`}
                </div>
            </div>
        `;

        return `
            <div class="bg-gradient-to-br ${cardColor} rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-2 hover:scale-[1.02] group overflow-hidden">
                <!-- Header do Card -->
                <div class="bg-white/80 backdrop-blur-sm p-4 border-b border-gray-200/50">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="text-xs text-gray-500 mt-1">Item #${index + 1}</div>
                                <h4 class="font-bold text-gray-800 text-lg leading-tight" title="${item.item_title}">${item.item_title}</h4>                                
                            </div>
                        </div>
                        <div class="">
                            <div class="text-xs text-gray-500">Total</div>
                            <div class="text-2xl font-bold text-green-600">${formatBRL(totalItem)}</div>                            
                        </div>
                    </div>
                </div>

                <!-- Corpo do Card -->
                <div class="p-4 space-y-4">
                    <!-- Valores Financeiros -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="bg-white/60 rounded-lg p-3 text-center hidden">
                            <div class="text-xs text-gray-600 font-medium mb-1">Valor Base</div>
                            <div class="font-bold text-gray-800">${formatBRL(item.item_preco_unit)}</div>
                        </div>
                        <div class="bg-white/60 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-600 font-medium mb-1">Desconto</div>
                            <div class="font-bold text-red-600">-${formatBRL(item.item_discount)}</div>
                        </div>
                        <div class="bg-white/60 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-600 font-medium mb-1">Extra</div>
                            <div class="font-bold text-blue-600">+${formatBRL(item.item_extra)}</div>
                        </div>
                        <div class="bg-white/60 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-600 font-medium mb-1">Frete</div>
                            <div class="font-bold text-orange-600">+${formatBRL(item.item_frete)}</div>
                        </div>
                    </div>

                    <!-- Anexos -->
                    <div class="bg-white/60 rounded-lg p-3">
                        ${anexosHtml}
                    </div>
                </div>
            </div>
        `;
    }).join('');

    return `
        <div class="child-row-content p-6 bg-gradient-to-br from-gray-50 to-gray-100 border-t border-gray-200">
            <div class="max-w-7xl mx-auto">
                ${resumoHtml}
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    ${itensHtml}
                </div>
            </div>
        </div>
    `;
}
        // =====================================================================
        // Fim da Lógica do DataTable e Acordeão
        // =====================================================================

    });
</script>

<?php get_footer(); ?> 