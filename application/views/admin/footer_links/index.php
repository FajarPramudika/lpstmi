<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="filters">
	<a class="btn btn-primary" href="<?= site_url('admin/footer_links/create') ?>" style="margin-left:auto">+ Tambah link footer</a>
</div>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead>
			<tr>
				<th class="num" style="width:1%">Urutan</th>
				<th>Judul</th>
				<th>URL</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($links)): ?>
			<tr><td colspan="3" class="muted text-center">Belum ada link footer.</td></tr>
		<?php endif; ?>
		<?php foreach ($links as $link): ?>
			<tr>
				<td class="num"><?= $link['order_num'] ?></td>
				<td>
					<a class="title" href="<?= site_url('admin/footer_links/edit/'.$link['id']) ?>"><?= html_escape($link['title']) ?></a>
					<div class="row-actions">
						<a href="<?= site_url('admin/footer_links/edit/'.$link['id']) ?>">Edit</a> ·
						<?= form_open('admin/footer_links/delete/'.$link['id'], array('class' => 'inline', 'data-confirm' => 'Hapus link "'.html_escape($link['title']).'"?')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
					</div>
				</td>
				<td class="muted"><a href="<?= html_escape($link['url']) ?>" target="_blank" rel="noopener" style="color:inherit; text-decoration:none"><?= html_escape($link['url']) ?></a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
