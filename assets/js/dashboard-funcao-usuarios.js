/**
 * Sistema de Controle de Usuários Premium - JavaScript
 * Dashboard moderno com funcionalidades avançadas
 * 
 * @package SativarApp
 * @version 2.0
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Configuração global
    const config = {
        searchDelay: 300,
        animationDuration: 300,
        autoSaveDelay: 1000
    };
    
    // Estado da aplicação
    let appState = {
        currentFilter: 'all',
        searchTerm: '',
        isLoading: false,
        users: []
    };
    
    // Cache de elementos
    const elements = {
        searchInput: $('#user-search'),
        filterChips: $('.filter-chip'),
        usersContainer: $('#users-container'),
        loadingSkeleton: $('#loading-skeleton'),
        emptyState: $('#empty-state'),
        modal: $('#edit-modal'),
        refreshBtn: $('#refresh-users')
    };
    
    // Inicialização
    init();
    
    /**
     * Inicializa o sistema premium
     */
    function init() {
        loadStyles();
        bindEvents();
        initializeUsers();
        setupSearch();
        setupFilters();
        console.log('Premium User Control System initialized');
    }
    
    /**
     * Carrega estilos premium
     */
    function loadStyles() {
        const premiumStyles = `
            <style id="premium-user-control-styles">
                /* Estilos Premium */
                .dashboard-header {
                    margin-bottom: 2rem;
                }
                
                .filter-chip {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    background: #f8fafc;
                    border: 2px solid #e2e8f0;
                    border-radius: 9999px;
                    font-size: 0.875rem;
                    font-weight: 500;
                    color: #64748b;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                
                .filter-chip:hover {
                    background: #e2e8f0;
                    border-color: #cbd5e1;
                }
                
                .filter-chip.active {
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                    border-color: #3b82f6;
                    color: white;
                }
                
                .chip-count {
                    background: rgba(255,255,255,0.2);
                    padding: 0.125rem 0.5rem;
                    border-radius: 9999px;
                    font-size: 0.75rem;
                    font-weight: 600;
                }
                
                .filter-chip.active .chip-count {
                    background: rgba(255,255,255,0.3);
                }
                
                .action-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1.5rem;
                    border-radius: 0.75rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    border: none;
                    font-size: 0.875rem;
                }
                
                .action-btn.primary {
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                    color: white;
                }
                
                .action-btn.primary:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
                }
                
                .action-btn.secondary {
                    background: #f1f5f9;
                    color: #475569;
                    border: 1px solid #e2e8f0;
                }
                
                .action-btn.secondary:hover {
                    background: #e2e8f0;
                    transform: translateY(-1px);
                }
                
                .action-btn.info {
                    background: #dbeafe;
                    color: #1d4ed8;
                    padding: 0.75rem;
                }
                
                .action-btn.info:hover {
                    background: #bfdbfe;
                    transform: translateY(-1px);
                }
                
                .role-badge {
                    padding: 0.25rem 0.75rem;
                    border-radius: 9999px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                }
                
                .role-badge.role-super_admin {
                    background: linear-gradient(135deg, #ef4444, #dc2626);
                    color: white;
                }
                
                .role-badge.role-gerente {
                    background: linear-gradient(135deg, #f97316, #ea580c);
                    color: white;
                }
                
                .role-badge.role-atendente {
                    background: linear-gradient(135deg, #22c55e, #16a34a);
                    color: white;
                }
                
                .role-badge.role-none {
                    background: #f1f5f9;
                    color: #64748b;
                }
                
                .role-option-premium {
                    position: relative;
                    cursor: pointer;
                    display: block;
                }
                
                .role-option-premium input[type="radio"] {
                    position: absolute;
                    opacity: 0;
                    pointer-events: none;
                }
                
                .option-content {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0.25rem;
                    padding: 0.75rem 0.5rem;
                    border: 2px solid #e2e8f0;
                    border-radius: 0.75rem;
                    background: #f8fafc;
                    transition: all 0.3s ease;
                    text-align: center;
                }
                
                .role-option-premium:hover .option-content {
                    border-color: #cbd5e1;
                    background: #f1f5f9;
                    transform: translateY(-1px);
                }
                
                .role-option-premium.active .option-content {
                    border-color: #3b82f6;
                    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
                    color: #1d4ed8;
                }
                
                .option-icon {
                    font-size: 1.25rem;
                }
                
                .option-text {
                    font-size: 0.75rem;
                    font-weight: 500;
                }
                
                .user-card {
                    transition: all 0.3s ease;
                }
                
                .user-card:hover {
                    transform: translateY(-2px);
                }
                
                .status-indicator {
                    position: absolute;
                    top: 1rem;
                    left: 1rem;
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.75rem;
                    font-weight: 500;
                    opacity: 0;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                    z-index: 10;
                }
                
                .status-indicator.show {
                    opacity: 1;
                    transform: translateY(0);
                }
                
                .status-indicator.success {
                    background: #dcfce7;
                    color: #166534;
                    border: 1px solid #bbf7d0;
                }
                
                .status-indicator.error {
                    background: #fef2f2;
                    color: #991b1b;
                    border: 1px solid #fecaca;
                }
                
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                }
                
                .modal-overlay.show {
                    opacity: 1;
                    visibility: visible;
                }
                
                .modal-content {
                    position: relative;
                    transform: scale(0.9) translateY(20px);
                    transition: all 0.3s ease;
                }
                
                .modal-overlay.show .modal-content {
                    transform: scale(1) translateY(0);
                }
                
                .animate-pulse {
                    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                }
                
                @keyframes pulse {
                    0%, 100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: .5;
                    }
                }
                
                .hidden {
                    display: none !important;
                }
                
                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .stats-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                    
                    .filter-chips {
                        justify-content: center;
                    }
                    
                    .role-options {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        `;
        
        if ($('#premium-user-control-styles').length === 0) {
            $('head').append(premiumStyles);
        }
    }
    
    /**
     * Vincula eventos aos elementos
     */
    function bindEvents() {
        // Busca em tempo real
        let searchTimeout;
        elements.searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                appState.searchTerm = $(this).val().toLowerCase();
                filterUsers();
            }, config.searchDelay);
        });
        
        // Filtros por chips
        elements.filterChips.on('click', function() {
            const filter = $(this).data('filter');
            setActiveFilter(filter);
            filterUsers();
        });
        
        // Controles de função premium
        $(document).on('change', '.role-switcher-premium input[type="radio"]', handleRoleChangePremium);
        
        // Botão de atualizar
        elements.refreshBtn.on('click', refreshUsers);
        
        // Modal
        $(document).on('click', '.modal-close, .modal-overlay', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Password Modal Events
        $(document).on('click', '.change-password-btn', handlePasswordModalOpen);
        $(document).on('click', '.btn-cancel', closePasswordModal);
        $(document).on('submit', '#password-form', handlePasswordSubmit);
        $(document).on('input', '#new-password', validatePasswordStrength);
        $(document).on('input', '#confirm-password', validatePasswordMatch);
        
        // Escape key para fechar modal
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closePasswordModal();
            }
        });
    }
    
    /**
     * Inicializa dados dos usuários
     */
    function initializeUsers() {
        appState.users = [];
        $('.user-card').each(function() {
            const $card = $(this);
            appState.users.push({
                id: $card.data('user-id'),
                role: $card.data('role'),
                element: $card
            });
        });
    }
    
    /**
     * Configura busca
     */
    function setupSearch() {
        elements.searchInput.attr('autocomplete', 'off');
    }
    
    /**
     * Configura filtros
     */
    function setupFilters() {
        setActiveFilter('all');
    }
    
    /**
     * Define filtro ativo
     */
    function setActiveFilter(filter) {
        appState.currentFilter = filter;
        elements.filterChips.removeClass('active');
        elements.filterChips.filter(`[data-filter="${filter}"]`).addClass('active');
    }
    
    /**
     * Filtra usuários baseado na busca e filtros
     */
    function filterUsers() {
        let visibleCount = 0;
        
        appState.users.forEach(user => {
            let shouldShow = true;
            
            // Filtro por função
            if (appState.currentFilter !== 'all' && user.role !== appState.currentFilter) {
                shouldShow = false;
            }
            
            // Filtro por busca
            if (appState.searchTerm && shouldShow) {
                const $card = user.element;
                const userName = $card.find('.user-name').text().toLowerCase();
                const userLogin = $card.find('.user-login').text().toLowerCase();
                const userEmail = $card.find('.user-email').text().toLowerCase();
                
                if (!userName.includes(appState.searchTerm) && 
                    !userLogin.includes(appState.searchTerm) && 
                    !userEmail.includes(appState.searchTerm)) {
                    shouldShow = false;
                }
            }
            
            // Mostra/esconde card com animação
            if (shouldShow) {
                user.element.removeClass('hidden').fadeIn(config.animationDuration);
                visibleCount++;
            } else {
                user.element.fadeOut(config.animationDuration, function() {
                    $(this).addClass('hidden');
                });
            }
        });
        
        // Mostra empty state se necessário
        if (visibleCount === 0) {
            elements.emptyState.removeClass('hidden').fadeIn(config.animationDuration);
        } else {
            elements.emptyState.fadeOut(config.animationDuration, function() {
                $(this).addClass('hidden');
            });
        }
    }
    
    /**
     * Manipula mudança de função premium
     */
    function handleRoleChangePremium(event) {
        const $input = $(event.target);
        const userId = $input.closest('.role-switcher-premium').data('user-id');
        const selectedRole = $input.val();
        const $card = $input.closest('.user-card');
        
        // Verifica se o usuário tem permissão para fazer esta alteração
        if (!validatePermissionChange(selectedRole, userId)) {
            showStatusIndicator(userId, 'Sem permissão!', 'error');
            revertRoleSelection($card, userId);
            setTimeout(() => {
                hideStatusIndicator(userId);
            }, 2000);
            return;
        }
        
        // Atualiza visual imediatamente
        updateRoleVisual($card, selectedRole);
        
        // Mostra indicador de carregamento
        showStatusIndicator(userId, 'Salvando...', 'info');
        
        // Desabilita controles temporariamente
        $input.closest('.role-switcher-premium').find('input').prop('disabled', true);
        
        // Salva via AJAX
        saveRoleChangePremium(userId, selectedRole)
            .done(function(response) {
                if (response.success) {
                    showStatusIndicator(userId, 'Salvo!', 'success');
                    updateUserData(userId, selectedRole);
                    logRoleChange(selectedRole, userId);
                } else {
                    showStatusIndicator(userId, response.data || 'Erro!', 'error');
                    revertRoleSelection($card, userId);
                }
            })
            .fail(function(xhr) {
                let errorMessage = 'Erro de conexão!';
                if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMessage = xhr.responseJSON.data;
                }
                showStatusIndicator(userId, errorMessage, 'error');
                revertRoleSelection($card, userId);
            })
            .always(function() {
                // Reabilita controles
                $input.closest('.role-switcher-premium').find('input').prop('disabled', false);
                
                // Remove indicador após delay
                setTimeout(() => {
                    hideStatusIndicator(userId);
                }, 2000);
            });
    }
    
    /**
     * Salva mudança de função via AJAX (versão premium)
     */
    function saveRoleChangePremium(userId, selectedRole) {
        return $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'update_user_role_control',
                user_id: userId,
                role: selectedRole,
                nonce: $('meta[name="wp-nonce"]').attr('content') || 'premium-nonce'
            },
            dataType: 'json'
        });
    }
    
    /**
     * Atualiza visual da função
     */
    function updateRoleVisual($card, role) {
        // Atualiza badge
        const $badge = $card.find('.role-badge');
        const roleLabels = {
            'super_admin': 'Super Admin',
            'gerente': 'Gerente',
            'atendente': 'Atendente',
            'none': 'Sem Função'
        };
        
        $badge.removeClass('role-super_admin role-gerente role-atendente role-none')
              .addClass(`role-${role}`)
              .text(roleLabels[role] || 'Sem Função');
        
        // Atualiza data attribute
        $card.attr('data-role', role);
        
        // Atualiza estado visual dos radio buttons
        $card.find('.role-option-premium').removeClass('active');
        $card.find(`input[value="${role}"]`).closest('.role-option-premium').addClass('active');
    }
    
    /**
     * Mostra indicador de status
     */
    function showStatusIndicator(userId, message, type) {
        const $indicator = $(`#status-${userId}`);
        $indicator.removeClass('success error info')
                 .addClass(type)
                 .text(message)
                 .addClass('show');
    }
    
    /**
     * Esconde indicador de status
     */
    function hideStatusIndicator(userId) {
        const $indicator = $(`#status-${userId}`);
        $indicator.removeClass('show');
    }
    
    /**
     * Reverte seleção de função
     */
    function revertRoleSelection($card, userId) {
        const originalRole = $card.data('role');
        $card.find(`input[value="${originalRole}"]`).prop('checked', true);
        updateRoleVisual($card, originalRole);
    }
    
    /**
     * Atualiza dados do usuário no estado
     */
    function updateUserData(userId, newRole) {
        const userIndex = appState.users.findIndex(u => u.id == userId);
        if (userIndex !== -1) {
            appState.users[userIndex].role = newRole;
        }
        
        // Atualiza contadores dos filtros
        updateFilterCounts();
    }
    
    /**
     * Atualiza contadores dos filtros
     */
    function updateFilterCounts() {
        const counts = {
            all: appState.users.length,
            super_admin: appState.users.filter(u => u.role === 'super_admin').length,
            gerente: appState.users.filter(u => u.role === 'gerente').length,
            atendente: appState.users.filter(u => u.role === 'atendente').length,
            none: appState.users.filter(u => u.role === 'none' || !u.role).length
        };
        
        Object.keys(counts).forEach(filter => {
            elements.filterChips.filter(`[data-filter="${filter}"]`)
                              .find('.chip-count')
                              .text(counts[filter]);
        });
    }
    
    /**
     * Atualiza lista de usuários
     */
    function refreshUsers() {
        showLoading();
        
        setTimeout(() => {
            // Simula refresh - em produção, faria nova requisição AJAX
            hideLoading();
            showStatusMessage('Lista atualizada!', 'success');
        }, 1000);
    }
    
    /**
     * Mostra loading skeleton
     */
    function showLoading() {
        appState.isLoading = true;
        elements.usersContainer.fadeOut(config.animationDuration, function() {
            elements.loadingSkeleton.removeClass('hidden').fadeIn(config.animationDuration);
        });
    }
    
    /**
     * Esconde loading skeleton
     */
    function hideLoading() {
        appState.isLoading = false;
        elements.loadingSkeleton.fadeOut(config.animationDuration, function() {
            $(this).addClass('hidden');
            elements.usersContainer.fadeIn(config.animationDuration);
        });
    }
    
    /**
     * Mostra mensagem de status global
     */
    function showStatusMessage(message, type) {
        // Implementar toast notification
        console.log(`${type.toUpperCase()}: ${message}`);
    }
    
    /**
     * Abre modal de edição
     */
    function openModal(content) {
        elements.modal.find('#modal-content').html(content);
        elements.modal.addClass('show');
        $('body').addClass('modal-open');
    }
    
    /**
     * Fecha modal
     */
    function closeModal() {
        elements.modal.removeClass('show');
        $('body').removeClass('modal-open');
        setTimeout(() => {
            elements.modal.find('#modal-content').empty();
        }, config.animationDuration);
    }
    
    /**
     * Edita usuário (função global)
     */
    window.editUser = function(userId) {
        const user = appState.users.find(u => u.id == userId);
        if (!user) return;
        
        const $card = user.element;
        const userName = $card.find('.user-name').text();
        const userEmail = $card.find('.user-email').text();
        
        const modalContent = `
            <div class="user-edit-form">
                <div class="user-info mb-4">
                    <h4 class="font-semibold text-lg mb-2">Editando: ${userName}</h4>
                    <p class="text-gray-600 text-sm">${userEmail}</p>
                </div>
                
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nível de Acesso
                    </label>
                    <div class="role-selector-modal">
                        <label class="role-option-modal">
                            <input type="radio" name="modal_role_${userId}" value="super_admin" ${user.role === 'super_admin' ? 'checked' : ''}>
                            <span class="option-label">👑 Super Admin</span>
                        </label>
                        <label class="role-option-modal">
                            <input type="radio" name="modal_role_${userId}" value="gerente" ${user.role === 'gerente' ? 'checked' : ''}>
                            <span class="option-label">👔 Gerente</span>
                        </label>
                        <label class="role-option-modal">
                            <input type="radio" name="modal_role_${userId}" value="atendente" ${user.role === 'atendente' ? 'checked' : ''}>
                            <span class="option-label">🎧 Atendente</span>
                        </label>
                        <label class="role-option-modal">
                            <input type="radio" name="modal_role_${userId}" value="none" ${!user.role || user.role === 'none' ? 'checked' : ''}>
                            <span class="option-label">👤 Nenhum</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-actions flex gap-3 justify-end">
                    <button class="action-btn secondary" onclick="closeModal()">Cancelar</button>
                    <button class="action-btn primary" onclick="saveModalChanges(${userId})">Salvar</button>
                </div>
            </div>
            
            <style>
                .role-selector-modal {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.75rem;
                }
                
                .role-option-modal {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem;
                    border: 2px solid #e2e8f0;
                    border-radius: 0.5rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                
                .role-option-modal:hover {
                    border-color: #cbd5e1;
                    background: #f8fafc;
                }
                
                .role-option-modal input[type="radio"]:checked + .option-label {
                    font-weight: 600;
                    color: #3b82f6;
                }
                
                .role-option-modal input[type="radio"] {
                    margin: 0;
                }
            </style>
        `;
        
        openModal(modalContent);
    };
    
    /**
     * Visualiza detalhes do usuário (função global)
     */
    window.viewUserDetails = function(userId) {
        const user = appState.users.find(u => u.id == userId);
        if (!user) return;
        
        const $card = user.element;
        const userName = $card.find('.user-name').text();
        const userLogin = $card.find('.user-login').text();
        const userEmail = $card.find('.user-email').text();
        
        const roleLabels = {
            'super_admin': 'Super Admin',
            'gerente': 'Gerente',
            'atendente': 'Atendente',
            'none': 'Sem Função'
        };
        
        const modalContent = `
            <div class="user-details">
                <div class="user-avatar-large text-center mb-6">
                    <img src="${$card.find('.user-avatar img').attr('src')}" 
                         alt="${userName}"
                         class="w-20 h-20 rounded-full mx-auto border-4 border-blue-100">
                    <h3 class="text-xl font-bold mt-3">${userName}</h3>
                    <p class="text-gray-600">${userLogin}</p>
                </div>
                
                <div class="user-info-grid space-y-4">
                    <div class="info-item">
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">${userEmail}</p>
                    </div>
                    
                    <div class="info-item">
                        <label class="block text-sm font-medium text-gray-500">Nível de Acesso</label>
                        <span class="role-badge role-${user.role || 'none'} inline-block mt-1">
                            ${roleLabels[user.role] || 'Sem Função'}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="block text-sm font-medium text-gray-500">ID do Usuário</label>
                        <p class="text-gray-900 font-mono">${userId}</p>
                    </div>
                </div>
                
                <div class="form-actions flex gap-3 justify-end mt-6">
                    <button class="action-btn secondary" onclick="closeModal()">Fechar</button>
                    <button class="action-btn primary" onclick="closeModal(); editUser(${userId})">Editar</button>
                </div>
            </div>
        `;
        
        openModal(modalContent);
    };
    
    /**
     * Salva alterações do modal (função global)
     */
    window.saveModalChanges = function(userId) {
        const selectedRole = $(`input[name="modal_role_${userId}"]:checked`).val();
        const $card = appState.users.find(u => u.id == userId).element;
        
        // Atualiza visual
        updateRoleVisual($card, selectedRole);
        
        // Salva via AJAX
        saveRoleChangePremium(userId, selectedRole)
            .done(function(response) {
                if (response.success) {
                    updateUserData(userId, selectedRole);
                    closeModal();
                    showStatusMessage('Usuário atualizado com sucesso!', 'success');
                } else {
                    showStatusMessage('Erro ao salvar alterações!', 'error');
                }
            })
            .fail(function() {
                showStatusMessage('Erro de conexão!', 'error');
            });
    };
    
    /**
     * Registra mudança de função para auditoria
     */
    function logRoleChange(newRole, userId) {
        console.log(`Premium User role changed: User ID ${userId} -> ${newRole}`);
        
        // Log para servidor (opcional)
        if (window.console && console.info) {
            console.info('Premium role change logged successfully');
        }
    }
    
    /**
     * Validação no frontend
     */
    function validateRoleSelection(role) {
        const validRoles = ['super_admin', 'gerente', 'atendente', 'none'];
        return validRoles.includes(role);
    }
    
    /**
     * Valida se o usuário atual pode fazer a alteração de role
     */
    function validatePermissionChange(newRole, targetUserId) {
        // Obtém informações do usuário atual do contexto global
        const currentUserRole = window.premiumDashboard?.current_user_role || 'none';
        const targetUser = appState.users.find(u => u.id == targetUserId);
        const targetUserRole = targetUser ? targetUser.role : 'none';
        
        // Super Admin pode alterar qualquer role
        if (currentUserRole === 'super_admin') {
            return true;
        }
        
        // Gerente pode alterar apenas atendentes
        if (currentUserRole === 'gerente') {
            // Pode alterar apenas se o usuário alvo for atendente ou sem função
            if (targetUserRole === 'atendente' || targetUserRole === 'none' || !targetUserRole) {
                // E só pode definir como atendente ou nenhum
                return newRole === 'atendente' || newRole === 'none';
            }
        }
        
        // Atendente não pode alterar nada
        return false;
    }
    
    /**
     * Função pública para uso externo (versão premium)
     */
    window.SativarUserRoleControlPremium = {
        // Estado da aplicação
        getState: function() {
            return { ...appState };
        },
        
        // Filtros
        setFilter: function(filter) {
            if (['all', 'super_admin', 'gerente', 'atendente', 'none'].includes(filter)) {
                setActiveFilter(filter);
                filterUsers();
                return true;
            }
            return false;
        },
        
        // Busca
        search: function(term) {
            appState.searchTerm = term.toLowerCase();
            elements.searchInput.val(term);
            filterUsers();
        },
        
        // Usuários
        getUsers: function() {
            return appState.users;
        },
        
        getUserById: function(userId) {
            return appState.users.find(u => u.id == userId);
        },
        
        // Funções
        updateUserRole: function(userId, role) {
            if (validateRoleSelection(role)) {
                const user = this.getUserById(userId);
                if (user) {
                    const $input = user.element.find(`input[value="${role}"]`);
                    $input.prop('checked', true).trigger('change');
                    return true;
                }
            }
            return false;
        },
        
        // Modal
        openUserModal: function(userId) {
            editUser(userId);
        },
        
        closeModal: function() {
            closeModal();
        },
        
        // Utilitários
        refresh: function() {
            refreshUsers();
        },
        
        isLoading: function() {
            return appState.isLoading;
        }
    };
    
    // Compatibilidade com versão anterior
    window.SativarUserRoleControl = window.SativarUserRoleControlPremium;
    
    // Debug info (apenas em desenvolvimento)
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
        console.log('Premium User Control System Debug Info:', {
            version: '2.0',
            totalUsers: appState.users.length,
            currentFilter: appState.currentFilter,
            searchTerm: appState.searchTerm,
            isLoading: appState.isLoading
        });
    }
    
    // Adiciona meta tag para nonce se não existir
    if (!$('meta[name="wp-nonce"]').length) {
        $('head').append('<meta name="wp-nonce" content="premium-nonce">');
    }
    
    /**
     * Manipula abertura do modal de senha
     */
    function handlePasswordModalOpen(event) {
        const $btn = $(event.target);
        const userId = $btn.data('user-id');
        const userName = $btn.data('user-name');
        
        if (!userId || !userName) {
            console.error('Dados do usuário não encontrados');
            return;
        }
        
        // Preenche dados do modal
        $('#target-user-id').val(userId);
        $('#modal-user-name').text(userName);
        
        // Limpa formulário
        $('#password-form')[0].reset();
        hidePasswordFormStatus();
        hidePasswordStrength();
        hidePasswordMatch();
        
        // Mostra modal
        openPasswordModal();
    }
    
    /**
     * Abre modal de senha
     */
    function openPasswordModal() {
        $('#password-modal').removeClass('hidden').addClass('show');
        $('body').addClass('modal-open');
        
        // Foca no primeiro campo
        setTimeout(() => {
            $('#new-password').focus();
        }, 300);
    }
    
    /**
     * Fecha modal de senha
     */
    function closePasswordModal() {
        $('#password-modal').removeClass('show');
        $('body').removeClass('modal-open');
        
        setTimeout(() => {
            $('#password-modal').addClass('hidden');
            $('#password-form')[0].reset();
            hidePasswordFormStatus();
            hidePasswordStrength();
            hidePasswordMatch();
        }, 300);
    }
    
    /**
     * Manipula envio do formulário de senha
     */
    function handlePasswordSubmit(event) {
        event.preventDefault();
        
        const userId = $('#target-user-id').val();
        const newPassword = $('#new-password').val();
        const confirmPassword = $('#confirm-password').val();
        
        // Validações
        if (!userId) {
            showPasswordFormStatus('ID do usuário não encontrado', 'error');
            return;
        }
        
        if (newPassword.length < 6) {
            showPasswordFormStatus('A senha deve ter pelo menos 6 caracteres', 'error');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            showPasswordFormStatus('As senhas não coincidem', 'error');
            return;
        }
        
        // Mostra loading
        setPasswordFormLoading(true);
        showPasswordFormStatus('Alterando senha...', 'info');
        
        // Envia requisição AJAX
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'change_user_password_premium',
                user_id: userId,
                new_password: newPassword,
                nonce: $('meta[name="wp-nonce"]').attr('content') || 'premium-nonce'
            },
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                showPasswordFormStatus('Senha alterada com sucesso!', 'success');
                setTimeout(() => {
                    closePasswordModal();
                    showStatusMessage('Senha do usuário alterada com sucesso!', 'success');
                }, 1500);
            } else {
                showPasswordFormStatus(response.data || 'Erro ao alterar senha', 'error');
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'Erro de conexão';
            if (xhr.responseJSON && xhr.responseJSON.data) {
                errorMessage = xhr.responseJSON.data;
            }
            showPasswordFormStatus(errorMessage, 'error');
        })
        .always(function() {
            setPasswordFormLoading(false);
        });
    }
    
    /**
     * Valida força da senha
     */
    function validatePasswordStrength() {
        const password = $('#new-password').val();
        const $strengthContainer = $('.password-strength');
        const $strengthFill = $('.strength-fill');
        const $strengthText = $('.strength-text');
        
        if (password.length === 0) {
            hidePasswordStrength();
            return;
        }
        
        $strengthContainer.removeClass('hidden');
        
        let strength = 0;
        let strengthText = '';
        let strengthClass = '';
        
        // Critérios de força
        if (password.length >= 6) strength += 25;
        if (password.length >= 8) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        if (/[^A-Za-z0-9]/.test(password)) strength += 25;
        
        // Limita a 100
        strength = Math.min(strength, 100);
        
        // Define texto e classe
        if (strength < 50) {
            strengthText = 'Fraca';
            strengthClass = 'weak';
        } else if (strength < 75) {
            strengthText = 'Média';
            strengthClass = 'medium';
        } else {
            strengthText = 'Forte';
            strengthClass = 'strong';
        }
        
        // Atualiza visual
        $strengthFill.css('width', strength + '%')
                    .removeClass('weak medium strong')
                    .addClass(strengthClass);
        $strengthText.text(`Força da senha: ${strengthText}`);
    }
    
    /**
     * Valida se as senhas coincidem
     */
    function validatePasswordMatch() {
        const password = $('#new-password').val();
        const confirmPassword = $('#confirm-password').val();
        const $indicator = $('#password-match-indicator');
        const $matchText = $indicator.find('.match-text');
        
        if (confirmPassword.length === 0) {
            hidePasswordMatch();
            return;
        }
        
        $indicator.removeClass('hidden match no-match');
        
        if (password === confirmPassword) {
            $indicator.addClass('match');
            $matchText.text('✓ Senhas coincidem');
        } else {
            $indicator.addClass('no-match');
            $matchText.text('✗ Senhas não coincidem');
        }
    }
    
    /**
     * Mostra status do formulário de senha
     */
    function showPasswordFormStatus(message, type) {
        const $status = $('#password-form-status');
        const $statusText = $status.find('.status-text');
        
        $status.removeClass('hidden success error info')
               .addClass(type);
        $statusText.text(message);
    }
    
    /**
     * Esconde status do formulário de senha
     */
    function hidePasswordFormStatus() {
        $('#password-form-status').addClass('hidden');
    }
    
    /**
     * Esconde indicador de força da senha
     */
    function hidePasswordStrength() {
        $('.password-strength').addClass('hidden');
    }
    
    /**
     * Esconde indicador de coincidência de senhas
     */
    function hidePasswordMatch() {
        $('#password-match-indicator').addClass('hidden');
    }
    
    /**
     * Define estado de loading do formulário
     */
    function setPasswordFormLoading(isLoading) {
        const $submitBtn = $('.btn-save');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoading = $submitBtn.find('.btn-loading');
        
        if (isLoading) {
            $submitBtn.prop('disabled', true);
            $btnText.addClass('hidden');
            $btnLoading.removeClass('hidden');
        } else {
            $submitBtn.prop('disabled', false);
            $btnText.removeClass('hidden');
            $btnLoading.addClass('hidden');
        }
    }
    
    // Adiciona funções ao escopo global para compatibilidade
    window.openPasswordModal = openPasswordModal;
    window.closePasswordModal = closePasswordModal;
});