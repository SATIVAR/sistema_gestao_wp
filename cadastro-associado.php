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
Template Name: Cadastro Associado
*/
get_header('zero');
?>
<?php get_template_part('header', 'user') ?>
					<main class="mt-5  bg-transparent pb-[60px]">
                        <div class="uk-container">
							<div class="flex flex-col gap-4 mt-5">


                                    <div class="flex flex-col gap-4 form-container vertical pb-12">


<?php if (isset($_GET['user_registered']) && $_GET['user_registered'] == 'true'): ?>
<div id="toast-success" class="flex items-center w-full max-w-full p-4 mb-4 text-green-600 bg-green-200 rounded-lg shadow" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Check icon</span>
    </div>
    <div class="ms-3 text-sm font-normal">Paciente cadastrado com sucesso.</div>
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

<form action="" enctype="multipart/form-data" method="POST">
    <?php wp_nonce_field('associado_action', 'associado_nonce'); ?>
    <!-- Campo de e-mail oculto -->
    <input type="hidden" id="email_oculto" name="acf[field_66b244e3d8b86]" />
    <input type="hidden" id="telefone_oculto" name="acf[field_6671b1480d481]" />

<div class="mb-5">
<div class="mb-5 card card-layout-frame">
    <div class="card-body">    
        <div class="flex justify-between space-x-2 uppercase font-semibold text-zinc-900">
            <div>
                <h3 class="mb-1 text-lg font-medium text-gray-900 uppercase">Cadastro de Paciente</h3>
                      
            </div>
            <div class="flex space-x-2">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
</svg>

                <div class="hidden">
                            <label class="inline-flex items-center cursor-pointer">
                              <input type="checkbox" class="sr-only peer" id="associado" name="acf[field_66b23d4bf502a]" value="1" checked disabled>
                              <input type="hidden" name="acf[field_66b23d4bf502a]" value="1" checked>
                              <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:bg-green-400 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all"></div>
                              <span class="hidden ms-3 text-sm font-medium text-gray-900">Ativar Associação</span>
                            </label>             
                </div>

            </div>
        </div>
    </div>
</div>   


<span class="block text-sm mt-5 font-bold">Selecione o tipo desejado abaixo *</span>   
<ul class="grid w-full gap-6 <?php if ( is_user_logged_in() && current_user_can('administrator') ) { ?> md:grid-cols-4 <?php } else { ?> md:grid-cols-3 <?php } ?> mt-5 mb-5" uk-height-match="target: > li > label">
    <li class="">
        <input type="radio" id="assoc_paciente" name="acf[field_66b40ca7a5636]" value="assoc_paciente" class="hidden  peer">
        <label for="assoc_paciente" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer  peer-checked:border-green-600 peer-checked:bg-green-50 hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50">                           
            <div class="block flex space-x-2">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div>
                    <div class="w-full text-lg font-semibold">Paciente</div>
                    <div class="w-full text-xs">Associação para uma pessoa.</div>
                </div>
            </div>
        </label>
    </li>
    <li class="">
        <input type="radio" id="assoc_respon" name="acf[field_66b40ca7a5636]" value="assoc_respon" class="hidden  peer">
        <label for="assoc_respon" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer  peer-checked:border-green-600 peer-checked:bg-green-50 hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50">                           
            <div class="block flex space-x-2">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div>
                    <div class="w-full text-lg font-semibold">Responsável pelo Paciente</div>
                    <div class="w-full text-xs">Associação para responsáveis por pacientes.</div>
                </div>
            </div>
        </label>
    </li>
    <li class="">
        <input type="radio" id="assoc_tutor" name="acf[field_66b40ca7a5636]" value="assoc_tutor" class="hidden  peer">
        <label for="assoc_tutor" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer  peer-checked:border-green-600 peer-checked:bg-green-50 hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50">                           
            <div class="block flex space-x-2">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <div>
                    <div class="w-full text-lg font-semibold">Tutor de Animal</div>
                    <div class="w-full text-xs">Associação para tutores de pets.</div>
                </div>
            </div>
        </label>
    </li>    
<?php if ( is_user_logged_in() && current_user_can('administrator') ) { ?>
    <li class="">
        <input type="radio" id="assoc_colab" name="acf[field_66b40ca7a5636]" value="assoc_colab" class="hidden  peer">
        <label for="assoc_colab" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer  peer-checked:border-green-600 peer-checked:bg-green-50 hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50">                           
            <div class="block flex space-x-2">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <div>
                    <div class="w-full text-lg font-semibold">Colaborador</div>
                    <div class="w-full text-xs">Associação para colaboradores.</div>
                </div>
            </div>
        </label>
    </li> 
<?php } ?>

</ul>


</div>

          <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="sm:col-span-2">
                  <label for="nome_completo" class="block mb-2 text-sm font-medium text-gray-900">Nome Completo *</label>
                  <input type="text" name="acf[field_666c8794b6c48]" id="nome_completo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Nome completo paciente" required="">
              </div>

              <div class="w-full mostra-tutor" style="display:none;">
                  <label for="idade" class="block mb-2 text-sm font-medium text-gray-900">Idade *</label>
                  <input type="text" name="acf[field_66dc4d9b7f708]" id="idade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Idade do paciente" required="">
              </div>

                <div class="w-full esconde-responsavel">
                    <label for="email_paciente" class="block mb-2 text-sm font-medium text-gray-900">E-mail *</label>
                    <input type="email" name="email_paciente" id="email_paciente" class="email-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="E-mail do paciente" />
                </div>

              <div class="w-full esconde-tutor">
                  <label for="data_nascimento" class="block mb-2 text-sm font-medium text-gray-900">Data Nascimento *</label>
                  <input type="text" name="acf[field_66db09c620956]" id="data_nascimento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Data nascimento do Paciente" required="">
              </div> 


             
        </div>
        <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="w-full esconde-tutor">
                  <label for="cpf" class="block mb-2 text-sm font-medium text-gray-900">CPF *</label>
                  <input type="text" name="acf[field_666c87a7b6c49]" id="cpf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="CPF do Paciente" required="">
              </div>   

              <div class="w-full esconde-tutor">
                  <label for="rg" class="block mb-2 text-sm font-medium text-gray-900">RG *</label>
                  <input type="text" name="acf[field_66db09b0b6165]" id="rg" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="RG do Paciente" required="">
              </div>

              <div class="w-full esconde-tutor">
                  <label for="genero" class="block mb-2 text-sm font-medium text-gray-900">Gênero *</label>
                  <select id="genero" name="acf[field_66db09e620957]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                      <option value="" disabled selected>Selecione uma opção</option>
                      <option value="masculino">Masculino</option>
                      <option value="feminio">Feminino</option>
                      <option value="naobinario">Não-Binário</option>
                      <option value="naoinformar">Não desejo informar</option>
                  </select>
              </div>


              <div class="w-full esconde-responsavel">
                  <label for="profissao" class="block mb-2 text-sm font-medium text-gray-900">Profissão *</label>
                  <input type="text" name="acf[field_66db0a3f8d587]" id="profissao" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Profissão do Paciente" >
              </div>


            <div class="w-full">
                <label for="lano_escolhas" class="block mb-2 text-sm font-medium text-gray-900">Tem plano de saúde? *</label>
                <select id="lano_escolhas" name="acf[field_66db0a604696d]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                    <option value="" disabled selected>Selecione uma opção</option>
                    <option value="nao">Não</option>
                    <option value="sim">Sim</option>
                </select>
            </div>

            <div class="w-full mostraPlano" style="display:none;">
                <label for="plano_saude" class="block mb-2 text-sm font-medium text-gray-900">Plano de saúde *</label>
                <input type="text" name="acf[field_66db0a9e2e313]" id="plano_saude" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Digite Aqui">
            </div>




        </div>
        <div class="card card-layout-frame my-8">
            <div class="flex items-center justify-between card-body uppercase font-semibold text-zinc-900">
                <span>Informações de Contato</span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m6.115 5.19.319 1.913A6 6 0 0 0 8.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 0 0 2.288-4.042 1.087 1.087 0 0 0-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 0 1-.98-.314l-.295-.295a1.125 1.125 0 0 1 0-1.591l.13-.132a1.125 1.125 0 0 1 1.3-.21l.603.302a.809.809 0 0 0 1.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 0 0 1.528-1.732l.146-.292M6.115 5.19A9 9 0 1 0 17.18 4.64M6.115 5.19A8.965 8.965 0 0 1 12 3c1.929 0 3.716.607 5.18 1.64" />
                    </svg>                    
                </span>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">
              <div class="sm:col-span-2">
                  <label for="endereco" class="block mb-2 text-sm font-medium text-gray-900">Endereço Completo *</label>
                  <input type="text" name="acf[field_666c87b0b6c4a]" id="endereco" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Endereço do Paciente" required>
              </div>

              <div class="w-full">
                  <label for="numero" class="block mb-2 text-sm font-medium text-gray-900">Número *</label>
                  <input type="text" name="acf[field_66db0ac742700]" id="numero" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Número do Paciente" required>
              </div> 

              <div class="w-full">
                  <label for="bairro" class="block mb-2 text-sm font-medium text-gray-900">Bairro *</label>
                  <input type="text" name="acf[field_666c87b6b6c4b]" id="bairro" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Bairro do Paciente" required>
              </div>


              <div class="sm:col-span-3">
                  <label for="complemento" class="block mb-2 text-sm font-medium text-gray-900">Completemento / Referência</label>
                  <input type="text" name="acf[field_66db0adb42701]" id="complemento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Complemento Endereço do Paciente" >
              </div>


              <div class="w-full">
                  <label for="cep" class="block mb-2 text-sm font-medium text-gray-900">CEP *</label>
                  <input type="text" name="acf[field_66db0af08561d]" id="cep" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="CEP do Paciente" required>
              </div>
          </div>
          <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="w-full">
                  <label for="cidade" class="block mb-2 text-sm font-medium text-gray-900">Cidade *</label>
                  <input type="text" name="acf[field_666c87bcb6c4c]" id="cidade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Cidade do Paciente" required>
              </div> 
              <div class="w-full">
                  <label for="estado" class="block mb-2 text-sm font-medium text-gray-900">Estado/UF *</label>

    <select id="select-uf" name="select-uf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
        <option value="" disabled selected>Selecione uma opção</option>
        <option value="ac">Acre</option>
        <option value="al">Alagoas</option>
        <option value="ap">Amapá</option>
        <option value="am">Amazonas</option>
        <option value="ba">Bahia</option>
        <option value="ce">Ceará</option>
        <option value="df">Distrito Federal</option>
        <option value="es">Espírito Santo</option>
        <option value="go">Goiás</option>
        <option value="ma">Maranhão</option>
        <option value="mt">Mato Grosso</option>
        <option value="ms">Mato Grosso do Sul</option>
        <option value="mg">Minas Gerais</option>
        <option value="pa">Pará</option>
        <option value="pb">Paraíba</option>
        <option value="pr">Paraná</option>
        <option value="pe">Pernambuco</option>
        <option value="pi">Piauí</option>
        <option value="rj">Rio de Janeiro</option>
        <option value="rn">Rio Grande do Norte</option>
        <option value="rs">Rio Grande do Sul</option>
        <option value="ro">Rondônia</option>
        <option value="rr">Roraima</option>
        <option value="sc">Santa Catarina</option>
        <option value="sp">São Paulo</option>
        <option value="se">Sergipe</option>
        <option value="to">Tocantins</option>
    </select>

                  <input type="text" name="acf[field_66db0b0e8561e]" id="estado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Estado do Paciente" required hidden>
              </div>               
              <div class="w-full esconde-responsavel">
                  <label for="telefone_paciente" class="block mb-2 text-sm font-medium text-gray-900">Telefone *</label>
                  <input type="text" name="telefone_paciente" id="telefone_paciente" class="tel-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Celular do Paciente" required="">
              </div>  
              <div class="w-full hidden" hidden style="display:none;">
                  <label for="cidade_" class="block mb-2 text-sm font-medium text-gray-900">EXEMPLO</label>
                  <select id="cidade_" name="acf[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                      <option value="" disabled selected>Selecione uma opção</option>
                      <option value="Choró">Choró</option>
                      <option value="Guanacés">Guanacés</option>
                      <option value="Cascavel">Cascavel</option>
                  </select>
              </div> 
       
 

              <div class="col-start-1 sm:col-span-4 hidden">
                    <label for="observacoes" class="block mb-2 text-sm font-medium text-gray-900">Observações</label>
                    <textarea id="observacoes" name="acf[field_666c87e6b6c4d]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Digite aqui..."></textarea>
              </div>                                                     
          </div>

        <div class="">
              <div class="card card-layout-frame my-8">
                <div class="flex items-center justify-between card-body uppercase font-semibold text-zinc-900">
                    <span>QUADRO GERAL DE SAÚDE</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>                        
                    </span>
                </div>
            </div>
              <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="col-start-1 sm:col-span-4">
                    <label for="diagnostico" class="block mb-2 text-sm font-medium text-gray-900">Descreva os diagnósticos de patologias existentes (Sintomas e etc) *</label>
                    <textarea id="diagnostico" name="acf[field_66db3419a6abf]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Digite aqui..." required></textarea>
              </div>


                <div class="w-full">
                    <label for="usa_medicacao" class="block mb-2 text-sm font-medium text-gray-900">Usa medicação? *</label>
                    <select id="usa_medicacao" name="acf[field_66db346780264]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                    </select>
                </div>

                <div class="w-full">
                    <label for="fez_uso_canabis_escolha" class="block mb-2 text-sm font-medium text-gray-900">Já fez uso terapêutico com a cannabis? *</label>
                    <select id="fez_uso_canabis_escolha" name="acf[field_66db34a5824c8]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                    </select>
                </div>

                <div class="col-start-1 sm:col-span-2">
                    <label for="medico_canabis_escolhas" class="block mb-2 text-sm font-medium text-gray-900">É acompanhado por médico prescritor de cannabis? *</label>
                    <select id="medico_canabis_escolhas" name="acf[field_66db34cd0310a]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                    </select>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">

              <div class="mostraMedicacao w-full" style="display:none;">
                  <label for="qual_medicacao" class="block mb-2 text-sm font-medium text-gray-900">Qual medicação? *</label>
                  <input type="text" name="acf[field_66db348c6b29d]" id="qual_medicacao" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Digite aqui" required="">
              </div> 


              <div class="mostraMPrescritor w-full" style="display:none;">
                  <label for="nome_profissional" class="block mb-2 text-sm font-medium text-gray-900">Qual o nome do profissional? *</label>
                  <input type="text" name="acf[field_66db34fbb2202]" id="nome_profissional" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Digite aqui" required="">
              </div> 

              <div class="mostraMPrescritor w-full" style="display:none;">
                  <label for="crm_profi" class="block mb-2 text-sm font-medium text-gray-900">CRM / UF do profissional  *</label>
                  <input type="text" name="acf[field_66db3509b2203]" id="crm_profi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Digite aqui" required="">
              </div>               


          </div>
      </div>


          <div class="mostra-responsavel" style="display:none">
            <div class="card card-layout-frame my-8">
                <div class="flex items-center justify-between card-body uppercase font-semibold text-zinc-900">
                    <span>Informações do Responsável</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>                        
                    </span>
                </div>
            </div>
              <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">
                  <div class="sm:col-span-2">
                      <label for="nome_completo_respon" class="block mb-2 text-sm font-medium text-gray-900">Nome Completo *</label>
                      <input type="text" name="acf[field_66dc418137429]" id="nome_completo_respon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Nome completo responsável" required="">
                  </div> 

                    <div class="w-full mostra-responsavel">
                        <label for="email_responsavel" class="block mb-2 text-sm font-medium text-gray-900">E-mail do Responsável *</label>
                        <input type="email" name="email_responsavel" id="email_responsavel" class="email-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="E-mail do responsável" />
                    </div>

                  <div class="w-full">
                      <label for="data_nascimento_respon" class="block mb-2 text-sm font-medium text-gray-900">Data de Nascimento *</label>
                      <input type="text" name="acf[field_66dc41a93742a]" id="data_nascimento_respon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Data de Nascimento do responsável" required="">
                  </div>

                  <div class="w-full">
                      <label for="genero_responsavel" class="block mb-2 text-sm font-medium text-gray-900">Gênero *</label>
                      <select id="genero_responsavel" name="acf[field_66dc41b93742b]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                          <option value="" disabled selected>Selecione uma opção</option>
                          <option value="masculino">Masculino</option>
                          <option value="feminio">Feminino</option>
                          <option value="naobinario">Não-Binário</option>
                          <option value="naoinformar">Não desejo informar</option>
                      </select>
                  </div>

                  <div class="w-full">
                      <label for="cpf_responsavel" class="block mb-2 text-sm font-medium text-gray-900">CPF *</label>
                      <input type="text" name="acf[field_66dc41f6e8aea]" id="cpf_responsavel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="CPF do responsável" required="">
                  </div>

                  <div class="w-full">
                      <label for="rg_responsavel" class="block mb-2 text-sm font-medium text-gray-900">RG *</label>
                      <input type="text" name="acf[field_66dc4201e8aeb]" id="rg_responsavel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="RG do responsável" required="">
                  </div>    

                  <div class="w-full">
                      <label for="profissao_responsavel" class="block mb-2 text-sm font-medium text-gray-900">Profissão *</label>
                      <input type="text" name="acf[field_66dc4211e8aec]" id="profissao_responsavel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Profissão do responsável" required="">
                  </div>  
                  <div class="w-full">
                      <label for="telefone_responsavel" class="block mb-2 text-sm font-medium text-gray-900">Telefone *</label>
                      <input type="text" name="telefone_responsavel" id="telefone_responsavel" class="tel-temp bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="Celular do Responsável" required="">
                  </div> 
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
                <div class="grid gap-4 sm:grid-cols-4 sm:gap-4 mb-4">  
                    
                    <!-- Comprovante RG -->
                    <div class="w-full file-upload-container">
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Comprovante Documento Pessoal do Paciente<br>
                             <span class="text-xs">(RG ou CNH frente e verso)</span></label>
                            
                            <div id="upload-zone-comprova-rg" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                                <input type="file" id="file-input-comprova-rg" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
                                
                                <div id="upload-placeholder-comprova-rg" class="upload-placeholder">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                                    <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
                                </div>
                                
                                <div id="upload-preview-comprova-rg" class="upload-preview hidden">
                                    <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                                        <div class="flex items-center space-x-3">
                                            <div id="file-icon-comprova-rg" class="flex-shrink-0"></div>
                                            <div class="text-left">
                                                <p id="file-name-comprova-rg" class="text-sm font-medium text-gray-900"></p>
                                                <p id="file-size-comprova-rg" class="text-xs text-gray-500"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-file-comprova-rg" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <img id="image-preview-comprova-rg" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
                                </div>
                                
                                <div id="upload-progress-comprova-rg" class="upload-progress hidden mt-4">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div id="progress-bar-comprova-rg" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="progress-text-comprova-rg" class="text-sm text-gray-600 mt-1">Enviando...</p>
                                </div>
                            </div>
                            
                            <input type="hidden" id="comprova_rg_paciente_id" name="comprova_rg_paciente_id">
                            <input type="hidden" id="comprova_rg_paciente_url" name="comprova_rg_paciente_url">
                        </div>
                    </div>                

                    <!-- Comprovante de Endereço -->
                    <div class="w-full file-upload-container">
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Comprovante de Endereço</label>
                            
                            <div id="upload-zone-comprova-end" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                                <input type="file" id="file-input-comprova-end" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
                                
                                <div id="upload-placeholder-comprova-end" class="upload-placeholder">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                                    <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
                                </div>
                                
                                <div id="upload-preview-comprova-end" class="upload-preview hidden">
                                    <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                                        <div class="flex items-center space-x-3">
                                            <div id="file-icon-comprova-end" class="flex-shrink-0"></div>
                                            <div class="text-left">
                                                <p id="file-name-comprova-end" class="text-sm font-medium text-gray-900"></p>
                                                <p id="file-size-comprova-end" class="text-xs text-gray-500"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-file-comprova-end" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <img id="image-preview-comprova-end" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
                                </div>
                                
                                <div id="upload-progress-comprova-end" class="upload-progress hidden mt-4">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div id="progress-bar-comprova-end" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="progress-text-comprova-end" class="text-sm text-gray-600 mt-1">Enviando...</p>
                                </div>
                            </div>
                            
                            <input type="hidden" id="comprova_end_paciente_id" name="comprova_end_paciente_id">
                            <input type="hidden" id="comprova_end_paciente_url" name="comprova_end_paciente_url">
                        </div>
                    </div>



                    <!-- Laudo Médico -->
                    <div class="w-full file-upload-container">
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Laudo Médico</label>
                            
                            <div id="upload-zone-laudo" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                                <input type="file" id="file-input-laudo" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
                                
                                <div id="upload-placeholder-laudo" class="upload-placeholder">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                                    <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
                                </div>
                                
                                <div id="upload-preview-laudo" class="upload-preview hidden">
                                    <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                                        <div class="flex items-center space-x-3">
                                            <div id="file-icon-laudo" class="flex-shrink-0"></div>
                                            <div class="text-left">
                                                <p id="file-name-laudo" class="text-sm font-medium text-gray-900"></p>
                                                <p id="file-size-laudo" class="text-xs text-gray-500"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-file-laudo" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <img id="image-preview-laudo" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
                                </div>
                                
                                <div id="upload-progress-laudo" class="upload-progress hidden mt-4">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div id="progress-bar-laudo" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="progress-text-laudo" class="text-sm text-gray-600 mt-1">Enviando...</p>
                                </div>
                            </div>
                            
                            <input type="hidden" id="laudo_paciente_id" name="laudo_paciente_id">
                            <input type="hidden" id="laudo_paciente_url" name="laudo_paciente_url">
                        </div>
                    </div>

<!-- Termo Associativo -->
<div class="w-full file-upload-container">
    <div class="w-full">
        <label class="block mb-2 text-sm font-medium text-gray-900">Termo Associativo Assinado (opicinal)</label>
        
        <div id="upload-zone-termo" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
            <input type="file" id="file-input-termo" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
            
            <div id="upload-placeholder-termo" class="upload-placeholder">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
            </div>
            
            <div id="upload-preview-termo" class="upload-preview hidden">
                <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                    <div class="flex items-center space-x-3">
                        <div id="file-icon-termo" class="flex-shrink-0"></div>
                        <div class="text-left">
                            <p id="file-name-termo" class="text-sm font-medium text-gray-900"></p>
                            <p id="file-size-termo" class="text-xs text-gray-500"></p>
                        </div>
                    </div>
                    <button type="button" id="remove-file-termo" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <img id="image-preview-termo" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
            </div>
            
            <div id="upload-progress-termo" class="upload-progress hidden mt-4">
                <div class="bg-gray-200 rounded-full h-2">
                    <div id="progress-bar-termo" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="progress-text-termo" class="text-sm text-gray-600 mt-1">Enviando...</p>
            </div>
        </div>
        
        <input type="hidden" id="termo_associativo_id" name="termo_associativo_id">
        <input type="hidden" id="termo_associativo_url" name="termo_associativo_url">
    </div>
</div>



                </div>


                <div class="flex items-center ps-4 py-4 border border-gray-200 rounded bg-green-100 mt-4">
                    <input id="concorda" type="radio" value="" name="field_66dc3cda88d4c" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 focus:ring-2" checked>
                    <label for="concorda" class="ms-2 text-md font-medium text-gray-900">Declaro que estou de acordo com o presente termo associativo e que todos os dados informados são verdadeiros. <a href="#!/termo-de-adesao/" target="_blank" class="text-blue-600 dark:text-blue-500 hover:underline">Saiba Mais</a></label>
                </div>

            </div>          





            <div id="stickyFooter_" class="w-full mt-5 px-6 flex items-center border border-gray-300 justify-between py-4 bg-white rounded-lg">
                <a class="btn btn-plain btn-sm" href="<?php bloginfo('url') ?>/todos-associados">
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



									</div>
                                </div><!-- container -->
								
							</main>




<script type="text/javascript">
jQuery(document).ready(function($) {


    // CLONAR ESTADO DO SELECT

    $('#select-uf').change(function() {
        var estadoSelecionado = $(this).val();
        $('#estado').val(estadoSelecionado);
    });    


    // Função genérica para mostrar ou esconder campos com base no select
    function toggleField(selectId, targetClass, required = false) {
        var selectedValue = $(selectId).val();

        if (selectedValue === 'sim') {
            $(targetClass).show();
            $(targetClass + ' input').attr('required', required);
        } else {
            $(targetClass).hide();
            $(targetClass + ' input').removeAttr('required');
        }
    }

    // Inicializar e monitorar alterações nos selects
    function initSelectToggle(selectId, targetClass, required = false) {
        toggleField(selectId, targetClass, required); // Inicializa

        $(selectId).on('change', function() {
            toggleField(selectId, targetClass, required); // Atualiza ao mudar
        });
    }

    // Lista de selects e seus respectivos campos a serem mostrados/escondidos
    var selectToggles = [
        {selectId: '#lano_escolhas', targetClass: '.mostraPlano', required: true},
        {selectId: '#usa_medicacao', targetClass: '.mostraMedicacao', required: true},
        {selectId: '#medico_canabis_escolhas', targetClass: '.mostraMPrescritor', required: true},
        {selectId: '#receita_laudo_escolhas', targetClass: '.mostraReceita', required: true}
    ];

    // Inicializa todos os selects
    selectToggles.forEach(function(item) {
        initSelectToggle(item.selectId, item.targetClass, item.required);
    });

    // Gerenciar inputs do tipo arquivo
    // Quando um arquivo é selecionado em qualquer input file
    $(document).on('change', '.file-input', function() {
        var fileInput = $(this);
        var fileName = fileInput.val().split('\\').pop(); // Obtém o nome do arquivo
        var container = fileInput.closest('.file-container'); // Container do input file

        // Atualiza o conteúdo do container para mostrar o nome do arquivo
        container.find('.file-details').html(
            '<p class="mb-2 text-sm text-gray-500 text-center"><span class="font-semibold">Arquivo selecionado: <br>' + fileName + '</span></p>' +
            '<p class="text-xs text-gray-500">Clique para selecionar um novo arquivo</p>'
        );
    });

    // Abre o seletor de arquivos ao clicar no container
    // Dispara o seletor de arquivo apenas uma vez ao clicar no container
    $(document).on('click', '.file-container', function(event) {
        var fileInput = $(this).find('.file-input');

        // Previne o comportamento padrão do clique e evita loops
        event.stopPropagation();
        event.preventDefault();

        // Abre o seletor de arquivo
        fileInput.trigger('click');
    });

    // Previne o duplo acionamento do seletor ao clicar na área do arquivo
    $(document).on('click', '.file-input', function(event) {
        event.stopPropagation();
    });

    // Remover o atributo 'required' de todos os inputs com a classe .file-input
    $('.file-input').removeAttr('required');

    function toggleResponsavelFields() {
        var selectedValue = $('input[name="acf[field_66b40ca7a5636]"]:checked').val();

        // Mostrar ou esconder seções com base na seleção dos radio buttons
        if (selectedValue === 'assoc_respon') {
            $('.mostra-responsavel').show().find('select, input').prop('required', true);
            $('.esconde-responsavel').hide().find('select, input').prop('required', false).val('');
            $('.mostra-tutor').hide().find('select, input').prop('required', false).val('');
            $('.esconde-tutor').show().find('select, input').prop('required', true);
        } else if (selectedValue === 'assoc_tutor') {
            $('.mostra-tutor').show().find('select, input').prop('required', true);
            $('.esconde-tutor').hide().find('select, input').prop('required', false).val('');
            $('.esconde-responsavel').hide().find('select, input').prop('required', false).val('');
            $('.mostra-responsavel').show().find('select, input').prop('required', true);
        } else {
            $('.mostra-responsavel').hide().find('select, input').prop('required', false).val('');
            $('.esconde-responsavel').show().find('select, input').prop('required', true);
            $('.mostra-tutor').hide().find('select, input').prop('required', false).val('');
            $('.esconde-tutor').show().find('select, input').prop('required', true);
        }
    }

    // Monitorar a mudança nos radio buttons
    $('input[name="acf[field_66b40ca7a5636]"]').on('change', toggleResponsavelFields);

    // Verificar o estado inicial dos radio buttons ao carregar a página
    $(document).ready(function() {
        toggleResponsavelFields();
    });

    // Copiar valores dos campos visíveis para os campos ocultos
    function atualizarCampoOculto() {
        var emailPaciente = $('#email_paciente').val().trim();
        var emailResponsavel = $('#email_responsavel').val().trim();
        var telPaciente = $('#telefone_paciente').val().trim();
        var telResponsavel = $('#telefone_responsavel').val().trim();

        // Atualizar o campo de e-mail oculto
        $('#email_oculto').val(emailResponsavel || emailPaciente || '');

        // Atualizar o campo de telefone oculto
        $('#telefone_oculto').val(telResponsavel || telPaciente || '');
    }

    // Monitorar a mudança nos campos temporários e atualizar os campos ocultos
    $(document).on('input change', '.email-temp, .tel-temp', atualizarCampoOculto);

    // Atualizar os campos ocultos ao carregar a página se os campos visíveis já estiverem preenchidos
    $(document).ready(atualizarCampoOculto);


    // Função para validar e-mails e telefones ao enviar o formulário
    $('form').on('submit', function(event) {
        // Verificar se algum radio button foi selecionado
        var isRadioChecked = $('input[name="acf[field_66b40ca7a5636]"]:checked').length > 0;
        
        // Se nenhum radio button estiver selecionado, exibir alerta e impedir envio
        if (!isRadioChecked) {
            event.preventDefault(); // Impede o envio do formulário
            alert('Por favor, selecione um tipo de associação antes de continuar.');
            return false;
        }

        // Adicione aqui suas outras validações (como e-mail e telefone)
        atualizarCampoOculto();

        var emailOculto = $('#email_oculto').val().trim();
        var telefoneOculto = $('#telefone_oculto').val().trim();

        // Validar e-mail
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailOculto && !emailPattern.test(emailOculto)) {
            event.preventDefault();
            alert('O e-mail é inválido. Verifique e tente novamente.');
            return false;
        }

        // Validar telefone
        var telefonePattern = /^\d{10,11}$/;

        // Remover caracteres não numéricos do telefone antes de validar
        var telefoneLimpo = telefoneOculto.replace(/[^\d]/g, '');

        if (telefoneLimpo && !telefonePattern.test(telefoneLimpo)) {
            event.preventDefault();
            alert('O telefone é inválido. Verifique e tente novamente.');
            return false;
        }
    });

});

</script>


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
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
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
        formData.append('action', 'upload_associado_file');
        formData.append('file', file);
        formData.append('security', window.uploadAssociadoNonce); 
        
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
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Inicializar uploads
const uploadComprovaEnd = new ModernFileUpload('comprova-end', 'file-input-comprova-end', 'comprova_end_paciente');
const uploadComprovaRg = new ModernFileUpload('comprova-rg', 'file-input-comprova-rg', 'comprova_rg_paciente');
const uploadLaudo = new ModernFileUpload('laudo', 'file-input-laudo', 'laudo_paciente');
const uploadTermo = new ModernFileUpload('termo', 'file-input-termo', 'termo_associativo');

// Formulário normal - arquivos já foram enviados via AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Os arquivos já foram enviados para o WordPress via AJAX quando selecionados
    // Os IDs dos arquivos estão nos campos hidden
});
</script>

<?php 
get_footer();
