<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage CAJU
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Novo Pedido
*/
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');
$current_user = wp_get_current_user();
$produtos = wc_get_products(['limit' => -1]);
?>
<?php get_template_part('header', 'user') ?>
			
                    <main class="mt-5  bg-transparent pb-[60px]">
                        <div class="uk-container">


                                <div class="flex">
                                    <div class="md:w-[100%]">

                <div class="bg-white text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
                    <div class="px-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <h1 class="leading-none font-semibold text-xl">Registrar Novo Pedido</h1>
                                <p class="text-muted-foreground text-sm"><span>Preencha os detalhes para registrar um novo pedido.</span>.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?php echo bloginfo("url"); ?>/pedidos/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                    Voltar
                                </a>
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
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Cadastrar Novo</button>
                <a href="<?php echo home_url('/pedidos'); ?>" class="uk-button bg-green-800 rounded-md text-white hover:opacity-80">Ver Todos</a>
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

<form id="pedido-form" class="space-y-6" enctype="multipart/form-data">
    <input type="hidden" name="action" value="criar_pedido_frontend">
    <?php wp_nonce_field('criar_pedido_nonce', 'criar_pedido_nonce_field'); ?>



    <div class="card card-border my-5 shadow-lg rounded-lg"><div class="card-body p-6"><div class="flex">

                                                    <div class="w-full lg:w-1/2 px-3">
                                                        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -ml-9 -mt-6">
                                                            <span>Informações do Associado</span>
                                                        </h3>
                                                        <div>
                                                            <label for="select-associados" class="block text-sm font-semibold text-gray-700 mb-2">
                                                                Selecione o Associado
                                                            </label>

<select id="select-associados" name="associado" class="w-full border-gray-300 text-sm rounded-lg py-2.5" required>
  <option value="">Selecione um associado</option>
  <?php
  $users = get_users(['role' => 'associados', 'orderby' => 'display_name']);
  foreach ($users as $user):
    $endereco    = get_field('endereco',    'user_' . $user->ID);
    $numero      = get_field('numero',      'user_' . $user->ID);
    $complemento = get_field('complemento', 'user_' . $user->ID);
    $cep         = get_field('cep',         'user_' . $user->ID);
    $bairro      = get_field('bairro',      'user_' . $user->ID);
    $cidade      = get_field('cidade',      'user_' . $user->ID);
    $estado      = get_field('estado',      'user_' . $user->ID);
    $pais        = 'BR';
    $shipping_1  = trim("{$endereco}, {$bairro}, {$numero}", ', ');
                    $nome_completo       = get_field('nome_completo', 'user_' . $user->ID);
                    $tipo_associacao     = get_field('tipo_associacao', 'user_' . $user->ID);
                    $nome_completo_respon= get_field('nome_completo_respon', 'user_' . $user->ID);    
  ?>
    <option value="<?= $user->ID; ?>"
      data-shipping_address_1="<?= esc_attr( $shipping_1 ); ?>"
      data-shipping_address_2="<?= esc_attr( $complemento ); ?>"
      data-shipping_city     ="<?= esc_attr( $cidade ); ?>"
      data-shipping_postcode ="<?= esc_attr( $cep ); ?>"
      data-shipping_state    ="<?= esc_attr( $estado ); ?>"
      data-shipping_country  ="<?= esc_attr( $pais ); ?>"
                        data-nome-completo="<?php echo esc_attr($nome_completo); ?>"
                        data-tipo-associacao="<?php echo esc_attr($tipo_associacao); ?>"
                        data-nome-completo-respon="<?php echo esc_attr($nome_completo_respon); ?>"
      >
      <?php echo esc_html($nome_completo); ?>
    </option>
  <?php endforeach; ?>
</select>

    </div>

</div>


<div class="md:w-[50%]">
<h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
    <span>Total do Pedido</span>
</h3>    


<div class="grid grid-cols-1 md:grid-cols-4 gap-4 ml-4">
  <!-- Desconto em R$ -->
  <div>
    <label for="pedido_discount" class="block text-sm font-medium text-gray-700">
      Desconto (R$)
    </label>
    <input
      type="number"
      name="pedido_discount"
      id="pedido_discount"
      step="0.01"
      min="0"
      value="0.00"
      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
      placeholder="0.00"
    />
  </div>

  <!-- Valor extra em R$ -->
  <div>
    <label for="pedido_extra" class="block text-sm font-medium text-gray-700">
      Valor Extra Cartão (R$)
    </label>
    <input
      type="number"
      name="pedido_extra"
      id="pedido_extra"
      step="0.01"
      min="0"
      value="0.00"
      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
      placeholder="0.00"
    />
  </div>

  <!-- Valor frete em R$ -->
  <div>
    <label for="pedido_frete" class="block text-sm font-medium text-gray-700">
      Valor Frete (R$)
    </label>
    <input
      type="number"
      name="pedido_frete"
      id="pedido_frete"
      step="0.01"
      min="0"
      value="0.00"
      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
      placeholder="0.00"
    />
  </div>

  <!-- Valor total em R$ -->
  <div>
    <label for="pedido_total" class="block text-sm font-medium text-gray-700">
      Valor total (R$)
    </label>
    <input
      type="number"
      name="pedido_total"
      id="pedido_total"
      step="0.01"
      min="0"
      value="0.00"
      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
      placeholder="0.00"
      readonly
    />
  </div>

</div>


</div>

</div>
</div>
</div>
</div>
</div><!-- flex -->


<div class="card card-border my-5 shadow-lg rounded-lg"><div class="card-body p-6"><div class="flex">
<div class="md:w-[70%]" id="entrega-section">
<h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
    <span>Entrega</span>
</h3>

  <!-- Checkbox para ativar entrega em outro endereço -->
  <label class="inline-flex items-center mb-4">
    <input
      type="checkbox"
      id="custom-delivery-checkbox"
      class="form-checkbox h-4 w-4 text-green-500 focus:ring-green-500 border border-gray-300 rounded"
    />
    <span class="ml-2 text-gray-700">Entregar em endereço diferente?</span>
  </label>

  <!-- Campos padrão do WooCommerce (serão sempre enviados) -->
  <div id="default-delivery-fields">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Endereço linha 1 -->
      <div>
        <label for="shipping_address_1" class="block text-sm font-medium text-gray-700">
          Endereço
        </label>
        <input
          type="text"
          name="shipping_address_1"
          id="shipping_address_1"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          required
          readonly
        />
      </div>
      <!-- CEP -->
      <div>
        <label for="shipping_postcode" class="block text-sm font-medium text-gray-700">
          CEP
        </label>
        <input
          type="text"
          name="shipping_postcode"
          id="shipping_postcode"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          required
          readonly
        />
      </div>

      <!-- Cidade -->
      <div>
        <label for="shipping_city" class="block text-sm font-medium text-gray-700">
          Cidade
        </label>
        <input
          type="text"
          name="shipping_city"
          id="shipping_city"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          required
          readonly
        />
      </div>


      <!-- Estado (select BR) -->
      <div>
        <label for="shipping_state" class="block text-sm font-medium text-gray-700">
          Estado
        </label>
        <select
          name="shipping_state"
          id="shipping_state"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          required
          readonly
        >
          <option value="">Selecione um estado</option>
          <option value="AC">Acre</option>
          <option value="AL">Alagoas</option>
          <option value="AP">Amapá</option>
          <option value="AM">Amazonas</option>
          <option value="BA">Bahia</option>
          <option value="CE">Ceará</option>
          <option value="DF">Distrito Federal</option>
          <option value="ES">Espírito Santo</option>
          <option value="GO">Goiás</option>
          <option value="MA">Maranhão</option>
          <option value="MT">Mato Grosso</option>
          <option value="MS">Mato Grosso do Sul</option>
          <option value="MG">Minas Gerais</option>
          <option value="PA">Pará</option>
          <option value="PB">Paraíba</option>
          <option value="PR">Paraná</option>
          <option value="PE">Pernambuco</option>
          <option value="PI">Piauí</option>
          <option value="RJ">Rio de Janeiro</option>
          <option value="RN">Rio Grande do Norte</option>
          <option value="RS">Rio Grande do Sul</option>
          <option value="RO">Rondônia</option>
          <option value="RR">Roraima</option>
          <option value="SC">Santa Catarina</option>
          <option value="SP">São Paulo</option>
          <option value="SE">Sergipe</option>
          <option value="TO">Tocantins</option>
        </select>
      </div>

      <!-- Endereço linha 2 -->
      <div>
        <label for="shipping_address_2" class="block text-sm font-medium text-gray-700">
          Complemento
        </label>
        <input
          type="text"
          name="shipping_address_2"
          id="shipping_address_2"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          readonly
        />
      </div>





      <!-- País (sempre Brasil, readonly) -->
      <div>
        <label for="shipping_country" class="block text-sm font-medium text-gray-700">
          País
        </label>
        <input
          type="text"
          name="shipping_country"
          id="shipping_country"
          class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
          value="BR"
          readonly
        />
      </div>


    </div>
  </div>

  <!-- Textarea extra, só aparece se marcar “endereço diferente” -->
  <div id="custom-delivery-textarea" class="mt-4 hidden">
    <label for="custom_delivery_address" class="block text-sm font-medium text-gray-700">
      Informe o novo endereço completo
    </label>
    <textarea
      name="custom_delivery_address"
      id="custom_delivery_address"
      rows="4"
      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
      placeholder="Rua Exemplo, nº, Bairro, CEP"
    ></textarea>
  </div>
</div>


<div class="md:w-[30%]" id="receitas-section">
<h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
    <span>Receitas / Laudos</span>
</h3>

<div id="receitas-container" class="ml-4"></div>

<!-- Campo hidden para receitas (ADICIONADO) -->
<input type="hidden" id="idreceitas" name="selected_receitas" value="">
</div>
</div></div></div><!-- flex endere e receitas -->
    <div class="card card-border my-5 shadow-lg rounded-lg"><div class="card-body p-6"><div class="">
        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
            <span>Produtos do Pedido</span>
        </h3>
        <table id="tabela-produtos" class="w-full border mb-4">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2 border">Produto</th>
              <th class="p-2 border">Preço Unit.</th>
              <th class="p-2 border">Quantidade</th>
              <th class="p-2 border">Subtotal</th>
              <th class="p-2 border">Ações</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <div class="flex gap-4">
            <select id="produto_id" class="select2-produtos w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400">
                <option value="">Buscar produto...</option>
                <?php
                $products_query = new WP_Query([
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                ]);

                if ($products_query->have_posts()) {
                    while ($products_query->have_posts()) {
                        $products_query->the_post();
                        $product = wc_get_product(get_the_ID());
                        $product_id = $product->get_id();
                        $product_title = $product->get_title();
                        $product_display_price = wc_price($product->get_price());
                        $product_image_url = has_post_thumbnail() ? get_the_post_thumbnail_url($product_id, 'thumbnail') : wc_placeholder_img_src();

                        echo '<option value="' . esc_attr($product_id) . '" data-image-src="' . esc_url($product_image_url) . '" data-price="' . esc_attr($product_display_price) . '" data-preco="' . esc_attr($product->get_price()) . '">' . esc_html($product_id . ' - ' . $product_title) . '</option>';
                    }
                    wp_reset_postdata();
                }
                ?>
            </select>
            <input type="number" id="quantidade" min="1" value="1" class="border p-2 w-24">
            <button type="button" id="adicionar-produto" class="bg-green-800 text-white px-4 py-2 rounded">Adicionar</button>
        </div>
    </div>

    <input type="hidden" name="produtos_json" id="produtos_json">

</div>
</div>


<!-- Card Informações Extras -->
<div class="card card-border my-5 shadow-lg rounded-lg">
    <div class="card-body p-6">
        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
            <span>Informações Extras</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Número da Transação -->
              <div>
                  <label for="numero_transacao" class="block text-sm font-medium text-gray-700 mb-2">
                      Número da Transação (Gateway) *
                  </label>
                  <input
                      type="text"
                      name="numero_transacao"
                      id="numero_transacao"
                      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
                      placeholder="Ex: TXN123456789"
                  />
              </div>

              <!-- Data do Pagamento -->
              <div>
                  <label for="data_pagamento" class="block text-sm font-medium text-gray-700 mb-2">
                      Data do Pagamento
                  </label>
                  <input
                      type="date"
                      name="data_pagamento"
                      id="data_pagamento"
                      class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                  />
              </div>
          </div>
            <!-- Observações -->
            <div>
                <label for="observacoes_pedido" class="block text-sm font-medium text-gray-700 mb-2">
                    Observações
                </label>
                <textarea
                    name="observacoes_pedido"
                    id="observacoes_pedido"
                    rows="3"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400"
                    placeholder="Observações sobre o pedido..."
                ></textarea>
            </div>
          </div>
            <!-- Comprovante da Transação -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">
                    Comprovante da Transação
                </label>
                
                <div id="upload-zone-comprovante" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                    <input type="file" id="file-input-comprovante" class="hidden" accept="image/*,application/pdf">
                    
                    <!-- Placeholder -->
                    <div id="upload-placeholder-comprovante" class="upload-placeholder">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                        <p class="text-sm text-gray-400 mt-1">PDF ou Imagem (máx. 5MB)</p>
                    </div>
                    
                    <!-- Preview -->
                    <div id="upload-preview-comprovante" class="upload-preview hidden">
                        <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                            <div class="flex items-center space-x-3">
                                <div id="file-icon-comprovante" class="flex-shrink-0"></div>
                                <div class="text-left">
                                    <p id="file-name-comprovante" class="text-sm font-medium text-gray-900"></p>
                                    <p id="file-size-comprovante" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" id="remove-file-comprovante" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <img id="image-preview-comprovante" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
                    </div>
                    
                    <!-- Progress -->
                    <div id="upload-progress-comprovante" class="upload-progress hidden mt-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div id="progress-bar-comprovante" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progress-text-comprovante" class="text-sm text-gray-600 mt-1">Enviando...</p>
                    </div>
                </div>
                
                <!-- Hidden inputs para WordPress -->
                <input type="hidden" id="comprovante_id" name="comprovante_id">
                <input type="hidden" id="comprovante_url" name="comprovante_url">
            </div>
        </div>
    </div>
</div>


                                        <div class="card card-border mb-5 shadow-lg rounded-lg">
                                            <div class="card-body flex justify-between p-6">
                                                <div>
                                                    <a href="<?php bloginfo("url"); ?>/pedidos" class="bg-red-800 text-white px-4 py-2 rounded">Cancelar</a>
                                                </div>
                                                <div>
                                                    <button type="submit" class="flex space-x-1 bg-green-800 text-white px-4 py-2 rounded text-sm">
                                                        <span>Salvar</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class=" h-5 w-5 text-white ml-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 96l0 320c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-245.5c0-4.2-1.7-8.3-4.7-11.3l33.9-33.9c12 12 18.7 28.3 18.7 45.3L448 416c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l245.5 0c17 0 33.3 6.7 45.3 18.7l74.5 74.5-33.9 33.9L320.8 84.7c-.3-.3-.5-.5-.8-.8L320 184c0 13.3-10.7 24-24 24l-192 0c-13.3 0-24-10.7-24-24L80 80 64 80c-8.8 0-16 7.2-16 16zm80-16l0 80 144 0 0-80L128 80zm32 240a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"></path></svg>                                  
                                                    </button>
                                                </div>                                                
                                            </div>
                                        </div>

</form>

                                    </div>
                                </div>



                            </div>
                        </main>



<script>
// --- UPLOAD MODERNO PARA COMPROVANTE ---
// Classe ModernFileUpload (baseada no promptIA.txt)
class ModernFileUpload {
    constructor(fieldId, inputId, fieldType = 'documento') {
        this.fieldId = fieldId;
        this.inputId = inputId;
        this.fieldType = fieldType;
        this.selectedFile = null;
        this.maxSize = 5 * 1024 * 1024; // 5MB
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        const uploadZone = document.getElementById(`upload-zone-${this.fieldId}`);
        const fileInput = document.getElementById(this.inputId);
        const removeBtn = document.getElementById(`remove-file-${this.fieldId}`);

        // Drag & Drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('border-green-400', 'bg-green-50');
        });

        uploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('border-green-400', 'bg-green-50');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('border-green-400', 'bg-green-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFile(files[0]);
            }
        });

        // Click to select
        uploadZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFile(e.target.files[0]);
            }
        });

        // Remove file
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeFile();
        });
    }

    handleFile(file) {
        if (!this.validateFile(file)) return;

        this.selectedFile = file;
        this.showPreview(file);
    }

    validateFile(file) {
        if (file.size > this.maxSize) {
            alert('Arquivo muito grande. Máximo 5MB.');
            return false;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use imagens ou PDF.');
            return false;
        }

        return true;
    }

    showPreview(file) {
        const placeholder = document.getElementById(`upload-placeholder-${this.fieldId}`);
        const preview = document.getElementById(`upload-preview-${this.fieldId}`);
        const fileName = document.getElementById(`file-name-${this.fieldId}`);
        const fileSize = document.getElementById(`file-size-${this.fieldId}`);
        const fileIcon = document.getElementById(`file-icon-${this.fieldId}`);
        const imagePreview = document.getElementById(`image-preview-${this.fieldId}`);

        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');

        fileName.textContent = file.name;
        fileSize.textContent = this.formatFileSize(file.size);

        // Icon based on file type
        if (file.type.startsWith('image/')) {
            fileIcon.innerHTML = `<svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            fileIcon.innerHTML = `<svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
            imagePreview.classList.add('hidden');
        }
    }

    removeFile() {
        this.selectedFile = null;
        const placeholder = document.getElementById(`upload-placeholder-${this.fieldId}`);
        const preview = document.getElementById(`upload-preview-${this.fieldId}`);
        const imagePreview = document.getElementById(`image-preview-${this.fieldId}`);
        const fileInput = document.getElementById(this.inputId);

        placeholder.classList.remove('hidden');
        preview.classList.add('hidden');
        imagePreview.classList.add('hidden');
        fileInput.value = '';

        // Clear hidden inputs
        document.getElementById(`${this.fieldId}_id`).value = '';
        document.getElementById(`${this.fieldId}_url`).value = '';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    getSelectedFile() {
        return this.selectedFile;
    }
}

// Inicializar upload do comprovante
const uploadComprovante = new ModernFileUpload('comprovante', 'file-input-comprovante', 'comprovante');



jQuery(function($) {

    // --- INICIALIZAÇÃO DO SELECT2 PARA PRODUTOS ---
    function formatProduct(product) {
        if (!product.id) {
            return product.text;
        }
        var $product = $(
            '<div class="flex items-center">' +
            '<img src="' + $(product.element).data('image-src') + '" class="w-10 h-10 mr-3 rounded" />' +
            '<div>' +
            '<div class="font-bold">' + product.text + '</div>' +
            '<div class="text-sm text-gray-500">' + $(product.element).data('price') + '</div>' +
            '</div>' +
            '</div>'
        );
        return $product;
    }

    $('#produto_id').select2({
        templateResult: formatProduct,
        templateSelection: function(product) {
            return product.text;
        }
    });
    // --- FIM DA INICIALIZAÇÃO DO SELECT2 PARA PRODUTOS ---

    // 1) Fecha a mensagem de alerta
    $('#pedido-mensagem-close').on('click', () => {
        $('#pedido-mensagem').addClass('hidden');
    });

    // 2) Inicializa Select2 para associados (código original)
    $('#select-associados').select2({
        placeholder: 'Selecione uma opção',
        allowClear: true,
        dropdownParent: $('#pedido-form'),
        templateResult: function(option) {
            if (!option.id) return option.text;
            const $opt = $(option.element);
            const nome = $opt.data('nome-completo') || option.text;
            const tipo = $opt.data('tipo-associacao');
            const respon = $opt.data('nome-completo-respon');
            if (tipo === 'assoc_respon' || tipo === 'assoc_tutor') {
                return $(`
                    <div>
                        <div>${nome}</div>
                        <div style="font-size:.85em;color:#777;">Resp: ${respon}</div>
                    </div>
                `);
            }
            return nome;
        }
    });

    // 3) Mapeamento dos campos de shipping (código original)
    const mapping = [
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_postcode',
        'shipping_state',
        'shipping_country'
    ];

    // 4) Função que rebinda checkboxes de receitas (código original)
    function bindReceitasCheckboxes() {
        function atualizarInputIds() {
            const sel = [];
            $('input[name="selected_receitas[]"]:checked').each(function() {
                sel.push(this.value);
            });
            $('#idreceitas').val(sel.join(','));
        }
        atualizarInputIds();
        $('input[name="selected_receitas[]"]').off('change').on('change', atualizarInputIds);
    }

    // 5) Ao selecionar um associado (código original)
    $('#select-associados')
        .on('select2:select', function(e) {
            const $opt = $(e.params.data.element);
            const userId = $opt.val();
            mapping.forEach(field => {
                $(`[name="${field}"]`).val($opt.data(field) || '');
            });
            $('#receitas-container').html('<p class="text-sm text-gray-600">Carregando receitas…</p>');
            console.log('Buscando receitas para usuário:', userId);
            console.log('getReceitasNonce disponível:', typeof getReceitasNonce !== 'undefined' ? getReceitasNonce : 'UNDEFINED');
            $.post(
                typeof ajaxurl !== 'undefined' ? ajaxurl : '<?php echo admin_url("admin-ajax.php"); ?>', {
                    action: 'get_receitas_paciente',
                    security: typeof getReceitasNonce !== 'undefined' ? getReceitasNonce : '<?php echo wp_create_nonce("get_receitas_nonce"); ?>',
                    user_id: userId,
                    order_id: 0 // Garante compatibilidade com o PHP, sempre envia order_id
                },
                function(html) {
                    console.log('Receitas HTML recebido:', html);
                    $('#receitas-container').html(html);
                    bindReceitasCheckboxes();
                }
            ).fail(function(xhr, status, error) {
                console.log('Erro ao buscar receitas:', {xhr, status, error});
                $('#receitas-container').html('<p class="text-red-500">Erro ao carregar receitas</p>');
            });
        })
        .on('select2:clear change', function() {
            if (!$(this).val()) {
                mapping.forEach(field => $(`[name="${field}"]`).val(''));
                $('#receitas-container').empty();
            }
        });

    // 7) Toggle do endereço customizado (código original)
    $('#custom-delivery-checkbox').on('change', function() {
        const usar = this.checked;
        $('#custom-delivery-textarea').toggleClass('hidden', !usar);
        $('#default-delivery-fields :input').prop('disabled', usar);
    });

    // 8) Lógica de produtos (MODIFICADA E CORRIGIDA)
    let produtos = [];

    $('#adicionar-produto').on('click', function() {
        const $sel = $('#produto_id');
        const selectedData = $sel.select2('data')[0];
        const produtoId = selectedData.id;

        if (!produtoId || !selectedData.element) {
            return;
        }

        const nome = selectedData.text;
        const precoUnit = parseFloat($(selectedData.element).data('preco')) || 0;
        const quantidade = parseInt($('#quantidade').val(), 10) || 0;

        if (quantidade < 1) {
            return;
        }
        
        if (produtos.some(p => p.produto_id == produtoId)) {
            alert('Este produto já foi adicionado.');
            return;
        }

        const formatBRL = v => v.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        const subtotal = precoUnit * quantidade;

        produtos.push({
            produto_id: produtoId,
            quantidade: quantidade,
            preco_unit: precoUnit
        });

        $('#tabela-produtos tbody').append(`
            <tr data-id="${produtoId}">
                <td class="border p-2">${nome}</td>
                <td class="border p-2">${formatBRL(precoUnit)}</td>
                <td class="border p-2">${quantidade}</td>
                <td class="border p-2">${formatBRL(subtotal)}</td>
                <td class="border p-2 text-center">
                    <button type="button" class="text-red-600 remover-produto">Remover</button>
                </td>
            </tr>
        `);

        $sel.val('').trigger('change');
        $('#quantidade').val(1);
        recalcPedidoTotal();
    });

    $(document).on('click', '.remover-produto', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        produtos = produtos.filter(p => p.produto_id != id);
        row.remove();
        recalcPedidoTotal();
    });

    // 9) Cálculo de total no frontend (MODIFICADO E CORRIGIDO)
    function recalcPedidoTotal() {
        let soma = produtos.reduce((acc, p) => acc + (p.preco_unit * p.quantidade), 0);
        
        const extra = parseFloat($('#pedido_extra').val()) || 0;
        const frete = parseFloat($('#pedido_frete').val()) || 0;
        const desconto = parseFloat($('#pedido_discount').val()) || 0;
        
        $('#pedido_total').val((soma + frete + extra - desconto).toFixed(2));
    }
    $('#pedido_extra, #pedido_frete, #pedido_discount').on('input change', recalcPedidoTotal);
    recalcPedidoTotal();

    // 10) Submissão do form (MODIFICADO PARA FORMDATA)
    $('#pedido-form').on('submit', function(e) {
        e.preventDefault();
        
        // Garantia: Verificar se o campo de nonce está presente no DOM
        if ($('input[name="criar_pedido_nonce_field"]').length === 0) {
            console.error('AVISO: Campo de nonce criar_pedido_nonce_field NÃO encontrado no DOM no momento do submit!');
            $('#error-message').text('Erro interno: campo de segurança ausente. Recarregue a página.');
            UIkit.modal('#modal-error').show();
            return;
        }
        
        console.log('=== DEBUG: Iniciando submissão do formulário ===');
        console.log('Produtos array:', produtos);
        console.log('ajaxurl disponível:', typeof ajaxurl !== 'undefined' ? ajaxurl : 'UNDEFINED');
        console.log('criarPedidoNonce disponível:', typeof criarPedidoNonce !== 'undefined' ? criarPedidoNonce : 'UNDEFINED');

        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        if (!produtos.length) {
            console.log('ERROR: Nenhum produto adicionado');
            $('#error-message').text('Adicione pelo menos um produto.');
            UIkit.modal('#modal-error').show();
            return;
        }

        // Validar número da transação
        if (!$('#numero_transacao').val().trim()) {
            console.log('ERROR: Número da transação vazio');
            $('#error-message').text('Número da transação é obrigatório.');
            UIkit.modal('#modal-error').show();
            return;
        }

        const produtosJson = JSON.stringify(produtos.map(p => ({produto_id: p.produto_id, quantidade: p.quantidade})));
        console.log('Produtos JSON:', produtosJson);
        $('#produtos_json').val(produtosJson);
        
        // Criar FormData para enviar arquivos
        const formData = new FormData();
        
        // Adicionar todos os campos do formulário (inclusive o nonce, pois está no form)
        const formFields = form.serializeArray();
        formFields.forEach(field => {
            formData.append(field.name, field.value);
        });
        // NÃO adicionar manualmente o nonce, pois já está no form
        // Garantia: O nome do campo de nonce é 'criar_pedido_nonce_field', igual ao esperado no PHP
        
        // Adicionar arquivo do comprovante se selecionado
        if (uploadComprovante.getSelectedFile()) {
            console.log('Adicionando arquivo de comprovante');
            formData.append('comprovante_file', uploadComprovante.getSelectedFile());
        }

        console.log('FormData criado, enviando requisição...');
        console.log('URL da requisição:', typeof ajaxurl !== 'undefined' ? ajaxurl : '<?php echo admin_url("admin-ajax.php"); ?>');
        submitButton.prop('disabled', true).html('Criando... <span class="btn-spinner"></span>');

        $.ajax({
            url: typeof ajaxurl !== 'undefined' ? ajaxurl : '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log('AJAX Success Response:', res);
                if (res.success) {
                    $('#success-message').text(res.data.message);
                    UIkit.modal('#modal-success').show();
                    
                    // Limpar formulário
                    form[0].reset();
                    $('#tabela-produtos tbody').empty();
                    produtos = [];
                    $('#select-associados').val('').trigger('change');
                    $('#produto_id').val('').trigger('change');
                    uploadComprovante.removeFile();
                    recalcPedidoTotal();

                } else {
                    console.log('AJAX Error Response:', res);
                    $('#error-message').text(res.data.message || 'Erro ao criar o pedido.');
                    UIkit.modal('#modal-error').show();
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Request Failed:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                $('#error-message').text('Erro de conexão ao criar o pedido.');
                UIkit.modal('#modal-error').show();
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
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
</style>
<?php 
get_footer();