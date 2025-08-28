# Implementation Plan

- [x] 1. Implementar lógica de verificação de receitas vencidas no backend




  - Modificar a função `get_order_display_data()` em `functions-woocommerce.php`
  - Adicionar verificação de vencimento durante o processamento das receitas existentes
  - Calcular contagem de receitas vencidas e gerar texto apropriado
  - Adicionar novos campos ao array de retorno: `tem_receitas_vencidas`, `count_receitas_vencidas`, `receitas_vencidas_texto`
  - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3_

- [x] 2. Adicionar renderização do alerta de receitas vencidas no template PHP





  - Modificar `dashboard-pedidos-woocommerce.php` na seção `infos_extra`
  - Adicionar condicional para exibir alerta de receitas vencidas quando aplicável
  - Usar ícone e estilo visual consistente com alertas existentes
  - Manter alerta "Sem receitas" funcionando independentemente
  - _Requirements: 1.2, 1.4, 1.5, 3.1, 3.2, 3.3, 3.4_

- [x] 3. Atualizar função JavaScript para suportar receitas vencidas via AJAX





  - Modificar função `updateInfosExtra()` em `assets/js/dashboard-woocommerce.js`
  - Adicionar lógica para renderizar alerta de receitas vencidas quando dados estão disponíveis
  - Manter compatibilidade com atualizações existentes de informações extras
  - Garantir que o HTML gerado seja idêntico ao template PHP
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 4. Implementar função auxiliar para verificação de data de vencimento





  - Criar função helper para validar e comparar datas de vencimento
  - Implementar tratamento de erros para datas inválidas ou vazias
  - Considerar receitas sem data como válidas por segurança
  - Usar formato de data existente (dd/mm/yyyy) do sistema ACF
  - _Requirements: 2.2, 2.3_

- [ ] 5. Testar integração e compatibilidade com funcionalidades existentes




  - Verificar que alertas "Sem receitas" continuam funcionando
  - Testar cenários com receitas válidas, vencidas e mistas
  - Validar atualizações via AJAX mantêm consistência
  - Confirmar que performance não foi impactada significativamente
  - _Requirements: 1.4, 1.5, 4.1, 4.2_