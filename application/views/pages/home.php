<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<main id="main" class="site-main hfeed">

				<div data-elementor-type="wp-page" data-elementor-id="490" class="elementor elementor-490">
				<div class="elementor-element elementor-element-ae2344a animated-slow e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="ae2344a" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInDown&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-20c89a9 elementor-arrows-position-inside elementor-widget elementor-widget-image-carousel" data-id="20c89a9" data-element_type="widget" data-settings="{&quot;slides_to_show&quot;:&quot;1&quot;,&quot;navigation&quot;:&quot;arrows&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;infinite&quot;:&quot;yes&quot;,&quot;effect&quot;:&quot;slide&quot;,&quot;speed&quot;:500}" data-widget_type="image-carousel.default">
				<div class="elementor-widget-container">
							<div class="elementor-image-carousel-wrapper swiper" role="region" aria-roledescription="carousel" aria-label="Image Carousel" dir="ltr">
			<div class="elementor-image-carousel swiper-wrapper swiper-image-stretch" aria-live="off">
								<?php $i = 1; foreach($home_banners as $banner): ?><div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="<?= $i++ ?> of <?= count($home_banners) ?>"><?php if($banner['url']): ?><a href="<?= html_escape(safe_href($banner['url'])) ?>"><?php endif; ?><figure class="swiper-slide-inner"><img decoding="async" class="<?= html_escape($banner['image_class']) ?>" src="<?= base_url(html_escape($banner['image_path'])) ?>" alt="Header Politeknik STMI"<?= $banner['image_srcset'] ? ' srcset="' . html_escape($banner['image_srcset']) . '"' : '' ?> /></figure><?php if($banner['url']): ?></a><?php endif; ?></div><?php endforeach; ?>			</div>
					</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-c0f24ab e-flex e-con-boxed e-con e-parent" data-id="c0f24ab" data-element_type="container">
					<div class="e-con-inner">
<?php foreach ($featured_links_grouped as $row_num => $links_row): ?>
		<div class="elementor-element elementor-element-<?= $row_num == 1 ? '6516bd0' : '48f0e4c' ?> e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="<?= $row_num == 1 ? '6516bd0' : '48f0e4c' ?>" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeIn&quot;}">
<?php foreach($links_row as $link):
	// Tile baru dari admin belum punya ID Elementor: pakai ID tile pertama (aturan CSS post-490 semua tile sama).
	$link['container_id'] = $link['container_id'] ? $link['container_id'] : '5fda677';
	$link['widget_id'] = $link['widget_id'] ? $link['widget_id'] : 'e1a2c9b'; ?>
		<div class="elementor-element elementor-element-<?= $link['container_id'] ?> e-con-full e-flex e-con e-child" data-id="<?= $link['container_id'] ?>" data-element_type="container">
				<div class="elementor-element elementor-element-<?= $link['widget_id'] ?> e-transform elementor-widget elementor-widget-image" data-id="<?= $link['widget_id'] ?>" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;none&quot;,&quot;_transform_scale_effect_hover&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:0.9,&quot;sizes&quot;:[]},&quot;_transform_scale_effect_hover_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;_transform_scale_effect_hover_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}" data-widget_type="image.default">
				<div class="elementor-widget-container">
																<a href="<?= html_escape(safe_href($link['url'])) ?>"<?= strpos($link['url'], 'ppid.stmi.ac.id') !== false ? '' : ' target="_blank"' ?>>
							<?= $link['img_hint_key'] ? wp_img_hint($img_hints, $link['img_hint_key']) : '' ?><img <?= strpos($link['url'], 'e-learning') === false ? 'loading="lazy" ' : '' ?>decoding="async" width="348" height="89" src="<?= base_url(html_escape($link['image_path'])) ?>" class="<?= html_escape($link['image_class']) ?>" alt=""<?= $link['image_srcset'] ? ' srcset="' . base_url(explode(' ', $link['image_srcset'])[0]) . ' 348w, ' . base_url(explode(' ', $link['image_srcset'])[2]) . ' 300w" sizes="(max-width: 348px) 100vw, 348px"' : '' ?> />								</a>
															</div>
				</div>
				</div>
<?php endforeach; ?>
				</div>
<?php endforeach; ?>
					</div>
				</div>
		<div class="elementor-element elementor-element-c3fba5b e-flex e-con-boxed e-con e-parent" data-id="c3fba5b" data-element_type="container">
					<div class="e-con-inner">
		<div class="elementor-element elementor-element-108eed4 e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="108eed4" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInLeft&quot;}">
				<div class="elementor-element elementor-element-73c8934 elementor-widget elementor-widget-heading" data-id="73c8934" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h3 class="elementor-heading-title elementor-size-default">Selamat Datang di</h3>				</div>
				</div>
				<div class="elementor-element elementor-element-290ad89 elementor-widget elementor-widget-text-editor" data-id="290ad89" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Perguruan Tinggi Negeri Kementerian Perindustrian RI</p>								</div>
				</div>
				<div class="elementor-element elementor-element-cd3ff72 elementor-widget elementor-widget-heading" data-id="cd3ff72" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h5 class="elementor-heading-title elementor-size-default">POLITEKNIK <br>
STMI <br> JAKARTA</h5>				</div>
				</div>
				<div class="elementor-element elementor-element-526d416 elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="526d416" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
				</div>
		<div class="elementor-element elementor-element-14f4e31 e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="14f4e31" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInRight&quot;}">
				<div class="elementor-element elementor-element-a531d7c elementor-widget elementor-widget-image" data-id="a531d7c" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
															<img loading="lazy" decoding="async" width="1024" height="307" src="<?= base_url('wp-content/uploads/2024/03/Logo-Kemenperin-1024px.png') ?>" class="attachment-large size-large wp-image-2100" alt="" srcset="<?= base_url('wp-content/uploads/2024/03/Logo-Kemenperin-1024px.png') ?> 1024w, <?= base_url('wp-content/uploads/2024/03/Logo-Kemenperin-1024px-300x90.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Logo-Kemenperin-1024px-768x230.png') ?> 768w" sizes="(max-width: 1024px) 100vw, 1024px" />															</div>
				</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-2ae8345 animated-slow e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="2ae8345" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-e714663 elementor-widget elementor-widget-heading" data-id="e714663" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;none&quot;}" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h6 class="elementor-heading-title elementor-size-default">Your Future Starts Here</h6>				</div>
				</div>
				<div class="elementor-element elementor-element-17133d6 animated-slow elementor-widget elementor-widget-heading" data-id="17133d6" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;none&quot;}" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h4 class="elementor-heading-title elementor-size-default">Institusi dan Prodi Ter-Akreditasi BAIK SEKALI</h4>				</div>
				</div>
				<div class="elementor-element elementor-element-b1c1866 elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="b1c1866" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
				<div class="elementor-element elementor-element-5f164a9 elementor-widget elementor-widget-text-editor" data-id="5f164a9" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Lulusan Tersertifikasi BNSP</p>								</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-a9b37b2 animated-slow e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="a9b37b2" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-c8646fa elementor-widget elementor-widget-image" data-id="c8646fa" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
															<img loading="lazy" decoding="async" width="846" height="73" src="<?= base_url('wp-content/uploads/2025/01/STMI-WBK-BISA-768x99-1-768x73-2.png') ?>" class="attachment-large size-large wp-image-2658" alt="" srcset="<?= base_url('wp-content/uploads/2025/01/STMI-WBK-BISA-768x99-1-768x73-2.png') ?> 846w, <?= base_url('wp-content/uploads/2025/01/STMI-WBK-BISA-768x99-1-768x73-2-300x26.png') ?> 300w, <?= base_url('wp-content/uploads/2025/01/STMI-WBK-BISA-768x99-1-768x73-2-768x66.png') ?> 768w" sizes="(max-width: 846px) 100vw, 846px" />															</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-2f34736 e-flex e-con-boxed e-con e-parent" data-id="2f34736" data-element_type="container">
					<div class="e-con-inner">
<?php foreach($study_programs as $prog): ?>
		<div class="elementor-element elementor-element-<?= $prog['container_id'] ?> e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="<?= $prog['container_id'] ?>" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;gradient&quot;,&quot;animation&quot;:&quot;zoomIn&quot;}">
				<div class="elementor-element elementor-element-<?= $prog['icon_id'] ?> elementor-view-framed elementor-shape-circle elementor-invisible elementor-widget elementor-widget-icon" data-id="<?= $prog['icon_id'] ?>" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeIn&quot;}" data-widget_type="icon.default">
				<div class="elementor-widget-container">
							<div class="elementor-icon-wrapper">
			<a class="elementor-icon" href="#">
			<svg aria-hidden="true" class="<?= $prog['container_id'] === '64b9fa9' ? 'e-font-icon-svg e-fas-cogs' : ($prog['container_id'] === 'f049439' ? 'e-font-icon-svg e-fas-vial' : ($prog['container_id'] === '7f98178' ? 'e-font-icon-svg e-fas-laptop-code' : ($prog['container_id'] === '29352f3' ? 'e-font-icon-svg e-fas-calculator' : 'e-font-icon-svg e-fas-atom'))) ?>" viewBox="0 0 <?= $prog['container_id'] === '64b9fa9' || $prog['container_id'] === '7f98178' ? '640' : ($prog['container_id'] === 'f049439' ? '480' : '448') ?> 512" xmlns="http://www.w3.org/2000/svg"><?= safe_inline_svg($prog['icon_svg']) ?></svg>			</a>
		</div>
						</div>
				</div>
				<div class="elementor-element elementor-element-<?= $prog['heading_id'] ?> elementor-widget elementor-widget-heading" data-id="<?= $prog['heading_id'] ?>" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h6 class="elementor-heading-title elementor-size-default"><?= safe_inline_html($prog['title']) ?></h6>				</div>
				</div>
				<div class="elementor-element elementor-element-<?= $prog['text_id'] ?> elementor-widget elementor-widget-text-editor" data-id="<?= $prog['text_id'] ?>" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p><?= html_escape($prog['description']) ?></p>								</div>
				</div>
				<div class="elementor-element elementor-element-<?= $prog['button_id'] ?> elementor-align-center elementor-widget elementor-widget-button" data-id="<?= $prog['button_id'] ?>" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
									<div class="elementor-button-wrapper">
					<a class="elementor-button elementor-button-link elementor-size-xs" href="<?= html_escape(safe_href($prog['url'])) ?>" target="_blank">
						<span class="elementor-button-content-wrapper">
									<span class="elementor-button-text">Selengkapnya</span>
					</span>
					</a>
				</div>
								</div>
				</div>
				</div>
<?php endforeach; ?>
					</div>
				</div>
		<div class="elementor-element elementor-element-1c25c44 e-flex e-con-boxed e-con e-parent" data-id="1c25c44" data-element_type="container">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-57dff7b elementor-widget elementor-widget-spacer" data-id="57dff7b" data-element_type="widget" data-widget_type="spacer.default">
				<div class="elementor-widget-container">
							<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-0be8bcf animated-slow e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="0be8bcf" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-a26676c elementor-widget elementor-widget-text-editor" data-id="a26676c" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Sekolah Tinggi Manajemen Industri (STMI) adalah Perguruan Tinggi Negeri yang berdiri sejak tahun 1968 di bawah binaan Kementerian Perindustrian. Tahun 2014 beralih menjadi Politeknik STMI Jakarta dengan spesialisasi kompetensi pada Industri Otomotif.</p><p>Politeknik STMI Jakarta menyelenggarakan pendidikan dengan jenjang Sarjana Terapan dengan gelar [S.Tr.]. Politeknik STMI Jakarta telah melakukan banyak kerjasama dengan industri, sehingga lulusan Politeknik STMI Jakarta terserap di dunia kerja.</p>								</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-8da43ed e-flex e-con-boxed e-con e-parent" data-id="8da43ed" data-element_type="container">
					<div class="e-con-inner">
		<div class="elementor-element elementor-element-7d87a38 e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="7d87a38" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInLeft&quot;}">
				<div class="elementor-element elementor-element-d603588 elementor-widget elementor-widget-video" data-id="d603588" data-element_type="widget" data-settings="{&quot;youtube_url&quot;:&quot;<?= isset($home_options['video_url']) ? html_escape(str_replace('/', '\/', $home_options['video_url'])) : '' ?>&quot;,&quot;loop&quot;:&quot;yes&quot;,&quot;video_type&quot;:&quot;youtube&quot;,&quot;controls&quot;:&quot;yes&quot;}" data-widget_type="video.default">
				<div class="elementor-widget-container">
							<div class="elementor-wrapper elementor-open-inline">
			<div class="elementor-video"></div>		</div>
						</div>
				</div>
				</div>
		<div class="elementor-element elementor-element-b15319f animated-slow e-flex e-con-boxed elementor-invisible e-con e-child" data-id="b15319f" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInRight&quot;}">
					<div class="e-con-inner">
		<div class="elementor-element elementor-element-664ed93 e-flex e-con-boxed e-con e-child" data-id="664ed93" data-element_type="container">
					<div class="e-con-inner">
		<div class="elementor-element elementor-element-60bc266 e-con-full e-flex e-con e-child" data-id="60bc266" data-element_type="container">
				<div class="elementor-element elementor-element-6622d79 elementor-widget elementor-widget-text-editor" data-id="6622d79" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<div class="elementor-element elementor-element-99b7b6c elementor-widget elementor-widget-heading animated fadeInDown" data-id="99b7b6c" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeInDown&quot;}" data-widget_type="heading.default"><div class="elementor-widget-container"><h3 class="elementor-heading-title elementor-size-default">KERJASAMA</h3></div></div><div class="elementor-element elementor-element-7c4bc06 elementor-widget elementor-widget-heading animated fadeInDown" data-id="7c4bc06" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeInDown&quot;}" data-widget_type="heading.default"><div class="elementor-widget-container"><h6 class="elementor-heading-title elementor-size-default">LEMBAGA DAN PERUSAHAAN</h6></div></div>								</div>
				</div>
				</div>
					</div>
				</div>
<?php 
$partner_css_map = [
    ['container_id' => '65b0ae6', 'widget_id' => '344313a'],
    ['container_id' => '9043359', 'widget_id' => 'd5a33bd'],
    ['container_id' => 'c423b5e', 'widget_id' => 'f4dcff6'],
    ['container_id' => '0714c57', 'widget_id' => '783bc0f'],
    ['container_id' => 'c406723', 'widget_id' => 'c473515'],
    ['container_id' => '5e1b92b', 'widget_id' => '72be9fd'],
    ['container_id' => '4fea60c', 'widget_id' => '4a0eace'],
    ['container_id' => 'e45c5fc', 'widget_id' => 'd96a3ce'],
];
$row_ids = ['9e7ab36', 'aeb7343'];

$chunks = array_chunk($home_partners, 4);
foreach($chunks as $i => $chunk):
    $row_id = isset($row_ids[$i]) ? $row_ids[$i] : 'aeb7343';
?>
		<div class="elementor-element elementor-element-<?= $row_id ?> e-con-full e-flex e-con e-child" data-id="<?= $row_id ?>" data-element_type="container">
<?php foreach($chunk as $j => $partner): 
        $idx = $i * 4 + $j;
        $c_id = isset($partner_css_map[$idx]) ? $partner_css_map[$idx]['container_id'] : 'e45c5fc';
        $w_id = isset($partner_css_map[$idx]) ? $partner_css_map[$idx]['widget_id'] : 'd96a3ce';
		$partner_srcset = ! empty($partner['image_srcset']) ? ' srcset="'.html_escape($partner['image_srcset']).'"' : '';
?>
		<div class="elementor-element elementor-element-<?= $c_id ?> e-con-full e-flex e-con e-child" data-id="<?= $c_id ?>" data-element_type="container">
				<div class="elementor-element elementor-element-<?= $w_id ?> elementor-widget elementor-widget-image" data-id="<?= $w_id ?>" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
<?php if ($partner['url']): // seperti widget gambar Elementor yang ber-link ?>
																<a href="<?= html_escape(safe_href($partner['url'])) ?>" target="_blank">
							<img loading="lazy" decoding="async" width="150" height="150" src="<?= base_url(html_escape($partner['image_path'])) ?>" class="attachment-full size-full<?= $partner['media_id'] ? ' wp-image-'.(int) $partner['media_id'] : '' ?>" alt="<?= html_escape($partner['name']) ?>"<?= $partner_srcset ?> />								</a>
															</div>
<?php else: ?>
															<img loading="lazy" decoding="async" width="150" height="150" src="<?= base_url(html_escape($partner['image_path'])) ?>" class="attachment-full size-full<?= $partner['media_id'] ? ' wp-image-'.(int) $partner['media_id'] : '' ?>" alt="<?= html_escape($partner['name']) ?>"<?= $partner_srcset ?> />															</div>
<?php endif; ?>
				</div>
				</div>
<?php endforeach; ?>
				</div>
<?php endforeach; ?>
		<div class="elementor-element elementor-element-37c411f e-flex e-con-boxed e-con e-child" data-id="37c411f" data-element_type="container">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-d9b340c elementor-align-center elementor-widget elementor-widget-button" data-id="d9b340c" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
									<div class="elementor-button-wrapper">
					<a class="elementor-button elementor-button-link elementor-size-sm elementor-animation-shrink" href="<?= site_url('mou') ?>" target="_blank">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-icon">
				<svg aria-hidden="true" class="e-font-icon-svg e-fas-arrow-right" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z"></path></svg>			</span>
									<span class="elementor-button-text">Selengkapnya</span>
					</span>
					</a>
				</div>
								</div>
				</div>
					</div>
				</div>
					</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-2f17347 animated-slow e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="2f17347" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-8a4de54 elementor-widget elementor-widget-heading" data-id="8a4de54" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h6 class="elementor-heading-title elementor-size-default">Your Future Starts Here</h6>				</div>
				</div>
				<div class="elementor-element elementor-element-eda5e49 elementor-widget elementor-widget-heading" data-id="eda5e49" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h4 class="elementor-heading-title elementor-size-default">Keunggulan Politeknik STMI Jakarta</h4>				</div>
				</div>
				<div class="elementor-element elementor-element-13d1d2e elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="13d1d2e" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-cf25015 e-flex e-con-boxed e-con e-parent" data-id="cf25015" data-element_type="container">
					<div class="e-con-inner">
		<div class="elementor-element elementor-element-d30e8ed e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="d30e8ed" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInLeft&quot;}">
				<div class="elementor-element elementor-element-612b653 elementor-widget elementor-widget-heading" data-id="612b653" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h5 class="elementor-heading-title elementor-size-default">Pengajar Profesional</h5>				</div>
				</div>
				<div class="elementor-element elementor-element-a5ee28f elementor-widget elementor-widget-text-editor" data-id="a5ee28f" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Tenaga pengajar dengan pengalaman di bidang industri dan praktisi dari Industri</p>								</div>
				</div>
				<div class="elementor-element elementor-element-e11dda5 elementor-widget elementor-widget-heading" data-id="e11dda5" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h5 class="elementor-heading-title elementor-size-default">Kurikulum Terbaru</h5>				</div>
				</div>
				<div class="elementor-element elementor-element-5fb5fe0 elementor-widget elementor-widget-text-editor" data-id="5fb5fe0" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Kami menerapakan kurikulum terbaru sejalan dengan kebutuhan industri</p>								</div>
				</div>
				<div class="elementor-element elementor-element-53a06ac elementor-widget elementor-widget-heading" data-id="53a06ac" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h5 class="elementor-heading-title elementor-size-default">Dual System</h5>				</div>
				</div>
				<div class="elementor-element elementor-element-24ab31c elementor-widget elementor-widget-text-editor" data-id="24ab31c" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<p>Kurikulum Pendidikan Berorientasi <em><strong>Dual System</strong></em></p>								</div>
				</div>
				<div class="elementor-element elementor-element-1f7a122 elementor-widget elementor-widget-text-editor" data-id="1f7a122" data-element_type="widget" data-widget_type="text-editor.default">
				<div class="elementor-widget-container">
									<ul><li>5 semester di Kampus</li><li>2 semester di Industri</li><li>1 semester Tugas Akhir</li></ul>								</div>
				</div>
				<div class="elementor-element elementor-element-f316e97 elementor-widget elementor-widget-image" data-id="f316e97" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
															<img loading="lazy" decoding="async" width="996" height="194" src="<?= base_url('wp-content/uploads/2024/03/Banner-STMI-1.png') ?>" class="attachment-full size-full wp-image-547" alt="" srcset="<?= base_url('wp-content/uploads/2024/03/Banner-STMI-1.png') ?> 996w, <?= base_url('wp-content/uploads/2024/03/Banner-STMI-1-300x58.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Banner-STMI-1-768x150.png') ?> 768w" sizes="(max-width: 996px) 100vw, 996px" />															</div>
				</div>
				</div>
		<div class="elementor-element elementor-element-00b30a3 e-con-full animated-slow e-flex elementor-invisible e-con e-child" data-id="00b30a3" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeInRight&quot;}">
				<div class="elementor-element elementor-element-66321a8 elementor-widget elementor-widget-image" data-id="66321a8" data-element_type="widget" data-widget_type="image.default">
				<div class="elementor-widget-container">
															<img loading="lazy" decoding="async" width="694" height="665" src="<?= base_url('wp-content/uploads/2024/03/Screenshot_5.jpg') ?>" class="attachment-large size-large wp-image-293" alt="" srcset="<?= base_url('wp-content/uploads/2024/03/Screenshot_5.jpg') ?> 694w, <?= base_url('wp-content/uploads/2024/03/Screenshot_5-300x287.jpg') ?> 300w" sizes="(max-width: 694px) 100vw, 694px" />															</div>
				</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-ce7f966 elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed e-con e-parent" data-id="ce7f966" data-element_type="container">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-45e06fa elementor-widget elementor-widget-spacer" data-id="45e06fa" data-element_type="widget" data-widget_type="spacer.default">
				<div class="elementor-widget-container">
							<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-dabbaba animated-slow elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="dabbaba" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;fadeIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-495cea7 elementor-widget elementor-widget-heading" data-id="495cea7" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h4 class="elementor-heading-title elementor-size-default">Berita Kampus</h4>				</div>
				</div>
				<div class="elementor-element elementor-element-6b0975c elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="6b0975c" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
				<div class="elementor-element elementor-element-d9d307e elementor-widget elementor-widget-posts" data-id="d9d307e" data-element_type="widget" data-widget_type="posts.cards">
				<div class="elementor-widget-container">
					 				</div>
				</div>
				<div class="elementor-element elementor-element-1cac49a elementor-align-center elementor-widget elementor-widget-button" data-id="1cac49a" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
									<div class="elementor-button-wrapper">
					<a class="elementor-button elementor-button-link elementor-size-sm elementor-animation-shrink" href="<?= site_url('category/berita') ?>" target="_blank">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-icon">
				<svg aria-hidden="true" class="e-font-icon-svg e-fas-arrow-right" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z"></path></svg>			</span>
									<span class="elementor-button-text">Lihat Informasi Lainnya</span>
					</span>
					</a>
				</div>
								</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-6d3ca63 e-flex e-con-boxed e-con e-parent" data-id="6d3ca63" data-element_type="container">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-a1d66a5 elementor-widget elementor-widget-spacer" data-id="a1d66a5" data-element_type="widget" data-widget_type="spacer.default">
				<div class="elementor-widget-container">
							<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-6e7e35d animated-slow elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="6e7e35d" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-bd96310 elementor-widget elementor-widget-heading" data-id="bd96310" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h6 class="elementor-heading-title elementor-size-default">Your Future Starts Here</h6>				</div>
				</div>
				<div class="elementor-element elementor-element-6cc9d17 elementor-widget elementor-widget-heading" data-id="6cc9d17" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h4 class="elementor-heading-title elementor-size-default">Testimoni Alumni</h4>				</div>
				</div>
				<div class="elementor-element elementor-element-07068ba elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="07068ba" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-2a9fd4b animated-slow elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="2a9fd4b" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomInRight&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-fa62178 elementor-widget elementor-widget-testimonial-carousel" data-id="fa62178" data-element_type="widget" data-widget_type="testimonial-carousel.default">
				<div class="elementor-widget-container">
					 				</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-4911799 animated-slow elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed elementor-invisible e-con e-parent" data-id="4911799" data-element_type="container" data-settings="{&quot;animation&quot;:&quot;zoomIn&quot;}">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-41c9efe elementor-widget elementor-widget-heading" data-id="41c9efe" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h4 class="elementor-heading-title elementor-size-default">Link Akademik &amp; Non Akademik</h4>				</div>
				</div>
				<div class="elementor-element elementor-element-55abb3d elementor-widget-divider--view-line elementor-widget elementor-widget-divider" data-id="55abb3d" data-element_type="widget" data-widget_type="divider.default">
				<div class="elementor-widget-container">
							<div class="elementor-divider">
			<span class="elementor-divider-separator">
						</span>
		</div>
						</div>
				</div>
					</div>
				</div>
		<div class="elementor-element elementor-element-5056b03 elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile e-flex e-con-boxed e-con e-parent" data-id="5056b03" data-element_type="container">
					<div class="e-con-inner">
				<div class="elementor-element elementor-element-1a346aa elementor-widget elementor-widget-spacer" data-id="1a346aa" data-element_type="widget" data-widget_type="spacer.default">
				<div class="elementor-widget-container">
							<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
						</div>
				</div>
					</div>
				</div>
				</div>
			</main>

	
