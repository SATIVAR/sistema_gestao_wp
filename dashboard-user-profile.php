<?php
/**
 * Dashboard do Usuário - Template Moderno
 * Baseado no padrão SidebarProvider do
 */

// Evita o acesso direto ao arquivo.
if (!defined('ABSPATH')) {
    exit;
}

// Verifica se o usuário está logado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Obtém dados do usuário
$user_data = amedis_get_user_profile_data();
$current_user = wp_get_current_user();
$user_stats = amedis_get_user_stats();

$user_id = get_current_user_id();

// Get user activation status
$is_active = get_field('associado_ativado', 'user_' . $user_id);
if ($is_active === null) {
    $is_active = get_user_meta($user_id, 'associado_ativado', true);
}
$is_active_bool = filter_var($is_active, FILTER_VALIDATE_BOOLEAN);

// Initialize variables
$nome_completo = '';
$email = '';
$cpf = '';
$telefone = '';
$cidade = '';
$endereco = '';
$bairro = '';
$data_admissao = '';
$observacoes = '';
$associado = '';
$associado_ativado = '';
$tipo_associacao = '';
$idconjuge = '';
$idfilho01 = '';
$idfilho02 = '';
$responsavel = '';
$responsavel_atendimento = '';

// Fetch user ACF fields
$nome_completo = get_field('nome_completo', 'user_' . $user_id) ?: $current_user->display_name;
$email = get_field('email', 'user_' . $user_id) ?: $current_user->user_email;
$cpf = get_field('cpf', 'user_' . $user_id);
$telefone = get_field('telefone', 'user_' . $user_id);
$cidade = get_field('cidade', 'user_' . $user_id);
$endereco = get_field('endereco', 'user_' . $user_id);
$bairro = get_field('bairro', 'user_' . $user_id);
$data_admissao = get_field('data_admissao', 'user_' . $user_id);
$observacoes = get_field('observacoes', 'user_' . $user_id);
$associado = get_field('associado', 'user_' . $user_id);
$associado_ativado = get_field('associado_ativado', 'user_' . $user_id);
$tipo_associacao = get_field('tipo_associacao', 'user_' . $user_id);
$nome_completo_respon = get_field('nome_completo_respon', 'user_' . $user_id);
$data_nascimento_respon = get_field('data_nascimento_respon', 'user_' . $user_id);

$responsible_types = ['assoc_respon', 'assoc_tutor'];
$responsible_data = [];

if (in_array($tipo_associacao, $responsible_types, true)) {
    // Usando os nomes de campos corretos (provavelmente)
    $responsible_data['nome'] = get_field('nome_completo_respon', 'user_' . $user_id) ?: '';
    $responsible_data['data_nascimento'] = get_field('data_nascimento_respon', 'user_' . $user_id) ?: '';
    $responsible_data['cpf'] = get_field('cpf_responsavel', 'user_' . $user_id) ?: ''; // Verifique este nome no ACF
    $responsible_data['rg'] = get_field('rg_responsavel', 'user_' . $user_id) ?: '';    // Verifique este nome no ACF
    $responsible_data['profissao'] = get_field('profissao_responsavel', 'user_' . $user_id) ?: ''; // Verifique este nome no ACF
} else {
    $responsible_data = [];
}

$tipo_associacao_info = [
    'text' => 'Não definido',
    'bg_class' => 'bg-gray-100',
    'text_class' => 'text-gray-600',
    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
];

switch ($tipo_associacao) {
    case 'assoc_paciente':
        $tipo_associacao_info = [
            'text' => 'Paciente',
            'bg_class' => 'bg-blue-100',
            'text_class' => 'text-blue-600',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        ];
        break;
    case 'assoc_respon':
        $tipo_associacao_info = [
            'text' => 'Responsável pelo Paciente',
            'bg_class' => 'bg-purple-100',
            'text_class' => 'text-purple-600',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>'
        ];
        break;
    case 'assoc_tutor':
        $tipo_associacao_info = [
            'text' => 'Tutor de Animal',
            'bg_class' => 'bg-green-100',
            'text_class' => 'text-green-600',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>'
        ];
        break;
    case 'assoc_colab':
        $tipo_associacao_info = [
            'text' => 'Colaborador',
            'bg_class' => 'bg-yellow-100',
            'text_class' => 'text-yellow-600',
            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        ];
        break;
}

// Obtém os 3 documentos ACF do usuário para o checklist
$has_endereco = false;
$has_rg = false;
$has_termo = false;

if (function_exists('get_field')) {
    $comprova_end = get_field('comprova_end_paciente', 'user_'.$user_id);
    $comprova_rg = get_field('comprova_rg_paciente', 'user_'.$user_id);
    $termo = get_field('termo_associativo', 'user_'.$user_id);

    $has_endereco = !empty($comprova_end);
    $has_rg = !empty($comprova_rg);
    $has_termo = !empty($termo);
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - <?php bloginfo('name'); ?></title>
    
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/dashboard-user-profile.css">
    
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.css">
   
    <?php wp_head(); ?>
    
    <!-- Ensure jQuery is loaded -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Cropper.js JavaScript -->
    <script src="https://unpkg.com/cropperjs/dist/cropper.js"></script>
    
    <!-- Localize script for AJAX -->
    <script>
        var userProfileAjax = {
            ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('user_profile_nonce'); ?>'
        };
    </script>
    
    <!-- Print Styles for Invoice -->
    <style>
        html, body {
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar-provider {
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar-inset {
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .main-header {
            flex-shrink: 0;
        }
        
        main {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Enhanced Error Handling Styles */
        .password-notification {
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Field validation styles */
        .field-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }
        
        .field-success {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .success-message {
            color: #10b981;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        /* Loading state styles */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        /* Password strength indicator */
        .password-strength {
            margin-top: 0.5rem;
        }
        
        .password-strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .password-strength-weak {
            background: linear-gradient(to right, #ef4444 30%, #f3f4f6 30%);
        }
        
        .password-strength-medium {
            background: linear-gradient(to right, #f59e0b 60%, #f3f4f6 60%);
        }
        
        .password-strength-strong {
            background: #10b981;
        }
        
        /* Network error indicator */
        .network-error-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ef4444;
            color: white;
            text-align: center;
            padding: 0.5rem;
            font-size: 0.875rem;
            z-index: 9999;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }
        
        .network-error-indicator.show {
            transform: translateY(0);
        }
        
        /* Retry button styles */
        .retry-button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: background 0.2s ease;
        }
        
        .retry-button:hover {
            background: #2563eb;
        }
        
        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus states for better accessibility */
        .password-notification button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        @media print {
            body * {
                display: none !important;
            }
            #print-invoice, #print-invoice * {
                display: block !important;
                visibility: visible !important;
            }
            #print-invoice {
                position: static !important;
                font-family: Arial, sans-serif !important;
                color: #000 !important;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                width: 100% !important;
                max-width: none !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
            }
        }
    </style>
</head>

<body>
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay" data-state="closed"></div>
    
    <!-- Sidebar Provider -->
    <div class="sidebar-provider" data-state="expanded">
        <!-- Sidebar -->
        <aside class="sidebar" data-state="closed">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="flex items-center gap-3">


<?php
$logo_url = hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes');

if (!empty($logo_url)) {
    $imagem_final = $logo_url;
} else {
    // Concatena a URL do tema com o caminho relativo da imagem
    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_hori.png';
}
?>
<img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="h-10">                     
                </div>
                <button id="sidebar-close" class="md:hidden p-1 rounded-lg hover:bg-sidebar-accent transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Sidebar Content -->
            <div class="sidebar-content custom-scrollbar">
               
                <!-- Main Navigation -->
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Principal</div>
                    <div class="sidebar-menu">
                        <button class="sidebar-menu-button" data-target="dashboard" data-active="true">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4m8-4v4"/>
                            </svg>
                            <span>Dashboard</span>
                        </button>
                        
                        <button class="sidebar-menu-button" data-target="profile">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Meu Perfil</span>
                        </button>
                        
                        <button class="sidebar-menu-button" data-target="orders">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Meus Pedidos</span>
                        </button>
                        
                        <button class="sidebar-menu-button" data-target="receitas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Minhas Receitas</span>
                        </button>
                    </div>
                </div>
                
                <!-- Secondary Navigation -->
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Configurações</div>
                    <div class="sidebar-menu">
                        <button class="sidebar-menu- hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Configurações</span>
                        </button>
                        
                        <button class="sidebar-menu-button" id="logout-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-sidebar-accent transition-colors">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 text-primary-foreground font-medium">
                        <?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-sidebar-foreground truncate"><?php echo esc_html($current_user->display_name); ?></p>
                        <p class="text-xs text-sidebar-foreground/70 truncate"><?php echo esc_html($current_user->user_email); ?></p>
                    </div>

                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="sidebar-inset">
            <!-- Header - Modern Navbar Design -->
            <header class="main-header bg-background border-b border-border shadow-sm">
                <div class="flex h-14 items-center px-2 md:px-4">
                    <button id="sidebar-toggle" class="md:hidden mr-4 p-2 rounded-md hover:bg-accent transition-colors focus-ring">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span class="sr-only">Menu</span>
                    </button>
                    <span class="display-inline sm:hidden">
                        <img src="<?php echo hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes'); ?>" class="h-10" alt="Logo ASSOC">
                    </span>
                    
                    <div class="flex flex-1 items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="hidden md:flex items-center gap-2 text-sm text-muted-foreground">
                                <span>Bem-vindo,</span>
                                <span class="font-medium text-foreground"><?php echo esc_html($current_user->display_name); ?></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <!-- Notifications -->
                            <div class="relative">
    <button id="notification-btn" class="relative inline-flex h-9 w-9 items-center justify-center rounded-md border border-border bg-background text-sm font-medium shadow-sm transition-colors hover:bg-accent focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" aria-haspopup="true" aria-expanded="false">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
</svg>
        <span id="notification-badge" class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full hidden"></span>
        <span class="sr-only">Notificações</span>
    </button>
    <!-- Painel de Notificações -->
    <div id="notification-panel" class="hidden absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-background border border-border shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 animate-fade-in">
        <div class="py-2 px-4 border-b border-border flex items-center justify-between">
            <span class="font-semibold text-sm">Notificações</span>
            <button id="mark-all-read" class="text-xs text-primary hover:underline">Marcar todas como lidas</button>
        </div>
        <div id="notification-list" class="max-h-72 overflow-y-auto divide-y divide-border">
            <div class="p-4 text-center text-muted-foreground text-sm">Carregando...</div>
        </div>
    </div>
</div>
                            

                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 p-4 lg:p-6">          
      <!-- Dashboard Section -->
                <div id="dashboard" class="content-section animate-fade-in">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
                        <div class="card p-6 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Total de Pedidos</p>
                                    <p class="text-2xl font-bold text-foreground"><?php echo esc_html($user_stats['total_orders']); ?></p>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <?php if ($user_stats['total_orders'] > 0): ?>
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    Ativo
                                </span>
                                <span class="text-muted-foreground">pedidos realizados</span>
                                <?php else: ?>
                                <span class="text-muted-foreground">Nenhum pedido ainda</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card p-6 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Receitas Ativas</p>
                                    <p class="text-2xl font-bold text-foreground"><?php echo esc_html($user_stats['active_prescriptions']); ?></p>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <?php if ($user_stats['active_prescriptions'] > 0): ?>
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Ativas
                                </span>
                                <span class="text-muted-foreground">receitas válidas</span>
                                <?php else: ?>
                                <span class="text-muted-foreground">Nenhuma receita ativa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card p-6 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Valor Total</p>
                                    <p class="text-2xl font-bold text-foreground"><?php echo wp_kses_post($user_stats['total_value_formatted']); ?></p>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <?php if ($user_stats['total_value'] > 0): ?>
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                                    </svg>
                                    Total gasto
                                </span>
                                <span class="text-muted-foreground">em pedidos</span>
                                <?php else: ?>
                                <span class="text-muted-foreground">Nenhuma compra ainda</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card p-6 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Status da Conta</p>
                                    <p class="text-2xl font-bold text-foreground"><?php echo $is_active_bool ? 'Ativa' : 'Inativa'; ?></p>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg <?php echo $is_active_bool ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'; ?>">
                                    <?php if ($is_active_bool): ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php else: ?>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <span class="status-dot <?php echo $is_active_bool ? 'status-active' : 'status-inactive'; ?>"></span>
                                <span class="text-muted-foreground">
                                    <?php if ($is_active_bool): ?>
                                        Conta verificada
                                    <?php else: ?>
                                        Conta inativa. <button onclick="openInactiveAccountModal()" class="text-primary hover:text-primary/80 underline cursor-pointer">Saiba Mais</button>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Recent Orders -->
                        <div class="card">
                            <div class="flex items-center justify-between p-6 border-b border-border">
                                <h3 class="text-lg font-semibold text-foreground">Pedidos Recentes</h3>
                                <button class="text-sm font-medium text-primary hover:text-primary/80 transition-colors" data-target="orders">
                                    Ver todos
                                </button>
                            </div>
                            <div class="p-6">
                                <div id="recent-orders-list" class="space-y-4">
                                    <!-- Os pedidos recentes serão carregados aqui via JavaScript -->
                                    <div class="flex items-center justify-center py-4">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Prescriptions -->
                        <div class="card">
                            <div class="flex items-center justify-between p-6 border-b border-border">
                                <h3 class="text-lg font-semibold text-foreground">Receitas Recentes</h3>
                                <button class="text-sm font-medium text-primary hover:text-primary/80 transition-colors" data-target="receitas">
                                    Ver todas
                                </button>
                            </div>
                            <div class="p-6">
                                <div id="recent-receitas-list">
                                    <div class="flex items-center justify-center py-4">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
               
                <!-- Profile Section -->
                <div id="profile" class="content-section hidden">
                    <div class="max-w-6xl mx-auto">
                        <div class="mb-6">
                            <h2 class="text-2xl font-semibold text-foreground">Meu Perfil</h2>
                            <p class="text-sm text-muted-foreground mt-1">Gerencie suas informações pessoais e configurações da conta</p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Avatar Card -->
                            <div class="lg:col-span-1">
                                <div class="card hover-lift profile-card">
                                    <div class="p-6">
                                        <div class="flex flex-col items-center text-center space-y-4">
                                            <!-- Avatar Container -->
                                            <div class="avatar-container">
                                                <div class="relative">
                                                    <!-- Main Avatar -->
                                                    <div id="avatar-display" class="flex h-24 w-24 items-center justify-center rounded-full avatar-gradient text-primary-foreground text-2xl font-bold shadow-lg ring-4 ring-background overflow-hidden">
                                                        <?php 
                                                        $avatar_id = get_user_meta($user_id, 'custom_avatar', true);
                                                        if ($avatar_id && wp_attachment_is_image($avatar_id)) {
                                                            $avatar_url = wp_get_attachment_image_url($avatar_id, 'thumbnail');
                                                            echo '<img src="' . esc_url($avatar_url) . '" alt="Avatar" class="w-full h-full object-cover">';
                                                        } else {
                                                            echo strtoupper(substr($current_user->display_name, 0, 2));
                                                        }
                                                        ?>
                                                    </div>
                                                    
                                                    <!-- Upload Overlay -->
                                                    <div class="avatar-upload-overlay" onclick="document.getElementById('avatar-upload').click()">
                                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                    </div>
                                                    
                                                    <!-- Status Indicator -->
                                                    <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-green-500 border-2 border-background flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                <!-- Hidden File Input -->
                                                <input type="file" id="avatar-upload" name="avatar_file" class="hidden" accept="image/jpeg,image/png,image/webp">
                                            </div>
                                            
                                            <!-- User Info -->
                                            <div class="space-y-1">
                                                <h3 class="text-lg font-semibold text-foreground"><?php echo esc_html($current_user->display_name); ?></h3>
                                                <p class="text-sm text-muted-foreground"><?php echo esc_html($current_user->user_email); ?></p>
                                                <!-- Upload Button -->
                                                <button type="button" onclick="document.getElementById('avatar-upload').click()" class="btn bg-slate-300 w-full">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Alterar Foto
                                                </button>
                                                
                                                <!-- Save Avatar Button (hidden initially) -->
                                                <button type="button" id="save-avatar-btn" class="btn btn-primary w-full mt-2 hidden">
                                                    <span class="spinner hidden">
                                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                    <span class="button-text">Salvar Avatar</span>
                                                </button>
                                                
                                                <!-- Remove Avatar Button (shown only when avatar exists) -->
                                                <?php if ($avatar_id && wp_attachment_is_image($avatar_id)): ?>
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
                                                <?php endif; ?>                                                
                                                <div class="border-t border-slate-100 py-4 mt-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-muted-foreground text-left">Status da Conta</p>
                                                            <span class="text-muted-foreground">
                                                                <?php if ($is_active_bool): ?>
                                                                    Conta verificada
                                                                <?php else: ?>
                                                                    Conta inativa. <button onclick="openInactiveAccountModal()" class="text-primary hover:text-primary/80 underline cursor-pointer">Saiba Mais</button>
                                                                <?php endif; ?>
                                                            </span>                                                            
                                                        </div>
                                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg <?php echo $is_active_bool ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'; ?>">
                                                            <?php if ($is_active_bool): ?>
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <?php else: ?>
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        </div>
                                    </div>
                                    
                                    <!-- Stats Section -->
                                    <div class="border-t border-border p-6">
                                        <div class="grid grid-cols-2 gap-4 text-center">
                                            <div class="space-y-1">
                                                <p class="stat-number text-2xl font-bold text-foreground cursor-pointer"><?php echo esc_html($user_stats['total_orders']); ?></p>
                                                <p class="text-xs text-muted-foreground">Pedidos</p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="stat-number text-2xl font-bold text-foreground cursor-pointer"><?php echo esc_html($user_stats['active_prescriptions']); ?></p>
                                                <p class="text-xs text-muted-foreground">Receitas</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="border-t border-border p-6 space-y-3">
                                        <h4 class="text-sm font-medium text-foreground mb-3">Ações Rápidas</h4>
                                        <div class="space-y-2">
                                            <button data-quick-action="new-order" class="quick-action-btn w-full flex items-center gap-3 p-2 rounded-lg text-left focus-ring">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Novo Pedido</p>
                                                    <p class="text-xs text-muted-foreground">Criar pedido rápido</p>
                                                </div>
                                            </button>
                                            
                                            <button data-quick-action="new-receita" class="quick-action-btn w-full flex items-center gap-3 p-2 rounded-lg text-left focus-ring">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Nova Receita</p>
                                                    <p class="text-xs text-muted-foreground">Enviar receita médica</p>
                                                </div>
                                            </button>
                                            
                                            <button data-quick-action="support" class="quick-action-btn w-full flex items-center gap-3 p-2 rounded-lg text-left focus-ring">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Suporte</p>
                                                    <p class="text-xs text-muted-foreground">Precisa de ajuda?</p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Form -->
                            <div class="lg:col-span-2">
                                <div class="card">
                                    <!-- Seção de Dados do Usuário - Somente Leitura -->
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-foreground">Dados do Paciente</h3>
                                            <!-- Badge do Tipo de Associação -->
                                            <div class="flex items-center gap-2 px-3 py-1 rounded-full <?php echo $tipo_associacao_info['bg_class']; ?> <?php echo $tipo_associacao_info['text_class']; ?>">
                                                <?php echo $tipo_associacao_info['icon']; ?>
                                                <span class="text-sm font-medium"><?php echo $tipo_associacao_info['text']; ?></span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <!-- Nome Completo -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Nome Completo</p>
                                                    <p class="text-xs text-muted-foreground"><?php echo esc_html($nome_completo ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>

                                            <!-- E-mail -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">E-mail</p>
                                                    <p class="text-xs text-muted-foreground"><?php echo esc_html($email ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>

                                            <!-- CPF -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">CPF</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($cpf ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>

                                            <!-- Telefone -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Telefone</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($telefone ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>

                                            <!-- Cidade -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Cidade</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($cidade ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>

                                            <!-- Data de Admissão -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 text-teal-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Data de Admissão</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($data_admissao ?: 'Não informado'); ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Endereço Completo -->
                                        <?php if ($endereco || $bairro): ?>
                                        <div class="mt-4 pt-4 border-t border-border">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Endereço</p>
                                                    <p class="text-sm text-muted-foreground">
                                                        <?php
                                                        $endereco_completo = [];
                                                        if ($endereco) $endereco_completo[] = $endereco;
                                                        if ($bairro) $endereco_completo[] = $bairro;
                                                        if ($cidade) $endereco_completo[] = $cidade;
                                                        echo esc_html(implode(', ', $endereco_completo) ?: 'Não informado');
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Seção de Dados do Responsável/Tutor (quando aplicável) -->
                                    <?php if (in_array($tipo_associacao, $responsible_types, true) && !empty($responsible_data['nome'])) : ?>
                                    <div class="p-6 border-y border-border">
                                        <h3 class="text-lg font-semibold text-foreground">Dados do Responsável/Tutor</h3>
                                    </div>                                    
                                    <div class="p-6 border-b border-border">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <?php if (!empty($responsible_data['nome'])) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Nome</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($responsible_data['nome']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($responsible_data['data_nascimento'])) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Data de Nascimento</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($responsible_data['data_nascimento']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($responsible_data['cpf'])) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">CPF</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($responsible_data['cpf']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($responsible_data['rg'])) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h2M14 10v4m0-4v-2a4 4 0 00-8 0v4m0 0h2m-2 0v2a4 4 0 004 4 4 4 0 004-4v-2m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">RG</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($responsible_data['rg']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($responsible_data['profissao'])) : ?>
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 00-8 0v1a7 7 0 007 7h3"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-foreground">Profissão</p>
                                                    <p class="text-sm text-muted-foreground"><?php echo esc_html($responsible_data['profissao']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                           

                                    <!-- Formulário de Alteração de Senha -->
                                    <div class="p-6 border-b border-border">
                                        <h3 class="text-lg font-semibold text-foreground">Alterar Senha</h3>
                                    </div>
                                    <div class="p-6 border-b border-border">
                                        <form id="user-profile-form" class="space-y-4" method="post">
                                            <!-- Enhanced UI Feedback Area -->
                                            <div id="password-form-messages" class="hidden">
                                                <div id="password-success-message" class="hidden p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span id="password-success-text"></span>
                                                    </div>
                                                </div>
                                                <div id="password-error-message" class="hidden p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span id="password-error-text"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label class="text-sm font-medium text-foreground" for="password">Nova Senha</label>
                                                    <div class="relative">
                                                        <input
                                                            class="form-input w-full pr-10 focus:bg-white text-slate-700"
                                                            id="password"
                                                            name="password"
                                                            type="password"
                                                            placeholder="Digite a nova senha"
                                                            autocomplete="new-password"
                                                        >
                                                    </div>
                                                    <p class="text-xs text-muted-foreground">Deixe em branco para manter a senha atual</p>
                                                </div>

                                                <div class="space-y-2">
                                                    <label class="text-sm font-medium text-foreground" for="password_confirm">Repetir Nova Senha</label>
                                                    <div class="relative">
                                                        <input
                                                            class="form-input w-full pr-10 focus:bg-white text-slate-700"
                                                            id="password_confirm"
                                                            name="password_confirm"
                                                            type="password"
                                                            placeholder="Repita a nova senha"
                                                            autocomplete="new-password"
                                                        >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex gap-3 pt-4">
                                                <button type="submit" class="btn btn-primary">
                                                    <span class="spinner hidden">
                                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                    <span class="button-text">Salvar Alterações</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Orders Section -->
                <div id="orders" class="content-section hidden">
                    <div class="card">
                        <div class="flex flex-col gap-4 p-6 border-b border-border md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-foreground">Meus Pedidos</h2>
                                <p class="text-sm text-muted-foreground mt-1">Acompanhe o status dos seus pedidos</p>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <select id="order-status-filter" class="form-input text-sm focus-ring">
                                    <option value="">Todos os status</option>
                                    <option value="pending">Pendente</option>
                                    <option value="processing">Processando</option>
                                    <option value="completed">Concluído</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-tbody">
                                    <!-- Orders will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex items-center justify-between px-6 py-4 border-t border-border">
                            <div class="text-sm text-muted-foreground">
                                <span id="orders-info">Carregando...</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="prev-page" class="btn btn-ghost">
                                    Anterior
                                </button>
                                <span class="px-3 py-2 text-sm font-medium text-foreground">
                                    Página <span id="page-info">1</span>
                                </span>
                                <button id="next-page" class="btn btn-ghost">
                                    Próxima
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Receitas Section -->
                <div id="receitas" class="content-section hidden">
                    <div class="card">
                        <div class="flex flex-col gap-4 p-6 border-b border-border md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-foreground">Minhas Receitas</h2>
                                <p class="text-sm text-muted-foreground mt-1">Gerencie suas receitas médicas</p>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <select id="receitas-status-filter" class="form-input text-sm focus-ring">
                                    <option value="">Todas as receitas</option>
                                    <option value="ativa">Ativas</option>
                                    <option value="expirando">Expirando</option>
                                    <option value="expirada">Expiradas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div id="receitas-grid" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <!-- Receitas will be loaded here via AJAX -->
                            </div>
                            
                            <div class="flex items-center justify-between mt-6 pt-6 border-t border-border">
                                <div class="text-sm text-muted-foreground">
                                    <span id="receitas-info">Carregando...</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button id="receitas-prev-page" class="btn btn-ghost">
                                        Anterior
                                    </button>
                                    <span class="px-3 py-2 text-sm font-medium text-foreground">
                                        Página <span id="receitas-page-info">1</span>
                                    </span>
                                    <button id="receitas-next-page" class="btn btn-ghost">
                                        Próxima
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>    

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeLogoutModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-card rounded-xl border border-border shadow-soft p-6 w-full max-w-md bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-foreground">Confirmar Logout</h3>
                    <button onclick="closeLogoutModal()" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-6">
                    <p class="text-sm text-muted-foreground">Você será deslogado e a página será recarregada. Tem certeza que deseja continuar?</p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button onclick="closeLogoutModal()" class="btn btn-ghost">
                        Cancelar
                    </button>
                    <button id="logout-confirm" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sim, sair.
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="order-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeOrderModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="bg-card rounded-xl border border-border shadow-soft overflow-hidden bg-white w-full max-w-[800px] max-h-[85vh]">
                <div class="flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between p-6 border-b border-border">
                        <div>
                            <h3 id="modal-order-title" class="text-lg font-semibold text-foreground">Detalhes do Pedido</h3>
                            <p id="modal-order-subtitle" class="text-sm text-muted-foreground"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="modal-print-btn" class="btn btn-ghost hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Imprimir
                            </button>
                            <button onclick="closeOrderModal()" class="btn btn-ghost">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="modal-order-content" class="flex-1 p-6 overflow-y-auto custom-scrollbar">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Avatar Crop Modal -->
    <div id="avatar-crop-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-card rounded-xl border border-border shadow-soft p-6 w-full max-w-lg bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-foreground">Ajustar Avatar</h3>
                    <button id="crop-modal-close" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-6">
                    <div class="mb-4">
                        <img id="crop-image" class="max-w-full" style="max-height: 300px;">
                    </div>
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm text-muted-foreground mb-2">Pré-visualização (150x150)</p>
                            <div id="crop-preview" class="w-24 h-24 rounded-full overflow-hidden border-2 border-border mx-auto"></div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button id="crop-cancel" class="btn btn-ghost">
                        Cancelar
                    </button>
                    <button id="crop-confirm" class="btn btn-primary">
                        <span class="spinner hidden">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span class="button-text">Confirmar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive Account Modal -->
    <div id="inactive-account-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeInactiveAccountModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-card rounded-xl border border-border shadow-soft p-6 w-full max-w-lg bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-foreground">🔒 Sua conta está inativa</h3>
                    <button onclick="closeInactiveAccountModal()" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-6 space-y-4">
                    <p class="text-sm text-muted-foreground">
                        Para ativá-la, faltam os documentos necessários.
                    </p>
                    
                    <!-- Checklist de Documentos -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-foreground mb-3">Checklist de Documentos</h4>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 text-sm">
                                <?php if ($has_endereco): ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-foreground">Comprovante de Endereço</span>
                                <?php else: ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-muted-foreground">Comprovante de Endereço <span class="text-yellow-600">(Pendente)</span></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center gap-3 text-sm">
                                <?php if ($has_rg): ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-foreground">RG</span>
                                <?php else: ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-muted-foreground">RG <span class="text-yellow-600">(Pendente)</span></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center gap-3 text-sm">
                                <?php if ($has_termo): ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-foreground">Termo Associativo</span>
                                <?php else: ?>
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-muted-foreground">Termo Associativo <span class="text-yellow-600">(Pendente)</span></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!$has_termo): ?>
                    <!-- Card de Download do Termo Associativo -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-sm font-medium text-blue-900 mb-1">Termo Associativo Necessário</h5>
                                <p class="text-xs text-blue-700 mb-3">
                                    Baixe, preencha e assine o termo associativo para completar sua documentação.
                                </p>
                                <a href="/docs/amedis_termo_associativo.doc" download class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Baixar Termo Associativo
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-sm text-muted-foreground">
                        🟢 Clique no botão do WhatsApp abaixo para abrir o atendimento da associação e enviar todos os arquivos.
                    </p>
                    <p class="text-sm text-muted-foreground">
                        Assim que recebermos tudo, sua conta será verificada e ativa automaticamente.
                    </p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button onclick="closeInactiveAccountModal()" class="btn btn-ghost">
                        Cancelar
                    </button>
                    <a href="https://api.whatsapp.com/send?phone=5585988063030&text=Olá%20preciso%20ativar%20minha%20conta" target="_blank" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                        </svg>
                        Atendimento via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar functionality
        const sidebarProvider = document.querySelector('.sidebar-provider');
        const sidebar = document.querySelector('.sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        
        // Mobile menu toggle
        sidebarToggle?.addEventListener('click', function() {
            sidebar.setAttribute('data-state', 'open');
            mobileOverlay.setAttribute('data-state', 'open');
            document.body.style.overflow = 'hidden';
        });
        
        sidebarClose?.addEventListener('click', function() {
            sidebar.setAttribute('data-state', 'closed');
            mobileOverlay.setAttribute('data-state', 'closed');
            document.body.style.overflow = 'auto';
        });
        
        mobileOverlay?.addEventListener('click', function() {
            sidebar.setAttribute('data-state', 'closed');
            mobileOverlay.setAttribute('data-state', 'closed');
            document.body.style.overflow = 'auto';
        });
        
        // Sidebar navigation
        document.querySelectorAll('.sidebar-menu-button[data-target]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active state from all buttons
                document.querySelectorAll('.sidebar-menu-button').forEach(btn => {
                    btn.setAttribute('data-active', 'false');
                    // Remove visual active classes
                    btn.classList.remove('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
                    btn.style.backgroundColor = '';
                    btn.style.color = '';
                });
                
                // Add active state to clicked button
                this.setAttribute('data-active', 'true');
                // Add visual active classes
                this.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
                this.style.backgroundColor = 'rgba(47,174,94, 0.1)';
                this.style.color = 'rgb(47,174,94)';
                
                // Hide all content sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.add('hidden');
                });
                
                // Show target section
                const target = this.getAttribute('data-target');
                const targetSection = document.getElementById(target);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                    targetSection.classList.add('animate-fade-in');
                }
                
                // Close mobile menu
                if (window.innerWidth < 768) {
                    sidebar.setAttribute('data-state', 'closed');
                    mobileOverlay.setAttribute('data-state', 'closed');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Quick navigation buttons
        document.querySelectorAll('button[data-target]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Find corresponding sidebar button and trigger click
                const target = this.getAttribute('data-target');
                const sidebarButton = document.querySelector(`.sidebar-menu-button[data-target="${target}"]`);
                if (sidebarButton) {
                    sidebarButton.click();
                }
            });
        });
        
        // Password visibility toggle
        document.querySelectorAll('button[type="button"]').forEach(button => {
            if (button.closest('.relative') && button.querySelector('svg')) {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const icon = this.querySelector('svg');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
                    } else {
                        input.type = 'password';
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                    }
                });
            }
        });
        
        // Avatar upload functionality
        document.getElementById('avatar-upload')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Por favor, selecione apenas arquivos de imagem.');
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('O arquivo deve ter no máximo 5MB.');
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Here you would typically upload to server
                    // For now, we'll just show a success message
                    console.log('Avatar upload would happen here');
                    
                    // Show success feedback
                    const statusIndicator = document.querySelector('.status-dot');
                    if (statusIndicator) {
                        statusIndicator.classList.remove('status-active');
                        statusIndicator.classList.add('status-pending');
                        
                        // Reset after 2 seconds
                        setTimeout(() => {
                            statusIndicator.classList.remove('status-pending');
                            statusIndicator.classList.add('status-active');
                        }, 2000);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Quick actions functionality
        document.querySelectorAll('[data-quick-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-quick-action');
                
                switch(action) {
                    case 'new-order':
                        // Navigate to orders section
                        document.querySelector('.sidebar-menu-button[data-target="orders"]')?.click();
                        break;
                    case 'new-receita':
                        // Navigate to receitas section
                        document.querySelector('.sidebar-menu-button[data-target="receitas"]')?.click();
                        break;
                    case 'support':
                        // Open support modal or redirect
                        console.log('Open support');
                        break;
                }
            });
        });
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Dashboard initialization - sections will be handled by dashboard-user-profile.js
            console.log('Dashboard initialized');
            
            // Set initial active state for dashboard button
            const dashboardBtn = document.querySelector('.sidebar-menu-button[data-target="dashboard"]');
            if (dashboardBtn && dashboardBtn.getAttribute('data-active') === 'true') {
                dashboardBtn.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
                dashboardBtn.style.backgroundColor = 'rgba(47,174,94, 0.1)';
                dashboardBtn.style.color = 'rgb(47,174,94)';
            }
        });
    </script>
<script>
// Funções globais para o modal
function openOrderModal(orderId) {
    const modal = document.getElementById('order-modal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Load order details via existing AJAX functionality
    if (typeof userProfileAjax !== 'undefined' && typeof jQuery !== 'undefined') {
        jQuery.ajax({
            url: userProfileAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_order_details',
                security: userProfileAjax.nonce,
                order_id: orderId
            },
            beforeSend: function() {
                document.getElementById('modal-order-content').innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>
                `;
            },
            success: function(response) {
                if (response.success) {
                    displayOrderDetails(response.data);
                } else {
                    document.getElementById('modal-order-content').innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Erro ao carregar detalhes: ${response.data.message || 'Erro desconhecido'}
                        </div>
                    `;
                }
            },
            error: function() {
                document.getElementById('modal-order-content').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        Erro de comunicação. Tente novamente.
                    </div>
                `;
            }
        });
    } else {
        // Fallback se AJAX não estiver disponível
        document.getElementById('modal-order-content').innerHTML = `
            <div class="text-center py-8 text-muted-foreground">
                Carregando detalhes do pedido...
            </div>
        `;
    }
}

function closeOrderModal() {
    const modal = document.getElementById('order-modal');
    if (!modal) return;
    
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function displayOrderDetails(order) {
    window.__lastOrderDetails = order;

    document.getElementById('modal-order-title').textContent = `Pedido #${order.number}`;
    document.getElementById('modal-order-subtitle').textContent = `${order.status_name} • ${order.date}`;

    let itemsHtml = '';
    let itemsTableHtml = '';

    if (order.items && order.items.length > 0) {
        // Screen version - cards with premium pastel design
        order.items.forEach(function(item) {
            itemsHtml += `
                <div class="bg-white/80 border border-slate-200 rounded-lg p-4 hover:bg-slate-50/50 transition-colors duration-200">
                    <div class="flex items-center gap-4">
                        ${item.image ? `<img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-lg object-cover border border-slate-100">` : `
                            <div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 rounded-lg flex items-center justify-center border border-slate-200">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        `}
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-800 mb-1">${item.name}</h4>
                            <p class="text-sm text-slate-600 mb-1">Quantidade: <span class="font-medium text-slate-700">${item.quantity}</span></p>
                            <p class="text-sm font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-md inline-block">${item.price}</p>
                        </div>
                    </div>
                </div>
            `;
        });

        // Print version - table
        order.items.forEach(function(item) {
            const subtotal = item.subtotal || item.price || 'R$ 0,00';

            itemsTableHtml += `
                <tr>
                    <td style="border: 1px solid #ccc; padding: 6px;">${item.name}</td>
                    <td style="border: 1px solid #ccc; padding: 6px; text-align: center;">${item.quantity}</td>
                    <td style="border: 1px solid #ccc; padding: 6px; text-align: right;">${item.price || 'R$ 0,00'}</td>
                    <td style="border: 1px solid #ccc; padding: 6px; text-align: right;">${subtotal}</td>
                </tr>
            `;
        });
    } else {
        itemsHtml = '<p class="text-sm text-muted-foreground">Nenhum item encontrado</p>';
        itemsTableHtml = '<tr><td colspan="4" style="padding: 12px; text-align: center; color: #888;">Nenhum item encontrado</td></tr>';
    }

    // --- PRINT INVOICE HTML ---
    let printInvoiceHtml = `
<div id="print-invoice" style="font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0;">

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px; vertical-align: top;">
                <!-- CABEÇALHO -->
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- Coluna Esquerda: Logo e Informações da Associação -->
                        <td style="width: 60%; vertical-align: top;">
                            <img src="${window.printLogoUrl || window.dynamicLogoUrl || '/wp-content/uploads/2023/11/logo-horizontal.png'}" alt="Logo AMEDIS" style="height: 40px; max-width: 180px; margin-bottom: 10px;">
                            <p style="margin: 0; font-size: 11px; line-height: 1.4;">
                                <strong>AMEDIS - ASSOCIAÇÃO MEDICINAL DE DIREITO À SAÚDE</strong><br>
                                CNPJ: 60.737.287/0001-69
                            </p>
                        </td>
                        <!-- Coluna Direita: Informações do Recibo -->
                        <td style="width: 40%; vertical-align: top; text-align: right;">
                            <h1 style="font-size: 24px; margin: 0 0 8px 0; color: #000;">RECIBO</h1>
                            <p style="margin: 0; font-size: 11px;"><strong>Pedido:</strong> #${order.number}</p>
                            <p style="margin: 2px 0 0 0; font-size: 11px;"><strong>Data:</strong> ${order.date}</p>
                            <p style="margin: 2px 0 0 0; font-size: 11px;"><strong>Status:</strong> ${order.status_name}</p>
                            <p style="margin: 2px 0 0 0; font-size: 11px;"><strong>Pagamento:</strong> ${order.payment_method || 'N/A'}</p>
                        </td>
                    </tr>
                </table>

                <div style="height: 20px;"></div> <!-- Espaçamento -->

                <!-- SEÇÃO DE PRODUTOS -->
                <h2 style="font-size: 13px; text-transform: uppercase; margin: 0 0 10px 0; padding-bottom: 5px; border-bottom: 1px solid #eee;">Produtos</h2>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f9f9f9;">
                            <th style="padding: 8px 5px; text-align: left; border-bottom: 1px solid #ddd;">Produto</th>
                            <th style="padding: 8px 5px; text-align: center; border-bottom: 1px solid #ddd; width: 60px;">Qtd</th>
                            <th style="padding: 8px 5px; text-align: right; border-bottom: 1px solid #ddd; width: 80px;">Preço Unit.</th>
                            <th style="padding: 8px 5px; text-align: right; border-bottom: 1px solid #ddd; width: 80px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsTableHtml}
                    </tbody>
                </table>

                <div style="height: 20px;"></div> <!-- Espaçamento -->

                <!-- SEÇÃO DIVIDIDA: ENTREGA E FINANCEIRO -->
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- COLUNA ESQUERDA: DADOS DA ENTREGA -->
                        <td style="width: 55%; vertical-align: top; padding-right: 20px;">
                            <h3 style="font-size: 13px; text-transform: uppercase; margin: 0 0 10px 0; padding-bottom: 5px; border-bottom: 1px solid #eee;">Dados da Entrega</h3>
                            <p style="margin: 0; font-size: 11px; line-height: 1.5;">
                                ${order.shipping_address ? order.shipping_address.replace(/\n/g, '<br>') : '<span style="color: #999;">Nenhum endereço informado.</span>'}
                            </p>

                            ${(order.customer_note && order.customer_note.trim() !== '') ? `
                            <div style="margin-top: 15px;">
                                <h4 style="font-size: 12px; text-transform: uppercase; margin: 0 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #eee;">Observações</h4>
                                <p style="margin: 0; font-size: 11px; line-height: 1.5; font-style: italic;">${order.customer_note}</p>
                            </div>
                            ` : ''}
                        </td>
                        
                        <!-- COLUNA DIREITA: RESUMO FINANCEIRO -->
                        <td style="width: 45%; vertical-align: top;">
                            <h3 style="font-size: 13px; text-transform: uppercase; margin: 0 0 10px 0; padding-bottom: 5px; border-bottom: 1px solid #eee;">Resumo Financeiro</h3>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 4px 0; border-bottom: 1px solid #eee;">Subtotal</td>
                                        <td style="padding: 4px 0; text-align: right; border-bottom: 1px solid #eee;">${order.subtotal || 'R$ 0,00'}</td>
                                    </tr>
                                    ${(order.fees && order.fees.extra_cartao) ? `<tr><td style='padding: 4px 0; border-bottom: 1px solid #eee;'>Taxa Cartão</td><td style='padding: 4px 0; text-align: right; border-bottom: 1px solid #eee;'>${order.fees.extra_cartao}</td></tr>` : ''}
                                    ${(order.fees && order.fees.frete) ? `<tr><td style='padding: 4px 0; border-bottom: 1px solid #eee;'>Frete</td><td style='padding: 4px 0; text-align: right; border-bottom: 1px solid #eee;'>${order.fees.frete}</td></tr>` : (order.shipping_total && order.shipping_total !== 'Grátis' && order.shipping_total !== 'R$ 0,00' ? `<tr><td style='padding: 4px 0; border-bottom: 1px solid #eee;'>Frete</td><td style='padding: 4px 0; text-align: right; border-bottom: 1px solid #eee;'>${order.shipping_total}</td></tr>` : '')}
                                    ${(order.fees && order.fees.desconto) ? `<tr><td style='padding: 4px 0; border-bottom: 1px solid #eee; color: #d9534f;'>Desconto</td><td style='padding: 4px 0; text-align: right; border-bottom: 1px solid #eee; color: #d9534f;'>−${order.fees.desconto}</td></tr>` : ''}
                                    ${(order.tax_total && order.tax_total !== 'R$ 0,00') ? `<tr><td style='padding: 4px 0; border-bottom: 1px solid #eee;'>Impostos</td><td style='padding: 4px 0; text-align: right; border-bottom: 1px solid #eee;'>${order.tax_total}</td></tr>` : ''}
                                    
                                    <!-- TOTAL GERAL -->
                                    <tr style="font-weight: bold;">
                                        <td style="padding: 8px 0 0 0; font-size: 14px; color: #000;">Total</td>
                                        <td style="padding: 8px 0 0 0; text-align: right; font-size: 16px; color: #000;">${order.total || 'R$ 0,00'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <div style="height: 20px;"></div> <!-- Espaçamento -->

                <!-- Rodapé -->
                <div style="text-align: center; font-size: 10px; color: #888; padding-top: 10px; border-top: 1px solid #eee;">
                    <p style="margin: 0;">Em caso de dúvidas sobre este recibo, entre em contato.</p>
                    <p style="margin: 3px 0 0 0;">Agradecemos a sua preferência!</p>
                </div>
            </td>
        </tr>
    </table>

</div>
    `;

    // Adiciona o print-invoice ao DOM (fora da tela, para impressão)
    let printDiv = document.getElementById('print-invoice');
    if (printDiv) printDiv.remove();
    printDiv = document.createElement('div');
    printDiv.id = 'print-invoice';
    printDiv.style.display = 'none';
    printDiv.innerHTML = printInvoiceHtml;
    document.body.appendChild(printDiv);

    const content = `
        <!-- Screen Version - Reorganized Layout -->
        <div class="no-print space-y-6">
            <!-- Seção final em duas colunas -->
            <div class="mb-2">
                <!-- Resumo Financeiro e Taxas - Coluna Direita -->
                <div class="px-5 py-3">
                    <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Resumo do Pedido
                    </h4>
                    <div class="mb-4">
                        <!-- Informações do Pedido -->
                        <div class="space-y-4">
                            <div class="card p-4">
                                <div class="space-y-2 text-sm">
                                    <!-- Dados do Usuário -->
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Nome:</span>
                                        <span class="font-medium text-foreground">${window.userACF?.nome || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Telefone:</span>
                                        <span class="font-medium text-foreground">${window.userACF?.telefone || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">CPF:</span>
                                        <span class="font-medium text-foreground">${window.userACF?.cpf || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">E-mail:</span>
                                        <span class="font-medium text-foreground">${window.userACF?.email || 'N/A'}</span>
                                    </div>
                                    <!-- Separador visual -->
                                    <div class="border-t border-border pt-2 mt-2"></div>
                                    <!-- Dados do Pedido -->
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Status:</span>
                                        <span class="font-medium text-foreground">${order.status_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Data:</span>
                                        <span class="font-medium text-foreground">${order.date}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Método de Pagamento:</span>
                                        <span class="font-medium text-foreground">${order.payment_method || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>


            </div>




            <!-- Resumo produtos -->
            <div class="space-y-4 mb-4">
                <!-- Itens do Pedido -->
                <div class="card p-4">
                    <h4 class="font-semibold text-foreground mb-3">Itens do Pedido</h4>
                    <div class="space-y-3">${itemsHtml}</div>
                </div>
                <!-- Endereços -->
                <div class="card p-4">
                    <h4 class="font-semibold text-foreground mb-3">Endereços</h4>
                    <div class="grid grid-cols-1 gap-4">
                        ${order.shipping_address ? `<div>
                            <h5 class="text-sm font-medium text-foreground mb-2">Endereço de Entrega</h5>
                            <div class="text-sm text-muted-foreground">${order.shipping_address}</div>
                        </div>` : '<p class="text-sm text-muted-foreground">Nenhum endereço de entrega</p>'}
                    </div>
                </div>
                <div class="card p-4">
                    <h4 class="font-semibold text-foreground mb-3">Resumo Financeiro</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Subtotal:</span>
                            <span class="font-medium text-foreground">${order.subtotal || 'R$ 0,00'}</span>
                        </div>
                        ${order.fees && order.fees.extra_cartao ? `
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Valor Extra Cartão:</span>
                            <span class="font-medium text-foreground">${order.fees.extra_cartao}</span>
                        </div>
                        ` : ''}
                        ${order.fees && order.fees.frete ? `
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Frete:</span>
                            <span class="font-medium text-foreground">${order.fees.frete}</span>
                        </div>
                        ` : (order.shipping_total && order.shipping_total !== 'Grátis' && order.shipping_total !== 'R$ 0,00' ? `
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Frete:</span>
                            <span class="font-medium text-foreground">${order.shipping_total}</span>
                        </div>
                        ` : '')}
                        ${order.fees && order.fees.desconto ? `
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Desconto:</span>
                            <span class="font-medium text-foreground text-red-600">−${order.fees.desconto}</span>
                        </div>
                        ` : ''}
                        ${order.tax_total && order.tax_total !== 'R$ 0,00' ? `
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Impostos:</span>
                            <span class="font-medium text-foreground">${order.tax_total}</span>
                        </div>
                        ` : ''}
                        <div class="border-t border-border pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="font-semibold text-foreground">Total:</span>
                                <span class="font-bold text-foreground text-lg">${order.total || 'R$ 0,00'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                ${order.customer_note ? `<div class="card p-4 mb-4">
                    <h4 class="font-semibold text-foreground mb-3">Observações do Cliente</h4>
                    <p class="text-sm text-muted-foreground">${order.customer_note}</p>
                </div>` : ''}

            </div>
        </div>
    `;

    document.getElementById('modal-order-content').innerHTML = content;

    // Controle condicional do botão "Imprimir" baseado no status do pedido
    const printBtn = document.getElementById('modal-print-btn');
    if (printBtn) {
        // Normalizar o status code e status name para comparação robusta
        const statusCode = (order.status || '').toString().toLowerCase().trim();
        const statusName = (order.status_name || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();

        // Verificar se o pedido está concluído
        const isCompleted = statusCode === 'completed' ||
                           statusName.includes('concluido') ||
                           statusName.includes('concluido') ||
                           statusName.includes('finalizado');

        if (isCompleted) {
            // Mostrar o botão se o pedido estiver concluído
            printBtn.classList.remove('hidden');
            printBtn.disabled = false;
        } else {
            // Esconder o botão se o pedido não estiver concluído
            printBtn.classList.add('hidden');
            printBtn.disabled = true;
        }
    }
}

// Funções globais para o modal de logout
function openLogoutModal() {
    const modal = document.getElementById('logout-modal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Foca no botão confirmar para acessibilidade
    const confirmBtn = document.getElementById('logout-confirm');
    if (confirmBtn) {
        setTimeout(() => confirmBtn.focus(), 100);
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logout-modal');
    if (!modal) return;
    
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Redireciona para a seção dashboard
    navigateToDashboard();
}

function navigateToDashboard() {
    // Remove classes ativas de todos os botões da sidebar
    const sidebarButtons = document.querySelectorAll('.sidebar-menu-button');
    sidebarButtons.forEach(button => {
        button.setAttribute('data-active', 'false');
        // Remove visual active classes
        button.classList.remove('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
        button.style.backgroundColor = '';
        button.style.color = '';
    });
    
    // Adiciona classes ativas ao botão dashboard
    const dashboardBtn = document.querySelector('.sidebar-menu-button[data-target="dashboard"]');
    if (dashboardBtn) {
        dashboardBtn.setAttribute('data-active', 'true');
        // Add visual active classes
        dashboardBtn.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
        dashboardBtn.style.backgroundColor = 'rgba(47,174,94, 0.1)';
        dashboardBtn.style.color = 'rgb(47,174,94)';
    }
    
    // Oculta todas as seções de conteúdo
    const contentSections = document.querySelectorAll('.content-section');
    contentSections.forEach(section => {
        section.classList.add('hidden');
    });
    
    // Mostra a seção dashboard
    const dashboardSection = document.getElementById('dashboard');
    if (dashboardSection) {
        dashboardSection.classList.remove('hidden');
        dashboardSection.classList.add('animate-fade-in');
    }
    
    // Fecha o sidebar mobile se estiver aberto
    if (window.innerWidth < 768) {
        const sidebar = document.querySelector('.sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        if (sidebar) sidebar.setAttribute('data-state', 'closed');
        if (mobileOverlay) mobileOverlay.setAttribute('data-state', 'closed');
        document.body.style.overflow = 'auto';
    }
}

function confirmLogout() {
    // Desabilita o botão para evitar cliques múltiplos
    const confirmBtn = document.getElementById('logout-confirm');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Saindo...';
    }
    
    // Verifica se userProfileAjax está disponível
    if (typeof userProfileAjax === 'undefined') {
        // Fallback para redirecionamento direto
        window.location.href = '/wp-login.php?action=logout';
        return;
    }
    
    // Faz logout via AJAX
    fetch(userProfileAjax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'amedis_logout',
            security: userProfileAjax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Logout bem-sucedido, recarrega a página
            window.location.reload();
        } else {
            // Erro no logout, mostra mensagem e reabilita botão
            console.error('Erro no logout:', data.data.message);
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Confirmar Logout';
            }
            // Fallback para redirecionamento direto
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        // Em caso de erro, recarrega a página
        window.location.reload();
    });
}

// Funções globais para o modal de conta inativa
function openInactiveAccountModal() {
    const modal = document.getElementById('inactive-account-modal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus no modal para acessibilidade
    modal.focus();
}

function closeInactiveAccountModal() {
    const modal = document.getElementById('inactive-account-modal');
    if (!modal) return;
    
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event listeners para o modal
document.addEventListener('DOMContentLoaded', function() {
    // Print button functionality
    const printBtn = document.getElementById('modal-print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            // Add print mode class
            document.body.classList.add('print-mode');
            
            // Print the page
            window.print();
            
            // Remove print mode class after printing
            window.addEventListener('afterprint', function() {
                document.body.classList.remove('print-mode');
            }, { once: true });
        });
    }
    
    // Logout button functionality
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openLogoutModal();
        });
    }
    
    // Logout confirm button functionality
    const logoutConfirmBtn = document.getElementById('logout-confirm');
    if (logoutConfirmBtn) {
        logoutConfirmBtn.addEventListener('click', function() {
            confirmLogout();
        });
    }
    
    // ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeOrderModal();
            closeLogoutModal();
            closeInactiveAccountModal();
        }
    });
});
</script>
    <!-- Dashboard JavaScript is loaded via wp_enqueue_script -->

    <!-- User ACF Data for Modal -->
    <script>
        window.printLogoUrl = '<?php echo esc_js(hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes')); ?>';
        window.userACF = {
            nome: '<?php echo esc_js($current_user->display_name ?: get_field('field_666c8794b6c48', 'user_'.$user_id) ?: 'N/A'); ?>',
            telefone: '<?php echo esc_js(get_field('field_6671b1480d481', 'user_'.$user_id) ?: 'N/A'); ?>',
            cpf: '<?php echo esc_js(get_field('field_666c87a7b6c49', 'user_'.$user_id) ?: 'N/A'); ?>',
            email: '<?php echo esc_js(get_field('field_66b244e3d8b86', 'user_'.$user_id) ?: 'N/A'); ?>'
        };
    </script>

    <?php wp_footer(); ?>
</body>
</html>