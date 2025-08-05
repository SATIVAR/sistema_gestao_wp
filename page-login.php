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
Template Name: Login
*/
//acf_form_head();
get_header('zero');
?>


<?php 
if (is_user_logged_in()) {
    // Se o usuário estiver logado, adicionar um script de redirecionamento
    ?>
    <script type="text/javascript">
        window.location.href = "<?php echo home_url('/todos-associados/'); ?>";
    </script>
    <?php
} else {
    // Exibir o conteúdo normal para usuários não logados
    get_template_part('login');
}
?>


<?php 
get_footer();
