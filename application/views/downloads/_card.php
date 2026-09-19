<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class='w3eden'><!-- WPDM Link Template: Default Template -->

<div class="link-template-default card mb-2">
    <div class="card-body">
        <div class="media">
            <div class="mr-3 img-48"><img decoding="async" class="wpdm_icon" alt="Icon" src="<?= base_url('wp-content/plugins/download-manager/assets/file-type-icons/'.$icon.'.svg') ?>" /></div>
            <div class="media-body">
                <h3 class="package-title"><a href='<?= site_url('download/'.$download['slug']) ?>'><?= $title ?></a></h3>
                <div class="text-muted text-small"><i class="fas fa-copy"></i> 1 file(s) <i class="fas fa-hdd ml-3"></i> <?= html_escape($download['file_size']) ?></div>
            </div>
            <div class="ml-3">
                <a class='wpdm-download-link download-on-click btn btn-primary btn-sm' rel='nofollow' href='#' data-downloadurl="<?= site_url('download/'.$download['slug']) ?>?wpdmdl=<?= $download['id'] ?>&amp;refresh=<?= $refresh ?>"><?= html_escape($download['button_label'] !== NULL ? $download['button_label'] : 'Download') ?></a>
            </div>
        </div>
    </div>
</div>

</div>