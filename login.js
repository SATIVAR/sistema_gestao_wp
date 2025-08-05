    jQuery(document).ready(function($) {
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            var username = $('input[name="userName"]').val();
            var password = $('input[name="password"]').val();
            
            $.ajax({
                url: ajaxurl, // O WordPress define isso automaticamente
                type: 'POST',
                data: {
                    action: 'ajax_login', // Nome da ação AJAX
                    username: username,
                    password: password
                },
                success: function(response) {
                    if(response.success) {
                        window.location.href = response.data.redirect; // Redireciona após login bem-sucedido
                    } else {
                        alert(response.data.message); // Mostra mensagem de erro
                    }
                }
            });
        });
    });