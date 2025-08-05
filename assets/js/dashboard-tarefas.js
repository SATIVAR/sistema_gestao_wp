jQuery(document).ready(function($) {
    'use strict'; // Ativa o modo estrito para evitar erros comuns.

    // =================================================================
    // 1. INICIALIZAÇÃO E CACHE DE ELEMENTOS
    // =================================================================
    
    // Garante que todos os componentes da Flowbite (como modais) sejam inicializados.
    if (typeof initFlowbite === 'function') {
        initFlowbite();
    }

    // Armazena seletores jQuery em variáveis para melhor performance e legibilidade.
    const $searchInput = $('#search-input');
    const $statusFilter = $('#status-filter');
    const $resultsCounter = $('#results-counter');
    const $patientsGrid = $('#patients-grid');
    const $formNovaTarefa = $('#form-nova-tarefa');
    const $contadorTarefasPendentes = $('#contador-tarefas-pendentes');

    // =================================================================
    // 2. LÓGICA DE BUSCA E FILTRO DE PACIENTES (Função original preservada)
    // =================================================================
    
    let searchTimeout;

    function performSearch() {
        const $patientCards = $patientsGrid.find('.patient-card');
        const searchTerm = $searchInput.val().toLowerCase().trim();
        const statusFilter = $statusFilter.val();
        let visibleCount = 0;
        
        $patientCards.each(function() {
            const $card = $(this);
            const searchableText = $card.find('[data-searchable]').text().toLowerCase();
            const cardStatus = $card.data('status');
            
            const matchesSearch = !searchTerm || searchableText.includes(searchTerm);
            const matchesStatus = !statusFilter || cardStatus === statusFilter;
            const shouldShow = matchesSearch && matchesStatus;
            
            $card.toggle(shouldShow);
            if (shouldShow) {
                visibleCount++;
            }
        });
        
        $resultsCounter.html(`<span class="inline-flex items-center pl-1 rounded-full text-xs font-medium text-gray-500">${visibleCount} de ${$patientCards.length} associados</span>`);
    }

    // Aplica um debounce no input de busca para evitar execuções excessivas.
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 250); // Atraso de 250ms
    });

    // Executa a busca imediatamente ao mudar o filtro de status.
    $statusFilter.on('change', performSearch);
    
    // Execução inicial para definir a contagem correta ao carregar a página.
    performSearch();

});

jQuery(document).ready(function($) {
    if (typeof dashboardAjax === 'undefined') {
        console.error('dashboardAjax não está definido.');
        return;
    }
    
    console.log('Dashboard Tarefas carregado', dashboardAjax); // DEBUG

    // Funções globais para checklist
    window.updateChecklistJSON = function() {
        const items = [];
        $('.checklist-item').each(function() {
            const $item = $(this);
            const $checkbox = $item.find('.item-checkbox');
            const text = $item.find('.item-text').val().trim();
            if (text) {
                const checked = $checkbox.is(':checked');
                const itemData = {
                    id: $item.data('id'),
                    text: text,
                    completed: checked,
                    feito: checked, // Padroniza para o backend PHP
                    created_at: $item.data('created-at') || new Date().toISOString(),
                    completed_at: null
                };
                if (checked && !$item.data('was-completed')) {
                    itemData.completed_at = new Date().toISOString();
                    $item.data('was-completed', true);
                } else if (!checked && $item.data('was-completed')) {
                    $item.data('was-completed', false);
                } else if (checked && $item.data('completed-at')) {
                    itemData.completed_at = $item.data('completed-at');
                }
                items.push(itemData);
            }
        });
        $('#tarefa_checklist_json').val(JSON.stringify(items));
        console.log('Checklist atualizado:', JSON.stringify(items));
    };

    window.updateProgress = function() {
        const total = $('.checklist-item').length;
        const completed = $('.item-checkbox:checked').length;
        const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        $('#checklist-progress').text(`${completed} de ${total} itens concluídos`);
        $('#checklist-percentage').text(`${percentage}%`);
        $('#progress-bar').css('width', `${percentage}%`);
        
        const $feedback = $('#checklist-feedback');
        const $itensConcluidos = $('#itens-concluidos');
        
        if (completed > 0) {
            $feedback.removeClass('hidden');
            $itensConcluidos.empty();
            
            $('.item-checkbox:checked').each(function() {
                const $item = $(this).closest('.checklist-item');
                const texto = $(this).siblings('.item-text').val();
                const completedAt = $item.data('completed-at') || new Date().toISOString();
                const timeAgo = getTimeAgo(completedAt);
                
                if (texto.trim()) {
                    $itensConcluidos.append(`
                        <div class="flex items-center justify-between text-xs bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                            <div class="flex items-center gap-2 text-green-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">${texto}</span>
                            </div>
                            <span class="text-green-600 text-xs">${timeAgo}</span>
                        </div>
                    `);
                    
                    $item.data('completed-at', completedAt);
                }
            });
        } else {
            $feedback.addClass('hidden');
        }
    };

    // Função auxiliar para calcular tempo relativo
    function getTimeAgo(timestamp) {
        const now = new Date();
        const past = new Date(timestamp);
        const diffMs = now - past;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        
        if (diffMins < 1) return 'agora';
        if (diffMins < 60) return `${diffMins}min atrás`;
        if (diffHours < 24) return `${diffHours}h atrás`;
        
        return past.toLocaleDateString('pt-BR', { 
            day: '2-digit', 
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Sistema de Notificações
    function showNotification(message, type) {
        type = type || 'success';
        const icons = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        
        const notification = $('<div class="dashboard-notification ' + type + ' flex items-center gap-3 p-4 rounded-xl shadow-lg">' +
            '<div class="flex-shrink-0">' + icons[type] + '</div>' +
            '<span class="font-medium">' + message + '</span>' +
            '</div>');
        
        $('body').append(notification);
        setTimeout(function() { notification.addClass('show'); }, 10);
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() { notification.remove(); }, 300);
        }, 4000);
    }
    
    // Cache de elementos
    const $form = $('#form-nova-tarefa');
    const $contador = $('#contador-tarefas-pendentes');
    const $checkboxPessoal = $('#tarefa_pessoal_modal');
    const $associadoWrapper = $('#associado-wrapper');
    const $descCounter = $('#desc-counter');
    
    // Sistema Multi-Responsável REFATORADO
    const $responsaveisSelecionados = $('#responsaveis-selecionados');
    const $filtrarUsuarios = $('#filtrar-usuarios');
    let responsaveisSelecionados = [];
    
    // Filtrar usuários
    $filtrarUsuarios.on('input', function() {
        const filtro = $(this).val().toLowerCase().trim();
        
        $('.usuario-item').each(function() {
            const nomeUsuario = $(this).data('user-name');
            const email = $(this).find('.text-xs').text().toLowerCase();
            
            if (nomeUsuario.includes(filtro) || email.includes(filtro)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Selecionar/Deselecionar usuário
    $('.user-checkbox').on('change', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userEmail = $(this).data('user-email');
        const userAvatar = $(this).data('user-avatar');
        
        if ($(this).is(':checked')) {
            adicionarResponsavel({
                id: userId,
                name: userName,
                email: userEmail,
                avatar: userAvatar
            });
        } else {
            removerResponsavel(userId);
        }
    });
    
    // Adicionar responsável
    function adicionarResponsavel(user) {
        if (responsaveisSelecionados.indexOf(user.id) !== -1) return;
        
        responsaveisSelecionados.push(user.id);
        
        const $chip = $(`
            <div class="responsavel-chip flex items-center gap-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium" data-user-id="${user.id}">
                <img src="${user.avatar}" class="w-4 h-4 rounded-full">
                <span>${user.name}</span>
                <button type="button" class="remover-responsavel ml-1 text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `);
        
        $chip.find('.remover-responsavel').on('click', function() {
            removerResponsavel(user.id);
            $(`.user-checkbox[data-user-id="${user.id}"]`).prop('checked', false);
        });
        
        $responsaveisSelecionados.append($chip);
    }
    
    // Remover responsável
    function removerResponsavel(userId) {
        responsaveisSelecionados = responsaveisSelecionados.filter(id => id != userId);
        $(`.responsavel-chip[data-user-id="${userId}"]`).remove();
    }
    
    // Toggle tarefa pessoal
    $checkboxPessoal.on('change', function() {
        if ($(this).is(':checked')) {
            $associadoWrapper.slideUp(300);
            $associadoWrapper.find('select').prop('disabled', true);
            showNotification('Tarefa configurada como pessoal', 'info');
        } else {
            $associadoWrapper.slideDown(300);
            $associadoWrapper.find('select').prop('disabled', false);
        }
    });
    
    // Validação em tempo real
    $('#tarefa_titulo').on('input', function() {
        const $input = $(this);
        const $feedback = $input.siblings('.validation-feedback');
        const value = $input.val().trim();
        
        if (value.length < 3) {
            $input.addClass('border-red-300 focus:border-red-500 focus:ring-red-500');
            $feedback.removeClass('hidden').addClass('text-red-500').text('Título deve ter pelo menos 3 caracteres');
        } else {
            $input.removeClass('border-red-300 focus:border-red-500 focus:ring-red-500');
            $feedback.addClass('hidden');
        }
    });

    // Submit do formulário com confirmação
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Validação básica
        if (!$('#tarefa_titulo').val().trim()) {
            showNotification('Título da tarefa é obrigatório', 'error');
            return;
        }
        
        // Confirmação elegante
        if (!confirm('Confirma a criação desta tarefa?')) {
            return;
        }
        
        const $submitBtn = $('#criar-tarefa-btn');
        const originalText = $submitBtn.find('.submit-text').text();
        
        // Estado de loading
        $submitBtn.prop('disabled', true)
                .find('.submit-text').text('Criando...');
        
        // FORÇAR atualização do checklist antes do envio
        updateChecklistJSON();
        
        // Preparar dados
        let formData = $(this).serialize();
        
        // Adicionar responsáveis
        responsaveisSelecionados.forEach(function(id) {
            formData += '&responsaveis[]=' + encodeURIComponent(id);
        });
        
        // DEBUG: Verificar se checklist está sendo enviado
        console.log('Checklist JSON:', $('#tarefa_checklist_json').val());
        
        $.ajax({
            url: dashboardAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $contador.text(response.data.nova_contagem_pendentes);
                    showNotification(response.data.message, 'success');
                    $form[0].reset();
                    $descCounter.text('0');
                    
                    // Limpar checklist
                    $('#checklist-items').empty();
                    $('#tarefa_checklist_json').val('');
                    updateProgress();
                    
                    // Limpar responsáveis selecionados
                    responsaveisSelecionados = [];
                    $('.responsavel-chip:not(.owner)').remove();
                    $('.user-checkbox').prop('checked', false);
                    
                    // Fechar modal
                    setTimeout(function() {
                        const modalEl = document.getElementById('nova-tarefa-modal');
                        if (typeof Modal !== 'undefined') {
                            const modal = new Modal(modalEl);
                            modal.hide();
                        }
                    }, 1000);
                } else {
                    showNotification(response.data.message || 'Erro ao criar tarefa', 'error');
                }
            },
            error: function() {
                showNotification('Erro de comunicação. Tente novamente.', 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false)
                        .find('.submit-text').text(originalText);
            }
        });
    });
});

jQuery(document).ready(function($) {
    // Aguardar modal abrir para inicializar Select2
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const modal = document.getElementById('nova-tarefa-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    setTimeout(initAssociadoSelect2, 100);
                    // Ocultar scroll da página
                    document.body.style.overflow = 'hidden';
                } else {
                    // Restaurar scroll da página
                    document.body.style.overflow = '';
                }
            }
        });
    });
    
    const modalElement = document.getElementById('nova-tarefa-modal');
    if (modalElement) {
        observer.observe(modalElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    function initAssociadoSelect2() {
        const $select = $('#associado_relacionado_modal');
        if ($select.length && !$select.hasClass('select2-hidden-accessible')) {
            $select.select2({
                placeholder: 'Selecione um associado',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#nova-tarefa-modal'),
                templateResult: formatAssociado,
                templateSelection: formatAssociadoSelection,
                escapeMarkup: function(markup) { return markup; }
            });
        }
    }
    
    function formatAssociado(associado) {
        if (!associado.id) return associado.text;
        
        const $option = $(associado.element);
        const nomeCompleto = $option.data('nome-completo');
        const tipoAssociacao = $option.data('tipo-associacao');
        const cpf = $option.data('cpf');
        const cidade = $option.data('cidade');
        
        // Converter valor técnico em rótulo amigável
        const tipoLabel = getTipoAssociacaoLabel(tipoAssociacao);
        
        return $(`
            <div class="flex items-center gap-3 py-2">
                <div class="flex-1">
                    <div class="font-medium text-gray-900 text-sm">${nomeCompleto || associado.text}</div>
                    <div class="text-xs text-gray-500">
                        ${cpf ? 'CPF: ' + cpf : ''} ${cidade ? '• ' + cidade : ''}
                    </div>
                </div>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">${tipoLabel}</span>
            </div>
        `);
    }

    // Função para converter valores em rótulos
    function getTipoAssociacaoLabel(tipo) {
        const tipos = {
            'assoc_paciente': 'Paciente',
            'assoc_respon': 'Responsável pelo Paciente', 
            'assoc_tutor': 'Tutor de Animal',
            'assoc_colab': 'Colaborador'
        };
        
        return tipos[tipo] || tipo || 'Associado';
    }
    
    function formatAssociadoSelection(associado) {
        const $option = $(associado.element);
        const nomeCompleto = $option.data('nome-completo');
        return nomeCompleto || associado.text;
    }
});

// Funcionalidade do Checklist Aprimorada
jQuery(document).ready(function($) {
    // Adicionar item ao checklist
    $('#add-checklist-item').on('click', function() {
        const itemId = Date.now();
        const createdAt = new Date().toISOString();
        
        const itemHtml = `
            <div class="checklist-item bg-white rounded-lg border border-gray-200 p-3 group hover:border-gray-300 transition-all duration-200" 
                data-id="${itemId}" data-created-at="${createdAt}">
                <div class="flex items-center gap-3">
                    <input type="checkbox" class="item-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <input type="text" class="item-text flex-1 bg-transparent border-none focus:outline-none text-sm text-gray-700" placeholder="Digite o item do checklist...">
                    <button type="button" class="remove-item opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition-all duration-200 p-1 rounded hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        $('#checklist-items').append(itemHtml);
        updateChecklistJSON();
        updateProgress();
    });
    
    // Remover item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.checklist-item').fadeOut(200, function() {
            $(this).remove();
            updateChecklistJSON();
            updateProgress();
        });
    });
    
    // Atualizar status
    $(document).on('change', '.item-checkbox, .item-text', function() {
        updateChecklistJSON();
        updateProgress();
    });
});

// Editor de Texto
jQuery(document).ready(function($) {
    const editor = document.getElementById('editor-content');
    const hiddenTextarea = document.getElementById('tarefa_descricao');
    const counter = document.getElementById('desc-counter');
    
    // Função para executar comandos do editor
    function execCommand(command) {
        document.execCommand(command, false, null);
        editor.focus();
        updateContent();
        updateToolbar();
    }
    
    // Atualizar conteúdo no textarea oculto
    function updateContent() {
        const content = editor.innerHTML;
        const textContent = editor.textContent || editor.innerText || '';
        
        hiddenTextarea.value = content;
        counter.textContent = textContent.length;
        
        // Limitar caracteres
        if (textContent.length > 500) {
            counter.classList.add('text-red-500');
        } else {
            counter.classList.remove('text-red-500');
        }
    }
    
    // Atualizar estado dos botões da toolbar
    function updateToolbar() {
        $('.editor-btn').removeClass('active');
        
        if (document.queryCommandState('bold')) {
            $('[data-command="bold"]').addClass('active');
        }
        if (document.queryCommandState('italic')) {
            $('[data-command="italic"]').addClass('active');
        }
        if (document.queryCommandState('underline')) {
            $('[data-command="underline"]').addClass('active');
        }
    }
    
    // Event listeners para botões da toolbar
    $('.editor-btn').on('click', function(e) {
        e.preventDefault();
        const command = $(this).data('command');
        execCommand(command);
    });
    
    // Event listeners para o editor
    $(editor).on('input keyup mouseup', function() {
        updateContent();
        updateToolbar();
    });
    
    // Atalhos de teclado
    $(editor).on('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'b':
                    e.preventDefault();
                    execCommand('bold');
                    break;
                case 'i':
                    e.preventDefault();
                    execCommand('italic');
                    break;
                case 'u':
                    e.preventDefault();
                    execCommand('underline');
                    break;
            }
        }
        
        // Limitar entrada se exceder 500 caracteres
        const textContent = editor.textContent || editor.innerText || '';
        if (textContent.length >= 500 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
            e.preventDefault();
        }
    });
    
    // Prevenir colagem de conteúdo muito grande
    $(editor).on('paste', function(e) {
        setTimeout(() => {
            const textContent = editor.textContent || editor.innerText || '';
            if (textContent.length > 500) {
                editor.textContent = textContent.substring(0, 500);
                updateContent();
            }
        }, 10);
    });
    
    // Inicializar
    updateContent();
    updateToolbar();
});