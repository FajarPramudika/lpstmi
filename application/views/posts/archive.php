<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<main id="main" class="site-main hfeed" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">

		
<div class="hero-section" data-type="type-2">
<?php if ($archive_type === 'author'): ?>	
			<header class="entry-header ct-container-narrow">
			<div class="ct-author-name"><span class="ct-media-container-static"><img src="https://secure.gravatar.com/avatar/<?= $author['gravatar_hash'] ?>?s=120&amp;d=mm&amp;r=g" width="60" height="60" style="height:60px" alt="<?= html_escape($author['display_name']) ?>"></span><h1 class="page-title" itemprop="headline"><?= html_escape($author['display_name']) ?></h1></div>
		<ul class="entry-meta" data-type="simple:slash">
							<li class="meta-date">Joined:&nbsp;<?= wp_date('d/m/Y', $author['registered_at']) ?></li>
			
							<li class="meta-articles">Articles:&nbsp;<?= $author_articles ?></li>
			
					</ul>

	<?php if ($author['website']): ?><div class="author-box-socials"><span><a href="<?= html_escape(wp_local_url($author['website'])) ?>" aria-label="Website icon"><svg class="ct-icon" width="12" height="12" viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10s4.5 10 10 10 10-4.5 10-10S15.5 0 10 0zm6.9 6H14c-.4-1.8-1.4-3.6-1.4-3.6s2.8.8 4.3 3.6zM10 2s1.2 1.7 1.9 4H8.1C8.8 3.6 10 2 10 2zM2.2 12s-.6-1.8 0-4h3.4c-.3 1.8 0 4 0 4H2.2zm.9 2H6c.6 2.3 1.4 3.6 1.4 3.6C4.3 16.5 3.1 14 3.1 14zM6 6H3.1c1.6-2.8 4.3-3.6 4.3-3.6S6.4 4.2 6 6zm4 12s-1.3-1.9-1.9-4h3.8c-.6 2.1-1.9 4-1.9 4zm2.3-6H7.7s-.3-2 0-4h4.7c.3 1.8-.1 4-.1 4zm.3 5.6s1-1.8 1.4-3.6h2.9c-1.6 2.7-4.3 3.6-4.3 3.6zm1.7-5.6s.3-2.1 0-4h3.4c.6 2.2 0 4 0 4h-3.4z"/></svg></a></span></div>	<?php else: ?>	<?php endif; ?>	</header>
	</div><?php else: ?>			<figure>
			<div class="ct-media-container"><img loading="lazy" width="1921" height="634" src="<?= base_url('wp-content/uploads/2024/03/Header-1-STMI.jpg') ?>" class="attachment-full size-full" alt="" loading="lazy" decoding="async" srcset="<?= base_url('wp-content/uploads/2024/03/Header-1-STMI.jpg') ?> 1921w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-300x99.jpg') ?> 300w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-1024x338.jpg') ?> 1024w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-768x253.jpg') ?> 768w, <?= base_url('wp-content/uploads/2024/03/Header-1-STMI-1536x507.jpg') ?> 1536w" sizes="auto, (max-width: 1921px) 100vw, 1921px" itemprop="image" /></div>		</figure>
	
			<header class="entry-header ct-container-narrow">
			<h1 class="page-title" itemprop="headline"><span class="ct-title-label"><?= $archive_label ?></span> <?= html_escape($archive_name) ?></h1>		</header>
	</div><?php endif; ?>




<div class="ct-container"  data-vertical-spacing="top:bottom">
	<section >
		<div class="entries" data-archive="default" data-layout="<?= $layout ?>" data-cards="boxed"><?php foreach ($posts as $item) { $this->load->view($card_view, array('item' => $item)); } ?></div><?php if ($total_pages > 1): ?>

		<nav class="ct-pagination" data-pagination="simple"  >
			<?= wp_paginate_links($base_path, $page, $total_pages) ?>

			
		</nav><?php endif; ?>	</section>

	</div>
	</main>

	