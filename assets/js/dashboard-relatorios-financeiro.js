document.addEventListener('DOMContentLoaded', function () {
    console.log('Dashboard Relatórios Financeiro - Inicializando...');
    
    // Check if tab buttons exist
    const tabButtons = document.querySelectorAll('.modern-tab-button');
    console.log('Tab buttons found:', tabButtons.length);
    
    // Aguarda um pouco para garantir que o dashboard-data.js foi carregado
    setTimeout(function() {
        // Verifica se a função initializeDashboard existe no escopo global (fornecida por dashboard-data.js)
        if (typeof initializeDashboard === 'function') {
            console.log('✓ Função initializeDashboard encontrada, inicializando dashboard...');
            initializeDashboard();
        } else if (typeof window.DashboardRelatorios !== 'undefined' && typeof window.DashboardRelatorios.init === 'function') {
            console.log('✓ DashboardRelatorios.init encontrada, inicializando dashboard...');
            window.DashboardRelatorios.init();
        } else {
            console.error('Erro: A biblioteca principal do dashboard (dashboard-data.js) não foi carregada ou as funções não estão disponíveis.');
            console.log('Tentando inicialização manual...');
            
            // Fallback: inicialização manual básica
            if (typeof dashboardRelatoriosAjax !== 'undefined') {
                console.log('dashboardRelatoriosAjax disponível, tentando inicialização básica');
                
                // Manual tab button setup
                const buttons = document.querySelectorAll('.modern-tab-button');
                console.log('Manual setup: Found buttons:', buttons.length);
                
                buttons.forEach(button => {
                    button.addEventListener('click', function() {
                        console.log('Manual: Tab clicked:', this.dataset.period);
                        
                        buttons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        const period = this.dataset.period;
                        
                        // Call functions if they exist
                        if (typeof updateCardsOnly === 'function') {
                            updateCardsOnly(period);
                        }
                        if (typeof updateExpensesCardsOnly === 'function') {
                            updateExpensesCardsOnly(period);
                        }
                        if (typeof updateTopProducts === 'function') {
                            updateTopProducts(period);
                        }
                        if (typeof updateTopAssociados === 'function') {
                            updateTopAssociados(period);
                        }
                        if (typeof loadCategorySalesChart === 'function') {
                            loadCategorySalesChart(period);
                        }
                        if (typeof loadPaymentMethodsChart === 'function') {
                            loadPaymentMethodsChart(period);
                        }
                        if (typeof updateAdditionalIndicators === 'function') {
                            updateAdditionalIndicators(period);
                        }
                    });
                });
                
                // Load initial data
                if (typeof updateCardsOnly === 'function') {
                    updateCardsOnly('all');
                }
                if (typeof updateExpensesCardsOnly === 'function') {
                    updateExpensesCardsOnly('all');
                }
                if (typeof loadDailySalesChart === 'function') {
                    loadDailySalesChart();
                }
                if (typeof updateTopProducts === 'function') {
                    updateTopProducts('all');
                }
                if (typeof updateTopAssociados === 'function') {
                    updateTopAssociados('all');
                }
                if (typeof loadCategorySalesChart === 'function') {
                    loadCategorySalesChart('all');
                }
                if (typeof loadPaymentMethodsChart === 'function') {
                    loadPaymentMethodsChart('all');
                }
                if (typeof updateAdditionalIndicators === 'function') {
                    updateAdditionalIndicators('all');
                }
            }
        }
    }, 500);
});