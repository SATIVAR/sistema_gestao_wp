<?php
// Define o role do usuário atual para uso nas condições
$current_user_role = sativar_get_user_role_safe(get_current_user_id());

// Debug temporário - remover após teste
if (WP_DEBUG && current_user_can('administrator')) {
    echo "<!-- DEBUG: Current User ID: " . get_current_user_id() . " -->";
    echo "<!-- DEBUG: Current User Role: " . $current_user_role . " -->";
    echo "<!-- DEBUG: User Meta: " . json_encode(get_user_meta(get_current_user_id(), 'user_role_control', true)) . " -->";
}
?>

<style>
body {
    padding-top: 80px !important;
}
</style>

<!-- Navbar Fixa - Início do Código Refatorado -->
<nav class="bg-white/80 backdrop-blur-lg border-b border-gray-200 fixed w-full z-50 top-0 start-0">
    <div class="flex flex-wrap items-center justify-between mx-auto p-4">
        
        <!-- Logo -->
        <a href="<?php bloginfo('url') ?>" class="flex items-center space-x-3 rtl:space-x-reverse">

            <?php
                $logo_url = hg_exibir_campo_acf('logo_horizontal', 'img', 'configuracoes');

                if (!empty($logo_url)) {
                    $imagem_final = $logo_url;
                } else {
                    // Concatena a URL do tema com o caminho relativo da imagem
                    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_hori.png';
                }
            ?>
            <img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="h-12"> 

        </a>

        <div class="flex items-center md:order-2 space-x-3 md:space-x-4 rtl:space-x-reverse">
            
            <!-- Informações do Usuário -->
            <div class="text-right">
                <span class="block text-xs text-gray-500">
                    <?php if (is_page('ficha-de-acolhimento')) { echo 'BEM-VINDO, VISITANTE!'; } else { echo 'BEM-VINDO!'; } ?>
                </span>
                <span class="text-sm font-medium text-gray-800 uppercase">
                    <?php
                    if (is_page('ficha-de-acolhimento')) {
                        echo 'Visitante';
                    } else {
                        if (is_user_logged_in()) {
                            $current_user = wp_get_current_user();
                            echo esc_html($current_user->display_name);
                            echo ' - ';
                            echo do_shortcode('[link_logout]');
                        } else {
                            echo 'Olá, visitante!';
                        }
                    }
                    ?>
                </span>
            </div>

            <!-- Botão do Menu (visível apenas para admin/gerente) -->
            <?php if (is_user_logged_in() && (current_user_can('administrator') || current_user_can('gerente'))) : ?>
            <div class="relative">
                <button id="menu-toggle-button" data-dropdown-toggle="menu-dropdown" data-dropdown-placement="bottom-end" data-dropdown-trigger="hover" type="button" class="inline-flex items-center justify-center p-2 w-11 h-11 text-sm text-white bg-green-500 hover:bg-green-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-200 transition-colors duration-200">
                    <span class="sr-only">Abrir menu principal</span>
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ======================================================================================================= -->
    <!-- Dropdown Menu (Acesso Rápido) - LAYOUT EM COLUNAS                                                     -->
    <!-- ======================================================================================================= -->
    <div class="hidden z-50 my-2 md:max-w-[800px] text-base list-none bg-white rounded-lg shadow-xl border border-gray-200/50" id="menu-dropdown">
        <div class="px-6 py-4 border-b border-gray-100">
            <span class="block text-sm font-semibold text-gray-900">Acesso Rápido</span>
            <span class="block text-sm text-gray-500 truncate">Navegação administrativa</span>
        </div>
<?php
    $grid_class = 'grid-cols-2'; 

    if ($current_user_role === 'super_admin' || $current_user_role === 'gerente') {
        $grid_class = 'grid-cols-3';
    }
?>
        <div class="p-6">
            <div class="grid <?php echo $grid_class; ?> gap-8">
                
                <!-- COLUNA 1 -->
                <div class="space-y-6">
                    
                    <!-- Seção: Pacientes Associados -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Pacientes Associados</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/cadastro-paciente/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                    </svg>
                                    Novo Paciente
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/todos-associados/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Todos os Pacientes
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Seção: Receitas & Laudos -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Receitas & Laudos</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/nova-receita/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Nova Receita / Laudo
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/receitas/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Todas as Receitas / Laudos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Seção: Prescritores -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Prescritores</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/cadastro-de-prescritor/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Novo Prescritor
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/prescritores/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.5 3.55a9.094 9.094 0 0 1-3.741-.479 3 3 0 0 1 4.682-2.72m0 0a24.32 24.32 0 0 1 3.741.479 3 3 0 0 1-4.682 2.72m0 0L15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Todos os Prescritores
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- COLUNA 2 -->
                <div class="space-y-6">
                    
                    <!-- Seção: Produtos -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Produtos</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/novo-produtowoo/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                    Novo Produto
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/todos-produtoswoo/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                    Todos os Produtos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Seção: Entradas -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Entradas</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/novo-pedido/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l2.25 2.25L15 10.5" />
                                    </svg>
                                    Nova Entrada
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/pedidos/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    Todas as Entradas
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Seção: Saídas -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Saídas</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="<?php bloginfo('url') ?>/nova-saida/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                    </svg>
                                    Nova Saída
                                </a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url') ?>/todas-saidas/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    Todas as Saídas
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

                <?php if ($current_user_role === 'super_admin' || $current_user_role === 'gerente'): ?>
                <!-- COLUNA 3 -->
                <div class="space-y-6">
                    
                    <!-- Seção: Relatórios -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Relatórios</h3>
                        <ul class="space-y-1">
                            <li>

                                    <a href="<?php bloginfo('url') ?>/relatorios-dashboard-associados" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>
                                        Associados
                                    </a>

                            </li>
                            <li>
                                    <a href="<?php bloginfo('url') ?>/relatorios-dashboard-financeiro" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-15c-.621 0-1.125-.504-1.125-1.125V8.25m15-3.75h-15" />
                                        </svg>
                                        Financeiro
                                    </a>

                            </li>
                        </ul>
                    </div>

                    <!-- Seção: Sistema -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3 pb-1 border-b border-gray-100">Sistema</h3>
                        <ul class="space-y-1">
                            <li>

                                    <a href="<?php bloginfo('url') ?>/dashboard-paciente/" target="_blank" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Minha Conta
                                    </a>

                            </li>          
                            <?php if ($current_user_role === 'super_admin') : ?>
                            <li>

                                    <a href="<?php bloginfo('url') ?>/sistema-usuarios/" target="_blank" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Usuários
                                    </a>

                            </li> 
                            <?php endif; ?>
                            <li>

                                    <a href="<?php bloginfo('url') ?>/configuracoes/" class="flex items-center w-full p-2 text-sm text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Configurações
                                    </a>

                            </li>
                        </ul>
                    </div>

                </div>
                <?php endif ?>

            </div>
        </div>
    </div>

</nav>
<!-- Fim do Código Refatorado -->

