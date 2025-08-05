<?php
/**
 * The template for displaying all pages
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link 'LinkGit'
 *
 * @package [ HG ]
 * @subpackage AMEDIS
 * @since [ HG ] W 1.0
/*
Template Name: Dashboard - Reset Senha
*/
get_header('zero');
echo do_shortcode('[cpf_pwd_reset]');
get_footer();