<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Daftar halaman statis. File ini DIHASILKAN oleh: php index.php tools convert <slug>
| Kunci = slug URL ('home' untuk halaman depan).
*/
$config['pages'] = array (
  'home' => 
  array (
    'source' => 'index.html',
    'view' => 'pages/home',
    'title' => 'Politeknik STMI Jakarta &#8211; Politeknik STMI Jakarta',
    'body_attrs' => ' class="home wp-singular page-template page-template-elementor_header_footer page page-id-490 wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-template-full-width elementor-kit-9 elementor-page elementor-page-490" data-link="type-2" data-prefix="single_page" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/WebPage"',
    'head' => 'home',
    'foot' => 'home',
    'menu_active' => 
    array (
      1794 => 'current-menu-item page_item page-item-490 current_page_item',
    ),
    'img_hints' => 
    array (
      'header:0' => 'fetchpriority="high" ',
      'header:2' => 'fetchpriority="high" ',
    ),
    'footer_logo_post_image' => false,
  ),
  'sejarah-kampus' => 
  array (
    'source' => 'sejarah-kampus/index.html',
    'view' => 'pages/sejarah-kampus',
    'title' => 'Sejarah Kampus &#8211; Politeknik STMI Jakarta',
    'body_attrs' => ' class="wp-singular page-template-default page page-id-778 wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-kit-9 ct-elementor-default-template" data-link="type-2" data-prefix="single_page" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/WebPage"',
    'head' => 'sejarah-kampus',
    'foot' => 'archive',
    'menu_active' => 
    array (
      1795 => 'current-menu-ancestor current-menu-parent',
      1816 => 'current-menu-item page_item page-item-778 current_page_item',
    ),
    'img_hints' => 
    array (
      'footer:0' => 'loading="lazy" ',
    ),
    'footer_logo_post_image' => false,
  ),
  'visi-dan-misi' => 
  array (
    'source' => 'visi-dan-misi/index.html',
    'view' => 'pages/visi-dan-misi',
    'title' => 'Visi dan Misi &#8211; Politeknik STMI Jakarta',
    'body_attrs' => ' class="wp-singular page-template-default page page-id-786 wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-kit-9 elementor-page elementor-page-786 ct-elementor-default-template" data-link="type-2" data-prefix="single_page" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/WebPage"',
    'head' => 'visi-dan-misi',
    'foot' => 'visi-dan-misi',
    'menu_active' => 
    array (
      1795 => 'current-menu-ancestor current-menu-parent',
      1822 => 'current-menu-item page_item page-item-786 current_page_item',
    ),
    'img_hints' => 
    array (
      'header:1' => 'loading="lazy" ',
      'header:3' => 'loading="lazy" ',
      'footer:0' => 'loading="lazy" ',
    ),
    'footer_logo_post_image' => false,
  ),
);
