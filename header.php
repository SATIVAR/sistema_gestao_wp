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
      <title>AMEDIS - ASSOCIAÇÃO MEDICINAL DE DIREITO A SAÚDE</title>
      <meta name="description" content="AMEDIS - Acesso à Saúde para Todos" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.21.5/dist/css/uikit.min.css" />
        <script src="https://cdn.tailwindcss.com"></script>
        <?php wp_head(); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6-beta.29/jquery.inputmask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   </head>
   <body class="md:min-h-[100vh]">

      <!-- App Start-->
      <div id="root">      

         <!-- App Layout-->
         <div class="app-layout-modern flex flex-auto flex-col">
            <div class="flex flex-auto min-w-0">
               <!-- Side Nav start-->
               <div class="side-nav bg-emerald-600 side-nav-expand side-nav-themed">
                  <div class="side-nav-header">
                     <div class="px-6 mt-3 md:mb-5">
                        <div class="uppercase font-bold text-base tracking-wider flex flex-row items-center justify-start w-full whitespace-nowrap text-white">
                           <a class="flex flex-row items-center justify-start space-x-2 hover:text-white" href="<?php bloginfo('url'); ?>">
                              <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="28" width="28" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                 <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                 <line x1="12" y1="22.08" x2="12" y2="12"></line>
                              </svg>
                              <span>ASSOC</span>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="side-nav-content relative side-nav-scroll">
                     <nav class="menu menu-transparent px-4 pb-4">
                        <div class="menu-group">
                           <div class="menu-title text-sm text-emerald-200">App</div>
                           <ul>

                              <li class="menu-collapse">
                                 <div class="menu-collapse-item">
                                    <svg class="menu-item-icon text-white" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="menu-item-text uppercase text-white">Associados</span>
                                 </div>
                                 <ul>
                                    <li data-menu-item="modern-crm-dashboard" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/novo-associado">
                                          <span>Novo Associado</span>
                                       </a>
                                    </li>
                                    <li data-menu-item="modern-calendar" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/todos-associados/">
                                          <span>Todos Associados</span>
                                       </a>
                                    </li>
                                    <li data-menu-item="modern-calendar" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/todos-usuarios/">
                                          <span>Agenda</span>
                                       </a>
                                    </li>                                    
                                    <li data-menu-item="modern-calendar" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/todos-usuarios/">
                                          <span>Fichas</span>
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                              <li class="menu-collapse">
                                 <div class="menu-collapse-item">



<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 menu-item-icon text-white">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>


                                    <span class="menu-item-text uppercase text-white">Financeiro</span>
                                 </div>
                                 <ul>

                                    <li data-menu-item="modern-product-list" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/todas-entradas">
                                          <span>Entradas</span>
                                       </a>
                                    </li>
                                    <li data-menu-item="modern-product-list" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/nova-entrada">
                                          <span>Nova Entrada</span>
                                       </a>
                                    </li>                                    
                                    <li data-menu-item="modern-product-list" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="#!">
                                          <span>Saidas</span>
                                       </a>
                                    </li>  
<!--      
                                    <li data-menu-item="modern-sales-dashboard" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/novo-produto/">
                                          <span>Novo Produto</span>
                                       </a>
                                    </li>
                                    <li data-menu-item="modern-sales-dashboard" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/todos-produtos/">
                                          <span>Todos Produtos</span>
                                       </a>
                                    </li>                                                                    
 
                                                               
                                    <li data-menu-item="modern-product-list" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="<?php bloginfo('url') ?>/novo-cliente">
                                          <span>Novo Cliente</span>
                                       </a>
                                    </li>                                     
                                    <li data-menu-item="modern-product-list" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="#!">
                                          <span>Todos Clientes</span>
                                       </a>
                                    </li>   
                                    -->                                 
                                 </ul>
                              </li>

                              <li class="menu-collapse">
                                 <div class="menu-collapse-item">
                                    <svg class="menu-item-icon text-white" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                    </svg>
                                    <span class="menu-item-text uppercase text-white">Relatórios</span>
                                 </div>
                                 <ul>
                                    <li data-menu-item="modern-chart" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="#!">
                                          <span>Associados</span>
                                       </a>
                                    </li>
                                    <li data-menu-item="modern-maps" class="menu-item">
                                       <a class="h-full w-full flex items-center" href="#!">
                                          <span>Financeiro</span>
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                           </ul>
                        </div>


                        <div class="menu-group">
                           <div class="menu-title menu-title-transparent text-sm text-emerald-200">
                              CONFIGURAÇÕES
                           </div>
                           <ul>
                              <li data-menu-item="modern-documentation" class="menu-item menu-item-single mb-2">
                                 <a class="menu-item-link" href="http://www.themenate.net/elstar-html-doc" target="_blank" >
                                    <span class="menu-item-icon">

                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="menu-item-icon text-white size-6">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                       </svg>

                                    </span>
                                    <span class="menu-item-text uppercase text-white">APLICAÇÃO</span>
                                 </a>
                              </li>                              
                           </ul>
                        </div>
                     </nav>   
                  </div>
               </div>
               <!-- Side Nav end-->

               <!-- Header Nav start-->
               <div class="flex flex-col flex-auto min-h-screen min-w-0 relative w-full bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
                  <header class="header border-b border-gray-200 dark:border-gray-700">
                     <div class="header-wrapper h-16">
                        <!-- Header Nav Start start-->
                        <div class="header-action header-action-start">
                           <div class="header-search header-action-item text-2xl">
                              <?php the_title(); ?>
                           </div>
                        </div>
                        <!-- Header Nav Start end-->
                        <!-- Header Nav End start -->
                        <div class="header-action header-action-end">
                           




<button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification" class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none" type="button">

         <svg class="w-7 h-7" stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
         </svg>

<div class="absolute block w-3 h-3 bg-red-500 border-2 border-white rounded-full -top-0.5 start-2.5"></div>
</button>

<!-- Dropdown menu -->
<div id="dropdownNotification" class="z-20 hidden w-full max-w-sm bg-white divide-y divide-gray-100 rounded-lg shadow" aria-labelledby="dropdownNotificationButton">
  <div class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50">
      Notifications
  </div>
  <div class="divide-y divide-gray-100">
    <a href="#" class="flex px-4 py-3 hover:bg-gray-100">
      <div class="flex-shrink-0">
        <img class="rounded-full w-11 h-11" src="/docs/images/people/profile-picture-1.jpg" alt="Jese image">
        <div class="absolute flex items-center justify-center w-5 h-5 ms-6 -mt-5 bg-blue-600 border border-white rounded-full">
          <svg class="w-2 h-2 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
            <path d="M1 18h16a1 1 0 0 0 1-1v-6h-4.439a.99.99 0 0 0-.908.6 3.978 3.978 0 0 1-7.306 0 .99.99 0 0 0-.908-.6H0v6a1 1 0 0 0 1 1Z"/>
            <path d="M4.439 9a2.99 2.99 0 0 1 2.742 1.8 1.977 1.977 0 0 0 3.638 0A2.99 2.99 0 0 1 13.561 9H17.8L15.977.783A1 1 0 0 0 15 0H3a1 1 0 0 0-.977.783L.2 9h4.239Z"/>
          </svg>
        </div>
      </div>
      <div class="w-full ps-3">
          <div class="text-gray-500 text-sm mb-1.5">New message from <span class="font-semibold text-gray-900">Jese Leos</span>: "Hey, what's up? All set for the presentation?"</div>
          <div class="text-xs text-blue-600">a few moments ago</div>
      </div>
    </a>
  </div>
  <a href="#" class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100">
    <div class="inline-flex items-center ">
      <svg class="w-4 h-4 me-2 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
        <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
      </svg>
        View all
    </div>
  </a>
</div>





<div type="button" data-dropdown-toggle="userDropdown" data-dropdown-placement="bottom-start" class="flex items-center gap-4 cursor-pointer">
                                 <div class="header-action-item flex items-center gap-2">
                                    <span class="avatar avatar-circle w-10 h-10 bg-green-200 text-green-700">

                                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                         <path d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                       </svg>

                                    </span>
                                    <div class="hidden md:block">
                                       <div class="text-xs capitalize">administrador</div>
                                       <div class="font-bold"><?php echo do_shortcode('[nome_usuario_logado]'); ?></div>
                                    </div>
                                 </div>
</div>


<!-- Dropdown menu -->
<div id="userDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
    <div class="px-4 py-3 text-sm text-gray-900">
      <div>Acesso Rápido</div>
    </div>
    <ul class="py-2 text-sm text-gray-700" aria-labelledby="avatarButton">
      <li>
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Painel</a>
      </li>
      <li>
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Configurações</a>
      </li>
      <li>
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Suporte</a>
      </li>
    </ul>
    <div class="py-1">
      <?php echo do_shortcode('[link_logout]'); ?>
    </div>
</div>
                        </div>
                        <!-- Header Nav End end -->
                     </div>
                  </header>         