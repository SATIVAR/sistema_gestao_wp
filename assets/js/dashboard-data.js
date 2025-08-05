// Dashboard Relatórios - Main Script
(function () {
    'use strict';

    // Prevent multiple script execution
    if (window.dashboardRelatoriosLoaded) {
        console.warn('Dashboard relatórios script already loaded, skipping...');
        return;
    }
    window.dashboardRelatoriosLoaded = true;

    console.log('Dashboard RELATÓRIOS data script loading...');

    // Check dependencies
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not loaded - dashboard may not work correctly');
        return;
    }

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded yet - charts may not work');
    } else {
        console.log('Chart.js available, version:', Chart.version);
    }

    // Wrapper para isolar o código do dashboard de relatórios
    window.DashboardRelatorios = window.DashboardRelatorios || {};

    // Global state management
    const DashboardState = {
        currentPeriod: 'all',
        isLoading: false,
        charts: {},
        animationQueue: [],
        initialized: false
    };

    // Initialize dashboard system
    window.DashboardRelatorios.init = function () {
        console.log('DashboardRelatorios.init() called');

        // Prevent multiple initializations
        if (DashboardState.initialized) {
            console.warn('Dashboard already initialized, skipping...');
            return;
        }

        // Check dependencies
        if (typeof jQuery === 'undefined') {
            console.error('jQuery not available');
            return;
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js not available');
            return;
        }

        // Wait for dashboardRelatoriosAjax if not available
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.warn('dashboardRelatoriosAjax not available, waiting...');
            setTimeout(function () {
                window.DashboardRelatorios.init();
            }, 200);
            return;
        }

        console.log('✓ All dependencies available, initializing dashboard...');

        // Destroy any existing charts first
        destroyAllCharts();

        // Initialize components
        initPeriodButtons();
        initChartInstances();
        loadInitialData();

        // Mark as initialized
        DashboardState.initialized = true;

        console.log('Dashboard initialized successfully');
    };

    // Initialize chart instances
    function initChartInstances() {
        DashboardState.charts = {
            dailySales: null,
            categorySales: null,
            paymentMethods: null,
            expensesCategory: null,
            expensesTimeline: null,
            monthlySales: null,
            annualSales: null
        };
    }

    // Destroy all charts safely
    function destroyAllCharts() {
        Object.keys(DashboardState.charts).forEach(chartKey => {
            if (DashboardState.charts[chartKey]) {
                try {
                    DashboardState.charts[chartKey].destroy();
                } catch (e) {
                    console.warn(`Error destroying chart ${chartKey}:`, e);
                }
                DashboardState.charts[chartKey] = null;
            }
        });
    }

    // Initialize period buttons
    function initPeriodButtons() {
        const periodButtons = document.querySelectorAll('.modern-tab-button');
        console.log('Found period buttons:', periodButtons.length);

        periodButtons.forEach(button => {
            button.addEventListener('click', function () {
                if (DashboardState.isLoading) {
                    console.log('Dashboard is loading, ignoring tab click');
                    return;
                }

                const period = this.dataset.period;
                console.log('Tab button clicked:', period);

                // Update state
                DashboardState.currentPeriod = period;
                DashboardState.isLoading = true;

                // Update UI
                periodButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Update all dashboard data
                updateDashboardData(period).finally(() => {
                    DashboardState.isLoading = false;
                });
            });
        });
    }

    // Load initial data
    function loadInitialData() {
        console.log('Loading initial dashboard data...');
        DashboardState.isLoading = true;

        updateDashboardData('all').finally(() => {
            DashboardState.isLoading = false;
            console.log('Initial data loading completed');
        });
    }

    // Centralized dashboard data update
    async function updateDashboardData(period) {
        console.log('Updating dashboard data for period:', period);

        try {
            // Update all components in parallel
            const promises = [
                updateSalesMetrics(period),
                updateExpensesMetrics(period),
                updateTopProducts(period),
                updateTopAssociados(period),
                loadCategorySalesChart(period),
                loadPaymentMethodsChart(period),
                updateAdditionalIndicators(period)
            ];

            // Daily sales chart is always last 7 days
            if (period === 'all' || !DashboardState.charts.dailySales) {
                promises.push(loadDailySalesChart());
            }

            await Promise.allSettled(promises);
            console.log('Dashboard data update completed for period:', period);
        } catch (error) {
            console.error('Error updating dashboard data:', error);
        }
    }

    // Update sales metrics
    async function updateSalesMetrics(period) {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for sales metrics');
            return;
        }

        console.log('Loading sales metrics for period:', period);
        showSalesMetricsSkeleton();

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_dashboard_metrics_cards',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Sales metrics response:', response);
                    if (response.success && response.data && response.data.metrics) {
                        updateSalesMetricsDisplay(response.data.metrics);
                        resolve(response.data.metrics);
                    } else {
                        console.error('Error loading sales metrics:', response);
                        reject(new Error('Invalid sales metrics response'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading sales metrics:', error);
                    reject(new Error('AJAX error: ' + error));
                }
            });
        });
    }

    // Update expenses metrics
    async function updateExpensesMetrics(period) {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for expenses metrics');
            return;
        }

        console.log('Loading expenses metrics for period:', period);
        showExpensesMetricsSkeleton();

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_expenses_metrics',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Expenses metrics response:', response);
                    if (response.success && response.data) {
                        updateExpensesMetricsDisplay(response.data);
                        resolve(response.data);
                    } else {
                        console.error('Error loading expenses metrics:', response);
                        // Show default values instead of rejecting
                        updateExpensesMetricsDisplay({
                            totalExpenses: 0,
                            expensesCount: 0,
                            averageExpense: 0
                        });
                        resolve({
                            totalExpenses: 0,
                            expensesCount: 0,
                            averageExpense: 0
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading expenses metrics:', error);
                    // Show default values instead of rejecting
                    updateExpensesMetricsDisplay({
                        totalExpenses: 0,
                        expensesCount: 0,
                        averageExpense: 0
                    });
                    resolve({
                        totalExpenses: 0,
                        expensesCount: 0,
                        averageExpense: 0
                    });
                }
            });
        });
    }

    // Update top products
    async function updateTopProducts(period) {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for top products');
            return;
        }

        console.log('Loading top products for period:', period);

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_top_products',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Top products response:', response);
                    if (response.success && response.data && response.data.topProducts) {
                        populateTopProductsList(response.data);
                        resolve(response.data.topProducts);
                    } else {
                        console.error('Error loading top products:', response);
                        populateTopProductsList({ topProducts: [] });
                        resolve([]);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading top products:', error);
                    populateTopProductsList({ topProducts: [] });
                    resolve([]);
                }
            });
        });
    }

    // Update top associados
    async function updateTopAssociados(period) {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for top associados');
            return;
        }

        console.log('Loading top associados for period:', period);

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_top_associados',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Top associados response:', response);
                    console.log('Response success:', response.success);
                    console.log('Response data:', response.data);
                    if (response.data && response.data.topAssociados) {
                        console.log('Top associados count:', response.data.topAssociados.length);
                    }

                    if (response.success && response.data && response.data.topAssociados) {
                        populateTopAssociadosList(response.data);
                        resolve(response.data.topAssociados);
                    } else {
                        console.error('Error loading top associados:', response);
                        populateTopAssociadosList({ topAssociados: [] });
                        resolve([]);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading top associados:', error);
                    populateTopAssociadosList({ topAssociados: [] });
                    resolve([]);
                }
            });
        });
    }

    // Função para atualizar o card de Vendas do Mês Atual
    async function updateMonthlySalesCard() {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax não disponível para Vendas do Mês Atual');
            return;
        }
        const card = document.getElementById('monthly-sales-card');
        if (!card) return;
        card.classList.add('card-loading');
        jQuery.ajax({
            url: dashboardRelatoriosAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_monthly_sales_chart',
                _ajax_nonce: dashboardRelatoriosAjax.nonce
            },
            success: function (response) {
                if (response.success && response.data && typeof response.data.total === 'number') {
                    card.querySelector('.card-value').textContent = formatCurrency(response.data.total);
                } else {
                    card.querySelector('.card-value').textContent = 'R$ 0,00';
                }
                card.classList.remove('card-loading');
            },
            error: function () {
                card.querySelector('.card-value').textContent = 'Erro';
                card.classList.remove('card-loading');
            }
        });
    }

    // Função para atualizar o card de Vendas Anual
    async function updateAnnualSalesCard() {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax não disponível para Vendas Anual');
            return;
        }
        const card = document.getElementById('annual-sales-card');
        if (!card) return;
        card.classList.add('card-loading');
        jQuery.ajax({
            url: dashboardRelatoriosAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_annual_sales_chart',
                _ajax_nonce: dashboardRelatoriosAjax.nonce
            },
            success: function (response) {
                if (response.success && response.data && typeof response.data.total === 'number') {
                    card.querySelector('.card-value').textContent = formatCurrency(response.data.total);
                } else {
                    card.querySelector('.card-value').textContent = 'R$ 0,00';
                }
                card.classList.remove('card-loading');
            },
            error: function () {
                card.querySelector('.card-value').textContent = 'Erro';
                card.classList.remove('card-loading');
            }
        });
    }

    // Inicialização dos cards de vendas (chamada junto com o dashboard)
    function initVendasCards() {
        updateMonthlySalesCard();
        updateAnnualSalesCard();
    }

    // Função para carregar o gráfico de Vendas do Mês Atual (padrão Vendas Diárias)
    async function loadMonthlySalesChart() {
        const chartCanvas = document.getElementById('monthly-sales-chart');
        if (!chartCanvas) {
            console.warn('Canvas do gráfico de Vendas do Mês Atual não encontrado');
            return;
        }
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax não disponível para Vendas do Mês Atual');
            return;
        }
        const chartContainer = chartCanvas.closest('.chart-container');
        if (chartContainer) {
            chartContainer.classList.add('chart-loading');
        }
        // Destroy existing chart
        if (DashboardState.charts.monthlySales) {
            try {
                DashboardState.charts.monthlySales.destroy();
            } catch (e) {
                console.warn('Erro ao destruir gráfico de Vendas do Mês Atual:', e);
            }
            DashboardState.charts.monthlySales = null;
        }
        console.log('Carregando gráfico de Vendas do Mês Atual...');
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_monthly_sales_chart',
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Resposta gráfico Vendas Mês Atual:', response);
                    if (response.success && response.data && response.data.labels && response.data.data) {
                        const data = response.data;
                        const ctx = chartCanvas.getContext('2d');
                        DashboardState.charts.monthlySales = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Vendas Mês Atual',
                                    data: data.data,
                                    borderColor: 'rgb(56, 189, 248)',
                                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(56, 189, 248)',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 800,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) {
                                                return formatCurrency(value);
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });
                        resolve(data);
                    } else {
                        console.error('Erro ao carregar dados do gráfico Vendas Mês Atual:', response);
                        showEmptyChart(chartCanvas, 'Sem dados para o mês atual');
                        reject(new Error('Dados inválidos para gráfico de Vendas Mês Atual'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error Vendas Mês Atual:', error);
                    showEmptyChart(chartCanvas, 'Erro ao carregar gráfico');
                    reject(new Error('AJAX error: ' + error));
                },
                complete: function () {
                    if (chartContainer) {
                        chartContainer.classList.remove('chart-loading');
                    }
                }
            });
        });
    }

    // Função para carregar o gráfico de Vendas Anual (padrão Vendas Diárias)
    async function loadAnnualSalesChart() {
        const chartCanvas = document.getElementById('annual-sales-chart');
        if (!chartCanvas) {
            console.warn('Canvas do gráfico de Vendas Anual não encontrado');
            return;
        }
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax não disponível para Vendas Anual');
            return;
        }
        const chartContainer = chartCanvas.closest('.chart-container');
        if (chartContainer) {
            chartContainer.classList.add('chart-loading');
        }
        // Destroy existing chart
        if (DashboardState.charts.annualSales) {
            try {
                DashboardState.charts.annualSales.destroy();
            } catch (e) {
                console.warn('Erro ao destruir gráfico de Vendas Anual:', e);
            }
            DashboardState.charts.annualSales = null;
        }
        console.log('Carregando gráfico de Vendas Anual...');
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_annual_sales_chart',
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Resposta gráfico Vendas Anual:', response);
                    if (response.success && response.data && response.data.labels && response.data.data) {
                        const data = response.data;
                        const ctx = chartCanvas.getContext('2d');
                        DashboardState.charts.annualSales = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Vendas Anual',
                                    data: data.data,
                                    borderColor: 'rgb(163, 230, 53)',
                                    backgroundColor: 'rgba(163, 230, 53, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(163, 230, 53)',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 800,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) {
                                                return formatCurrency(value);
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });
                        resolve(data);
                    } else {
                        console.error('Erro ao carregar dados do gráfico Vendas Anual:', response);
                        showEmptyChart(chartCanvas, 'Sem dados para o ano atual');
                        reject(new Error('Dados inválidos para gráfico de Vendas Anual'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error Vendas Anual:', error);
                    showEmptyChart(chartCanvas, 'Erro ao carregar gráfico');
                    reject(new Error('AJAX error: ' + error));
                },
                complete: function () {
                    if (chartContainer) {
                        chartContainer.classList.remove('chart-loading');
                    }
                }
            });
        });
    }

    // Adiciona a inicialização dos cards ao init principal do dashboard
    const originalInit = window.DashboardRelatorios.init;
    window.DashboardRelatorios.init = function () {
        if (typeof originalInit === 'function') originalInit();
        initVendasCards();
        loadMonthlySalesChart();
        loadAnnualSalesChart();
    }

    // Update additional indicators
    async function updateAdditionalIndicators(period) {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for additional indicators');
            return;
        }

        console.log('Loading additional indicators for period:', period);
        showAdditionalIndicatorsSkeleton();

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_additional_indicators',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Additional indicators response:', response);
                    if (response.success && response.data && response.data.indicators) {
                        updateAdditionalIndicatorsDisplay(response.data.indicators);
                        resolve(response.data.indicators);
                    } else {
                        console.error('Error loading additional indicators:', response);
                        // Show default values instead of rejecting
                        updateAdditionalIndicatorsDisplay({
                            newCustomers: 0,
                            returningCustomers: 0,
                            cartAbandonmentRate: 0,
                            refundRate: 0,
                            customerLifetimeValue: 0
                        });
                        resolve({
                            newCustomers: 0,
                            returningCustomers: 0,
                            cartAbandonmentRate: 0,
                            refundRate: 0,
                            customerLifetimeValue: 0
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading additional indicators:', error);
                    // Show default values instead of rejecting
                    updateAdditionalIndicatorsDisplay({
                        newCustomers: 0,
                        returningCustomers: 0,
                        cartAbandonmentRate: 0,
                        refundRate: 0,
                        customerLifetimeValue: 0
                    });
                    resolve({
                        newCustomers: 0,
                        returningCustomers: 0,
                        cartAbandonmentRate: 0,
                        refundRate: 0,
                        customerLifetimeValue: 0
                    });
                }
            });
        });
    }

    // Show skeleton for additional indicators
    function showAdditionalIndicatorsSkeleton() {
        const indicatorCards = document.querySelectorAll('#new-customers, #returning-customers, #cart-abandonment, #refund-rate, #customer-lifetime-value');

        indicatorCards.forEach(element => {
            if (element) {
                const card = element.closest('.modern-card');
                if (card) {
                    clearElementTransition(card);

                    animateElement(card,
                        { opacity: '1', transform: 'scale(1)' },
                        { opacity: '0.6', transform: 'scale(0.98)' }
                    );
                }

                if (!element.innerHTML.includes('animate-pulse')) {
                    element.innerHTML = '<div class="h-8 bg-slate-300 rounded-lg w-2/3 animate-pulse"></div>';
                }

                if (card) {
                    card.classList.add('card-loading');
                }
            }
        });
    }

    // Update additional indicators display
    function updateAdditionalIndicatorsDisplay(indicators) {
        const indicatorCards = document.querySelectorAll('#new-customers, #returning-customers, #cart-abandonment, #refund-rate, #customer-lifetime-value');

        indicatorCards.forEach(element => {
            if (element) {
                const card = element.closest('.modern-card');
                if (card) {
                    // Remove skeleton content
                    if (element.innerHTML.includes('animate-pulse')) {
                        element.innerHTML = '';
                    }

                    // Animate back to normal state
                    animateElement(card,
                        { opacity: '0.6', transform: 'scale(0.98)' },
                        { opacity: '1', transform: 'scale(1)' }
                    );

                    card.classList.remove('card-loading');
                }
            }
        });

        // Update values
        const updateElement = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        };

        updateElement('new-customers', formatNumber(indicators.newCustomers || 0));
        updateElement('returning-customers', formatNumber(indicators.returningCustomers || 0));
        updateElement('cart-abandonment', (indicators.cartAbandonmentRate || 0) + '%');
        updateElement('refund-rate', (indicators.refundRate || 0) + '%');
        updateElement('customer-lifetime-value', formatCurrency(indicators.customerLifetimeValue || 0));
    }

    // Utility functions
    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value || 0);
    }

    function formatNumber(value) {
        return new Intl.NumberFormat('pt-BR').format(value || 0);
    }

    // Animation helpers
    function animateElement(element, fromState, toState, duration = 300) {
        return new Promise((resolve) => {
            if (!element) {
                resolve();
                return;
            }

            // Apply from state
            Object.assign(element.style, fromState);

            // Force reflow
            element.offsetHeight;

            // Apply transition
            element.style.transition = `all ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;

            // Apply to state
            Object.assign(element.style, toState);

            setTimeout(() => {
                element.style.transition = '';
                resolve();
            }, duration);
        });
    }

    function clearElementTransition(element) {
        if (element) {
            element.style.transition = '';
            element.style.transform = '';
            element.style.opacity = '';
        }
    }

    // Show skeleton for sales metrics cards
    function showSalesMetricsSkeleton() {
        const salesCards = document.querySelectorAll('.metric-card-primary, .metric-card-secondary, .metric-card-success');

        salesCards.forEach(card => {
            clearElementTransition(card);

            animateElement(card,
                { opacity: '1', transform: 'scale(1)' },
                { opacity: '0.6', transform: 'scale(0.98)' }
            );

            const valueElements = card.querySelectorAll('[id^="gross-sales"], [id^="total-orders"], [id^="items-sold"], [id^="average-order"], [id^="conversion-rate"]');
            valueElements.forEach(element => {
                if (!element.innerHTML.includes('animate-pulse')) {
                    element.innerHTML = '<div class="h-8 bg-white/40 rounded-lg w-2/3 animate-pulse"></div>';
                }
            });

            card.classList.add('card-loading');
        });
    }

    // Show skeleton for expenses metrics cards  
    function showExpensesMetricsSkeleton() {
        const expensesCards = document.querySelectorAll('.metric-card-warning, .metric-card-info, .metric-card-purple');

        expensesCards.forEach(card => {
            clearElementTransition(card);

            animateElement(card,
                { opacity: '1', transform: 'scale(1)' },
                { opacity: '0.6', transform: 'scale(0.98)' }
            );

            const valueElements = card.querySelectorAll('[id^="total-expenses"], [id^="expenses-count"], [id^="average-expense"]');
            valueElements.forEach(element => {
                if (!element.innerHTML.includes('animate-pulse')) {
                    element.innerHTML = '<div class="h-8 bg-white/40 rounded-lg w-2/3 animate-pulse"></div>';
                }
            });

            card.classList.add('card-loading');
        });
    }

    // Update sales metrics display
    function updateSalesMetricsDisplay(metrics) {
        // Restore loading cards
        const salesCards = document.querySelectorAll('.metric-card-primary, .metric-card-secondary, .metric-card-success');

        salesCards.forEach(card => {
            // Remove skeleton content
            const valueElements = card.querySelectorAll('[id^="gross-sales"], [id^="total-orders"], [id^="items-sold"], [id^="average-order"], [id^="conversion-rate"]');
            valueElements.forEach(element => {
                if (element.innerHTML.includes('animate-pulse')) {
                    element.innerHTML = '';
                }
            });

            // Animate back to normal state
            animateElement(card,
                { opacity: '0.6', transform: 'scale(0.98)' },
                { opacity: '1', transform: 'scale(1)' }
            );

            card.classList.remove('card-loading');
        });

        // Update values
        const updateElement = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        };

        updateElement('gross-sales', formatCurrency(metrics.grossSales));
        updateElement('total-orders', formatNumber(metrics.totalOrders));
        updateElement('items-sold', formatNumber(metrics.itemsSold));
        updateElement('average-order', formatCurrency(metrics.averageOrder));
        updateElement('conversion-rate', metrics.conversionRate + '%');
    }

    // Update expenses metrics display
    function updateExpensesMetricsDisplay(metrics) {
        // Restore loading cards
        const expensesCards = document.querySelectorAll('.metric-card-warning, .metric-card-info, .metric-card-purple');

        expensesCards.forEach(card => {
            // Remove skeleton content
            const valueElements = card.querySelectorAll('[id^="total-expenses"], [id^="expenses-count"], [id^="average-expense"]');
            valueElements.forEach(element => {
                if (element.innerHTML.includes('animate-pulse')) {
                    element.innerHTML = '';
                }
            });

            // Animate back to normal state
            animateElement(card,
                { opacity: '0.6', transform: 'scale(0.98)' },
                { opacity: '1', transform: 'scale(1)' }
            );

            card.classList.remove('card-loading');
        });

        // Update values
        const updateElement = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        };

        updateElement('total-expenses', formatCurrency(metrics.totalExpenses || 0));
        updateElement('expenses-count', formatNumber(metrics.expensesCount || 0));
        updateElement('average-expense', formatCurrency(metrics.averageExpense || 0));
    }

    // Load daily sales chart - always last 7 days
    async function loadDailySalesChart() {
        const chartCanvas = document.getElementById('daily-sales-chart');
        if (!chartCanvas) {
            console.warn('Daily sales chart canvas not found');
            return;
        }

        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for daily sales chart');
            return;
        }

        const chartContainer = chartCanvas.closest('.chart-container');
        if (chartContainer) {
            chartContainer.classList.add('chart-loading');
        }

        // Destroy existing chart
        if (DashboardState.charts.dailySales) {
            try {
                DashboardState.charts.dailySales.destroy();
            } catch (e) {
                console.warn('Error destroying daily sales chart:', e);
            }
            DashboardState.charts.dailySales = null;
        }

        console.log('Loading daily sales chart...');

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_daily_sales_chart',
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Daily sales chart response:', response);
                    if (response.success && response.data && response.data.dailySales) {
                        const data = response.data.dailySales;
                        const ctx = chartCanvas.getContext('2d');

                        DashboardState.charts.dailySales = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Vendas (R$)',
                                    data: data.data,
                                    borderColor: 'rgb(99, 102, 241)',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(99, 102, 241)',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 800,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function (value) {
                                                return formatCurrency(value);
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });

                        resolve(data);
                    } else {
                        console.error('Error loading daily sales chart data:', response);
                        reject(new Error('Invalid daily sales chart response'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading daily sales chart:', error);
                    reject(new Error('AJAX error: ' + error));
                },
                complete: function () {
                    if (chartContainer) {
                        chartContainer.classList.remove('chart-loading');
                    }
                }
            });
        });
    }

    // Update top products list
    function populateTopProductsList(data) {
        const container = document.getElementById('top-products-list');
        if (!container) return;

        // Clear existing content with animation
        animateElement(container,
            { opacity: '1', transform: 'translateY(0)' },
            { opacity: '0.5', transform: 'translateY(10px)' }
        ).then(() => {
            container.innerHTML = '';

            if (data.topProducts && data.topProducts.length > 0) {
                data.topProducts.forEach((product, index) => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors';
                    item.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-200 bg-gradient-to-r from-blue-500 to-purple-600 text-green-700 text-sm font-bold">
                                ${index + 1}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-900">${product.name}</div>
                                <div class="text-xs text-slate-500">Produto mais vendido</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-900">${product.quantity}</div>
                            <div class="text-xs text-slate-500">vendidos</div>
                        </div>
                    `;

                    // Initial state for animation
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    container.appendChild(item);

                    // Animate in with delay
                    setTimeout(() => {
                        animateElement(item,
                            { opacity: '0', transform: 'translateX(-20px)' },
                            { opacity: '1', transform: 'translateX(0)' }
                        );
                    }, index * 100);
                });
            } else {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-slate-500">Nenhum produto vendido ainda</p>
                    </div>
                `;
            }

            // Animate container back in
            setTimeout(() => {
                animateElement(container,
                    { opacity: '0.5', transform: 'translateY(10px)' },
                    { opacity: '1', transform: 'translateY(0)' }
                );
            }, 100);
        });
    }

    // Update top associados list
    function populateTopAssociadosList(data) {
        console.log('populateTopAssociadosList called with data:', data);
        const container = document.getElementById('top-associados-list');
        if (!container) {
            console.error('top-associados-list container not found');
            return;
        }
        console.log('Container found:', container);

        // Clear existing content with animation
        animateElement(container,
            { opacity: '1', transform: 'translateY(0)' },
            { opacity: '0.5', transform: 'translateY(10px)' }
        ).then(() => {
            container.innerHTML = '';

            if (data.topAssociados && data.topAssociados.length > 0) {
                console.log('Processing', data.topAssociados.length, 'associados');
                data.topAssociados.forEach((associado, index) => {
                    const item = document.createElement('div');
                    item.className = 'ranking-position flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors';

                    // Get position colors
                    let positionColor = 'ranking-default';
                    if (index === 0) positionColor = 'ranking-gold'; // Gold
                    else if (index === 1) positionColor = 'ranking-silver'; // Silver
                    else if (index === 2) positionColor = 'ranking-bronze'; // Bronze

                    item.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full ${positionColor} text-white text-sm font-bold">
                                ${associado.position}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-900">${associado.name}</div>
                                <div class="text-xs text-slate-500">${associado.orders} pedidos • Ticket médio: ${formatCurrency(associado.average)}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-900">${formatCurrency(associado.total)}</div>
                            <div class="text-xs text-slate-500">total comprado</div>
                        </div>
                    `;

                    // Initial state for animation
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    container.appendChild(item);

                    // Animate in with delay
                    setTimeout(() => {
                        animateElement(item,
                            { opacity: '0', transform: 'translateX(-20px)' },
                            { opacity: '1', transform: 'translateX(0)' }
                        );
                    }, index * 50); // Faster animation for more items
                });
            } else {
                console.log('No associados data found, showing empty state');
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                        </svg>
                        <p class="text-slate-500">Nenhum cliente com compras ainda</p>
                        <p class="text-xs text-slate-400 mt-2">Debug: ${JSON.stringify(data).substring(0, 100)}...</p>
                    </div>
                `;
            }

            // Animate container back in
            setTimeout(() => {
                animateElement(container,
                    { opacity: '0.5', transform: 'translateY(10px)' },
                    { opacity: '1', transform: 'translateY(0)' }
                );
            }, 100);
        });
    }

    // Load category sales chart
    async function loadCategorySalesChart(period) {
        const chartCanvas = document.getElementById('category-sales-chart');
        if (!chartCanvas) {
            console.warn('Category sales chart canvas not found');
            return;
        }

        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for category sales chart');
            return;
        }

        const chartContainer = chartCanvas.closest('.chart-container');
        if (chartContainer) {
            chartContainer.classList.add('chart-loading');
        }

        // Destroy existing chart
        if (DashboardState.charts.categorySales) {
            try {
                DashboardState.charts.categorySales.destroy();
            } catch (e) {
                console.warn('Error destroying category sales chart:', e);
            }
            DashboardState.charts.categorySales = null;
        }

        console.log('Loading category sales chart for period:', period);

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_category_sales_chart',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Category sales chart response:', response);
                    if (response.success && response.data && response.data.categorySales) {
                        const data = response.data.categorySales;

                        if (data.labels && data.labels.length > 0) {
                            const ctx = chartCanvas.getContext('2d');
                            DashboardState.charts.categorySales = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: data.labels,
                                    datasets: [{
                                        data: data.data,
                                        backgroundColor: [
                                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                                        ],
                                        borderWidth: 2,
                                        borderColor: '#ffffff'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 800,
                                        easing: 'easeInOutQuart'
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                padding: 20,
                                                usePointStyle: true
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function (context) {
                                                    return context.label + ': ' + formatCurrency(context.parsed);
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            showEmptyChart(chartCanvas, 'Nenhum dado disponível');
                        }

                        resolve(data);
                    } else {
                        console.error('Error loading category sales chart data:', response);
                        showEmptyChart(chartCanvas, 'Erro ao carregar dados');
                        reject(new Error('Invalid category sales chart response'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading category sales chart:', error);
                    showEmptyChart(chartCanvas, 'Erro de conexão');
                    reject(new Error('AJAX error: ' + error));
                },
                complete: function () {
                    if (chartContainer) {
                        chartContainer.classList.remove('chart-loading');
                    }
                }
            });
        });
    }

    // Load payment methods chart
    async function loadPaymentMethodsChart(period) {
        const chartCanvas = document.getElementById('payment-methods-chart');
        if (!chartCanvas) {
            console.warn('Payment methods chart canvas not found');
            return;
        }

        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available for payment methods chart');
            return;
        }

        const chartContainer = chartCanvas.closest('.chart-container');
        if (chartContainer) {
            chartContainer.classList.add('chart-loading');
        }

        // Destroy existing chart
        if (DashboardState.charts.paymentMethods) {
            try {
                DashboardState.charts.paymentMethods.destroy();
            } catch (e) {
                console.warn('Error destroying payment methods chart:', e);
            }
            DashboardState.charts.paymentMethods = null;
        }

        console.log('Loading payment methods chart for period:', period);

        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: dashboardRelatoriosAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_payment_methods_chart',
                    period: period,
                    _ajax_nonce: dashboardRelatoriosAjax.nonce
                },
                success: function (response) {
                    console.log('Payment methods chart response:', response);
                    if (response.success && response.data && response.data.paymentMethods) {
                        const data = response.data.paymentMethods;

                        if (data.labels && data.labels.length > 0) {
                            const ctx = chartCanvas.getContext('2d');
                            DashboardState.charts.paymentMethods = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.labels,
                                    datasets: [{
                                        label: 'Receita (R$)',
                                        data: data.data,
                                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 800,
                                        easing: 'easeInOutQuart'
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function (context) {
                                                    return 'Receita: ' + formatCurrency(context.parsed.y);
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function (value) {
                                                    return formatCurrency(value);
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(0, 0, 0, 0.05)'
                                            }
                                        },
                                        x: {
                                            grid: {
                                                color: 'rgba(0, 0, 0, 0.05)'
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            showEmptyChart(chartCanvas, 'Nenhum dado disponível');
                        }

                        resolve(data);
                    } else {
                        console.error('Error loading payment methods chart data:', response);
                        showEmptyChart(chartCanvas, 'Erro ao carregar dados');
                        reject(new Error('Invalid payment methods chart response'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error loading payment methods chart:', error);
                    showEmptyChart(chartCanvas, 'Erro de conexão');
                    reject(new Error('AJAX error: ' + error));
                },
                complete: function () {
                    if (chartContainer) {
                        chartContainer.classList.remove('chart-loading');
                    }
                }
            });
        });
    }

    // Show empty chart message
    function showEmptyChart(canvas, message) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText(message, canvas.width / 2, canvas.height / 2);
    }

    // Global initialization function
    function initializeDashboard() {
        console.log('initializeDashboard() called');

        if (window.DashboardRelatorios && typeof window.DashboardRelatorios.init === 'function') {
            window.DashboardRelatorios.init();
        } else {
            console.error('DashboardRelatorios.init not available - using fallback');

            // Fallback initialization
            initChartInstances();
            initPeriodButtons();
            loadInitialData();
        }
    }

    // Make functions globally available for backward compatibility
    window.initializeDashboard = initializeDashboard;
    window.updateDashboardData = updateDashboardData;
    window.loadDailySalesChart = loadDailySalesChart;
    window.loadCategorySalesChart = loadCategorySalesChart;
    window.loadPaymentMethodsChart = loadPaymentMethodsChart;
    window.populateTopProductsList = populateTopProductsList;
    window.populateTopAssociadosList = populateTopAssociadosList;

    // Legacy function aliases for backward compatibility
    window.updateCardsOnly = (period) => updateSalesMetrics(period);
    window.updateExpensesCardsOnly = (period) => updateExpensesMetrics(period);
    window.updateTopProducts = updateTopProducts;
    window.updateTopAssociados = updateTopAssociados;
    window.showMetricSkeleton = showSalesMetricsSkeleton;
    window.showExpensesSkeleton = showExpensesMetricsSkeleton;
    window.updateAdditionalIndicators = updateAdditionalIndicators;
    window.showAdditionalIndicatorsSkeleton = showAdditionalIndicatorsSkeleton;
    window.updateAdditionalIndicatorsDisplay = updateAdditionalIndicatorsDisplay;

    console.log('Dashboard functions made globally available');

})(); // End of IIFE