jQuery(document).ready(function($) {
    var perPage = 16; // Mesma quantidade definida no PHP

    function carregarPagina(page) {
        var search = $('#search-input').val();
        var status = $('#status-filter').val();

        $('#patients-grid').html('<div class="col-span-full text-center py-12"><span>Carregando...</span></div>');
        $('#pagination-controls').html('');

        $.ajax({
            url: dashboardAjax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'paginacao_associados',
                page: page,
                per_page: perPage,
                search: search,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    $('#patients-grid').html(response.data.html);
                    renderPagination(response.data.current_page, response.data.total_pages);
                    $('#results-counter').text('(' + response.data.total + ' resultados)');
                } else {
                    $('#patients-grid').html('<div class="col-span-full text-center py-12"><p>Erro ao carregar.</p></div>');
                }
            }
        });
    }

    function renderPagination(current, total) {
        if (total <= 1) {
            $('#pagination-controls').html('');
            return;
        }
        var html = '';
        if (current > 1) {
            html += '<button class="px-3 py-1 rounded bg-gray-200" data-page="' + (current - 1) + '">Anterior</button>';
        }
        for (var i = 1; i <= total; i++) {
            html += '<button class="px-3 py-1 rounded ' + (i === current ? 'bg-green-600 text-white' : 'bg-gray-100') + '" data-page="' + i + '">' + i + '</button>';
        }
        if (current < total) {
            html += '<button class="px-3 py-1 rounded bg-gray-200" data-page="' + (current + 1) + '">Próxima</button>';
        }
        $('#pagination-controls').html(html);
    }

    // Eventos
    $('#pagination-controls').on('click', 'button', function() {
        var page = $(this).data('page');
        carregarPagina(page);
    });

    $('#search-input, #status-filter').on('input change', function() {
        carregarPagina(1);
    });

    // Carregar primeira página ao iniciar
    carregarPagina(1);
});


$(document).on('click', '.abrir-modal-paciente', function() {
    var userId = $(this).data('user-id');
    // Limpa campos do modal antes de preencher
    $('#patient-modal .nome-completo, #patient-modal .cpf, #patient-modal .data-nascimento, #patient-modal .status, #patient-modal .email, #patient-modal .telefone, #patient-modal .cidade-estado, #patient-modal .doc-rg, #patient-modal .doc-endereco, #patient-modal .doc-laudo, #patient-modal .doc-termo, #patient-modal .resumo-status').text('');

    $.ajax({
        url: dashboardAjax.ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'buscar_dados_paciente',
            user_id: userId
        },
        success: function(response) {
            if (response.success) {
                var d = response.data;
                $('#patient-modal .nome-completo').text(d.nome_completo || '');
                $('#patient-modal .user-id').text('ID: ' + (d.user_id || ''));
                $('#patient-modal .data-nascimento').text(d.data_nascimento || '');
                $('#patient-modal .cpf').text(d.cpf || '');
                $('#patient-modal .rg').text(d.rg || '');
                $('#patient-modal .email').text(d.email || '');
                $('#patient-modal .telefone').text(d.telefone || '');
                $('#patient-modal .status').html('<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ' + (d.associado_ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + '">' + d.associado_ativo_texto + '</span>');
                $('#patient-modal .tipo-associacao').text(d.text_tipo_assoc || '');
                $('#patient-modal .endereco-completo').text(d.full_address || '');
                $('#patient-modal .observacoes').text(d.observacoes || '');
                // Documentos
// RG/CPF
$('#patient-modal .doc-rg').html(
    d.comprova_rg_paciente_url
        ? `<a href="${d.comprova_rg_paciente_url}" target="_blank" class="group flex items-center gap-1 text-green-600 hover:text-green-800 transition-colors">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Visualizar RG/CPF</span>
           </a>`
        : `<span class="text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Não enviado
           </span>`
);

// Comprovante de Endereço
$('#patient-modal .doc-endereco').html(
    d.comprova_end_paciente_url
        ? `<a href="${d.comprova_end_paciente_url}" target="_blank" class="group flex items-center gap-1 text-green-600 hover:text-green-800 transition-colors">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Visualizar Endereço</span>
           </a>`
        : `<span class="text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Não enviado
           </span>`
);

// Laudo Médico
$('#patient-modal .doc-laudo').html(
    d.laudo_paciente_url
        ? `<a href="${d.laudo_paciente_url}" target="_blank" class="group flex items-center gap-1 text-green-600 hover:text-green-800 transition-colors">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Visualizar Laudo</span>
           </a>`
        : `<span class="text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Não enviado
           </span>`
);

// Termo Associativo
$('#patient-modal .doc-termo').html(
    d.termo_associativo_url
        ? `<a href="${d.termo_associativo_url}" target="_blank" class="group flex items-center gap-1 text-green-600 hover:text-green-800 transition-colors">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Visualizar Termo</span>
           </a>`
        : `<span class="text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Não enviado
           </span>`
);
                // Dados clínicos
                $('#patient-modal .diagnostico').text(d.diagnostico || '');
                $('#patient-modal .medicacao').text(d.qual_medicacao || '');
                $('#patient-modal .fez-uso-canabis').text(d.fez_uso_canabis_escolha || '');
                $('#patient-modal .nome-profissional').text(d.nome_profissional || '');
                $('#patient-modal .crm-profissional').text(d.crm_profi || '');
                // Resumo status
                $('#patient-modal .resumo-status').html(
                    '<div><b>Tipo:</b> ' + (d.text_tipo_assoc || '-') + '</div>' +
                    '<div><b>Termos:</b> ' + (d.doc_termo_icon_class === 'text-green-500' ? 'OK' : 'Pendente') + '</div>'
                );
                // Exibe o modal
                $('#patient-modal').removeClass('hidden').addClass('flex');
            } else {
                alert('Erro ao buscar dados do paciente.');
            }
        }
    });
});

// Botão para fechar o modal
$(document).on('click', '[data-modal-hide="patient-modal"]', function() {
    $('#patient-modal').addClass('hidden').removeClass('flex');
});

// Lógica para abrir o modal de tarefa já setando o associado relacionado
$(document).on('click', '[data-modal-target="nova-tarefa-modal"][data-user-id]', function() {
    var userId = $(this).data('user-id');
    // Abre o modal (caso não esteja usando um framework/modal custom, pode ser só remover a classe 'hidden')
    $('#nova-tarefa-modal').removeClass('hidden').addClass('flex');
    // Seta o select do associado relacionado
    var $select = $('#associado_relacionado_modal');
    if ($select.length) {
        $select.val(userId).trigger('change');
    }
});