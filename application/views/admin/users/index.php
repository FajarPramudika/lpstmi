<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="filters">
	<a class="btn btn-primary" href="<?= site_url('admin/users/create') ?>" style="margin-left:auto">+ Tambah pengguna</a>
</div>
<div class="card" style="padding:0">
	<div class="table-wrap">
	<table class="list">
		<thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th class="hide-sm">Login terakhir</th><th class="num">Post</th></tr></thead>
		<tbody>
		<?php foreach ($users as $u): ?>
			<tr>
				<td>
					<a class="title" href="<?= site_url('admin/users/edit/'.$u['id']) ?>"><?= html_escape($u['display_name']) ?></a>
					<?php if ( ! $u['is_active']): ?> <span class="badge">nonaktif</span><?php endif; ?>
					<div class="row-actions">
						<a href="<?= site_url('author/'.$u['slug']) ?>" target="_blank" rel="noopener">Halaman author</a>
						<?php if ((int) $u['post_count'] === 0 && (int) $u['id'] !== (int) $user['id']): ?> ·
						<?= form_open('admin/users/delete/'.$u['id'], array('class' => 'inline', 'data-confirm' => 'Hapus pengguna ini?')) ?><button class="btn-link danger" type="submit">Hapus</button><?= form_close() ?>
						<?php endif; ?>
					</div>
				</td>
				<td><?= $u['username'] ? html_escape($u['username']) : '<span class="muted">tidak bisa login</span>' ?></td>
				<td><?= $u['role'] === 'admin' ? 'Admin' : 'Editor' ?></td>
				<td class="hide-sm muted"><?= $u['last_login_at'] ? date('d/m/Y H:i', strtotime($u['last_login_at'])) : '–' ?></td>
				<td class="num"><?= $u['post_count'] ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
<p class="hint">Admin: semua menu termasuk pengguna. Editor: post, media, kategori, tag, dan profil sendiri.</p>
