<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="tabs">
	<a href="<?= admin_url_query('admin/posts', array('category' => $filter['category'], 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === '' ? 'active' : '' ?>">Semua (<?= $counts['publish'] + $counts['draft'] ?>)</a>
	<a href="<?= admin_url_query('admin/posts', array('status' => 'publish', 'category' => $filter['category'], 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'publish' ? 'active' : '' ?>">Terbit (<?= $counts['publish'] ?>)</a>
	<a href="<?= admin_url_query('admin/posts', array('status' => 'draft', 'category' => $filter['category'], 'q' => $filter['q'])) ?>" class="<?= $filter['status'] === 'draft' ? 'active' : '' ?>">Draft (<?= $counts['draft'] ?>)</a>
</div>
<form class="filters" method="get" action="<?= site_url('admin/posts') ?>">
	<?php if ($filter['status']): ?><input type="hidden" name="status" value="<?= $filter['status'] ?>"><?php endif; ?>
	<select name="category">
		<option value="">Semua kategori</option>
		<?php foreach ($categories as $c): ?>
		<option value="<?= $c['id'] ?>" <?= $filter['category'] === (int) $c['id'] ? 'selected' : '' ?>><?= html_escape($c['name']) ?></option>
		<?php endforeach; ?>
	</select>
	<input type="search" name="q" value="<?= html_escape($filter['q']) ?>" placeholder="Cari judul…">
	<button class="btn" type="submit">Filter</button>
	<a class="btn btn-primary" href="<?= site_url('admin/posts/create') ?>" style="margin-left:auto">+ Post baru</a>
</form>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Judul</th><th class="hide-sm">Author</th><th>Status</th><th class="nowrap">Tanggal terbit</th></tr></thead>
		<tbody>
		<?php if (empty($posts)): ?>
			<tr><td colspan="4" class="muted">Tidak ada post.</td></tr>
		<?php endif; ?>
		<?php foreach ($posts as $p): ?>
			<tr>
				<td>
					<a class="title" href="<?= site_url('admin/posts/edit/'.$p['id']) ?>"><?= html_escape($p['title']) ?></a>
					<div class="row-actions">
						<a href="<?= site_url('admin/posts/edit/'.$p['id']) ?>">Edit</a>
						<?php if ($p['status'] === 'publish'): ?> · <a href="<?= site_url($p['slug']) ?>" target="_blank" rel="noopener">Lihat</a><?php endif; ?>
						·
						<?= form_open('admin/posts/delete/'.$p['id'], array('class' => 'inline', 'data-confirm' => 'Hapus post ini secara permanen?')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
					</div>
				</td>
				<td class="hide-sm"><?= html_escape($p['author_name']) ?></td>
				<td><span class="badge <?= $p['status'] ?>"><?= $p['status'] === 'publish' ? 'Terbit' : 'Draft' ?></span></td>
				<td class="nowrap muted"><?= date('d/m/Y H:i', strtotime($p['published_at'])) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<?= admin_pagination($page, $total_pages, function ($n) use ($filter) {
	return admin_url_query('admin/posts', array_merge($filter, array('page' => $n > 1 ? $n : NULL)));
}) ?>
