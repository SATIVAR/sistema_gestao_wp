<form id="login-form" action="#" class="space-y-6">
    <!-- Status para compatibilidade (invisível por padrão) -->
    <p class="status text-sm text-slate-600 text-center" style="display: none;"></p>
    
    <!-- Campos do formulário -->
    <div class="space-y-4">
        <!-- Campo Username -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Usuário</label>
            <input
                class="w-full h-12 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder:text-slate-400 px-4 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-emerald-500 focus-visible:border-emerald-500 transition-colors"
                type="text"
                name="username"
                autocomplete="username"
                placeholder="Digite seu usuário"
                value=""
            >
        </div>
        
        <!-- Campo Password -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Senha</label>
            <input
                class="w-full h-12 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder:text-slate-400 px-4 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-emerald-500 focus-visible:border-emerald-500 transition-colors"
                type="password"
                name="password"
                autocomplete="current-password"
                placeholder="Digite sua senha"
                value=""
            >
        </div>
    </div>

    <!-- Status Alert (shadcn/ui style) -->
    <div id="status" class="flex items-center gap-3 p-4 text-sm text-sky-800 bg-sky-50 border border-sky-200 rounded-lg transition-all" role="alert" style="display: none;">
        <svg class="flex-shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 1 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div>
            <span class="font-medium">Aguarde!</span> <span id="status-message"></span>
        </div>
    </div>
    
    <!-- Botão Submit (shadcn/ui Button style) -->
    <button 
        class="w-full inline-flex items-center justify-center h-12 rounded-lg px-4 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200" 
        type="submit"
    >
        Entrar na plataforma
    </button>

    <!-- Link "Perdeu a senha?" -->
    <div class="text-center">
        <a class="text-sm text-slate-600 hover:text-emerald-600 hover:underline transition-colors" href="<?php bloginfo('url') ?>">
            Esqueceu sua senha?
        </a>
    </div>

    <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
</form>