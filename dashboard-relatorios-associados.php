<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage AMEDIS
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Relatorios Associados
*/
if (!current_user_can('administrator') && !current_user_can('gerente') && !user_has_area_access('relatorios')) {
    wp_redirect(home_url());
    exit;
}

// Include functions file first
require_once get_template_directory() . '/functions-relatorios-associados.php';

// Scripts are handled by the isolated system
// Just ensure jQuery and Chart.js are loaded
wp_enqueue_script('jquery');
wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array('jquery'), '3.7.0', true);

// Main.js if exists
$main_js_path = get_template_directory() . '/assets/js/main.js';
if (file_exists($main_js_path)) {
    wp_enqueue_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.1', true);
}

// Enqueue the custom CSS
wp_enqueue_style('dashboard-associados-css', get_template_directory_uri() . '/assets/css/dashboard-relatorios-associados.css', array(), '1.1');

// Enqueue the custom JS
wp_enqueue_script('dashboard-associados-js', get_template_directory_uri() . '/assets/js/dashboard-relatorios-associados.js', array('jquery', 'chart-js'), '1.1', true);

// Localize script with API data
wp_localize_script('dashboard-associados-js', 'dashboardData', array(
    'apiUrl' => rest_url('associados/v1/stats'),
    'nonce' => wp_create_nonce('wp_rest')
));

get_header('zero');
?>

<?php get_template_part('header', 'user') ?>

<main class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="uk-container mx-auto px-4 py-8">
        <!-- Modern Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center bg-slate-100 border-slate-300 text-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight text-slate-900">
                                Relatórios de Associados
                            </h1>
                            <p class="text-lg text-slate-600 mt-1">
                                Análise completa dos dados dos associados da plataforma
                            </p>
                        </div>
                    </div>
                </div>                
                
                <!-- Status Indicator -->
                <div class="flex items-center gap-2 p-3 bg-white rounded-lg border border-slate-200 shadow-sm">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-slate-700">Dados Atualizados</span>
                </div>
            </div>
        </div>

        <!-- Modern Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Associados Card -->
            <div class="stats-card fade-in">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="stat-label">Total de Associados</p>
                        <p class="stat-value text-blue-700" id="total-associados">0</p>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-default text-xs">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Ativo
                            </span>
                        </div>
                    </div>
                    <div class="icon-container icon-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Novos este Mês Card -->
            <div class="stats-card fade-in-delay-1">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="stat-label">Novos este Mês</p>
                        <p class="stat-value text-green-600 dark:text-green-400" id="novos-associados-mes">0</p>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-secondary text-xs">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mensal
                            </span>
                        </div>
                    </div>
                    <div class="icon-container icon-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Associados Ativos Card -->
            <div class="stats-card fade-in-delay-2">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="stat-label">Associados Ativos</p>
                        <p class="stat-value text-purple-600 dark:text-purple-400" id="associados-ativos">0</p>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-outline text-xs">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verificado
                            </span>
                        </div>
                    </div>
                    <div class="icon-container icon-purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Com Médico Prescritor Card -->
            <div class="stats-card fade-in-delay-3">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="stat-label">Com Médico Prescritor</p>
                        <p class="stat-value text-indigo-600 dark:text-indigo-400" id="com-medico-prescritor">0</p>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-secondary text-xs">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Médico
                            </span>
                        </div>
                    </div>
                    <div class="icon-container icon-indigo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Charts Section -->
        <div class="space-y-8">
            <!-- Main Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Crescimento de Associados -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Crescimento de Associados
                            </h3>
                            <p class="card-description">
                                Evolução temporal do número de associados
                            </p>
                        </div>
                        <div class="badge badge-default">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Crescimento
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="crescimentoAssociadosChart"></canvas>
                    </div>
                </div>

                <!-- Associados por Estado -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Associados por Estado
                            </h3>
                            <p class="card-description">
                                Distribuição geográfica dos associados
                            </p>
                        </div>
                        <div class="badge badge-secondary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Geografia
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="associadosPorEstadoChart"></canvas>
                    </div>
                </div>

                <!-- Tipos de Associação -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Tipos de Associação
                            </h3>
                            <p class="card-description">
                                Categorização dos tipos de associação
                            </p>
                        </div>
                        <div class="badge badge-outline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Categorias
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="associadosPorTipoChart"></canvas>
                    </div>
                </div>

                <!-- Distribuição por Gênero -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Distribuição por Gênero
                            </h3>
                            <p class="card-description">
                                Análise demográfica por gênero
                            </p>
                        </div>
                        <div class="badge badge-secondary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Demografia
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="associadosPorGeneroChart"></canvas>
                    </div>
                </div>

                <!-- Plano de Saúde -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Cobertura de Plano de Saúde
                            </h3>
                            <p class="card-description">
                                Análise da cobertura de planos de saúde
                            </p>
                        </div>
                        <div class="badge badge-default">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Saúde
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="planoSaudeChart"></canvas>
                    </div>
                </div>

                <!-- Associados Ativos por Estado -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Associados Ativos por Estado
                            </h3>
                            <p class="card-description">
                                Distribuição geográfica dos associados ativos
                            </p>
                        </div>
                        <div class="badge badge-default">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Ativos
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="ativosEstadoChart"></canvas>
                    </div>
                </div>

                <!-- Histórico de Uso de Cannabis -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="card-title">
                                Histórico de Uso de Cannabis
                            </h3>
                            <p class="card-description">
                                Análise do histórico de uso terapêutico de cannabis
                            </p>
                        </div>
                        <div class="badge badge-secondary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Histórico
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="cannabisHistoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</main>

<!-- Debug info -->
<script>
console.log('=== DASHBOARD ASSOCIADOS DEBUG INFO ===');
console.log('Current URL:', window.location.href);
console.log('Template loaded:', 'dashboard-relatorios-associados.php');

// Verificar se dashboardData foi definido
setTimeout(function() {
    console.log('dashboardData available:', typeof dashboardData !== 'undefined');
    if (typeof dashboardData !== 'undefined') {
        console.log('dashboardData:', dashboardData);
    }
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('Chart.js available:', typeof Chart !== 'undefined');
    console.log('CSS loaded:', document.querySelector('link[href*="dashboard-relatorios-associados.css"]') !== null);
}, 200);

console.log('=== END DEBUG INFO ===');

// Fallback apenas se o JavaScript principal falhar
setTimeout(function() {
    // Verificar se os cards ainda estão com erro após 2 segundos
    const totalElement = document.getElementById('total-associados');
    if (totalElement && (totalElement.textContent === 'Erro' || totalElement.textContent === '0')) {
        console.log('Fallback: Carregando dados via script inline...');
        fetch('<?php echo rest_url('associados/v1/stats'); ?>', {
            method: 'GET',
            headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && typeof data === 'object') {
                const novosElement = document.getElementById('novos-associados-mes');
                const ativosElement = document.getElementById('associados-ativos');
                const medicoElement = document.getElementById('com-medico-prescritor');
                
                if (totalElement) totalElement.textContent = data.total || 0;
                if (novosElement) novosElement.textContent = data.novos_mes || 0;
                if (ativosElement) ativosElement.textContent = data.ativos || 0;
                if (medicoElement) {
                    const comMedico = data.com_medico_prescritor ? data.com_medico_prescritor.data[0] : 0;
                    medicoElement.textContent = comMedico;
                }
            }
        })
        .catch(error => console.error('Erro no fallback:', error));
    }
}, 2000);
</script>

<?php
get_footer();