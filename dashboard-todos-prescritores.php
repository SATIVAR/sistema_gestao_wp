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
Template Name: Prescritores - Todos 2
*/

if ( !is_user_logged_in() && !current_user_can('administrator') ) {
    // Se o usuário não estiver logado, redireciona para a home
    wp_redirect(home_url());
    exit;
}

get_header('zero'); ?>
<?php get_template_part('header', 'user') ?>
<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container">
        <div class="bg-white text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
            <div class="px-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <h1 class="leading-none font-semibold text-xl">Todos os Prescritores</h1>
                        <p class="text-muted-foreground text-sm">Visão geral de todos os prescritores cadastrados.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="<?php echo bloginfo("url"); ?>/cadastro-de-prescritor" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Novo Prescritor
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-border">
            <div class="card-body p-0">
                <div class="md:overflow-x-hidden">
                    <table id="prescritoresTable" class="amedis-datatable min-w-full divide-y divide-gray-200 md:overflow-x-hidden">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left">
                                    <div class="flex items-center">
                                    <span class="header-icon">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </span>
                                    <span>Prescritor</span>
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
                                    <div class="flex items-center">
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
                            $role = 'prescritor';
                            $user_query = new WP_User_Query(array('role' => $role));
                            $users = $user_query->get_results();
                            $all_prescritores_details = [];

                            if (!empty($users)) {
                                foreach ($users as $user) {
                                    $userID = $user->ID;
                                    $nome_completo_prescritor = get_field('nome_completo_prescritor', 'user_' . $userID) ?: 'Não informado';
                                    $especialidade = get_field('especialidade', 'user_' . $userID) ?: 'Não informado';
                                    $telefone_prescritor = get_field('telefone_prescritor', 'user_' . $userID);
                                    $email_prescritor = get_field('email_prescritor', 'user_' . $userID) ?: $user->user_email;
                                    $foto_site = get_field('foto_site', 'user_' . $userID) ?: get_avatar_url($userID);
                                    $prescritor_ativo = get_field('prescritor_amedis_ativo', 'user_' . $userID);
                                    $n_id_prescritor = get_field('n_id_prescritor', 'user_' . $userID);
                                    $estado_id_conselho = get_field('estado_id_conselho', 'user_' . $userID);
                                    $curriculo = get_field('curriculo', 'user_' . $userID);
                                    $infos_consulta = get_field('infos_consulta', 'user_' . $userID);
                                    $valor_consulta = get_field('valor_consulta', 'user_' . $userID);
                                    $modo_consulta = get_field('modo_consulta', 'user_' . $userID);
                                    $observacoes = get_field('prescritor_observacoes', 'user_' . $userID);

                                    $prescritor_ativo_texto = $prescritor_ativo ? 'Ativo' : 'Inativo';
                                    $prescritor_ativo_cor = $prescritor_ativo ? 'text-green-600' : 'text-red-600';
                                    $prescritor_ativo_dot = $prescritor_ativo ? 'bg-green-500' : 'bg-red-500';

                                    $all_prescritores_details[$userID] = [
                                        'nome_completo' => $nome_completo_prescritor,
                                        'especialidade' => $especialidade,
                                        'crm' => $n_id_prescritor . ' / ' . $estado_id_conselho,
                                        'curriculo' => $curriculo,
                                        'infos_consulta' => $infos_consulta,
                                        'valor_consulta' => $valor_consulta,
                                        'modo_consulta' => $modo_consulta,
                                        'observacoes' => $observacoes,
                                        'prescritor_ativo' => $prescritor_ativo,
                                        'icon_svg_check_circle' => amedis_get_icon_svg('check_circle', 'w-4 h-4'),
                                        'icon_svg_x_circle' => amedis_get_icon_svg('x_circle', 'w-4 h-4'),
                                    ];
                            ?>
                                    <tr class="master-row transition-colors duration-200" data-prescritor-id="<?php echo esc_attr($userID); ?>">
                                        <td class="px-6 py-4 align-middle">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <img class="h-12 w-12 rounded-lg object-cover shadow-sm ring-1 ring-gray-200" src="<?php echo esc_url($foto_site); ?>" alt="Foto de <?php echo esc_attr($nome_completo_prescritor); ?>">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-base font-medium text-gray-600 leading-tight mb-0.5"><?php echo esc_html($nome_completo_prescritor); ?></div>
                                                    <div class="text-xs font-medium text-gray-500 mt-0.5"><?php echo esc_html($especialidade); ?></div>
                                                    <div class="text-xs text-gray-500 mt-1.5">ID: #<?php echo esc_html($userID); ?> | CRM: <?php echo esc_html($n_id_prescritor . ' / ' . $estado_id_conselho); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-left space-y-2 uppercase">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-medium text-gray-500">Status:</span>
                                                <div class="inline-flex items-center gap-2 text-xs font-semibold <?php echo $prescritor_ativo_cor; ?>">
                                                    <span class="h-2 w-2 rounded-full <?php echo $prescritor_ativo_dot; ?>"></span>
                                                    <span><?php echo $prescritor_ativo_texto; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            <?php if (!empty($telefone_prescritor)) : ?>
                                                <div class="text-md text-gray-600 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                                    <a href="javascript:void(0);" class="copy-btn hover:underline" data-copy-text="<?php echo esc_attr($telefone_prescritor); ?>" title="Copiar telefone"><?php echo esc_html($telefone_prescritor); ?></a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($email_prescritor)) : ?>
                                                <div class="text-md text-gray-600 flex items-center gap-2 mt-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                                    <a href="javascript:void(0);" class="copy-btn hover:underline" data-copy-text="<?php echo esc_attr($email_prescritor); ?>" title="Copiar e-mail"><?php echo esc_html($email_prescritor); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-middle">
                                            <div class="flex items-center justify-end flex-wrap gap-2 min-w-[150px]">
                                                <button type="button" class="toggle-details-btn inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" title="Ver Detalhes">
                                                    <?php echo amedis_get_icon_svg('chevron_down', 'w-4 h-4'); ?>
                                                </button>
                                                <button type="button" class="copy-info-paciente-btn inline-flex items-center justify-center p-2 border border-orange-300 shadow-sm rounded-full text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200" title="Copiar Infos para Paciente" data-prescritor-id="<?php echo esc_attr($userID); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"></path></svg>
                                                </button>
                                                <a href="#configs-prescritor-<?php echo $userID; ?>" uk-toggle title="Configurações" class="inline-flex items-center justify-center p-2 border border-gray-300 shadow-sm rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                                    <?php echo amedis_get_icon_svg('settings', 'w-4 h-4'); ?>
                                                </a>
                                                <a href="<?php echo esc_url(home_url('/editar-prescritor/?editar_prescritor=' . $userID)); ?>" title="Editar Prescritor" class="inline-flex items-center justify-center p-2 border border-blue-300 shadow-sm rounded-full text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
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
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum prescritor encontrado</h3>
                                            <p class="text-gray-500">Não há prescritores para exibir no momento.</p>
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
if (!empty($all_prescritores_details)) {
    foreach ($all_prescritores_details as $user_id => $data) {
?>
<div id="configs-prescritor-<?php echo $user_id; ?>" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg">
        <button class="uk-modal-close-outside uk-close-large" type="button" uk-close></button>
        <h3 class="text-lg font-medium text-center mb-4">Configurações de <br> <?php echo esc_html($data['nome_completo']); ?></h3>
        <form method="POST" class="space-y-6 configs-form">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Status</h4>
                <label class="inline-flex items-center w-full cursor-pointer">
                    <input type="checkbox" name="acf[prescritor_amedis_ativo]" class="sr-only peer" <?php checked($data['prescritor_ativo'], '1'); ?>>
                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900">Prescritor Ativo</span>
                </label>
            </div>

            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-600 mb-2 pb-2 border-b">Observações</h4>
                <textarea name="acf[prescritor_observacoes]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4"><?php echo esc_textarea($data['observacoes']); ?></textarea>
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
    var allPrescritoresDetails = <?php echo json_encode($all_prescritores_details); ?>;
</script>

<script>
jQuery(document).ready(function($) {
    var table = $('#prescritoresTable').DataTable({
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
        order: [[0, 'asc']],
    });

    // Copiar texto genérico
    $('#prescritoresTable tbody').on('click', '.copy-btn', function(e) {
        e.preventDefault();
        var textToCopy = $(this).data('copy-text');
        navigator.clipboard.writeText(textToCopy).then(() => {
            UIkit.notification({ message: "<span uk-icon='icon: check'></span> Copiado: " + textToCopy, status: 'success', pos: 'top-center', timeout: 2000 });
        });
    });

    // Copiar informações formatadas para o paciente
    $('#prescritoresTable tbody').on('click', '.copy-info-paciente-btn', function(e) {
        e.preventDefault();
        var prescritorId = $(this).closest('tr').data('prescritor-id');
        var infoText = formatPrescritorInfoForCopy(prescritorId);
        navigator.clipboard.writeText(infoText).then(() => {
            UIkit.notification({ message: "<span uk-icon='icon: check'></span> Informações do prescritor copiadas!", status: 'success', pos: 'top-center', timeout: 2500 });
        });
    });

    // Acordeão (Child Rows)
    $('#prescritoresTable tbody').on('click', 'button.toggle-details-btn', function () {
        var button = $(this);
        var tr = button.closest('tr');
        var row = table.row(tr);
        var prescritorId = tr.data('prescritor-id');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('details-shown');
            button.find('svg').removeClass('rotate-180');
        } else {
            row.child(format(prescritorId)).show();
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

        saveButton.prop('disabled', true).html("<span uk-spinner='ratio: 0.8'></span> Salvando...");

        var formData = new FormData(this);
        formData.append('action', 'save_prescritor_config');

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

    function formatPrescritorInfoForCopy(prescritorId) {
        const data = allPrescritoresDetails[prescritorId];
        if (!data) return 'Informações não encontradas.';

        let infoText = `*${data.nome_completo}* / ${data.crm}\n`;
        infoText += `🩺 *Especialidade:* ${data.especialidade}\n`;
        infoText += `💵 *Valor:* ${data.valor_consulta}. Modo: ${data.modo_consulta}\n\n`;
        if (data.curriculo) {
            infoText += `${data.curriculo.replace(/<[^>]*>/g, '')}\n`; // Remove tags HTML
        }
        if (data.infos_consulta) {
            infoText += `${data.infos_consulta.replace(/<[^>]*>/g, '')}`;
        }
        return infoText;
    }

    function format(prescritorId) {
        const data = allPrescritoresDetails[prescritorId];
        if (!data) return '<div class="p-4 text-center">Detalhes não encontrados.</div>';

        const curriculoHtml = data.curriculo ? `<div class="bg-blue-50 p-3 border border-blue-200 rounded-lg text-sm text-blue-800"><span class="font-medium block mb-1">Currículo:</span><p>${data.curriculo}</p></div>` : '';
        const infosConsultaHtml = data.infos_consulta ? `<div class="bg-gray-50 p-3 border border-gray-200 rounded-lg text-sm text-gray-800"><span class="font-medium block mb-1">Informações da Consulta:</span><p>${data.infos_consulta}</p></div>` : '';
        const consultaHtml = `
            <div class="text-sm space-y-3 mt-4">
                <div><strong class="font-medium text-gray-600">Valor da Consulta:</strong> ${data.valor_consulta || 'Não informado'}</div>
                <div><strong class="font-medium text-gray-600">Modo da Consulta:</strong> ${data.modo_consulta || 'Não informado'}</div>
            </div>`;
        const observacoesHtml = data.observacoes ? `<p class="text-sm text-gray-600">${data.observacoes}</p>` : '<p class="text-gray-500">Nenhuma observação.</p>';

        return `
            <div class="child-row-content p-6 md:p-8 bg-gray-50 border-t border-gray-100">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-8 gap-y-8">
                    <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-8">
                        <div class="space-y-4 text-sm">
                            <h4 class="text-xs font-medium uppercase text-gray-500">Detalhes Profissionais</h4>
                            ${curriculoHtml}
                        </div>
                        <div class="space-y-4 text-sm">
                            <h4 class="text-xs font-medium uppercase text-gray-500">Consulta</h4>
                            ${infosConsultaHtml}
                            ${consultaHtml}
                        </div>
                    </div>
                    <div class="space-y-4 text-sm">
                        <h4 class="text-xs font-medium uppercase text-gray-500">Observações</h4>
                        ${observacoesHtml}
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
<?php 
get_footer();
?>