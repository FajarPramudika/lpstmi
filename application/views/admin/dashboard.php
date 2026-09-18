<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="grid-stats">
	<div class="stat"><b><?= $counts['publish'] ?></b><span>Post terbit</span></div>
	<div class="stat"><b><?= $counts['draft'] ?></b><span>Draft</span></div>
	<div class="stat"><b><?= $categories ?></b><span>Kategori</span></div>
	<div class="stat"><b><?= $tags ?></b><span>Tag</span></div>
	<div class="stat"><b><?= $media ?></b><span>File media</span></div>
</div>
<div class="card">
	<div class="page-head" style="margin-bottom:12px">
		<h2 style="margin:0">Post terbaru</h2>
		<a class="btn btn-primary" href="<?= site_url('admin/posts/create') ?>">+ Post baru</a>
	</div>
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Judul</th><th class="hide-sm">Author</th><th>Status</th><th class="nowrap">Tanggal</th></tr></thead>
		<tbody>
		<?php foreach ($recent as $p): ?>
			<tr>
				<td class="title"><a href="<?= site_url('admin/posts/edit/'.$p['id']) ?>"><?= html_escape($p['title']) ?></a></td>
				<td class="hide-sm"><?= html_escape($p['author_name']) ?></td>
				<td><span class="badge <?= $p['status'] ?>"><?= $p['status'] === 'publish' ? 'Terbit' : 'Draft' ?></span></td>
				<td class="nowrap muted"><?= date('d/m/Y H:i', strtotime($p['published_at'])) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
