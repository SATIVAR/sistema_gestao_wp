<?php
/**
 * Arquivo de Debug para Roles
 * 
 * ATENÇÃO: Este arquivo é apenas para debug. Remover após resolver o problema.
 * 
 * @package SativarApp
 * @version 1.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Função para debug e correção de roles
 */
function sativar_debug_and_fix_roles() {
    if (!current_user_can('administrator') || !WP_DEBUG) {
        return;
    }
    
    $current_user_id = get_current_user_id();
    $user_role_control = get_user_meta($current_user_id, 'user_role_control', true);
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px; border-radius: 5px;'>";
    echo "<h3>🔍 Debug de Roles - Usuário Atual</h3>";
    echo "<p><strong>User ID:</strong> " . $current_user_id . "</p>";
    echo "<p><strong>User Meta (user_role_control):</strong> " . json_encode($user_role_control) . "</p>";
    echo "<p><strong>Role Ativo:</strong> " . (sativar_get_user_active_role($current_user_id) ?: 'NENHUM') . "</p>";
    echo "<p><strong>Role Seguro:</strong> " . sativar_get_user_role_safe($current_user_id) . "</p>";
    
    // Se o usuário não tem role definido, oferece opção para definir
    if (!$user_role_control || empty($user_role_control)) {
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px;'>";
        echo "<p><strong>⚠️ PROBLEMA DETECTADO:</strong> Usuário não possui role customizado definido!</p>";
        echo "<p>Para testar o sistema, você pode definir um role temporário:</p>";
        
        if (isset($_GET['set_role']) && in_array($_GET['set_role'], ['super_admin', 'gerente', 'atendente'])) {
            $role_to_set = sanitize_text_field($_GET['set_role']);
            $new_role_control = array(
                'super_admin' => false,
                'gerente' => false,
                'atendente' => false
            );
            $new_role_control[$role_to_set] = true;
            
            update_user_meta($current_user_id, 'user_role_control', $new_role_control);
            echo "<p style='color: green;'>✅ Role '{$role_to_set}' definido com sucesso! Recarregue a página.</p>";
        } else {
            echo "<p>";
            echo "<a href='?set_role=super_admin' style='background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; margin: 5px; border-radius: 3px;'>Definir como Super Admin</a> ";
            echo "<a href='?set_role=gerente' style='background: #fd7e14; color: white; padding: 5px 10px; text-decoration: none; margin: 5px; border-radius: 3px;'>Definir como Gerente</a> ";
            echo "<a href='?set_role=atendente' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; margin: 5px; border-radius: 3px;'>Definir como Atendente</a>";
            echo "</p>";
        }
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px;'>";
        echo "<p style='color: green;'>✅ Role customizado está definido corretamente!</p>";
        echo "</div>";
    }
    
    // Testa as condições
    echo "<h4>🧪 Teste de Condições:</h4>";
    echo "<ul>";
    echo "<li>current_user_role === 'super_admin': " . ($current_user_role === 'super_admin' ? '✅ TRUE' : '❌ FALSE') . "</li>";
    echo "<li>current_user_role === 'gerente': " . ($current_user_role === 'gerente' ? '✅ TRUE' : '❌ FALSE') . "</li>";
    echo "<li>current_user_role === 'atendente': " . ($current_user_role === 'atendente' ? '✅ TRUE' : '❌ FALSE') . "</li>";
    echo "</ul>";
    
    echo "</div>";
}

// Adiciona o debug no admin
add_action('admin_notices', 'sativar_debug_and_fix_roles');

// Adiciona o debug no frontend para páginas específicas
add_action('wp_head', function() {
    if (is_page_template('dashboard-usuarios-sistema.php') && WP_DEBUG) {
        sativar_debug_and_fix_roles();
    }
});