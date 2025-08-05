<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ASSOC
 * @subpackage CAJU
 * @since ASSOC - CAJU 1.0
 */

?>

</div><!-- root -->


        <?php wp_footer(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6-beta.29/jquery.inputmask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    <!--
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.uikit.js"></script>-->

    




<?php if ( is_user_logged_in() && current_user_can('administrator') || current_user_can('gerente') ) { ?>
<?php // get_template_part('modal', 'pacientes'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/dashboard-search.js?v=<?php echo time(); ?>"></script>
<?php } ?>



<script type="text/javascript">
    
$('#cpf, #cpf_responsavel, #cpf_prescritor').inputmask('999.999.999-99', {
    placeholder: '___.___.___-__',
    clearIncomplete: true,
    clearMaskOnLostFocus: true
});

$('#data_nascimento, #data_nascimento_respon').inputmask('99/99/9999', {
    placeholder: 'DD/MM/AAAA',
    clearIncomplete: true,
    clearMaskOnLostFocus: true
});

$('#telefone_respon, #telefone_paciente, #whatsapp_pessoa, #telefone_prescritor').inputmask('(99) 99999-9999', {
    placeholder: '(DD) 9XXXX-XXXX',
    clearIncomplete: true,
    clearMaskOnLostFocus: true
});

$('#cep').inputmask('99999-999', {
    placeholder: '_____-___',
    clearIncomplete: true,
    clearMaskOnLostFocus: true
});

<?php if ( is_user_logged_in() && current_user_can('administrator') || current_user_can('gerente') ) { ?>

// relatorios associados




// copiar texto <a>

$(document).ready(function() {
    $('[data-txt-copy]').on('click', function(e) {
        e.preventDefault(); // Impede o comportamento padrão do link

        var content = $(this).html(); // Pega o HTML completo do elemento

        // Usa o Clipboard API para copiar com formatação
        navigator.clipboard.write([
            new ClipboardItem({
                "text/html": new Blob([content], { type: "text/html" }),
                "text/plain": new Blob([$(content).text()], { type: "text/plain" }) // Opcional: copia o texto simples também
            })
        ]).then(function() {
            // Exibe uma notificação de sucesso com UIkit
            UIkit.notification({
                message: 'Texto copiado com sucesso!',
                status: 'success',
                pos: 'top-center',
                timeout: 3000
            });
        }).catch(function(error) {
            console.error('Erro ao copiar: ', error);
        });
    });
});

// copiar texto remover caracteres

jQuery(document).ready(function($) {
    // Quando clicar em um link com o atributo data-copy-nb
    $('[data-copy-nb]').on('click', function(e) {
        e.preventDefault(); // Evita o comportamento padrão do link

        // Pega o conteúdo do link
        var numero = $(this).text();

        // Remove todos os caracteres que não sejam números
        var numeroLimpo = numero.replace(/\D/g, '');

        // Cria um elemento temporário para copiar o conteúdo
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(numeroLimpo).select();
        document.execCommand("copy");
        tempInput.remove();

        // Opcional: Aviso visual que o número foi copiado
        alert("Número copiado: " + numeroLimpo);
    });
});


// buscar no loop

$(document).ready(function() {
    $("#default-search").on("input", function() {
        var value = $(this).val().toLowerCase();
        
        // Se o campo estiver vazio, mostrar todos os itens
        if (value === "") {
            $(".loopSearch .itemSearch").show();
        } else {
            // Caso contrário, aplicar o filtro
            $(".loopSearch .itemSearch").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        }
    });
});

// copiar

$(document).ready(function() {
    $('#copy-btn').on('click', function() {
        // Obtém o conteúdo do div e preserva as quebras de linha
        var content = $('#content-msg-whats').html().replace(/<br\s*[\/]?>/gi, '\n').trim();
        
        // Cria um elemento de input temporário para copiar o texto
        var tempElement = $('<textarea>');
        $('body').append(tempElement);
        tempElement.val(content).select();

        // Copia o conteúdo para a área de transferência
        try {
            document.execCommand('copy');
            alert('Codigo de atendimento copiado com sucesso!');
        } catch (err) {
            alert('Falha ao copiar o conteúdo');
        }

        // Remove o elemento temporário
        tempElement.remove();
    });
});


// copiar HREF <a>

jQuery(document).ready(function ($) {
    $('[data-link-copy]').on('click', function (e) {
        e.preventDefault(); // Impede comportamento padrão do link, se for o caso
        const linkToCopy = $(this).attr('href'); // Pega o valor do atributo href

        // Cria um elemento temporário para copiar o texto
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(linkToCopy).select();
        document.execCommand('copy');
        tempInput.remove(); // Remove o elemento temporário

        // Notificação com UIkit
        UIkit.notification({
            message: 'Link copiado com sucesso!',
            status: 'success',
            pos: 'top-center',
            timeout: 3000
        });
    });
});



// funcao mensagens ajax 

$(document).ready(function() {
    carregarMensagens();

    $('#mensagemForm').on('submit', function(e) {
        e.preventDefault();

        var texto = $('#mensagemTexto').val();
        var categoria = $('#mensagemCategoria').val();
        var id = $('#mensagemId').val();

        if (texto.trim() !== '') {
            if (!id) {
                // Gera o ID se não estiver definido
                id = gerarId(texto);
                $('#mensagemId').val(id);
            }

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'salvar_mensagem',
                    mensagem: texto,
                    categoria: categoria,
                    id: id, // Usa o ID gerado
                    security: salvarMensagemNonce
                },
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        alert(response.message);
                        $('#mensagemTexto').val('');
                        $('#mensagemCategoria').val('geral');
                        $('#mensagemId').val(''); // Limpa o campo ID
                        carregarMensagens(); // Atualiza a lista de mensagens
                    } catch (e) {
                        console.error('Erro ao processar a resposta do servidor.', e);
                        alert('Erro ao processar a resposta do servidor.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao enviar a mensagem:', status, error);
                    alert('Erro ao enviar a mensagem');
                }
            });
        } else {
            alert('Por favor, escreva uma mensagem.');
        }
    });

    function gerarId(texto) {
        var dataHora = new Date().toISOString().replace(/[-T:.Z]/g, ''); // Gera a data e hora
        var primeiraPalavra = texto.split(' ')[0]; // Pega a primeira palavra do texto
        return dataHora + primeiraPalavra; // Cria o ID combinando data/hora e a primeira palavra
    }

    function carregarMensagens() {
        $.ajax({
            url: '<?= get_template_directory_uri(); ?>/mensagens.json',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (typeof data === 'object') {
                    var categorias = {};

                    // Agrupa mensagens por categoria
                    Object.values(data).forEach(function(mensagem) {
                        if (!categorias[mensagem.categoria]) {
                            categorias[mensagem.categoria] = [];
                        }
                        categorias[mensagem.categoria].push(mensagem);
                    });

                    var mensagensHTML = '';

                    // Cria HTML para cada categoria
                    for (var categoria in categorias) {
                        mensagensHTML += `<h2 class="text-xl font-bold">${categoria.toUpperCase()}</h2>`;
                        categorias[categoria].forEach(function(mensagem) {
                            mensagensHTML += `
                                <div class="bg-blue-100 rounded-lg p-4 flex justify-between">
                                    <div class="bubble-chat">
                                        <p>${mensagem.texto}</p>
                                    </div>
                                    <div>
                                        <button class="bg-yellow-500 text-white rounded-md p-1" onclick="editarMensagem('${mensagem.id}', '${mensagem.texto}', '${mensagem.categoria}')">Editar</button>
                                        <button class="bg-red-500 text-white rounded-md p-1" onclick="excluirMensagem('${mensagem.id}')">Excluir</button>
                                        <button class="bg-green-500 text-white rounded-md p-1" onclick="copiarMensagem('${mensagem.texto}')">Copiar</button>
                                    </div>
                                </div>`;
                        });
                    }
                    
                    $('#mensagens').html(mensagensHTML);
                } else {
                    $('#mensagens').html('<p>Dados inválidos recebidos.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar as mensagens:', status, error);
                $('#mensagens').html('<p>Nenhuma mensagem encontrada.</p>');
            }
        });
    }

    window.editarMensagem = function(id, texto, categoria) {
        $('#mensagemTexto').val(texto);
        $('#mensagemCategoria').val(categoria);
        $('#mensagemId').val(id); // Define o ID para edição
    };

    window.excluirMensagem = function(id) {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'excluir_mensagem',
                id: id,
                security: salvarMensagemNonce
            },
            success: function(response) {
                try {
                    response = JSON.parse(response);
                    alert(response.message);
                    carregarMensagens(); // Atualiza a lista de mensagens
                } catch (e) {
                    console.error('Erro ao processar a resposta do servidor.', e);
                    alert('Erro ao processar a resposta do servidor.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao excluir a mensagem:', status, error);
                alert('Erro ao excluir a mensagem');
            }
        });
    };

window.copiarMensagem = function(texto) {
    // Verifica se o navegador suporta a API de Clipboard
    if (navigator.clipboard && window.isSecureContext) {
        // Usa a API Clipboard moderna
        navigator.clipboard.writeText(texto).then(function() {
            alert('Mensagem copiada para a área de transferência!');
        }).catch(function(err) {
            console.error('Erro ao copiar para a área de transferência:', err);
            alert('Erro ao copiar a mensagem. Por favor, tente novamente.');
        });
    } else {
        // Método de fallback para navegadores mais antigos
        var tempInput = document.createElement('textarea');
        tempInput.style.position = 'absolute';
        tempInput.style.left = '-9999px';
        tempInput.value = texto;
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
            document.execCommand('copy');
            alert('Mensagem copiada para a área de transferência!');
        } catch (err) {
            console.error('Erro ao copiar para a área de transferência:', err);
            alert('Erro ao copiar a mensagem. Por favor, tente novamente.');
        }
        document.body.removeChild(tempInput);
    }
};

});


<?php } ?>


</script>



<?php
$show_debug_button = get_option('amedis_show_debug_button', false);
if ($show_debug_button && current_user_can('administrator')) : ?>
    
    <style>
    #debug-log-btn {
      position: fixed; bottom: 24px; right: 24px; z-index: 9999;
      background: #222; color: #fff; border-radius: 50%; width: 48px; height: 48px;
      display: flex; align-items: center; justify-content: center; font-size: 22px;
      box-shadow: 0 2px 8px #0002; cursor: pointer; transition: background .2s;
    }
    #debug-log-btn:hover { background: #38a169; }
    #debug-log-modal {
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 99999;
      background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;
    }
    #debug-log-modal .modal-content {
      background: #18181b; color: #e5e7eb; border-radius: 8px; max-width: 90vw; max-height: 80vh;
      padding: 24px; overflow: auto; font-family: monospace; font-size: 13px;
      box-shadow: 0 4px 32px #0008;
    }
    #debug-log-modal .close-btn {
      position: absolute; top: 16px; right: 32px; color: #fff; font-size: 24px; cursor: pointer;
    }
    </style>
    <div id="debug-log-btn" title="Ver Log Backend">🐞</div>
    <div id="debug-log-modal">
      <div class="modal-content">
        <span class="close-btn">&times;</span>
        <pre id="debug-log-content">Carregando log...</pre>
      </div>
    </div>
    <script>
    jQuery(function($){
      $('#debug-log-btn').on('click', function(){
        $('#debug-log-modal').fadeIn(150);
        $('#debug-log-content').text('Carregando log...');
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: { action: 'get_last_error_log' },
          success: function(res){
            if(res.success && res.data && res.data.log){
              $('#debug-log-content').text(res.data.log);
            } else {
              $('#debug-log-content').text(res.data && res.data.message ? res.data.message : 'Erro ao buscar log.');
            }
          },
          error: function(){
            $('#debug-log-content').text('Erro de comunicação ao buscar log.');
          }
        });
      });
      $('#debug-log-modal .close-btn, #debug-log-modal').on('click', function(e){
        if(e.target === this) $('#debug-log-modal').fadeOut(150);
      });
    });
    </script>
    <?php endif; ?>

	</body>
</html>
