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
Template Name: Dashboard - Configurações
*/

if (!current_user_can('administrator') && !current_user_can('gerente') && !user_has_area_access('configuracoes')) {
    wp_redirect(home_url());
    exit;
}
get_header('zero'); ?>


<?php get_template_part('header', 'user') ?>


<?php 

$logo_horizontal = get_field('logo_horizontal');
$logo_vertical = get_field('logo_vertical');
$texto_apresentacao = get_field('texto_apresentacao');

?>
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
                                                                <h6 class="font-medium">Configurações</h6>

                                                            </div>
                                                            <div class="flex">                                                               


                                                                <span>Configurações do sistema!</span>


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 items-center">
     
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<div id="toastSalvo"></div>

<form id="configuracoes_app" class="relative" method="post" action="" name="configApp">
<?php wp_nonce_field('form_configapp_action', 'form_configapp_nonce'); ?>


<div class="flex items-center justify-start space-x-2">
    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">

        <div class="w-full">
            <label class="block mb-2 text-sm font-medium text-gray-900" for="logo_horizontal_button">Logo Horizontal [ Largura Máx. 300px ]</label>
            <div class="flex items-center justify-center space-x-2">
                <button type="button" class="btn btn-sm bg-white" id="logo_horizontal_button">Selecionar Imagem</button>
                <div class="text-center">
                    <div id="logo_horizontal_preview" class="w-[150px] h-16 p-2 mx-auto border border-gray-300 rounded flex items-center justify-center space-y-3">
                        <?php
                        $logo_horizontal_id = get_field('logo_horizontal');
                        if ($logo_horizontal_id) {
                            $logo_horizontal_url = wp_get_attachment_image_src($logo_horizontal_id, 'thumbnail')[0];
                            echo '<img src="' . esc_url($logo_horizontal_url) . '" alt="Logo Horizontal Preview" style="max-width: 100%; height: auto;">';
                        } else {
                            echo '<span class="text-gray-500">Nenhuma logo selecionada</span>';
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-plain btn-xs text-red-500" id="remover_logo_horizontal" <?php echo $logo_horizontal_id ? '' : 'style="display:none;"'; ?>>Remover</button>
                </div>
            </div>
            <input type="hidden" name="acf[field_67fc5f59b750d]" id="logo_horizontal_id" value="<?php echo esc_attr($logo_horizontal_id); ?>">
            <p class="mt-1 text-sm text-gray-500 text-center" id="comprova_laudo_paciente_log">PNG, JPG ou JPEG (MAX. 2mb)</p>
        </div>

        <div class="w-full">
            <label class="block mb-2 text-sm font-medium text-gray-900" for="logo_vertical_button">Logo Vertical [ QUADRADA 500px x 500px ]</label>
            <div class="flex items-center justify-center space-x-2">
                <button type="button" class="btn btn-sm bg-white" id="logo_vertical_button">Selecionar Imagem</button>
                <div class="text-center">
                    <div id="logo_vertical_preview" class="w-16 h-16 p-2 mx-auto border border-gray-300 rounded flex items-center justify-center space-y-3">
                        <?php
                        $logo_vertical_id = get_field('logo_vertical');
                        if ($logo_vertical_id) {
                            $logo_vertical_url = wp_get_attachment_image_src($logo_vertical_id, 'thumbnail')[0];
                            echo '<img src="' . esc_url($logo_vertical_url) . '" alt="Logo Vertical Preview" style="max-width: 100%; height: auto;">';
                        } else {
                            echo '<span class="text-gray-500">Nenhuma logo selecionada</span>';
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-plain btn-xs text-red-500" id="remover_logo_vertical" <?php echo $logo_vertical_id ? '' : 'style="display:none;"'; ?>>Remover</button>
                </div>
            </div>
            <input type="hidden" name="acf[field_67fc5f78b750e]" id="logo_vertical_id" value="<?php echo esc_attr($logo_vertical_id); ?>">
            <p class="mt-1 text-sm text-gray-500 text-center" id="comprova_laudo_paciente_log">PNG, JPG ou JPEG (MAX. 2mb)</p>
        </div>

    </div>
</div>


<div class="mt-4 pt-4 border-t border-dashed border-gray-300">
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" id="show_debug_button" name="show_debug_button" class="sr-only peer" <?php checked(get_option('amedis_show_debug_button'), 1); ?> >
        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white before:content-[''] after:content-[''] before:absolute after:absolute after:top-[2px] before:start-[-18px] after:start-[-18px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
        <span class="ms-3 text-sm font-medium text-gray-900">Exibir botão de debug</span>
    </label>
</div>
<div class="mt-4 pt-4 border-t border-dashed border-gray-300">
    <label for="texto_apresentacao" class="block text-sm font-medium text-gray-700">Texto de Apresentação</label>
    <p class="my-1 text-sm text-gray-500">Texto que será exibido na apresentação do sistema.</p>
    <?php
    $texto_apresentacao_value = get_field('texto_apresentacao');
    $conteudo_editor = ($texto_apresentacao_value === null) ? '' : $texto_apresentacao_value;
    wp_editor($conteudo_editor, 'texto_apresentacao', array(
        'textarea_name' => 'acf[field_67fc61b0a341e]',
        'textarea_rows' => 10,
        'media_buttons' => false, // Remove o botão de "Adicionar mídia"
        'quicktags'     => true,
        'drag_drop_upload' => false,
    ));
    ?>
    
</div>

            <div id="stickyFooter_" class="w-full mt-5 px-6 flex items-center border border-gray-300 justify-between py-4 bg-white rounded-lg">
                <a class="btn btn-plain btn-sm" href="<?php bloginfo('url') ?>/">
                    <span class="flex items-center justify-center text-red-600">
                        <span class="text-lg">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </span>
                        <span class="ltr:ml-1 rtl:mr-1">Cancelar</span>
                    </span>
                </a>                                                    
                <div class="md:flex items-center">
                    
                    <button class="btn btn-sm bg-green-500 text-white hover:opacity-50" type="submit" name="submit_associado">
                        <span class="flex items-center justify-center">
                            <span class="mr-1"> Salvar</span>
                            <span class="text-lg">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M893.3 293.3L730.7 130.7c-7.5-7.5-16.7-13-26.7-16V112H144c-17.7 0-32 14.3-32 32v736c0 17.7 14.3 32 32 32h736c17.7 0 32-14.3 32-32V338.5c0-17-6.7-33.2-18.7-45.2zM384 184h256v104H384V184zm456 656H184V184h136v136c0 17.7 14.3 32 32 32h320c17.7 0 32-14.3 32-32V205.8l136 136V840zM512 442c-79.5 0-144 64.5-144 144s64.5 144 144 144 144-64.5 144-144-64.5-144-144-144zm0 224c-44.2 0-80-35.8-80-80s35.8-80 80-80 80 35.8 80 80-35.8 80-80 80z"></path>
                                </svg>
                            </span>
                            
                        </span>
                    </button>
                </div>
            </div>
    
</form>
                            </div>
                        </main>


<script>

// media wordpress

jQuery(document).ready(function($) {
    var frame;

    // Função para abrir a Media Library para a Logo Horizontal
    $('#logo_horizontal_button').on('click', function(e) {
        e.preventDefault();

        // Se o frame já existir, apenas abra
        if (frame) {
            frame.open();
            return;
        }

        // Cria o frame da Media Library
        frame = wp.media({
            title: 'Selecionar Logo Horizontal',
            multiple: false, // Permite selecionar apenas uma imagem
            library: {
                type: 'image' // Filtra para mostrar apenas imagens
            },
            button: {
                text: 'Usar esta imagem'
            }
        });

        // Função para executar quando uma imagem é selecionada
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#logo_horizontal_preview').html('<img src="' + attachment.url + '" alt="Logo Horizontal Preview" style="max-width: 100%; height: auto;">');
            $('#logo_horizontal_id').val(attachment.id);
            $('#remover_logo_horizontal').show();
        });

        // Abre o frame
        frame.open();
    });

    // Função para remover a Logo Horizontal
    $('#remover_logo_horizontal').on('click', function(e) {
        e.preventDefault();
        $('#logo_horizontal_preview').html('');
        $('#logo_horizontal_id').val('');
        $(this).hide();
    });

    // Repete o processo para a Logo Vertical
    var frame_vertical;

    $('#logo_vertical_button').on('click', function(e) {
        e.preventDefault();

        if (frame_vertical) {
            frame_vertical.open();
            return;
        }

        frame_vertical = wp.media({
            title: 'Selecionar Logo Vertical',
            multiple: false,
            library: {
                type: 'image'
            },
            button: {
                text: 'Usar esta imagem'
            }
        });

        frame_vertical.on('select', function() {
            var attachment = frame_vertical.state().get('selection').first().toJSON();
            $('#logo_vertical_preview').html('<img src="' + attachment.url + '" alt="Logo Vertical Preview" style="max-width: 100%; height: auto;">');
            $('#logo_vertical_id').val(attachment.id);
            $('#remover_logo_vertical').show();
        });

        frame_vertical.open();
    });

    $('#remover_logo_vertical').on('click', function(e) {
        e.preventDefault();
        $('#logo_vertical_preview').html('');
        $('#logo_vertical_id').val('');
        $(this).hide();
    });

    $('#show_debug_button').on('change', function() {
        var nonce = '<?php echo wp_create_nonce('save_debug_button_setting'); ?>';
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_debug_button_setting',
                show_debug: $(this).is(':checked'),
                nonce: nonce
            },
            success: function(response) {
                console.log(response);
            }
        });
    });

    // Seu código AJAX de envio do formulário (que você já tem) deve permanecer o mesmo,
    // pois ele agora enviará os IDs das imagens nos campos hidden.
});    

jQuery(document).ready(function($) {
    $('#configuracoes_app').on('submit', function(e) {
        e.preventDefault(); // Impede o envio padrão do formulário

        // Coleta os dados do formulário
        var formData = new FormData(this);

        // Adiciona explicitamente os IDs das imagens dos campos hidden
        formData.append('logo_horizontal_id', $('#logo_horizontal_id').val());
        formData.append('logo_vertical_id', $('#logo_vertical_id').val());

        // Adiciona a ação do AJAX
        formData.append('action', 'salvar_configuracoes_app');

        console.log('Dados FormData antes do AJAX:');
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);
        }

        $.ajax({
            url: ajaxurl, // Variável global do WordPress para o arquivo admin-ajax.php
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Tratar a resposta de sucesso (exibir alerta Tailwind ou modal UIkit)
                console.log('Sucesso:', response);
                if (response.success) {
                    // Exibir mensagem de sucesso
                    var toastSuccess = `
                        <div id="toast-success" class="flex items-center w-full max-w-full p-4 mb-4 text-green-600 bg-green-200 rounded-lg shadow" role="alert">
                            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                </svg>
                                <span class="sr-only">Check icon</span>
                            </div>
                            <div class="ms-3 text-sm font-normal">${response.data.mensagem}</div>
                            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-200 text-green-600 hover:text-green-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-success" aria-label="Close">
                                <span class="sr-only">Close</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                        </div>
                    `;

                    $('#toastSalvo').html(toastSuccess);

                    // Fechar toast após 5 segundos
                    setTimeout(function() {
                        $('#toast-success').fadeOut(300, function() {
                            $(this).remove();
                            $('#toastSalvo').empty(); // Limpar a div após o fadeOut
                        });
                    }, 5000);
                } else {
                    // Exibir mensagens de erro
                    if (response.data && response.data.erros) {
                        $.each(response.data.erros, function(campo, mensagem) {
                            exibirAlertaErro(mensagem); // A função de erro você pode manter como está ou adaptar para um toast de erro similar
                        });
                    } else {
                        exibirAlertaErro('Ocorreu um erro ao salvar as configurações.');
                    }
                }
            },
            error: function(errorThrown) {
                // Tratar erros de requisição
                console.error('Erro:', errorThrown);
                exibirAlertaErro('Ocorreu um erro na requisição.');
            }
        });
    });

    // Funções de exemplo para exibir alertas (adapte para Tailwind ou UIkit)
    function exibirAlertaSucesso(mensagem) {
        // Exemplo com Tailwind CSS (você precisará ter a estrutura HTML para o alerta)
        $('body').append('<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Sucesso!</strong><span class="block sm:inline">' + mensagem + '</span><span class="absolute top-0 bottom-0 right-0 px-4 py-3"><svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path fill-rule="evenodd" d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.759 3.152a1.2 1.2 0 0 1 0 1.697z"/></svg></span></div>');
        // Remova o alerta após alguns segundos (opcional)
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }

    function exibirAlertaErro(mensagem) {
        // Exemplo com Tailwind CSS (você precisará ter a estrutura HTML para o alerta)
        $('body').append('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Erro!</strong><span class="block sm:inline">' + mensagem + '</span><span class="absolute top-0 bottom-0 right-0 px-4 py-3"><svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path fill-rule="evenodd" d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.759 3.152a1.2 1.2 0 0 1 0 1.697z"/></svg></span></div>');
        // Remova o alerta após alguns segundos (opcional)
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
});    


jQuery(document).ready(function($) {
    function atualizarCamposHiddenComIDs() {
        var logoHorizontalURL = $('input[name="acf[field_67fc5f59b750d]"]').val();
        var logoVerticalURL = $('input[name="acf[field_67fc5f78b750e]"]').val();
        var ajaxData = {
            'action': 'get_attachment_id_from_url',
            'logo_horizontal_url': logoHorizontalURL,
            'logo_vertical_url': logoVerticalURL
        };

        $.post(ajaxurl, ajaxData, function(response) {
            if (response.success) {
                if (response.data.logo_horizontal_id) {
                    $('#logo_horizontal_id').val(response.data.logo_horizontal_id);
                    console.log('Campo hidden logo_horizontal_id atualizado para:', response.data.logo_horizontal_id);
                    // Nova função para exibir a prévia da imagem horizontal
                    exibirPreviaImagem('logo_horizontal', response.data.logo_horizontal_id);
                }
                if (response.data.logo_vertical_id) {
                    $('#logo_vertical_id').val(response.data.logo_vertical_id);
                    console.log('Campo hidden logo_vertical_id atualizado para:', response.data.logo_vertical_id);
                    // Nova função para exibir a prévia da imagem vertical
                    exibirPreviaImagem('logo_vertical', response.data.logo_vertical_id);
                }
            } else {
                console.error('Erro ao obter IDs das imagens:', response.data);
            }
        });
    }

    function exibirPreviaImagem(prefixo, attachmentId) {
        if (attachmentId) {
            var ajaxDataPrevia = {
                'action': 'get_image_url_by_id',
                'attachment_id': attachmentId,
                'size': 'full' // Ou o tamanho desejado para a prévia
            };

            $.post(ajaxurl, ajaxDataPrevia, function(responsePrevia) {
                if (responsePrevia.success && responsePrevia.data.url) {
                    $('#' + prefixo + '_preview').html('<img src="' + responsePrevia.data.url + '" alt="' + prefixo + ' Preview" style="max-width: 100%; height: auto;">');
                    $('#remover_' + prefixo).show();
                } else {
                    $('#' + prefixo + '_preview').html(''); // Limpa a prévia em caso de erro
                    $('#remover_' + prefixo).hide();
                    console.error('Erro ao obter URL da imagem ' + prefixo + ':', responsePrevia.data);
                }
            });
        } else {
            $('#' + prefixo + '_preview').html('');
            $('#remover_' + prefixo).hide();
        }
    }

    // Chame a função para atualizar os campos hidden e exibir as prévias após o carregamento
    atualizarCamposHiddenComIDs();
});

</script>

<?php
get_footer();