<?php
/**
 * The template for editing 'Saídas' (Outputs/Expenses)
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage CAJU
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Editar Saída
*/
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');

$saida_id = isset($_GET['saida_id']) ? intval($_GET['saida_id']) : 0;

if (!$saida_id || get_post_type($saida_id) !== 'saidas') {
    wp_redirect(home_url('/todas-saidas')); // Redireciona se ID inválido ou não for uma saída
    exit;
}

$saida_post = get_post($saida_id);
$saida_title_parts = explode(' - ', $saida_post->post_title);
$saida_mes_existente = $saida_title_parts[0] ?? '';
$saida_titulo_extra_existente = $saida_title_parts[1] ?? '';

$saida_total_existente = get_post_meta($saida_id, 'saida_total', true);

// Obter termos da taxonomia categoria-saida
$categorias_saida = get_terms(array(
    'taxonomy'   => 'categoria-saida',
    'hide_empty' => false,
));

// Obter a categoria atual da saída
$current_saida_categories = get_the_terms($saida_id, 'categoria-saida');
$saida_categoria_existente = !empty($current_saida_categories) ? $current_saida_categories[0]->term_id : '';

// Buscar itens associados a esta saída para pré-carregar no JS
$itens_existentes_json = get_post_meta($saida_id, 'itens_saida', true);
$itens_existentes = !empty($itens_existentes_json) ? json_decode($itens_existentes_json, true) : [];

// Garantir que itens antigos sem UUID tenham um
if (is_array($itens_existentes)) {
    foreach ($itens_existentes as &$item) {
        if (empty($item['temp_uuid'])) {
            $item['temp_uuid'] = 'legacy_' . uniqid();
        }
    }
}

$nonceEditarItem = wp_create_nonce('editar_item_saida_individual');
?>
<?php get_template_part('header', 'user') ?>
            
    <main class="mt-5  bg-transparent pb-[60px]">
        <div class="uk-container">

            <div class="flex">
                <div class="md:w-[100%]">

                    <div class="card card-border mb-5">
                        <div class="card-body">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="flex items-center">
                                            <h6 class="font-medium">Editar Saída #<?php echo $saida_id; ?></h6>
                                        </div>
                                        <div class="flex">                                                               
                                            <span>Edite os detalhes da sua despesa existente.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <a href="<?php echo home_url("/todas-saidas"); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Voltar</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Sucesso -->
<div id="modal-success" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-green-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Sucesso!</h2>
            <p class="text-gray-600 mb-6" id="success-message"></p>
            <div class="flex justify-center gap-4">
                <a href="javascript:location.reload();" class="uk-button uk-button-default rounded-md" type="button">Continuar Editando</a>
                <a href="<?php echo home_url('/todas-saidas'); ?>" class="uk-button bg-green-800 rounded-md text-white hover:opacity-80">Ver Todas</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Erro -->
<div id="modal-error" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-red-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Erro!</h2>
            <p class="text-gray-600 mb-6" id="error-message"></p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Tentar Novamente</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Remoção -->
<div id="modal-confirm-remove" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-orange-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Confirmar Remoção</h2>
            <p class="text-gray-600 mb-6">Esta nota fiscal será removida da saída. A ação só será efetivada após salvar o formulário completo.</p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Cancelar</button>
                <button id="confirm-remove-btn" class="uk-button bg-red-600 rounded-md text-white hover:opacity-80" type="button">Confirmar Remoção</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Edição -->
<div id="modal-confirm-edit" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-blue-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Edição Salva</h2>
            <p class="text-gray-600 mb-6">As alterações foram salvas temporariamente. Para confirmar definitivamente, clique em "Salvar" no formulário principal.</p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Entendi</button>
            </div>
        </div>
    </div>
</div>



                    <form id="saida-form" class="space-y-6" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editar_saida_frontend">
                        <input type="hidden" name="saida_id" value="<?php echo esc_attr($saida_id); ?>">
                        <?php wp_nonce_field('editar_saida_nonce', 'editar_saida_nonce_field'); ?>

                        
                        <div class="card card-border my-5 shadow-lg rounded-lg">
                            <div class="card-body p-6">

                                <div class="flex flex-wrap -mx-3 mb-6 lg:flex-row flex-col">
                                    <div class="w-full lg:w-3/4 px-3">
                                        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                            <span>Informações Gerais da Saída</span>
                                        </h3>  
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div>
                                                <label for="select-categoria-saida" class="block text-sm font-semibold text-gray-700 mb-2">
                                                  Categoria
                                                </label>
                                                <select id="select-categoria-saida" name="saida_categoria" class="w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200" required>
                                                  <option value="">Selecione a categoria</option>
                                                  <?php foreach ($categorias_saida as $categoria) : ?>
                                                      <option value="<?php echo esc_attr($categoria->term_id); ?>" <?php selected($saida_categoria_existente, $categoria->term_id); ?>><?php echo esc_html($categoria->name); ?></option>
                                                  <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="select-mes" class="block text-sm font-semibold text-gray-700 mb-2">
                                                  Mês de Referência
                                                </label>
                                                <select id="select-mes" name="saida_mes" class="w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200" required>
                                                  <option value="">Selecione o mês</option>
                                                  <?php
                                                  $meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
                                                  foreach ($meses as $mes) : ?>
                                                      <option value="<?php echo esc_attr($mes); ?>" <?php selected($saida_mes_existente, $mes); ?>><?php echo esc_html($mes); ?></option>
                                                  <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="saida_titulo_extra" class="block text-sm font-semibold text-gray-700 mb-2">
                                                  Título da Saída (Ex: Manutenção Geral)
                                                </label>
                                                <input
                                                  type="text"
                                                  name="saida_titulo_extra"
                                                  id="saida_titulo_extra"
                                                  class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                                  placeholder="Ex: Pagamento de Aluguel"
                                                  value="<?php echo esc_attr($saida_titulo_extra_existente); ?>"
                                                  required
                                                />
                                            </div>
                                        </div>

                                    </div>


                                    <div class="w-full lg:w-1/4 px-3 mt-8 lg:mt-0">
                                        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                            <span>Resumo Total da Saída</span>
                                        </h3>    

                                        <div class="grid grid-cols-1 gap-4">
                                            <div class="">
                                                <label for="saida_total" class="block text-sm font-semibold text-gray-700 mb-2">
                                                  Valor Total Geral (R$)
                                                </label>
                                                <input
                                                  type="number"
                                                  name="saida_total"
                                                  id="saida_total"
                                                  step="0.01"
                                                  min="0"
                                                  value="<?php echo esc_attr(number_format((float)$saida_total_existente, 2, '.', '')); ?>"
                                                  class="border border-gray-300 bg-gray-100 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                                  placeholder="0.00"
                                                  readonly
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- Flex -->
                            </div><!-- Card Body -->
                        </div><!-- Card -->


                        <div class="card card-border my-6  shadow-lg rounded-lg">
                            <div class="card-body p-3">
                                <div class="w-full px-3">
                                    <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                        <span>Adicionar Nova Nota Fiscal</span>
                                    </h3>                        
                                    <div class="">
                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end">
                                            <div class="md:col-span-2">
                                                <label for="item_title" class="block text-sm font-semibold text-gray-700 mb-2">Número/Descrição da Nota Fiscal</label>
                                                <input type="text" id="item_title" class="border border-gray-300 p-2 w-full rounded-md focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="Ex: Material de escritório - NF 001">
                                            </div>
                                            <div>
                                                <label for="item_preco_unit" class="block text-sm font-semibold text-gray-700 mb-2">Valor da Nota Fiscal (R$)</label>
                                                <input type="number" id="item_preco_unit" step="0.01" min="0" value="0.00" class="border border-gray-300 p-2 w-full rounded-md focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="0.00">
                                            </div>
                                            <div>
                                                <label for="item_discount" class="block text-sm font-semibold text-gray-700 mb-2">Desconto (R$)</label>
                                                <input type="number" id="item_discount" step="0.01" min="0" value="0.00" class="border border-gray-300 p-2 w-full rounded-md focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="0.00">
                                            </div>
                                            <div>
                                                <label for="item_extra" class="block text-sm font-semibold text-gray-700 mb-2">Valor Extra (R$)</label>
                                                <input type="number" id="item_extra" step="0.01" min="0" value="0.00" class="border border-gray-300 p-2 w-full rounded-md focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="0.00">
                                            </div>
                                            <div>
                                                <label for="item_frete" class="block text-sm font-semibold text-gray-700 mb-2">Frete (R$)</label>
                                                <input type="number" id="item_frete" step="0.01" min="0" value="0.00" class="border border-gray-300 p-2 w-full rounded-md focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="0.00">
                                            </div>
<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Anexar Nota Fiscal</label>
    
    <div id="upload-zone-nf" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-nf" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        
        <div id="upload-placeholder-nf" class="upload-placeholder">
            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium text-sm">Clique ou arraste o arquivo</p>
            <p class="text-xs text-gray-400">PDF, DOC, JPG (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-nf" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-2">
                <div class="flex items-center space-x-2">
                    <div id="file-icon-nf" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-nf" class="text-xs font-medium text-gray-900"></p>
                        <p id="file-size-nf" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-nf" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="image-preview-nf" class="hidden mt-2 max-w-full h-32 object-cover rounded-lg">
        </div>
    </div>
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Anexar Comp. Bancário</label>
    
    <div id="upload-zone-comp" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-comp" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        
        <div id="upload-placeholder-comp" class="upload-placeholder">
            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium text-sm">Clique ou arraste o arquivo</p>
            <p class="text-xs text-gray-400">PDF, DOC, JPG (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-comp" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-2">
                <div class="flex items-center space-x-2">
                    <div id="file-icon-comp" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-comp" class="text-xs font-medium text-gray-900"></p>
                        <p id="file-size-comp" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-comp" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                </div>
            <img id="image-preview-comp" class="hidden mt-2 max-w-full h-32 object-cover rounded-lg">
        </div>
    </div>
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Anexar Recibo</label>
    
    <div id="upload-zone-recibo" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-purple-400 hover:bg-purple-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-recibo" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        
        <div id="upload-placeholder-recibo" class="upload-placeholder">
            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium text-sm">Clique ou arraste o arquivo</p>
            <p class="text-xs text-gray-400">PDF, DOC, JPG (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-recibo" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-2">
                <div class="flex items-center space-x-2">
                    <div id="file-icon-recibo" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-recibo" class="text-xs font-medium text-gray-900"></p>
                        <p id="file-size-recibo" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-recibo" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                </div>
            <img id="image-preview-recibo" class="hidden mt-2 max-w-full h-32 object-cover rounded-lg">
        </div>
    </div>
</div>

                                            <div class="md:col-span-full">
                                                <button type="button" id="adicionar-item" class="bg-blue-500 text-white px-4 py-2 rounded font-medium shadow-md hover:bg-green-700 transition-all duration-300">Adicionar Nota Fiscal</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="itens_json" id="itens_json">
                                <div id="file_inputs_container"></div> <!-- Container for dynamic file inputs -->

                            </div>
                        </div>

                        <div class="card card-border my-6  shadow-lg rounded-lg">
                            <div class="card-body p-3">
                                <div class="w-full px-3">
                                    <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                        <span>Notas Fiscais / Detalhes dos Gastos</span>
                                    </h3>

                                    <!-- Container para os cards de itens -->
                                    <div id="itens-container" class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-6 px-2"></div>

                                </div>
                            </div>
                        </div>


                        <div class="card card-border mb-5 shadow-lg rounded-lg">
                            <div class="card-body flex justify-between p-6">
                                <div>
                                    <a href="<?php bloginfo("url"); ?>/todas-saidas" class="bg-red-800 text-white px-4 py-2 rounded text-sm">Cancelar</a>
                                </div>
                                <div>
                                    <div>
                                    <button type="submit" class="flex space-x-1 bg-green-800 text-white px-4 py-2 rounded text-sm">
                                        <span>Salvar</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class=" h-5 w-5 text-white ml-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 96l0 320c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-245.5c0-4.2-1.7-8.3-4.7-11.3l33.9-33.9c12 12 18.7 28.3 18.7 45.3L448 416c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l245.5 0c17 0 33.3 6.7 45.3 18.7l74.5 74.5-33.9 33.9L320.8 84.7c-.3-.3-.5-.5-.8-.8L320 184c0 13.3-10.7 24-24 24l-192 0c-13.3 0-24-10.7-24-24L80 80 64 80c-8.8 0-16 7.2-16 16zm80-16l0 80 144 0 0-80L128 80zm32 240a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"></path></svg>                                  
                                    </button>
                                </div>
                                </div>                                                
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </main>

<script>
window.nonceEditarItem = '<?php echo $nonceEditarItem; ?>';
// Classe ModernFileUpload (copiar da nova-receita.php)
class ModernFileUpload {
    constructor(zoneId, inputId, type) {
        this.zone = document.getElementById(`upload-zone-${zoneId}`);
        this.input = document.getElementById(`file-input-${zoneId}`);
        this.placeholder = document.getElementById(`upload-placeholder-${zoneId}`);
        this.preview = document.getElementById(`upload-preview-${zoneId}`);
        this.removeBtn = document.getElementById(`remove-file-${zoneId}`);
        this.fileName = document.getElementById(`file-name-${zoneId}`);
        this.fileSize = document.getElementById(`file-size-${zoneId}`);
        this.fileIcon = document.getElementById(`file-icon-${zoneId}`);
        this.imagePreview = document.getElementById(`image-preview-${zoneId}`);
        this.type = type;
        this.selectedFile = null;
        
        this.init();
    }
    
    init() {
        this.zone.addEventListener('click', () => this.input.click());
        this.input.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));
        this.removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeFile();
        });
        
        this.zone.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.zone.addEventListener('drop', (e) => this.handleDrop(e));
        this.zone.addEventListener('dragleave', () => this.handleDragLeave());
    }
    
    handleDragOver(e) {
        e.preventDefault();
        this.zone.classList.add('border-green-500', 'bg-green-100');
    }
    
    handleDragLeave() {
        this.zone.classList.remove('border-green-500', 'bg-green-100');
    }
    
    handleDrop(e) {
        e.preventDefault();
        this.handleDragLeave();
        const file = e.dataTransfer.files[0];
        if (file) this.handleFileSelect(file);
    }
    
    handleFileSelect(file) {
        if (!this.validateFile(file)) return;
        this.selectedFile = file;
        this.showPreview(file);
    }

    getSelectedFile() {
        return this.selectedFile;
    }    
    
    validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use PNG, JPG ou PDF.');
            return false;
        }
        
        if (file.size > maxSize) {
            alert('Arquivo muito grande. Máximo 10MB.');
            return false;
        }
        
        return true;
    }
    
    showPreview(file) {
        this.placeholder.classList.add('hidden');
        this.preview.classList.remove('hidden');
        
        this.fileName.textContent = file.name;
        this.fileSize.textContent = this.formatFileSize(file.size);
        
        if (file.type.startsWith('image/')) {
            this.fileIcon.innerHTML = `<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview.src = e.target.result;
                this.imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            this.fileIcon.innerHTML = `<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>`;
        }
    }
    
    removeFile() {
        this.placeholder.classList.remove('hidden');
        this.preview.classList.add('hidden');
        this.imagePreview.classList.add('hidden');
        this.input.value = '';
        this.selectedFile = null;
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Inicializar uploads modernos
const uploadNF = new ModernFileUpload('nf', 'file-input-nf', 'nf');
const uploadComp = new ModernFileUpload('comp', 'file-input-comp', 'comp');
const uploadRecibo = new ModernFileUpload('recibo', 'file-input-recibo', 'recibo');

jQuery(function($) {

    let itens = <?php echo json_encode($itens_existentes); ?>.map(item => ({ ...item, is_deleted: false, is_new: false }));
    const filesToUpload = {};

    let itemToRemoveUUID = null;

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    const formatBRL = v => parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    function renderItens() {
        const container = $('#itens-container').empty();
        itens.filter(item => !item.is_deleted).forEach(item => {
            const totalNota = (parseFloat(item.preco_unit) || 0) + (parseFloat(item.item_extra) || 0) + (parseFloat(item.item_frete) || 0) - (parseFloat(item.item_discount) || 0);
            const identifier = item.temp_uuid;

            // Função para criar links ou nomes de arquivos
            const createFileLink = (url, type, file) => {
                let displayName = type.charAt(0).toUpperCase() + type.slice(1);
                if (type === 'file') displayName = 'Nota Fiscal';
                if (type === 'comprovante') displayName = 'Comp. Bancário';
                
                if (url) {
                    return `<a href="${url}" target="_blank" class="text-sm text-blue-600 hover:underline">Ver ${displayName}</a> 
                            <button type="button" class="text-red-500 hover:text-red-700 text-xs ml-2 remover-arquivo" data-type="${type}">Remover</button>`;
                }
                if (file) return `<span class="text-sm text-green-600 font-semibold">${file.name} (Novo)</span>`;
                return '<span class="text-sm text-gray-500">Nenhum</span>';
            };

            // Se está em modo edição, renderiza formulário inline
            if (item.is_editing) {
                const nfLinkHtml = createFileLink(item.file_url, 'file', filesToUpload[`item_file_upload_${identifier}`]);
                const comprovanteLinkHtml = createFileLink(item.comprovante_url, 'comprovante', filesToUpload[`item_comprovante_upload_${identifier}`]);
                const reciboLinkHtml = createFileLink(item.recibo_url, 'recibo', filesToUpload[`item_recibo_upload_${identifier}`]);
                const cardHtml = `
                <div class="item-edit-form item-card border rounded-lg shadow-md bg-white" data-uuid="${identifier}">
                    <div class="p-4 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                        <input type="text" class="font-bold text-gray-800 truncate pr-2 border-b border-gray-200 focus:border-green-500 outline-none w-2/3" name="item_title" value="${item.item_title}" required />
                        <div class="flex gap-2">
                            <button type="button" class="text-green-700 hover:text-green-900 font-semibold salvar-edicao" tabindex="0">Salvar</button>
                            <button type="button" class="text-gray-500 hover:text-gray-700 font-semibold cancelar-edicao" tabindex="0">Cancelar</button>
                        </div>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <div class="flex justify-between"><span class="font-semibold text-gray-600">Valor da Nota:</span><input type="number" step="0.01" min="0" name="preco_unit" value="${item.preco_unit}" class="border rounded p-1 w-24 text-right" required /></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-600">Desconto:</span><input type="number" step="0.01" min="0" name="item_discount" value="${item.item_discount}" class="border rounded p-1 w-24 text-right" /></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-600">Extra:</span><input type="number" step="0.01" min="0" name="item_extra" value="${item.item_extra}" class="border rounded p-1 w-24 text-right" /></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-600">Frete:</span><input type="number" step="0.01" min="0" name="item_frete" value="${item.item_frete}" class="border rounded p-1 w-24 text-right" /></div>
                        </div>
                        <div class="md:col-span-2 space-y-2 pl-4 border-l">
                            <div><span class="font-semibold text-gray-600">Nota Fiscal:</span> ${nfLinkHtml}<br><input type="file" name="item_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1" /></div>
                            <div><span class="font-semibold text-gray-600">Comp. Bancário:</span> ${comprovanteLinkHtml}<br><input type="file" name="item_comprovante" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1" /></div>
                            <div><span class="font-semibold text-gray-600">Recibo:</span> ${reciboLinkHtml}<br><input type="file" name="item_recibo" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1" /></div>
                        </div>
                    </div>
                </div>
                `;
                container.append(cardHtml);
            } else {
                // Card normal com botão Editar
                const nfLinkHtml = createFileLink(item.file_url, 'file', filesToUpload[`item_file_upload_${identifier}`]);
                const comprovanteLinkHtml = createFileLink(item.comprovante_url, 'comprovante', filesToUpload[`item_comprovante_upload_${identifier}`]);
                const reciboLinkHtml = createFileLink(item.recibo_url, 'recibo', filesToUpload[`item_recibo_upload_${identifier}`]);
                const cardHtml = `
                    <div class="item-card border rounded-lg shadow-md bg-white" data-uuid="${identifier}">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                            <h5 class="font-bold text-gray-800 truncate pr-2">${item.item_title}</h5>
                            <div class="flex gap-2">
                                <button type="button" class="text-blue-600 hover:text-blue-800 font-semibold editar-item">Editar</button>
                                <button type="button" class="text-red-500 hover:text-red-700 font-semibold remover-item">Remover</button>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <div class="flex justify-between"><span class="font-semibold text-gray-600">Valor da Nota:</span><span>${formatBRL(item.preco_unit)}</span></div>
                                <div class="flex justify-between"><span class="font-semibold text-gray-600">Desconto:</span><span>-${formatBRL(item.item_discount)}</span></div>
                                <div class="flex justify-between"><span class="font-semibold text-gray-600">Extra:</span><span>+${formatBRL(item.item_extra)}</span></div>
                                <div class="flex justify-between"><span class="font-semibold text-gray-600">Frete:</span><span>+${formatBRL(item.item_frete)}</span></div>
                                <hr class="my-1">
                                <div class="flex justify-between font-bold text-lg"><span class="text-gray-800">Total:</span><span class="text-green-700">${formatBRL(totalNota)}</span></div>
                            </div>
                            <div class="md:col-span-2 space-y-2 pl-4 border-l">
                                <div><span class="font-semibold text-gray-600">Nota Fiscal:</span> ${nfLinkHtml}</div>
                                <div><span class="font-semibold text-gray-600">Comp. Bancário:</span> ${comprovanteLinkHtml}</div>
                                <div><span class="font-semibold text-gray-600">Recibo:</span> ${reciboLinkHtml}</div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(cardHtml);
            }
        });
        recalcSaidaTotal();
    }

    // Handler para entrar em modo edição
    $(document).on('click', '.editar-item', function() {
        const uuid = $(this).closest('.item-card').data('uuid');
        const item = itens.find(i => i.temp_uuid === uuid);
        if (item) {
            item.is_editing = true;
            renderItens();
        }
    });
// Handler para remover arquivo existente
$(document).on('click', '.remover-arquivo', function(e) {
    e.preventDefault();
    const $container = $(this).closest('.item-edit-form');
    const uuid = $container.data('uuid');
    const type = $(this).data('type');
    const item = itens.find(i => i.temp_uuid === uuid);
    
    if (item) {
        // Marcar arquivo para remoção
        if (type === 'file') item.file_url = null;
        if (type === 'comprovante') item.comprovante_url = null;
        if (type === 'recibo') item.recibo_url = null;
        
        // Adicionar flag de remoção
        item[`remove_${type}`] = true;
        
        renderItens();
    }
});
    // Handler para salvar edição
    $(document).on('click', '.salvar-edicao', function(e) {
        e.preventDefault();
        const $container = $(this).closest('.item-edit-form');
        const uuid = $container.data('uuid');
        const item = itens.find(i => i.temp_uuid === uuid);
        if (!item) return;

        // Validação
        const newTitle = $container.find('[name="item_title"]').val().trim();
        const newPreco = parseFloat($container.find('[name="preco_unit"]').val()) || 0;
        
        if (!newTitle) {
            alert('Título é obrigatório');
            return;
        }
        if (newPreco <= 0) {
            alert('Valor deve ser maior que zero');
            return;
        }

        // Atualizar dados do item
        item.item_title = newTitle;
        item.preco_unit = newPreco;
        item.item_discount = parseFloat($container.find('[name="item_discount"]').val()) || 0;
        item.item_extra = parseFloat($container.find('[name="item_extra"]').val()) || 0;
        item.item_frete = parseFloat($container.find('[name="item_frete"]').val()) || 0;

        // Gerenciar arquivos novos
        const fileInputs = ['item_file', 'item_comprovante', 'item_recibo'];
        fileInputs.forEach(inputName => {
            const fileInput = $container.find(`[name="${inputName}"]`)[0];
            if (fileInput && fileInput.files[0]) {
                filesToUpload[`${inputName}_upload_${uuid}`] = fileInput.files[0];
            }
        });
// Marcar arquivos para remoção
const removeFlags = ['remove_file', 'remove_comprovante', 'remove_recibo'];
removeFlags.forEach(flag => {
    if (item[flag]) {
        filesToUpload[`${flag}_${uuid}`] = 'REMOVE';
    }
});
        // Sair do modo edição
        item.is_editing = false;
        renderItens();

        // MOSTRAR MODAL DE CONFIRMAÇÃO
        UIkit.modal('#modal-confirm-edit').show();

    });

    // Handler para cancelar edição
    $(document).on('click', '.cancelar-edicao', function() {
        const uuid = $(this).closest('.item-edit-form').data('uuid');
        const item = itens.find(i => i.temp_uuid === uuid);
        if (item) {
            item.is_editing = false;
            renderItens();
        }
    });
    // Renderizar itens existentes
    renderItens();

$('#adicionar-item').on('click', function() {
    const temp_uuid = generateUUID();
    const newItem = {
        item_title: $('#item_title').val().trim(),
        preco_unit: parseFloat($('#item_preco_unit').val()) || 0,
        item_discount: parseFloat($('#item_discount').val()) || 0,
        item_extra: parseFloat($('#item_extra').val()) || 0,
        item_frete: parseFloat($('#item_frete').val()) || 0,
        temp_uuid: temp_uuid,
        is_deleted: false,
        is_new: true,
        file_url: null, 
        comprovante_url: null,
        recibo_url: null
    };

    if (!newItem.item_title) {
        $('#error-message').text('Por favor, insira o número/descrição da Nota Fiscal.');
        UIkit.modal('#modal-error').show();
        return;
    }
    if (newItem.preco_unit <= 0) {
        $('#error-message').text('Por favor, insira um valor válido para a Nota Fiscal.');
        UIkit.modal('#modal-error').show();
        return;
    }

    // Usar os uploads modernos
    const itemFile = uploadNF.getSelectedFile();
    const itemComprovante = uploadComp.getSelectedFile();
    const itemRecibo = uploadRecibo.getSelectedFile();

    if (itemFile) filesToUpload[`item_file_upload_${temp_uuid}`] = itemFile;
    if (itemComprovante) filesToUpload[`item_comprovante_upload_${temp_uuid}`] = itemComprovante;
    if (itemRecibo) filesToUpload[`item_recibo_upload_${temp_uuid}`] = itemRecibo;

    itens.push(newItem);
    renderItens();

    // Limpar campos
    $('#item_title').val('');
    $('#item_preco_unit, #item_discount, #item_extra, #item_frete').val('0.00');
    
    // Limpar uploads modernos
    uploadNF.removeFile();
    uploadComp.removeFile();
    uploadRecibo.removeFile();
});

/*
    $(document).on('click', '.remover-item', function() {
        const uuidToRemove = $(this).closest('.item-card').data('uuid');
        const itemIndex = itens.findIndex(i => i.temp_uuid === uuidToRemove);

        if (itemIndex !== -1) {
            itens[itemIndex].is_deleted = true;
        }

        delete filesToUpload[`item_file_upload_${uuidToRemove}`];
        delete filesToUpload[`item_comprovante_upload_${uuidToRemove}`];
        delete filesToUpload[`item_recibo_upload_${uuidToRemove}`];

        renderItens();
    });
*/
$(document).on('click', '.remover-item', function() {
    itemToRemoveUUID = $(this).closest('.item-card').data('uuid');
    UIkit.modal('#modal-confirm-remove').show();
});
// Handler para confirmação de remoção
$(document).on('click', '#confirm-remove-btn', function() {
    if (itemToRemoveUUID) {
        const item = itens.find(i => i.temp_uuid === itemToRemoveUUID);
        if (item) {
            item.is_deleted = true;
            renderItens();
        }
        itemToRemoveUUID = null;
    }
    UIkit.modal('#modal-confirm-remove').hide();
});



    function recalcSaidaTotal() {
        const total = itens.filter(i => !i.is_deleted).reduce((acc, i) => {
            return acc + (parseFloat(i.preco_unit) || 0) + (parseFloat(i.item_extra) || 0) + (parseFloat(i.item_frete) || 0) - (parseFloat(i.item_discount) || 0);
        }, 0);
        $('#saida_total').val(total.toFixed(2));
    }

    $('#saida-form').on('submit', function(e) {
        e.preventDefault();
        
        $('#itens_json').val(JSON.stringify(itens));

        const formData = new FormData(this);

        for (const key in filesToUpload) {
            if (filesToUpload.hasOwnProperty(key)) {
                formData.append(key, filesToUpload[key]);
            }
        }

        const $btnSalvar = $(this).find('button[type="submit"]');
        const originalBtnHtml = $btnSalvar.html();
        $btnSalvar.prop('disabled', true).html('Salvando <span class="btn-spinner"></span>');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    $('#success-message').text(res.data.message || 'Saída atualizada com sucesso!');
                    UIkit.modal('#modal-success').show();
                } else {
                    $('#error-message').text(res.data.message || 'Erro ao atualizar a saída.');
                    UIkit.modal('#modal-error').show();
                }
            },
            error: function() {
                $('#error-message').text('Ocorreu um erro de comunicação. Por favor, tente novamente.');
                UIkit.modal('#modal-error').show();
            },
            complete: function() {
                $btnSalvar.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

});
</script>

<style>
/* Spinner minimalista para botão */
.btn-spinner {
  display: inline-block;
  width: 1.2em;
  height: 1.2em;
  border: 2px solid #fff;
  border-top: 2px solid #38a169;
  border-radius: 50%;
  animation: spinBtn 0.7s linear infinite;
  vertical-align: middle;
  margin-left: 0.5em;
}
@keyframes spinBtn {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.item-saved-highlight {
  animation: highlightSaved 1.2s cubic-bezier(0.4,0,0.2,1);
  background: #e6fffa !important;
  box-shadow: 0 0 0 2px #38a16933;
}
@keyframes highlightSaved {
  0% { background: #e6fffa; box-shadow: 0 0 0 2px #38a16933; }
  80% { background: #e6fffa; box-shadow: 0 0 0 2px #38a16933; }
  100% { background: #fff; box-shadow: none; }
}
</style>


<?php 
get_footer(); 
