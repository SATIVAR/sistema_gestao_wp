/**
 * Ativa o estado de carregamento de um botão.
 * @param {jQuery} $button O objeto jQuery do botão.
 * @param {string} loadingText O texto a ser exibido durante o carregamento. (Padrão: 'Salvando')
 */
function setButtonLoading($button, loadingText = 'Salvando') {
    // Armazena o HTML original se ainda não foi armazenado
    if (!$button.data('original-html')) {
        $button.data('original-html', $button.html());
    }
    $button.prop('disabled', true);
    $button.html(loadingText + ' <span class="btn-spinner"></span>');
}

/**
 * Restaura o estado original de um botão.
 * @param {jQuery} $button O objeto jQuery do botão.
 */
function resetButtonState($button) {
    // Restaura do HTML armazenado
    if ($button.data('original-html')) {
        $button.html($button.data('original-html'));
    }
    $button.prop('disabled', false);
}

/**
 * Exibe o spinner de carregamento do dashboard com transições suaves.
 */
function showDashboardLoading() {
    const spinner = document.getElementById('dashboard-spinner');
    const content = document.getElementById('dashboard-content');
    const error = document.getElementById('dashboard-error');
    
    if (spinner) {
        spinner.classList.remove('hidden');
    }
    if (content) {
        content.classList.add('opacity-0', 'pointer-events-none');
    }
    if (error) {
        error.classList.add('hidden');
    }
}

/**
 * Esconde o spinner e exibe o conteúdo com fade-in suave.
 */
function hideDashboardLoading() {
    const spinner = document.getElementById('dashboard-spinner');
    const content = document.getElementById('dashboard-content');
    const error = document.getElementById('dashboard-error');
    
    if (spinner) {
        spinner.classList.add('hidden');
    }
    if (content) {
        content.classList.remove('pointer-events-none');
        // Pequeno delay para garantir transição suave
        setTimeout(() => {
            content.classList.remove('opacity-0');
        }, 50);
    }
    if (error) {
        error.classList.add('hidden');
    }
}

/**
 * Exibe mensagem de erro no dashboard com animação.
 */
function showDashboardError() {
    const spinner = document.getElementById('dashboard-spinner');
    const content = document.getElementById('dashboard-content');
    const error = document.getElementById('dashboard-error');
    
    if (spinner) {
        spinner.classList.add('hidden');
    }
    if (content) {
        content.classList.add('opacity-0', 'pointer-events-none');
    }
    if (error) {
        error.classList.remove('hidden');
    }
}


