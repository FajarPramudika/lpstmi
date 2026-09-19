<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Daftar halaman statis. File ini DIHASILKAN oleh: php index.php tools convert <slug>
| Kunci = slug URL ('home' untuk halaman depan).
*/
$config['pages'] = array (
  'error-404' => 
  array (
    'source' => 'js15_as.html',
    'view' => 'pages/error-404',
    'title' => 'Page not found &#8211; Politeknik STMI Jakarta',
    'body_attrs' => ' class="error404 wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-kit-9 ct-elementor-default-template" data-link="type-2" data-prefix="" data-header="type-1:sticky" data-footer="type-1"',
    'head' => 'error-404',
    'foot' => 'error-404',
    'img_hints' => 
    array (
      'header:0' => 'fetchpriority="high" ',
      'header:2' => 'fetchpriority="high" ',
    ),
    'footer_logo_post_image' => false,
    'verify_url' => 'js15_as.js',
  ),
  'home' => 
  array (
    'source' => 'index.html',
    'view' => 'pages/home',
    'title' => 'Politeknik STMI Jakarta &#8211; Politeknik STMI Jakarta',
    'body_attrs' => ' class="home wp-singular page-template page-template-elementor_header_footer page page-id-490 wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-template-full-width elementor-kit-9 elementor-page elementor-page-490" data-link="type-2" data-prefix="single_page" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/WebPage"',
    'head' => 'home',
    'foot' => 'home',
    'img_hints' => 
    array (
      'header:0' => 'fetchpriority="high" ',
      'header:2' => 'fetchpriority="high" ',
    ),
    'footer_logo_post_image' => false,
  ),
);
