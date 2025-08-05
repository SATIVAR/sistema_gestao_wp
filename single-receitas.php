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
Template Name: Dashboard - Editar receita
*/

get_header('zero');

if ( !is_user_logged_in() && !current_user_can('administrator') ) { 
    // Se o usuário não estiver logado, redireciona para a home
    //wp_redirect(home_url());
    //exit;
    ?>


            <?php  get_template_part('login'); ?>            


<?php } else { ?>
<?php 

get_template_part('header', 'user'); 

function ler_csv_datasus($caminho_arquivo) {
    $dados = array();
    if (($handle = fopen($caminho_arquivo, "r")) !== FALSE) {
        while (($linha = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Assumindo que seu CSV tem apenas uma coluna com os dados desejados
            if (isset($linha[0])) {
                $dados[] = trim($linha[0]); // Adiciona o valor da primeira coluna, removendo espaços extras
            }
        }
        fclose($handle);
    }
    return $dados;
}

// Define o caminho para o arquivo CSV
$caminho_csv = get_template_directory() . '/datasus/datasus.csv';

// Lê os dados do CSV
$dados_datasus = ler_csv_datasus($caminho_csv);

$id_paciente_receita = get_field('id_paciente_receita');
$data_emissao = get_field('data_emissao');
$data_vencimento = get_field('data_vencimento');
$arquivo_receita = get_field('arquivo_receita');
$arquivo_laudo = get_field('arquivo_laudo');
$prescritor_amedis_check = get_field('prescritor_amedis_check');
$nome_prescritor = get_field('nome_prescritor');
$prescritor_amedis = get_field('prescritor_amedis');
$desc_curta = get_field('desc_curta');
$cid_patologia = get_field('cid_patologia');

?>


					<main class="mt-5  bg-transparent pb-[60px]">
                        <div class="uk-container">


                                <div class="flex space-x-6">
                                    <div class="md:w-[100%]">


                                    <div class="card card-border mb-5">
                                            <div class="card-body">
                                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                                    <div class="flex items-center gap-2">

                                                        <div>
                                                            <div class="flex items-center">
                                                                <h6 class="font-medium">Editar Receita - <?php the_title(); ?></h6>

                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 items-center">
                                                        <a href="<?php echo bloginfo("url"); ?>/receitas/" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Voltar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <form id="editar-receita-form" action="POST">
                                        <?php wp_nonce_field('editar_receita_action', 'editar_receita_nonce'); ?>
                                        <input type="hidden" name="id_post" value="<?php the_ID(); ?>">

                                        <!-- ================== EDITAR RECEITA - FORM SECTIONS ================== -->
                                        <div class="space-y-6">
                                            <!-- Informações do Associado -->
                                            <div class="card card-border my-5 shadow-lg rounded-lg">
                                                <div class="card-body p-6">
                                                    <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                                        <span>Informações do Associado</span>
                                                    </h3>
                                                    <div class="flex items-center justify-start w-full space-x-2 pb-5 mb-5 border-b border-gray-200">
                                                        <!-- Campo associado -->
                                                        <div class="md:w-[50%]">
                                                            <label for="id_paciente_receita" class="block mb-2 text-sm font-medium text-gray-900">Selecione o associado</label>
                                                            
                                                            <?php
                                                            // Obter usuários com a função "associados"
                                                            $args = array(
                                                                'role'    => 'associados',
                                                                'orderby' => 'display_name',
                                                                'order'   => 'ASC',
                                                            );
                                                            $associados = get_users( $args );
                                                            ?>

                                                            <select id="select-associados" name="associado" class="w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 py-2.5" required>
                                                                <option value="" selected disabled>Selecione um associado</option>
                                                                <?php foreach ( $associados as $associado ) : 
                                                                    // Obtém os campos ACF para o usuário (considerando que os campos estão configurados para o objeto "user")
                                                                    $nome_completo       = get_field('nome_completo', 'user_' . $associado->ID);
                                                                    $tipo_associacao     = get_field('tipo_associacao', 'user_' . $associado->ID);
                                                                    $nome_completo_respon= get_field('nome_completo_respon', 'user_' . $associado->ID);
                                                                ?>
                                                                    <option value="<?php echo $associado->ID; ?>" class="uppercase"
                                                                        data-nome-completo="<?php echo esc_attr($nome_completo); ?>"
                                                                        data-tipo-associacao="<?php echo esc_attr($tipo_associacao); ?>"
                                                                        data-nome-completo-respon="<?php echo esc_attr($nome_completo_respon); ?>">
                                                                        <b><?php echo esc_html($nome_completo); ?></b>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            
                                                            <input type="hidden" name="acf[field_67e2dc3177f72]" id="id_paciente_receita" class="mt-2 w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 py-2.5" placeholder="ID do paciente" value="<?php echo esc_html($id_paciente_receita); ?>" readonly>
                                                        </div>
                                                        <!-- Descrição curta -->
                                                        <div class="md:w-[50%]">
                                                            <label for="desc_curta" class="block mb-2 text-sm font-medium text-gray-900">Descrição Curta</label>
                                                            <input type="text" name="acf[field_6808d39d15408]" id="desc_curta" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" value="<?php echo esc_html($desc_curta); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informações do Prescritor -->
                                            <div class="card card-border my-5 shadow-lg rounded-lg">
                                                <div class="card-body p-6">
                                                    <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                                        <span>Informações do Prescritor</span>
                                                    </h3>
                                                    <div class="w-full flex flex-col space-y-2">
                                                        <div class="esconde-prescrior-amedis">
                                                            <div class="w-full">
                                                                <label for="nome_prescritor" class="block mb-2 text-sm font-medium text-gray-900">Nome do Prescritor / Conselho / UF</label>
                                                                <input type="text" name="acf[field_67e3cd572a4c4]" id="nome_prescritor" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" value="<?php echo esc_attr($nome_prescritor); ?>">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                                                <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                                                    <div class="flex items-center ps-3">
                                                                        <input id="prescritor_amedis_check" name="acf[field_67e3cbb620e17]" type="checkbox" value="1" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2" <?php checked($prescritor_amedis_check, 1); ?>>
                                                                        <label for="prescritor_amedis_check" class="w-full py-3 ms-2 text-sm font-medium text-gray-900">Prescritor AMEDIS</label>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="flex items-center space-x-2 mostra-prescrior-amedis" style="<?php echo $prescritor_amedis_check ? '' : 'display:none;'; ?>">
                                                            <div class="w-[30%]">
                                                                <label for="prescritor_amedis" class="block mb-2 text-sm font-medium text-gray-900">ID_prescritor</label>
                                                                <input type="text" name="acf[field_67e3d1c98801a]" id="prescritor_amedis" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" readonly value="<?php echo esc_attr($prescritor_amedis); ?>">
                                                            </div>
                                                            <div class="w-full">

                                                            <?php
                                                            $prescritores = get_users(array('role' => 'prescritor'));
                                                            ?>

                                                                <label for="prescritor" class="block mb-2 text-sm font-medium text-gray-900">Prescritor</label>
                                                                <select id="prescritor" name="acf[field_67e3d1c98801a]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                                                    <option value="" selected disabled>Selecione</option>
                                                                    <?php foreach ($prescritores as $prescritor):
                                                                        $nome_completo_prescritor = get_field('nome_completo_prescritor', 'user_' . $prescritor->ID);
                                                                        $n_id_prescritor = get_field('n_id_prescritor', 'user_' . $prescritor->ID);
                                                                        $estado_id_conselho = get_field('estado_id_conselho', 'user_' . $prescritor->ID);
                                                                        $especialidade = get_field('especialidade', 'user_' . $prescritor->ID);
                                                                        $modo_consulta = get_field('modo_consulta', 'user_' . $prescritor->ID);
                                                                        $valor_consulta = get_field('valor_consulta', 'user_' . $prescritor->ID);
                                                                    ?>
                                                                        <option value="<?php echo $prescritor->ID; ?>" <?php selected($prescritor_amedis, $prescritor->ID); ?>>
                                                                            <span class="block">
                                                                                <?php echo esc_html($nome_completo_prescritor); ?> - <?php echo esc_html($n_id_prescritor); ?>/<?php echo esc_html($estado_id_conselho); ?>
                                                                            </span>
                                                                            <br>
                                                                            <span>
                                                                                <?php echo esc_html($especialidade); ?> | <?php echo esc_html($modo_consulta); ?> <?php echo esc_html($valor_consulta); ?>
                                                                            </span>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Detalhes da Receita -->
                                            <div class="card card-border my-5 shadow-lg rounded-lg">
                                                <div class="card-body p-6">
                                                    <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
                                                        <span>Detalhes da Receita</span>
                                                    </h3>
                                                    <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-2">
                                                        <!-- DATASUS -->
                                                        <div class="w-full">
                                                            <label for="datasus" class="block mb-2 text-sm font-medium text-gray-900">Patologia DATASUS: <span class="text-green-500 block"><?php echo esc_html($cid_patologia); ?></span></label>
                                                            
                                                            <select id="datasus" name="datasus" style="width: 300px;">
                                                                <option value="">Selecione</option>
                                                                <?php
                                                                if (!empty($dados_datasus)) {
                                                                    foreach ($dados_datasus as $item) {
                                                                        echo '<option value="' . esc_attr($item) . '">' . esc_html($item) . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <input type="text" name="acf[field_6808d3ab15409]" id="cid_patologia" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" value="<?php echo esc_attr($cid_patologia); ?>" hidden>
                                                        </div>
                                                        <!-- Data Emissão -->
                                                        <div class="w-full">
                                                            <label for="data_receita" class="block mb-2 text-sm font-medium text-gray-900">Data Emissão</label>
                                                            <input type="text" name="acf[field_67e2dc6b349dc]" id="data_receita" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" required value="<?php echo esc_html($data_emissao) ?>">
                                                        </div>
                                                        <!-- Tempo -->
                                                        <div class="w-full">
                                                            <label for="tempo_receita" class="block mb-2 text-sm font-medium text-gray-900">Tempo (meses)</label>
                                                            <input type="number" name="tempo_receita" id="tempo_receita" value="6" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500">
                                                        </div>
                                                        <!-- Vencimento -->
                                                        <div class="w-full">
                                                            <label for="data_vencimento" class="block mb-2 text-sm font-medium text-gray-900">Vencimento Receita</label>
                                                            <input type="text" name="acf[field_67e2dc7a349dd]" id="data_vencimento" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-green-500 focus:border-green-500" readonly value="<?php echo $data_vencimento ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

<!-- Anexos -->
<div class="card card-border my-5 shadow-lg rounded-lg">
    <div class="card-body p-6">
        <h3 class="px-5 border-y border-gray-100 mb-5 py-3 bg-gray-50 uppercase text-gray-800 font-bold rounded-t-lg -mx-6 -mt-6">
            <span>Anexos</span>
        </h3>
        <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 md:space-x-2">

            <!-- Arquivo da Receita -->
            <div class="w-full">
                <?php if ($arquivo_receita) : ?>
                    <a href="<?php echo esc_html($arquivo_receita); ?>" target="_blank" class="flex items-center justify-between w-full block rounded-lg bg-blue-50 p-3 mb-2">
                        <span>Visualizar Receita Atual</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                            </svg>                                
                        </span>
                    </a>
                <?php endif; ?>

                <label class="block mb-2 text-sm font-medium text-gray-900">Arquivo da Receita</label>
                
                <!-- Upload Zone -->
                <div id="upload-zone-receita" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                    <input type="file" id="file-input-receita" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
                    
                    <!-- Placeholder -->
                    <div id="upload-placeholder-receita" class="upload-placeholder">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                        <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
                    </div>
                    
                    <!-- Preview -->
                    <div id="upload-preview-receita" class="upload-preview hidden">
                        <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                            <div class="flex items-center space-x-3">
                                <div id="file-icon-receita" class="flex-shrink-0"></div>
                                <div class="text-left">
                                    <p id="file-name-receita" class="text-sm font-medium text-gray-900"></p>
                                    <p id="file-size-receita" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" id="remove-file-receita" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <img id="image-preview-receita" class="hidden mt-3 max-w-full h-48 object-cover rounded-lg">
                    </div>
                    
                    <!-- Progress -->
                    <div id="upload-progress-receita" class="upload-progress hidden mt-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div id="progress-bar-receita" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progress-text-receita" class="text-sm text-gray-600 mt-1">Enviando...</p>
                    </div>
                </div>
                
                <!-- Hidden inputs -->
                <input type="hidden" id="arquivo_receita_id" name="arquivo_receita_id">
                <input type="hidden" id="arquivo_receita_url" name="arquivo_receita_url">
            </div>

            <!-- Arquivo do Laudo -->
            <div class="w-full">
                <?php if ($arquivo_laudo) : ?>
                    <a href="<?php echo esc_html($arquivo_laudo); ?>" target="_blank" class="flex items-center justify-between w-full block rounded-lg bg-blue-50 p-3 mb-2">
                        <span>Visualizar Laudo Atual</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                            </svg>                                
                        </span>
                    </a>
                <?php endif; ?>

                <label class="block mb-2 text-sm font-medium text-gray-900">Arquivo do Laudo</label>
                
                <!-- Upload Zone -->
                <div id="upload-zone-laudo" class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer">
                    <input type="file" id="file-input-laudo" class="hidden" accept="image/jpeg,image/png,image/jpg,application/pdf">
                    
                    <!-- Placeholder -->
                    <div id="upload-placeholder-laudo" class="upload-placeholder">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Arraste o arquivo ou clique para selecionar</p>
                        <p class="text-sm text-gray-400 mt-1">PNG, JPG ou PDF (máx. 10MB)</p>
                    </div>
                    
                    <!-- Preview -->
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
                    
                    <!-- Progress -->
                    <div id="upload-progress-laudo" class="upload-progress hidden mt-4">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div id="progress-bar-laudo" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progress-text-laudo" class="text-sm text-gray-600 mt-1">Enviando...</p>
                    </div>
                </div>
                
                <!-- Hidden inputs -->
                <input type="hidden" id="arquivo_laudo_id" name="arquivo_laudo_id">
                <input type="hidden" id="arquivo_laudo_url" name="arquivo_laudo_url">
            </div>

        </div>
    </div>
</div>


                                            <!-- Ações -->
                                            <div class="card card-border mb-5 shadow-lg rounded-lg">
                                                <div class="card-body flex justify-between p-6">
                                                    <div>
                                                        <a href="<?php echo bloginfo('url'); ?>/receitas/" class="bg-red-800 text-white px-4 py-2 rounded text-sm">Cancelar</a>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="flex space-x-1 bg-green-800 text-white px-4 py-2 rounded text-sm">
                                                            <span>Salvar</span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class=" h-5 w-5 text-white ml-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 96l0 320c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-245.5c0-4.2-1.7-8.3-4.7-11.3l33.9-33.9c12 12 18.7 28.3 18.7 45.3L448 416c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l245.5 0c17 0 33.3 6.7 45.3 18.7l74.5 74.5-33.9 33.9L320.8 84.7c-.3-.3-.5-.5-.8-.8L320 184c0 13.3-10.7 24-24 24l-192 0c-13.3 0-24-10.7-24-24L80 80 64 80c-8.8 0-16 7.2-16 16zm80-16l0 80 144 0 0-80L128 80zm32 240a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"></path></svg>                                  
                                                        </button> 
                                                    </div>
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        <!-- ================== END FORM SECTIONS ================== -->

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
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Continuar Editando</button>
                <a href="<?php echo home_url('/receitas'); ?>" class="uk-button bg-green-800 rounded-md text-white hover:opacity-80">Ver Todas</a>
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
<script type="text/javascript">

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
        this.hiddenId = document.getElementById(`arquivo_${zoneId}_id`);
        this.hiddenUrl = document.getElementById(`arquivo_${zoneId}_url`);
        this.type = type;
        this.selectedFile = null;
        
        this.init();
    }
    
    init() {
        // Click events
        this.zone.addEventListener('click', () => this.input.click());
        this.input.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));
        this.removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeFile();
        });
        
        // Drag & Drop
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
        
        // Icon
        if (file.type.startsWith('image/')) {
            this.fileIcon.innerHTML = `<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`;
            
            // Image preview
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

// Inicializar uploads
const uploadReceita = new ModernFileUpload('receita', 'file-input-receita', 'receita');
const uploadLaudo = new ModernFileUpload('laudo', 'file-input-laudo', 'laudo');

jQuery(document).ready(function($) {
    // Inicializar Select2 para associados
    $('#select-associados').select2({
        placeholder: 'Selecione uma opção',
        allowClear: true,
        templateResult: function(option) {
            if (!option.id) {
                return option.text;
            }
            var nomeCompleto = $(option.element).data('nome-completo');
            var tipoAssociacao = $(option.element).data('tipo-associacao');
            var nomeRespon = $(option.element).data('nome-completo-respon');

            if (tipoAssociacao === 'assoc_respon' || tipoAssociacao === 'assoc_tutor') {
                var $container = $(
                    '<div>' +
                        '<div>' + nomeCompleto + '</div>' +
                        '<div style="font-size: 0.85em; color: #777;">Resp: ' + nomeRespon + '</div>' +
                    '</div>'
                );
                return $container;
            } else {
                return nomeCompleto;
            }
        }
    });

    // Preencher Select2 de associados na edição
    var idPacienteReceita = $('#id_paciente_receita').val();
    if (idPacienteReceita) {
        $('#select-associados').val(idPacienteReceita).trigger('change');
    }

    // Atualizar o campo ID do paciente quando selecionar um associado
    $('#select-associados').on('change', function() {
        $('#id_paciente_receita').val($(this).val());
    });

    // datasus
    $('#datasus').select2();
    $('#datasus').change(function() {
        $('#cid_patologia').val($(this).val());
    });

    // Aplicar Inputmask nos campos de data
    $("[id^='data_receita'], [id^='data_vencimento']").inputmask("99/99/9999", { "placeholder": "dd/mm/aaaa" });

    // Função para calcular a data de vencimento
    function calcularVencimento() {
        const dataReceita = $(`#data_receita`).val();
        const tempoReceita = parseInt($(`#tempo_receita`).val());

        if (dataReceita.length === 10) {
            const [dia, mes, ano] = dataReceita.split('/');
            const data = new Date(`${ano}-${mes}-${dia}`);

            if (!isNaN(data.getTime())) {
                data.setMonth(data.getMonth() + tempoReceita);
                const vencimento = `${("0" + data.getDate()).slice(-2)}/${("0" + (data.getMonth() + 1)).slice(-2)}/${data.getFullYear()}`;
                $(`#data_vencimento`).val(vencimento);
            }
        }
    }

    // Calcular o vencimento inicial na edição
    calcularVencimento();

    // Quando a data_receita ou tempo_receita mudar, calcular o vencimento
    $("[id^='data_receita'], [id^='tempo_receita']").on('input change', function() {
        calcularVencimento();
    });

    // prescritor
    $('input[type="checkbox"][id^="prescritor_amedis"]').on('change', function() {
        const amedisContent = $('.mostra-prescrior-amedis');
        const normalContent = $('.esconde-prescrior-amedis');

        if ($(this).is(':checked')) {
            amedisContent.show();
            normalContent.hide();
        } else {
            amedisContent.hide();
            normalContent.show();
            $('#prescritor_amedis').val('');
            $('#prescritor').val('');
        }
    });

    $('#prescritor').on('change', function() {
        const selectedValue = $(this).val();
        $('#prescritor_amedis').val(selectedValue);
    });

    // Restaurar o estado do prescritor na edição
    const amedisCheckboxEdit = $('#prescritor_amedis_check');
    const amedisContentEdit = $('.mostra-prescrior-amedis');
    const normalContentEdit = $('.esconde-prescrior-amedis');
    const prescritorAmedisIdEdit = $('#prescritor_amedis').val();

    if (amedisCheckboxEdit.length > 0) {
        if (amedisCheckboxEdit.prop('checked')) {
            amedisContentEdit.show();
            normalContentEdit.hide();
            if (prescritorAmedisIdEdit) {
                $('#prescritor').val(prescritorAmedisIdEdit);
            }
        } else {
            amedisContentEdit.hide();
            normalContentEdit.show();
        }
    }

    // Enviar o formulário via AJAX
    $('#editar-receita-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(form[0]);
        formData.append('security', $('#editar_receita_nonce').val());
        formData.append('action', 'editar_receita_ajax');
        formData.append('post_id', <?php echo get_the_ID(); ?>);

        // Adicionar arquivos selecionados
        if (uploadReceita.getSelectedFile()) {
            formData.append('receita_file', uploadReceita.getSelectedFile());
        }
        if (uploadLaudo.getSelectedFile()) {
            formData.append('laudo_file', uploadLaudo.getSelectedFile());
        }

        // Obter o nome do paciente para o título
        var pacienteId = $('#id_paciente_receita').val();
        var pacienteNome = $('#select-associados option:selected').text();
        var dataAtual = new Date();
        var dataFormatada = ('0' + dataAtual.getDate()).slice(-2) +
                            ('0' + (dataAtual.getMonth() + 1)).slice(-2) +
                            dataAtual.getFullYear() +
                            ('0' + dataAtual.getHours()).slice(-2) +
                            ('0' + dataAtual.getMinutes()).slice(-2);

        var tituloReceita = pacienteNome.trim() + ' ' + dataFormatada;
        formData.append('titulo_receita', tituloReceita);

        const submitButton = form.find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('Salvando... <span class="btn-spinner"></span>');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    $('#success-message').text(res.data.message);
                    UIkit.modal('#modal-success').show();
                    
                    // Reset uploads após sucesso
                    uploadReceita.removeFile();
                    uploadLaudo.removeFile();
                } else {
                    $('#error-message').text(res.data.message || 'Erro ao salvar a receita.');
                    UIkit.modal('#modal-error').show();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                $('#error-message').text('Erro de conexão ao salvar a receita.');
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
} 
get_footer();