<?php
/**
 * Template Name: Dashboard Usuarios do Sistema
 * 
 * Dashboard premium para gerenciamento de usuários com interface moderna
 * 
 * @package SativarApp
 * @version 2.0
 */

// Verificação de acesso com sistema de roles customizado
if (!sativar_check_user_access()) {
    wp_safe_redirect(home_url());
    exit;
}

$current_user_role = sativar_get_user_role_safe(get_current_user_id());

get_header('zero');
?>

<?php get_template_part('header', 'user') ?>

<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container uk-container-expand">
        
        <!-- Header Dashboard Premium -->
        <div class="dashboard-header mb-8">
            <div class="header-content bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-2xl">
                <div class="flex justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center justify-start space-x-2 header-info">
                        <span class="icon-users text-6xl"></span>
                        <div>
                            <h1 class="text-blue-100 text-4xl font-bold mb-2 flex items-center gap-3">
                                <?php if ($current_user_role === 'super_admin'): ?>
                                    Sistema de Administradores
                                <?php elseif ($current_user_role === 'gerente'): ?>
                                    Painel do Gerente
                                <?php else: ?>
                                    Meu Perfil
                                <?php endif; ?>
                            </h1>
                            <p class="text-blue-100 text-lg m-0">
                                <?php if ($current_user_role === 'super_admin'): ?>
                                    Gerencie todos os administradores e suas permissões especiais
                                <?php elseif ($current_user_role === 'gerente'): ?>
                                    Gerencie gerentes e atendentes da sua equipe
                                <?php else: ?>
                                    Visualize suas informações de perfil
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Estatísticas Rápidas -->
                    <div class="stats-grid grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <?php
                        // Busca usuários baseado no role atual
                        if ($current_user_role === 'super_admin') {
                            // Super Admin vê todos os administradores
                            $admin_users = get_users(array('role' => 'administrator'));
                        } elseif ($current_user_role === 'gerente') {
                            // Gerente vê apenas gerentes e atendentes
                            $admin_users = get_users(array('role' => 'administrator'));
                            $filtered_users = array();
                            foreach ($admin_users as $user) {
                                $user_role = sativar_get_user_active_role($user->ID);
                                if ($user_role === 'gerente' || $user_role === 'atendente' || !$user_role) {
                                    $filtered_users[] = $user;
                                }
                            }
                            $admin_users = $filtered_users;
                        } else {
                            // Atendente vê apenas próprio perfil
                            $admin_users = array(wp_get_current_user());
                        }
                        
                        $total_admin_users = count($admin_users);
                        $super_admins = 0;
                        $gerentes = 0;
                        $atendentes = 0;
                        
                        foreach ($admin_users as $user) {
                            if (sativar_user_has_role($user->ID, 'super_admin')) $super_admins++;
                            if (sativar_user_has_role($user->ID, 'gerente')) $gerentes++;
                            if (sativar_user_has_role($user->ID, 'atendente')) $atendentes++;
                        }
                        ?>
                        
                        <div class="stat-card bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold"><?php echo $total_admin_users; ?></div>
                            <div class="text-sm text-blue-100">Administradores</div>
                        </div>
                        
                        <div class="stat-card bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-red-200"><?php echo $super_admins; ?></div>
                            <div class="text-sm text-blue-100">Super Admins</div>
                        </div>
                        
                        <div class="stat-card bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-orange-200"><?php echo $gerentes; ?></div>
                            <div class="text-sm text-blue-100">Gerentes</div>
                        </div>
                        
                        <div class="stat-card bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-green-200"><?php echo $atendentes; ?></div>
                            <div class="text-sm text-blue-100">Atendentes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Filtros Premium -->
        <div class="filter-bar mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex gap-6 items-center">

                    <!-- Filtros por Função -->
                    <div class="filter-chips flex flex-wrap gap-3">
                        <button class="filter-chip active" data-filter="all">
                            <span class="chip-text">Todos</span>
                            <span class="chip-count"><?php echo $total_admin_users; ?></span>
                        </button>
                        
                        <button class="filter-chip" data-filter="super_admin">
                            <span class="chip-text">Super Admin</span>
                            <span class="chip-count"><?php echo $super_admins; ?></span>
                        </button>
                        
                        <button class="filter-chip" data-filter="gerente">
                            <span class="chip-text">Gerente</span>
                            <span class="chip-count"><?php echo $gerentes; ?></span>
                        </button>
                        
                        <button class="filter-chip" data-filter="atendente">
                            <span class="chip-text">Atendente</span>
                            <span class="chip-count"><?php echo $atendentes; ?></span>
                        </button>
                        
                        <button class="filter-chip" data-filter="none">
                            <span class="chip-text">Sem Função</span>
                            <span class="chip-count"><?php echo $total_admin_users - $super_admins - $gerentes - $atendentes; ?></span>
                        </button>
                    </div>
                    <!-- Busca em Tempo Real -->
                    <div class="search-container flex-1 relative">
                        <div class="relative">
                            <input type="text" 
                                   id="user-search" 
                                   placeholder="Buscar administradores..." 
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 bg-gray-50 focus:bg-white">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <!-- Ações Rápidas -->
                    <div class="quick-actions flex gap-3">
                        <button class="action-btn primary" id="refresh-users">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Usuários Premium -->
        <div class="users-grid">
            <div id="users-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                <?php foreach ($admin_users as $user): 
                    $user_role = sativar_get_user_active_role($user->ID);
                    $avatar_url = get_avatar_url($user->ID, array('size' => 80));
                    $can_edit = sativar_can_edit_user_role($current_user_role, $user_role);
                ?>
                
                <div class="user-card" data-user-id="<?php echo $user->ID; ?>" data-role="<?php echo $user_role ?: 'none'; ?>">
                    <div class="card-content bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden group">
                        
                        <!-- Card Header -->
                        <div class="card-header relative p-6 pb-4">
                            <div class="absolute top-4 right-4">
                                <div class="role-badge role-<?php echo $user_role ?: 'none'; ?>">
                                    <?php 
                                    $role_labels = array(
                                        'super_admin' => 'Super Admin',
                                        'gerente' => 'Gerente',
                                        'atendente' => 'Atendente'
                                    );
                                    echo $user_role ? $role_labels[$user_role] : 'Sem Função';
                                    ?>
                                </div>
                            </div>
                            
                            <div class="user-avatar mb-4">
                                <img src="<?php echo $avatar_url; ?>" 
                                     alt="<?php echo esc_attr($user->display_name); ?>"
                                     class="w-16 h-16 rounded-full border-4 border-white shadow-lg mx-auto">
                            </div>
                            
                            <div class="user-info text-center">
                                <h3 class="user-name text-lg font-bold text-gray-800 mb-1">
                                    <?php echo esc_html($user->display_name); ?>
                                </h3>
                                <p class="user-login text-sm text-gray-500 mb-2">
                                    @<?php echo esc_html($user->user_login); ?>
                                </p>
                                <p class="user-email text-xs text-gray-400 truncate">
                                    <?php echo esc_html($user->user_email); ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-center">
                            <?php if ($current_user_role === 'super_admin'): ?>
                                <button class="change-password-btn text-sm font-medium text-blue-600 hover:text-blue-800 mb-3 cursor-pointer transition-colors duration-200" 
                                        data-user-id="<?php echo $user->ID; ?>" 
                                        data-user-name="<?php echo esc_attr($user->display_name); ?>">
                                    🔑 Alterar Senha
                                </button>
                            <?php endif; ?>
                        </div>
                        <!-- Role Control Switcher -->
                        <div class="role-control p-6 pt-2">
                            <?php if ($can_edit): ?>
                                <!-- Controles de edição para usuários com permissão -->
                                <div class="role-switcher-premium" data-user-id="<?php echo $user->ID; ?>">
                                    <div class="switcher-label text-sm font-medium text-gray-700 mb-3">Nível de Acesso:</div>
                                    
                                    <div class="role-options grid grid-cols-2 gap-2">
                                        <?php if ($current_user_role === 'super_admin'): ?>
                                            <label class="role-option-premium <?php echo $user_role === 'super_admin' ? 'active' : ''; ?>">
                                                <input type="radio" name="role_<?php echo $user->ID; ?>" value="super_admin" <?php checked($user_role, 'super_admin'); ?>>
                                                <span class="option-content">
                                                    <span class="option-icon">👑</span>
                                                    <span class="option-text">Super</span>
                                                </span>
                                            </label>
                                            
                                            <label class="role-option-premium <?php echo $user_role === 'gerente' ? 'active' : ''; ?>">
                                                <input type="radio" name="role_<?php echo $user->ID; ?>" value="gerente" <?php checked($user_role, 'gerente'); ?>>
                                                <span class="option-content">
                                                    <span class="option-icon">👔</span>
                                                    <span class="option-text">Gerente</span>
                                                </span>
                                            </label>
                                        <?php endif; ?>
                                        
                                        <label class="role-option-premium <?php echo $user_role === 'atendente' ? 'active' : ''; ?>">
                                            <input type="radio" name="role_<?php echo $user->ID; ?>" value="atendente" <?php checked($user_role, 'atendente'); ?>>
                                            <span class="option-content">
                                                <span class="option-icon">🎧</span>
                                                <span class="option-text">Atendente</span>
                                            </span>
                                        </label>
                                        
                                        <label class="role-option-premium <?php echo !$user_role ? 'active' : ''; ?>">
                                            <input type="radio" name="role_<?php echo $user->ID; ?>" value="none" <?php checked($user_role, false); ?>>
                                            <span class="option-content">
                                                <span class="option-icon">👤</span>
                                                <span class="option-text">Nenhum</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Visualização apenas para usuários sem permissão -->
                                <div class="role-display-only">
                                    <div class="switcher-label text-sm font-medium text-gray-700 mb-3">Nível de Acesso:</div>
                                    <div class="role-badge-large role-<?php echo $user_role ?: 'none'; ?> text-center p-3 rounded-lg">
                                        <?php echo $user_role ? $role_labels[$user_role] : 'Sem Função'; ?>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-center">Visualização apenas</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Status Indicator -->
                        <div class="status-indicator" id="status-<?php echo $user->ID; ?>"></div>
                    </div>
                </div>                
                <?php endforeach; ?>
            </div>

            <!-- Loading Skeleton -->
            <div id="loading-skeleton" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                    <div class="skeleton-card bg-white rounded-2xl shadow-lg p-6 animate-pulse">
                        <div class="skeleton-avatar w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4"></div>
                        <div class="skeleton-text h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="skeleton-text h-3 bg-gray-200 rounded w-3/4 mx-auto mb-4"></div>
                        <div class="skeleton-controls space-y-2">
                            <div class="h-8 bg-gray-200 rounded"></div>
                            <div class="h-8 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden text-center py-16">
                <div class="empty-illustration mb-6">
                    <svg class="w-24 h-24 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum administrador encontrado</h3>
                <p class="text-gray-400">Tente ajustar os filtros ou termos de busca</p>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Troca de Senha -->
<div id="password-modal" class="modal-overlay hidden">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-md mx-auto p-6 relative">
        <div class="modal-header flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Alterar Senha</h3>
            <button class="modal-close text-gray-400 hover:text-gray-600 text-2xl font-bold transition-colors duration-200">&times;</button>
        </div>
        
        <div class="user-info mb-4 p-3 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Alterando senha para:</p>
            <p id="modal-user-name" class="font-semibold text-gray-800"></p>
        </div>
        
        <form id="password-form" class="space-y-4">
            <input type="hidden" id="target-user-id">
            
            <div class="form-group">
                <label for="new-password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha:</label>
                <input type="password" 
                       id="new-password" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300" 
                       placeholder="Digite a nova senha"
                       required 
                       minlength="6">
                <div class="password-strength mt-2 hidden">
                    <div class="strength-bar bg-gray-200 rounded-full h-2">
                        <div class="strength-fill bg-red-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p class="strength-text text-xs text-gray-500 mt-1">Força da senha</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha:</label>
                <input type="password" 
                       id="confirm-password" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300" 
                       placeholder="Confirme a nova senha"
                       required>
                <div id="password-match-indicator" class="mt-2 text-sm hidden">
                    <span class="match-text"></span>
                </div>
            </div>
            
            <div id="password-form-status" class="hidden p-3 rounded-lg text-sm">
                <span class="status-text"></span>
            </div>
            
            <div class="modal-actions flex gap-3 justify-end pt-4">
                <button type="button" class="btn-cancel px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200 font-medium">
                    Cancelar
                </button>
                <button type="submit" class="btn-save px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="btn-text">Salvar</span>
                    <span class="btn-loading hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Salvando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Estilos Premium Adicionais -->
<style>
/* Gradientes e efeitos premium */
.bg-gradient-to-r {
    background: linear-gradient(to right, var(--tw-gradient-stops));
}

.from-blue-600 {
    --tw-gradient-from: #2563eb;
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(37, 99, 235, 0));
}

.to-purple-600 {
    --tw-gradient-to: #9333ea;
}

/* Grid responsivo */
.grid {
    display: grid;
}

.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

@media (min-width: 768px) {
    .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (min-width: 1024px) {
    .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .lg\:flex-row { flex-direction: row; }
}

@media (min-width: 1280px) {
    .xl\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

/* Flexbox utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-1 { flex: 1 1 0%; }
.items-center { align-items: center; }
.items-start { align-items: flex-start; }
.justify-between { justify-content: space-between; }
.justify-center { justify-content: center; }
.justify-end { justify-content: flex-end; }

/* Spacing */
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
.gap-6 { gap: 1.5rem; }

.p-4 { padding: 1rem; }
.p-6 { padding: 1.5rem; }
.p-8 { padding: 2rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }

.m-0 { margin: 0; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.mb-8 { margin-bottom: 2rem; }
.mt-1 { margin-top: 0.25rem; }
.mt-3 { margin-top: 0.75rem; }

/* Sizing */
.w-4 { width: 1rem; }
.w-5 { width: 1.25rem; }
.w-6 { width: 1.5rem; }
.w-16 { width: 4rem; }
.w-20 { width: 5rem; }
.w-24 { width: 6rem; }
.w-full { width: 100%; }

.h-4 { height: 1rem; }
.h-5 { height: 1.25rem; }
.h-6 { height: 1.5rem; }
.h-16 { height: 4rem; }
.h-20 { height: 5rem; }
.h-24 { height: 6rem; }

.max-w-md { max-width: 28rem; }

/* Colors */
.text-white { color: #ffffff; }
.text-gray-400 { color: #9ca3af; }
.text-gray-500 { color: #6b7280; }
.text-gray-600 { color: #4b5563; }
.text-gray-700 { color: #374151; }
.text-gray-800 { color: #1f2937; }
.text-gray-900 { color: #111827; }
.text-blue-100 { color: #dbeafe; }
.text-blue-600 { color: #2563eb; }
.text-red-200 { color: #fecaca; }
.text-orange-200 { color: #fed7aa; }
.text-green-200 { color: #bbf7d0; }

.bg-white { background-color: #ffffff; }
.bg-gray-50 { background-color: #f9fafb; }
.bg-gray-100 { background-color: #f3f4f6; }
.bg-gray-200 { background-color: #e5e7eb; }
.bg-transparent { background-color: transparent; }

/* Borders */
.border { border-width: 1px; }
.border-2 { border-width: 2px; }
.border-4 { border-width: 4px; }
.border-gray-100 { border-color: #f3f4f6; }
.border-gray-200 { border-color: #e5e7eb; }
.border-blue-100 { border-color: #dbeafe; }
.border-blue-500 { border-color: #3b82f6; }
.border-white { border-color: #ffffff; }

/* Border radius */
.rounded-xl { border-radius: 0.75rem; }
.rounded-2xl { border-radius: 1rem; }
.rounded-full { border-radius: 9999px; }

/* Shadows */
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
.shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

/* Typography */
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
.text-5xl { font-size: 3rem; line-height: 1; }

.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.font-bold { font-weight: 700; }

.text-center { text-align: center; }
.truncate { 
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Positioning */
.relative { position: relative; }
.absolute { position: absolute; }
.fixed { position: fixed; }

.top-0 { top: 0; }
.top-4 { top: 1rem; }
.right-4 { right: 1rem; }
.left-4 { left: 1rem; }
.bottom-0 { bottom: 0; }

.transform { transform: var(--tw-transform); }
.-translate-y-1\/2 { --tw-translate-y: -50%; }

/* Effects */
.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
.duration-300 { transition-duration: 300ms; }

.hover\:shadow-2xl:hover { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
.hover\:border-blue-500:hover { border-color: #3b82f6; }
.hover\:bg-white:hover { background-color: #ffffff; }
.hover\:text-gray-600:hover { color: #4b5563; }

.focus\:border-blue-500:focus { border-color: #3b82f6; }
.focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
.focus\:bg-white:focus { background-color: #ffffff; }

/* Backdrop effects */
.backdrop-blur-sm { backdrop-filter: blur(4px); }

/* Overflow */
.overflow-hidden { overflow: hidden; }

/* Z-index */
.z-10 { z-index: 10; }

/* Display utilities */
.block { display: block; }
.inline-block { display: inline-block; }
.inline-flex { display: inline-flex; }

/* Space utilities */
.space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
.space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 1rem; }

/* Glassmorphism effect */
.bg-white\/20 {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Custom premium styles */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Role display only styles */
.role-badge-large {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.role-badge-large.role-super_admin {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.role-badge-large.role-gerente {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
}

.role-badge-large.role-atendente {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
}

.role-badge-large.role-none {
    background: #f1f5f9;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

/* Modal styles */
body.modal-open {
    overflow: hidden;
}

/* Password Modal Styles */
#password-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

#password-modal.show {
    opacity: 1;
    visibility: visible;
}

#password-modal .modal-content {
    transform: scale(0.9) translateY(20px);
    transition: all 0.3s ease;
    max-height: 90vh;
    overflow-y: auto;
}

#password-modal.show .modal-content {
    transform: scale(1) translateY(0);
}

.change-password-btn {
    background: none;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.change-password-btn:hover {
    background: rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
}

.strength-fill.weak { background: #ef4444; }
.strength-fill.medium { background: #f59e0b; }
.strength-fill.strong { background: #10b981; }

#password-match-indicator.match {
    color: #10b981;
}

#password-match-indicator.no-match {
    color: #ef4444;
}

#password-form-status.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

#password-form-status.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

#password-form-status.info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

/* Responsive modal */
@media (max-width: 640px) {
    #password-modal .modal-content {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
}

/* Icon utilities */
.icon-users::before {
    content: "👥";
    font-size: inherit;
}

/* Responsive utilities */
@media (max-width: 767px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .filter-chips {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .role-options {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Adiciona suporte para Tailwind se não estiver carregado
if (typeof window.tailwind === 'undefined') {
    console.log('Tailwind CSS styles loaded via fallback');
}

// Inicialização adicional para o dashboard premium
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona classes de animação para cards
    const cards = document.querySelectorAll('.user-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 50}ms`;
        card.classList.add('animate-fade-in');
    });
});
</script>

<?php get_footer();
