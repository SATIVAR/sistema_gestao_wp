# Design Document

## Overview

Este documento descreve o design para implementar a funcionalidade de verificação e alerta de receitas vencidas no dashboard de pedidos WooCommerce. A solução adiciona uma nova verificação de vencimento de receitas que funciona em paralelo com o sistema existente de alertas, mantendo toda a funcionalidade atual intacta.

## Architecture

### Componentes Afetados

1. **functions-woocommerce.php**: Função `get_order_display_data()` - adicionar lógica de verificação de vencimento
2. **dashboard-pedidos-woocommerce.php**: Template PHP - adicionar exibição do novo alerta
3. **assets/js/dashboard-woocommerce.js**: JavaScript - atualizar função `updateInfosExtra()` para suportar receitas vencidas

### Fluxo de Dados

```
Pedido WooCommerce
    ↓
get_order_display_data()
    ↓
Verificar receitas selecionadas
    ↓
Para cada receita: verificar data_vencimento
    ↓
Calcular status de vencimento
    ↓
Adicionar dados ao array de retorno
    ↓
Template PHP renderiza alertas
    ↓
JavaScript atualiza via AJAX quando necessário
```

## Components and Interfaces

### 1. Função de Verificação de Vencimento (PHP)

**Localização**: `functions-woocommerce.php` dentro da função `get_order_display_data()`

**Interface**:
```php
// Adicionar ao array de retorno da função get_order_display_data()
$data = [
    // ... dados existentes ...
    'tem_receitas_vencidas' => boolean,
    'count_receitas_vencidas' => integer,
    'receitas_vencidas_texto' => string
];
```

**Lógica**:
- Iterar sobre as receitas já carregadas no loop existente
- Para cada receita, verificar se `data_vencimento` existe e está no formato correto
- Comparar `data_vencimento` com a data atual
- Contar receitas vencidas e gerar texto apropriado

### 2. Renderização do Alerta (Template PHP)

**Localização**: `dashboard-pedidos-woocommerce.php` na seção `infos_extra`

**Interface**:
```php
<?php if ($tem_receitas_vencidas): ?>
    <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
        <!-- Ícone de alerta -->
        <svg>...</svg>
        <span><?php echo esc_html($receitas_vencidas_texto); ?></span>
    </div>
<?php endif; ?>
```

### 3. Atualização JavaScript (AJAX)

**Localização**: `assets/js/dashboard-woocommerce.js` na função `updateInfosExtra()`

**Interface**:
```javascript
function updateInfosExtra(orderId, data) {
    // ... código existente ...
    
    // Adicionar alerta de receitas vencidas se aplicável
    if (data.tem_receitas_vencidas) {
        infosHtml += `<div class="text-xs text-red-600 mt-1 flex items-center gap-1">
            <!-- SVG icon -->
            <span>${data.receitas_vencidas_texto}</span>
        </div>`;
    }
    
    // ... resto do código ...
}
```

## Data Models

### Estrutura de Dados de Receita Vencida

```php
// Adicionado ao array de retorno de get_order_display_data()
[
    'tem_receitas_vencidas' => false,      // boolean: indica se há receitas vencidas
    'count_receitas_vencidas' => 0,        // int: número de receitas vencidas
    'receitas_vencidas_texto' => '',       // string: texto do alerta ("Receita vencida" ou "Receitas vencidas")
]
```

### Formato de Data

- **Input**: `data_vencimento` no formato "dd/mm/yyyy" (formato ACF existente)
- **Processamento**: Conversão para DateTime para comparação
- **Comparação**: Data de vencimento < data atual = vencida

## Error Handling

### Tratamento de Erros na Verificação de Data

1. **Data vazia ou null**: Considerar receita como válida (não vencida)
2. **Formato de data inválido**: Considerar receita como válida por segurança
3. **Erro na conversão DateTime**: Log do erro e considerar receita como válida
4. **Receita não encontrada**: Ignorar na contagem

### Fallbacks

- Se houver erro na verificação de vencimento, o sistema continua funcionando normalmente
- Alertas existentes ("Sem receitas") não são afetados por erros na nova funcionalidade
- JavaScript mantém funcionalidade mesmo se dados de vencimento não estiverem disponíveis

## Testing Strategy

### Cenários de Teste

1. **Pedido sem receitas**: Deve exibir apenas "Sem receitas"
2. **Pedido com receitas válidas**: Não deve exibir alerta de vencimento
3. **Pedido com uma receita vencida**: Deve exibir "Receita vencida"
4. **Pedido com múltiplas receitas vencidas**: Deve exibir "Receitas vencidas"
5. **Pedido com receitas mistas** (algumas válidas, algumas vencidas): Deve exibir "Receitas vencidas"
6. **Receita sem data_vencimento**: Deve ser considerada válida
7. **Receita com data_vencimento inválida**: Deve ser considerada válida
8. **Atualização via AJAX**: Deve refletir mudanças no status de vencimento

### Testes de Performance

- Verificar que a adição da verificação de vencimento não impacta significativamente o tempo de carregamento do dashboard
- Confirmar que não há consultas SQL adicionais desnecessárias
- Validar que o cache existente de receitas continua funcionando

### Testes de Compatibilidade

- Verificar que alertas existentes continuam funcionando
- Confirmar que atualizações via AJAX mantêm funcionalidade
- Validar que o JavaScript não quebra em navegadores suportados