<?php
/**
 * Arquivo de teste para verificar se a alteração de senha está funcionando
 * Acesse: /test-password-update.php
 */

// Carrega o WordPress
require_once('wp-config.php');

// Verifica se o usuário está logado
if (!is_user_logged_in()) {
    die('Você precisa estar logado para testar.');
}

$user_id = get_current_user_id();
$user = get_userdata($user_id);

echo "<h1>Teste de Alteração de Senha</h1>";
echo "<p>Usuário atual: {$user->user_login} (ID: {$user_id})</p>";

// Teste 1: Verificar se a função wp_set_password funciona
if (isset($_POST['test_password'])) {
    $new_password = 'teste123456';
    
    echo "<h2>Testando wp_set_password...</h2>";
    
    // Salva a senha atual para restaurar depois
    $old_password_hash = $user->user_pass;
    
    // Tenta alterar a senha
    wp_set_password($new_password, $user_id);
    
    // Verifica se funcionou
    $updated_user = get_userdata($user_id);
    if (wp_check_password($new_password, $updated_user->user_pass, $user_id)) {
        echo "<p style='color: green;'>✅ Senha alterada com sucesso!</p>";
        
        // Restaura a senha original
        global $wpdb;
        $wpdb->update(
            $wpdb->users,
            ['user_pass' => $old_password_hash],
            ['ID' => $user_id]
        );
        echo "<p>Senha original restaurada.</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha ao alterar a senha.</p>";
    }
}

// Teste 2: Verificar se a função AJAX está registrada
echo "<h2>Verificando registros AJAX:</h2>";
global $wp_filter;
if (isset($wp_filter['wp_ajax_update_user_profile'])) {
    echo "<p style='color: green;'>✅ Action 'wp_ajax_update_user_profile' está registrada.</p>";
} else {
    echo "<p style='color: red;'>❌ Action 'wp_ajax_update_user_profile' NÃO está registrada.</p>";
}

// Teste 3: Simular requisição AJAX
if (isset($_POST['test_ajax'])) {
    echo "<h2>Testando requisição AJAX...</h2>";
    
    $_POST['security'] = wp_create_nonce('user_profile_nonce');
    $_POST['password'] = 'teste123456';
    $_POST['password_confirm'] = 'teste123456';
    
    ob_start();
    amedis_handle_user_profile_update();
    $output = ob_get_clean();
    
    echo "<p>Saída da função: <code>{$output}</code></p>";
}

?>

<form method="post">
    <h2>Testes Disponíveis:</h2>
    <p>
        <button type="submit" name="test_password" value="1">Testar wp_set_password</button>
    </p>
    <p>
        <button type="submit" name="test_ajax" value="1">Testar função AJAX</button>
    </p>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
code { background: #f0f0f0; padding: 2px 4px; }
</style>