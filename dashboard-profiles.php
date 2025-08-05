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
Template Name: Dashboard - Perfis
*/

// Evita o acesso direto ao arquivo.
if (!defined('ABSPATH')) {
    exit;
}

if ( ! is_user_logged_in() ) {
    // Fallback: Processa o formulário de login se enviado via POST
    $login_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cpf_login_nonce'])) {
        if (!wp_verify_nonce($_POST['cpf_login_nonce'], 'cpf_login_action')) {
            $login_error = 'Falha de segurança. Por favor, tente novamente.';
        } else {
            $cpf_input = sanitize_text_field($_POST['cpf']);
            $password = $_POST['password'];
            
            // Validação básica
            if (empty($cpf_input) || empty($password)) {
                $login_error = 'Por favor, preencha todos os campos.';
            } else {
                // Busca usuário por CPF
                $users = amedis_find_user_by_cpf($cpf_input);

                if (empty($users)) {
                    $login_error = 'CPF não cadastrado.';
                } else {
                    $user = $users[0];
                    $creds = [
                        'user_login'    => $user->user_login,
                        'user_password' => $password,
                        'remember'      => true
                    ];
                    $signon = wp_signon($creds, false);

                    if (is_wp_error($signon)) {
                        $login_error = 'Senha incorreta. <a href="' . esc_url(site_url('/reset-senha')) . '" class="text-primary hover:underline">Esqueceu a senha?</a>';
                    } else {
                        wp_safe_redirect($_SERVER['REQUEST_URI']);
                        exit;
                    }
                }
            }
        }
    }
    
    // Renderiza a página de login
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - <?php bloginfo('name'); ?></title>
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            border: "hsl(214.3 31.8% 91.4%)",
                            input: "hsl(214.3 31.8% 91.4%)",
                            ring: "hsl(142 76% 36%)",
                            background: "hsl(0 0% 100%)",
                            foreground: "hsl(240 10% 3.9%)",
                            primary: {
                                DEFAULT: "hsl(142 76% 36%)",
                                foreground: "hsl(0 0% 98%)",
                            },
                            secondary: {
                                DEFAULT: "hsl(210 40% 98%)",
                                foreground: "hsl(240 5.9% 10%)",
                            },
                            destructive: {
                                DEFAULT: "hsl(0 84.2% 60.2%)",
                                foreground: "hsl(210 40% 98%)",
                            },
                            muted: {
                                DEFAULT: "hsl(210 40% 98%)",
                                foreground: "hsl(215.4 16.3% 46.9%)",
                            },
                            accent: {
                                DEFAULT: "hsl(210 40% 96%)",
                                foreground: "hsl(222.2 84% 4.9%)",
                            },
                            card: {
                                DEFAULT: "hsl(0 0% 100%)",
                                foreground: "hsl(240 10% 3.9%)",
                            },
                        },
                        borderRadius: {
                            lg: "var(--radius)",
                            md: "calc(var(--radius) - 2px)",
                            sm: "calc(var(--radius) - 4px)",
                        },
                    }
                },
            }
        </script>
        
        <style>
            :root {
                --radius: 0.75rem;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Field validation styles */
            .field-error {
                border-color: hsl(0 84.2% 60.2%) !important;
                box-shadow: 0 0 0 1px hsl(0 84.2% 60.2% / 0.2) !important;
            }
            
            .field-success {
                border-color: hsl(142 76% 36%) !important;
                box-shadow: 0 0 0 1px hsl(142 76% 36% / 0.2) !important;
            }
        </style>
        
        <?php wp_head(); ?>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-6">
        <div class="w-full max-w-sm animate-fade-in">
            <!-- Login Card -->
            <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border shadow-sm py-6">
                <!-- Card Header -->
                <div class="grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6">
                    <div class="text-center mb-4">

<?php
$logo_url = hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes');

if (!empty($logo_url)) {
    $imagem_final = $logo_url;
} else {
    // Concatena a URL do tema com o caminho relativo da imagem
    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_hori.png';
}
?>
<img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="h-18 mx-auto mb-0"> 
                        
                    </div>
                    <div class="leading-none font-semibold text-xl text-center">Acesse sua conta</div>
                    <div class="text-muted-foreground text-sm text-center">Digite seu CPF e senha para continuar</div>
                </div>
                
                <!-- Card Content -->
                <div class="px-6">
                    <!-- Message Container -->
                    <div id="login-message" class="hidden mb-4 p-3 rounded-md text-sm"></div>
                    
                    <?php if (!empty($login_error)): ?>
                        <div class="mb-4 p-3 rounded-md bg-destructive/10 border border-destructive/20 text-destructive text-sm">
                            <?php echo $login_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="login-form" method="post" class="space-y-4">
                        <?php wp_nonce_field('cpf_login_action', 'cpf_login_nonce'); ?>
                        
                        <!-- CPF Field -->
                        <div class="space-y-2">
                            <label for="cpf" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                CPF
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    id="cpf" 
                                    name="cpf" 
                                    placeholder="000.000.000-00"
                                    required
                                    data-mask="cpf"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent pl-10 pr-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                />
                            </div>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Senha
                                </label>
                                <a href="<?php echo esc_url(site_url('/reset-senha')); ?>" class="text-xs text-primary hover:underline">
                                    Esqueceu a senha?
                                </a>
                            </div>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent pl-10 pr-10 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <button 
                                    type="button" 
                                    id="toggle-password"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            id="login-btn"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 w-full"
                        >
                            <span id="login-btn-text">Entrar</span>
                            <svg id="login-btn-icon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <!-- Loading Spinner (hidden by default) -->
                            <svg id="login-spinner" class="hidden animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <script>
            // Login AJAX functionality
            jQuery(document).ready(function($) {
                // CPF Mask
                const cpfInput = document.querySelector('[data-mask="cpf"]');
                if (cpfInput) {
                    cpfInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length > 11) value = value.substring(0, 11);
                        
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                        e.target.value = value;
                    });
                }
                
                // Password Toggle
                const togglePassword = document.getElementById('toggle-password');
                const passwordInput = document.getElementById('password');
                
                if (togglePassword && passwordInput) {
                    togglePassword.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        
                        const svg = this.querySelector('svg');
                        if (type === 'text') {
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
                        } else {
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                        }
                    });
                }
                
                // AJAX Login Form
                $('#login-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    const $form = $(this);
                    const $btn = $('#login-btn');
                    const $btnText = $('#login-btn-text');
                    const $btnIcon = $('#login-btn-icon');
                    const $spinner = $('#login-spinner');
                    const $messageContainer = $('#login-message');
                    
                    const cpf = $('#cpf').val();
                    const password = $('#password').val();
                    
                    // Reset field styles
                    $('#cpf, #password').removeClass('field-error field-success');
                    
                    // Validação básica
                    let hasError = false;
                    
                    if (!cpf) {
                        $('#cpf').addClass('field-error');
                        hasError = true;
                    }
                    
                    if (!password) {
                        $('#password').addClass('field-error');
                        hasError = true;
                    }
                    
                    if (hasError) {
                        showMessage('error', 'Por favor, preencha todos os campos.');
                        return;
                    }
                    
                    // Validação de CPF
                    const cpfClean = cpf.replace(/\D/g, '');
                    if (cpfClean.length !== 11) {
                        $('#cpf').addClass('field-error');
                        showMessage('error', 'Por favor, digite um CPF válido.');
                        return;
                    }
                    
                    // Mark fields as valid
                    $('#cpf, #password').addClass('field-success');
                    
                    // Loading state
                    $btn.prop('disabled', true);
                    $btnText.text('Entrando...');
                    $btnIcon.addClass('hidden');
                    $spinner.removeClass('hidden');
                    $messageContainer.addClass('hidden');
                    
                    // AJAX Request
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        timeout: 10000, // 10 seconds timeout
                        data: {
                            action: 'amedis_login',
                            cpf: cpf,
                            password: password,
                            nonce: '<?php echo wp_create_nonce('cpf_login_action'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('success', response.data.message);
                                // Redirect after success
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showMessage('error', response.data.message);
                                resetButton();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                responseText: xhr.responseText,
                                statusCode: xhr.status
                            });
                            
                            let errorMessage = 'Erro de conexão. Tente novamente.';
                            
                            if (xhr.status === 403) {
                                errorMessage = 'Tentando método alternativo...';
                                // Fallback: submit form normally
                                setTimeout(function() {
                                    $form.off('submit').submit();
                                }, 1000);
                            } else if (xhr.status === 404) {
                                errorMessage = 'Serviço não encontrado.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Erro interno do servidor.';
                            }
                            
                            showMessage('error', errorMessage);
                            
                            if (xhr.status !== 403) {
                                resetButton();
                            }
                        }
                    });
                });
                
                // Show message function
                function showMessage(type, message) {
                    const $messageContainer = $('#login-message');
                    $messageContainer.removeClass('hidden bg-destructive/10 border-destructive/20 text-destructive bg-green-50 border-green-200 text-green-800');
                    
                    if (type === 'error') {
                        $messageContainer.addClass('bg-destructive/10 border-destructive/20 text-destructive');
                    } else if (type === 'success') {
                        $messageContainer.addClass('bg-green-50 border-green-200 text-green-800');
                    }
                    
                    $messageContainer.html(message).removeClass('hidden');
                }
                
                // Reset button function
                function resetButton() {
                    const $btn = $('#login-btn');
                    const $btnText = $('#login-btn-text');
                    const $btnIcon = $('#login-btn-icon');
                    const $spinner = $('#login-spinner');
                    
                    $btn.prop('disabled', false);
                    $btnText.text('Entrar');
                    $btnIcon.removeClass('hidden');
                    $spinner.addClass('hidden');
                }
                
                // Real-time validation
                $('#cpf').on('input blur', function() {
                    const $field = $(this);
                    const value = $field.val().replace(/\D/g, '');
                    
                    $field.removeClass('field-error field-success');
                    
                    if (value.length === 11) {
                        $field.addClass('field-success');
                    } else if (value.length > 0) {
                        $field.addClass('field-error');
                    }
                });
                
                $('#password').on('input blur', function() {
                    const $field = $(this);
                    const value = $field.val();
                    
                    $field.removeClass('field-error field-success');
                    
                    if (value.length >= 4) {
                        $field.addClass('field-success');
                    } else if (value.length > 0) {
                        $field.addClass('field-error');
                    }
                });
                
                // Auto-focus on CPF field
                setTimeout(function() {
                    $('#cpf').focus();
                }, 300);
            });
        </script>
        
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
} else {
    get_header('zero');
    get_template_part('dashboard', 'user-profile');
    get_footer();
}