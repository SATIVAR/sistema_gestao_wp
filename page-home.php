<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage Tronozelo e Pe
 * @since [ HG ] W 1.0
/*
Template Name: Home
*/
//acf_form_head();
get_header('zero');
?>





<section class="md:h-[100vh] flex items-center justify-center">
    <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-12">
        
<?php
$logo_url = hg_exibir_campo_acf('logo_vertical', 'img', 'configuracoes');

if (!empty($logo_url)) {
    $imagem_final = $logo_url;
} else {
    // Concatena a URL do tema com o caminho relativo da imagem
    $imagem_final = get_stylesheet_directory_uri() . '/assets/images/logo_vert.png';
}
?>

<img src="<?php echo $imagem_final; ?>" alt="SATIVAR" class="mt-5 mb-0 max-w-[320px] mx-auto">                
        <div class="mb-8 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48"><?php echo hg_exibir_campo_acf('texto_apresentacao', 'editor', 'configuracoes'); ?></div>
        <div class="flex flex-col mb-8 lg:mb-16 space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
<?php 
if ( !is_user_logged_in() && !current_user_can('administrator') ) { 
    // Se o usuário não estiver logado, redireciona para a home
    //wp_redirect(home_url());
    //exit;

?>
            <a href="<?php bloginfo('url') ?>/login" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>

                Login
            </a> 
<?php } else { ?>

            <a href="<?php bloginfo('url') ?>/todos-associados/" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>

                Todos Associados
            </a>

<?php } ?>            

            <a href="<?php bloginfo('url') ?>/cadastro-paciente" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white bg-green-600 rounded-lg border border-gray-300 hover:text-white hover:opacity-85 focus:ring-4 focus:ring-gray-100">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
</svg>

                Cadastro de Paciente
            </a>  
        </div>
    
    </div>
</section>
<?php 
get_footer();
