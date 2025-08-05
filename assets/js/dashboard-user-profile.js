/**
 * Dashboard User Profile JavaScript
 * Funcionalidades: navegação do dashboard, AJAX, modais
 */


// Dashboard functionality starts here


// assets/js/dashboard-user-profile.js
jQuery(document).ready(function ($) {
    
    // Verificação inicial para debug
    console.log('Dashboard User Profile JS loaded');
    console.log('userProfileAjax available:', typeof userProfileAjax !== 'undefined');
    if (typeof userProfileAjax !== 'undefined') {
        console.log('AJAX URL:', userProfileAjax.ajax_url);
        console.log('Nonce available:', !!userProfileAjax.nonce);
    }

    // Variáveis globais para paginação
    let currentPage = 1;
    let totalPages = 1;
    let currentStatus = '';

    // Variáveis para receitas
    let receitasCurrentPage = 1;
    let receitasTotalPages = 1;
    let receitasCurrentStatus = '';

    // Oculta todas as seções, exceto a primeira (Dashboard)
    $('.content-section').not(':first').addClass('hidden');

    // Manipulador de clique para os botões da sidebar
    $('.sidebar-menu-button').on('click', function (e) {
        e.preventDefault();

        // Obtém o alvo do atributo data-target
        var target = $(this).data('target');

        // Remove classes ativas de todos os botões
        $('.sidebar-menu-button').removeClass('text-white bg-gradient-to-r from-green-500 to-green-600 shadow-sm')
            .attr('data-active', 'false');

        // Adiciona classes ativas ao botão clicado
        $(this).attr('data-active', 'true');

        // Oculta todas as seções de conteúdo
        $('.content-section').addClass('hidden');

        // Mostra a seção de conteúdo alvo
        $(target).removeClass('hidden').addClass('animate-fade-in');

        // Carrega dados específicos da seção se necessário
        if (target === '#receitas' && $('#receitas-grid').length > 0) {
            loadReceitas();
        }

        // Fecha o sidebar mobile após a navegação
        if (window.innerWidth < 768) {
            $('.sidebar').attr('data-state', 'closed');
            $('#mobile-overlay').attr('data-state', 'closed');
            document.body.style.overflow = 'auto';
        }
    });

    // Manipulador para botões "Ver todos" no dashboard
    $('button[data-target]').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');

        // Remove classes ativas de todos os botões da sidebar
        $('.sidebar-menu-button').removeClass('text-white bg-gradient-to-r from-green-500 to-green-600 shadow-sm')
            .attr('data-active', 'false');

        // Adiciona classes ativas ao botão correspondente na sidebar
        $('.sidebar-menu-button[data-target="#' + target + '"]').attr('data-active', 'true');

        // Oculta todas as seções de conteúdo
        $('.content-section').addClass('hidden');

        // Mostra a seção de conteúdo alvo
        $('#' + target).removeClass('hidden').addClass('animate-fade-in');

        // Carrega dados específicos da seção se necessário
        if (target === 'receitas' && $('#receitas-grid').length > 0) {
            loadReceitas();
        }
        if (target === 'orders' && $('#orders-tbody').length > 0) {
            loadOrders();
        }
    });

    // Carrega os pedidos na inicialização apenas se os elementos existirem
    if ($('#orders-tbody').length > 0) {
        loadOrders();
    }

    // Carrega as receitas na inicialização apenas se os elementos existirem
    if ($('#receitas-grid').length > 0) {
        loadReceitas();
    }

    // Carrega os pedidos recentes do dashboard
    if ($('#recent-orders-list').length > 0) {
        loadRecentOrders();
    }

    // Carrega as receitas recentes do dashboard
    if ($('#recent-receitas-list').length > 0) {
        loadRecentReceitas();
    }

    // Filtro por status
    $('#order-status-filter').on('change', function () {
        currentStatus = $(this).val();
        currentPage = 1;
        loadOrders();
    });

    // Paginação
    $('#prev-page').on('click', function () {
        if (currentPage > 1) {
            currentPage--;
            loadOrders();
        }
    });

    $('#next-page').on('click', function () {
        if (currentPage < totalPages) {
            currentPage++;
            loadOrders();
        }
    });

    // Filtro por status das receitas
    $('#receitas-status-filter').on('change', function () {
        receitasCurrentStatus = $(this).val();
        receitasCurrentPage = 1;
        loadReceitas();
    });

    // Paginação das receitas
    $('#receitas-prev-page').on('click', function () {
        if (receitasCurrentPage > 1) {
            receitasCurrentPage--;
            loadReceitas();
        }
    });

    $('#receitas-next-page').on('click', function () {
        if (receitasCurrentPage < receitasTotalPages) {
            receitasCurrentPage++;
            loadReceitas();
        }
    });

    // Função para carregar pedidos
    function loadOrders() {
        // Verifica se userProfileAjax está definido
        if (typeof userProfileAjax === 'undefined') {
            $('#orders-tbody').html('<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erro: Configuração AJAX não encontrada.</td></tr>');
            return;
        }

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_user_orders',
                security: userProfileAjax.nonce,
                page: currentPage,
                per_page: 10,
                status: currentStatus
            },
            beforeSend: function () {
                $('#orders-tbody').html('<tr><td colspan="5" class="px-6 py-8 text-center text-muted-foreground">Carregando pedidos...</td></tr>');
            },
            success: function (response) {
                if (response.success) {
                    displayOrders(response.data);
                } else {
                    $('#orders-tbody').html('<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erro ao carregar pedidos: ' + response.data.message + '</td></tr>');
                }
            },
            error: function () {
                $('#orders-tbody').html('<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erro de comunicação. Tente novamente.</td></tr>');
            }
        });
    }

    // Função para exibir pedidos na tabela
    function displayOrders(data) {
        const orders = data.orders;
        totalPages = data.pages;

        if (orders.length === 0) {
            $('#orders-tbody').html('<tr><td colspan="5" class="px-6 py-8 text-center text-muted-foreground">Nenhum pedido encontrado.</td></tr>');
            $('#orders-info').text('Nenhum pedido encontrado');
            $('#prev-page, #next-page').prop('disabled', true);
            $('#page-info').text('0');
            return;
        }

        let html = '';
        orders.forEach(function (order) {
            const statusClass = getStatusClass(order.status);
            const statusBg = getStatusBg(order.status);

            html += `
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg ${statusBg}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-foreground">#${order.number}</div>
                                <div class="text-sm text-muted-foreground">${order.items_count} ${order.items_count === 1 ? 'item' : 'itens'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                            ${order.status_name}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                        ${order.date_relative}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                        ${order.total}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="openOrderModal(${order.id})" class="text-primary hover:text-primary/80 transition-colors">
                            Ver detalhes
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#orders-tbody').html(html);

        // Atualiza informações de paginação
        const start = (currentPage - 1) * 10 + 1;
        const end = Math.min(currentPage * 10, data.total);
        $('#orders-info').text(`Mostrando ${start}-${end} de ${data.total} pedidos`);
        $('#page-info').text(`${currentPage} de ${totalPages}`);

        // Atualiza botões de paginação
        $('#prev-page').prop('disabled', currentPage <= 1);
        $('#next-page').prop('disabled', currentPage >= totalPages);
    }

    // Função para obter classe CSS do status
    function getStatusClass(status) {
        const statusClasses = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'processing': 'bg-blue-100 text-blue-800',
            'on-hold': 'bg-orange-100 text-orange-800',
            'completed': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800',
            'refunded': 'bg-purple-100 text-purple-800',
            'failed': 'bg-red-100 text-red-800'
        };
        return statusClasses[status] || 'bg-gray-100 text-gray-800';
    }

    // Função para obter classe de background do ícone
    function getStatusBg(status) {
        const statusBgs = {
            'pending': 'bg-yellow-100 text-yellow-600',
            'processing': 'bg-blue-100 text-blue-600',
            'on-hold': 'bg-orange-100 text-orange-600',
            'completed': 'bg-green-100 text-green-600',
            'cancelled': 'bg-red-100 text-red-600',
            'refunded': 'bg-purple-100 text-purple-600',
            'failed': 'bg-red-100 text-red-600'
        };
        return statusBgs[status] || 'bg-gray-100 text-gray-600';
    }

    // Função para carregar receitas
    function loadReceitas() {
        // Verifica se userProfileAjax está definido
        if (typeof userProfileAjax === 'undefined') {
            $('#receitas-grid').html('<div class="col-span-full text-center text-red-600 py-8">Erro: Configuração AJAX não encontrada.</div>');
            return;
        }

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_user_receitas',
                security: userProfileAjax.nonce,
                page: receitasCurrentPage,
                per_page: 9,
                status: receitasCurrentStatus
            },
            beforeSend: function () {
                $('#receitas-grid').html('<div class="col-span-full flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>');
            },
            success: function (response) {
                if (response.success) {
                    displayReceitas(response.data);
                } else {
                    $('#receitas-grid').html('<div class="col-span-full text-center text-red-600 py-8">Erro ao carregar receitas: ' + response.data.message + '</div>');
                }
            },
            error: function () {
                $('#receitas-grid').html('<div class="col-span-full text-center text-red-600 py-8">Erro de comunicação. Tente novamente.</div>');
            }
        });
    }

    // Função para exibir receitas no grid
    function displayReceitas(data) {
        const receitas = data.receitas;
        receitasTotalPages = data.pages;

        if (receitas.length === 0) {
            $('#receitas-grid').html('<div class="col-span-full text-center text-muted-foreground py-8">Nenhuma receita encontrada.</div>');
            $('#receitas-info').text('Nenhuma receita encontrada');
            $('#receitas-prev-page, #receitas-next-page').prop('disabled', true);
            $('#receitas-page-info').text('0');
            return;
        }

        let html = '';
        receitas.forEach(function (receita) {
            const iconBg = getReceitaIconBg(receita.status);

            html += `
                <div class="rounded-xl border border-border bg-card p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg ${iconBg}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${receita.status_class}">
                            ${receita.status_text}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-foreground mb-2">${receita.prescritor_nome || 'Prescritor não informado'}</h3>
                    <p class="text-sm text-muted-foreground mb-4">
                        ${receita.prescritor_especialidade ? receita.prescritor_especialidade + ' • ' : ''}
                        ${receita.data_vencimento ? 'Válida até ' + receita.data_vencimento : 'Data não informada'}
                    </p>
                    <div class="space-y-2">
                        ${receita.cid_patologia ? `
                            <div class="text-sm">
                                <span class="font-medium text-foreground">Patologia:</span>
                                <span class="text-muted-foreground ml-1">${receita.cid_patologia}</span>
                            </div>
                        ` : ''}
                        ${receita.desc_curta ? `
                            <div class="text-sm">
                                <span class="font-medium text-foreground">Descrição:</span>
                                <span class="text-muted-foreground ml-1">${receita.desc_curta}</span>
                            </div>
                        ` : ''}
                        <div class="text-sm">
                            <span class="font-medium text-foreground">Data da receita:</span>
                            <span class="text-muted-foreground ml-1">${receita.data_receita || 'Não informada'}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border">
                        <div class="flex items-center justify-center gap-4">
                            ${receita.arquivo_receita ? `<a href="${receita.arquivo_receita}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">Receita</a>` : ''}
                            ${receita.arquivo_laudo ? `<a href="${receita.arquivo_laudo}" target="_blank" class="text-sm font-medium text-green-600 hover:text-green-800 transition-colors">Laudo</a>` : ''}
                            ${!receita.arquivo_receita && !receita.arquivo_laudo ? '<span class="text-sm text-muted-foreground">Nenhum arquivo disponível</span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        $('#receitas-grid').html(html);

        // Atualiza informações de paginação
        const start = (receitasCurrentPage - 1) * 9 + 1;
        const end = Math.min(receitasCurrentPage * 9, data.total);
        $('#receitas-info').text(`Mostrando ${start}-${end} de ${data.total} receitas`);
        $('#receitas-page-info').text(`${receitasCurrentPage} de ${receitasTotalPages}`);

        // Atualiza botões de paginação
        $('#receitas-prev-page').prop('disabled', receitasCurrentPage <= 1);
        $('#receitas-next-page').prop('disabled', receitasCurrentPage >= receitasTotalPages);
    }

    // Função para obter classe de background do ícone das receitas
    function getReceitaIconBg(status) {
        const statusBgs = {
            'ativa': 'bg-green-100 text-green-600',
            'expirando': 'bg-yellow-100 text-yellow-600',
            'expirada': 'bg-gray-100 text-gray-600'
        };
        return statusBgs[status] || 'bg-blue-100 text-blue-600';
    }

    // Função para carregar pedidos recentes do dashboard
    function loadRecentOrders() {
        // Verifica se userProfileAjax está definido
        if (typeof userProfileAjax === 'undefined') {
            $('#recent-orders-list').html('<div class="text-center text-red-600 py-4">Erro: Configuração AJAX não encontrada.</div>');
            return;
        }

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_recent_orders',
                security: userProfileAjax.nonce
            },
            beforeSend: function () {
                $('#recent-orders-list').html('<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div></div>');
            },
            success: function (response) {
                if (response.success) {
                    displayRecentOrders(response.data.orders);
                } else {
                    $('#recent-orders-list').html('<div class="text-center text-muted-foreground py-4">Erro ao carregar pedidos recentes.</div>');
                }
            },
            error: function () {
                $('#recent-orders-list').html('<div class="text-center text-red-600 py-4">Erro de comunicação. Tente novamente.</div>');
            }
        });
    }

    // Função para exibir pedidos recentes no dashboard
    function displayRecentOrders(orders) {
        if (orders.length === 0) {
            $('#recent-orders-list').html('<div class="text-center text-muted-foreground py-8">Nenhum pedido recente encontrado.</div>');
            return;
        }

        let html = '';
        orders.forEach(function (order) {
            const statusBadge = getRecentOrderStatusBadge(order.status);
            const iconBg = getRecentOrderIconBg(order.status);

            html += `
                <div class="flex items-center gap-4 p-4 rounded-lg border border-border hover:bg-muted/50 transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg ${iconBg}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="flex-1 cursor-pointer" onclick="openOrderModal(${order.id})">
                        <p class="text-sm font-medium text-foreground">Pedido #${order.number}</p>
                        <p class="text-xs text-muted-foreground">${order.items_count} ${order.items_count === 1 ? 'item' : 'itens'} • ${order.total}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge ${statusBadge.class}">${statusBadge.text}</span>
                        <button onclick="openOrderModal(${order.id})" class="print-btn p-2 text-muted-foreground hover:text-foreground transition-colors rounded-md hover:bg-muted/50" title="Imprimir pedido">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        });

        $('#recent-orders-list').html(html);
    }

    // Função para mapear status dos pedidos recentes para badges
    function getRecentOrderStatusBadge(status) {
        const statusMap = {
            'pending': { class: 'badge-warning', text: 'Pendente' },
            'processing': { class: 'badge-primary', text: 'Processando' },
            'completed': { class: 'badge-success', text: 'Concluído' }
        };
        return statusMap[status] || { class: 'badge-secondary', text: 'Desconhecido' };
    }

    // Função para mapear status dos pedidos recentes para ícones
    function getRecentOrderIconBg(status) {
        const statusBgs = {
            'pending': 'bg-yellow-100 text-yellow-600',
            'processing': 'bg-blue-100 text-blue-600',
            'completed': 'bg-green-100 text-green-600'
        };
        return statusBgs[status] || 'bg-gray-100 text-gray-600';
    }

    // Função para carregar receitas recentes do dashboard
    function loadRecentReceitas() {
        // Verifica se userProfileAjax está definido
        if (typeof userProfileAjax === 'undefined') {
            $('#recent-receitas-list').html('<div class="text-center text-red-600 py-4">Erro: Configuração AJAX não encontrada.</div>');
            return;
        }

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_recent_receitas',
                security: userProfileAjax.nonce
            },
            beforeSend: function () {
                $('#recent-receitas-list').html('<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div></div>');
            },
            success: function (response) {
                if (response.success) {
                    displayRecentReceitas(response.data.receitas);
                } else {
                    $('#recent-receitas-list').html('<div class="text-center text-muted-foreground py-4">Erro ao carregar receitas recentes.</div>');
                }
            },
            error: function () {
                $('#recent-receitas-list').html('<div class="text-center text-red-600 py-4">Erro de comunicação. Tente novamente.</div>');
            }
        });
    }

    // Função para exibir receitas recentes no dashboard
    function displayRecentReceitas(receitas) {
        if (receitas.length === 0) {
            $('#recent-receitas-list').html(`
                <div class="text-center py-8">
                    <div class="text-muted-foreground">Nenhuma receita encontrada.</div>
                </div>
            `);
            return;
        }

        let html = '<div class="space-y-4">';
        receitas.forEach(function (receita) {
            const statusBadge = getRecentReceitaStatusBadge(receita.status);
            const iconBg = getRecentReceitaIconBg(receita.status);

            // Monta o texto da especialidade e validade
            let subtexto = '';
            if (receita.prescritor_especialidade) {
                subtexto += receita.prescritor_especialidade + ' • ';
            }

            if (receita.status === 'expirada') {
                subtexto += 'Expirada';
            } else if (receita.data_vencimento) {
                subtexto += 'Válida até ' + receita.data_vencimento;
            } else {
                subtexto += 'Data não informada';
            }

            // Monta os botões de ação
            let actionButtons = '';
            
            // Botão Receita (se existir arquivo)
            if (receita.arquivo_receita) {
                actionButtons += `<a href="${receita.arquivo_receita}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 transition-colors mr-2">Receita</a>`;
            }
            
            // Botão Laudo (se existir arquivo)
            if (receita.arquivo_laudo) {
                actionButtons += `<a href="${receita.arquivo_laudo}" target="_blank" class="text-xs text-green-600 hover:text-green-800 transition-colors">Laudo</a>`;
            }

            html += `
                <div class="flex items-center gap-4 p-4 rounded-lg border border-border hover:bg-muted/50 transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg ${iconBg}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-foreground">${receita.prescritor_nome}</p>
                                <p class="text-xs text-muted-foreground">${subtexto}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge ${statusBadge.class}">${statusBadge.text}</span>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-1">
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $('#recent-receitas-list').html(html);
    }

    // Função para mapear status das receitas recentes para badges
    function getRecentReceitaStatusBadge(status) {
        const statusMap = {
            'ativa': { class: 'badge-success', text: 'Ativa' },
            'expirando': { class: 'badge-warning', text: 'Expirando' },
            'expirada': { class: 'badge-secondary', text: 'Expirada' }
        };
        return statusMap[status] || { class: 'badge-secondary', text: 'Desconhecido' };
    }

    // Função para mapear status das receitas recentes para ícones
    function getRecentReceitaIconBg(status) {
        const statusBgs = {
            'ativa': 'bg-green-100 text-green-600',
            'expirando': 'bg-yellow-100 text-yellow-600',
            'expirada': 'bg-gray-100 text-gray-600'
        };
        return statusBgs[status] || 'bg-blue-100 text-blue-600';
    }

    // Função para navegar para a aba de receitas
    window.navigateToReceitas = function (receitaId) {
        // Remove classes ativas de todos os botões da sidebar
        $('.sidebar-menu-button').removeClass('text-white bg-gradient-to-r from-green-500 to-green-600 shadow-sm')
            .attr('data-active', 'false');

        // Adiciona classes ativas ao botão de receitas
        $('.sidebar-menu-button[data-target="#receitas"]').addClass('text-white bg-gradient-to-r from-green-500 to-green-600 shadow-sm')
            .attr('data-active', 'true');

        // Oculta todas as seções de conteúdo
        $('.content-section').addClass('hidden');

        // Mostra a seção de receitas
        $('#receitas').removeClass('hidden').addClass('animate-fade-in');

        // Carrega as receitas se necessário
        if ($('#receitas-grid').length > 0) {
            loadReceitas();
        }

        // Fecha o sidebar mobile se estiver aberto
        if (window.innerWidth < 768) {
            $('.sidebar').attr('data-state', 'closed');
            $('#mobile-overlay').attr('data-state', 'closed');
            document.body.style.overflow = 'auto';
        }
    };

    // DISABLED: Old form handler - using enhanced handler below
    /*
    $(document).on('submit', '#user-profile-form', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var $spinner = $submitButton.find('.spinner');
        var $buttonText = $submitButton.find('.button-text');

        // Novos campos de senha
        var password = $form.find('input[name="password"]').val();
        var passwordConfirm = $form.find('input[name="password_confirm"]').val();
        
        console.log('Password form submission:', {
            password: password ? '[HIDDEN]' : 'empty',
            passwordConfirm: passwordConfirm ? '[HIDDEN]' : 'empty',
            passwordLength: password.length,
            passwordConfirmLength: passwordConfirm.length
        });

        // Validação dos campos de senha usando a mesma lógica da validação em tempo real
        if (password !== '' || passwordConfirm !== '') {
            if (password === '' || passwordConfirm === '') {
                console.log('Validation failed: One field empty');
                showModernNotification('error', 'Preencha ambos os campos de senha.');
                showFieldError('password', 'Campo obrigatório quando alterando senha');
                showFieldError('password_confirm', 'Campo obrigatório quando alterando senha');
                return;
            }
            
            // Validação de força da senha (mínimo 4 caracteres)
            if (password.length < 4) {
                console.log('Validation failed: Password too short');
                showModernNotification('error', 'A nova senha deve ter pelo menos 4 caracteres.');
                showFieldError('password', 'Senha deve ter pelo menos 4 caracteres');
                return;
            }
            
            if (password !== passwordConfirm) {
                console.log('Validation failed: Passwords do not match');
                showModernNotification('error', 'As senhas não coincidem.');
                showFieldError('password_confirm', 'As senhas não coincidem');
                return;
            }
        }

        // Verifica se userProfileAjax está definido
        if (typeof userProfileAjax === 'undefined') {
            showModernNotification('error', 'Erro: Configuração AJAX não encontrada.');
            $submitButton.prop('disabled', false);
            $spinner.addClass('hidden');
            $buttonText.text('Salvar Alterações');
            return;
        }

        var formData = {
            action: 'update_user_profile',
            security: userProfileAjax.nonce,
            password: password,
            password_confirm: passwordConfirm
        };



        // Enhanced loading state management
        setSubmitButtonLoading($submitButton, $spinner, $buttonText);

        // Converte formData para FormData para uso com fetch API
        const fetchFormData = new FormData();
        Object.keys(formData).forEach(key => {
            fetchFormData.append(key, formData[key]);
        });

        // Cria um AbortController para controlar timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 segundos timeout

        // Usa fetch API para fazer a requisição AJAX
        fetch(userProfileAjax.ajax_url, {
            method: 'POST',
            body: fetchFormData,
            credentials: 'same-origin', // Inclui cookies para autenticação
            signal: controller.signal
        })
        .then(response => {
            // Limpa o timeout se a requisição foi bem-sucedida
            clearTimeout(timeoutId);
            
            // Verifica se a resposta é válida
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            console.log('Password update response:', response);
            
            if (response.success) {
                // Show success notification with enhanced message
                showModernNotification('success', response.data.message || 'Senha alterada com sucesso!');
                
                // Enhanced form reset functionality
                resetPasswordForm($form);
                
                // Clear any existing field validation states
                clearFieldValidation('password');
                clearFieldValidation('password_confirm');
                
                // Se há redirecionamento (por segurança), redireciona após 2 segundos
                if (response.data.redirect) {
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 2000);
                }
            } else {
                // Enhanced error handling with specific field errors if provided
                const errorMessage = response.data.message || 'Erro desconhecido ao alterar senha.';
                showModernNotification('error', errorMessage);
                
                // Handle specific field errors if provided by backend
                if (response.data.field_errors) {
                    Object.keys(response.data.field_errors).forEach(fieldId => {
                        showFieldError(fieldId, response.data.field_errors[fieldId]);
                    });
                }
            }
        })
        .catch(error => {
            // Limpa o timeout em caso de erro
            clearTimeout(timeoutId);
            
            console.error('Password update error:', error);
            
            // Enhanced error handling with user-friendly messages
            let errorMessage = 'Erro de comunicação. Tente novamente.';
            
            if (error.name === 'AbortError') {
                errorMessage = 'Tempo limite excedido. Verifique sua conexão e tente novamente.';
            } else if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                errorMessage = 'Erro de conexão. Verifique sua internet e tente novamente.';
            } else if (error.message.includes('HTTP error')) {
                errorMessage = 'Erro do servidor. Tente novamente em alguns instantes.';
            } else if (error.message.includes('NetworkError')) {
                errorMessage = 'Erro de rede. Verifique sua conexão com a internet.';
            }
            
            showModernNotification('error', errorMessage);
            
            // Clear any field validation states on network errors
            clearFieldValidation('password');
            clearFieldValidation('password_confirm');
        })
        .finally(() => {
            // Enhanced loading state management - restore button to original state
            restoreSubmitButtonState($submitButton, $spinner, $buttonText);
        });
        
        return false; // Garante que o formulário não seja submetido
    });
    */

    // Funcionalidade de upload de avatar
    $('#avatar-upload').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validações no front-end
        if (!file.type.startsWith('image/')) {
            showModernNotification('error', 'Por favor, selecione apenas arquivos de imagem.');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showModernNotification('error', 'O arquivo deve ter no máximo 5MB.');
            return;
        }
        
        // Mostra preview da imagem
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#avatar-display').html(`<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`);
        };
        reader.readAsDataURL(file);
        
        // Mostra o botão de salvar
        $('#save-avatar-btn').removeClass('hidden');
    });

    // Manipulador para salvar o avatar
    $('#save-avatar-btn').on('click', function() {
        const fileInput = document.getElementById('avatar-upload');
        const file = fileInput.files[0];
        
        if (!file) {
            showModernNotification('error', 'Nenhum arquivo selecionado.');
            return;
        }
        
        const $button = $(this);
        const $spinner = $button.find('.spinner');
        const $buttonText = $button.find('.button-text');
        
        // Prepara FormData para upload
        const formData = new FormData();
        formData.append('action', 'amedis_avatar_upload');
        formData.append('avatar_nonce', $('input[name="avatar_nonce"]').val());
        formData.append('avatar_file', file);
        
        // Desabilita o botão e mostra loading
        $button.prop('disabled', true);
        $spinner.removeClass('hidden');
        $buttonText.text('Salvando...');
        
        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showModernNotification('success', response.data.message);
                    
                    // Atualiza a imagem do avatar
                    $('#avatar-display').html(`<img src="${response.data.avatar_url}" alt="Avatar" class="w-full h-full object-cover">`);
                    
                    // Esconde o botão de salvar
                    $('#save-avatar-btn').addClass('hidden');
                    
                    // Mostra o botão de remover se não existir
                    if ($('#remove-avatar-btn').length === 0) {
                        $('#save-avatar-btn').after(`
                            <button type="button" id="remove-avatar-btn" class="btn bg-red-100 text-red-600 hover:bg-red-200 w-full mt-2">
                                <span class="spinner hidden">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span class="button-text">Remover Avatar</span>
                            </button>
                        `);
                    } else {
                        $('#remove-avatar-btn').removeClass('hidden');
                    }
                    
                    // Limpa o input de arquivo
                    fileInput.value = '';
                } else {
                    showModernNotification('error', response.data.message);
                }
            },
            error: function() {
                showModernNotification('error', 'Erro de comunicação. Tente novamente.');
            },
            complete: function() {
                // Reabilita o botão e esconde loading
                $button.prop('disabled', false);
                $spinner.addClass('hidden');
                $buttonText.text('Salvar Avatar');
            }
        });
    });

    // Manipulador para remover o avatar
    $(document).on('click', '#remove-avatar-btn', function() {
        if (!confirm('Tem certeza que deseja remover seu avatar? Você voltará a usar o avatar padrão.')) {
            return;
        }
        
        const $button = $(this);
        const $spinner = $button.find('.spinner');
        const $buttonText = $button.find('.button-text');
        
        // Desabilita o botão e mostra loading
        $button.prop('disabled', true);
        $spinner.removeClass('hidden');
        $buttonText.text('Removendo...');
        
        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'amedis_avatar_remove',
                avatar_nonce: $('input[name="avatar_nonce"]').val()
            },
            success: function(response) {
                if (response.success) {
                    showModernNotification('success', response.data.message);
                    
                    // Volta para o avatar padrão (iniciais)
                    const userName = window.userACF?.nome || 'Usuario';
                    const initials = userName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    $('#avatar-display').html(initials);
                    
                    // Remove o botão de remover
                    $button.remove();
                    
                    // Limpa o input de arquivo
                    document.getElementById('avatar-upload').value = '';
                } else {
                    showModernNotification('error', response.data.message);
                }
            },
            error: function() {
                showModernNotification('error', 'Erro de comunicação. Tente novamente.');
            },
            complete: function() {
                // Reabilita o botão e esconde loading
                $button.prop('disabled', false);
                $spinner.addClass('hidden');
                $buttonText.text('Remover Avatar');
            }
        });
    });

    // Função para mostrar notificações modernas
    /**
     * Enhanced notification system for UI feedback
     * Shows modern notifications with improved styling and behavior
     */
    function showModernNotification(type, message, options = {}) {
        // Remove existing notifications of the same type to avoid spam
        $(`.modern-notification[data-type="${type}"]`).remove();
        
        const iconMap = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        
        const colorMap = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800'
        };
        
        // Default options
        const defaultOptions = {
            autoHide: true,
            duration: type === 'success' ? 4000 : 6000, // Success messages hide faster
            closable: true,
            position: 'top-right'
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        const notification = $(`
            <div class="modern-notification fixed top-4 right-4 z-50 max-w-sm w-full ${colorMap[type]} border rounded-lg shadow-lg p-4 animate-fade-in" data-type="${type}">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${iconMap[type]}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    ${finalOptions.closable ? `
                    <div class="ml-auto pl-3">
                        <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none transition-colors" onclick="$(this).closest('.modern-notification').remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-hide functionality
        if (finalOptions.autoHide) {
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, finalOptions.duration);
        }
        
        // Log notification for debugging
        console.log(`Notification shown: ${type} - ${message}`);

        return notification;
    }

        // Adiciona a action e o nonce aos dados do formulário
        formData += '&action=update_user_profile&security=' + userProfileAjax.nonce;

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    // Sucesso: Exibe notificação moderna
                    showModernNotification('success', response.data.message);
                } else {
                    // Erro: Exibe notificação moderna
                    showModernNotification('error', response.data.message);
                }
            },
            error: function () {
                // Erro de conexão/servidor
                showModernNotification('error', 'Ocorreu um erro de comunicação. Tente novamente.');
            },
            complete: function () {
                // Restaura o botão ao estado original
                $spinner.addClass('hidden');
                $buttonText.text('Salvar Alterações');
                $submitButton.prop('disabled', false).removeClass('opacity-50');
            }
        });
    });



    // Toggle password visibility
    $('button[type="button"]').on('click', function () {
        var $input = $(this).siblings('input[type="password"], input[type="text"]');
        var $icon = $(this).find('svg');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>');
        } else {
            $input.attr('type', 'password');
            $icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>');
        }
    });

    // --- Dropdown do Usuário ---
(function ($) {
    const userMenuBtn = $('#user-menu-btn');
    const userMenuDropdown = $('#user-menu-dropdown');
    if (userMenuBtn.length && userMenuDropdown.length) {
        userMenuBtn.on('click', function (e) {
            e.stopPropagation();
            const expanded = userMenuBtn.attr('aria-expanded') === 'true';
            userMenuBtn.attr('aria-expanded', !expanded);
            userMenuDropdown.toggleClass('hidden');
        });
        $(document).on('click', function () {
            if (!userMenuDropdown.hasClass('hidden')) {
                userMenuDropdown.addClass('hidden');
                userMenuBtn.attr('aria-expanded', 'false');
            }
        });
        userMenuDropdown.on('click', function (e) {
            e.stopPropagation();
        });
        userMenuBtn.on('keydown', function (e) {
            if (e.key === 'Escape') {
                userMenuDropdown.addClass('hidden');
                userMenuBtn.attr('aria-expanded', 'false');
            }
        });
    }
})(jQuery);

// --- Notificações Dinâmicas ---
(function ($) {
    const notifBtn = $('#notification-btn');
    const notifPanel = $('#notification-panel');
    const notifBadge = $('#notification-badge');
    const notifList = $('#notification-list');
    const markAllReadBtn = $('#mark-all-read');
    let notifications = [];
    let unreadCount = 0;
    function fetchNotifications() {
        notifList.html('<div class="p-4 text-center text-muted-foreground text-sm">Carregando...</div>');
        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'fetch_user_notifications',
                nonce: userProfileAjax.nonce
            },
            success: function (data) {
                notifications = data.notifications || [];
                unreadCount = notifications.filter(n => !n.read).length;
                renderNotifications();
            },
            error: function () {
                notifList.html('<div class="p-4 text-center text-red-500 text-sm">Erro ao carregar notificações.</div>');
            }
        });
    }
    function renderNotifications() {
        if (!notifications.length) {
            notifList.html('<div class="p-4 text-center text-muted-foreground text-sm">Nenhuma notificação.</div>');
            notifBadge.addClass('hidden');
            return;
        }
        notifList.html(notifications.map(function (n) {
            return `<div class="flex items-start gap-3 p-4 hover:bg-accent/50 transition-colors cursor-pointer ${n.read ? '' : 'bg-accent/20'}" data-id="${n.id}">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class=\"h-5 w-5 text-primary\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01\"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-foreground">${n.title}</p>
                    <p class="text-xs text-muted-foreground">${n.message}</p>
                    <span class="text-xs text-muted-foreground">${n.date}</span>
                </div>
                ${!n.read ? '<span class="badge badge-primary">Novo</span>' : ''}
            </div>`;
        }).join(''));
        if (unreadCount > 0) {
            notifBadge.removeClass('hidden');
        } else {
            notifBadge.addClass('hidden');
        }
    }
    if (notifBtn.length && notifPanel.length) {
        notifBtn.on('click', function (e) {
            e.stopPropagation();
            notifPanel.toggleClass('hidden');
            if (!notifPanel.hasClass('hidden')) {
                fetchNotifications();
            }
        });
        $(document).on('click', function () {
            notifPanel.addClass('hidden');
        });
        notifPanel.on('click', function (e) {
            e.stopPropagation();
        });
        if (markAllReadBtn.length) {
            markAllReadBtn.on('click', function () {
                $.ajax({
                    url: userProfileAjax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mark_all_notifications_read',
                        nonce: userProfileAjax.nonce
                    },
                    success: function () {
                        notifications.forEach(function (n) { n.read = true; });
                        unreadCount = 0;
                        renderNotifications();
                    }
                });
            });
        }
    }
})(jQuery);

// Controles do Sidebar Mobile
jQuery(document).ready(function ($) {
    // Toggle do sidebar mobile
    $('#sidebar-toggle').on('click', function () {
        const sidebar = $('.sidebar');
        const overlay = $('#mobile-overlay');
        const isOpen = sidebar.attr('data-state') === 'open';

        if (isOpen) {
            sidebar.attr('data-state', 'closed');
            overlay.attr('data-state', 'closed');
            document.body.style.overflow = 'auto';
        } else {
            sidebar.attr('data-state', 'open');
            overlay.attr('data-state', 'open');
            document.body.style.overflow = 'hidden';
        }
    });

    // Fechar sidebar mobile
    $('#sidebar-close, #mobile-overlay').on('click', function () {
        $('.sidebar').attr('data-state', 'closed');
        $('#mobile-overlay').attr('data-state', 'closed');
        document.body.style.overflow = 'auto';
    });

    // Fechar sidebar ao redimensionar para desktop
    $(window).on('resize', function () {
        if (window.innerWidth >= 768) {
            $('.sidebar').attr('data-state', 'closed');
            $('#mobile-overlay').attr('data-state', 'closed');
            document.body.style.overflow = 'auto';
        }
    });

    // Funcionalidade de toggle de senha
    $(document).on('click', '.relative button[type="button"]', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $input = $button.siblings('input[type="password"], input[type="text"]');

        if ($input.length === 0) return;

        var $icon = $button.find('svg');

        if ($input.attr('type') === 'password') {
            // Mostrar senha
            $input.attr('type', 'text');
            $icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>');
        } else {
            // Ocultar senha
            $input.attr('type', 'password');
            $icon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>');
        }
    });
});



// Avatar functionality with Cropper.js
jQuery(document).ready(function($) {
    let cropper = null;
    let selectedFile = null;

    // Avatar upload functionality
    $('#avatar-upload').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showModernNotification('error', 'Por favor, selecione apenas arquivos JPEG, PNG ou WebP.');
            this.value = '';
            return;
        }

        // Validate file size (3MB)
        if (file.size > 3 * 1024 * 1024) {
            showModernNotification('error', 'O arquivo deve ter no máximo 3MB.');
            this.value = '';
            return;
        }

        selectedFile = file;
        openCropModal(file);
    });

    // Open crop modal
    function openCropModal(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const modal = $('#avatar-crop-modal');
            const image = $('#crop-image');
            
            image.attr('src', e.target.result);
            modal.removeClass('hidden');
            document.body.style.overflow = 'hidden';

            // Initialize cropper after image loads
            image.on('load', function() {
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(this, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                    guides: false,
                    preview: '#crop-preview',
                    ready: function() {
                        // Cropper is ready
                    }
                });
            });
        };
        reader.readAsDataURL(file);
    }

    // Close crop modal
    function closeCropModal() {
        const modal = $('#avatar-crop-modal');
        modal.addClass('hidden');
        document.body.style.overflow = 'auto';
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        selectedFile = null;
        $('#avatar-upload').val('');
    }

    // Modal close handlers
    $('#crop-modal-close, #crop-cancel').on('click', closeCropModal);

    // Confirm crop
    $('#crop-confirm').on('click', function() {
        if (!cropper || !selectedFile) return;

        const button = $(this);
        const spinner = button.find('.spinner');
        const buttonText = button.find('.button-text');

        // Show loading state
        button.prop('disabled', true);
        spinner.removeClass('hidden');
        buttonText.text('Processando...');

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: 150,
            height: 150,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        // Convert to blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                showModernNotification('error', 'Erro ao processar a imagem.');
                resetCropButton();
                return;
            }

            uploadAvatar(blob);
        }, 'image/jpeg', 0.92);
    });

    // Upload avatar
    function uploadAvatar(blob) {
        const formData = new FormData();
        formData.append('action', 'upload_user_avatar');
        formData.append('security', userProfileAjax.nonce);
        formData.append('avatar_file', blob, 'avatar.jpg');

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Update avatar display with cache bust
                    const timestamp = new Date().getTime();
                    const avatarUrl = response.data.url + '?t=' + timestamp;
                    
                    $('#avatar-display').html(`<img src="${avatarUrl}" alt="Avatar" class="w-full h-full object-cover">`);
                    
                    // Show remove button
                    if ($('#remove-avatar-btn').length === 0) {
                        const removeBtn = `
                            <button type="button" id="remove-avatar-btn" class="btn bg-red-100 text-red-600 hover:bg-red-200 w-full mt-2">
                                <span class="spinner hidden">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span class="button-text">Remover Avatar</span>
                            </button>
                        `;
                        $('.btn.bg-slate-300').after(removeBtn);
                    } else {
                        $('#remove-avatar-btn').removeClass('hidden');
                    }

                    showModernNotification('success', 'Avatar atualizado com sucesso!');
                    closeCropModal();
                } else {
                    showModernNotification('error', response.data.message || 'Erro ao fazer upload do avatar.');
                    resetCropButton();
                }
            },
            error: function() {
                showModernNotification('error', 'Erro de comunicação. Tente novamente.');
                resetCropButton();
            }
        });
    }

    // Reset crop button state
    function resetCropButton() {
        const button = $('#crop-confirm');
        const spinner = button.find('.spinner');
        const buttonText = button.find('.button-text');

        button.prop('disabled', false);
        spinner.addClass('hidden');
        buttonText.text('Confirmar');
    }

    // Remove avatar functionality
    $(document).on('click', '#remove-avatar-btn', function() {
        const button = $(this);
        const spinner = button.find('.spinner');
        const buttonText = button.find('.button-text');

        // Show loading state
        button.prop('disabled', true);
        spinner.removeClass('hidden');
        buttonText.text('Removendo...');

        $.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'remove_user_avatar',
                security: userProfileAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reset to initials with proper styling
                    const initials = response.data.initials || 'U';
                    $('#avatar-display').html(initials);
                    $('#avatar-display').removeClass().addClass('flex h-24 w-24 items-center justify-center rounded-full avatar-gradient text-primary-foreground text-2xl font-bold shadow-lg ring-4 ring-background overflow-hidden');
                    
                    // Hide remove button
                    $('#remove-avatar-btn').addClass('hidden');
                    
                    showModernNotification('success', 'Avatar removido com sucesso!');
                } else {
                    showModernNotification('error', response.data.message || 'Erro ao remover avatar.');
                }
            },
            error: function() {
                showModernNotification('error', 'Erro de comunicação. Tente novamente.');
            },
            complete: function() {
                // Reset button state
                button.prop('disabled', false);
                spinner.addClass('hidden');
                buttonText.text('Remover Avatar');
            }
        });
    });

    // ESC key to close crop modal
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#avatar-crop-modal').hasClass('hidden')) {
            closeCropModal();
        }
    });

    // ========================================================================
    // PASSWORD VALIDATION FUNCTIONALITY
    // ========================================================================

    // Password validation state
    let passwordValidation = {
        isPasswordValid: false,
        isConfirmValid: false,
        passwordsMatch: false
    };

    // Password validation functions
    function validatePasswordStrength(password) {
        const minLength = 4;
        const hasMinLength = password.length >= minLength;
        
        return {
            isValid: hasMinLength,
            minLength: hasMinLength,
            message: hasMinLength ? 'Senha válida' : `Senha deve ter pelo menos ${minLength} caracteres`
        };
    }

    function validatePasswordMatch(password, confirmPassword) {
        const match = password === confirmPassword && password.length > 0;
        return {
            isValid: match,
            message: match ? 'Senhas coincidem' : 'As senhas não coincidem'
        };
    }

    function showFieldError(fieldId, message) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2');
        
        // Remove existing error message
        container.find('.password-error').remove();
        
        // Add error styling
        field.addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
        field.removeClass('border-green-500 focus:border-green-500 focus:ring-green-500');
        
        // Add error message
        if (message) {
            container.append(`<p class="password-error text-xs text-red-600 mt-1">${message}</p>`);
        }
    }

    function showFieldSuccess(fieldId, message) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2');
        
        // Remove existing error message
        container.find('.password-error').remove();
        
        // Add success styling
        field.addClass('border-green-500 focus:border-green-500 focus:ring-green-500');
        field.removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
        
        // Add success message if provided
        if (message) {
            container.append(`<p class="password-error text-xs text-green-600 mt-1">${message}</p>`);
        }
    }

    function clearFieldValidation(fieldId) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2');
        
        // Remove error message
        container.find('.password-error').remove();
        
        // Remove validation styling
        field.removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
        field.removeClass('border-green-500 focus:border-green-500 focus:ring-green-500');
    }

    /**
     * Enhanced form reset functionality after successful password update
     * Clears password fields and resets form state
     */
    function resetPasswordForm($form) {
        // Clear password fields
        $form.find('input[name="password"], input[name="password_confirm"]').val('');
        
        // Reset password validation state
        passwordValidation = {
            isPasswordValid: false,
            isConfirmValid: false,
            passwordsMatch: false
        };
        
        // Clear any visual feedback
        clearFieldValidation('password');
        clearFieldValidation('password_confirm');
        
        // Focus back to first password field for better UX
        setTimeout(() => {
            $form.find('input[name="password"]').focus();
        }, 100);
        
        console.log('Password form reset completed');
    }

    /**
     * Enhanced loading state management for submit button
     * Sets button to loading state with spinner and disabled state
     */
    function setSubmitButtonLoading($submitButton, $spinner, $buttonText) {
        $submitButton.prop('disabled', true);
        $spinner.removeClass('hidden');
        $buttonText.text('Salvando...');
        
        // Add loading class for additional styling if needed
        $submitButton.addClass('loading');
    }

    /**
     * Enhanced loading state management - restore button to original state
     * Restores button to normal state after operation completion
     */
    function restoreSubmitButtonState($submitButton, $spinner, $buttonText) {
        $submitButton.prop('disabled', false);
        $spinner.addClass('hidden');
        $buttonText.text('Salvar Alterações');
        
        // Remove loading class
        $submitButton.removeClass('loading');
    }

    function updateSubmitButtonState() {
        const submitButton = $('#user-profile-form button[type="submit"]');
        const passwordField = $('#password');
        const confirmField = $('#password_confirm');
        
        // If both password fields are empty, allow submission (no password change)
        if (passwordField.val() === '' && confirmField.val() === '') {
            submitButton.prop('disabled', false);
            return;
        }
        
        // If password fields have content, validate them
        const isValid = passwordValidation.isPasswordValid && 
                       passwordValidation.isConfirmValid && 
                       passwordValidation.passwordsMatch;
        
        submitButton.prop('disabled', !isValid);
    }

    // Real-time password validation
    // Enhanced password input validation with strength checking
    $('#password').on('input', function() {
        const password = $(this).val();
        const confirmPassword = $('#password_confirm').val();
        
        if (password === '') {
            clearFieldValidation('password');
            passwordValidation.isPasswordValid = true;
            passwordValidation.passwordsMatch = true;
            
            // Also clear confirm field if password is empty
            if (confirmPassword === '') {
                clearFieldValidation('password_confirm');
                passwordValidation.isConfirmValid = true;
            }
        } else {
            // Enhanced validation with strength checking
            const validation = validatePasswordStrength(password);
            const strength = checkPasswordStrength(password);
            
            passwordValidation.isPasswordValid = validation.isValid;
            
            if (validation.isValid) {
                showFieldSuccess('password');
                // Show strength indicator for valid passwords
                updatePasswordStrengthUI('password', strength);
            } else {
                showFieldError('password', validation.message);
            }
            
            // Enhanced password match checking
            if (confirmPassword !== '') {
                const matchValidation = validatePasswordMatch(password, confirmPassword);
                passwordValidation.passwordsMatch = matchValidation.isValid;
                
                if (matchValidation.isValid) {
                    showFieldSuccess('password_confirm');
                } else {
                    showFieldError('password_confirm', matchValidation.message);
                }
            }
        }
        
        updateSubmitButtonState();
    });

    // Real-time password confirmation validation
    $('#password_confirm').on('input', function() {
        const confirmPassword = $(this).val();
        const password = $('#password').val();
        
        if (confirmPassword === '') {
            clearFieldValidation('password_confirm');
            passwordValidation.isConfirmValid = password === '';
            passwordValidation.passwordsMatch = password === '';
        } else {
            const matchValidation = validatePasswordMatch(password, confirmPassword);
            passwordValidation.isConfirmValid = matchValidation.isValid;
            passwordValidation.passwordsMatch = matchValidation.isValid;
            
            if (matchValidation.isValid) {
                showFieldSuccess('password_confirm');
            } else {
                showFieldError('password_confirm', matchValidation.message);
            }
        }
        
        updateSubmitButtonState();
    });

    // Clear validation when fields lose focus if they're empty
    $('#password, #password_confirm').on('blur', function() {
        if ($(this).val() === '') {
            clearFieldValidation($(this).attr('id'));
        }
    });

    // Enhanced form reset - clear all password validation and UI states
    $('#user-profile-form').on('reset', function() {
        clearFieldValidation('password');
        clearFieldValidation('password_confirm');
        passwordValidation = {
            isPasswordValid: false,
            isConfirmValid: false,
            passwordsMatch: false
        };
        updateSubmitButtonState();
        
        // Clear any existing notifications
        $('.modern-notification').remove();
        
        console.log('Form reset completed with enhanced cleanup');
    });

    /**
     * Enhanced password strength checker
     * Provides real-time feedback on password strength
     */
    function checkPasswordStrength(password) {
        if (password.length === 0) {
            return { strength: 'empty', message: '', score: 0 };
        }
        
        if (password.length < 4) {
            return { strength: 'weak', message: 'Muito curta (mínimo 4 caracteres)', score: 1 };
        }
        
        let score = 0;
        let feedback = [];
        
        // Length bonus
        if (password.length >= 6) score += 1;
        if (password.length >= 8) score += 1;
        
        // Character variety bonus
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        
        // Determine strength level
        if (score <= 2) {
            return { strength: 'weak', message: 'Senha fraca', score: score };
        } else if (score <= 4) {
            return { strength: 'medium', message: 'Senha média', score: score };
        } else {
            return { strength: 'strong', message: 'Senha forte', score: score };
        }
    }

    /**
     * Enhanced UI feedback for password strength
     * Shows visual indicator of password strength
     */
    function updatePasswordStrengthUI(fieldId, strength) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2');
        
        // Remove existing strength indicator
        container.find('.password-strength').remove();
        
        if (strength.strength === 'empty') {
            return;
        }
        
        // Add strength indicator
        const strengthHtml = `
            <div class="password-strength">
                <div class="password-strength-bar password-strength-${strength.strength}"></div>
            </div>
            <p class="text-xs text-muted-foreground mt-1">${strength.message}</p>
        `;
        
        container.append(strengthHtml);
    }

    // Initialize validation state
    updateSubmitButtonState();
    
    // Debug log to confirm password validation is loaded
    console.log('Password validation system initialized');
    
    // Test password validation functions (only in development)
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
        console.log('Password validation test:');
        console.log('- Short password validation:', validatePasswordStrength('123'));
        console.log('- Valid password validation:', validatePasswordStrength('1234'));
        console.log('- Password match test (match):', validatePasswordMatch('1234', '1234'));
        console.log('- Password match test (no match):', validatePasswordMatch('1234', '5678'));
    }

    // ========================================================================
    // COMPREHENSIVE ERROR HANDLING SYSTEM
    // ========================================================================

    /**
     * Enhanced error handling and user feedback system
     * Provides comprehensive error catching, logging, and user-friendly messages
     */

    // Error logging system
    const ErrorLogger = {
        log: function(error, context = '') {
            const timestamp = new Date().toISOString();
            const errorData = {
                timestamp: timestamp,
                context: context,
                error: error,
                userAgent: navigator.userAgent,
                url: window.location.href,
                userId: userProfileAjax?.userId || 'unknown'
            };
            
            console.error('Password Change Error:', errorData);
            
            // Send error to server for logging (optional)
            if (typeof userProfileAjax !== 'undefined') {
                $.ajax({
                    url: userProfileAjax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'log_frontend_error',
                        security: userProfileAjax.nonce,
                        error_data: JSON.stringify(errorData)
                    },
                    timeout: 5000,
                    error: function() {
                        // Silent fail for error logging
                        console.warn('Failed to send error log to server');
                    }
                });
            }
        }
    };

    // User-friendly error messages mapping
    const ErrorMessages = {
        'invalid_nonce': 'Sessão expirada. Por favor, recarregue a página e tente novamente.',
        'unauthorized': 'Você precisa estar logado para alterar a senha.',
        'empty_fields': 'Por favor, preencha todos os campos de senha.',
        'passwords_mismatch': 'As senhas não coincidem. Verifique e tente novamente.',
        'weak_password': 'A senha deve ter pelo menos 4 caracteres.',
        'password_too_simple': 'A senha deve conter pelo menos uma letra e um número.',
        'database_error': 'Erro interno do servidor. Tente novamente em alguns minutos.',
        'unknown_error': 'Ocorreu um erro inesperado. Entre em contato com o suporte se o problema persistir.',
        'network_error': 'Erro de conexão. Verifique sua internet e tente novamente.',
        'timeout_error': 'A operação demorou muito para responder. Tente novamente.',
        'server_error': 'O servidor está temporariamente indisponível. Tente novamente em alguns minutos.'
    };

    // Enhanced notification system
    function showNotification(message, type = 'error', duration = 5000) {
        // Remove existing notifications
        $('.password-notification').remove();
        
        const notificationClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
                                 type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' :
                                 'bg-red-50 border-red-200 text-red-800';
        
        const iconSvg = type === 'success' ? 
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
            type === 'warning' ?
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>' :
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
        const notification = $(`
            <div class="password-notification fixed top-4 right-4 z-50 max-w-md p-4 border rounded-lg shadow-lg ${notificationClass} animate-fade-in">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${iconSvg}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="$(this).closest('.password-notification').remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);
        }
    }

    // Enhanced loading state management
    function setLoadingState(isLoading) {
        const $submitButton = $('#user-profile-form button[type="submit"]');
        const $passwordField = $('#password');
        const $confirmField = $('#password_confirm');
        
        if (isLoading) {
            $submitButton.prop('disabled', true)
                         .html('<div class="flex items-center justify-center gap-2"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div><span>Alterando senha...</span></div>');
            $passwordField.prop('disabled', true);
            $confirmField.prop('disabled', true);
        } else {
            $submitButton.prop('disabled', false)
                         .html('Salvar Alterações');
            $passwordField.prop('disabled', false);
            $confirmField.prop('disabled', false);
        }
    }

    // Network error detection
    function isNetworkError(xhr) {
        return xhr.status === 0 || xhr.status === 408 || xhr.readyState === 0;
    }

    // Server error detection
    function isServerError(xhr) {
        return xhr.status >= 500 && xhr.status < 600;
    }

    // Enhanced AJAX error handler
    function handleAjaxError(xhr, textStatus, errorThrown, context = '') {
        let errorMessage = 'Ocorreu um erro inesperado. Tente novamente.';
        let errorCode = 'unknown_error';
        
        try {
            if (textStatus === 'timeout') {
                errorCode = 'timeout_error';
                errorMessage = ErrorMessages.timeout_error;
            } else if (isNetworkError(xhr)) {
                errorCode = 'network_error';
                errorMessage = ErrorMessages.network_error;
            } else if (isServerError(xhr)) {
                errorCode = 'server_error';
                errorMessage = ErrorMessages.server_error;
            } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                errorMessage = xhr.responseJSON.data.message;
                errorCode = xhr.responseJSON.data.code || 'server_response_error';
            } else if (xhr.responseText) {
                // Try to parse error from response text
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.data && response.data.message) {
                        errorMessage = response.data.message;
                        errorCode = response.data.code || 'parsed_error';
                    }
                } catch (parseError) {
                    errorMessage = 'Erro de comunicação com o servidor.';
                    errorCode = 'parse_error';
                }
            }
        } catch (error) {
            ErrorLogger.log(error, 'Error parsing AJAX response');
        }
        
        // Log the error
        ErrorLogger.log({
            xhr: {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText
            },
            textStatus: textStatus,
            errorThrown: errorThrown,
            errorCode: errorCode,
            errorMessage: errorMessage
        }, context);
        
        return { code: errorCode, message: errorMessage };
    }

    // ACTIVE Enhanced form submission handler with comprehensive error handling
    // This is the main password change handler integrated with the dashboard
    $(document).on('submit', '#user-profile-form', function(e) {
        e.preventDefault();
        
        console.log('Enhanced password form handler activated - Integration successful');
        
        try {
            const $form = $(this);
            const password = $form.find('input[name="password"]').val();
            const passwordConfirm = $form.find('input[name="password_confirm"]').val();
            
            // Clear previous notifications
            $('.password-notification').remove();
            
            // Frontend validation with user-friendly messages
            if (!password || !passwordConfirm) {
                showNotification(ErrorMessages.empty_fields, 'error');
                return;
            }
            
            if (password !== passwordConfirm) {
                showNotification(ErrorMessages.passwords_mismatch, 'error');
                return;
            }
            
            if (password.length < 4) {
                showNotification(ErrorMessages.weak_password, 'error');
                return;
            }
            
            // Check if userProfileAjax is available
            if (typeof userProfileAjax === 'undefined') {
                ErrorLogger.log('userProfileAjax not defined', 'Form submission');
                showNotification('Erro de configuração. Recarregue a página e tente novamente.', 'error');
                return;
            }
            
            // Set loading state
            setLoadingState(true);
            
            // AJAX request with comprehensive error handling
            $.ajax({
                url: userProfileAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_user_password',
                    security: userProfileAjax.nonce,
                    password: password,
                    password_confirm: passwordConfirm
                },
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    try {
                        setLoadingState(false);
                        
                        if (response.success) {
                            // Success handling
                            showNotification(response.data.message || 'Senha alterada com sucesso!', 'success');
                            
                            // Clear form
                            $form[0].reset();
                            clearFieldValidation('password');
                            clearFieldValidation('password_confirm');
                            
                            // Reset validation state
                            passwordValidation = {
                                isPasswordValid: false,
                                isConfirmValid: false,
                                passwordsMatch: false
                            };
                            updateSubmitButtonState();
                            
                        } else {
                            // Server returned error
                            const errorCode = response.data?.code || 'server_error';
                            const errorMessage = ErrorMessages[errorCode] || response.data?.message || ErrorMessages.unknown_error;
                            
                            showNotification(errorMessage, 'error');
                            ErrorLogger.log(response, 'Server returned error');
                        }
                    } catch (error) {
                        setLoadingState(false);
                        ErrorLogger.log(error, 'Success callback error');
                        showNotification(ErrorMessages.unknown_error, 'error');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    try {
                        setLoadingState(false);
                        
                        const errorInfo = handleAjaxError(xhr, textStatus, errorThrown, 'Password update AJAX');
                        const userMessage = ErrorMessages[errorInfo.code] || errorInfo.message;
                        
                        showNotification(userMessage, 'error');
                        
                    } catch (error) {
                        setLoadingState(false);
                        ErrorLogger.log(error, 'AJAX error callback');
                        showNotification(ErrorMessages.unknown_error, 'error');
                    }
                }
            });
            
        } catch (error) {
            setLoadingState(false);
            ErrorLogger.log(error, 'Form submission handler');
            showNotification(ErrorMessages.unknown_error, 'error');
        }
    });

    // Global error handler for unhandled JavaScript errors
    window.addEventListener('error', function(event) {
        ErrorLogger.log({
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error
        }, 'Global error handler');
    });

    // Global handler for unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        ErrorLogger.log({
            reason: event.reason,
            promise: event.promise
        }, 'Unhandled promise rejection');
    });

    // ========================================================================
    // ADDITIONAL ERROR HANDLING UTILITIES
    // ========================================================================

    // Network status monitoring
    let isOnline = navigator.onLine;
    let networkErrorShown = false;

    function showNetworkError() {
        if (!networkErrorShown) {
            const networkIndicator = $(`
                <div class="network-error-indicator show">
                    <span>Sem conexão com a internet. Verifique sua conexão.</span>
                    <button class="retry-button" onclick="location.reload()">Tentar novamente</button>
                </div>
            `);
            $('body').prepend(networkIndicator);
            networkErrorShown = true;
        }
    }

    function hideNetworkError() {
        $('.network-error-indicator').removeClass('show');
        setTimeout(() => {
            $('.network-error-indicator').remove();
            networkErrorShown = false;
        }, 300);
    }

    // Monitor network status
    window.addEventListener('online', function() {
        isOnline = true;
        hideNetworkError();
        showNotification('Conexão restaurada!', 'success', 3000);
    });

    window.addEventListener('offline', function() {
        isOnline = false;
        showNetworkError();
    });

    // Enhanced field validation with visual feedback
    function showFieldError(fieldId, message) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2, .form-group');
        
        // Remove existing messages
        container.find('.error-message, .success-message').remove();
        
        // Add error styling
        field.addClass('field-error').removeClass('field-success');
        
        // Add error message
        const errorHtml = `
            <div class="error-message">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        container.append(errorHtml);
        
        // Add ARIA attributes for accessibility
        field.attr('aria-invalid', 'true');
        field.attr('aria-describedby', fieldId + '-error');
        container.find('.error-message').attr('id', fieldId + '-error');
    }

    function showFieldSuccess(fieldId, message = '') {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2, .form-group');
        
        // Remove existing messages
        container.find('.error-message, .success-message').remove();
        
        // Add success styling
        field.addClass('field-success').removeClass('field-error');
        
        // Add success message if provided
        if (message) {
            const successHtml = `
                <div class="success-message">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            container.append(successHtml);
        }
        
        // Remove ARIA error attributes
        field.attr('aria-invalid', 'false');
        field.removeAttr('aria-describedby');
    }

    function clearFieldValidation(fieldId) {
        const field = $('#' + fieldId);
        const container = field.closest('.space-y-2, .form-group');
        
        // Remove styling and messages
        field.removeClass('field-error field-success');
        container.find('.error-message, .success-message').remove();
        
        // Remove ARIA attributes
        field.removeAttr('aria-invalid aria-describedby');
    }

    // Retry mechanism for failed requests
    function retryPasswordUpdate(formData, maxRetries = 3, currentRetry = 0) {
        if (currentRetry >= maxRetries) {
            showNotification('Falha após várias tentativas. Entre em contato com o suporte.', 'error');
            setLoadingState(false);
            return;
        }

        const retryDelay = Math.pow(2, currentRetry) * 1000; // Exponential backoff
        
        setTimeout(() => {
            $.ajax({
                url: userProfileAjax.ajax_url,
                type: 'POST',
                data: formData,
                timeout: 30000,
                success: function(response) {
                    // Handle success (same as main handler)
                    setLoadingState(false);
                    if (response.success) {
                        showNotification(response.data.message || 'Senha alterada com sucesso!', 'success');
                        $('#user-profile-form')[0].reset();
                        clearFieldValidation('password');
                        clearFieldValidation('password_confirm');
                    } else {
                        const errorCode = response.data?.code || 'server_error';
                        const errorMessage = ErrorMessages[errorCode] || response.data?.message || ErrorMessages.unknown_error;
                        showNotification(errorMessage, 'error');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    if (isNetworkError(xhr) && currentRetry < maxRetries - 1) {
                        showNotification(`Tentativa ${currentRetry + 2} de ${maxRetries}...`, 'warning', 2000);
                        retryPasswordUpdate(formData, maxRetries, currentRetry + 1);
                    } else {
                        setLoadingState(false);
                        const errorInfo = handleAjaxError(xhr, textStatus, errorThrown, 'Password update retry');
                        const userMessage = ErrorMessages[errorInfo.code] || errorInfo.message;
                        showNotification(userMessage, 'error');
                    }
                }
            });
        }, retryDelay);
    }

    // Validation helper functions
    function validatePasswordStrength(password) {
        if (password.length < 4) {
            return { isValid: false, message: 'A senha deve ter pelo menos 4 caracteres.' };
        }
        
        return { isValid: true, message: 'Senha válida.' };
    }

    function validatePasswordMatch(password, confirmPassword) {
        if (password !== confirmPassword) {
            return { isValid: false, message: 'As senhas não coincidem.' };
        }
        
        return { isValid: true, message: 'Senhas coincidem.' };
    }

    // Expose utility functions globally for debugging
    window.PasswordErrorHandling = {
        showNotification: showNotification,
        showFieldError: showFieldError,
        showFieldSuccess: showFieldSuccess,
        clearFieldValidation: clearFieldValidation,
        ErrorLogger: ErrorLogger,
        ErrorMessages: ErrorMessages
    };

    console.log('Comprehensive error handling system initialized');
});