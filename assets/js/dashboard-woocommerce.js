/**
 * Dashboard WooCommerce - Sistema de Controle de Acesso e Interface Moderna
 * Funcionalidades específicas para o dashboard de pedidos
 */
(function () {
    'use strict';

    // Prevent multiple script execution
    if (window.dashboardWooCommerceLoaded) {
        console.warn('Dashboard WooCommerce script already loaded, skipping...');
        return;
    }
    window.dashboardWooCommerceLoaded = true;

    console.log('Dashboard WooCommerce script loading...');

    // Check dependencies
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not loaded - dashboard may not work correctly');
        return;
    }

    // Global namespace
    window.DashboardWooCommerce = window.DashboardWooCommerce || {};

    /**
     * Sistema de Pedidos WooCommerce
     * Gerencia operações específicas de pedidos
     */
    class OrderManager {
        static updateOrderDisplay(orderId, updatedData) {
            // Atualizar badges de status operacional
            const statusContainer = document.querySelector(`[data-order-id="${orderId}"] [data-operational-statuses]`);
            if (statusContainer && updatedData.operational_statuses) {
                statusContainer.innerHTML = this.renderOperationalStatuses(updatedData.operational_statuses);
            }

            // Atualizar status do pedido
            const statusBadge = document.querySelector(`[data-order-id="${orderId}"] [data-order-status]`);
            if (statusBadge && updatedData.status_text) {
                statusBadge.innerHTML = this.createBadge(updatedData.status_text, this.getStatusVariant(updatedData.order_status_slug));
            }

            // Atualizar total do pedido
            const totalElement = document.querySelector(`[data-order-id="${orderId}"] [data-order-total]`);
            if (totalElement && updatedData.formatted_order_total) {
                totalElement.textContent = updatedData.formatted_order_total;
            }

            // Atualizar informações extras (cidade/estado e alerta de receitas)
            this.updateInfosExtra(orderId, updatedData);
        }

        static updateInfosExtra(orderId, data) {
            const infosExtraContainer = document.querySelector(`.infos_extra[data-order-id="${orderId}"]`);
            if (!infosExtraContainer) {
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
            
            // Adicionar alerta de receitas vencidas se aplicável
            if (data.tem_receitas_vencidas) {
                console.debug('[DEBUG] Adicionando alerta de receitas vencidas:', data.receitas_vencidas_texto);
                infosHtml += `
                    <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <span>${data.receitas_vencidas_texto}</span>
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
            infosExtraContainer.innerHTML = infosHtml;
            console.debug('[DEBUG] Informações extras atualizadas para pedido:', orderId, 'HTML final:', infosHtml);
        }

        static renderOperationalStatuses(statuses) {
            let html = '';
            
            Object.entries(statuses).forEach(([key, status]) => {
                const variant = this.getOperationalStatusVariant(key);
                html += this.createBadge(status.label, variant, status.icon) + ' ';
            });
            
            return html.trim();
        }

        static createBadge(text, variant = 'default', icon = null) {
            const variants = {
                default: 'inline-flex items-center justify-center rounded-md border border-transparent bg-primary text-primary-foreground px-2 py-0.5 text-xs font-medium',
                secondary: 'inline-flex items-center justify-center rounded-md border border-transparent bg-secondary text-secondary-foreground px-2 py-0.5 text-xs font-medium',
                destructive: 'inline-flex items-center justify-center rounded-md border border-transparent bg-destructive text-white px-2 py-0.5 text-xs font-medium',
                outline: 'inline-flex items-center justify-center rounded-md border text-foreground px-2 py-0.5 text-xs font-medium',
                success: 'inline-flex items-center justify-center rounded-md border border-transparent bg-green-100 text-green-800 px-2 py-0.5 text-xs font-medium',
                warning: 'inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs font-medium',
                info: 'inline-flex items-center justify-center rounded-md border border-transparent bg-blue-100 text-blue-800 px-2 py-0.5 text-xs font-medium'
            };

            const badgeClass = variants[variant] || variants.default;
            const iconHtml = icon ? `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>` : '';
            
            return `<span class="${badgeClass} gap-1 whitespace-nowrap shrink-0">${iconHtml}${text}</span>`;
        }

        static getStatusVariant(status) {
            const variants = {
                'pending': 'warning',
                'processing': 'info',
                'on-hold': 'warning',
                'completed': 'success',
                'cancelled': 'destructive',
                'refunded': 'secondary',
                'failed': 'destructive'
            };
            
            return variants[status] || 'default';
        }

        static getOperationalStatusVariant(metaKey) {
            const variants = {
                '_forma_pagamento_woo': 'info',
                '_forma_entrega_woo': 'secondary',
                '_status_entrega': 'success',
                '_extracao': 'warning'
            };
            
            return variants[metaKey] || 'default';
        }
    }

    /**
     * Sistema de Notificações
     * Sistema de notificações usando componentes modernos
     */
    class NotificationSystem {
        static show(title, message, type = 'info', duration = 5000) {
            // Criar container se não existir
            let container = document.querySelector('#notification-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed top-4 right-4 z-50 space-y-2 max-w-sm';
                document.body.appendChild(container);
            }

            // Criar notificação
            const notification = document.createElement('div');
            notification.className = `transform transition-all duration-300 translate-x-full opacity-0 p-4 rounded-lg shadow-lg ${this.getNotificationClass(type)}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${this.getNotificationIcon(type)}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${title}</p>
                        <p class="text-sm">${message}</p>
                    </div>
                </div>
            `;
            
            container.appendChild(notification);

            // Animar entrada
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto remover
            if (duration > 0) {
                setTimeout(() => {
                    notification.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, duration);
            }
        }

        static getNotificationClass(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800',
                error: 'bg-red-50 border border-red-200 text-red-800',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
                info: 'bg-blue-50 border border-blue-200 text-blue-800'
            };
            return classes[type] || classes.info;
        }

        static getNotificationIcon(type) {
            const icons = {
                success: '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                error: '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                warning: '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                info: '<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };
            return icons[type] || icons.info;
        }

        static success(title, message, duration = 5000) {
            this.show(title, message, 'success', duration);
        }

        static error(title, message, duration = 7000) {
            this.show(title, message, 'error', duration);
        }

        static warning(title, message, duration = 6000) {
            this.show(title, message, 'warning', duration);
        }

        static info(title, message, duration = 5000) {
            this.show(title, message, 'info', duration);
        }
    }

    // Expor classes para uso global
    window.DashboardWooCommerce.OrderManager = OrderManager;
    window.DashboardWooCommerce.NotificationSystem = NotificationSystem;

    // Auto-inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard WooCommerce inicializado');
        });
    } else {
        console.log('Dashboard WooCommerce inicializado');
    }

})();