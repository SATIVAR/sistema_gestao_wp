<?php
/**
 * ISOLATED REPORTS DASHBOARD FUNCTIONS
 * Completely separated from tasks system
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Reports Dashboard System
 */
function hg_init_reports_dashboard() {
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Reports dashboard system initializing...');
    }
    
    // Always register AJAX handlers (they check permissions internally)
    hg_register_reports_ajax_handlers();
    
    // Enqueue scripts only on reports pages
    add_action('wp_enqueue_scripts', 'hg_enqueue_reports_scripts');
    
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Reports dashboard system initialized');
        error_log('Functions available: hg_get_top_associados=' . (function_exists('hg_get_top_associados') ? 'YES' : 'NO'));
        error_log('WooCommerce available: ' . (function_exists('wc_get_orders') ? 'YES' : 'NO'));
    }
}
add_action('init', 'hg_init_reports_dashboard');

/**
 * Check if current page is reports dashboard
 */
function hg_is_reports_dashboard_page() {
    // Method 1: Check URL path (most reliable)
    $current_url = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($current_url, 'relatorios-dashboard') !== false) {
        return true;
    }
    
    // Method 2: Check template file
    global $template;
    if ($template) {
        $template_name = basename($template);
        if ($template_name === 'dashboard-relatorios-dashboard.php') {
            return true;
        }
    }
    
    // Method 3: Check page template
    if (function_exists('is_page_template') && is_page_template('dashboard-relatorios-dashboard.php')) {
        return true;
    }
    
    // Method 4: Check current page slug
    if (function_exists('is_page') && is_page()) {
        $post = get_queried_object();
        if ($post && isset($post->post_name) && strpos($post->post_name, 'relatorios-dashboard') !== false) {
            return true;
        }
    }
    
    // Method 5: Check if we're in admin-ajax and it's a reports request
    if (defined('DOING_AJAX') && DOING_AJAX) {
        $action = $_POST['action'] ?? '';
        $reports_actions = array(
            'get_dashboard_metrics_cards', 
            'get_daily_sales_chart', 
            'get_expenses_metrics', 
            'get_top_products',
            'get_all_products_sales',
            'get_top_associados',
            'get_category_sales_chart',
            'get_payment_methods_chart',
            'test_isolated_system'
        );
        if (in_array($action, $reports_actions)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Register all AJAX handlers for reports
 */
function hg_register_reports_ajax_handlers() {
    // Sales metrics
    add_action('wp_ajax_get_dashboard_metrics_cards', 'hg_ajax_get_dashboard_metrics_cards');
    add_action('wp_ajax_nopriv_get_dashboard_metrics_cards', 'hg_ajax_get_dashboard_metrics_cards');
    
    // Daily sales chart
    add_action('wp_ajax_get_daily_sales_chart', 'hg_ajax_get_daily_sales_chart');
    add_action('wp_ajax_nopriv_get_daily_sales_chart', 'hg_ajax_get_daily_sales_chart');
    
    // Top products
    add_action('wp_ajax_get_top_products', 'hg_ajax_get_top_products');
    add_action('wp_ajax_nopriv_get_top_products', 'hg_ajax_get_top_products');
    
    // All products sales
    add_action('wp_ajax_get_all_products_sales', 'hg_ajax_get_all_products_sales');
    add_action('wp_ajax_nopriv_get_all_products_sales', 'hg_ajax_get_all_products_sales');
    
    // Top associados
    add_action('wp_ajax_get_top_associados', 'hg_ajax_get_top_associados');
    add_action('wp_ajax_nopriv_get_top_associados', 'hg_ajax_get_top_associados');
    
    // Expenses metrics
    add_action('wp_ajax_get_expenses_metrics', 'hg_ajax_get_expenses_metrics');
    add_action('wp_ajax_nopriv_get_expenses_metrics', 'hg_ajax_get_expenses_metrics');
    
    // Expenses charts
    add_action('wp_ajax_get_expenses_charts', 'hg_ajax_get_expenses_charts');
    add_action('wp_ajax_nopriv_get_expenses_charts', 'hg_ajax_get_expenses_charts');
    
    // Category sales chart
    add_action('wp_ajax_get_category_sales_chart', 'hg_ajax_get_category_sales_chart');
    add_action('wp_ajax_nopriv_get_category_sales_chart', 'hg_ajax_get_category_sales_chart');
    
    // Payment methods chart
    add_action('wp_ajax_get_payment_methods_chart', 'hg_ajax_get_payment_methods_chart');
    add_action('wp_ajax_nopriv_get_payment_methods_chart', 'hg_ajax_get_payment_methods_chart');
    
    // Debug endpoint
    add_action('wp_ajax_test_isolated_system', 'hg_ajax_test_isolated_system');
    add_action('wp_ajax_nopriv_test_isolated_system', 'hg_ajax_test_isolated_system');
    
    // Simple test endpoint for top associados
    add_action('wp_ajax_test_top_associados_simple', 'hg_ajax_test_top_associados_simple');
    add_action('wp_ajax_nopriv_test_top_associados_simple', 'hg_ajax_test_top_associados_simple');
    
    // Diagnostic endpoint
    add_action('wp_ajax_diagnose_top_associados', 'hg_ajax_diagnose_top_associados');
    add_action('wp_ajax_nopriv_diagnose_top_associados', 'hg_ajax_diagnose_top_associados');
    
    // Vendas mês atual chart
    add_action('wp_ajax_get_monthly_sales_chart', 'hg_ajax_get_monthly_sales_chart');
    add_action('wp_ajax_nopriv_get_monthly_sales_chart', 'hg_ajax_get_monthly_sales_chart');
    // Vendas anual chart
    add_action('wp_ajax_get_annual_sales_chart', 'hg_ajax_get_annual_sales_chart');
    add_action('wp_ajax_nopriv_get_annual_sales_chart', 'hg_ajax_get_annual_sales_chart');

}

/**
 * AJAX Handler: Dashboard Metrics Cards
 */
function hg_ajax_get_dashboard_metrics_cards() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Validate period
        $allowed_periods = array('all', 'month', 'week', 'year');
        if (!in_array($period, $allowed_periods)) {
            $period = 'all';
        }
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'metrics' => array(
                    'grossSales' => 0,
                    'totalOrders' => 0,
                    'itemsSold' => 0,
                    'averageOrder' => 0,
                    'conversionRate' => 0
                ),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        // Get metrics with fallback
        $metrics = array(
            'grossSales' => function_exists('hg_get_gross_sales') ? hg_get_gross_sales($period) : 0,
            'totalOrders' => function_exists('hg_get_total_orders') ? hg_get_total_orders($period) : 0,
            'itemsSold' => function_exists('hg_get_total_items_sold') ? hg_get_total_items_sold($period) : 0,
            'averageOrder' => function_exists('hg_get_average_order_value') ? hg_get_average_order_value($period) : 0,
            'conversionRate' => function_exists('hg_get_conversion_rate') ? hg_get_conversion_rate($period) : 0,
        );
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Dashboard metrics request - Period: ' . $period);
            error_log('WooCommerce available: ' . (function_exists('wc_get_orders') ? 'YES' : 'NO'));
            error_log('Metrics calculated: ' . print_r($metrics, true));
        }
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Dashboard metrics loaded for period: ' . $period);
            error_log('Metrics data: ' . print_r($metrics, true));
        }
        
        wp_send_json_success(array('metrics' => $metrics));
        
    } catch (Exception $e) {
        error_log('Reports dashboard metrics error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading metrics: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: Daily Sales Chart
 */
function hg_ajax_get_daily_sales_chart() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'dailySales' => array(
                    'labels' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'),
                    'data' => array(0, 0, 0, 0, 0, 0, 0)
                ),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        $daily_sales = function_exists('hg_get_daily_sales') ? hg_get_daily_sales(7) : array(
            'labels' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'),
            'data' => array(0, 0, 0, 0, 0, 0, 0)
        );
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Daily sales chart data: ' . print_r($daily_sales, true));
        }
        
        wp_send_json_success(array(
            'dailySales' => $daily_sales,
            'title' => 'Vendas Diárias (Últimos 7 dias)',
            'period_independent' => true,
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
        
    } catch (Exception $e) {
        error_log('Daily sales chart error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading daily sales: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: Top Products
 */
function hg_ajax_get_top_products() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'topProducts' => array(),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        $top_products = function_exists('hg_get_top_selling_products') ? hg_get_top_selling_products(5, $period) : array();
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Top products loaded for period: ' . $period);
            error_log('Top products data: ' . print_r($top_products, true));
        }
        
        wp_send_json_success(array(
            'topProducts' => $top_products,
            'period' => $period,
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
        
    } catch (Exception $e) {
        error_log('Top products error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading top products: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: All Products Sales
 */
function hg_ajax_get_all_products_sales() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'allProducts' => array(),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        $all_products = function_exists('hg_get_all_products_sales') ? hg_get_all_products_sales($period) : array();
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('All products sales loaded for period: ' . $period);
            error_log('All products count: ' . count($all_products));
        }
        
        wp_send_json_success(array(
            'allProducts' => $all_products,
            'period' => $period,
            'total_products' => count($all_products),
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
        
    } catch (Exception $e) {
        error_log('All products sales error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading all products sales: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: Top Associados
 */
function hg_ajax_get_top_associados() {
    // Log that the handler was called
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('=== hg_ajax_get_top_associados handler called ===');
        error_log('POST data: ' . print_r($_POST, true));
    }
    
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Nonce validation failed: ' . $nonce);
            }
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Validate period
        $allowed_periods = array('all', 'month', 'week', 'year');
        if (!in_array($period, $allowed_periods)) {
            $period = 'all';
        }
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'topAssociados' => array(),
                'message' => 'WooCommerce not available',
                'period' => $period,
                'total_found' => 0,
                'generated_at' => current_time('Y-m-d H:i:s')
            ));
            return;
        }
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AJAX get_top_associados - Period: ' . $period);
            error_log('AJAX get_top_associados - Function exists: ' . (function_exists('hg_get_top_associados') ? 'YES' : 'NO'));
        }
        
        $top_associados = array();
        
        // Try main function first
        if (function_exists('hg_get_top_associados')) {
            $top_associados = hg_get_top_associados(25, $period);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Main function results: ' . count($top_associados));
            }
        }
        
        // If no data found, try fallback method
        if (empty($top_associados) && function_exists('hg_get_simple_top_customers')) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('No associados found, trying fallback method...');
            }
            $top_associados = hg_get_simple_top_customers(25, $period);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Fallback method results: ' . count($top_associados));
            }
        }
        
        // If still no data, try direct WooCommerce query
        if (empty($top_associados)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Trying direct WooCommerce query...');
            }
            $top_associados = hg_get_customers_direct_query(25, $period);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Direct query results: ' . count($top_associados));
            }
        }
        
        // Final debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Final top associados result count: ' . count($top_associados));
            if (!empty($top_associados)) {
                error_log('Sample data: ' . print_r(array_slice($top_associados, 0, 2), true));
            }
        }
        
        wp_send_json_success(array(
            'topAssociados' => $top_associados,
            'period' => $period,
            'total_found' => count($top_associados),
            'generated_at' => current_time('Y-m-d H:i:s'),
            'method_used' => !empty($top_associados) ? 'success' : 'no_data'
        ));
        
    } catch (Exception $e) {
        error_log('Top associados error: ' . $e->getMessage());
        error_log('Top associados error trace: ' . $e->getTraceAsString());
        
        wp_send_json_error(array(
            'message' => 'Error loading top associados: ' . $e->getMessage(),
            'period' => $period ?? 'all',
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
    }
}

/**
 * AJAX Handler: Expenses Metrics
 */
function hg_ajax_get_expenses_metrics() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Validate period
        $allowed_periods = array('all', 'month', 'week', 'year');
        if (!in_array($period, $allowed_periods)) {
            $period = 'all';
        }
        
        // Get metrics with fallback
        $metrics = array(
            'totalExpenses' => function_exists('hg_get_total_expenses') ? hg_get_total_expenses($period) : 0,
            'expensesCount' => function_exists('hg_get_expenses_count') ? hg_get_expenses_count($period) : 0,
            'averageExpense' => function_exists('hg_get_average_expense') ? hg_get_average_expense($period) : 0
        );
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Expenses metrics request - Period: ' . $period);
            error_log('Post type saidas exists: ' . (post_type_exists('saidas') ? 'YES' : 'NO'));
            error_log('Expenses metrics calculated: ' . print_r($metrics, true));
        }
        
        wp_send_json_success($metrics);
        
    } catch (Exception $e) {
        error_log('Expenses metrics error: ' . $e->getMessage());
        wp_send_json_success(array(
            'totalExpenses' => 0,
            'expensesCount' => 0,
            'averageExpense' => 0,
            'error' => 'Data temporarily unavailable'
        ));
    }
}

/**
 * AJAX Handler: Expenses Charts
 */
function hg_ajax_get_expenses_charts() {
    try {
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Check if expenses functions exist
        if (!function_exists('hg_get_expenses_by_category')) {
            wp_send_json_success(array(
                'expensesByCategory' => array('labels' => array(), 'data' => array()),
                'expensesTimeline' => array('labels' => array(), 'data' => array()),
                'message' => 'Expenses functions not available'
            ));
            return;
        }
        
        $charts_data = array(
            'expensesByCategory' => function_exists('hg_get_expenses_by_category') ? hg_get_expenses_by_category($period) : array('labels' => array(), 'data' => array()),
            'expensesTimeline' => function_exists('hg_get_expenses_timeline') ? hg_get_expenses_timeline($period) : array('labels' => array(), 'data' => array())
        );
        
        wp_send_json_success($charts_data);
        
    } catch (Exception $e) {
        error_log('Expenses charts error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading expenses charts: ' . $e->getMessage()));
    }
}

/**
 * Enqueue scripts for reports dashboard
 */
function hg_enqueue_reports_scripts() {
    $is_reports_page = hg_is_reports_dashboard_page();
    
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Reports script enqueue check: ' . ($is_reports_page ? 'YES' : 'NO'));
        global $template;
        error_log('Current template: ' . ($template ? basename($template) : 'none'));
        error_log('Current URL: ' . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
    }
    
    // Force load on any page that might be reports dashboard
    $current_url = $_SERVER['REQUEST_URI'] ?? '';
    $force_load = (strpos($current_url, 'relatorios') !== false || 
                   strpos($current_url, 'dashboard') !== false ||
                   $is_reports_page);
    
    if (!$force_load) {
        return;
    }
    
    // Chart.js
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.0', true);
    
    // Main.js if exists
    $main_js_path = get_template_directory() . '/assets/js/main.js';
    $dashboard_deps = array('jquery', 'chart-js');
    
    if (file_exists($main_js_path)) {
        wp_enqueue_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.1', true);
        $dashboard_deps[] = 'mainjs';
    }
    
    // Enqueue a biblioteca principal do dashboard (dashboard-data.js)
    wp_enqueue_script(
        'dashboard-reports-data', 
        get_template_directory_uri() . '/assets/js/dashboard-data.js', 
        $dashboard_deps, 
        filemtime(get_template_directory() . '/assets/js/dashboard-data.js'), 
        true
    );

    // Localize a biblioteca principal com os dados necessários
    wp_localize_script('dashboard-reports-data', 'dashboardRelatoriosAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dashboard_ajax_nonce'),
        'site_url' => get_site_url(),
        'template_directory' => get_template_directory_uri(),
        'is_user_logged_in' => is_user_logged_in(),
        'current_user' => wp_get_current_user()->ID,
        'debug' => defined('WP_DEBUG') && WP_DEBUG,
        'isolated_system' => true,
        'template_detected' => $is_reports_page,
        'loaded_by' => 'isolated_system'
    ));

    // Also add inline script to ensure dashboardRelatoriosAjax is available immediately
    wp_add_inline_script('dashboard-reports-data', '
        if (typeof dashboardRelatoriosAjax === "undefined") {
            console.warn("dashboardRelatoriosAjax not found, creating fallback");
            window.dashboardRelatoriosAjax = {
                ajaxurl: "' . admin_url('admin-ajax.php') . '",
                nonce: "' . wp_create_nonce('dashboard_ajax_nonce') . '",
                site_url: "' . get_site_url() . '",
                template_directory: "' . get_template_directory_uri() . '",
                is_user_logged_in: ' . (is_user_logged_in() ? 'true' : 'false') . ',
                current_user: ' . wp_get_current_user()->ID . ',
                debug: ' . (defined('WP_DEBUG') && WP_DEBUG ? 'true' : 'false') . ',
                isolated_system: true,
                fallback: true
            };
        }
        console.log("✓ dashboardRelatoriosAjax configured:", dashboardRelatoriosAjax);
    ', 'before');

    // Enqueue o script de inicialização específico para o dashboard de relatórios financeiros
    wp_enqueue_script(
        'dashboard-relatorios-financeiro-js',
        get_template_directory_uri() . '/assets/js/dashboard-relatorios-financeiro.js',
        array('dashboard-reports-data'), // Depende da biblioteca principal
        filemtime(get_template_directory() . '/assets/js/dashboard-relatorios-financeiro.js'),
        true
    );

    // Adiciona um script inline para confirmar o carregamento de ambos os scripts
    wp_add_inline_script('dashboard-reports-data', 'console.log("✓ Biblioteca principal do dashboard (dashboard-data.js) carregada.");', 'after');
    wp_add_inline_script('dashboard-relatorios-financeiro-js', 'console.log("✓ Script de inicialização do dashboard de relatórios (dashboard-relatorios-financeiro.js) carregado.");', 'after');
    
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Reports dashboard scripts enqueued successfully');
        error_log('Script handle: dashboard-reports-data');
        error_log('Script URL: ' . get_template_directory_uri() . '/assets/js/dashboard-data.js');
    }
    
    // Also add inline script to confirm loading
    wp_add_inline_script('dashboard-reports-data', 'console.log("✓ Dashboard reports script loaded by isolated system");', 'before');
}

/**
 * AJAX Handler: Category Sales Chart
 */
function hg_ajax_get_category_sales_chart() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'categorySales' => array(
                    'labels' => array(),
                    'data' => array()
                ),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        $category_sales = function_exists('hg_get_sales_by_category') ? hg_get_sales_by_category($period) : array(
            'labels' => array(),
            'data' => array()
        );
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Category sales chart data: ' . print_r($category_sales, true));
        }
        
        wp_send_json_success(array(
            'categorySales' => $category_sales,
            'period' => $period,
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
        
    } catch (Exception $e) {
        error_log('Category sales chart error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading category sales: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: Payment Methods Chart
 */
function hg_ajax_get_payment_methods_chart() {
    try {
        // Validate nonce - more flexible approach
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }
        
        $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'all';
        
        // Check if WooCommerce is available
        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'paymentMethods' => array(
                    'labels' => array(),
                    'data' => array()
                ),
                'message' => 'WooCommerce not available'
            ));
            return;
        }
        
        $payment_methods = function_exists('hg_get_sales_by_payment_method') ? hg_get_sales_by_payment_method($period) : array(
            'labels' => array(),
            'data' => array()
        );
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Payment methods chart data: ' . print_r($payment_methods, true));
        }
        
        wp_send_json_success(array(
            'paymentMethods' => $payment_methods,
            'period' => $period,
            'generated_at' => current_time('Y-m-d H:i:s')
        ));
        
    } catch (Exception $e) {
        error_log('Payment methods chart error: ' . $e->getMessage());
        wp_send_json_error(array('message' => 'Error loading payment methods: ' . $e->getMessage()));
    }
}

/**
 * AJAX Handler: Test Isolated System
 */
function hg_ajax_test_isolated_system() {
    // Get basic WooCommerce data for testing
    $wc_orders_count = 0;
    $wc_completed_orders_count = 0;
    $sample_orders = array();
    
    if (function_exists('wc_get_orders')) {
        // Count all orders
        $all_orders = wc_get_orders(array('limit' => -1, 'return' => 'ids'));
        $wc_orders_count = count($all_orders);
        
        // Count completed orders
        $completed_orders = wc_get_orders(array('status' => 'completed', 'limit' => -1, 'return' => 'ids'));
        $wc_completed_orders_count = count($completed_orders);
        
        // Get sample of recent orders
        $recent_orders = wc_get_orders(array('limit' => 3, 'return' => 'objects'));
        foreach ($recent_orders as $order) {
            // Skip if this is not a valid WC_Order object or is a refund
            if (!method_exists($order, 'get_customer_id') || 
                !method_exists($order, 'get_total') ||
                strpos(get_class($order), 'Refund') !== false) {
                continue;
            }
            
            $sample_orders[] = array(
                'id' => $order->get_id(),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'customer_id' => $order->get_customer_id(),
                'customer_name' => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
                'date' => $order->get_date_created()->format('Y-m-d H:i:s')
            );
        }
    }
    
    // Test system functionality
    wp_send_json_success(array(
        'message' => 'Isolated reports system is working!',
        'timestamp' => current_time('Y-m-d H:i:s'),
        'system' => 'reports-isolated',
        'woocommerce_active' => function_exists('wc_get_orders'),
        'template_check' => hg_is_reports_dashboard_page(),
        'user_logged_in' => is_user_logged_in(),
        'current_user_id' => get_current_user_id(),
        'ajax_url' => admin_url('admin-ajax.php'),
        'woocommerce_data' => array(
            'total_orders' => $wc_orders_count,
            'completed_orders' => $wc_completed_orders_count,
            'sample_orders' => $sample_orders
        ),
        'functions_available' => array(
            'hg_get_gross_sales' => function_exists('hg_get_gross_sales'),
            'hg_get_total_orders' => function_exists('hg_get_total_orders'),
            'hg_get_total_expenses' => function_exists('hg_get_total_expenses'),
            'hg_get_expenses_count' => function_exists('hg_get_expenses_count'),
            'hg_get_average_expense' => function_exists('hg_get_average_expense'),
            'hg_get_daily_sales' => function_exists('hg_get_daily_sales'),
            'hg_get_top_selling_products' => function_exists('hg_get_top_selling_products'),
            'hg_get_top_associados' => function_exists('hg_get_top_associados'),
            'hg_get_simple_top_customers' => function_exists('hg_get_simple_top_customers'),
            'hg_get_customers_direct_query' => function_exists('hg_get_customers_direct_query')
        ),
        'handlers_registered' => array(
            'get_dashboard_metrics_cards' => has_action('wp_ajax_get_dashboard_metrics_cards'),
            'get_daily_sales_chart' => has_action('wp_ajax_get_daily_sales_chart'),
            'get_expenses_metrics' => has_action('wp_ajax_get_expenses_metrics'),
            'get_top_products' => has_action('wp_ajax_get_top_products'),
            'get_all_products_sales' => has_action('wp_ajax_get_all_products_sales'),
            'get_top_associados' => has_action('wp_ajax_get_top_associados')
        ),
        'sample_data' => array(
            'gross_sales' => function_exists('hg_get_gross_sales') ? hg_get_gross_sales('all') : 'N/A',
            'total_orders' => function_exists('hg_get_total_orders') ? hg_get_total_orders('all') : 'N/A',
            'total_expenses' => function_exists('hg_get_total_expenses') ? hg_get_total_expenses('all') : 'N/A',
            'top_associados_count' => function_exists('hg_get_top_associados') ? count(hg_get_top_associados(5, 'all')) : 'N/A'
        )
    ));
}

/**
 * Simple test handler for top associados debugging
 */
function hg_ajax_test_top_associados_simple() {
    // Basic test without complex logic
    wp_send_json_success(array(
        'message' => 'Simple test working',
        'timestamp' => current_time('Y-m-d H:i:s'),
        'woocommerce_available' => function_exists('wc_get_orders'),
        'test_data' => array(
            array(
                'position' => 1,
                'name' => 'Cliente Teste 1',
                'total' => 1500.00,
                'orders' => 5,
                'average' => 300.00,
                'customer_id' => 1
            ),
            array(
                'position' => 2,
                'name' => 'Cliente Teste 2',
                'total' => 1200.00,
                'orders' => 3,
                'average' => 400.00,
                'customer_id' => 2
            )
        )
    ));
}

/**
 * Diagnostic handler for top associados
 */
function hg_ajax_diagnose_top_associados() {
    $diagnostics = array(
        'handler_called' => true,
        'timestamp' => current_time('Y-m-d H:i:s'),
        'wordpress_functions' => array(
            'wp_send_json_success' => function_exists('wp_send_json_success'),
            'wp_send_json_error' => function_exists('wp_send_json_error'),
            'current_time' => function_exists('current_time'),
            'wp_verify_nonce' => function_exists('wp_verify_nonce')
        ),
        'woocommerce_functions' => array(
            'wc_get_orders' => function_exists('wc_get_orders'),
            'wc_get_order' => function_exists('wc_get_order'),
            'WC_Customer' => class_exists('WC_Customer')
        ),
        'custom_functions' => array(
            'hg_get_top_associados' => function_exists('hg_get_top_associados'),
            'hg_get_simple_top_customers' => function_exists('hg_get_simple_top_customers'),
            'hg_get_customers_direct_query' => function_exists('hg_get_customers_direct_query')
        ),
        'post_data' => $_POST,
        'user_info' => array(
            'logged_in' => is_user_logged_in(),
            'user_id' => get_current_user_id(),
            'can_manage_options' => current_user_can('manage_options')
        )
    );
    
    // Try to get a simple count of orders
    if (function_exists('wc_get_orders')) {
        try {
            $orders = wc_get_orders(array('limit' => 5, 'return' => 'ids'));
            $diagnostics['sample_orders'] = array(
                'count' => count($orders),
                'ids' => $orders
            );
        } catch (Exception $e) {
            $diagnostics['sample_orders'] = array(
                'error' => $e->getMessage()
            );
        }
    }
    
    wp_send_json_success($diagnostics);
}

// Fallback data functions if main functions aren't available
if (!function_exists('hg_get_gross_sales')) {
    function hg_get_gross_sales($period = 'all') {
        if (!function_exists('wc_get_orders')) return 0;
        
        $args = array('status' => array('completed'), 'limit' => -1, 'return' => 'ids');
                $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        $total = 0;
        foreach ($orders as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) $total += $order->get_total();
        }
        return $total;
    }
}

if (!function_exists('hg_get_total_orders')) {
    function hg_get_total_orders($period = 'all') {
        if (!function_exists('wc_get_orders')) return 0;
        
        $args = array('status' => array('completed'), 'limit' => -1, 'return' => 'ids');
                $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        return count(wc_get_orders($args));
    }
}

if (!function_exists('hg_get_total_items_sold')) {
    function hg_get_total_items_sold($period = 'all') {
        if (!function_exists('wc_get_orders')) return 0;
        
        $args = array('status' => array('completed'), 'limit' => -1);
                $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        $total = 0;
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $total += $item->get_quantity();
            }
        }
        return $total;
    }
}

if (!function_exists('hg_get_average_order_value')) {
    function hg_get_average_order_value($period = 'all') {
        $total_sales = hg_get_gross_sales($period);
        $total_orders = hg_get_total_orders($period);
        return $total_orders > 0 ? $total_sales / $total_orders : 0;
    }
}

if (!function_exists('hg_get_conversion_rate')) {
    function hg_get_conversion_rate($period = 'all') {
        $total_orders = hg_get_total_orders($period);
        $total_products = wp_count_posts('product')->publish ?? 1;
        return round(($total_orders / $total_products) * 100, 2);
    }
}

if (!function_exists('hg_get_daily_sales')) {
    function hg_get_daily_sales($days = 7) {
        if (!function_exists('wc_get_orders')) {
            return array(
                'labels' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'),
                'data' => array(0, 0, 0, 0, 0, 0, 0)
            );
        }
        
        $sales_by_date = array();
        $dates = array();
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            $sales_by_date[$date] = 0;
        }
        
        $start_date = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
        $end_date = date('Y-m-d');
        
        $args = array(
            'status' => array('completed'),
            'limit' => -1,
            'date_created' => $start_date . '...' . $end_date,
            'return' => 'objects'
        );
        
        $orders = wc_get_orders($args);
        
        foreach ($orders as $order) {
            $order_date = $order->get_date_created()->format('Y-m-d');
            if (isset($sales_by_date[$order_date])) {
                $sales_by_date[$order_date] += (float) $order->get_total();
            }
        }
        
        $labels = array_map(function($date) { 
            return date_i18n('D', strtotime($date)); 
        }, $dates);
        
        return array(
            'labels' => $labels,
            'data' => array_values($sales_by_date)
        );
    }
}

if (!function_exists('hg_get_top_selling_products')) {
    function hg_get_top_selling_products($limit = 5, $period = 'all') {
        if (!function_exists('wc_get_orders')) return array();
        
        $product_sales = array();
        $args = array('status' => array('completed'), 'limit' => -1);
        
                $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $quantity = $item->get_quantity();
                $product = wc_get_product($product_id);
                if ($product) {
                    $product_name = $product->get_name();
                    if (!isset($product_sales[$product_name])) {
                        $product_sales[$product_name] = 0;
                    }
                    $product_sales[$product_name] += $quantity;
                }
            }
        }
        
        arsort($product_sales);
        $top_products = array_slice($product_sales, 0, $limit, true);
        
        $result = [];
        foreach ($top_products as $name => $quantity) {
            $result[] = ['name' => $name, 'quantity' => $quantity];
        }
        return $result;
    }
}

if (!function_exists('hg_get_all_products_sales')) {
    function hg_get_all_products_sales($period = 'all') {
        if (!function_exists('wc_get_orders')) return array();
        
        // First, get all WooCommerce products
        $all_products = array();
        $products_query = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        if ($products_query->have_posts()) {
            foreach ($products_query->posts as $product_id) {
                $product = wc_get_product($product_id);
                if ($product && $product->is_type('simple')) {
                    $all_products[$product_id] = array(
                        'id' => $product_id,
                        'name' => $product->get_name(),
                        'sku' => $product->get_sku(),
                        'price' => $product->get_price(),
                        'quantity_sold' => 0,
                        'total_revenue' => 0,
                        'stock_quantity' => $product->get_stock_quantity(),
                        'status' => $product->get_stock_status()
                    );
                }
            }
        }
        wp_reset_postdata();
        
        // Now get sales data for the period
        $args = array('status' => array('completed'), 'limit' => -1);
        
        $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $quantity = $item->get_quantity();
                $total = $item->get_total();
                
                if (isset($all_products[$product_id])) {
                    $all_products[$product_id]['quantity_sold'] += $quantity;
                    $all_products[$product_id]['total_revenue'] += $total;
                }
            }
        }
        
        // Sort by quantity sold (descending)
        uasort($all_products, function($a, $b) {
            return $b['quantity_sold'] <=> $a['quantity_sold'];
        });
        
        // Convert to indexed array and add position
        $result = array();
        $position = 1;
        foreach ($all_products as $product) {
            $result[] = array(
                'position' => $position,
                'id' => $product['id'],
                'name' => $product['name'],
                'sku' => $product['sku'] ?: 'N/A',
                'price' => (float) $product['price'],
                'quantity_sold' => (int) $product['quantity_sold'],
                'total_revenue' => (float) $product['total_revenue'],
                'stock_quantity' => $product['stock_quantity'] !== null ? (int) $product['stock_quantity'] : null,
                'stock_status' => $product['status'],
                'average_price' => $product['quantity_sold'] > 0 ? $product['total_revenue'] / $product['quantity_sold'] : 0
            );
            $position++;
        }
        
        return $result;
    }
}

if (!function_exists('hg_get_top_associados')) {
    function hg_get_top_associados($limit = 25, $period = 'all') {
        if (!function_exists('wc_get_orders')) return array();
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('hg_get_top_associados called with limit: ' . $limit . ', period: ' . $period);
        }
        
        $customer_sales = array();
        $args = array(
            'status' => array('wc-completed', 'completed'),
            'limit' => -1,
            'return' => 'objects'
        );
        
        $end = date('Y-m-d 23:59:59');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01 00:00:00');
                break;
            case 'month':
                $start = date('Y-m-01 00:00:00');
                break;
            case 'week':
                $start = date('Y-m-d 00:00:00', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Found ' . count($orders) . ' orders for period: ' . $period);
        }
        
        foreach ($orders as $order) {
            if (!is_object($order)) continue;
            
            // Skip if this is not a valid WC_Order object or is a refund
            if (!method_exists($order, 'get_customer_id') || 
                !method_exists($order, 'get_total') ||
                strpos(get_class($order), 'Refund') !== false) {
                continue;
            }
            
            $customer_id = $order->get_customer_id();
            $order_total = (float) $order->get_total();
            $customer_key = '';
            $customer_name = '';
            
            // Skip orders with zero total
            if ($order_total <= 0) continue;
            
            if ($customer_id > 0) {
                // Registered customer
                $customer_key = 'customer_' . $customer_id;
                
                // Try to get customer name from user data first
                $user = get_user_by('ID', $customer_id);
                if ($user) {
                    $first_name = get_user_meta($customer_id, 'first_name', true);
                    $last_name = get_user_meta($customer_id, 'last_name', true);
                    $customer_name = trim($first_name . ' ' . $last_name);
                    
                    // If no name from user meta, try display name
                    if (empty($customer_name) || $customer_name === ' ') {
                        $customer_name = $user->display_name;
                    }
                    
                    // If still no name, use email
                    if (empty($customer_name) || $customer_name === ' ') {
                        $customer_name = $user->user_email;
                    }
                }
                
                // Fallback to order billing data
                if (empty($customer_name) || $customer_name === ' ') {
                    $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
                }
                
                // Final fallback
                if (empty($customer_name) || $customer_name === ' ') {
                    $customer_name = 'Cliente #' . $customer_id;
                }
                
            } else {
                // Guest order - use billing information
                $billing_first = $order->get_billing_first_name();
                $billing_last = $order->get_billing_last_name();
                $billing_email = $order->get_billing_email();
                
                $customer_name = trim($billing_first . ' ' . $billing_last);
                
                if (empty($customer_name) || $customer_name === ' ') {
                    $customer_name = $billing_email ?: 'Cliente Convidado';
                }
                
                // Use email as key for guest orders to group them properly
                $customer_key = 'guest_' . ($billing_email ?: 'order_' . $order->get_id());
            }
            
            // Initialize customer data if not exists
            if (!isset($customer_sales[$customer_key])) {
                $customer_sales[$customer_key] = array(
                    'name' => $customer_name,
                    'total' => 0,
                    'orders' => 0,
                    'customer_id' => $customer_id,
                    'is_guest' => $customer_id == 0
                );
            }
            
            // Add order data
            $customer_sales[$customer_key]['total'] += $order_total;
            $customer_sales[$customer_key]['orders']++;
        }
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Processed ' . count($customer_sales) . ' unique customers');
        }
        
        // Remove customers with zero total (safety check)
        $customer_sales = array_filter($customer_sales, function($customer) {
            return $customer['total'] > 0;
        });
        
        // Sort by total sales (descending)
        uasort($customer_sales, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        // Limit results
        $top_customers = array_slice($customer_sales, 0, $limit, true);
        
        $result = [];
        $position = 1;
        foreach ($top_customers as $customer) {
            $result[] = array(
                'position' => $position,
                'name' => $customer['name'],
                'total' => (float) $customer['total'],
                'orders' => (int) $customer['orders'],
                'average' => $customer['orders'] > 0 ? (float) ($customer['total'] / $customer['orders']) : 0,
                'customer_id' => (int) $customer['customer_id'],
                'is_guest' => $customer['is_guest']
            );
            $position++;
        }
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Returning ' . count($result) . ' top customers');
            if (!empty($result)) {
                error_log('Top customer: ' . $result[0]['name'] . ' - R$ ' . $result[0]['total']);
            }
        }
        
        return $result;
    }
}

// Simple fallback function for top customers
if (!function_exists('hg_get_simple_top_customers')) {
    function hg_get_simple_top_customers($limit = 25, $period = 'all') {
        if (!function_exists('wc_get_orders')) return array();
        
        global $wpdb;
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('hg_get_simple_top_customers called as fallback');
        }
        
        // Build date condition
        $date_condition = '';
        $end = date('Y-m-d 23:59:59');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01 00:00:00');
                break;
            case 'month':
                $start = date('Y-m-01 00:00:00');
                break;
            case 'week':
                $start = date('Y-m-d 00:00:00', strtotime('-6 days'));
                break;
            default:
                $start = null;
                break;
        }
        
        if ($start) {
            $date_condition = $wpdb->prepare(" AND p.post_date BETWEEN %s AND %s", $start, $end);
        }
        
        // Improved query to get top customers by order total
        $query = "
            SELECT 
                COALESCE(pm_customer.meta_value, 0) as customer_id,
                CASE 
                    WHEN pm_customer.meta_value > 0 THEN 
                        CASE 
                            WHEN TRIM(CONCAT(COALESCE(um_first.meta_value, ''), ' ', COALESCE(um_last.meta_value, ''))) != '' 
                            THEN TRIM(CONCAT(COALESCE(um_first.meta_value, ''), ' ', COALESCE(um_last.meta_value, '')))
                            WHEN TRIM(CONCAT(COALESCE(pm_billing_first.meta_value, ''), ' ', COALESCE(pm_billing_last.meta_value, ''))) != ''
                            THEN TRIM(CONCAT(COALESCE(pm_billing_first.meta_value, ''), ' ', COALESCE(pm_billing_last.meta_value, '')))
                            WHEN u.user_email IS NOT NULL
                            THEN u.user_email
                            ELSE CONCAT('Cliente #', pm_customer.meta_value)
                        END
                    ELSE 
                        CASE 
                            WHEN TRIM(CONCAT(COALESCE(pm_billing_first.meta_value, ''), ' ', COALESCE(pm_billing_last.meta_value, ''))) != ''
                            THEN TRIM(CONCAT(COALESCE(pm_billing_first.meta_value, ''), ' ', COALESCE(pm_billing_last.meta_value, '')))
                            WHEN pm_billing_email.meta_value IS NOT NULL
                            THEN pm_billing_email.meta_value
                            ELSE 'Cliente Convidado'
                        END
                END as customer_name,
                COALESCE(pm_billing_email.meta_value, u.user_email, '') as customer_email,
                SUM(CAST(COALESCE(pm_total.meta_value, 0) AS DECIMAL(10,2))) as total_spent,
                COUNT(p.ID) as order_count,
                CASE WHEN pm_customer.meta_value > 0 THEN 0 ELSE 1 END as is_guest
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total'
            LEFT JOIN {$wpdb->postmeta} pm_customer ON p.ID = pm_customer.post_id AND pm_customer.meta_key = '_customer_user'
            LEFT JOIN {$wpdb->postmeta} pm_billing_first ON p.ID = pm_billing_first.post_id AND pm_billing_first.meta_key = '_billing_first_name'
            LEFT JOIN {$wpdb->postmeta} pm_billing_last ON p.ID = pm_billing_last.post_id AND pm_billing_last.meta_key = '_billing_last_name'
            LEFT JOIN {$wpdb->postmeta} pm_billing_email ON p.ID = pm_billing_email.post_id AND pm_billing_email.meta_key = '_billing_email'
            LEFT JOIN {$wpdb->users} u ON pm_customer.meta_value = u.ID
            LEFT JOIN {$wpdb->usermeta} um_first ON pm_customer.meta_value = um_first.user_id AND um_first.meta_key = 'first_name'
            LEFT JOIN {$wpdb->usermeta} um_last ON pm_customer.meta_value = um_last.user_id AND um_last.meta_key = 'last_name'
            WHERE p.post_type = 'shop_order' 
            AND (p.post_status = 'wc-completed' OR p.post_status = 'completed')
            AND CAST(COALESCE(pm_total.meta_value, 0) AS DECIMAL(10,2)) > 0
            {$date_condition}
            GROUP BY 
                customer_id, 
                customer_name, 
                customer_email,
                is_guest
            HAVING total_spent > 0
            ORDER BY total_spent DESC
            LIMIT %d
        ";
        
        $results = $wpdb->get_results($wpdb->prepare($query, $limit));
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('hg_get_simple_top_customers - SQL Results: ' . count($results));
            if ($wpdb->last_error) {
                error_log('hg_get_simple_top_customers - SQL Error: ' . $wpdb->last_error);
                error_log('hg_get_simple_top_customers - Query: ' . $wpdb->last_query);
            }
        }
        
        $customers = array();
        $position = 1;
        foreach ($results as $row) {
            $name = trim($row->customer_name);
            
            // Additional name cleanup
            if (empty($name) || $name === ' ' || $name === '') {
                if (!empty($row->customer_email)) {
                    $name = $row->customer_email;
                } else if ($row->customer_id > 0) {
                    $name = 'Cliente #' . $row->customer_id;
                } else {
                    $name = 'Cliente Convidado';
                }
            }
            
            $customers[] = array(
                'position' => $position,
                'name' => $name,
                'total' => (float) $row->total_spent,
                'orders' => (int) $row->order_count,
                'average' => $row->order_count > 0 ? (float) $row->total_spent / (int) $row->order_count : 0,
                'customer_id' => (int) $row->customer_id,
                'is_guest' => (bool) $row->is_guest
            );
            $position++;
        }
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('hg_get_simple_top_customers - Returning ' . count($customers) . ' customers');
        }
        
        return $customers;
    }
}

// Direct WooCommerce query as last resort
if (!function_exists('hg_get_customers_direct_query')) {
    function hg_get_customers_direct_query($limit = 25, $period = 'all') {
        if (!function_exists('wc_get_orders')) return array();
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('hg_get_customers_direct_query called as last resort');
        }
        
        // Simple approach - get all completed orders and group by customer
        $args = array(
            'status' => 'completed',
            'limit' => -1,
            'return' => 'objects'
        );
        
        // Add date filter if needed
        $end = date('Y-m-d 23:59:59');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01 00:00:00');
                break;
            case 'month':
                $start = date('Y-m-01 00:00:00');
                break;
            case 'week':
                $start = date('Y-m-d 00:00:00', strtotime('-6 days'));
                break;
            default:
                $start = null;
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        try {
            $orders = wc_get_orders($args);
            $customers = array();
            
            foreach ($orders as $order) {
                // Skip if this is not a valid WC_Order object or is a refund
                if (!method_exists($order, 'get_customer_id') || 
                    !method_exists($order, 'get_total') ||
                    strpos(get_class($order), 'Refund') !== false) {
                    continue;
                }
                
                $customer_id = $order->get_customer_id();
                $total = (float) $order->get_total();
                
                if ($total <= 0) continue;
                
                // Create customer key
                if ($customer_id > 0) {
                    $key = 'customer_' . $customer_id;
                    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    $name = trim($name);
                    if (empty($name)) {
                        $name = 'Cliente #' . $customer_id;
                    }
                } else {
                    $email = $order->get_billing_email();
                    $key = 'guest_' . ($email ?: $order->get_id());
                    $name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
                    if (empty($name)) {
                        $name = $email ?: 'Cliente Convidado';
                    }
                }
                
                if (!isset($customers[$key])) {
                    $customers[$key] = array(
                        'name' => $name,
                        'total' => 0,
                        'orders' => 0,
                        'customer_id' => $customer_id
                    );
                }
                
                $customers[$key]['total'] += $total;
                $customers[$key]['orders']++;
            }
            
            // Sort by total
            uasort($customers, function($a, $b) {
                return $b['total'] <=> $a['total'];
            });
            
            // Format result
            $result = array();
            $position = 1;
            $count = 0;
            
            foreach ($customers as $customer) {
                if ($count >= $limit) break;
                
                $result[] = array(
                    'position' => $position,
                    'name' => $customer['name'],
                    'total' => (float) $customer['total'],
                    'orders' => (int) $customer['orders'],
                    'average' => $customer['orders'] > 0 ? (float) ($customer['total'] / $customer['orders']) : 0,
                    'customer_id' => (int) $customer['customer_id']
                );
                
                $position++;
                $count++;
            }
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Direct query found ' . count($result) . ' customers');
            }
            
            return $result;
            
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Direct query failed: ' . $e->getMessage());
            }
            return array();
        }
    }
}

// Simple fallback for expenses functions
if (!function_exists('hg_get_total_expenses')) {
    function hg_get_total_expenses($period = 'all') { 
        $args = array(
            'post_type' => 'saidas',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'saida_total',
                    'compare' => 'EXISTS'
                )
            )
        );

        $date_query = array();
        $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $date_query[] = array(
                'after'     => $start . ' 00:00:00',
                'before'    => $end . ' 23:59:59',
                'inclusive' => true,
            );
            $args['date_query'] = $date_query;
        }

        $query = new WP_Query($args);
        $total = 0;
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $total += (float) get_post_meta(get_the_ID(), 'saida_total', true);
            }
        }
        wp_reset_postdata();
        return $total;
    }
}
if (!function_exists('hg_get_expenses_count')) {
    function hg_get_expenses_count($period = 'all') { 
        $args = array(
            'post_type' => 'saidas',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $date_query = array();
        $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $date_query[] = array(
                'after'     => $start . ' 00:00:00',
                'before'    => $end . ' 23:59:59',
                'inclusive' => true,
            );
            $args['date_query'] = $date_query;
        }

        $query = new WP_Query($args);
        return $query->post_count;
    }
}
if (!function_exists('hg_get_average_expense')) {
    function hg_get_average_expense($period = 'all') { 
        $total_expenses = hg_get_total_expenses($period);
        $expenses_count = hg_get_expenses_count($period);
        return $expenses_count > 0 ? $total_expenses / $expenses_count : 0;
    }
}

// Chart data functions
if (!function_exists('hg_get_sales_by_category')) {
    function hg_get_sales_by_category($period = 'all') {
        if (!function_exists('wc_get_orders')) {
            return array('labels' => array(), 'data' => array());
        }
        
        $args = array('status' => array('completed'), 'limit' => -1);
        
        $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_created'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        $category_sales = array();
        
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                
                if ($product) {
                    $categories = wp_get_post_terms($product_id, 'product_cat');
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $category_name = $category->name;
                            if (!isset($category_sales[$category_name])) {
                                $category_sales[$category_name] = 0;
                            }
                            $category_sales[$category_name] += (float) $item->get_total();
                        }
                    } else {
                        // Produto sem categoria
                        if (!isset($category_sales['Sem Categoria'])) {
                            $category_sales['Sem Categoria'] = 0;
                        }
                        $category_sales['Sem Categoria'] += (float) $item->get_total();
                    }
                }
            }
        }
        
        // Ordenar por valor decrescente
        arsort($category_sales);
        
        // Limitar a 10 categorias principais
        $category_sales = array_slice($category_sales, 0, 10, true);
        
        return array(
            'labels' => array_keys($category_sales),
            'data' => array_values($category_sales)
        );
    }
}

if (!function_exists('hg_get_sales_by_payment_method')) {
    function hg_get_sales_by_payment_method($period = 'all') {
        if (!function_exists('wc_get_orders')) {
            return array('labels' => array(), 'data' => array());
        }
        
        $args = array('status' => array('completed'), 'limit' => -1);
        
        $end = date('Y-m-d');
        switch ($period) {
            case 'year':
                $start = date('Y-01-01');
                break;
            case 'month':
                $start = date('Y-m-01');
                break;
            case 'week':
                $start = date('Y-m-d', strtotime('-6 days'));
                break;
            default:
                $start = null; // all time
                break;
        }

        if ($start) {
            $args['date_paid'] = $start . '...' . $end;
        }
        
        $orders = wc_get_orders($args);
        $payment_methods = array();
        
        foreach ($orders as $order) {
            $payment_method = $order->get_payment_method_title();
            if (empty($payment_method)) {
                $payment_method = $order->get_payment_method();
            }
            if (empty($payment_method)) {
                $payment_method = 'Não informado';
            }
            
            if (!isset($payment_methods[$payment_method])) {
                $payment_methods[$payment_method] = 0;
            }
            $payment_methods[$payment_method] += (float) $order->get_total();
        }
        
        // Ordenar por valor decrescente
        arsort($payment_methods);
        
        return array(
            'labels' => array_keys($payment_methods),
            'data' => array_values($payment_methods)
        );
    }
}

// Função: Vendas do mês atual
if (!function_exists('hg_get_monthly_sales')) {
    /**
     * Retorna o total de vendas (valor bruto) do mês atual
     */
    function hg_get_monthly_sales() {
        if (!function_exists('wc_get_orders')) return 0;
        $year = date('Y');
        $month = date('m');
        $start = $year . '-' . $month . '-01';
        $end = date('Y-m-t');
        $args = array(
            'status' => array('completed'),
            'limit' => -1,
            'date_paid' => $start . '...' . $end,
        );
        $orders = wc_get_orders($args);
        $total = 0;
        foreach ($orders as $order) {
            $total += $order->get_total();
        }
        return $total;
    }
}

// Função: Vendas anual
if (!function_exists('hg_get_annual_sales')) {
    /**
     * Retorna o total de vendas (valor bruto) do ano atual
     */
    function hg_get_annual_sales() {
        if (!function_exists('wc_get_orders')) return 0;
        $year = date('Y');
        $start = $year . '-01-01';
        $end = $year . '-12-31';
        $args = array(
            'status' => array('completed'),
            'limit' => -1,
            'date_paid' => $start . '...' . $end,
        );
        $orders = wc_get_orders($args);
        $total =  0;
        foreach ($orders as $order) {
            $total += $order->get_total();
        }
        return $total;
    }
}

/**
 * AJAX Handler: Monthly Sales Chart
 */
function hg_ajax_get_monthly_sales_chart() {
    try {
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }

        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'labels' => [],
                'data' => [],
                'total' => 0,
                'message' => 'WooCommerce not available'
            ));
            return;
        }

        // Monta dados diários do mês atual
        $year = date('Y');
        $month = date('m');
        $days_in_month = date('t');
        $labels = [];
        $data = [];
        for ($d = 1; $d <= $days_in_month; $d++) {
            $labels[] = str_pad($d, 2, '0', STR_PAD_LEFT) . '/' . $month;
            $start = "$year-$month-" . str_pad($d, 2, '0', STR_PAD_LEFT) . " 00:00:00";
            $end = "$year-$month-" . str_pad($d, 2, '0', STR_PAD_LEFT) . " 23:59:59";
            $orders = wc_get_orders([
                'status' => ['completed'],
                'limit' => -1,
                'date_paid' => $start . '...' . $end,
            ]);
            $total = 0;
            foreach ($orders as $order) {
                $total += $order->get_total();
            }
            $data[] = (float)$total;
        }

        $total_month = function_exists('hg_get_monthly_sales') ? hg_get_monthly_sales() : array_sum($data);

        wp_send_json_success([
            'labels' => $labels,
            'data' => $data,
            'total' => $total_month,
            'title' => 'Vendas Mês Atual',
            'generated_at' => current_time('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        error_log('Monthly sales chart error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Error loading monthly sales: ' . $e->getMessage()]);
    }
}

/**
 * AJAX Handler: Annual Sales Chart
 */
function hg_ajax_get_annual_sales_chart() {
    try {
        $nonce = $_POST['_ajax_nonce'] ?? '';
        if (!empty($nonce) && !wp_verify_nonce($nonce, 'dashboard_ajax_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            return;
        }

        if (!function_exists('wc_get_orders')) {
            wp_send_json_success(array(
                'labels' => [],
                'data' => [],
                'total' => 0,
                'message' => 'WooCommerce not available'
            ));
            return;
        }

        // Monta dados mensais do ano atual
        $year = date('Y');
        $labels = [];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = sprintf('%02d/%s', $m, $year);
            $start = "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01 00:00:00";
            $end = date('Y-m-t 23:59:59', strtotime($start));
            $orders = wc_get_orders([
                'status' => ['completed'],
                'limit' => -1,
                'date_paid' => $start . '...' . $end,
            ]);
            $total = 0;
            foreach ($orders as $order) {
                $total += $order->get_total();
            }
            $data[] = (float)$total;
        }

        $total_year = function_exists('hg_get_annual_sales') ? hg_get_annual_sales() : array_sum($data);

        wp_send_json_success([
            'labels' => $labels,
            'data' => $data,
            'total' => $total_year,
            'title' => 'Vendas Anual',
            'generated_at' => current_time('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        error_log('Annual sales chart error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Error loading annual sales: ' . $e->getMessage()]);
    }
}