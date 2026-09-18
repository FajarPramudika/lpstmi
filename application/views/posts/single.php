<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<main id="main" class="site-main hfeed" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">

		
<div class="hero-section" data-type="type-2">
			<figure>
			<div class="ct-media-container"><img loading="lazy" width="1921" height="634" src="<?= base_url('wp-content/uploads/2024/03/Header-1-STMI.jpg') ?>" class="attachment-full size-full" alt="" loading="lazy" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Header-1-STMI.jpg') ?> 1921w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-300x99.jpg') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-1024x338.jpg') ?> 1024w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-768x253.jpg') ?> 768w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-1536x507.jpg') ?> 1536w" sizes="auto, (max-width: 1921px) 100vw, 1921px" itemprop="image" /></div>		</figure>
	
			<header class="entry-header ct-container-narrow">
			<h1 class="page-title" itemprop="headline"><?= wp_texturize($post['title']) ?></h1><ul class="entry-meta" data-type="icons:slash" ><li class="meta-date" itemprop="datePublished"><svg width='15' height='15' viewBox='0 0 15 15'><path d='M7.5,0C3.4,0,0,3.4,0,7.5S3.4,15,7.5,15S15,11.6,15,7.5S11.6,0,7.5,0z M7.5,13.6c-3.4,0-6.1-2.8-6.1-6.1c0-3.4,2.8-6.1,6.1-6.1c3.4,0,6.1,2.8,6.1,6.1C13.6,10.9,10.9,13.6,7.5,13.6z M10.8,9.2c-0.1,0.2-0.4,0.4-0.6,0.4c-0.1,0-0.2,0-0.3-0.1L7.2,8.1C7,8,6.8,7.8,6.8,7.5V4c0-0.4,0.3-0.7,0.7-0.7S8.2,3.6,8.2,4v3.1l2.4,1.2C10.9,8.4,11,8.8,10.8,9.2z'/></svg><time class="ct-meta-element-date" datetime="<?= wp_date('c', $post['published_at']) ?>"><?= wp_date('l, j F Y', $post['published_at']) ?></time></li><?php if ($categories): ?><li class="meta-categories" data-type="simple"><svg width='15' height='15' viewBox='0 0 15 15'><path d='M14.4,1.2H0.6C0.3,1.2,0,1.5,0,1.9V5c0,0.3,0.3,0.6,0.6,0.6h0.6v7.5c0,0.3,0.3,0.6,0.6,0.6h11.2c0.3,0,0.6-0.3,0.6-0.6V5.6h0.6C14.7,5.6,15,5.3,15,5V1.9C15,1.5,14.7,1.2,14.4,1.2z M12.5,12.5h-10V5.6h10V12.5z M13.8,4.4H1.2V2.5h12.5V4.4z M5.6,7.5c0-0.3,0.3-0.6,0.6-0.6h2.5c0.3,0,0.6,0.3,0.6,0.6S9.1,8.1,8.8,8.1H6.2C5.9,8.1,5.6,7.8,5.6,7.5z'/></svg><?= wp_term_links($categories, 'category') ?></li><?php endif; ?></ul>		</header>
	</div>



	<div
		class="ct-container"
		data-sidebar="right"				data-vertical-spacing="top:bottom">

		
		
	<article
		id="post-<?= $post['id'] ?>"
		class="<?= $post_class ?>">

		
		
		
		<div class="entry-content is-layout-flow">
			<?= $content ?>		</div>

		
					<?php if ($tags): ?><div class="entry-tags is-width-constrained "><span class="ct-module-title">Tags</span><div class="entry-tags-items"><?php foreach ($tags as $tag): ?><a href="<?= site_url('tag/'.$tag['slug']) ?>" rel="tag"><span>#</span> <?= html_escape($tag['name']) ?></a><?php endforeach; ?></div></div><?php endif; ?>		
					
		<div class="ct-share-box is-width-constrained ct-hidden-sm" data-location="bottom" data-type="type-2" >
			<span class="ct-module-title">Share :</span>
			<div data-color="custom" data-icons-type="custom:solid">
							
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" data-network="facebook" aria-label="Facebook" rel="noopener noreferrer nofollow">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M20,10.1c0-5.5-4.5-10-10-10S0,4.5,0,10.1c0,5,3.7,9.1,8.4,9.9v-7H5.9v-2.9h2.5V7.9C8.4,5.4,9.9,4,12.2,4c1.1,0,2.2,0.2,2.2,0.2v2.5h-1.3c-1.2,0-1.6,0.8-1.6,1.6v1.9h2.8L13.9,13h-2.3v7C16.3,19.2,20,15.1,20,10.1z"/>
					</svg>
				</span>				</a>
							
				<a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&amp;text=<?= $share_title ?>" data-network="twitter" aria-label="X (Twitter)" rel="noopener noreferrer nofollow">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M2.9 0C1.3 0 0 1.3 0 2.9v14.3C0 18.7 1.3 20 2.9 20h14.3c1.6 0 2.9-1.3 2.9-2.9V2.9C20 1.3 18.7 0 17.1 0H2.9zm13.2 3.8L11.5 9l5.5 7.2h-4.3l-3.3-4.4-3.8 4.4H3.4l5-5.7-5.3-6.7h4.4l3 4 3.5-4h2.1zM14.4 15 6.8 5H5.6l7.7 10h1.1z"/>
					</svg>
				</span>				</a>
							
				<a href="https://t.me/share/url?url=<?= $share_url ?>&amp;text=<?= $share_title ?>" data-network="telegram" aria-label="Telegram" rel="noopener noreferrer nofollow">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M19.9,3.1l-3,14.2c-0.2,1-0.8,1.3-1.7,0.8l-4.6-3.4l-2.2,2.1c-0.2,0.2-0.5,0.5-0.9,0.5l0.3-4.7L16.4,5c0.4-0.3-0.1-0.5-0.6-0.2L5.3,11.4L0.7,10c-1-0.3-1-1,0.2-1.5l17.7-6.8C19.5,1.4,20.2,1.9,19.9,3.1z"/>
					</svg>
				</span>				</a>
							
				<a href="whatsapp://send?text=<?= $share_url ?>" data-network="whatsapp" aria-label="WhatsApp" rel="noopener noreferrer nofollow">
					<span class="ct-icon-container">
					<svg
					width="20px"
					height="20px"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M10,0C4.5,0,0,4.5,0,10c0,1.9,0.5,3.6,1.4,5.1L0.1,20l5-1.3C6.5,19.5,8.2,20,10,20c5.5,0,10-4.5,10-10S15.5,0,10,0zM6.6,5.3c0.2,0,0.3,0,0.5,0c0.2,0,0.4,0,0.6,0.4c0.2,0.5,0.7,1.7,0.8,1.8c0.1,0.1,0.1,0.3,0,0.4C8.3,8.2,8.3,8.3,8.1,8.5C8,8.6,7.9,8.8,7.8,8.9C7.7,9,7.5,9.1,7.7,9.4c0.1,0.2,0.6,1.1,1.4,1.7c0.9,0.8,1.7,1.1,2,1.2c0.2,0.1,0.4,0.1,0.5-0.1c0.1-0.2,0.6-0.7,0.8-1c0.2-0.2,0.3-0.2,0.6-0.1c0.2,0.1,1.4,0.7,1.7,0.8s0.4,0.2,0.5,0.3c0.1,0.1,0.1,0.6-0.1,1.2c-0.2,0.6-1.2,1.1-1.7,1.2c-0.5,0-0.9,0.2-3-0.6c-2.5-1-4.1-3.6-4.2-3.7c-0.1-0.2-1-1.3-1-2.6c0-1.2,0.6-1.8,0.9-2.1C6.1,5.4,6.4,5.3,6.6,5.3z"/>
					</svg>
				</span>				</a>
							
				<a href="mailto:?subject=<?= $share_title ?>&amp;body=<?= $share_url ?>" data-network="email" aria-label="Email" rel="noopener noreferrer nofollow">
					<span class="ct-icon-container">
					<svg
					width="20"
					height="20"
					viewBox="0 0 20 20"
					aria-hidden="true">
						<path d="M10,10.1L0,4.7C0.1,3.2,1.4,2,3,2h14c1.6,0,2.9,1.2,3,2.8L10,10.1z M10,11.8c-0.1,0-0.2,0-0.4-0.1L0,6.4V15c0,1.7,1.3,3,3,3h4.9h4.3H17c1.7,0,3-1.3,3-3V6.4l-9.6,5.2C10.2,11.7,10.1,11.7,10,11.8z"/>
					</svg>
				</span>				</a>
			
			</div>
					</div>

			
		
		<nav class="post-navigation is-width-constrained " >
							<?php if ($prev): ?><a href="<?= site_url($prev['slug']) ?>" class="nav-item-prev">
					<?php if ($prev['thumbnail']): ?><figure class="ct-media-container  "><?= $prev['thumbnail'] ?><svg width="20px" height="15px" viewBox="0 0 20 15" fill="#ffffff"><polygon points="0,7.5 5.5,13 6.4,12.1 2.4,8.1 20,8.1 20,6.9 2.4,6.9 6.4,2.9 5.5,2 "/></svg></figure><?php endif; ?>

					<div class="item-content">
						<span class="item-label">
							Previous <span>Post</span>						</span>

													<span class="item-title ct-hidden-sm">
								<?= wp_nav_title($prev['title']) ?>							</span>
											</div>

				</a><?php else: ?><div class="nav-item-prev"></div><?php endif; ?>

			
							<?php if ($next): ?><a href="<?= site_url($next['slug']) ?>" class="nav-item-next">
					<div class="item-content">
						<span class="item-label">
							Next <span>Post</span>						</span>

													<span class="item-title ct-hidden-sm">
								<?= wp_nav_title($next['title']) ?>							</span>
											</div>

					<?php if ($next['thumbnail']): ?><figure class="ct-media-container  "><?= $next['thumbnail'] ?><svg width="20px" height="15px" viewBox="0 0 20 15" fill="#ffffff"><polygon points="14.5,2 13.6,2.9 17.6,6.9 0,6.9 0,8.1 17.6,8.1 13.6,12.1 14.5,13 20,7.5 "/></svg></figure><?php endif; ?>				</a><?php else: ?><div class="nav-item-next"></div><?php endif; ?>

			
		</nav>

	
	</article>

	
		<aside class="ct-hidden-sm ct-hidden-md" data-type="type-2" id="sidebar" itemtype="https://schema.org/WPSideBar" itemscope="itemscope"><div class="ct-sidebar"><div class="ct-widget is-layout-flow widget_search" id="search-2">

<form role="search" method="get" class="ct-search-form" data-form-controls="inside" data-taxonomy-filter="false" data-submit-button="icon" action="<?= site_url('') ?>" aria-haspopup="listbox" data-live-results="thumbs">

	<input type="search"  placeholder="Search" value="" name="s" autocomplete="off" title="Search for..." aria-label="Search for...">

	<div class="ct-search-form-controls">
		
		<button type="submit" class="wp-element-button" data-button="inside:icon" aria-label="Search button">
			<svg class="ct-icon ct-search-button-content" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/></svg>
			<span class="ct-ajax-loader">
				<svg viewBox="0 0 24 24">
					<circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"/>

					<path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2">
						<animateTransform
							attributeName="transform"
							attributeType="XML"
							type="rotate"
							dur="0.6s"
							from="0 12 12"
							to="360 12 12"
							repeatCount="indefinite"
						/>
					</path>
				</svg>
			</span>
		</button>

		
		
		

		<input type="hidden" value="afc3785fa0" class="ct-live-results-nonce">	</div>

			<div class="screen-reader-text" aria-live="polite" role="status">
			No results		</div>
	
</form>


</div><div class="ct-widget is-layout-flow widget_media_image" id="media_image-5"><a href="http://e-learning.stmi.ac.id/"><img <?= wp_img_hint($img_hints, 'sidebar:0') ?>width="300" height="77" src="<?= base_url('wp-content/uploads/2024/03/E-Learning-STMI-300x77.png') ?>" class="image wp-image-724  attachment-medium size-medium wp-post-image" alt="" style="max-width: 100%; height: auto;" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/E-Learning-STMI-300x77.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/E-Learning-STMI.png') ?> 348w" sizes="(max-width: 300px) 100vw, 300px" /></a></div><div class="ct-widget is-layout-flow widget_media_image" id="media_image-6"><a href="https://jarvis.stmi.ac.id/"><img <?= wp_img_hint($img_hints, 'sidebar:1') ?>width="300" height="77" src="<?= base_url('wp-content/uploads/2024/03/Jarvis-STMI-300x77.png') ?>" class="image wp-image-723  attachment-medium size-medium wp-post-image" alt="" style="max-width: 100%; height: auto;" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Jarvis-STMI-300x77.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Jarvis-STMI.png') ?> 348w" sizes="(max-width: 300px) 100vw, 300px" /></a></div><div class="ct-widget is-layout-flow widget_media_image" id="media_image-7"><a href="http://lib.stmi.ac.id/"><img <?= wp_img_hint($img_hints, 'sidebar:2') ?>width="300" height="77" src="<?= base_url('wp-content/uploads/2024/03/Perpustakaan-STMI-300x77.png') ?>" class="image wp-image-740  attachment-medium size-medium wp-post-image" alt="" style="max-width: 100%; height: auto;" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Perpustakaan-STMI-300x77.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Perpustakaan-STMI.png') ?> 348w" sizes="(max-width: 300px) 100vw, 300px" /></a></div><div class="ct-widget is-layout-flow widget_media_image" id="media_image-8"><a href="https://karir.stmi.ac.id/"><img <?= wp_img_hint($img_hints, 'sidebar:3') ?>width="300" height="77" src="<?= base_url('wp-content/uploads/2024/03/Career-STMI-300x77.png') ?>" class="image wp-image-731  attachment-medium size-medium wp-post-image" alt="" style="max-width: 100%; height: auto;" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Career-STMI-300x77.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Career-STMI.png') ?> 348w" sizes="(max-width: 300px) 100vw, 300px" /></a></div><div class="ct-widget is-layout-flow widget_media_image" id="media_image-9"><a href="http://virtualtour.stmi.ac.id/"><img <?= wp_img_hint($img_hints, 'sidebar:4') ?>width="300" height="77" src="<?= base_url('wp-content/uploads/2024/03/Virtual-Tour-STMI-300x77.png') ?>" class="image wp-image-744  attachment-medium size-medium wp-post-image" alt="" style="max-width: 100%; height: auto;" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Virtual-Tour-STMI-300x77.png') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Virtual-Tour-STMI.png') ?> 348w" sizes="(max-width: 300px) 100vw, 300px" /></a></div></div></aside>
			</div>

	</main>

	