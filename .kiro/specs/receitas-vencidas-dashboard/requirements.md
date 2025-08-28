# Requirements Document

## Introduction

Esta especificação define a implementação de uma funcionalidade para verificar e alertar sobre receitas vencidas no dashboard de pedidos WooCommerce. O sistema deve adicionar alertas específicos que indiquem quando as receitas associadas a um pedido estão vencidas, mantendo o alerta existente "Sem receitas" e proporcionando melhor visibilidade sobre o status das receitas médicas.

## Requirements

### Requirement 1

**User Story:** Como um usuário do dashboard de pedidos, eu quero ver alertas específicos sobre receitas vencidas além do alerta existente "Sem receitas", para que eu possa identificar rapidamente pedidos que precisam de atenção devido a receitas expiradas.

#### Acceptance Criteria

1. WHEN um pedido possui receitas selecionadas THEN o sistema SHALL verificar a data de vencimento de cada receita
2. WHEN uma ou mais receitas estão vencidas (data_vencimento < data atual) THEN o sistema SHALL exibir o alerta "Receita vencida" ou "Receitas vencidas" (plural) ALÉM de outros alertas
3. WHEN todas as receitas estão válidas (data_vencimento >= data atual) THEN o sistema SHALL NOT exibir alerta de vencimento
4. WHEN um pedido não possui receitas selecionadas THEN o sistema SHALL continuar exibindo o alerta "Sem receitas" como atualmente
5. WHEN um pedido possui receitas mas algumas estão vencidas THEN o sistema SHALL exibir AMBOS os alertas se aplicável

### Requirement 2

**User Story:** Como um desenvolvedor, eu quero que a verificação de vencimento seja eficiente e não impacte a performance do dashboard, para que a experiência do usuário seja mantida.

#### Acceptance Criteria

1. WHEN o dashboard carrega os pedidos THEN a verificação de vencimento SHALL ser feita durante o processamento dos dados do pedido
2. WHEN uma receita não possui data_vencimento definida THEN o sistema SHALL considerar a receita como válida (não vencida)
3. WHEN a data_vencimento está em formato inválido THEN o sistema SHALL considerar a receita como válida por segurança
4. WHEN múltiplas receitas são verificadas THEN o sistema SHALL usar uma única consulta otimizada quando possível

### Requirement 3

**User Story:** Como um usuário do dashboard, eu quero que os alertas de receitas vencidas sejam visualmente distintos dos outros alertas, para que eu possa identificar rapidamente a criticidade da situação.

#### Acceptance Criteria

1. WHEN receitas estão vencidas THEN o alerta SHALL usar cor vermelha (text-red-600) com ícone de alerta
2. WHEN o alerta é para receita única vencida THEN o texto SHALL ser "Receita vencida"
3. WHEN o alerta é para múltiplas receitas vencidas THEN o texto SHALL ser "Receitas vencidas"
4. WHEN o alerta é exibido THEN ele SHALL manter o mesmo padrão visual dos outros alertas existentes

### Requirement 4

**User Story:** Como um usuário do sistema, eu quero que as atualizações via AJAX também reflitam corretamente o status de receitas vencidas, para que as informações sejam sempre atuais após modificações no pedido.

#### Acceptance Criteria

1. WHEN dados do pedido são atualizados via AJAX THEN o sistema SHALL recalcular o status de receitas vencidas
2. WHEN a função updateInfosExtra é chamada THEN ela SHALL incluir a verificação de receitas vencidas
3. WHEN receitas são adicionadas ou removidas de um pedido THEN o alerta SHALL ser atualizado automaticamente
4. WHEN dados são enviados para o JavaScript THEN eles SHALL incluir informação sobre receitas vencidas