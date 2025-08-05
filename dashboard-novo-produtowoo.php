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
Template Name: Dashboard - Novo Produto WooCommerce
*/
if (!current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}
get_header('zero');
?>
<?php get_template_part('header', 'user') ?>

<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container">
        <div class="flex">
            <div class="md:w-[100%]">

                <div class="bg-white text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
                    <div class="px-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <h1 class="leading-none font-semibold text-xl">Cadastrar Novo Produto</h1>
                                <p class="text-muted-foreground text-sm">Preencha os detalhes para criar um novo produto no sistema.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?php echo bloginfo("url"); ?>/todos-produtoswoo" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-white shadow-xs hover:bg-green-700 hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                    Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="novo-produto-form" class="space-y-6">
                    <input type="hidden" name="action" value="create_simple_product">
                    <?php wp_nonce_field('create_product_nonce', 'create_product_nonce_field'); ?>

                    <!-- Seção: Informações Básicas do Produto -->
                    <div class="card card-border my-5 shadow-lg rounded-lg">
                        <div class="card-body p-6">
                            <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                <span>Informações Básicas do Produto</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome do Produto -->
                                <div>
                                    <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nome do Produto <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="product_name"
                                        id="product_name"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="Nome completo do produto"
                                        required
                                    />
                                </div>

                                <!-- Status do Estoque -->
                                <div>
                                    <label for="stock_status" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Status do Estoque
                                    </label>
                                    <select name="stock_status" id="stock_status" class="w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200">
                                        <option value="instock">Em estoque</option>
                                        <option value="outofstock">Fora de estoque</option>
                                        <option value="onbackorder">Aceita encomenda</option>
                                    </select>
                                </div>

                                <!-- Categoria do Produto -->
                                <div>
                                    <label for="product_category" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Categoria do Produto
                                    </label>
                                    <select name="product_category" id="product_category" class="w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200">
                                        <option value="">Sem categoria</option>
                                        <?php 
                                        $categories = get_hierarchical_product_categories();
                                        foreach ($categories as $category) {
                                            echo '<option value="' . esc_attr($category['id']) . '">' . esc_html($category['name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Preço e Estoque -->
                    <div class="card card-border my-5 shadow-lg rounded-lg">
                        <div class="card-body p-6">
                            <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                <span>Preço e Estoque</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Preço Regular -->
                                <div>
                                    <label for="regular_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Preço Regular (R$) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="regular_price"
                                        id="regular_price"
                                        step="0.01"
                                        min="0"
                                        value="0.00"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="0.00"
                                        required
                                    />
                                </div>

                                <!-- Preço de Venda (Opcional) -->
                                <div>
                                    <label for="sale_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Preço de Venda (R$) <span class="text-gray-500">(Opcional)</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="sale_price"
                                        id="sale_price"
                                        step="0.01"
                                        min="0"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="0.00"
                                    />
                                </div>

                                <!-- Gerenciar Estoque -->
                                <div class="col-span-1 md:col-span-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="manage_stock" name="manage_stock" value="yes" class="form-checkbox h-4 w-4 text-green-500 focus:ring-green-500 border border-gray-300 rounded">
                                        <span class="ml-2 text-sm font-semibold text-gray-700">Gerenciar estoque?</span>
                                    </label>
                                </div>

                                <!-- Quantidade em Estoque (aparece se gerenciar estoque for marcado) -->
                                <div id="stock_quantity_field" class="hidden">
                                    <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Quantidade em Estoque <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="stock_quantity"
                                        id="stock_quantity"
                                        min="0"
                                        value="0"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="0"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Detalhes do Produto -->
                    <div class="card card-border my-5 shadow-lg rounded-lg">
                        <div class="card-body p-6">
                            <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                <span>Detalhes do Produto</span>
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Descrição Curta -->
                                <div>
                                    <label for="short_description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descrição Curta
                                    </label>
                                    <textarea
                                        name="short_description"
                                        id="short_description"
                                        rows="3"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="Breve descrição do produto, visível na listagem de produtos"
                                    ></textarea>
                                </div>

                                <!-- Descrição Longa -->
                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descrição Longa
                                    </label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="6"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 placeholder:text-gray-400 transition-all duration-200"
                                        placeholder="Descrição completa do produto, exibida na página do produto"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

<!-- Seção: Mídia do Produto - MODERNO -->
<div class="w-full">
    <label class="block mb-2 text-sm font-medium text-gray-900">Imagem do Produto</label>
    
    <!-- Upload Zone -->
    <div id="upload-zone-produto" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-produto" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif">
        
        <!-- Placeholder -->
        <div id="upload-placeholder-produto" class="upload-placeholder">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium">Arraste a imagem ou clique para selecionar</p>
            <p class="text-sm text-gray-400 mt-1">JPG, PNG, GIF (máx. 10MB)</p>
        </div>
        
        <!-- Preview -->
        <div id="upload-preview-produto" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div id="file-icon-produto" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-produto" class="text-sm font-medium text-gray-900"></p>
                        <p id="file-size-produto" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-produto" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="image-preview-produto" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
        </div>
        
        <!-- Progress -->
        <div id="upload-progress-produto" class="upload-progress hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar-produto" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progress-text-produto" class="text-sm text-gray-600 mt-1">Enviando...</p>
        </div>
    </div>
    
    <!-- Hidden inputs -->
    <input type="hidden" id="produto_image_id" name="produto_image_id">
    <input type="hidden" id="produto_image_url" name="produto_image_url">
</div>


                    <div class="card card-border mb-5 shadow-lg rounded-lg">
                        <div class="card-body flex justify-between p-6">
                            <div>
                                <a href="<?php bloginfo("url"); ?>/todos-produtoswoo" class="bg-red-800 text-white px-4 py-2 rounded text-sm">Cancelar</a>
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
                <a href="<?php echo home_url('/todos-produtoswoo'); ?>" class="uk-button bg-green-800 rounded-md text-white hover:opacity-80">Ver Todos</a>
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

<script>

// Classe ModernFileUpload (copiar da dashboard-nova-receita.php)
class ModernFileUpload {
    constructor(zoneId, inputId, type) {
        this.zone = document.getElementById(`upload-zone-${zoneId}`);
        this.input = document.getElementById(`file-input-${zoneId}`);
        this.placeholder = document.getElementById(`upload-placeholder-${zoneId}`);
        this.preview = document.getElementById(`upload-preview-${zoneId}`);
        this.progress = document.getElementById(`upload-progress-${zoneId}`);
        this.progressBar = document.getElementById(`progress-bar-${zoneId}`);
        this.progressText = document.getElementById(`progress-text-${zoneId}`);
        this.removeBtn = document.getElementById(`remove-file-${zoneId}`);
        this.fileName = document.getElementById(`file-name-${zoneId}`);
        this.fileSize = document.getElementById(`file-size-${zoneId}`);
        this.fileIcon = document.getElementById(`file-icon-${zoneId}`);
        this.imagePreview = document.getElementById(`image-preview-${zoneId}`);
        this.hiddenId = document.getElementById(`${zoneId}_image_id`);
        this.hiddenUrl = document.getElementById(`${zoneId}_image_url`);
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
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use JPG, PNG ou GIF.');
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
        
        this.fileIcon.innerHTML = `<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreview.src = e.target.result;
            this.imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
    
    removeFile() {
        this.placeholder.classList.remove('hidden');
        this.preview.classList.add('hidden');
        this.progress.classList.add('hidden');
        this.imagePreview.classList.add('hidden');
        this.hiddenId.value = '';
        this.hiddenUrl.value = '';
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

// Inicializar upload de produto
const uploadProduto = new ModernFileUpload('produto', 'file-input-produto', 'produto');


jQuery(document).ready(function($) {
    
    // Teste de conectividade AJAX
    console.log('Testando conectividade AJAX...');
    $.ajax({
        url: window.AmedisAjax ? window.AmedisAjax.url : ajaxurl,
        type: 'POST',
        data: {
            action: 'test_ajax_connection'
        },
        success: function(response) {
            console.log('Teste AJAX bem-sucedido:', response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Teste AJAX falhou:', textStatus, errorThrown, jqXHR.responseText);
        }
    });

    // Toggle do campo de quantidade em estoque
    $('#manage_stock').on('change', function() {
        if ($(this).is(':checked')) {
            $('#stock_quantity_field').removeClass('hidden');
            $('#stock_quantity').prop('required', true);
        } else {
            $('#stock_quantity_field').addClass('hidden');
            $('#stock_quantity').prop('required', false);
        }
    });

    // Initial state check for stock quantity field on page load
    if ($('#manage_stock').is(':checked')) {
        $('#stock_quantity_field').removeClass('hidden');
        $('#stock_quantity').prop('required', true);
    }

    // Initial check for stock quantity field visibility on page load to ensure UI consistency
    $('#manage_stock').trigger('change');

    // Submissão do formulário - CORRIGIDO
    $('#novo-produto-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData();
        
        // Adicionar action para o AJAX
        formData.append('action', 'create_simple_product');
        
        // Adicionar todos os campos do formulário incluindo nonces
        form.find('input, select, textarea').each(function() {
            const field = $(this);
            const name = field.attr('name');
            const type = field.attr('type');
            
            if (name) {
                if (type === 'checkbox') {
                    if (field.is(':checked')) {
                        formData.append(name, field.val());
                    }
                } else if (type === 'hidden' || field.val() !== '') {
                    formData.append(name, field.val());
                }
            }
        });
        
        // Garantir que o nonce está sendo enviado
        const nonceField = form.find('input[name="create_product_nonce_field"]');
        if (nonceField.length && nonceField.val()) {
            formData.append('create_product_nonce_field', nonceField.val());
            console.log('Nonce adicionado:', nonceField.val());
        } else {
            console.error('Nonce field não encontrado!');
        }
        
        // Adicionar arquivo selecionado se houver
        if (uploadProduto.getSelectedFile()) {
            formData.append('product_image_file', uploadProduto.getSelectedFile());
        }

        const submitButton = form.find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('Criando... <span class="btn-spinner"></span>');

        console.log('=== DEBUG: Iniciando submissão do formulário ===');
        console.log('AJAX URL:', window.AmedisAjax ? window.AmedisAjax.url : ajaxurl);
        console.log('Nonce disponível no window.AmedisAjax:', window.AmedisAjax ? window.AmedisAjax.nonces.createProduct : 'N/A');
        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? 'FILE - ' + pair[1].name : pair[1]));
        }
        
        // Verificar se todos os campos obrigatórios estão presentes
        const requiredFields = ['action', 'create_product_nonce_field'];
        for (let field of requiredFields) {
            if (!formData.has(field)) {
                console.error('Campo obrigatório ausente:', field);
            }
        }

        $.ajax({
            url: window.AmedisAjax ? window.AmedisAjax.url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '<?php echo admin_url("admin-ajax.php"); ?>'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 30000, // 30 segundos de timeout
            success: function(res) {
                console.log('AJAX Success Response:', res);
                if (res && res.success) {
                    $('#success-message').text(res.data.message || 'Produto criado com sucesso!');
                    UIkit.modal('#modal-success').show();
                    form[0].reset();
                    $('#stock_quantity_field').addClass('hidden');
                    $('#stock_quantity').prop('required', false);
                    // Reset do upload
                    uploadProduto.removeFile();
                } else {
                    const errorMsg = (res && res.data && res.data.message) ? res.data.message : 'Erro desconhecido ao criar o produto.';
                    $('#error-message').text(errorMsg);
                    UIkit.modal('#modal-error').show();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error Details:');
                console.error('Status:', textStatus);
                console.error('Error:', errorThrown);
                console.error('Response Text:', jqXHR.responseText);
                console.error('Status Code:', jqXHR.status);
                
                let errorMessage = 'Erro de conexão ao criar o produto.';
                
                // Verificar se a resposta é "0" (erro comum do WordPress)
                if (jqXHR.responseText === '0') {
                    errorMessage = 'Erro no servidor: A função AJAX não foi encontrada ou há um erro fatal no PHP. Verifique os logs do servidor.';
                } else if (jqXHR.responseText) {
                    try {
                        const errorResponse = JSON.parse(jqXHR.responseText);
                        if (errorResponse.data && errorResponse.data.message) {
                            errorMessage = errorResponse.data.message;
                        }
                    } catch (e) {
                        // Se não conseguir fazer parse do JSON, usar mensagem baseada no status
                        if (jqXHR.status === 400) {
                            errorMessage = 'Dados inválidos enviados. Verifique os campos e tente novamente.';
                        } else if (jqXHR.status === 403) {
                            errorMessage = 'Acesso negado. Você não tem permissão para esta ação.';
                        } else if (jqXHR.status === 500) {
                            errorMessage = 'Erro interno do servidor. Tente novamente em alguns instantes.';
                        } else if (textStatus === 'timeout') {
                            errorMessage = 'Tempo limite excedido. Tente novamente.';
                        } else {
                            errorMessage = 'Erro desconhecido: ' + jqXHR.responseText;
                        }
                    }
                }
                
                $('#error-message').text(errorMessage);
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
?>
