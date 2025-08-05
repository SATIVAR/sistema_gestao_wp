<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage Tronozelo e Pe
 * @since [ HG ] W 1.0
/*
Template Name: Home
*/
//acf_form_head();
get_header('zero');
?>
   <style>
        /* Animações para elementos flutuantes */
        @keyframes float-1 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(90deg); }
            50% { transform: translate(-20px, -60px) rotate(180deg); }
            75% { transform: translate(-40px, -20px) rotate(270deg); }
        }

        @keyframes float-2 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-40px, 40px) rotate(120deg); }
            66% { transform: translate(50px, -30px) rotate(240deg); }
        }

        @keyframes float-3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -40px) scale(1.1); }
        }

        @keyframes float-4 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            20% { transform: translate(25px, 35px) rotate(72deg); }
            40% { transform: translate(-30px, 20px) rotate(144deg); }
            60% { transform: translate(-15px, -45px) rotate(216deg); }
            80% { transform: translate(35px, -25px) rotate(288deg); }
        }

        /* Animações de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fade-in-scale {
            animation: fadeInScale 0.6s ease-out forwards;
        }

        .animate-delay-200 {
            animation-delay: 0.2s;
        }

        .animate-delay-400 {
            animation-delay: 0.4s;
        }

        .animate-delay-600 {
            animation-delay: 0.6s;
        }

        /* Elementos flutuantes */
        .floating-element-1 {
            animation: float-1 45s ease-in-out infinite;
        }

        .floating-element-2 {
            animation: float-2 60s ease-in-out infinite;
        }

        .floating-element-3 {
            animation: float-3 35s ease-in-out infinite;
        }

        .floating-element-4 {
            animation: float-4 50s ease-in-out infinite;
        }

        /* Gradiente radial personalizado */
        .bg-gradient-radial {
            background: radial-gradient(circle at center, #f8fafc 0%, #ffffff 50%, #f1f5f9 100%);
        }
    </style>
</head>
<body>
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-100">
        
        <!-- Elementos flutuantes abstratos -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Elemento flutuante 1 -->
            <div class="floating-element-1 absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/10 to-teal-300/5 rounded-full blur-3xl opacity-30"></div>
            
            <!-- Elemento flutuante 2 -->
            <div class="floating-element-2 absolute top-3/4 right-1/4 w-80 h-80 bg-gradient-to-br from-blue-200/8 to-indigo-300/5 rounded-full blur-3xl opacity-25"></div>
            
            <!-- Elemento flutuante 3 -->
            <div class="floating-element-3 absolute top-1/2 left-3/4 w-48 h-48 bg-gradient-to-br from-purple-200/12 to-pink-300/6 rounded-full blur-3xl opacity-20"></div>
            
            <!-- Elemento flutuante 4 -->
            <div class="floating-element-4 absolute bottom-1/4 left-1/2 w-72 h-72 bg-gradient-to-br from-amber-200/8 to-orange-300/4 rounded-full blur-3xl opacity-15"></div>
            
            <!-- Elemento flutuante adicional para mais profundidade -->
            <div class="floating-element-1 absolute top-1/3 right-1/3 w-56 h-56 bg-gradient-to-br from-slate-200/10 to-gray-300/5 rounded-full blur-3xl opacity-20" style="animation-delay: -15s;"></div>
        </div>

        <!-- Container principal com isolamento de contexto -->
        <div class="relative z-10 py-12 px-6 mx-auto max-w-screen-xl text-center lg:py-20 lg:px-12">
            
            <!-- Logo com animação de entrada -->
            <div class="opacity-0 animate-fade-in-scale">

<?php
$logo_url = hg_exibir_campo_acf('logo_vertical', 'img', 'configuracoes');

if (!empty($logo_url)) {
    $imagem_final = $logo_url;
} else {
    // Concatena a URL do tema com o caminho relativo da imagem
    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_vert.png';
}
?>

<img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="mt-5 mb-0 max-w-[320px] mx-auto opacity-0 animate-fade-in-up animate-delay-400">

            </div>
            
            <!-- Texto de apresentação com animação -->
            <div class="opacity-0 animate-fade-in-up animate-delay-200 mb-12 text-md font-normal text-slate-700 lg:text-2xl px-4 mx-auto leading-relaxed">
                <?php echo hg_exibir_campo_acf('texto_apresentacao', 'editor', 'configuracoes'); ?>
            </div>
            
            <!-- Container dos botões com animação -->
            <div class="opacity-0 animate-fade-in-up animate-delay-400 flex flex-col mb-8 lg:mb-16 space-y-6 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-6">
<?php 
if ( !is_user_logged_in() && !current_user_can('administrator') ) { 
    // Se o usuário não estiver logado, redireciona para a home
    //wp_redirect(home_url());
    //exit;

?>                
                <!-- Botão Login (Secundário) -->
                <a href="<?php bloginfo('url') ?>/login/" class="group inline-flex justify-center items-center py-4 px-8 text-base font-medium text-center text-slate-700 rounded-xl border-2 border-emerald-500/40 bg-white/50 backdrop-blur-sm hover:bg-slate-50 hover:border-emerald-500 hover:text-slate-900 focus:ring-4 focus:ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-3 -ml-1 w-5 h-5 transition-transform duration-300 group-hover:scale-110">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    Login
                </a>
<?php } else { ?>

                <a href="<?php bloginfo('url') ?>/todos-associados/" class="group inline-flex justify-center items-center py-4 px-8 text-base font-medium text-center text-slate-700 rounded-xl border-2 border-emerald-500/40 bg-white/50 backdrop-blur-sm hover:bg-slate-50 hover:border-emerald-500 hover:text-slate-900 focus:ring-4 focus:ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-3 -ml-1 w-5 h-5 transition-transform duration-300 group-hover:scale-110">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    Todos Associados
                </a>

<?php } ?>    
                <!-- Botão Cadastro de Paciente (Principal) -->
                <a href="<?php bloginfo('url') ?>/cadastro-paciente/" class="group inline-flex justify-center items-center py-4 px-8 text-base font-medium text-center text-white hover:text-white bg-green-600 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl border-2 border-emerald-500/20 hover:from-emerald-700 hover:to-teal-700 hover:border-emerald-400/30 focus:ring-4 focus:ring-emerald-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl shadow-emerald-500/25">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-3 -ml-1 w-5 h-5 transition-transform duration-300 group-hover:scale-110">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Cadastro de Paciente
                </a>  
            </div>
        
        </div>
    </section>
<?php 
get_footer();
