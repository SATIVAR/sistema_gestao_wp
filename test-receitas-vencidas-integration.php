<?php
/**
 * Script de teste para validar a integração da funcionalidade de receitas vencidas
 * Este script testa todos os cenários definidos nos requirements
 */

// Incluir WordPress
require_once('wp-config.php');

// Verificar se WooCommerce está ativo
if (!class_exists('WooCommerce')) {
    die('WooCommerce não está ativo. Teste não pode ser executado.');
}

echo "<h1>Teste de Integração - Receitas Vencidas Dashboard</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
    .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>\n";

/**
 * Função auxiliar para criar receita de teste
 */
function criar_receita_teste($data_vencimento, $nome = 'Receita Teste') {
    $receita_id = wp_insert_post([
        'post_type' => 'receitas',
        'post_title' => $nome,
        'post_status' => 'publish'
    ]);
    
    if ($receita_id && !is_wp_error($receita_id)) {
        update_field('data_vencimento', $data_vencimento, $receita_id);
        return $receita_id;
    }
    
    return false;
}

/**
 * Função auxiliar para criar pedido de teste
 */
function criar_pedido_teste($receitas_ids = []) {
    $order = wc_create_order();
    if (!$order || is_wp_error($order)) {
        return false;
    }
    
    // Adicionar produto simples
    $product = new WC_Product_Simple();
    $product->set_name('Produto Teste');
    $product->set_regular_price(10.00);
    $product->set_status('publish');
    $product_id = $product->save();
    
    $order->add_product(wc_get_product($product_id), 1);
    $order->calculate_totals();
    $order_id = $order->save();
    
    // Associar receitas se fornecidas
    if (!empty($receitas_ids)) {
        update_field('receitas_selecionadas', $receitas_ids, $order_id);
    }
    
    return $order_id;
}

/**
 * Teste 1: Verificar função auxiliar hg_is_receita_vencida
 */
echo "<div class='test-section info'>";
echo "<h2>Teste 1: Função hg_is_receita_vencida</h2>";

$testes_data = [
    ['data' => '', 'esperado' => false, 'descricao' => 'Data vazia'],
    ['data' => null, 'esperado' => false, 'descricao' => 'Data null'],
    ['data' => 'formato_invalido', 'esperado' => false, 'descricao' => 'Formato inválido'],
    ['data' => date('d/m/Y', strtotime('-1 day')), 'esperado' => true, 'descricao' => 'Data vencida (ontem)'],
    ['data' => date('d/m/Y'), 'esperado' => false, 'descricao' => 'Data hoje'],
    ['data' => date('d/m/Y', strtotime('+1 day')), 'esperado' => false, 'descricao' => 'Data futura (amanhã)'],
];

$todos_passaram = true;
foreach ($testes_data as $teste) {
    $resultado = hg_is_receita_vencida($teste['data']);
    $passou = ($resultado === $teste['esperado']);
    $todos_passaram = $todos_passaram && $passou;
    
    $status_class = $passou ? 'success' : 'error';
    echo "<div class='$status_class'>";
    echo "<strong>{$teste['descricao']}:</strong> ";
    echo "Data: '{$teste['data']}' | ";
    echo "Esperado: " . ($teste['esperado'] ? 'true' : 'false') . " | ";
    echo "Resultado: " . ($resultado ? 'true' : 'false') . " | ";
    echo "Status: " . ($passou ? 'PASSOU' : 'FALHOU');
    echo "</div>";
}

echo "<p><strong>Resultado Geral do Teste 1:</strong> " . ($todos_passaram ? "✅ PASSOU" : "❌ FALHOU") . "</p>";
echo "</div>";

/**
 * Teste 2: Cenários de pedidos com receitas
 */
echo "<div class='test-section info'>";
echo "<h2>Teste 2: Cenários de Pedidos com Receitas</h2>";

// Criar receitas de teste
$receita_valida = criar_receita_teste(date('d/m/Y', strtotime('+30 days')), 'Receita Válida');
$receita_vencida = criar_receita_teste(date('d/m/Y', strtotime('-5 days')), 'Receita Vencida');
$receita_sem_data = criar_receita_teste('', 'Receita Sem Data');

echo "<p>Receitas criadas para teste:</p>";
echo "<ul>";
echo "<li>Receita Válida (ID: $receita_valida) - Vence em 30 dias</li>";
echo "<li>Receita Vencida (ID: $receita_vencida) - Venceu há 5 dias</li>";
echo "<li>Receita Sem Data (ID: $receita_sem_data) - Sem data de vencimento</li>";
echo "</ul>";

// Cenários de teste
$cenarios = [
    [
        'nome' => 'Pedido sem receitas',
        'receitas' => [],
        'esperado_tem_receitas' => false,
        'esperado_tem_vencidas' => false,
        'esperado_count_vencidas' => 0,
        'esperado_texto' => ''
    ],
    [
        'nome' => 'Pedido com receita válida',
        'receitas' => [$receita_valida],
        'esperado_tem_receitas' => true,
        'esperado_tem_vencidas' => false,
        'esperado_count_vencidas' => 0,
        'esperado_texto' => ''
    ],
    [
        'nome' => 'Pedido com uma receita vencida',
        'receitas' => [$receita_vencida],
        'esperado_tem_receitas' => true,
        'esperado_tem_vencidas' => true,
        'esperado_count_vencidas' => 1,
        'esperado_texto' => 'Receita vencida'
    ],
    [
        'nome' => 'Pedido com múltiplas receitas vencidas',
        'receitas' => [$receita_vencida, criar_receita_teste(date('d/m/Y', strtotime('-10 days')), 'Receita Vencida 2')],
        'esperado_tem_receitas' => true,
        'esperado_tem_vencidas' => true,
        'esperado_count_vencidas' => 2,
        'esperado_texto' => 'Receitas vencidas'
    ],
    [
        'nome' => 'Pedido com receitas mistas',
        'receitas' => [$receita_valida, $receita_vencida],
        'esperado_tem_receitas' => true,
        'esperado_tem_vencidas' => true,
        'esperado_count_vencidas' => 1,
        'esperado_texto' => 'Receita vencida'
    ],
    [
        'nome' => 'Pedido com receita sem data',
        'receitas' => [$receita_sem_data],
        'esperado_tem_receitas' => true,
        'esperado_tem_vencidas' => false,
        'esperado_count_vencidas' => 0,
        'esperado_texto' => ''
    ]
];

$todos_cenarios_passaram = true;
foreach ($cenarios as $cenario) {
    echo "<div class='test-section'>";
    echo "<h3>{$cenario['nome']}</h3>";
    
    // Criar pedido de teste
    $pedido_id = criar_pedido_teste($cenario['receitas']);
    if (!$pedido_id) {
        echo "<div class='error'>Erro ao criar pedido de teste</div>";
        $todos_cenarios_passaram = false;
        continue;
    }
    
    // Obter dados do pedido
    $order = wc_get_order($pedido_id);
    $data = get_order_display_data($order);
    
    // Verificar resultados
    $resultados = [
        'tem_receitas' => $data['tem_receitas'] ?? false,
        'tem_receitas_vencidas' => $data['tem_receitas_vencidas'] ?? false,
        'count_receitas_vencidas' => $data['count_receitas_vencidas'] ?? 0,
        'receitas_vencidas_texto' => $data['receitas_vencidas_texto'] ?? ''
    ];
    
    $cenario_passou = true;
    $cenario_passou = $cenario_passou && ($resultados['tem_receitas'] === $cenario['esperado_tem_receitas']);
    $cenario_passou = $cenario_passou && ($resultados['tem_receitas_vencidas'] === $cenario['esperado_tem_vencidas']);
    $cenario_passou = $cenario_passou && ($resultados['count_receitas_vencidas'] === $cenario['esperado_count_vencidas']);
    $cenario_passou = $cenario_passou && ($resultados['receitas_vencidas_texto'] === $cenario['esperado_texto']);
    
    $todos_cenarios_passaram = $todos_cenarios_passaram && $cenario_passou;
    
    $status_class = $cenario_passou ? 'success' : 'error';
    echo "<div class='$status_class'>";
    echo "<strong>Pedido ID:</strong> $pedido_id<br>";
    echo "<strong>Receitas associadas:</strong> " . implode(', ', $cenario['receitas']) . "<br>";
    echo "<strong>Resultados:</strong><br>";
    echo "<pre>" . print_r($resultados, true) . "</pre>";
    echo "<strong>Status:</strong> " . ($cenario_passou ? "✅ PASSOU" : "❌ FALHOU");
    echo "</div>";
    
    // Limpar pedido de teste
    wp_delete_post($pedido_id, true);
    echo "</div>";
}

echo "<p><strong>Resultado Geral do Teste 2:</strong> " . ($todos_cenarios_passaram ? "✅ PASSOU" : "❌ FALHOU") . "</p>";
echo "</div>";

/**
 * Teste 3: Verificar template PHP
 */
echo "<div class='test-section info'>";
echo "<h2>Teste 3: Verificação do Template PHP</h2>";

// Verificar se o arquivo existe
$template_file = 'dashboard-pedidos-woocommerce.php';
if (file_exists($template_file)) {
    $template_content = file_get_contents($template_file);
    
    // Verificar se contém o código de receitas vencidas
    $checks = [
        'tem_receitas_vencidas' => strpos($template_content, 'tem_receitas_vencidas') !== false,
        'receitas_vencidas_texto' => strpos($template_content, 'receitas_vencidas_texto') !== false,
        'alerta_vencimento' => strpos($template_content, 'text-red-600') !== false && strpos($template_content, 'receitas_vencidas_texto') !== false,
        'svg_alerta' => strpos($template_content, 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374') !== false
    ];
    
    $template_ok = true;
    foreach ($checks as $check => $resultado) {
        $template_ok = $template_ok && $resultado;
        $status_class = $resultado ? 'success' : 'error';
        echo "<div class='$status_class'>";
        echo "<strong>$check:</strong> " . ($resultado ? "✅ ENCONTRADO" : "❌ NÃO ENCONTRADO");
        echo "</div>";
    }
    
    echo "<p><strong>Resultado do Template:</strong> " . ($template_ok ? "✅ PASSOU" : "❌ FALHOU") . "</p>";
} else {
    echo "<div class='error'>Template não encontrado: $template_file</div>";
}
echo "</div>";

/**
 * Teste 4: Verificar JavaScript
 */
echo "<div class='test-section info'>";
echo "<h2>Teste 4: Verificação do JavaScript</h2>";

$js_file = 'assets/js/dashboard-woocommerce.js';
if (file_exists($js_file)) {
    $js_content = file_get_contents($js_file);
    
    // Verificar se contém o código de receitas vencidas
    $js_checks = [
        'updateInfosExtra' => strpos($js_content, 'updateInfosExtra') !== false,
        'tem_receitas_vencidas' => strpos($js_content, 'tem_receitas_vencidas') !== false,
        'receitas_vencidas_texto' => strpos($js_content, 'receitas_vencidas_texto') !== false,
        'alerta_js' => strpos($js_content, 'text-red-600') !== false && strpos($js_content, 'receitas_vencidas_texto') !== false
    ];
    
    $js_ok = true;
    foreach ($js_checks as $check => $resultado) {
        $js_ok = $js_ok && $resultado;
        $status_class = $resultado ? 'success' : 'error';
        echo "<div class='$status_class'>";
        echo "<strong>$check:</strong> " . ($resultado ? "✅ ENCONTRADO" : "❌ NÃO ENCONTRADO");
        echo "</div>";
    }
    
    echo "<p><strong>Resultado do JavaScript:</strong> " . ($js_ok ? "✅ PASSOU" : "❌ FALHOU") . "</p>";
} else {
    echo "<div class='error'>Arquivo JavaScript não encontrado: $js_file</div>";
}
echo "</div>";

/**
 * Teste 5: Performance
 */
echo "<div class='test-section info'>";
echo "<h2>Teste 5: Verificação de Performance</h2>";

// Criar múltiplos pedidos para teste de performance
$start_time = microtime(true);

$pedidos_teste = [];
for ($i = 0; $i < 10; $i++) {
    $receitas = [];
    if ($i % 2 == 0) {
        $receitas[] = $receita_valida;
    }
    if ($i % 3 == 0) {
        $receitas[] = $receita_vencida;
    }
    
    $pedido_id = criar_pedido_teste($receitas);
    if ($pedido_id) {
        $pedidos_teste[] = $pedido_id;
    }
}

// Processar todos os pedidos
foreach ($pedidos_teste as $pedido_id) {
    $order = wc_get_order($pedido_id);
    $data = get_order_display_data($order);
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time) * 1000; // em milissegundos

echo "<div class='info'>";
echo "<strong>Pedidos processados:</strong> " . count($pedidos_teste) . "<br>";
echo "<strong>Tempo de execução:</strong> " . number_format($execution_time, 2) . " ms<br>";
echo "<strong>Tempo médio por pedido:</strong> " . number_format($execution_time / count($pedidos_teste), 2) . " ms<br>";

$performance_ok = $execution_time < 1000; // Menos de 1 segundo para 10 pedidos
echo "<strong>Performance:</strong> " . ($performance_ok ? "✅ ACEITÁVEL" : "⚠️ PODE PRECISAR OTIMIZAÇÃO");
echo "</div>";

// Limpar pedidos de teste
foreach ($pedidos_teste as $pedido_id) {
    wp_delete_post($pedido_id, true);
}

echo "</div>";

/**
 * Limpeza
 */
echo "<div class='test-section warning'>";
echo "<h2>Limpeza</h2>";
echo "<p>Removendo dados de teste...</p>";

// Remover receitas de teste
$receitas_teste = [$receita_valida, $receita_vencida, $receita_sem_data];
foreach ($receitas_teste as $receita_id) {
    if ($receita_id) {
        wp_delete_post($receita_id, true);
        echo "<div>Receita $receita_id removida</div>";
    }
}

echo "<p>✅ Limpeza concluída</p>";
echo "</div>";

/**
 * Resumo Final
 */
echo "<div class='test-section " . ($todos_passaram && $todos_cenarios_passaram ? 'success' : 'error') . "'>";
echo "<h2>Resumo Final dos Testes</h2>";
echo "<ul>";
echo "<li><strong>Teste 1 - Função auxiliar:</strong> " . ($todos_passaram ? "✅ PASSOU" : "❌ FALHOU") . "</li>";
echo "<li><strong>Teste 2 - Cenários de pedidos:</strong> " . ($todos_cenarios_passaram ? "✅ PASSOU" : "❌ FALHOU") . "</li>";
echo "<li><strong>Teste 3 - Template PHP:</strong> " . (isset($template_ok) && $template_ok ? "✅ PASSOU" : "❌ FALHOU") . "</li>";
echo "<li><strong>Teste 4 - JavaScript:</strong> " . (isset($js_ok) && $js_ok ? "✅ PASSOU" : "❌ FALHOU") . "</li>";
echo "<li><strong>Teste 5 - Performance:</strong> " . ($performance_ok ? "✅ PASSOU" : "⚠️ ATENÇÃO") . "</li>";
echo "</ul>";

$resultado_geral = $todos_passaram && $todos_cenarios_passaram && 
                   (isset($template_ok) && $template_ok) && 
                   (isset($js_ok) && $js_ok);

echo "<h3>Resultado Geral: " . ($resultado_geral ? "✅ TODOS OS TESTES PASSARAM" : "❌ ALGUNS TESTES FALHARAM") . "</h3>";
echo "</div>";

echo "<p><em>Teste concluído em " . date('Y-m-d H:i:s') . "</em></p>";
?>