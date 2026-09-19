<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="tabs">
	<a href="<?= admin_url_query('admin/pages', array('q' => $filter['q'])) ?>" class="<?= $filter['status'] === '' ? 'active' : '' ?>">Semua (<?= $counts['all'] ?>)</a>
	<a href="<?= admin_url_query('admin/pages', array('status' => 'publish', 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'publish' ? 'active' : '' ?>">Terbit (<?= $counts['publish'] ?>)</a>
	<a href="<?= admin_url_query('admin/pages', array('status' => 'draft', 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'draft' ? 'active' : '' ?>">Draft (<?= $counts['draft'] ?>)</a>
</div>
<form class="filters" method="get" action="<?= site_url('admin/pages') ?>">
	<?php if ($filter['status']): ?><input type="hidden" name="status" value="<?= $filter['status'] ?>"><?php endif; ?>
	<input type="search" name="q" value="<?= html_escape($filter['q']) ?>" placeholder="Cari judul atau slug…">
	<button class="btn" type="submit">Filter</button>
	<a class="btn btn-primary" href="<?= site_url('admin/pages/create') ?>" style="margin-left:auto">+ Halaman baru</a>
</form>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Judul</th><th class="hide-sm">Editor</th><th>Status</th><th class="nowrap hide-sm">Diubah</th></tr></thead>
		<tbody>
		<?php if (empty($pages)): ?>
			<tr><td colspan="4" class="muted">Tidak ada halaman.</td></tr>
		<?php endif; ?>
		<?php foreach ($pages as $p): ?>
			<tr>
				<td>
					<a class="title" href="<?= site_url('admin/pages/edit/'.$p['id']) ?>"><?= html_escape($p['title']) ?></a>
					<div class="muted">/<?= html_escape($p['slug']) ?></div>
					<div class="row-actions">
						<a href="<?= site_url('admin/pages/edit/'.$p['id']) ?>">Edit</a>
						<?php if ($p['status'] === 'publish'): ?> · <a href="<?= site_url($p['slug']) ?>" target="_blank" rel="noopener">Lihat</a><?php endif; ?>
					</div>
				</td>
				<td class="hide-sm muted"><?= $p['elementor'] ? 'HTML (Elementor)' : 'Visual' ?></td>
				<td><span class="badge <?= $p['status'] ?>"><?= $p['status'] === 'publish' ? 'Terbit' : 'Draft' ?></span></td>
				<td class="nowrap muted hide-sm"><?= date('d/m/Y H:i', strtotime($p['modified_at'])) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<p class="hint">Beranda, Statistik, dan Lowongan Kerja memakai kerangka khusus sehingga tidak diedit di sini (Beranda lewat menu <a href="<?= site_url('admin/home_settings') ?>">Beranda</a>).</p>
<?= admin_pagination($page, $total_pages, function ($n) use ($filter) {
	return admin_url_query('admin/pages', array_merge($filter, array('page' => $n > 1 ? $n : NULL)));
}) ?>
