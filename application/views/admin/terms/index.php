<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="filters">
	<a class="btn btn-primary" href="<?= site_url('admin/terms/create/'.$type) ?>" style="margin-left:auto">+ Tambah <?= strtolower($tax['label']) ?></a>
</div>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Nama</th><th>Slug</th><th class="num">Post</th></tr></thead>
		<tbody>
		<?php if (empty($terms)): ?><tr><td colspan="3" class="muted">Belum ada data.</td></tr><?php endif; ?>
		<?php foreach ($terms as $t): ?>
			<tr>
				<td>
					<a class="title" href="<?= site_url('admin/terms/edit/'.$type.'/'.$t['id']) ?>"><?= html_escape($t['name']) ?></a>
					<div class="row-actions">
						<a href="<?= site_url('admin/terms/edit/'.$type.'/'.$t['id']) ?>">Edit</a> ·
						<a href="<?= site_url($tax['path'].'/'.$t['slug']) ?>" target="_blank" rel="noopener">Lihat</a> ·
						<?= form_open('admin/terms/delete/'.$type.'/'.$t['id'], array('class' => 'inline', 'data-confirm' => 'Hapus "'.html_escape($t['name']).'"? Post tidak ikut terhapus.')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
					</div>
				</td>
				<td class="muted"><?= html_escape($t['slug']) ?></td>
				<td class="num"><a href="<?= $type === 'category' ? admin_url_query('admin/posts', array('category' => $t['id'])) : site_url($tax['path'].'/'.$t['slug']) ?>"><?= $t['post_count'] ?></a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<?php if ($type === 'category'): ?>
<p class="hint">Menu utama situs (header) tidak berubah otomatis saat kategori ditambah. Menu kategori yang ditandai aktif diatur di <code>application/config/site.php</code>.</p>
<?php endif; ?>
