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
Template Name: Editar Prescritor
*/
if ( !is_user_logged_in() && !current_user_can('administrator') ) {
    // Se o usuário não estiver logado, redireciona para a home
    wp_redirect(home_url());
    exit;
}
get_header('zero');

ob_start(); // No início do seu script
if (isset($_GET['editar_prescritor'])) {
    $user_id = intval($_GET['editar_prescritor']);
    $user_info = get_userdata($user_id);
    $nome_completo_prescritor = get_user_meta($user_id, 'nome_completo_prescritor', true);
    $especialidade = get_user_meta($user_id, 'especialidade', true);
    $curriculo = get_user_meta($user_id, 'curriculo', true);
    
    $email_prescritor = get_user_meta($user_id, 'email_prescritor', true);
    $telefone_prescritor = get_user_meta($user_id, 'telefone_prescritor', true);
    $cpf_prescritor = get_user_meta($user_id, 'cpf_prescritor', true);
    $infos_consulta = get_user_meta($user_id, 'infos_consulta', true);
    $valor_consulta = get_user_meta($user_id, 'valor_consulta', true);
    $modo_consulta = get_user_meta($user_id, 'modo_consulta', true);
    $prescritor_amedis_ativo = get_user_meta($user_id, 'prescritor_amedis_ativo', true);
    $n_id_prescritor = get_user_meta($user_id, 'n_id_prescritor', true);
    $estado_id_conselho = get_user_meta($user_id, 'estado_id_conselho', true);

    $doc_frente_conselho_id = get_user_meta($user_id, 'doc_frente_conselho', true);
    $doc_verso_conselho_id = get_user_meta($user_id, 'doc_verso_conselho', true);
    $foto_site_id = get_user_meta($user_id, 'foto_site', true);

    $doc_frente_conselho_url = wp_get_attachment_url($doc_frente_conselho_id);
    $doc_verso_conselho_url = wp_get_attachment_url($doc_verso_conselho_id);
    $foto_site_url = wp_get_attachment_url($foto_site_id);


}
ob_end_flush(); // No final do seu script
?>
<?php get_template_part('header', 'user') ?>
					<main class="mt-5  bg-transparent pb-[60px]">
                        <div class="uk-container">
							<div class="flex flex-col gap-4 mt-5">


                                    <div class="flex flex-col gap-4 form-container vertical pb-12">


<?php if (isset($_GET['prescritor_salvo']) && $_GET['prescritor_salvo'] == 'true'): ?>
<div id="toast-success" class="flex items-center w-full max-w-full p-4 mb-4 text-green-600 bg-green-200 rounded-lg shadow" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Check icon</span>
    </div>
    <div class="ms-3 text-sm font-normal">Prescritor editado com sucesso.</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-200 text-green-600 hover:text-green-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-success" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
    </button>
</div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se o parâmetro 'user_registered' está presente na URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('user_registered') === 'true') {
        // Exibir o toast de sucesso
        const toast = document.getElementById('toast-success');
        if (toast) {
            toast.style.transition = 'opacity 0.5s ease';
            setTimeout(function() {
                toast.style.opacity = '1';
            }, 100); // Aparecer gradualmente

            // Remover o toast após 3 segundos
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() {
                    toast.remove();
                }, 500); // Tempo extra para a transição
            }, 13000);
        }

        // Remover o parâmetro da URL após o carregamento
        history.replaceState(null, '', window.location.pathname);
    }
});
</script>

<form id="edit_prescritor" action="" enctype="multipart/form-data" method="POST">
    <?php wp_nonce_field('edit_prescritor_action', 'edit_prescritor_nonce'); ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">



<div class="mb-5 card card-layout-frame">
    <div class="card-body">    
        <div class="flex justify-between space-x-2 uppercase font-semibold text-zinc-900">
            <div>
                <h3 class="mb-1 text-lg font-medium text-gray-900 uppercase">Editar Prescritor #<?php echo $user_id ?> - <?php echo $nome_completo_prescritor ?></h3>                      
            </div>

<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>


        </div>
    </div>
</div>   


          <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="sm:col-span-2">
                  <label for="nome_completo_prescritor" class="block mb-2 text-sm font-medium text-gray-900">Nome Completo *</label>
                  <input type="text" name="acf[field_66e5c2cd553d3]" id="nome_completo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Nome completo do Prescritor" required="" value="<?php echo $nome_completo_prescritor ?>">
              </div>

                <div class="w-full">
                    <label for="email_prescritor" class="block mb-2 text-sm font-medium text-gray-900">E-mail *</label>
                    <input type="text" name="acf[field_66e6494ba7957]" id="email_prescritor" class="email-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="E-mail Prescritor" required value="<?php echo $email_prescritor ?>"/>
                </div>

                <div class="w-full">
                    <label for="telefone_prescritor" class="block mb-2 text-sm font-medium text-gray-900">Whatsapp *</label>
                    <input type="text" name="acf[field_66e649d3ab0e1]" id="telefone_prescritor" class="email-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Whatsapp Prescritor" required value="<?php echo $telefone_prescritor ?>" />
                </div>

             
        </div>
        <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="w-full">
                  <label for="cpf_prescritor" class="block mb-2 text-sm font-medium text-gray-900">CPF *</label>
                  <input type="text" name="acf[field_66ed849450332]" id="cpf_prescritor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="CPF" required="" value="<?php echo $cpf_prescritor ?>">
              </div>

              <div class="w-full esconde-tutor">
                  <label for="n_id_prescritor" class="block mb-2 text-sm font-medium text-gray-900">Documento de identificação do conselho *</label>
                  <input type="text" name="acf[field_66e64bf206275]" id="n_id_prescritor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Documento de identificação do conselho" required="" value="<?php echo $n_id_prescritor ?>">
              </div>   

              <div class="w-full">
                  <label for="estado_id_conselho" class="block mb-2 text-sm font-medium text-gray-900">Estado *</label>
                  <input type="text" name="acf[field_66e5c2fc553d5]" id="estado_id_conselho" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Estado" required="" value="<?php echo $estado_id_conselho ?>">
              </div>

              <div class="w-full">
                  <label for="especialidade" class="block mb-2 text-sm font-medium text-gray-900">Especialidade *</label>
                  <input type="text" name="acf[field_66e5c319553d6]" id="especialidade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Especialidade" required="" value="<?php echo $especialidade ?>">
              </div>

<!--
              <div class="w-full">
                  <label for="genero_" class="block mb-2 text-sm font-medium text-gray-900">Gênero *</label>
                  <select id="genero_" name="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                      <option value="" disabled selected>Selecione uma opção</option>
                      <option value="masculino">Masculino</option>
                      <option value="feminio">Feminino</option>
                      <option value="naobinario">Não-Binário</option>
                      <option value="naoinformar">Não desejo informar</option>
                  </select>
              </div>
-->


              <div class="col-start-1 sm:col-span-4">
                    <label for="curriculo" class="block mb-2 text-sm font-medium text-gray-900">Biografia Divulgação (texto curto)</label>
                    <textarea id="curriculo" name="acf[field_66e5c320553d7]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Digite aqui..."><?php echo $curriculo ?></textarea>
              </div>

              <div class="col-start-1 sm:col-span-4">
                    <label for="infos_consulta" class="block mb-2 text-sm font-medium text-gray-900">Informações da Consulta</label>
                    <textarea id="infos_consulta" name="acf[field_6708f0ed66b80]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Digite aqui..."><?php echo $infos_consulta ?></textarea>
              </div>

                <div class="w-full">
                    <label for="modo_consulta" class="block mb-2 text-sm font-medium text-gray-900">Modo Consulta</label>
                    <select id="modo_consulta" name="acf[field_6708f11066b82]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        <option value="" <?php if (empty($modo_consulta)) echo 'selected'; ?> disabled>Selecione uma opção</option>
                        <option value="online" <?php if ($modo_consulta === 'online') echo 'selected'; ?>>Online</option>
                        <option value="presencial" <?php if ($modo_consulta === 'presencial') echo 'selected'; ?>>Presencial</option>
                        <option value="hibrida" <?php if ($modo_consulta === 'hibrida') echo 'selected'; ?>>Híbrida</option>
                    </select>
                </div>


              <div class="w-full">
                  <label for="valor_consulta" class="block mb-2 text-sm font-medium text-gray-900">Valor Consulta</label>
                  <input type="text" name="acf[field_6708f10766b81]" id="valor_consulta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="R$" required="" value="<?php echo $valor_consulta ?>">
              </div>

        </div>

          

            <div class="">
                <div class="card card-layout-frame my-8">
                    <div class="flex items-center justify-between card-body uppercase font-semibold text-zinc-900">
                        <span>DOCUMENTAÇÃO</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>                            
                        </span>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-3 sm:gap-4 mb-4">                    
                     <div class="w-full file-upload-container">
<div class="w-full">
    <label class="block mb-2 text-sm font-medium text-gray-900">Documento Frente do Conselho</label>
    
    <?php if ($doc_frente_conselho_url) : ?>
        <a href="<?php echo $doc_frente_conselho_url; ?>" target="_blank" class="flex items-center justify-between w-full block rounded-lg bg-blue-50 p-3 mb-2">
            <span>Visualizar Documento Atual</span>
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                </svg>
            </span>
        </a>
    <?php endif; ?>
    
    <div id="upload-zone-docfrente" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-docfrente" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
        
        <div id="upload-placeholder-docfrente" class="upload-placeholder">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
            <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-docfrente" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div id="file-icon-docfrente" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-docfrente" class="text-sm font-medium text-gray-900"></p>
                        <p id="file-size-docfrente" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-docfrente" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="image-preview-docfrente" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
        </div>
        
        <div id="upload-progress-docfrente" class="upload-progress hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar-docfrente" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progress-text-docfrente" class="text-sm text-gray-600 mt-1">Enviando...</p>
        </div>
    </div>
    
    <input type="hidden" id="doc_frente_conselho_id" name="doc_frente_conselho_id">
    <input type="hidden" id="doc_frente_conselho_url" name="doc_frente_conselho_url">
</div>

                        
                    </div>

                    <div class="w-full file-upload-container">
<div class="w-full">
    <label class="block mb-2 text-sm font-medium text-gray-900">Documento Verso do Conselho</label>
    
    <?php if ($doc_verso_conselho_url) : ?>
        <a href="<?php echo $doc_verso_conselho_url; ?>" target="_blank" class="flex items-center justify-between w-full block rounded-lg bg-blue-50 p-3 mb-2">
            <span>Visualizar Documento Atual</span>
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                </svg>
            </span>
        </a>
    <?php endif; ?>
    
    <div id="upload-zone-docverso" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-docverso" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
        
        <div id="upload-placeholder-docverso" class="upload-placeholder">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
            <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-docverso" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div id="file-icon-docverso" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-docverso" class="text-sm font-medium text-gray-900"></p>
                        <p id="file-size-docverso" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-docverso" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="image-preview-docverso" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
        </div>
        
        <div id="upload-progress-docverso" class="upload-progress hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar-docverso" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progress-text-docverso" class="text-sm text-gray-600 mt-1">Enviando...</p>
        </div>
    </div>
    
    <input type="hidden" id="doc_verso_conselho_id" name="doc_verso_conselho_id">
    <input type="hidden" id="doc_verso_conselho_url" name="doc_verso_conselho_url">
</div>

                        
                    </div>

                    <div class="w-full file-upload-container">
<div class="w-full">
    <label class="block mb-2 text-sm font-medium text-gray-900">Foto para Divulgação</label>
    
    <?php if ($foto_site_url) : ?>
        <a href="<?php echo $foto_site_url; ?>" target="_blank" class="flex items-center justify-between w-full block rounded-lg bg-blue-50 p-3 mb-2">
            <span>Visualizar Documento Atual</span>
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                </svg>
            </span>
        </a>
    <?php endif; ?>
    
    <div id="upload-zone-fotosite" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
        <input type="file" id="file-input-fotosite" class="hidden" accept="image/jpeg,image/png,image/jpg">
        
        <div id="upload-placeholder-fotosite" class="upload-placeholder">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
            <p class="text-sm text-gray-400 mt-1">PNG ou JPG (máx. 10MB)</p>
        </div>
        
        <div id="upload-preview-fotosite" class="upload-preview hidden">
            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div id="file-icon-fotosite" class="flex-shrink-0"></div>
                    <div class="text-left">
                        <p id="file-name-fotosite" class="text-sm font-medium text-gray-900"></p>
                        <p id="file-size-fotosite" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button type="button" id="remove-file-fotosite" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="image-preview-fotosite" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
        </div>
        
        <div id="upload-progress-fotosite" class="upload-progress hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar-fotosite" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progress-text-fotosite" class="text-sm text-gray-600 mt-1">Enviando...</p>
        </div>
    </div>
    
    <input type="hidden" id="foto_site_id" name="foto_site_id">
    <input type="hidden" id="foto_site_url" name="foto_site_url">
</div>

                        
                    </div>


                </div>


                <div class="flex items-center me-4 ps-4 py-4 border border-gray-200 rounded bg-green-50">
                    <input id="concorda" type="radio" value="" name="field_66dc3cda88d4c" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 focus:ring-2" checked>
                    <label for="concorda" class="ms-2 text-sm font-medium text-gray-900">Declaro que estou de acordo com o presente termo associativo e que todos os dados informados são verdadeiros. <a href="" class="text-blue-600 dark:text-blue-500 hover:underline">Saiba Mais</a></label>
                </div>

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
                    
                    <button class="btn btn-sm bg-green-500 text-white hover:opacity-50" type="submit" name="submit_edit_prescritor_form">
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



									</div>
                                </div><!-- container -->
								
							</main>


<script type="text/javascript">

class ModernFileUpload {
    constructor(zoneId, inputId, type) {
        this.zone = document.getElementById(`upload-zone-${zoneId}`);
        this.input = document.getElementById(`file-input-${zoneId}`);
        this.placeholder = document.getElementById(`upload-placeholder-${zoneId}`);
        this.preview = document.getElementById(`upload-preview-${zoneId}`);
        this.progress = document.getElementById(`upload-progress-${zoneId}`);
        this.progressBar = document.getElementById(`progress-bar-${zoneId}`);
        this.removeBtn = document.getElementById(`remove-file-${zoneId}`);
        this.fileName = document.getElementById(`file-name-${zoneId}`);
        this.fileSize = document.getElementById(`file-size-${zoneId}`);
        this.fileIcon = document.getElementById(`file-icon-${zoneId}`);
        this.imagePreview = document.getElementById(`image-preview-${zoneId}`);
        this.hiddenId = document.getElementById(`${type}_id`);
        this.hiddenUrl = document.getElementById(`${type}_url`);
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
        this.uploadToWordPress(file);
    }
    
    validateFile(file) {
        const maxSize = 10 * 1024 * 1024;
        const allowedTypes = this.type === 'foto_site' 
            ? ['image/jpeg', 'image/png', 'image/jpg'] 
            : ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido.');
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
            this.fileIcon.innerHTML = `<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview.src = e.target.result;
                this.imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            this.fileIcon.innerHTML = `<svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>`;
        }
    }
    
    uploadToWordPress(file) {
        const formData = new FormData();
        formData.append('action', 'upload_prescritor_file');
        formData.append('file', file);
        
        this.showProgress();
        
        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideProgress();
            if (data.success) {
                this.hiddenId.value = data.data.attachment_id;
                this.hiddenUrl.value = data.data.attachment_url;
            } else {
                alert('Erro no upload: ' + (data.data || 'Erro desconhecido'));
                this.removeFile();
            }
        })
        .catch(error => {
            this.hideProgress();
            alert('Erro no upload: ' + error.message);
            this.removeFile();
        });
    }
    
    showProgress() {
        if (this.progress) {
            this.progress.classList.remove('hidden');
            this.progressBar.style.width = '50%';
        }
    }
    
    hideProgress() {
        if (this.progress) {
            this.progress.classList.add('hidden');
            this.progressBar.style.width = '0%';
        }
    }
    
    removeFile() {
        this.placeholder.classList.remove('hidden');
        this.preview.classList.add('hidden');
        this.imagePreview.classList.add('hidden');
        this.input.value = '';
        this.selectedFile = null;
        if (this.hiddenId) this.hiddenId.value = '';
        if (this.hiddenUrl) this.hiddenUrl.value = '';
    }
    
    getSelectedFile() {
        return this.selectedFile;
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Inicializar uploads
const uploadDocFrente = new ModernFileUpload('docfrente', 'file-input-docfrente', 'doc_frente_conselho');
const uploadDocVerso = new ModernFileUpload('docverso', 'file-input-docverso', 'doc_verso_conselho');
const uploadFotoSite = new ModernFileUpload('fotosite', 'file-input-fotosite', 'foto_site');

</script>


<script type="text/javascript">

// Formulário normal - arquivos já foram enviados via AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Não precisa interceptar o submit - deixa o formulário funcionar normalmente
    // Os arquivos já foram enviados para o WordPress via AJAX quando selecionados
    // Os IDs dos arquivos estão nos campos hidden
});


</script>


<?php 
get_footer();
