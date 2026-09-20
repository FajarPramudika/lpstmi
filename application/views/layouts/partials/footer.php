<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<footer id="footer" class="ct-footer" data-id="type-1" itemscope="" itemtype="https://schema.org/WPFooter"><div data-row="middle"><div class="ct-container"><div data-column="widget-area-2"><div class="ct-widget is-layout-flow widget_media_image" id="media_image-3"><img <?= wp_img_hint($img_hints, 'footer:0') ?>width="196" height="147" src="<?= base_url('wp-content/uploads/2024/03/Logo-Kan.png') ?>" class="image wp-image-612  attachment-full size-full<?= $footer_logo_post_image ? ' wp-post-image' : '' ?>" alt="" style="max-width: 100%; height: auto;" decoding="async" /></div><div class="widget_text ct-widget is-layout-flow widget_custom_html" id="custom_html-3"><div class="textwidget custom-html-widget"><!-- Histats.com  (div with counter) --><div id="histats_counter"></div>
<!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,2578358,4,430,112,75,00011101']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="<?= site_url('') ?>" target="_blank"><img <?= wp_img_hint($img_hints, 'footer:1') ?> src="//sstatic1.histats.com/0.gif?2578358&amp;101" alt="" border="0"></a></noscript>
<!-- Histats.com  END  --></div></div></div><div data-column="widget-area-1"><div class="ct-widget is-layout-flow widget_nav_menu" id="nav_menu-2"><h3 class="widget-title">Link :</h3><div class="menu-footer-menu-container"><ul id="menu-footer-menu" class="widget-menu"><?php foreach ($footer_links as $i => $link): ?><li id="menu-item-<?= 619 + $i ?>" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-<?= 619 + $i ?>"><a href="<?= html_escape(safe_href($link['url'])) ?>"><?= html_escape($link['title']) ?></a></li>
<?php endforeach; ?></ul></div></div></div><div data-column="widget-area-3"><div class="ct-widget is-layout-flow widget_text" id="text-4"><h3 class="widget-title">Alamat :</h3>			<div class="textwidget"><p>Jl Letjen Suprapto No. 26 RT. 000 RW. 000<br />
Cempaka Putih Timur, Cempaka Putih<br />
Kota Jakarta Pusat &#8211; 10510</p>
</div>
		</div><div class="widget_text ct-widget is-layout-flow widget_custom_html" id="custom_html-2"><div class="textwidget custom-html-widget"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15866.81479988534!2d106.8678534!3d-6.1704146!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5043973ff63%3A0xc125c1242e567fd1!2sPolytechnic%20STMI%20Jakarta!5e0!3m2!1sen!2sid!4v1709964453699!5m2!1sen!2sid" width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div></div></div></div></div><div data-row="bottom"><div class="ct-container"><div data-column="copyright">
<div
	class="ct-footer-copyright"
	data-id="copyright">

	<p>Copyright © 2026 - Pusdata STMI</p></div>
</div></div></div></footer>