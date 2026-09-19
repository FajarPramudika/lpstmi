<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<main id="main" class="site-main hfeed" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">

		
	<div
		class="ct-container-full"
				data-content="normal"		data-vertical-spacing="top:bottom">

		
		
	<article
		id="post-<?= $download['id'] ?>"
		class="post-<?= $download['id'] ?> wpdmpro type-wpdmpro status-publish<?= $download['featured_media_id'] ? ' has-post-thumbnail' : '' ?> hentry">

		
<div class="hero-section is-width-constrained" data-type="type-1">
			<header class="entry-header">
			<h1 class="page-title" itemprop="headline"><?= wp_texturize($download['title']) ?></h1><ul class="entry-meta" data-type="simple:slash" ><li class="meta-author" itemprop="author" itemscope="" itemtype="https://schema.org/Person"><a href="<?= site_url('author/'.$author['slug']) ?>" tabindex="-1" class="ct-media-container-static"><img <?= $featured ? 'loading="lazy" ' : '' ?>src="https://secure.gravatar.com/avatar/<?= $author['gravatar_hash'] ?>?s=50&amp;d=mm&amp;r=g" width="25" height="25" style="height:25px" alt="<?= html_escape($author['display_name']) ?>"></a><a class="ct-meta-element-author" href="<?= site_url('author/'.$author['slug']) ?>" title="Posts by <?= html_escape($author['display_name']) ?>" rel="author" itemprop="url"><span itemprop="name"><?= html_escape($author['display_name']) ?></span></a></li><li class="meta-date" itemprop="datePublished"><time class="ct-meta-element-date" datetime="<?= wp_date('c', $download['published_at']) ?>"><?= wp_date('d/m/Y', $download['published_at']) ?></time></li></ul>		</header>
	</div>
		
		
		<div class="entry-content is-layout-constrained">
			<div class='w3eden' ><!-- WPDM Template: <?= $download['template'] === 'default' ? 'Default Template' : 'Default Template ( Simplified )' ?> -->
<div class="row">
    <?php if ($download['template'] === 'default'): ?><div class="col-md-12">
        <?php if ($featured): ?><div class="card mb-3 p-3 hide_empty [hide_empty:featured_image]"><?= $featured ?></div><?php else: ?><div class="card mb-3 p-3 hide_empty wpdm_hide wpdm_remove_empty">[featured_image]</div><?php endif; ?>

    </div>
    <?php endif; ?><div class="col-md-5">
        <div class="wpdm-button-area mb-3 p-3 card">
            <a class='wpdm-download-link download-on-click btn btn-primary btn-sm' rel='nofollow' href='#' data-downloadurl="<?= site_url('download/'.$download['slug']) ?>?wpdmdl=<?= $download['id'] ?>&amp;refresh=<?= $refresh ?>"><?= $download['button_label'] === NULL ? 'Download' : html_escape($download['button_label']) ?></a>
            <div class="alert alert-warning mt-2 wpdm_hide wpdm_remove_empty">
                Download is available until [expire_date]
            </div>
        </div>
        <ul class="list-group ml-0 mb-2">
            <li class="list-group-item d-flex justify-content-between align-items-center wpdm_hide wpdm_remove_empty">
                Version
                <span class="badge"></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:download_count]">
                Download
                <span class="badge"><?= $download['download_count'] ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_size]">
                File Size
                <span class="badge"><?= html_escape($download['file_size']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_count]">
                File Count
                <span class="badge">1</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:create_date]">
                Create Date
                <span class="badge"><?= wp_date('d/m/Y', $download['published_at']) ?></span>
            </li>
            <li class="list-group-item  d-flex justify-content-between align-items-center [hide_empty:update_date]">
                Last Updated
                <span class="badge"><?= wp_date('d/m/Y', $download['modified_at']) ?></span>
            </li>

        </ul>
    </div>

    <div class="col-md-7">
<?php if ($download['template'] === 'default'): ?>        <h1 class="mt-0"><?= wp_nav_title($download['title']) ?></h1>
        <?php else: ?>

        <?php endif; ?><?= $description ?>


        <div class="wel">
            
        </div>

    </div>

</div>


</div>		</div>

		
		
		
		
	</article>

	
		
			</div>

	</main>

	