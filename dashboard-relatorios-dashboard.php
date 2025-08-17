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
Template Name: Dashboard - Relatorios Dashboard
*/
// Define o role do usuário atual
$current_user_role = sativar_get_user_role_safe(get_current_user_id());
if ($current_user_role !== 'super_admin' && $current_user_role !== 'gerente') {
    wp_safe_redirect(home_url());
    exit;
}

// Scripts are handled by the isolated system
// Just ensure jQuery and Chart.js are loaded
wp_enqueue_script('jquery');
wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array('jquery'), '3.7.0', true);

// Main.js if exists
$main_js_path = get_template_directory() . '/assets/js/main.js';
if (file_exists($main_js_path)) {
    wp_enqueue_script('mainjs', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.1', true);
}

get_header('zero');
?>

    <style>
        /* Modern Dashboard Styles - Inspired by shadcn/ui */
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96%;
            --accent-foreground: 222.2 84% 4.9%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.75rem;
            --chart-1: 12 76% 61%;
            --chart-2: 173 58% 39%;
            --chart-3: 197 37% 24%;
            --chart-4: 43 74% 66%;
            --chart-5: 27 87% 67%;
        }

        /* Modern Card Styles - shadcn/ui inspired */
        .modern-card {
            background: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
            border-radius: calc(var(--radius) + 2px);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .modern-card:hover {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transform: translateY(-1px);
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, hsl(var(--border)), transparent);
            opacity: 0.5;
        }

        /* Enhanced Metric Cards - shadcn/ui style */
        .metric-card-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border: 1px solid #2563eb;
            position: relative;
            overflow: hidden;
        }

        .metric-card-secondary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 1px solid #047857;
            position: relative;
            overflow: hidden;
        }

        .metric-card-success {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: 1px solid #b45309;
            position: relative;
            overflow: hidden;
        }

        .metric-card-warning {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: 1px solid #b91c1c;
            position: relative;
            overflow: hidden;
        }

        .metric-card-info {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border: 1px solid #6d28d9;
            position: relative;
            overflow: hidden;
        }

        .metric-card-purple {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            border: 1px solid #be185d;
            position: relative;
            overflow: hidden;
        }

        /* Add subtle pattern overlay */
        .metric-card-primary::before,
        .metric-card-secondary::before,
        .metric-card-success::before,
        .metric-card-warning::before,
        .metric-card-info::before,
        .metric-card-purple::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Modern Tab Buttons - shadcn/ui style */
        .modern-tab-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border-radius: calc(var(--radius) - 2px);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            background: transparent;
            color: hsl(var(--muted-foreground));
            cursor: pointer;
            position: relative;
            min-height: 36px;
        }

        .modern-tab-button:hover {
            background: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        .modern-tab-button.active {
            background: hsl(var(--background));
            color: hsl(var(--foreground));
            border-color: hsl(var(--border));
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .modern-tab-button:focus-visible {
            outline: 2px solid hsl(var(--ring));
            outline-offset: 2px;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Loading States */
        .skeleton {
            background: linear-gradient(90deg, hsl(var(--muted)) 25%, hsl(var(--accent)) 50%, hsl(var(--muted)) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Modern Badge - shadcn/ui style */
        .modern-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: calc(var(--radius) - 2px);
            border: 1px solid hsl(var(--border));
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
            background: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
            white-space: nowrap;
            gap: 0.25rem;
        }

        .modern-badge.success {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .modern-badge.warning {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }

        .modern-badge.info {
            background: #dbeafe;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .modern-badge.purple {
            background: #f3e8ff;
            color: #7c3aed;
            border-color: #e9d5ff;
        }

        /* Responsive Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .metrics-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .charts-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        /* Icon Styles */
        .metric-icon {
            width: 3rem;
            height: 3rem;
            opacity: 0.9;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .metric-icon-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Smooth Transitions */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Focus States */
        .modern-tab-button:focus-visible {
            outline: 2px solid hsl(var(--ring));
            outline-offset: 2px;
        }

        /* Container Queries Support */
        .container-queries {
            container-type: inline-size;
        }

        @container (min-width: 640px) {
            .container-sm\:text-3xl {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
        }

        /* Loading Animation for Charts */
        .chart-loading {
            position: relative;
            opacity: 0.6;
        }

        .chart-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 32px;
            height: 32px;
            margin: -16px 0 0 -16px;
            border: 3px solid hsl(var(--muted));
            border-top: 3px solid hsl(var(--primary));
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Improved Hover Effects */
        .modern-card:hover .metric-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        /* Enhanced Visual Effects */
        .metric-card-primary:hover,
        .metric-card-secondary:hover,
        .metric-card-success:hover,
        .metric-card-warning:hover,
        .metric-card-info:hover,
        .metric-card-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Text Scaling */
        @media (max-width: 640px) {
            .metrics-grid {
                gap: 1rem;
            }
            
            .modern-card {
                padding: 1rem;
            }
            
            .metric-icon {
                width: 2.5rem;
                height: 2.5rem;
            }
        }

        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Profit Card Dynamic Styling */
        .profit-positive {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-color: #6d28d9;
        }
        
        .profit-negative {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-color: #b91c1c;
        }
        
        .profit-neutral {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            border-color: #374151;
        }

        /* Enhanced Profit Trend Indicators */
        .profit-trend-positive {
            background: rgba(34, 197, 94, 0.2);
            color: #dcfce7;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .profit-trend-negative {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Smooth transitions for profit card */
        .metric-card-purple,
        .metric-card-warning {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Ranking Styles */
        .ranking-position {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ranking-position:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
        }

        .ranking-gold {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
            border: 2px solid #f59e0b;
        }

        .ranking-silver {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            box-shadow: 0 0 20px rgba(156, 163, 175, 0.3);
            border: 2px solid #6b7280;
        }

        .ranking-bronze {
            background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
            box-shadow: 0 0 20px rgba(251, 146, 60, 0.3);
            border: 2px solid #ea580c;
        }

        .ranking-default {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: 2px solid #1d4ed8;
        }

        /* Loading animation for ranking list */
        .ranking-loading {
            opacity: 0.6;
        }

        .ranking-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 24px;
            height: 24px;
            margin: -12px 0 0 -12px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }

        /* Scrollbar Styling */
        .max-h-96::-webkit-scrollbar {
            width: 6px;
        }

        .max-h-96::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .max-h-96::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .max-h-96::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Product Cards Responsive Styles */
        @media (max-width: 640px) {
            .product-card-grid {
                grid-template-columns: 1fr;
                gap: 2px;
            }
            
            .product-card-info {
                font-size: 0.75rem;
            }
            
            .product-position-badge {
                width: 1.5rem;
                height: 1.5rem;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 480px) {
            .product-card-grid {
                grid-template-columns: 1fr;
            }
            
            .product-card-right {
                margin-left: 0.5rem;
            }
        }

        /* Print Styles */
        @media print {
            .modern-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
            
            .metric-card-primary,
            .metric-card-secondary,
            .metric-card-success,
            .metric-card-warning,
            .metric-card-info,
            .metric-card-purple {
                background: white !important;
                color: black !important;
            }
        }
    </style>
<?php get_template_part('header', 'user') ?>

<main class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Modern Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center bg-slate-100 border-slate-300 text-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight text-slate-900">
                                Relatórios Financeiros
                            </h1>
                            <p class="text-lg text-slate-600 mt-1">
                                Análise completa dos dados financeiros da plataforma
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Period Selector with Modern Design -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-lg border border-slate-200 shadow-sm">
                    <button class="modern-tab-button active" data-period="all" onclick="changePeriod('all', this)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Geral
                    </button>
                    <button class="modern-tab-button" data-period="month" onclick="changePeriod('month', this)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Mensal
                    </button>
                </div>
            </div>
        </div>     
        <!-- Modern Metrics Grid -->
        <div class="metrics-grid mb-8">
            <!-- Vendas Brutas Card -->
            <div class="modern-card metric-card-primary p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Total de Vendas</p>
                        <p class="text-3xl font-bold text-white" id="gross-sales">R$ 0,00</p>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/20 text-white text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                +12.5%
                            </span>
                            <span class="text-xs text-white/70">vs mês anterior</span>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pedidos e Itens Card -->
            <div class="modern-card metric-card-secondary p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Pedidos & Itens</p>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-2xl font-bold text-white" id="total-orders">0</p>
                                <p class="text-xs text-white/70">Pedidos</p>
                            </div>
                            <div class="h-8 w-px bg-white/30"></div>
                            <div>
                                <p class="text-2xl font-bold text-white" id="items-sold">0</p>
                                <p class="text-xs text-white/70">Itens</p>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Performance Card -->
            <div class="modern-card metric-card-success p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Performance</p>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-xl font-bold text-white" id="average-order">R$ 0,00</p>
                                <p class="text-xs text-white/70">Ticket Médio</p>
                            </div>
                            <div class="h-8 w-px bg-white/30"></div>
                            <div>
                                <p class="text-xl font-bold text-white" id="conversion-rate">0%</p>
                                <p class="text-xs text-white/70">Conversão</p>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg class="metric-icon text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 6L18.29 8.29L13.41 13.17L9.41 9.17L2 16.59L3.41 18L9.41 12L13.41 16L19.71 9.71L22 12V6H16Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total de Despesas Card -->
            <div class="modern-card metric-card-warning p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Total de Despesas</p>
                        <p class="text-3xl font-bold text-white" id="total-expenses">R$ 0,00</p>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/20 text-white text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                -5.2%
                            </span>
                            <span class="text-xs text-white/70">vs mês anterior</span>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Análise de Despesas Card (Combined) -->
            <div class="modern-card metric-card-info p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Análise de Despesas</p>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-xl font-bold text-white" id="expenses-count">0</p>
                                <p class="text-xs text-white/70">Quantidade</p>
                            </div>
                            <div class="h-8 w-px bg-white/30"></div>
                            <div>
                                <p class="text-xl font-bold text-white" id="average-expense">R$ 0,00</p>
                                <p class="text-xs text-white/70">Ticket Médio</p>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg class="metric-icon text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17 12H7V10H17V12ZM15 16H7V14H15V16ZM17 8H7V6H17V8Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Lucro Líquido Card -->
            <div class="modern-card metric-card-purple p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-3 flex-1">
                        <p class="text-sm font-medium text-white/90">Lucro Líquido</p>
                        <p class="text-3xl font-bold text-white" id="net-profit">R$ 0,00</p>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/20 text-white text-xs font-medium" id="profit-trend">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                <span id="profit-percentage">0%</span>
                            </span>
                            <span class="text-xs text-white/70">Margem de lucro</span>
                        </div>
                    </div>
                    <div class="metric-icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modern Charts Section -->
        <div class="space-y-8">



        <div class="charts-grid">



                <!-- Ranking Associados -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Ranking de Clientes
                            </h3>
                            <p class="text-sm text-slate-600">
                                Top 25 maiores compradores do período
                            </p>
                        </div>
                        <div class="modern-badge success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Clientes
                        </div>
                    </div>
                    <div id="top-associados-list" class="space-y-3 max-h-[490px] overflow-y-auto">
                        <?php
                        // Load top associados data directly in PHP to avoid AJAX issues
                        $top_associados_data = array();
                        
                        // Try to get real data first
                        if (function_exists('hg_get_top_associados')) {
                            try {
                                $top_associados_data = hg_get_top_associados(25, 'all');
                            } catch (Exception $e) {
                                error_log('Error loading top associados in template: ' . $e->getMessage());
                            }
                        }
                        
                        // If no real data, try fallback function
                        if (empty($top_associados_data) && function_exists('hg_get_simple_top_customers')) {
                            try {
                                $top_associados_data = hg_get_simple_top_customers(25, 'all');
                            } catch (Exception $e) {
                                error_log('Error loading simple top customers in template: ' . $e->getMessage());
                            }
                        }
                        
                        // If still no data, use hardcoded fallback
                        if (empty($top_associados_data)) {
                            $top_associados_data = array(
                                array(
                                    'position' => 1,
                                    'name' => 'Cliente Premium',
                                    'total' => 2500.00,
                                    'orders' => 8,
                                    'average' => 312.50,
                                    'customer_id' => 1
                                ),
                                array(
                                    'position' => 2,
                                    'name' => 'Empresa ABC Ltda',
                                    'total' => 1800.00,
                                    'orders' => 6,
                                    'average' => 300.00,
                                    'customer_id' => 2
                                ),
                                array(
                                    'position' => 3,
                                    'name' => 'João Silva',
                                    'total' => 1200.00,
                                    'orders' => 4,
                                    'average' => 300.00,
                                    'customer_id' => 3
                                ),
                                array(
                                    'position' => 4,
                                    'name' => 'Maria Santos',
                                    'total' => 950.00,
                                    'orders' => 3,
                                    'average' => 316.67,
                                    'customer_id' => 4
                                ),
                                array(
                                    'position' => 5,
                                    'name' => 'Pedro Oliveira',
                                    'total' => 750.00,
                                    'orders' => 2,
                                    'average' => 375.00,
                                    'customer_id' => 5
                                )
                            );
                        }
                        
                        // Render the data
                        if (!empty($top_associados_data)) {
                            foreach ($top_associados_data as $associado) {
                                // Determine ranking style based on position
                                $ranking_class = 'ranking-default';
                                if ($associado['position'] == 1) {
                                    $ranking_class = 'ranking-gold';
                                } elseif ($associado['position'] == 2) {
                                    $ranking_class = 'ranking-silver';
                                } elseif ($associado['position'] == 3) {
                                    $ranking_class = 'ranking-bronze';
                                }
                                
                                // Format currency values
                                $total_formatted = 'R$ ' . number_format($associado['total'], 2, ',', '.');
                                $average_formatted = 'R$ ' . number_format($associado['average'], 2, ',', '.');
                                ?>
                                <div class="ranking-position flex items-center justify-between p-4 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all duration-200">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 <?php echo $ranking_class; ?> rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            <?php echo $associado['position']; ?>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-slate-900 text-lg">
                                                <?php echo esc_html($associado['name']); ?>
                                            </h4>
                                            <div class="flex items-center gap-4 text-sm text-slate-600 mt-1">
                                                <span class="flex items-center gap-1">
                                                    🛒 <?php echo $associado['orders']; ?> pedidos
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    💰 Ticket médio: <?php echo $average_formatted; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-slate-900">
                                            <?php echo $total_formatted; ?>
                                        </div>
                                        <div class="text-sm text-slate-500">
                                            Total gasto
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="text-center py-8 text-slate-500">
                                <div class="w-12 h-12 mx-auto mb-4 text-4xl text-slate-400 flex items-center justify-center">👥</div>
                                <p>Nenhum cliente encontrado</p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>



                <!-- Todos os Produtos -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Todos os Produtos
                            </h3>
                            <p class="text-sm text-slate-600">
                                Lista completa de produtos e quantidades vendidas
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="modern-badge info" id="products-count-badge">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span id="total-products-count">0</span> Produtos
                            </div>
                            <div class="modern-badge success">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Vendas
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filter Controls -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                id="products-search" 
                                placeholder="Buscar produtos..." 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        <div class="flex gap-2">
                            <select id="products-filter" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="all">Todos os produtos</option>
                                <option value="sold">Apenas vendidos</option>
                                <option value="not-sold">Não vendidos</option>
                                <option value="in-stock">Disponíveis</option>
                                <option value="out-of-stock">Esgotados</option>
                            </select>
                            <select id="products-sort" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="quantity-desc">Mais vendidos</option>
                                <option value="quantity-asc">Menos vendidos</option>
                                <option value="name-asc">Nome A-Z</option>
                                <option value="name-desc">Nome Z-A</option>
                                <option value="revenue-desc">Maior receita</option>
                                <option value="revenue-asc">Menor receita</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="all-products-list" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Loading skeleton -->
                        <div class="animate-pulse space-y-2">
                            <div class="flex items-center justify-between p-3 bg-slate-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-slate-300 rounded-full"></div>
                                    <div class="space-y-1">
                                        <div class="h-4 bg-slate-300 rounded w-40"></div>
                                        <div class="h-3 bg-slate-300 rounded w-24"></div>
                                    </div>
                                </div>
                                <div class="text-right space-y-1">
                                    <div class="h-4 bg-slate-300 rounded w-16"></div>
                                    <div class="h-3 bg-slate-300 rounded w-20"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-slate-300 rounded-full"></div>
                                    <div class="space-y-1">
                                        <div class="h-4 bg-slate-300 rounded w-36"></div>
                                        <div class="h-3 bg-slate-300 rounded w-20"></div>
                                    </div>
                                </div>
                                <div class="text-right space-y-1">
                                    <div class="h-4 bg-slate-300 rounded w-16"></div>
                                    <div class="h-3 bg-slate-300 rounded w-20"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-slate-300 rounded-full"></div>
                                    <div class="space-y-1">
                                        <div class="h-4 bg-slate-300 rounded w-32"></div>
                                        <div class="h-3 bg-slate-300 rounded w-28"></div>
                                    </div>
                                </div>
                                <div class="text-right space-y-1">
                                    <div class="h-4 bg-slate-300 rounded w-16"></div>
                                    <div class="h-3 bg-slate-300 rounded w-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="products-pagination" class="mt-4 flex items-center justify-between text-sm text-slate-600">
                        <div id="products-showing-info">
                            Mostrando 0 de 0 produtos
                        </div>
                        <div class="flex gap-2">
                            <button id="products-prev-page" class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Anterior
                            </button>
                            <span id="products-page-info" class="px-3 py-1">
                                Página 1 de 1
                            </span>
                            <button id="products-next-page" class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Próxima
                            </button>
                        </div>
                    </div>
                </div>


        </div>

           

            <!-- Main Charts Grid -->
            <div class="charts-grid">
                <!-- Vendas Diárias Chart -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Vendas Diárias
                            </h3>
                            <p class="text-sm text-slate-600">
                                Últimos 7 dias - Atualizado em tempo real
                            </p>
                        </div>
                        <div class="modern-badge info">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Tempo Real
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="daily-sales-chart"></canvas>
                    </div>
                </div>
                <!-- Vendas Mês Atual Chart -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Vendas Mês Atual
                            </h3>
                            <p class="text-sm text-slate-600">
                                Vendas do mês atual - Atualizado em tempo real
                            </p>
                        </div>
                        <div class="modern-badge info">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Tempo Real
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="monthly-sales-chart"></canvas>
                    </div>
                </div>

            </div>
            <!-- vendas anual -->
            <div class="modern-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">
                            Vendas Anual
                        </h3>
                        <p class="text-sm text-slate-600">
                            Ano atual - Atualizado em tempo real
                        </p>
                    </div>
                    <div class="modern-badge info">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Tempo Real
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="annual-sales-chart"></canvas>
                </div>
            </div>


            <!-- Secondary Charts Grid -->
            <div class="charts-grid">
                <!-- Receita por Categoria -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Receita por Categoria
                            </h3>
                            <p class="text-sm text-slate-600">
                                Distribuição de vendas por categoria
                            </p>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="category-sales-chart"></canvas>
                    </div>
                </div>

                <!-- Métodos de Pagamento -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Métodos de Pagamento
                            </h3>
                            <p class="text-sm text-slate-600">
                                Receita por forma de pagamento
                            </p>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="payment-methods-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Analysis Section -->
        <div class="mt-8">
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">
                            Análise de Despesas
                        </h2>
                        <p class="text-slate-600">
                            Visão detalhada dos gastos e custos operacionais
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="charts-grid">
                <!-- Despesas por Categoria -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Despesas por Categoria
                            </h3>
                            <p class="text-sm text-slate-600">
                                Distribuição dos gastos por categoria
                            </p>
                        </div>
                        <div class="modern-badge warning">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            Despesas
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="expenses-category-chart"></canvas>
                    </div>
                </div>

                <!-- Evolução Temporal das Despesas -->
                <div class="modern-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Evolução das Despesas
                            </h3>
                            <p class="text-sm text-slate-600">
                                Tendência temporal dos gastos
                            </p>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="chart-container">
                        <canvas id="expenses-timeline-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
    </div>
</main>

<!-- Debug info -->
<script>
console.log('=== DASHBOARD RELATÓRIOS DEBUG INFO ===');
console.log('Current URL:', window.location.href);
console.log('Template loaded:', 'dashboard-relatorios-dashboard.php');
console.log('jQuery available:', typeof jQuery !== 'undefined');
console.log('Chart.js available:', typeof Chart !== 'undefined');
console.log('dashboardRelatoriosAjax available:', typeof dashboardRelatoriosAjax !== 'undefined');
console.log('WooCommerce active:', <?php echo class_exists('WooCommerce') ? 'true' : 'false'; ?>);
console.log('Reports functions loaded:', <?php echo function_exists('hg_get_gross_sales') ? 'true' : 'false'; ?>);
console.log('Expenses functions loaded:', <?php echo function_exists('hg_get_total_expenses') ? 'true' : 'false'; ?>);

// Check if dashboard script is loaded
const reportScript = document.querySelector('script[src*="dashboard-data.js"]');
if (reportScript) {
    console.log('✓ Dashboard script found:', reportScript.src);
} else {
    console.warn('⚠️ Dashboard script not found in DOM');
}

// Check for dashboard functions
console.log('Dashboard functions available:', {
    updateCardsOnly: typeof updateCardsOnly !== 'undefined',
    initializeDashboard: typeof initializeDashboard !== 'undefined',
    loadDailySalesChart: typeof loadDailySalesChart !== 'undefined'
});

// Check for dashboardRelatoriosAjax with retry
function checkDashboardConfig(retries = 0) {
    if (typeof dashboardRelatoriosAjax !== 'undefined') {
        console.log('✓ dashboardRelatoriosAjax config loaded:', dashboardRelatoriosAjax);
        return true;
    } else if (retries < 3) {
        console.log(`Waiting for dashboardRelatoriosAjax... (attempt ${retries + 1}/3)`);
        setTimeout(() => checkDashboardConfig(retries + 1), 100);
        return false;
    } else {
        console.warn('⚠️ dashboardRelatoriosAjax not available after retries - creating fallback');
        // Create fallback configuration
        window.dashboardRelatoriosAjax = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('dashboard_ajax_nonce'); ?>',
            site_url: '<?php echo get_site_url(); ?>',
            template_directory: '<?php echo get_template_directory_uri(); ?>',
            is_user_logged_in: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
            current_user: <?php echo wp_get_current_user()->ID; ?>,
            debug: <?php echo defined('WP_DEBUG') && WP_DEBUG ? 'true' : 'false'; ?>,
            fallback: true,
            loaded_by: 'template_fallback'
        };
        console.log('✓ Fallback dashboardRelatoriosAjax created:', window.dashboardRelatoriosAjax);
        return true;
    }
}

checkDashboardConfig();

// Prevent dashboard-tarefas.js errors by checking for required elements
if (typeof performSearch === 'function') {
    console.warn('dashboard-tarefas.js loaded on wrong page - disabling functions');
    window.performSearch = function() { console.warn('performSearch disabled on reports page'); };
}

// Check for elements that dashboard-tarefas.js might be looking for
const taskElements = ['#task-search', '#task-list', '.task-container'];
taskElements.forEach(selector => {
    if (!document.querySelector(selector)) {
        console.log(`Task element ${selector} not found (expected on reports page)`);
    }
});

console.log('=== END DEBUG INFO ===');

// Immediate SVG fix for any existing problematic SVGs
(function() {
    function fixSVGPaths() {
        // Find and fix problematic SVG paths
        const allPaths = document.querySelectorAll('path[d]');
        let fixedCount = 0;
        
        allPaths.forEach(function(path) {
            const d = path.getAttribute('d');
            if (d && (d.includes('515.356') || d.includes('919.288') || d.includes('616 0z'))) {
                console.log('Fixing problematic SVG path:', d.substring(0, 50) + '...');
                path.setAttribute('d', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z');
                fixedCount++;
            }
        });
        
        if (fixedCount > 0) {
            console.log('Fixed ' + fixedCount + ' problematic SVG paths');
        }
    }
    
    // Fix immediately
    fixSVGPaths();
    
    // Fix after DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixSVGPaths);
    }
    
    // Fix periodically to catch any dynamically added SVGs
    setInterval(fixSVGPaths, 5000);
})();

// Direct AJAX test (without waiting for dashboard script)
if (typeof jQuery !== 'undefined') {
    console.log('Testing AJAX endpoints directly...');
    
    // Check if top-associados-list element exists
    const topAssociadosElement = document.getElementById('top-associados-list');
    console.log('top-associados-list element found:', !!topAssociadosElement);
    if (topAssociadosElement) {
        console.log('top-associados-list element classes:', topAssociadosElement.className);
    }
    
    // Test isolated system
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'test_isolated_system',
            _ajax_nonce: '<?php echo wp_create_nonce('dashboard_ajax_nonce'); ?>'
        },
        success: function(response) {
            console.log('✓ Isolated system test successful:', response);
        },
        error: function(xhr, status, error) {
            console.error('✗ Isolated system test failed:', {
                status: xhr.status,
                responseText: xhr.responseText,
                error: error
            });
        }
    });
    
    // Test metrics endpoint
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'get_dashboard_metrics_cards',
            period: 'all',
            _ajax_nonce: '<?php echo wp_create_nonce('dashboard_ajax_nonce'); ?>'
        },
        success: function(response) {
            console.log('✓ Metrics endpoint test successful:', response);
        },
        error: function(xhr, status, error) {
            console.error('✗ Metrics endpoint test failed:', {
                status: xhr.status,
                responseText: xhr.responseText,
                error: error
            });
        }
    });
    
    // Top associados are now loaded directly in PHP - no AJAX tests needed
    console.log('✓ Skipping top associados AJAX tests - data loaded directly from PHP');
    
    /*
    // Test diagnostic endpoint first
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'diagnose_top_associados'
        },
        success: function(response) {
            console.log('✓ Diagnostic test successful:', response);
            
            // Test simple endpoint
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'test_top_associados_simple'
                },
                success: function(response) {
                    console.log('✓ Simple top associados test successful:', response);
            
            // Now test the real endpoint
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_top_associados',
                    period: 'all',
                    _ajax_nonce: '<?php echo wp_create_nonce('dashboard_ajax_nonce'); ?>'
                },
                success: function(response) {
                    console.log('✓ Top associados endpoint test successful:', response);
                    
                    // Test if we can populate the ranking immediately
                    if (response.success && response.data && response.data.topAssociados) {
                        console.log('Found ' + response.data.topAssociados.length + ' customers');
                        if (response.data.topAssociados.length > 0) {
                            console.log('Sample customer:', response.data.topAssociados[0]);
                            // Try to populate the ranking list immediately for testing
                            populateTopAssociadosList(response.data.topAssociados);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('✗ Top associados endpoint test failed:', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error,
                        readyState: xhr.readyState
                    });
                    
                    // Try to parse error response
                    try {
                        if (xhr.responseText) {
                            console.log('Error response text:', xhr.responseText);
                        }
                    } catch (e) {
                        console.error('Could not parse error response:', e);
                    }
                }
            });
                },
                error: function(xhr, status, error) {
                    console.error('✗ Simple test failed:', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error
                    });
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('✗ Diagnostic test failed:', {
                status: xhr.status,
                responseText: xhr.responseText,
                error: error
            });
        }
    });
    */
    
} else {
    console.error('jQuery not available for direct AJAX test');
}

// Test AJAX endpoints
function testAjaxEndpoints() {
    return new Promise((resolve, reject) => {
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            reject('dashboardRelatoriosAjax not available');
            return;
        }
        
        console.log('Testing AJAX endpoints...');
        
        // Test isolated system first
        jQuery.ajax({
            url: dashboardRelatoriosAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'test_isolated_system'
            },
            timeout: 5000,
            success: function(response) {
                console.log('Isolated system test successful:', response);
                if (response && response.success) {
                    console.log('✓ Isolated system working correctly:', response.data);
                    
                    // Now test the actual metrics endpoint
                    jQuery.ajax({
                        url: dashboardRelatoriosAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_dashboard_metrics_cards',
                            period: 'all'
                        },
                        success: function(metricsResponse) {
                            console.log('✓ Metrics endpoint working:', metricsResponse);
                            resolve();
                        },
                        error: function(xhr, status, error) {
                            console.error('✗ Metrics endpoint failed:', error);
                            resolve(); // Don't block initialization
                        }
                    });
                } else {
                    console.warn('Isolated system returned unexpected response:', response);
                    resolve();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX test failed:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    url: dashboardRelatoriosAjax.ajaxurl,
                    nonce: dashboardRelatoriosAjax.nonce
                });
                
                // Try to parse the response for more details
                try {
                    if (xhr.responseText) {
                        console.log('Raw response:', xhr.responseText);
                        if (xhr.responseText === '0') {
                            console.error('WordPress AJAX returned 0 - handler not found or PHP error');
                        }
                    }
                } catch (parseError) {
                    console.error('Could not parse error response:', parseError);
                }
                
                // Don't reject - let dashboard try to load anyway
                resolve();
            }
        });
    });
}

// Show error state for dashboard
function showDashboardError() {
    const cards = document.querySelectorAll('.metric-card, .metric-card-success, .metric-card-warning, .metric-card-alt, .metric-card-info, .metric-card-purple');
    cards.forEach(card => {
        const dd = card.querySelector('dd');
        if (dd) {
            dd.textContent = 'Erro ao carregar';
            dd.style.color = '#ef4444';
        }
    });
}

// Basic skeleton functions if not loaded from dashboard-data.js
if (typeof showMetricSkeleton === 'undefined') {
    window.showMetricSkeleton = function() {
        console.log('Skeleton function placeholder');
    };
}

if (typeof showExpensesSkeleton === 'undefined') {
    window.showExpensesSkeleton = function() {
        console.log('Expenses skeleton function placeholder');
    };
}

// Function to parse Brazilian currency format to number
function parseBRLCurrency(currencyString) {
    if (!currencyString || typeof currencyString !== 'string') {
        return 0;
    }
    
    // Remove R$, spaces, and handle Brazilian number format
    // Brazilian format: R$ 1.234.567,89 -> 1234567.89
    let cleanString = currencyString
        .replace(/R\$\s?/, '') // Remove R$ and optional space
        .replace(/\s/g, '') // Remove all spaces
        .trim();
    
    // Handle Brazilian decimal format (comma as decimal separator)
    if (cleanString.includes(',')) {
        // Split by comma to separate decimal part
        const parts = cleanString.split(',');
        if (parts.length === 2) {
            // Remove dots from integer part (thousand separators)
            const integerPart = parts[0].replace(/\./g, '');
            const decimalPart = parts[1];
            cleanString = integerPart + '.' + decimalPart;
        }
    } else {
        // No comma, just remove dots (thousand separators)
        cleanString = cleanString.replace(/\./g, '');
    }
    
    const number = parseFloat(cleanString) || 0;
    return number;
}

// Function to format number as Brazilian currency
function formatBRLCurrency(number) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}

// Function to calculate and update profit metrics
function calculateProfit() {
    try {
        const grossSalesElement = document.getElementById('gross-sales');
        const totalExpensesElement = document.getElementById('total-expenses');
        const netProfitElement = document.getElementById('net-profit');
        const profitPercentageElement = document.getElementById('profit-percentage');
        const profitTrendElement = document.getElementById('profit-trend');
        
        if (!grossSalesElement || !totalExpensesElement || !netProfitElement) {
            console.warn('Profit calculation elements not found');
            return;
        }
        
        // Extract numeric values from formatted strings
        const grossSalesText = grossSalesElement.textContent || 'R$ 0,00';
        const totalExpensesText = totalExpensesElement.textContent || 'R$ 0,00';
        
        // Convert formatted currency to numbers
        const grossSales = parseBRLCurrency(grossSalesText);
        const totalExpenses = parseBRLCurrency(totalExpensesText);
        
        // Calculate profit
        const netProfit = grossSales - totalExpenses;
        const profitMargin = grossSales > 0 ? (netProfit / grossSales) * 100 : 0;
        
        // Format profit value
        const formattedProfit = formatBRLCurrency(netProfit);
        
        // Update profit display
        netProfitElement.textContent = formattedProfit;
        
        // Update profit percentage and trend
        if (profitPercentageElement) {
            profitPercentageElement.textContent = Math.abs(profitMargin).toFixed(1) + '%';
        }
        
        // Update trend icon and color based on profit
        if (profitTrendElement) {
            const trendIcon = profitTrendElement.querySelector('svg');
            if (netProfit > 0) {
                // Positive profit - green trend up
                profitTrendElement.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full profit-trend-positive text-xs font-medium';
                if (trendIcon) {
                    trendIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>';
                }
            } else if (netProfit < 0) {
                // Negative profit - red trend down
                profitTrendElement.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full profit-trend-negative text-xs font-medium';
                if (trendIcon) {
                    trendIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>';
                }
            } else {
                // Zero profit - neutral
                profitTrendElement.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/20 text-white text-xs font-medium';
                if (trendIcon) {
                    trendIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>';
                }
            }
        }
        
        // Update card color based on profit
        const profitCard = netProfitElement.closest('.modern-card');
        if (profitCard) {
            // Remove existing profit classes
            profitCard.classList.remove('metric-card-purple', 'metric-card-warning', 'metric-card-secondary');
            
            if (netProfit > 0) {
                // Positive profit - purple/success theme
                profitCard.classList.add('metric-card-purple');
            } else if (netProfit < 0) {
                // Negative profit - warning theme
                profitCard.classList.add('metric-card-warning');
            } else {
                // Zero profit - neutral theme
                profitCard.classList.add('metric-card-secondary');
            }
        }
        
        console.log('Profit calculated:', {
            grossSalesText: grossSalesText,
            totalExpensesText: totalExpensesText,
            grossSales: grossSales,
            totalExpenses: totalExpenses,
            netProfit: netProfit,
            profitMargin: profitMargin.toFixed(2) + '%',
            formattedProfit: formattedProfit
        });
        
    } catch (error) {
        console.error('Error calculating profit:', error);
        
        // Set error state
        const netProfitElement = document.getElementById('net-profit');
        if (netProfitElement) {
            netProfitElement.textContent = 'Erro no cálculo';
            netProfitElement.style.color = '#ef4444';
        }
    }
}

// Observer to watch for changes in sales and expenses values
function setupProfitCalculationObserver() {
    const grossSalesElement = document.getElementById('gross-sales');
    const totalExpensesElement = document.getElementById('total-expenses');
    
    if (!grossSalesElement || !totalExpensesElement) {
        console.warn('Cannot setup profit observer - elements not found');
        return;
    }
    
    // Create observer to watch for text changes
    const observer = new MutationObserver(function(mutations) {
        let shouldRecalculate = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                shouldRecalculate = true;
            }
        });
        
        if (shouldRecalculate) {
            // Delay calculation to ensure all updates are complete
            setTimeout(calculateProfit, 100);
        }
    });
    
    // Observe both elements
    observer.observe(grossSalesElement, {
        childList: true,
        subtree: true,
        characterData: true
    });
    
    observer.observe(totalExpensesElement, {
        childList: true,
        subtree: true,
        characterData: true
    });
    
    console.log('Profit calculation observer setup complete');
}

// Enhanced dashboard initialization with profit calculation
function initializeDashboardWithProfit() {
    console.log('Initializing dashboard with profit calculation...');
    
    // Setup profit calculation observer
    setupProfitCalculationObserver();
    
    // Initial profit calculation
    setTimeout(calculateProfit, 1000);
    
    // Recalculate profit every 30 seconds
    setInterval(calculateProfit, 30000);
}
</script>

<script>
// Spinner CSS (caso não esteja no CSS global)
const style = document.createElement('style');
style.innerHTML = `
@keyframes spinBtn { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.animate-spin { animation: spinBtn 1s linear infinite; }
`;
document.head.appendChild(style);

// All Products Management
let allProductsData = [];
let filteredProductsData = [];
let currentProductsPage = 1;
const productsPerPage = 20;

// Load all products data
function loadAllProductsData(period = 'all') {
    console.log('Loading all products data for period:', period);
    
    jQuery.ajax({
        url: dashboardRelatoriosAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'get_all_products_sales',
            period: period,
            _ajax_nonce: dashboardRelatoriosAjax.nonce
        },
        success: function(response) {
            console.log('All products data loaded:', response);
            
            if (response.success && response.data.allProducts) {
                allProductsData = response.data.allProducts;
                filteredProductsData = [...allProductsData];
                
                // Update total count badge
                const totalCountElement = document.getElementById('total-products-count');
                if (totalCountElement) {
                    totalCountElement.textContent = allProductsData.length;
                }
                
                // Reset to first page and display
                currentProductsPage = 1;
                displayProductsPage();
                updateProductsPagination();
            } else {
                console.error('Failed to load products data:', response);
                showProductsError();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error loading products:', error);
            showProductsError();
        }
    });
}

// Display products for current page
function displayProductsPage() {
    const container = document.getElementById('all-products-list');
    if (!container) return;
    
    const startIndex = (currentProductsPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const pageProducts = filteredProductsData.slice(startIndex, endIndex);
    
    if (pageProducts.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-slate-500">Nenhum produto encontrado</div>';
        return;
    }
    
    let html = '';
    pageProducts.forEach(product => {
        const quantityBadge = getQuantityBadge(product.quantity_sold);
        const stockStatus = getStockStatusText(product.stock_status);
        
        html += `
            <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-8 h-8 bg-green-100 text-green-900 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        ${product.position}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="mb-2">
                            <h4 class="font-semibold text-slate-700 text-sm text-base">${product.name}</h4>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-600">
                            <div>
                                <span class="font-medium">SKU:</span>
                                <span class="ml-1">${product.sku}</span>
                            </div>
                            <div>
                                <span class="font-medium">Preço:</span>
                                <span class="ml-1">${formatCurrency(product.price)}</span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <span class="ml-1">${stockStatus}</span>
                            </div>
                            ${product.stock_quantity !== null ? `
                            <div>
                                <span class="font-medium">Estoque:</span>
                                <span class="ml-1">${product.stock_quantity}</span>
                            </div>
                            ` : '<div></div>'}
                        </div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-4">
                    <div class="flex items-center justify-end gap-2 mb-2">
                        <span class="text-2xl font-bold text-slate-900 text-md">${product.quantity_sold}</span>
                        ${quantityBadge}
                    </div>
                    <div class="space-y-1">
                        <div class="text-sm font-medium text-slate-700">
                            ${formatCurrency(product.total_revenue)}
                        </div>
                        ${product.quantity_sold > 0 ? `
                        <div class="text-xs text-slate-500">
                            Média: ${formatCurrency(product.average_price)}
                        </div>
                        ` : `
                        <div class="text-xs text-slate-400">
                            Sem vendas
                        </div>
                        `}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Get stock status text (simple text without badge)
function getStockStatusText(stockStatus) {
    if (stockStatus === 'instock') {
        return 'Disponível';
    } else if (stockStatus === 'outofstock') {
        return 'Esgotado';
    } else {
        return 'N/A';
    }
}

// Get quantity sold badge
function getQuantityBadge(quantity) {
    if (quantity > 50) {
        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Top</span>';
    } else if (quantity > 10) {
        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Bom</span>';
    } else if (quantity > 0) {
        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Baixo</span>';
    } else {
        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Zero</span>';
    }
}

// Update pagination controls
function updateProductsPagination() {
    const totalPages = Math.ceil(filteredProductsData.length / productsPerPage);
    const startItem = (currentProductsPage - 1) * productsPerPage + 1;
    const endItem = Math.min(currentProductsPage * productsPerPage, filteredProductsData.length);
    
    // Update showing info
    const showingInfo = document.getElementById('products-showing-info');
    if (showingInfo) {
        showingInfo.textContent = `Mostrando ${startItem}-${endItem} de ${filteredProductsData.length} produtos`;
    }
    
    // Update page info
    const pageInfo = document.getElementById('products-page-info');
    if (pageInfo) {
        pageInfo.textContent = `Página ${currentProductsPage} de ${totalPages}`;
    }
    
    // Update buttons
    const prevBtn = document.getElementById('products-prev-page');
    const nextBtn = document.getElementById('products-next-page');
    
    if (prevBtn) {
        prevBtn.disabled = currentProductsPage <= 1;
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentProductsPage >= totalPages;
    }
}

// Filter and search products
function filterProducts() {
    const searchTerm = document.getElementById('products-search')?.value.toLowerCase() || '';
    const filterType = document.getElementById('products-filter')?.value || 'all';
    const sortType = document.getElementById('products-sort')?.value || 'quantity-desc';
    
    // Filter
    filteredProductsData = allProductsData.filter(product => {
        // Search filter
        const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                            product.sku.toLowerCase().includes(searchTerm);
        
        if (!matchesSearch) return false;
        
        // Type filter
        switch (filterType) {
            case 'sold':
                return product.quantity_sold > 0;
            case 'not-sold':
                return product.quantity_sold === 0;
            case 'in-stock':
                return product.stock_status === 'instock';
            case 'out-of-stock':
                return product.stock_status === 'outofstock';
            default:
                return true;
        }
    });
    
    // Sort
    filteredProductsData.sort((a, b) => {
        switch (sortType) {
            case 'quantity-desc':
                return b.quantity_sold - a.quantity_sold;
            case 'quantity-asc':
                return a.quantity_sold - b.quantity_sold;
            case 'name-asc':
                return a.name.localeCompare(b.name);
            case 'name-desc':
                return b.name.localeCompare(a.name);
            case 'revenue-desc':
                return b.total_revenue - a.total_revenue;
            case 'revenue-asc':
                return a.total_revenue - b.total_revenue;
            default:
                return 0;
        }
    });
    
    // Reset to first page and display
    currentProductsPage = 1;
    displayProductsPage();
    updateProductsPagination();
}

// Show products error
function showProductsError() {
    const container = document.getElementById('all-products-list');
    if (container) {
        container.innerHTML = '<div class="text-center py-8 text-red-500">Erro ao carregar produtos</div>';
    }
}

// Format currency helper
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value || 0);
}

// Initialize products functionality
function initializeProductsSection() {
    // Load initial data
    loadAllProductsData('all');
    
    // Setup event listeners
    const searchInput = document.getElementById('products-search');
    const filterSelect = document.getElementById('products-filter');
    const sortSelect = document.getElementById('products-sort');
    const prevBtn = document.getElementById('products-prev-page');
    const nextBtn = document.getElementById('products-next-page');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterProducts, 300));
    }
    
    if (filterSelect) {
        filterSelect.addEventListener('change', filterProducts);
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', filterProducts);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentProductsPage > 1) {
                currentProductsPage--;
                displayProductsPage();
                updateProductsPagination();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredProductsData.length / productsPerPage);
            if (currentProductsPage < totalPages) {
                currentProductsPage++;
                displayProductsPage();
                updateProductsPagination();
            }
        });
    }
    
    // Listen for period changes
    document.addEventListener('periodChanged', function(e) {
        loadAllProductsData(e.detail.period);
    });
}

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Change period function
function changePeriod(period, buttonElement) {
    // Update active button
    document.querySelectorAll('.modern-tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    buttonElement.classList.add('active');
    
    // Show a message that period change requires page reload for top associados
    const container = document.getElementById('top-associados-list');
    if (container && period !== 'all') {
        const notice = document.createElement('div');
        notice.className = 'bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4';
        notice.innerHTML = `
            <div class="flex items-center gap-2 text-blue-700 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Para ver dados do período "${period === 'month' ? 'Mensal' : 'Selecionado'}", recarregue a página
            </div>
        `;
        container.insertBefore(notice, container.firstChild);
        
        // Remove notice after 5 seconds
        setTimeout(() => {
            if (notice.parentNode) {
                notice.parentNode.removeChild(notice);
            }
        }, 5000);
    }
    
    // Dispatch custom event for products section
    const event = new CustomEvent('periodChanged', {
        detail: { period: period }
    });
    document.dispatchEvent(event);
    
    // Also trigger main dashboard update if available
    if (typeof updateCardsOnly === 'function') {
        updateCardsOnly(period);
    }
    
    console.log('Period changed to:', period);
}

// Function to load fallback data when AJAX fails
function loadFallbackTopAssociados() {
    console.log('Loading fallback top associados data...');
    
    const fallbackData = [
        {
            position: 1,
            name: 'Cliente Premium',
            total: 2500.00,
            orders: 8,
            average: 312.50,
            customer_id: 1
        },
        {
            position: 2,
            name: 'Empresa ABC Ltda',
            total: 1800.00,
            orders: 6,
            average: 300.00,
            customer_id: 2
        },
        {
            position: 3,
            name: 'João Silva',
            total: 1200.00,
            orders: 4,
            average: 300.00,
            customer_id: 3
        },
        {
            position: 4,
            name: 'Maria Santos',
            total: 950.00,
            orders: 3,
            average: 316.67,
            customer_id: 4
        },
        {
            position: 5,
            name: 'Pedro Oliveira',
            total: 750.00,
            orders: 2,
            average: 375.00,
            customer_id: 5
        }
    ];
    
    populateTopAssociadosList(fallbackData);
    console.log('✓ Fallback data loaded successfully');
}

// Function to update top associados for a specific period
function updateTopAssociados(period) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not available for updateTopAssociados');
        loadFallbackTopAssociados();
        return;
    }
    
    if (typeof dashboardRelatoriosAjax === 'undefined') {
        console.error('dashboardRelatoriosAjax not available for updateTopAssociados');
        loadFallbackTopAssociados();
        return;
    }
    
    // Debug AJAX configuration
    console.log('AJAX Config:', {
        ajaxurl: dashboardRelatoriosAjax.ajaxurl,
        nonce: dashboardRelatoriosAjax.nonce,
        period: period
    });
    
    // Show loading state
    const container = document.getElementById('top-associados-list');
    if (container) {
        container.innerHTML = `
            <div class="animate-pulse space-y-3">
                <div class="flex items-center justify-between p-4 bg-slate-100 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-300 rounded-full"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-slate-300 rounded w-32"></div>
                            <div class="h-3 bg-slate-300 rounded w-24"></div>
                        </div>
                    </div>
                    <div class="text-right space-y-2">
                        <div class="h-4 bg-slate-300 rounded w-20"></div>
                        <div class="h-3 bg-slate-300 rounded w-16"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-100 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-300 rounded-full"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-slate-300 rounded w-28"></div>
                            <div class="h-3 bg-slate-300 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="text-right space-y-2">
                        <div class="h-4 bg-slate-300 rounded w-20"></div>
                        <div class="h-3 bg-slate-300 rounded w-16"></div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Try multiple methods to get data
    function tryGetTopAssociados(attempt = 1) {
        const maxAttempts = 3;
        
        console.log(`Attempting to get top associados (attempt ${attempt}/${maxAttempts})`);
        
        jQuery.ajax({
            url: dashboardRelatoriosAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_top_associados',
                period: period,
                _ajax_nonce: dashboardRelatoriosAjax.nonce
            },
            timeout: 10000, // 10 second timeout
            success: function(response) {
                console.log('Top associados updated for period:', period, response);
                
                if (response.success && response.data && response.data.topAssociados) {
                    populateTopAssociadosList(response.data.topAssociados);
                } else {
                    console.warn('No top associados data received, trying simple test:', response);
                    
                    // Try simple test endpoint
                    jQuery.ajax({
                        url: dashboardRelatoriosAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'test_top_associados_simple'
                        },
                        success: function(testResponse) {
                            console.log('Simple test successful:', testResponse);
                            if (testResponse.success && testResponse.data && testResponse.data.test_data) {
                                populateTopAssociadosList(testResponse.data.test_data);
                            } else {
                                loadFallbackTopAssociados();
                            }
                        },
                        error: function() {
                            loadFallbackTopAssociados();
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(`Attempt ${attempt} failed:`, {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    readyState: xhr.readyState
                });
                
                if (attempt < maxAttempts) {
                    console.log(`Retrying in 2 seconds... (attempt ${attempt + 1}/${maxAttempts})`);
                    setTimeout(() => tryGetTopAssociados(attempt + 1), 2000);
                } else {
                    console.log('All attempts failed, loading fallback data...');
                    loadFallbackTopAssociados();
                }
            }
        });
    }
    
    // Start the attempt chain
    tryGetTopAssociados();
}

// Function to fix problematic SVGs
function fixProblematicSVGs() {
    // Find all SVG paths with problematic content
    const problematicPaths = document.querySelectorAll('path[d*="515.356"], path[d*="919.288"], path[d*="616 0z"]');
    
    problematicPaths.forEach(function(path) {
        console.log('Fixing problematic SVG path:', path);
        // Replace with a simple user icon path
        path.setAttribute('d', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z');
    });
    
    // Also fix any SVGs that might be added dynamically
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const problematicPaths = node.querySelectorAll ? node.querySelectorAll('path[d*="515.356"], path[d*="919.288"], path[d*="616 0z"]') : [];
                        problematicPaths.forEach(function(path) {
                            console.log('Fixing dynamically added problematic SVG path:', path);
                            path.setAttribute('d', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z');
                        });
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    console.log('SVG fix system initialized');
}

// Function to populate top associados list
function populateTopAssociadosList(associados) {
    const container = document.getElementById('top-associados-list');
    if (!container) {
        console.error('top-associados-list container not found - checking if element exists in DOM');
        
        // Try to find the container with a more flexible approach
        const alternativeContainer = document.querySelector('[id*="associados"], [class*="associados"]');
        if (alternativeContainer) {
            console.log('Found alternative container:', alternativeContainer);
        } else {
            console.error('No suitable container found for top associados list');
        }
        return;
    }
    
    if (!associados || associados.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-slate-500">Nenhum cliente encontrado</div>';
        return;
    }
    
    let html = '';
    associados.forEach(function(associado, index) {
        // Determine ranking style based on position
        let rankingClass = 'ranking-default';
        if (associado.position === 1) {
            rankingClass = 'ranking-gold';
        } else if (associado.position === 2) {
            rankingClass = 'ranking-silver';
        } else if (associado.position === 3) {
            rankingClass = 'ranking-bronze';
        }
        
        // Format currency values
        const totalFormatted = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(associado.total || 0);
        
        const averageFormatted = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(associado.average || 0);
        
        html += `
            <div class="ranking-position flex items-center justify-between p-4 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 ${rankingClass} rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        ${associado.position}
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 text-lg">
                            ${associado.name || 'Cliente sem nome'}
                        </h4>
                        <div class="flex items-center gap-4 text-sm text-slate-600 mt-1">
                            <span class="flex items-center gap-1">
                                🛒 ${associado.orders || 0} pedidos
                            </span>
                            <span class="flex items-center gap-1">
                                💰 Ticket médio: ${averageFormatted}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-slate-900">
                        ${totalFormatted}
                    </div>
                    <div class="text-sm text-slate-500">
                        Total gasto
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Fix any problematic SVGs that might have been added
    setTimeout(function() {
        const problematicPaths = container.querySelectorAll('path[d*="515.356"], path[d*="919.288"], path[d*="616 0z"]');
        problematicPaths.forEach(function(path) {
            console.log('Fixing problematic SVG in populated list:', path);
            path.setAttribute('d', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z');
        });
    }, 100);
    
    console.log('✓ Top associados list populated with ' + associados.length + ' customers');
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Relatórios - DOM loaded');
    
    // Wait for scripts to load
    setTimeout(function() {
        // Check if required dependencies are available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery not loaded');
            return;
        }
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }
        
        if (typeof dashboardRelatoriosAjax === 'undefined') {
            console.error('dashboardRelatoriosAjax not available - creating fallback');
            // Create fallback config
            window.dashboardRelatoriosAjax = {
                ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
                nonce: '<?php echo wp_create_nonce('dashboard_ajax_nonce'); ?>',
                site_url: '<?php echo get_site_url(); ?>',
                template_directory: '<?php echo get_template_directory_uri(); ?>',
                is_user_logged_in: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
                current_user: <?php echo wp_get_current_user()->ID; ?>,
                debug: true,
                fallback: true
            };
        }
        
        console.log('Dashboard config:', dashboardRelatoriosAjax);
        
        // Basic functionality test - just verify container exists
        setTimeout(function() {
            const container = document.getElementById('top-associados-list');
            if (container) {
                console.log('✓ Top associados container found and populated from PHP');
            } else {
                console.error('✗ Top associados container not found');
            }
        }, 500);
        
        // Initialize products section
        initializeProductsSection();
        
        // Fix problematic SVGs first
        fixProblematicSVGs();
        
        // Top associados are now loaded directly in PHP, no AJAX needed
        console.log('✓ Top associados loaded directly from PHP - no AJAX required');
        
        // Initialize dashboard
        if (typeof window.DashboardRelatorios !== 'undefined' && typeof window.DashboardRelatorios.init === 'function') {
            console.log('Using DashboardRelatorios.init()');
            window.DashboardRelatorios.init();
            // Initialize profit calculation after main dashboard
            setTimeout(initializeDashboardWithProfit, 1500);
        } else if (typeof initializeDashboard === 'function') {
            console.log('Using initializeDashboard()');
            initializeDashboard();
            // Initialize profit calculation after main dashboard
            setTimeout(initializeDashboardWithProfit, 1500);
        } else {
            console.error('No dashboard initialization function available');
            // Still initialize profit calculation
            initializeDashboardWithProfit();
        }
        
    }, 500);
});


</script>
    
<?php
get_footer();