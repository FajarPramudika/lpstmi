<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="tabs">
	<a href="<?= admin_url_query('admin/downloads', array('q' => $filter['q'])) ?>" class="<?= $filter['status'] === '' ? 'active' : '' ?>">Semua (<?= $counts['publish'] + $counts['draft'] ?>)</a>
	<a href="<?= admin_url_query('admin/downloads', array('status' => 'publish', 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'publish' ? 'active' : '' ?>">Terbit (<?= $counts['publish'] ?>)</a>
	<a href="<?= admin_url_query('admin/downloads', array('status' => 'draft', 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'draft' ? 'active' : '' ?>">Draft (<?= $counts['draft'] ?>)</a>
</div>
<form class="filters" method="get" action="<?= site_url('admin/downloads') ?>">
	<?php if ($filter['status']): ?><input type="hidden" name="status" value="<?= $filter['status'] ?>"><?php endif; ?>
	<input type="search" name="q" value="<?= html_escape($filter['q']) ?>" placeholder="Cari judul…">
	<button class="btn" type="submit">Cari</button>
	<a class="btn btn-primary" href="<?= site_url('admin/downloads/create') ?>" style="margin-left:auto">+ Paket baru</a>
</form>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Judul</th><th class="hide-sm">File</th><th class="num">Unduhan</th><th>Status</th><th class="nowrap hide-sm">Dibuat</th></tr></thead>
		<tbody>
		<?php if (empty($items)): ?><tr><td colspan="5" class="muted">Tidak ada paket.</td></tr><?php endif; ?>
		<?php foreach ($items as $d): $external = Download_model::is_external($d); ?>
			<tr>
				<td>
					<a class="title" href="<?= site_url('admin/downloads/edit/'.$d['id']) ?>"><?= html_escape($d['title']) ?></a>
					<div class="row-actions">
						<a href="<?= site_url('admin/downloads/edit/'.$d['id']) ?>">Edit</a>
						<?php if ($d['status'] === 'publish'): ?> · <a href="<?= site_url('download/'.$d['slug']) ?>" target="_blank" rel="noopener">Lihat</a><?php endif; ?>
						·
						<?= form_open('admin/downloads/delete/'.$d['id'], array('class' => 'inline', 'data-confirm' => 'Hapus paket ini? Tombol Download yang menunjuk ke paket ini di post/halaman lain akan 404.')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
					</div>
				</td>
				<td class="hide-sm muted" style="word-break:break-all">
					<?= $external ? '<span class="badge">URL luar</span> ' : '' ?><?= html_escape($external ? parse_url($d['file'], PHP_URL_HOST) : basename($d['file'])) ?>
					<?php if ($d['file_size'] !== ''): ?><div><?= html_escape($d['file_size']) ?></div><?php endif; ?>
				</td>
				<td class="num"><?= number_format($d['download_count'], 0, ',', '.') ?></td>
				<td><span class="badge <?= $d['status'] ?>"><?= $d['status'] === 'publish' ? 'Terbit' : 'Draft' ?></span></td>
				<td class="nowrap muted hide-sm"><?= date('d/m/Y', strtotime($d['published_at'])) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<?= admin_pagination($page, $total_pages, function ($n) use ($filter) {
	return admin_url_query('admin/downloads', array_merge($filter, array('page' => $n > 1 ? $n : NULL)));
}) ?>
