<?php
/**
 * Header file for the Twenty Twenty WordPress default theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ASSOC
 * @subpackage CAJU
 * @since ASSOC - CAJU 1.0
 */

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="next-head-count" content="2" />
      <meta charset="utf-8" />
      <title>SATIVAR - Modernizando o Atendimento</title>
      <meta name="description" content="Acesso à Saúde para Todos" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

         <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">-->

        <?php wp_head(); ?>
        
   </head>
   <body class="bg-gray-100 md:min-h-[100vh]">

      <?php
if (is_user_logged_in()) {
?>
<div id="loading-overlay" class="fixed top-0 left-0 w-full h-full bg-gray-100 bg-opacity-[99] z-[9999!important] flex flex-col items-center justify-center overflow-hidden">


<?php
$logo_url = hg_exibir_campo_acf('logo_vertical', 'img', 'configuracoes');

if (!empty($logo_url)) {
    $imagem_final = $logo_url;
} else {
    // Concatena a URL do tema com o caminho relativo da imagem
    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_vert.png';
}
?>
<img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="w-48 h-auto mb-4"> 

    <div class="inline-flex items-center text-green-500">
        <svg aria-hidden="true" class="w-4 h-4 text-green-200 animate-spin fill-green-600 me-2" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
        <span class="animate-pulse text-green-800">Aguarde...</span>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loading-overlay');

        // Adiciona a classe de transição para o fade-out
        if (loadingOverlay) {
            loadingOverlay.classList.add('transition-opacity', 'duration-500');
        }

        window.addEventListener('load', function() {
            if (loadingOverlay) {
                // Inicia o fade-out alterando a opacidade
                loadingOverlay.style.opacity = '0';
                // Espera o tempo da transição antes de remover completamente
                setTimeout(function() {
                    loadingOverlay.style.display = 'none';
                }, 500); // 500ms corresponde à duração da transição
            }
        });

        // Opcional: Esconde após um tempo limite com fade-out
        // setTimeout(function() {
        //     if (loadingOverlay && loadingOverlay.style.display !== 'none') {
        //         loadingOverlay.style.opacity = '0';
        //         setTimeout(function() {
        //             loadingOverlay.style.display = 'none';
        //         }, 500);
        //     }
        // }, 5000);
    });
</script>
<?php
}
?>

      <!-- App Start-->
      <div id="root">  