<?php
/**
 * Template Name: Dashboard - Associados
 */
if (!current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');
get_template_part('header', 'user');
?>

<style>
    /* Estilos para DataTables e UI */
    #associadosTable_wrapper .dataTables_filter { z-index: 10; }
    #associadosTable_wrapper .dataTables_length,
    #associadosTable_wrapper .dataTables_info,
    #associadosTable_wrapper .dataTables_paginate { z-index: 5; }
    .overflow-x-auto { min-width: 0; overflow-x: auto; }
    #associadosTable { width: 100% !important; table-layout: fixed; }

    /* Larguras das colunas */
    #associadosTable th:nth-child(1), #associadosTable td:nth-child(1) { width: 35%; } /* Associado */
    #associadosTable th:nth-child(2), #associadosTable td:nth-child(2) { width: 30%; } /* Status */
    #associadosTable th:nth-child(3), #associadosTable td:nth-child(3) { width: 20%; } /* Contato */
    #associadosTable th:nth-child(4), #associadosTable td:nth-child(4) { width: 15%; } /* Ações */

    #associadosTable td { white-space: normal; word-wrap: break-word; }
    #associadosTable .action-buttons > div { flex-wrap: wrap; justify-content: flex-end; }

    /* Animação para Child-Row */
    .child-row-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
    .details-shown + tr > td > div.child-row-content { max-height: 1500px; transition: max-height 0.5s ease-in; }

    /* Estilização Zebra-Striping e Hover */
    #associadosTable tbody tr.odd { background-color: #ffffff; }
    #associadosTable tbody tr.even { background-color: #f8fafc; }
    #associadosTable tbody tr:hover { background-color: #f0fdf4; }
    tr > td[colspan="4"] { padding: 0; }
</style>

<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container">

        <div class="bg-white text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
            <div class="px-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <h1 class="leading-none font-semibold text-xl">Todos os Associados (<?php echo do_shortcode('[contagem_associados]'); ?>)</h1>
                        <span>Visão geral de todos os associados cadastrados.</span>
                    </div>
                    <div class="flex items-center justify-end">
                        <div class="flex gap-2 items-center">
                            <span class="flex items-center text-xs font-light uppercase text-gray-900 me-3"><span class="flex w-2.5 h-2.5 bg-sky-500 rounded-full me-1.5 shrink-0"></span>Pacinete ( <?php echo do_shortcode('[contagem_associados tipo="assoc_paciente"]'); ?> )</span>
                            <span class="flex items-center text-xs font-light uppercase text-gray-900 me-3"><span class="flex w-2.5 h-2.5 bg-purple-500 rounded-full me-1.5 shrink-0"></span>Responsável ( <?php echo do_shortcode('[contagem_associados tipo="assoc_respon"]'); ?> )</span>
                            <span class="flex items-center text-xs font-light uppercase text-gray-900 me-3"><span class="flex w-2.5 h-2.5 bg-pink-500 rounded-full me-1.5 shrink-0"></span>Tutor ( <?php echo do_shortcode('[contagem_associados tipo="assoc_tutor"]'); ?> ) </span>
                            <span class="flex items-center text-xs font-light uppercase text-gray-900 me-3"><span class="flex w-2.5 h-2.5 bg-gray-500 rounded-full me-1.5 shrink-0"></span>Colaborador ( <?php echo do_shortcode('[contagem_associados tipo="assoc_colab"]'); ?> ) </span>      
                        </div>                        
                        <a href="<?php echo bloginfo("url"); ?>/cadastro-paciente/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                            Novo Associado
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <div class="card card-border">
            <div class="card-body p-0">
                <div class="md:overflow-x-hidden">
                    <table id="associadosTable" class="amedis-datatable min-w-full divide-y divide-gray-200 md:overflow-x-hidden">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </span>
                                    <span>Associado</span>
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
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    <span>Contato</span>
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
                            $associados = obter_dados_associados();
                            $all_associados_details = [];

                            if (!empty($associados)) {
                                foreach ($associados as $associado) {
                                    $data = get_associado_display_data($associado->ID);
                                    extract($data);
                                    $all_associados_details[$user_id] = $data;
                            ?>
                                    <tr class="master-row transition-colors duration-200" data-associado-id="<?php echo esc_attr($user_id); ?>">
                                        <td class="px-6 py-4 align-middle">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 <?php echo $bg_badge; ?> <?php echo $txt_color; ?> rounded-lg flex items-center justify-center shadow-sm ring-1 ring-gray-200">
                                                        <?php echo $tipo_associacao_icon; ?>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-600 leading-tight mb-0.5 uppercase"><?php echo esc_html($nome_completo); ?></div>
                                                    <div class="text-xs font-medium <?php echo $txt_color; ?> mt-0.5"><?php echo esc_html($text_tipo_assoc); ?></div>
                                                    <?php if (!empty($nome_completo_respon)) : ?>
                                                        <div class="text-xs text-gray-500 mt-1.5 uppercase">Resp: <?php echo esc_html($nome_completo_respon); ?></div>
                                                    <?php endif; ?>
                                                    <div class="text-xs text-gray-500 mt-1.5">ID: #<?php echo esc_html($user_id); ?> | Desde: <?php echo esc_html($data_criacao); ?></div>

                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-left space-y-2 uppercase">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-medium text-gray-500">Status:</span>
                                                <div class="inline-flex items-center gap-2 text-xs font-semibold <?php echo $associado_ativo_cor; ?>">
                                                    <span class="h-2 w-2 rounded-full <?php echo $associado_ativo_dot; ?>"></span>
                                                    <span><?php echo $associado_ativo_texto; ?></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 pt-1.5">
                                                <span class="text-xs font-medium text-gray-500">Docs:</span>
                                                <div class="flex items-center gap-2">
                                                    <span title="Documento de Identidade" class="<?php echo $doc_rg_icon_class; ?>"><?php echo $doc_rg_icon; ?></span>
                                                    <span title="Comprovante de Endereço" class="<?php echo $doc_end_icon_class; ?>"><?php echo $doc_end_icon; ?></span>
                                                    <span title="Laudo Médico" class="<?php echo $doc_laudo_icon_class; ?>"><?php echo $doc_laudo_icon; ?></span>
                                                    <span title="Termo Associativo" class="<?php echo $doc_termo_icon_class; ?>"><?php echo $doc_termo_icon; ?></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 pt-1.5">
                                                <span class="text-xs font-medium text-gray-500">Receitas:</span>
                                                <div class="inline-flex items-center gap-2 text-xs <?php echo $receitas_count > 0 ? 'text-green-600' : 'text-gray-500'; ?>">
                                                    <span class="h-2 w-2 rounded-full <?php echo $receitas_count > 0 ? 'bg-green-500' : 'bg-gray-400'; ?>"></span>
                                                    <span><?php echo $receitas_count; ?> receita<?php echo $receitas_count != 1 ? 's' : ''; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            <?php if (!empty($telefone)) : ?>
                                                <div class="text-md text-gray-600 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                                    <a href="javascript:void(0);" class="copy-btn hover:underline" data-copy-text="<?php echo esc_attr($telefone); ?>" title="Copiar telefone"><?php echo esc_html($telefone); ?></a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($email)) : ?>
                                                <div class="text-md text-gray-600 flex items-center gap-2 mt-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                                    <a href="javascript:void(0);" class="copy-btn hover:underline" data-copy-text="<?php echo esc_attr($email); ?>" title="Copiar e-mail"><?php echo esc_html($email); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                            <div class="flex items-center justify-end flex-wrap gap-2 min-w-[150px]">
                                                <button type="button" class="toggle-details-btn inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" title="Ver Detalhes">
                                                    <?php echo amedis_get_icon_svg('chevron_down', 'w-4 h-4'); ?>
                                                </button>
                                                <a href="#configs-associado-<?php echo $user_id; ?>" uk-toggle title="Configurações" class="inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                                    <?php echo amedis_get_icon_svg('settings', 'w-4 h-4'); ?>
                                                </a>
                                                <a href="<?php echo esc_url(home_url('/editar-paciente/?editar_paciente=' . $user_id)); ?>" title="Editar Associado" class="inline-flex items-center justify-center p-2 border border-blue-300 shadow-sm rounded-full text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                    <?php echo amedis_get_icon_svg('edit', 'w-4 h-4'); ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mb-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum associado encontrado</h3>
                                            <p class="text-gray-500">Não há associados para exibir no momento.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals de Configuração -->
<?php
if (!empty($all_associados_details)) {
    foreach ($all_associados_details as $user_id => $data) {
?>
<div id="configs-associado-<?php echo $user_id; ?>" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg">
        <button class="uk-modal-close-outside uk-close-large" type="button" uk-close></button>
        <h3 class="text-lg font-medium text-center mb-4">Configurações de <br> <?php echo esc_html($data['nome_completo']); ?></h3>
        <form method="POST" class="space-y-6 configs-form">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Status</h4>
                <label class="inline-flex items-center w-full cursor-pointer">
                    <input type="checkbox" name="acf[field_66b252b04990d]" class="sr-only peer" <?php checked($data['associado_ativo'], '1'); ?>>
                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[-18px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900">Associado Ativo</span>
                </label>
            </div>

<?php if (current_user_can('administrator')) { ?>
<div class="mt-4 border-y border-slate-200 my-4 py-4">
    <h4 class="text-sm font-medium text-slate-600">Áreas Liberadas</h4>
</div>

<div class="mt-4 border-t border-slate-100 pt-4 mt-4">
    <div class="grid grid-cols-3 gap-3">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[pacientes]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['pacientes']) ? $data['areas_liberadas']['pacientes'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Pacientes</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[receitas]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['receitas']) ? $data['areas_liberadas']['receitas'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Receitas</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[prescritor]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['prescritor']) ? $data['areas_liberadas']['prescritor'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Prescritor</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[produtos]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['produtos']) ? $data['areas_liberadas']['produtos'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Produtos</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[entradas]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['entradas']) ? $data['areas_liberadas']['entradas'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Entradas</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[saidas]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['saidas']) ? $data['areas_liberadas']['saidas'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Saídas</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="areas_liberadas[relatorios]" class="sr-only peer" <?php checked(!empty($data['areas_liberadas']) && !empty($data['areas_liberadas']['relatorios']) ? $data['areas_liberadas']['relatorios'] : '', '1'); ?>>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-900">Relatórios</span>
        </label>        
    </div>
</div>
<?php } ?>

<div class="mt-4 border-y border-slate-200 my-4 py-4">
    <h4 class="text-sm font-medium text-slate-600">Alterar senha</h4>
</div>
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Observações</h4>
                <textarea name="acf[field_666c87e6b6c4d]" class="w-full border border-gray-300 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-4" rows="4"><?php echo esc_textarea($data['observacoes']); ?></textarea>
            </div>

            <button type="submit" class="salvar-btn w-full text-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 uppercase">
                Salvar Configurações
            </button>
        </form>
    </div>
</div>
<?php
    }
}
?>

<script>
    var allAssociadosDetails = <?php echo json_encode($all_associados_details); ?>;
</script>

<script>
jQuery(document).ready(function($) {
    var table = $('#associadosTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        dom: "<'flex items-center justify-between flex-wrap gap-2'lf>"+
             "<'flex justify-center items-center md:absolute md:left-[-50%] md:right-[-50%] md:top-[-5px]'p>"+
             "<'overflow-x-auto'tr>"+
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
        order: [[0, 'asc']],
    });

    // Copiar texto
    $('#associadosTable tbody').on('click', '.copy-btn', function(e) {
        e.preventDefault();
        var textToCopy = $(this).data('copy-text');
        navigator.clipboard.writeText(textToCopy).then(() => {
            UIkit.notification({ message: "<span uk-icon='icon: check'></span> Copiado: " + textToCopy, status: 'success', pos: 'top-center', timeout: 2000 });
        });
    });

    // Acordeão (Child Rows)
    $('#associadosTable tbody').on('click', 'button.toggle-details-btn', function () {
        var button = $(this);
        var tr = button.closest('tr');
        var row = table.row(tr);
        var associadoId = tr.data('associado-id');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('details-shown');
            button.find('svg').removeClass('rotate-180');
        } else {
            row.child(format(associadoId)).show();
            tr.addClass('details-shown');
            button.find('svg').addClass('rotate-180');
        }
    });

    // Submissão do formulário de configurações
    $(document).on('submit', '.configs-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var saveButton = form.find('.salvar-btn');
        var originalButtonText = saveButton.html();
        var associadoId = form.find('input[name="user_id"]').val();

        saveButton.prop('disabled', true).html("<span uk-spinner='ratio: 0.8'></span> Salvando...");

        var formData = new FormData(this);
        formData.append('action', 'save_config');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    UIkit.notification({ message: "<span uk-icon='icon: check'></span> " + response.data, status: 'success', pos: 'top-center' });
                    location.reload();
                } else {
                    UIkit.notification({ message: "<span uk-icon='icon: warning'></span> Erro: " + response.data, status: 'danger', pos: 'top-center' });
                }
            },
            error: function() {
                UIkit.notification({ message: "<span uk-icon='icon: close'></span> Erro de conexão.", status: 'danger', pos: 'top-center' });
            },
            complete: function() {
                saveButton.prop('disabled', false).html(originalButtonText);
                UIkit.modal(form.closest('.uk-modal')).hide();
            }
        });
    });

    function format(associadoId) {
        const data = allAssociadosDetails[associadoId];
        if (!data) return '<div class="p-4 text-center">Detalhes não encontrados.</div>';

        const createDocLink = (url, text, isMissing) => {
            const icon = isMissing ? data.icon_svg_x_circle : data.icon_svg_check_circle;
            const statusClass = isMissing ? 'text-red-500' : 'text-green-500';
            const textStatus = isMissing ? ' (Pendente)' : '';

            if (isMissing) {
                return `<li class="flex items-center gap-2 text-gray-500"><span class="${statusClass}">${icon}</span> ${text}${textStatus}</li>`;
            }
            return `<li class="flex items-center gap-2"><a href="${url}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1.5"><span class="${statusClass}">${icon}</span> ${text}</a></li>`;
        };

        const docsHtml = `
            <ul class="space-y-2 text-sm">
                ${createDocLink(data.comprova_rg_paciente_url, 'Documento de Identidade', !data.comprova_rg_paciente_url)}
                ${createDocLink(data.comprova_end_paciente_url, 'Comprovante de Endereço', !data.comprova_end_paciente_url)}                
                ${createDocLink(data.laudo_paciente_url, 'Laudo Médico', !data.laudo_paciente_url)}
                ${createDocLink(data.termo_associativo_url, 'Termo Associativo', !data.termo_associativo_url)}
            </ul>`;

        const receitasHtml = data.receitas_html || '<p class="text-sm text-gray-500">Nenhuma receita encontrada.</p>';
        const enderecoHtml = data.full_address ? `<div class="text-gray-800 bg-white p-3 rounded-md border leading-relaxed">${data.full_address}</div>` : '<p class="text-gray-500">Endereço não fornecido.</p>';
        const diagnosticoHtml = data.diagnostico ? `<div class="bg-blue-50 p-3 border border-blue-200 rounded-lg text-sm text-blue-800"><span class="font-medium block mb-1">Diagnóstico:</span><p>${data.diagnostico}</p></div>` : '';

        const personalInfoHtml = `
            <div class="text-sm space-y-3 mt-4">
                <div><strong class="font-medium text-gray-600">Data de Nascimento:</strong> ${data.data_nascimento || 'Não informado'}</div>
                <div><strong class="font-medium text-gray-600">CPF:</strong> ${data.cpf || 'Não informado'}</div>
                <div><strong class="font-medium text-gray-600">RG:</strong> ${data.rg || 'Não informado'}</div>
            </div>`;

        return `
            <div class="child-row-content p-6 md:p-8 bg-gray-50 border-t border-gray-100">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-8">
                    <div class="space-y-4 text-sm">
                        <h4 class="text-xs font-medium uppercase text-gray-500">Informações Pessoais</h4>
                        ${personalInfoHtml}
                        <h4 class="text-xs font-medium uppercase text-gray-500 mt-6">Documentos</h4>
                        ${docsHtml}
                        <h4 class="text-xs font-medium uppercase text-gray-500 mt-6">Receitas</h4>
                        <div class="space-y-2">${receitasHtml}</div>
                    </div>
                    <div class="space-y-4 text-sm">
                        <h4 class="text-xs font-medium uppercase text-gray-500">Quadro de Saúde</h4>
                        ${diagnosticoHtml}
                        <div class="text-sm space-y-3 mt-4">
                            <div><strong class="font-medium text-gray-600">Usa medicação?</strong> ${data.usa_medicacao === 'sim' ? data.qual_medicacao : 'Não'}</div>
                            <div><strong class="font-medium text-gray-600">Já usou Cannabis?</strong> ${data.fez_uso_canabis_escolha}</div>
                            <div><strong class="font-medium text-gray-600">Acompanhado por prescritor?</strong> ${data.medico_canabis_escolhas === 'sim' ? data.nome_profissional + ' (' + data.crm_profi + ')' : 'Não'}</div>
                        </div>
                    </div>
                    <div class="space-y-4 text-sm">
                        <h4 class="text-xs font-medium uppercase text-gray-500">Endereço</h4>
                        ${enderecoHtml}
                        <h4 class="text-xs font-medium uppercase text-gray-500 mt-6">Observações</h4>
                        <p class="text-sm text-gray-600">${data.observacoes || 'Nenhuma observação.'}</p>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>

<?php
get_footer();