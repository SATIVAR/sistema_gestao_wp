jQuery(document).ready(function($) {
    // Realiza o login AJAX ao submeter o formulário
    $('form#login-form').on('submit', function(e) {
        e.preventDefault();

        $('#status').hide(); // Esconde o alerta inicialmente

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: {
                'action': 'ajaxlogin', // Chama wp_ajax_nopriv_ajaxlogin
                'username': $('form#login-form input[name="username"]').val(),
                'password': $('form#login-form input[name="password"]').val(),
                'security': $('form#login-form #security').val()
            },
            success: function(data) {
                $('#status').show(); // Mostra o alerta
                $('#status-message').text(data.message); // Insere a mensagem no alerta
                
                if (data.loggedin === true) {
                    if (data.redirect) {
                        document.location.href = data.redirect;
                    } else {
                        document.location.href = ajax_login_object.redirecturl;
                    }
                }
            }
        });
    });
});
