<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="filters">
	<span class="muted">Menu utama di header (desktop) dan menu mobile. Urutan di sini sama dengan urutan di situs.</span>
	<a class="btn btn-primary" href="<?= site_url('admin/menu/create') ?>" style="margin-left:auto">+ Tambah item</a>
</div>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list menu-tree">
		<thead><tr><th>Label</th><th class="hide-sm">Tipe</th><th class="hide-sm">Tujuan</th><th class="nowrap">Urutan</th></tr></thead>
		<tbody>
		<?php if (empty($items)): ?>
			<tr><td colspan="4" class="muted">Menu masih kosong.</td></tr>
		<?php endif; ?>
		<?php
		// Posisi di antara saudara, untuk menonaktifkan tombol naik/turun di ujung.
		$siblings = array();
		foreach ($items as $it) { $siblings[(int) $it['parent_id']][] = (int) $it['id']; }
		?>
		<?php foreach ($items as $it):
			$group = $siblings[(int) $it['parent_id']];
			$first = ($group[0] === (int) $it['id']);
			$last = (end($group) === (int) $it['id']);
			$href = Menu_model::href($it);
			if ($it['type'] === 'page') {
				$target = isset($pages[$it['slug']]) ? $pages[$it['slug']]['title'] : $it['slug'];
				$missing = isset($pages[$it['slug']]) && ! $pages[$it['slug']]['migrated'];
			} elseif ($it['type'] === 'category') {
				$target = $it['term_name'] !== NULL ? $it['term_name'] : '(kategori terhapus)';
				$missing = ($it['term_name'] === NULL);
			} else {
				$target = ($href === NULL) ? 'Tanpa link' : $href;
				$missing = FALSE;
			}
		?>
			<tr id="item-<?= $it['id'] ?>">
				<td style="padding-left:<?= 12 + $it['depth'] * 28 ?>px">
					<?= $it['depth'] ? '<span class="muted">&#8627;</span> ' : '' ?><a class="title" href="<?= site_url('admin/menu/edit/'.$it['id']) ?>"><?= html_escape($it['title']) ?></a>
					<div class="row-actions">
						<a href="<?= site_url('admin/menu/edit/'.$it['id']) ?>">Edit</a> ·
						<a href="<?= site_url('admin/menu/create?parent='.$it['id']) ?>">+ Sub-item</a> ·
						<?= form_open('admin/menu/delete/'.$it['id'], array('class' => 'inline', 'data-confirm' => $it['has_children']
							? 'Hapus "'.html_escape($it['title']).'" beserta SEMUA sub-itemnya?'
							: 'Hapus "'.html_escape($it['title']).'" dari menu?')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
					</div>
				</td>
				<td class="hide-sm"><?= $type_labels[$it['type']] ?></td>
				<td class="hide-sm muted" style="word-break:break-all">
					<?= html_escape($target) ?>
					<?php if ($missing): ?><span class="badge draft" title="Tujuan belum tersedia di situs CodeIgniter">belum ada</span><?php endif; ?>
				</td>
				<td class="nowrap">
					<?= form_open('admin/menu/move/'.$it['id'].'/up', array('class' => 'inline')) ?><button class="btn btn-sm" type="submit" title="Naik" aria-label="Naikkan" <?= $first ? 'disabled' : '' ?>>&uarr;</button><?= form_close() ?>
					<?= form_open('admin/menu/move/'.$it['id'].'/down', array('class' => 'inline')) ?><button class="btn btn-sm" type="submit" title="Turun" aria-label="Turunkan" <?= $last ? 'disabled' : '' ?>>&darr;</button><?= form_close() ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<p class="hint">Untuk memindah item ke induk lain, buka Edit lalu ubah "Induk". Item baru selalu ditaruh di urutan terakhir induknya.
Label "belum ada" berarti halaman tujuannya belum dimigrasi ke CodeIgniter (link akan 404 sampai halamannya dikonversi).</p>
