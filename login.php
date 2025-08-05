<!-- Split Screen Layout - shadcn/ui inspired -->
<section class="min-h-screen bg-white flex">
    <!-- Left Side - Marketing Content -->
    <div class="hidden lg:flex lg:w-[65%] relative overflow-hidden" style="background: linear-gradient(150deg, #16a34a 0%, #119e55 40%, #0d8e4d 70%, #0a6e3c 100%);">
        <!-- Background Pattern - Minimal overlay -->
        <div class="absolute inset-0" style="background: linear-gradient(30deg, rgba(22,163,74,0.08) 0%, rgba(22,163,74,0.00) 45%, rgba(10,110,60,0.10) 100%);"></div>
        
        <!-- Content Container -->
        <div class="relative z-10 flex flex-col justify-center px-12 xl:px-16 text-white">
            
            <!-- Slideshow Container -->
            <div class="space-y-8">
                <div id="slideshow" class="relative min-h-[240px]">
                    <!-- Slide 1 - Sistema de Gestão -->
                    <div class="slide active opacity-100 transition-all duration-700 ease-in-out">
                        <div class="flex items-center mb-6">
                            <!-- SVG Icon -->
                            <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h2 class="text-3xl xl:text-4xl font-bold leading-tight text-white">
                                Sistema de Gestão
                            </h2>
                        </div>
                        <p class="text-white/90 text-lg xl:text-2xl leading-relaxed">
                            Gestão Completa para associações de pacientes, com módulos integrados para usuários, prescritores e vendas.
                        </p>
                    </div>
                    
                    <!-- Slide 2 - Cadastro de Usuários -->
                    <div class="slide opacity-0 absolute top-0 left-0 w-full transition-all duration-700 ease-in-out">
                        <div class="flex items-center mb-6">
                            <!-- SVG Icon -->
                            <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h2 class="text-3xl xl:text-4xl font-bold leading-tight text-white">
                                Cadastro de Usuários
                            </h2>
                        </div>
                        <p class="text-white/90 text-lg xl:text-2xl leading-relaxed">
                            Gestão completa de pacientes associados e documentos, com histórico detalhado e informações centralizadas.
                        </p>
                    </div>
                    
                    <!-- Slide 3 - Gestão de Prescritores -->
                    <div class="slide opacity-0 absolute top-0 left-0 w-full transition-all duration-700 ease-in-out">
                        <div class="flex items-center mb-6">
                            <!-- SVG Icon -->
                            <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h2 class="text-3xl xl:text-4xl font-bold leading-tight text-white">
                                Gestão de Prescritores
                            </h2>
                        </div>
                        <p class="text-white/90 text-lg xl:text-2xl leading-relaxed">
                            Controle completo de médicos, profissionais de saúde e receitas médicas com validação e segurança.
                        </p>
                    </div>
                    
                    <!-- Slide 4 - Módulo de Vendas -->
                    <div class="slide opacity-0 absolute top-0 left-0 w-full transition-all duration-700 ease-in-out">
                        <div class="flex items-center mb-6">
                            <!-- SVG Icon -->
                            <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h2 class="text-3xl xl:text-4xl font-bold leading-tight text-white">
                                Módulo de Vendas
                            </h2>
                        </div>
                        <p class="text-white/90 text-lg xl:text-2xl leading-relaxed">
                            Sistema opcional para transações, controle de produtos e gestão financeira para sua associação.
                        </p>
                    </div>
                    
                    <!-- Slide 5 - Acesso Rápido -->
                    <div class="slide opacity-0 absolute top-0 left-0 w-full transition-all duration-700 ease-in-out">
                        <div class="flex items-center mb-6">
                            <!-- SVG Icon -->
                            <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <h2 class="text-3xl xl:text-4xl font-bold leading-tight text-white">
                                Acesso Rápido
                            </h2>
                        </div>
                        <p class="text-white/90 text-lg xl:text-2xl leading-relaxed">
                            Navegação administrativa simplificada com acesso rápido a pacientes, receitas, prescritores, produtos e relatórios.
                        </p>
                    </div>
                </div>
                
                <!-- Slide Indicators -->
                <div class="flex justify-center space-x-3">
                    <button class="slide-indicator w-3 h-3 rounded-full bg-white/90 transition-all duration-300 hover:bg-white active" data-slide="0"></button>
                    <button class="slide-indicator w-3 h-3 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/70" data-slide="1"></button>
                    <button class="slide-indicator w-3 h-3 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/70" data-slide="2"></button>
                    <button class="slide-indicator w-3 h-3 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/70" data-slide="3"></button>
                    <button class="slide-indicator w-3 h-3 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/70" data-slide="4"></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-[35%] flex items-center justify-center p-4 lg:p-8">
        <div class="w-full max-w-md space-y-5">
            <!-- Mobile Logo (visible only on small screens) -->
            <div class="lg:hidden text-center">
                <img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="mx-auto h-16 w-auto"> 
            </div>                        
            <!-- Form Header -->
            <div class="text-center lg:text-center space-y-2">
            <!-- Logo -->
            <div class="mb-0">
                <?php
                $logo_url = hg_exibir_campo_acf('logo_vertical', 'img', 'configuracoes');
                if (!empty($logo_url)) {
                    $imagem_final = $logo_url;
                } else {
                    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_vert.png';
                }
                ?>
                <img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="h-48 w-auto mx-auto"> 
            </div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 m-0">Bem vindo de volta!</h1>
                <p class="text-sm text-slate-600">Acesse com suas credenciais</p>
            </div>
            
            <!-- Form Container -->
            <div>
                <?php get_template_part('login', 'form') ?>
            </div>
        </div>
    </div>
</section>
<!-- Slideshow Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.slide-indicator');
    let currentSlide = 0;
    let slideInterval;
    
    function showSlide(index) {
        // Remove active classes from all slides
        slides.forEach((slide, i) => {
            slide.classList.remove('active', 'opacity-100');
            slide.classList.add('opacity-0');
        });
        
        // Remove active classes from all indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('active', 'bg-white/90');
            indicator.classList.add('bg-white/40');
        });
        
        // Show current slide
        slides[index].classList.remove('opacity-0');
        slides[index].classList.add('active', 'opacity-100');
        
        // Update current indicator
        indicators[index].classList.remove('bg-white/40');
        indicators[index].classList.add('active', 'bg-white/90');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    function startSlideshow() {
        slideInterval = setInterval(nextSlide, 5000);
    }
    
    function stopSlideshow() {
        clearInterval(slideInterval);
    }
    
    // Initialize slideshow
    showSlide(0);
    startSlideshow();
    
    // Manual slide control
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            stopSlideshow();
            currentSlide = index;
            showSlide(currentSlide);
            // Restart slideshow after manual interaction
            setTimeout(startSlideshow, 1000);
        });
    });
    
    // Pause on hover
    const slideshowContainer = document.getElementById('slideshow');
    if (slideshowContainer) {
        slideshowContainer.addEventListener('mouseenter', stopSlideshow);
        slideshowContainer.addEventListener('mouseleave', startSlideshow);
    }
});
</script>