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
Template Name: Dashboard - Pedidos WooCommerce
*/

get_header('zero');

if ( !is_user_logged_in() ) {
    // Se o usuário não estiver logado, redireciona para a home
    //wp_redirect(home_url());
    //exit;
    ?>

    <?php  get_template_part('login'); ?>

<?php } else { ?>
<?php get_template_part('header', 'user') ?>
<style>
    /* Correção para o z-index dos controles do DataTables */
    #pedidosTable_wrapper .dataTables_filter {
        position: relative;
        z-index: 10;
    }

    #pedidosTable_wrapper .dataTables_length,
    #pedidosTable_wrapper .dataTables_info,
    #pedidosTable_wrapper .dataTables_paginate {
        position: relative;
        z-index: 5;
    }

    /* ************************************************************ */
    /* Ajustes para a Scrollbar Horizontal na Tabela - INÍCIO */
    /* ************************************************************ */

    /* Garante que o contêiner de overflow funcione corretamente */
    .overflow-x-auto {
        /* Garante que o contêiner não tenha largura mínima que force a barra */
        min-width: 0;
        /* Garante que ele lide com o overflow horizontal se ainda houver */
        overflow-x: auto;
    }

    #pedidosTable {
        width: 100% !important; /* Força a tabela a ocupar 100% da largura do seu container */
        table-layout: fixed; /* Essencial para que as larguras de coluna sejam respeitadas */
    }

    /* Definir larguras para as colunas. Ajuste estas porcentagens conforme a necessidade do seu conteúdo */
    /* A soma deve ser 100%. */
    #pedidosTable th:nth-child(1),
    #pedidosTable td:nth-child(1) { /* Coluna Pedido */
        width: 30%; 
    }
    #pedidosTable th:nth-child(2),
    #pedidosTable td:nth-child(2) { /* Coluna Status */
        width: 25%;
        white-space: nowrap;
    }
    #pedidosTable th:nth-child(3),
    #pedidosTable td:nth-child(3) { /* Coluna Valor */
        width: 15%;
    }
    #pedidosTable th:nth-child(4),
    #pedidosTable td:nth-child(4) { /* Coluna Ações */
        width: 30%; 
    }

    /* Importante: Forçar quebra de texto dentro das células para evitar overflow */
    #pedidosTable td {
        white-space: normal; /* Permite que o texto quebre linhas */
        word-wrap: break-word; /* Força a quebra de palavras longas */
        overflow-wrap: break-word; /* Outra propriedade para quebra de palavras */
    }

    /* Especialmente para a coluna de ações, que tem muitos botões */
    #pedidosTable td:nth-child(4) > div {
        flex-wrap: wrap; /* Permite que os botões quebrem para a próxima linha */
        justify-content: flex-end; /* Alinha os botões à direita */
        /* min-width: 120px; */ /* Pode ser necessário se os botões ficarem muito espremidos */
    }

    /* Ajuste para badges e spans, para que não excedam a largura da coluna */
    #pedidosTable .entrega-status-badge,
    #pedidosTable span.inline-flex {
        display: inline-flex; /* Garante que o flexbox interno funcione */
        box-sizing: border-box; /* Inclui padding e border na largura total */
        /* min-width: 80px; */ /* Já deve estar no seu HTML, mas reforça */
        /* max-width: 100%; */ /* Garante que não exceda o pai */
        white-space: normal; /* Permite que o texto dentro do badge quebre */
        text-overflow: ellipsis; /* Adiciona reticências se o texto for muito longo */
        overflow: hidden; /* Oculta o que excede */
    }

    /* Para a imagem do produto nos detalhes (child row), garantir que ela não estoure */
    #pedidosTable .w-12.h-12 {
        flex-shrink: 0; /* Impede que a imagem encolha */
        object-fit: cover; /* Garante que a imagem preencha o espaço sem distorcer */
    }

    /* ************************************************************ */
    /* Ajustes para a Scrollbar Horizontal na Tabela - FIM */
    /* ************************************************************ */

    /* Estilos para as notificações de cópia */
    .copy-notification {
        font-size: 14px;
        font-weight: 500;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease;
    }

    /* Estilos para as informações extras */
    .infos_extra {
        min-height: 20px;
        transition: all 0.3s ease;
    }

    .infos_extra .text-xs {
        line-height: 1.4;
    }

    .child-row-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out; /* Ajuste a duração e a função de temporização */
    }

    .details-shown + tr > td > div.child-row-content {
        max-height: 1000px; /* Um valor grande o suficiente para conter o conteúdo */
        transition: max-height 0.5s ease-in; /* Duração ao abrir */
    }

    /* Estilização Zebra-Striping e Hover para o DataTables */
    #pedidosTable tbody tr.odd {
        background-color: #ffffff; /* Cor de bg-white */
    }
    #pedidosTable tbody tr.even {
        background-color: #f8fafc; /* Cor de bg-slate-50 */
    }
    #pedidosTable tbody tr:hover {
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
                        <h1 class="leading-none font-semibold text-xl">Todos os Pedidos</h1>
                        <p class="text-muted-foreground text-sm">Todos os pedidos do sistema!</p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="<?php echo bloginfo("url"); ?>/novo-pedido/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Novo Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
                

                <div class="card card-border">
                    <div class="card-body p-0">
                        <div class="md:overflow-x-hidden">


                  <table id="pedidosTable" class="amedis-datatable min-w-full divide-y divide-gray-200 md:overflow-x-hidden">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                        </svg>
                                    </span>
                                    <span>Pedido</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                    <span>Status</span>
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
                                    // Inicializa o array para coletar todos os detalhes dos pedidos
                                    $all_order_details = [];

                                    // Define as opções para os selects do modal, removendo a dependência do ACF.
                                    $opcoes_forma_pagamento = [
                                        'gatewaypix'   => 'GATEWAY PIX',
                                        'gatewaycard'  => 'GATEWAY CARTÃO',
                                        'dinheiro'     => 'Dinheiro',
                                        'pix'          => 'PIX',
                                        'pgtodividido' => 'Pagamento Dividido',
                                    ];
                                    $opcoes_extracao = [
                                        'informado'  => 'Informado',
                                        'produzindo' => 'Produzindo',
                                        'pronto'     => 'Pronto para entrega',
                                    ];
                                    $opcoes_forma_entrega = [
                                        'pabloPaciente' => 'Pablo - Paciente',
                                        'pabloCorreios' => 'Pablo - Correios',
                                        'felipe'        => 'Felipe',
                                        'uber'          => 'Uber',
                                        'valentino'     => 'Valentino',
                                        'cezar'         => 'Cezar Colômbia',
                                        'correios'      => 'Entregador + Correios',
                                    ];
                                    $opcoes_status_entrega = [
                                        'pendente' => 'Pendente',
                                        'enviado'  => 'Enviado',
                                        'entregue' => 'Entregue',
                                    ];

                                    // Buscar pedidos do WooCommerce (excluindo refunds)
                                    $orders = wc_get_orders(array(
                                        'limit' => 100, // Limite de 100 pedidos, pode ser ajustado
                                        'orderby' => 'date',
                                        'order' => 'DESC',
                                        'status' => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'),
                                        'type' => 'shop_order' // Garante que apenas pedidos sejam retornados, não refunds
                                    ));
                                    
                                    // Debug: Log dos tipos de objetos retornados
                                    if (!empty($orders)) {
                                        error_log('[DEBUG ORDERS] Total de objetos retornados: ' . count($orders));
                                        foreach ($orders as $index => $order) {
                                            $class_name = is_object($order) ? get_class($order) : gettype($order);
                                            $order_type = (is_object($order) && method_exists($order, 'get_type')) ? $order->get_type() : 'N/A';
                                            error_log("[DEBUG ORDERS] Index $index: Class = $class_name, Type = $order_type");
                                        }
                                    }

                                    // Coletar IDs de todos os pedidos para pré-carregar as receitas
                                    $order_ids_to_preload = [];
                                    if (!empty($orders)) {
                                        foreach ($orders as $order) {
                                            // Só adiciona se for um WC_Order válido e não um refund
                                            if ($order instanceof WC_Order && !($order instanceof WC_Order_Refund)) {
                                                $order_ids_to_preload[] = $order->get_id();
                                            }
                                        }
                                    }
                                    // Pré-carregar todas as receitas necessárias em uma única consulta
                                                                        //_amedis_preload_recipes_for_dashboard($order_ids_to_preload);

                                    // Verifica se há pedidos para exibir
                                    if (!empty($orders)) :
                                        foreach ($orders as $order) :
                                            // Verifica se é um objeto WC_Order válido e não um refund
                                            if (!$order instanceof WC_Order || $order instanceof WC_Order_Refund) {
                                                continue; // Pula para o próximo item se não for um pedido válido
                                            }
                                            
                                            clean_post_cache($order->get_id()); // Limpa o cache do post antes de obter os dados
                                            // Chama a nova função para obter todos os dados formatados do pedido
                                            $data = get_order_display_data($order);
                                            
                                            // Se get_order_display_data retornar array vazio, pula este pedido
                                            if (empty($data)) {
                                                continue;
                                            }
                                            
                                            extract($data); // Extrai todas as variáveis do array $data para uso direto no template

                                            // Colete os dados dos detalhes para o JavaScript
                                            $all_order_details[$order_id] = array(
                                                'billing_phone' => $billing_phone,
                                                'telefone_formatado' => $telefone_formatado,
                                                'mensagem_whatsapp' => $mensagem_whatsapp,
                                                'diagnostico' => $diagnostico,
                                                'order_date_sortable' => $order_date_sortable,
                                                'items' => $items,
                                                'receitas_data' => $receitas_data,
                                                'receitas_html' => $receitas_html, // Adicionado para o JS
                                                'shipping_address' => $shipping_address,
                                                'formatted_shipping_address' => $formatted_shipping_address,
                                                'subtotal_display' => $subtotal_display,
                                                'order_fees' => $order_fees,
                                                'total_tax' => $total_tax,
                                                'total_tax_display' => wc_price($total_tax), // Garante que o total de impostos formatado esteja disponível
                                                'formatted_order_total' => $formatted_order_total,
                                                'usa_medicacao' => $usa_medicacao,
                                                'qual_medicacao' => $qual_medicacao,
                                                'fez_uso_canabis_escolha' => $fez_uso_canabis_escolha,
                                                'observacoes_user' => $observacoes_user,
                                                'customer_name' => $customer_name,
                                                'nome_completo_respon' => $nome_completo_respon,
                                                'tipo_associacao' => $tipo_associacao,
                                                'extracao' => $extracao,
                                                'order_id' => $order_id,
                                                'operational_statuses' => $operational_statuses,
                                                'forma_pagamento_woo' => $forma_pagamento_woo,
                                                'forma_entrega_woo' => $forma_entrega_woo,
                                                'status_entrega' => $status_entrega,
                                                'order_status_slug' => $order_status_slug,
                                                'cidade_estado' => $cidade_estado,
                                                'tem_receitas' => $tem_receitas,
                                                'tem_receitas_vencidas' => $tem_receitas_vencidas, // Adicionado para alertas de vencimento
                                                'count_receitas_vencidas' => $count_receitas_vencidas, // Contagem de receitas vencidas
                                                'receitas_vencidas_texto' => $receitas_vencidas_texto, // Texto do alerta de vencimento
                                                'status_dot_color' => $status_dot_color, // Adicionado para o JS
                                                'status_text' => $status_text, // Adicionado para o JS
                                            );
                                            ?>

                                            <tr class="master-row transition-colors duration-200" data-order-id="<?php echo esc_attr($order_id); ?>">
                                                <td class="px-6 py-4 align-middle">
                                                    <span class="uk-hidden"><?php echo esc_attr($order_date_sortable); ?></span>
                                                    <div class="flex items-center">
                                                         <div class="flex-shrink-0 h-12 w-12">
                                                            <div class="h-12 w-12 <?php echo $bg_badge; ?> <?php echo $txt_color; ?> rounded-lg flex items-center justify-center shadow-sm ring-1 ring-gray-200">
                                                                <?php if ($tipo_associacao == 'assoc_respon') : ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                                                                <?php elseif($tipo_associacao == 'assoc_tutor' ) : ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                                                                <?php else : ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-base font-medium text-gray-600 leading-tight mb-0.5">
                                                                <?php echo esc_html($customer_name); ?>
                                                            </div>
                                                            <div class="text-xs font-medium <?php echo $txt_color; ?> mt-0.5">
                                                                <?php echo $text_tipo_assoc; ?>
                                                            </div>
                                                            <?php if (isset($nome_completo_respon) && $nome_completo_respon !== '') : ?>
                                                                <div class="text-xs text-gray-500 mt-1.5">
                                                                    <?php echo esc_html($nome_completo_respon); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (isset($billing_phone) && $billing_phone !== '') : ?>
                                                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                                                    <a href="javascript:void(0);" class="copy-phone-btn hover:underline" data-phone="<?php echo esc_attr($billing_phone); ?>" title="Copiar telefone">
                                                                        <span><?php echo esc_html($billing_phone); ?></span>
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="infos_extra" data-order-id="<?php echo $order_id; ?>">
                                                                <?php if (!empty($cidade_estado)): ?>
                                                                    <div class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-gray-400">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                                        </svg>
                                                                        <span><?php echo esc_html($cidade_estado); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($tem_receitas_vencidas): ?>
                                                                    <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-red-500">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                                        </svg>
                                                                        <span><?php echo esc_html($receitas_vencidas_texto); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <?php if (!$tem_receitas): ?>
                                                                    <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-red-500">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                                        </svg>
                                                                        <span>Sem receitas</span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                             <div class="text-xs text-gray-500 mt-1.5">
                                                                Pedido <a href="<?php echo esc_url($order->get_edit_order_url()); ?>" target="_blank" class="text-blue-600 hover:text-blue-700 hover:underline transition-colors duration-200">#<?php echo $order_id; ?></a> - <?php echo $order_date; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="px-6 py-4 align-middle text-left space-y-2 uppercase">
                                                    <div class="flex flex-col gap-1">
                                                        <!-- STATUS PEDIDO -->
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-medium text-gray-500">STATUS PGTO:</span>
                                                            <div class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                                                                <span class="h-2 w-2 rounded-full <?php echo $status_dot_color; ?>"></span>
                                                                <span><?php echo $status_text ? esc_html($status_text) : '<span class="text-gray-400">Não definido</span>'; ?></span>
                                                            </div>
                                                        </div>
                                                        <!-- FORMA PGTO -->
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-medium text-gray-500">FORMA PGTO:</span>
                                                            <span class="text-xs font-semibold <?php echo isset($operational_statuses['_forma_pagamento_woo']) ? 'text-gray-700' : 'text-gray-400'; ?>">
                                                                <?php echo isset($operational_statuses['_forma_pagamento_woo']) ? esc_html($operational_statuses['_forma_pagamento_woo']['label']) : 'Não definido'; ?>
                                                            </span>
                                                        </div>
                                                        <!-- EXTRAÇÃO -->
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-medium text-gray-500">EXTRAÇÃO:</span>
                                                            <span class="text-xs font-semibold <?php echo isset($operational_statuses['_extracao']) ? 'text-gray-700' : 'text-gray-400'; ?>">
                                                                <?php echo isset($operational_statuses['_extracao']) ? esc_html($operational_statuses['_extracao']['label']) : 'Não definido'; ?>
                                                            </span>
                                                        </div>
                                                        <!-- ENTREGA -->
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-medium text-gray-500">ENTREGA:</span>
                                                            <span class="text-xs font-semibold <?php echo isset($operational_statuses['_forma_entrega_woo']) ? 'text-gray-700' : 'text-gray-400'; ?>">
                                                                <?php echo isset($operational_statuses['_forma_entrega_woo']) ? esc_html($operational_statuses['_forma_entrega_woo']['label']) : 'Não definido'; ?>
                                                            </span>
                                                        </div>
                                                        <!-- STATUS ENTREGA -->
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-medium text-gray-500">STATUS ENTREGA:</span>
                                                            <span class="text-xs font-semibold <?php echo isset($operational_statuses['_status_entrega']) ? 'text-gray-700' : 'text-gray-400'; ?>">
                                                                <?php echo isset($operational_statuses['_status_entrega']) ? esc_html($operational_statuses['_status_entrega']['label']) : 'Não definido'; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="px-6 py-4 align-middle">
                                                    <div class="text-xs space-y-1 w-full min-w-[150px]">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Subtotal:</span>
                                                            <span class="font-medium text-gray-700"><?php echo $subtotal_display; ?></span>
                                                        </div>
                                                        <?php if (!empty($order_fees)): ?>
                                                            <?php foreach ($order_fees as $fee): ?>
                                                                <?php
                                                                $is_discount = (float) $fee['value'] < 0 || strpos($fee['value'], '-') !== false;
                                                                $value_color = $is_discount ? 'text-green-600' : 'text-gray-700';
                                                                ?>
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo esc_html($fee['name']); ?>:</span>
                                                                    <span class="font-medium <?php echo $value_color; ?>"><?php echo $fee['value']; ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                        <div class="border-t border-gray-200 !my-2"></div>
                                                        <div class="flex justify-between font-medium text-sm">
                                                            <span class="text-gray-800">Total:</span>
                                                            <span class="text-gray-900"><?php echo $formatted_order_total; ?></span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                                    <div class="flex items-center justify-end flex-wrap gap-2 min-w-[150px]">
                                                        <button type="button" class="toggle-details-btn inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" title="Ver Detalhes">
                                                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                                        </button>
                                                         <a href="#configs-entradas-<?php echo $order_id; ?>" uk-toggle title="Configurações" class="inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                         </a>

<button id="dropdownMenuIconButton-<?php echo $order_id; ?>" data-dropdown-toggle="dropdownDots-<?php echo $order_id; ?>" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-slate-100 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-50" type="button">
<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
<path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
</svg>
</button>

<!-- Dropdown menu -->
<div id="dropdownDots-<?php echo $order_id; ?>" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44">
    <div class="px-4 py-3 text-sm text-gray-900 text-left">
      <div>Configurações</div>
      <div class="font-medium truncate text-xs">PEDIDO: #<?php echo $order_id; ?></div>
    </div>    
    <ul class="py-2 text-sm text-gray-700 text-left" aria-labelledby="dropdownMenuIconButton-<?php echo $order_id; ?>">
      <li>
        <a href="<?php echo esc_url(amedis_get_edit_order_url($order_id)); ?>" class="block px-4 py-2 hover:bg-gray-100" title="Copiar Informações">Editar Pedido</a>
      </li>        
      <li>
        <a href="#" data-id="<?php echo $order_id; ?>" class="copyInfos block px-4 py-2 hover:bg-gray-100" title="Copiar Informações">Extração/Entrega</a>
      </li>
      <li>
        <a href="#" data-order-id="<?php echo $order_id; ?>" class="copyPacienteInfo block px-4 py-2 hover:bg-gray-100">Enviar Paciente</a>
      </li>
    </ul>
    <div class="py-2 text-left">

        <?php if (!$is_paid): ?>
            <a href="#!<?php // echo esc_url($pay_url); ?>" title="Realizar Pagamento" class="block px-4 py-2 text-sm text-green-700 hover:bg-gray-100">
                Realizar Pagamento
            </a>
        <?php else: ?>
            <a href="#!" title="Realizar Pagamento" class="cursor-not-allowed block px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                Realizar Pagamento
            </a>
        <?php endif; ?>
    </div>    
</div>


                                                    </div>
                                                </td>
                                            </tr>

                                            <div id="configs-entradas-<?php echo $order_id; ?>" class="uk-flex-top" uk-modal>
                                                <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg">
                                                    <button class="uk-modal-close-outside uk-close-large" type="button" uk-close></button>
                                                    <h3 class="text-lg font-medium text-center mb-4">Configurações do Pedido #<?php echo $order_id; ?></h3>
                                                    <form method="POST" class="space-y-6 configs-form">
                                                        <input type="hidden" name="action" value="save_order_custom_meta">
                                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                                        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('save_order_meta_nonce'); ?>">
                                                        
                                                        <!-- Seção Pagamento -->
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Pagamento</h4>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <div>
                                                                    <label for="forma_pagamento_woo_<?php echo $order_id; ?>" class="block mb-2 text-sm font-medium text-gray-900">Forma de Pagamento</label>
                                                                    <select id="forma_pagamento_woo_<?php echo $order_id; ?>" name="forma_pagamento_woo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-300 block w-full p-2.5">
                                                                        <option value="">Selecione</option>
                                                                        <?php foreach ($opcoes_forma_pagamento as $value => $label) : ?>
                                                                            <option value="<?php echo esc_attr($value); ?>" <?php selected($forma_pagamento_woo, $value); ?>><?php echo esc_html($label); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label for="order_status_<?php echo $order_id; ?>" class="block mb-2 text-sm font-medium text-gray-900">Status do Pedido (Woo)</label>
                                                                    <select id="order_status_<?php echo $order_id; ?>" name="order_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-300 block w-full p-2.5">
                                                                        <?php
                                                                        $statuses = wc_get_order_statuses();
                                                                        foreach ($statuses as $status_slug => $status_name) {
                                                                            // O status do pedido não tem prefixo 'wc-', mas as chaves de wc_get_order_statuses() têm.
                                                                            echo '<option value="' . esc_attr($status_slug) . '" ' . selected('wc-' . $order_status_slug, $status_slug, false) . '>' . esc_html($status_name) . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Seção Extração -->
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Extração</h4>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <div>
                                                                    <label for="extracao_<?php echo $order_id; ?>" class="block mb-2 text-sm font-medium text-gray-900">Status da Extração</label>
                                                                    <select id="extracao_<?php echo $order_id; ?>" name="extracao" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-300 block w-full p-2.5">
                                                                        <option value="">Selecione</option>
                                                                        <?php foreach ($opcoes_extracao as $value => $label) : ?>
                                                                            <option value="<?php echo esc_attr($value); ?>" <?php selected($extracao, $value); ?>><?php echo esc_html($label); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Seção Entrega -->
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Entrega</h4>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <div>
                                                                    <label for="forma_entrega_woo_<?php echo $order_id; ?>" class="block mb-2 text-sm font-medium text-gray-900">Forma de Entrega</label>
                                                                    <select id="forma_entrega_woo_<?php echo $order_id; ?>" name="forma_entrega_woo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-300 block w-full p-2.5">
                                                                        <option value="">Selecione</option>
                                                                        <?php foreach ($opcoes_forma_entrega as $value => $label) : ?>
                                                                            <option value="<?php echo esc_attr($value); ?>" <?php selected($forma_entrega_woo, $value); ?>><?php echo esc_html($label); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label for="status_entrega_<?php echo $order_id; ?>" class="block mb-2 text-sm font-medium text-gray-900">Status da Entrega</label>
                                                                    <select id="status_entrega_<?php echo $order_id; ?>" name="status_entrega" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-300 block w-full p-2.5">
                                                                        <option value="">Selecione</option>
                                                                        <?php foreach ($opcoes_status_entrega as $value => $label) : ?>
                                                                            <option value="<?php echo esc_attr($value); ?>" <?php selected($status_entrega, $value); ?>><?php echo esc_html($label); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="salvar-btn w-full text-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 uppercase">
                                                            Salvar Configurações do Pedido #<?php echo $order_id; ?>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <?php
                                        endforeach;
                                    else :
                                    ?>
                                        <tr class="bg-white">
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mb-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375 0 0 1 .75 0Z" />
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
                                                    <p class="text-gray-500">Não há pedidos para exibir no momento.</p>
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

<div id="offcanvas-usage" uk-offcanvas="overlay: true">
    <div class="uk-offcanvas-bar">
        <button class="uk-offcanvas-close" type="button" uk-close></button>
        <h3>Filtros</h3>
        <p>Conteúdo do Offcanvas para filtros.</p>
    </div>
</div>

<script>
    // JSON com todos os dados dos detalhes dos pedidos, passado do PHP
    var allOrderDetails = <?php echo json_encode($all_order_details); ?>;
    console.log('[DEBUG JS] allOrderDetails (after PHP encode):', allOrderDetails);
</script>

<script>
    jQuery(document).ready(function($) {
        var table = $('#pedidosTable').DataTable({
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
            order: [[0, 'desc']],
        });

        // =====================================================================
        // Handlers da Tabela Principal
        // =====================================================================

        $('#pedidosTable tbody').on('click', '.copy-phone-btn', function(e) {
            e.preventDefault();
            var phone = $(this).data('phone');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(phone).then(function() {
                    UIkit.notification({ message: '<span uk-icon=\'icon: check\'></span> Telefone copiado: ' + phone, status: 'success', pos: 'top-center', timeout: 2000 });
                }, function(err) {
                    UIkit.notification({ message: '<span uk-icon=\'icon: warning\'></span> Falha ao copiar o telefone.', status: 'danger', pos: 'top-center', timeout: 3000 });
                });
            } else {
                UIkit.notification({ message: '<span uk-icon=\'icon: warning\'></span> Seu navegador não suporta esta funcionalidade.', status: 'warning', pos: 'top-center', timeout: 3000 });
            }
        });

        $('#pedidosTable tbody').on('click', '.copyInfos', function (e) {
            e.preventDefault();
            var orderId = $(this).data('id');
            var data = allOrderDetails[orderId];
            if (data) {
let text = `===================================
INFORMAÇÕES DO PACIENTE
===================================\n
*ID do Pedido:* ${data.order_id}\n
*Nome do Paciente:* ${data.customer_name}
`;
                if (data.tipo_associacao !== 'assoc_paciente' && data.nome_completo_respon) { text += `*Nome do Responsável:* ${data.nome_completo_respon}
`; }
                text += `*Telefone:* ${data.billing_phone}\n
*Diagnóstico:* ${data.diagnostico}
*Usa Medicação:* ${data.usa_medicacao}
*Qual Medicação:* ${data.qual_medicacao}
*Fez uso de Cannabis (Escolha):* ${data.fez_uso_canabis_escolha}
\n===================================
DOCUMENTOS
===================================\n
`;
                let receitasInfo = data.receitas_data && data.receitas_data.length ? data.receitas_data.map(r => `Título: ${r.post_title || `Receita #${r.id}`}, Venc: ${r.data_vencimento || 'N/A'}, Link: ${r.receita_href || 'N/A'}`).join('\n') : 'Nenhuma';
                text += `*Receitas:*
${receitasInfo}
\n===================================
PRODUTOS
===================================\n
`;
                // Formatar produtos um abaixo do outro
                let produtosInfo = data.items && data.items.length ? data.items.map(item => `${item.name} ( Qtd: ${item.quantity} )`).join('\n') : '';
                text += `${produtosInfo}

===================================
ENTREGA
===================================\n
*Endereço:*
`;
                // Remover tags HTML do endereço e substituir por quebras de linha
                let enderecoFormatado = data.formatted_shipping_address.replace(/<br\/?>/gi, '\n');
                // Remover quebras de linha extras e ajustar formatação
                enderecoFormatado = enderecoFormatado.replace(/\n+/g, '\n');
                text += `${enderecoFormatado}
\n===================================
OBSERVAÇÕES
===================================\n
*Observações Gerais:* ${data.observacoes_user}
`;
                navigator.clipboard.writeText(text).then(() => UIkit.notification({ message: '<span uk-icon=\'icon: check\'></span> Informações copiadas!', status: 'success', pos: 'top-center', timeout: 2000 })).catch(err => console.error('Erro ao copiar texto: ', err));
            }
        });

        $('#pedidosTable tbody').on('click', 'button.toggle-details-btn', function () {
            var button = $(this);
            var tr = button.closest('tr');
            var row = table.row(tr);
            var orderId = tr.data('order-id');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('details-shown');
                button.find('svg').removeClass('rotate-180');
            } else {
                row.child(format(orderId)).show();
                tr.addClass('details-shown');
                button.find('svg').addClass('rotate-180');
            }
        });

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

        // =====================================================================
        // Handlers do Modal de Configurações
        // =====================================================================

        $(document).on('shown', '[id^="configs-entradas-"]', function () {
            const modal = $(this);
            const form = modal.find('.configs-form');
            const saveButton = form.find('.salvar-btn');
            
            const modalId = modal.attr('id');
            const orderId = modalId.split('-').pop();
            const currentData = allOrderDetails[orderId];

            if (currentData) {
                form.find('select[name="forma_pagamento_woo"]').val(currentData.forma_pagamento_woo);
                form.find('select[name="order_status"]').val('wc-' + currentData.order_status_slug);
                form.find('select[name="extracao"]').val(currentData.extracao);
                form.find('select[name="forma_entrega_woo"]').val(currentData.forma_entrega_woo);
                form.find('select[name="status_entrega"]').val(currentData.status_entrega);
            }

            form.off('submit').on('submit', function(e) {
                e.preventDefault();
                
                var originalButtonText = saveButton.html();
                saveButton.prop('disabled', true).html('<span uk-spinner="ratio: 0.8"></span> Salvando...');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success && response.data.updated_data) {
                            UIkit.notification({ message: '<span uk-icon=\'icon: check\'></span> ' + response.data.message, status: 'success', pos: 'top-center', timeout: 2500 });

                            const newData = response.data.updated_data;
                            const orderId = newData.order_id;
                            const rowSelector = `tr[data-order-id="${orderId}"]`;
                            const row = table.row(rowSelector);
                            const isAdmin = $(row.node()).find('.copyInfos').length > 0;

                            // Atualiza o objeto global para refletir os dados mais recentes
                            allOrderDetails[orderId] = newData;
                            console.debug('[DEBUG] Dados recebidos do backend para o pedido', orderId, newData);

                            if (row.length && row.node()) {
                                // Atualiza apenas a célula de status (coluna 2, índice 1)
                                $(row.node()).find('td').eq(1).html(renderStatusCell(newData));

                                // Atualiza as informações extras (cidade/estado e alerta de receitas)
                                if (typeof window.DashboardWooCommerce !== 'undefined' && window.DashboardWooCommerce.OrderManager) {
                                    window.DashboardWooCommerce.OrderManager.updateInfosExtra(orderId, newData);
                                } else {
                                    updateInfosExtra(orderId, newData);
                                }

                                // Opcional, mas bom: redesenha a linha para que o DataTables reconheça a mudança
                                // sem alterar os dados de outras colunas. O 'false' impede a paginação.
                                row.invalidate().draw(false);

                                UIkit.modal(form.closest('.uk-modal')).hide();
                                console.debug('[DEBUG] Célula de status atualizada com sucesso para o pedido:', orderId);
                            } else {
                                console.warn('[DEBUG] Linha não encontrada para o pedido:', orderId, '. Recarregando página como fallback.');
                                setTimeout(() => location.reload(), 1000);
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) { 
                        UIkit.notification({ message: '<span uk-icon=\'icon: close\'></span> Erro de conexão ao salvar.', status: 'danger', pos: 'top-center' }); 
                    },
                    complete: function() { 
                        saveButton.prop('disabled', false).html(originalButtonText); 
                    }
                });
            });
        });

        function renderStatusCell(data) {
            // Ordem e labels dos status
            const statusOrder = [
                { key: 'status_text', label: 'STATUS PGTO:', dot: true },
                { key: '_forma_pagamento_woo', label: 'FORMA PGTO:' },
                { key: '_extracao', label: 'EXTRAÇÃO:' },
                { key: '_forma_entrega_woo', label: 'ENTREGA:' },
                { key: '_status_entrega', label: 'STATUS ENTREGA:' }
            ];
            let html = '<div class="flex flex-col gap-1">';
            statusOrder.forEach(item => {
                if (item.key === 'status_text') {
                    html += `<div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">${item.label}</span>
                        <div class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                            <span class="h-2 w-2 rounded-full ${data.status_dot_color}"></span>
                            <span>${data.status_text ? data.status_text : '<span class=\"text-gray-400\">Não definido</span>'}</span>
                        </div>
                    </div>`;
                } else {
                    // Busca o valor no objeto operational_statuses
                    let badge = data.operational_statuses && data.operational_statuses[item.key] ? data.operational_statuses[item.key].label : null;
                    let badgeClass = data.operational_statuses && data.operational_statuses[item.key] ? 'text-gray-700' : 'text-gray-400';
                    html += `<div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">${item.label}</span>
                        <span class="text-xs font-semibold ${badgeClass}">${badge ? badge : 'Não definido'}</span>
                    </div>`;
                }
            });
            html += '</div>';
            return html;
        }

        function renderValueCell(data) {
            let feesHtml = '';
            if (data.order_fees && data.order_fees.length > 0) {
                data.order_fees.forEach(fee => {
                    const is_discount = String(fee.value).includes('-');
                    const value_color = is_discount ? 'text-green-600' : 'text-gray-700';
                    feesHtml += `<div class="flex justify-between"><span class="text-gray-500">${fee.name}:</span><span class="font-medium ${value_color}">${fee.value}</span></div>`;
                });
            }
            return `<div class="text-xs space-y-1 w-full min-w-[150px]">
                <div class="flex justify-between">
                    <span class="text-gray-500">Subtotal:</span>
                    <span class="font-medium text-gray-700">${data.subtotal_display}</span>
                </div>
                ${feesHtml}
                <div class="border-t border-gray-200 !my-2"></div>
                <div class="flex justify-between font-medium text-sm">
                    <span class="text-gray-800">Total:</span>
                    <span class="text-gray-900">${data.formatted_order_total}</span>
                </div>
            </div>`;
        }

        function updateInfosExtra(orderId, data) {
            const infosExtraContainer = $(`.infos_extra[data-order-id="${orderId}"]`);
            if (infosExtraContainer.length === 0) {
                console.warn('[DEBUG] Container infos_extra não encontrado para pedido:', orderId);
                return;
            }

            console.debug('[DEBUG] updateInfosExtra chamado para pedido:', orderId, 'com dados:', data);

            let infosHtml = '';
            
            // Adicionar cidade e estado se disponível
            if (data.cidade_estado && data.cidade_estado.trim() !== '') {
                console.debug('[DEBUG] Adicionando cidade_estado:', data.cidade_estado);
                infosHtml += `
                    <div class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <span>${data.cidade_estado}</span>
                    </div>
                `;
            }
            
            // Adicionar alerta de receitas se não tiver receitas
            if (!data.tem_receitas) {
                console.debug('[DEBUG] Adicionando alerta sem receitas');
                infosHtml += `
                    <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <span>Sem receitas</span>
                    </div>
                `;
            }
            
            // Atualizar o conteúdo
            infosExtraContainer.html(infosHtml);
            console.debug('[DEBUG] Informações extras atualizadas para pedido:', orderId, 'HTML final:', infosHtml);
        }

        function renderActionsCell(data, isAdmin) {
            let payButtonHtml = '';
            if (!data.is_paid) {
                payButtonHtml = `<a href="${data.pay_url}" title="Realizar Pagamento" class="inline-flex items-center justify-center p-2 border border-green-300 shadow-sm rounded-full text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                </a>`;
            } else {
                payButtonHtml = `<span class="inline-flex items-center justify-center p-2 font-medium text-green-800 bg-green-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </span>`;
            }
            let copyButtonHtml = '';
            if (isAdmin) {
                copyButtonHtml = `<a href="#" data-id="${data.order_id}" title="Copiar Informações" class="copyInfos inline-flex items-center justify-center p-2 border border-orange-300 shadow-sm rounded-full text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                </a>`;
            }
            return `<div class="flex items-center justify-end flex-wrap gap-2 min-w-[150px]">
                <button type="button" class="toggle-details-btn inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" title="Ver Detalhes">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <a href="#configs-entradas-${data.order_id}" uk-toggle title="Configurações" class="inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                </a>
                ${copyButtonHtml}
                <a href="/editar-pedido/?order_id=${data.order_id}" title="Editar Pedido" class="inline-flex items-center justify-center p-2 border border-blue-300 shadow-sm rounded-full text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                </a>
                ${payButtonHtml}
            </div>`;
        }

        function format(orderId) {
            const data = allOrderDetails[orderId];
            if (!data) {
                return '<div class="p-4 text-center text-gray-500">Detalhes não encontrados para este pedido.</div>';
            }

            const createReceitasList = (receitasHtml, diagnostico) => {
                if (!receitasHtml) {
                    return '';
                }
                const listClass = (diagnostico && diagnostico.trim() !== '') ? 'mt-4 pt-4 border-t border-gray-200' : '';
                return `<div class="${listClass}">${receitasHtml}</div>`;
            };

            const createItemsList = (items) => {
                if (!items || items.length === 0) {
                    return `<p class="text-sm text-gray-500 bg-gray-100 p-3 rounded-lg border border-gray-200">Nenhum produto no pedido.</p>`;
                }
                return items.map(item => `
                    <div>
                        <div class="font-medium text-gray-800 text-sm">${item.name}</div>
                        <div class="text-xs text-gray-500">Qtd: ${item.quantity} &times; ${item.subtotal}</div>
                    </div>
                `).join('');
            };
            
            const diagnosticoHtml = (data.diagnostico && data.diagnostico.trim() !== '') ? `<div class="bg-blue-50 p-3 border border-blue-200 rounded-lg text-xs text-blue-800"><span class="font-medium block mb-1">Diagnóstico:</span><p class="text-sm">${data.diagnostico}</p></div>` : '';
            const receitasHtml = createReceitasList(data.receitas_html, data.diagnostico);
            const itemsHtml = createItemsList(data.items);
            const enderecoHtml = (data.shipping_address && data.shipping_address.address_1) ? `<div class="text-gray-800 bg-white p-3 rounded-md border leading-relaxed">${data.formatted_shipping_address}</div>` : `<p class="text-gray-500 bg-gray-100 p-3 rounded-lg border border-gray-200">Endereço de entrega não fornecido.</p>`;
            
            let feesHtml = '';
            if (data.order_fees && data.order_fees.length > 0) {
                feesHtml = data.order_fees.map(fee => `<div class="flex justify-between text-gray-600"><span>${fee.name}:</span> <span>${fee.value}</span></div>`).join('');
            }
            const impostosHtml = data.total_tax > 0 ? `<div class="flex justify-between text-gray-600"><span>Impostos:</span> <span>${data.total_tax_display}</span></div>` : '';

            return `
                <div class="child-row-content p-6 md:p-8 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-8">
                        
                        <div class="space-y-4 text-sm">
                            <h4 class="text-xs font-medium uppercase text-gray-500">Diagnóstico / Receitas / Laudos</h4>
                            ${diagnosticoHtml}
                            ${receitasHtml}
                        </div>

                        <div class="space-y-4 text-sm">
                            <h4 class="text-xs font-medium uppercase text-gray-500">Produtos</h4>
                            <div class="space-y-3">${itemsHtml}</div>
                        </div>
                        
                        <div class="space-y-4 text-sm">
                            <h4 class="text-xs font-medium uppercase text-gray-500">Entrega & Financeiro</h4>
                            ${enderecoHtml}
                            <div class="space-y-1 text-xs p-3 bg-white rounded-md border mt-4">
                                <div class="flex justify-between text-gray-600"><span>Subtotal:</span> <span>${data.subtotal_display}</span></div>
                                ${feesHtml}
                                ${impostosHtml}
                                <div class="flex justify-between font-medium text-gray-900 text-sm pt-1 border-t mt-1"><span>Total:</span> <span>${data.formatted_order_total}</span></div>
                            </div>
                        </div>

                    </div>
                </div>
            `;
        }
    });

    // Manipulador de eventos para o link "Enviar Paciente"
    $(document).on('click', '.copyPacienteInfo', function(e) {
        e.preventDefault();
        
        const orderId = $(this).data('order-id');
        console.log('[DEBUG] Clicou em Enviar Paciente para pedido:', orderId);
        
        // Buscar os dados do pedido no objeto global
        const orderData = allOrderDetails[orderId];
        
        if (!orderData) {
            console.error('[ERROR] Dados do pedido não encontrados:', orderId);
            alert('Erro: Dados do pedido não encontrados.');
            return;
        }
        
        console.log('[DEBUG] Dados do pedido encontrados:', orderData);
        
        // Função auxiliar para limpar HTML e formatar texto
        function cleanHtmlText(htmlText) {
            if (!htmlText) return '';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlText;
            return (tempDiv.textContent || tempDiv.innerText || htmlText)
                .replace(/<br\s*\/?>/gi, '\n')
                .replace(/<[^>]*>/g, '')
                .replace(/&nbsp;/g, ' ')
                .replace(/&amp;/g, '&')
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&#82;&#36;/g, 'R$')
                .trim();
        }
        
        // Função auxiliar para extrair valor monetário limpo
        function cleanPrice(priceHtml) {
            if (!priceHtml) return '';
            return cleanHtmlText(priceHtml);
        }
        
        // Formatar o texto conforme solicitado
        let textoFormatado = `*PEDIDO #${orderId}*\n\n`;
        
        // Adicionar nome do cliente
        if (orderData.customer_name) {
            textoFormatado += `*Cliente:* ${orderData.customer_name}\n`;
        }
        
        // Adicionar responsável se existir
        if (orderData.nome_completo_respon && orderData.nome_completo_respon.trim() !== '') {
            textoFormatado += `*Responsável:* ${orderData.nome_completo_respon}\n`;
        }
        
        // Adicionar telefone
        if (orderData.billing_phone) {
            textoFormatado += `*Telefone:* ${orderData.billing_phone}\n`;
        }
        
        textoFormatado += `\n*PRODUTOS:*\n`;
        
        // Adicionar produtos com quantidade e valor unitário
        if (orderData.items && orderData.items.length > 0) {
            orderData.items.forEach(function(item) {
                textoFormatado += `• ${item.name}`;
                
                // Adicionar quantidade
                if (item.quantity && item.quantity > 0) {
                    textoFormatado += ` (Qtd: ${item.quantity})`;
                }
                
                // Adicionar valor unitário (que já vem no subtotal)
                if (item.subtotal) {
                    textoFormatado += ` - Valor Unit: ${cleanPrice(item.subtotal)}`;
                }
                
                // Calcular e adicionar total do item (valor unitário × quantidade)
                if (item.subtotal && item.quantity && item.quantity > 0) {
                    const subtotalLimpo = cleanPrice(item.subtotal);
                    // Extrair apenas números e vírgula/ponto do preço
                    const valorNumerico = subtotalLimpo.replace(/[^\d,.-]/g, '').replace(',', '.');
                    if (valorNumerico && !isNaN(parseFloat(valorNumerico))) {
                        const totalItem = (parseFloat(valorNumerico) * item.quantity).toFixed(2).replace('.', ',');
                        if (item.quantity > 1) {
                            textoFormatado += ` - Total: R$ ${totalItem}`;
                        }
                    }
                }
                
                textoFormatado += `\n`;
            });
        }
        
        // Adicionar endereço de entrega (limpar HTML)
        if (orderData.formatted_shipping_address) {
            const enderecoLimpo = cleanHtmlText(orderData.formatted_shipping_address);
            textoFormatado += `\n*ENDEREÇO DE ENTREGA:*\n${enderecoLimpo}\n`;
        }
        
        // Adicionar informações financeiras
        textoFormatado += `\n*VALORES:*\n`;
        
        if (orderData.subtotal_display) {
            textoFormatado += `Subtotal: ${cleanPrice(orderData.subtotal_display)}\n`;
        }
        
        // Adicionar taxas (frete, etc.) - corrigir undefined
        if (orderData.order_fees && orderData.order_fees.length > 0) {
            orderData.order_fees.forEach(function(fee) {
                if (fee.name && fee.value) {
                    textoFormatado += `${fee.name}: ${cleanPrice(fee.value)}\n`;
                }
            });
        }
        
        // Adicionar impostos se houver
        if (orderData.total_tax_display && orderData.total_tax > 0) {
            textoFormatado += `Impostos: ${cleanPrice(orderData.total_tax_display)}\n`;
        }
        
        // Adicionar total
        if (orderData.formatted_order_total) {
            textoFormatado += `*TOTAL: ${cleanPrice(orderData.formatted_order_total)}*\n`;
        }
        
        // Adicionar forma de pagamento
        if (orderData.operational_statuses && orderData.operational_statuses._forma_pagamento_woo) {
            textoFormatado += `\n*Forma de Pagamento:* ${orderData.operational_statuses._forma_pagamento_woo.label}\n`;
        } else if (orderData.forma_pagamento_woo) {
            textoFormatado += `\n*Forma de Pagamento:* ${orderData.forma_pagamento_woo}\n`;
        }
        
        // Adicionar informações operacionais
        if (orderData.operational_statuses && orderData.operational_statuses._forma_entrega_woo) {
            textoFormatado += `*Forma de Entrega:* ${orderData.operational_statuses._forma_entrega_woo.label}\n`;
        } else if (orderData.forma_entrega_woo) {
            textoFormatado += `*Forma de Entrega:* ${orderData.forma_entrega_woo}\n`;
        }
        
        if (orderData.operational_statuses && orderData.operational_statuses._status_entrega) {
            textoFormatado += `*Status da Entrega:* ${orderData.operational_statuses._status_entrega.label}\n`;
        } else if (orderData.status_entrega) {
            textoFormatado += `*Status da Entrega:* ${orderData.status_entrega}\n`;
        }
        
        // Adicionar extração
        if (orderData.operational_statuses && orderData.operational_statuses._extracao) {
            textoFormatado += `*Extração:* ${orderData.operational_statuses._extracao.label}\n`;
        } else if (orderData.extracao) {
            textoFormatado += `*Extração:* ${orderData.extracao}\n`;
        }
        
        // REMOVER RECEITAS E LAUDOS conforme solicitado
        // (Código das receitas removido)
        
        // Adicionar observações se houver
        if (orderData.observacoes_user && orderData.observacoes_user.trim() !== '') {
            textoFormatado += `\n*OBSERVAÇÕES:*\n${cleanHtmlText(orderData.observacoes_user)}\n`;
        }
        
        console.log('[DEBUG] Texto formatado:', textoFormatado);
        
        // Copiar para a área de transferência
        if (navigator.clipboard && window.isSecureContext) {
            // Método moderno para HTTPS
            navigator.clipboard.writeText(textoFormatado).then(function() {
                console.log('[SUCCESS] Texto copiado para a área de transferência');
                
                // Exibir notificação de sucesso
                showNotification('Informações do paciente copiadas com sucesso!', 'success');
                
            }).catch(function(err) {
                console.error('[ERROR] Erro ao copiar texto:', err);
                fallbackCopyTextToClipboard(textoFormatado);
            });
        } else {
            // Fallback para HTTP ou navegadores mais antigos
            fallbackCopyTextToClipboard(textoFormatado);
        }
    });
    
    // Função fallback para copiar texto
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                console.log('[SUCCESS] Texto copiado usando fallback');
                showNotification('Informações do paciente copiadas com sucesso!', 'success');
            } else {
                console.error('[ERROR] Falha ao copiar usando fallback');
                showNotification('Erro ao copiar informações. Tente novamente.', 'error');
            }
        } catch (err) {
            console.error('[ERROR] Erro no fallback:', err);
            showNotification('Erro ao copiar informações. Tente novamente.', 'error');
        }
        
        document.body.removeChild(textArea);
    }
    
    // Função para exibir notificações
    function showNotification(message, type = 'success') {
        // Remover notificação existente se houver
        const existingNotification = document.querySelector('.copy-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Criar nova notificação
        const notification = document.createElement('div');
        notification.className = `copy-notification fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        // Adicionar ao DOM
        document.body.appendChild(notification);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
</script>

<?php } ?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/dashboard-woocommerce.js"></script>

<?php get_footer(); ?>