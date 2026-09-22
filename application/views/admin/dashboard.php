<?php defined('BASEPATH') OR exit('No direct script access allowed');
$num = function ($n) { return number_format((int) $n, 0, ',', '.'); };
$types = array(
	'post'     => array('label' => 'Post', 'admin' => 'admin/posts'),
	'page'     => array('label' => 'Halaman', 'admin' => 'admin/pages'),
	'download' => array('label' => 'Download', 'admin' => 'admin/downloads'),
);
// Kartu statistik: [ikon, URL, jumlah, label, catatan, URL draft (NULL = tanpa draft)]
$stats = array();
foreach ($types as $type => $t)
{
	$draft = $counts[$type]['draft'];
	$note = '';
	$stats[] = array($type, $t['admin'], $counts[$type]['publish'], $t['label'].' terbit', $note,
		$draft ? array($t['admin'].'?status=draft', $num($draft).' draft') : NULL);
}
$stats[] = array('media', 'admin/media', $media, 'File media', '', NULL);
?>
<div class="grid-stats">
	<?php foreach ($stats as $s): ?>
	<div class="stat">
		<span class="stat-icon"><?= admin_icon($s[0]) ?></span>
		<div class="stat-body">
			<a class="stat-link" href="<?= site_url($s[1]) ?>"><b><?= $num($s[2]) ?></b><span><?= $s[3] ?></span></a>
			<?php if ($s[4] !== ''): ?><small class="stat-note"><?= $s[4] ?></small><?php endif; ?>
			<?php if ($s[5]): ?><a class="stat-draft" href="<?= site_url($s[5][0]) ?>"><?= $s[5][1] ?></a><?php endif; ?>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<div class="quick-actions" style="justify-content: flex-end;">
	<a class="btn btn-primary" href="<?= site_url('admin/posts/create') ?>"><span>+ Post baru</span></a>
	<a class="btn btn-primary" href="<?= site_url('admin/pages/create') ?>"><span>+ Halaman baru</span></a>
	<a class="btn btn-primary" href="<?= site_url('admin/downloads/create') ?>"><span>+ Download baru</span></a>
	<a class="btn btn-primary" href="<?= site_url('admin/media') ?>#files"><span>+ Upload media</span></a>
</div>

<div class="card">
	<h2 style="margin:0 0 12px">Terakhir diubah</h2>
	<?php if (empty($recent)): ?>
	<p class="muted" style="margin:0">Belum ada konten.</p>
	<?php else: ?>
	<div class="table-wrap">
	<table class="list list-recent">
		<thead><tr><th>Judul</th><th>Jenis</th><th class="hide-sm">Author</th><th>Status</th><th class="nowrap">Diubah</th><th><span class="sr-only">Aksi</span></th></tr></thead>
		<tbody>
		<?php foreach ($recent as $r):
			$edit = site_url($types[$r['type']]['admin'].'/edit/'.$r['id']);
			$time = strtotime($r['modified_at']); ?>
			<tr>
				<td class="title"><a href="<?= $edit ?>"><?= html_escape($r['title']) ?></a></td>
				<td class="col-type"><span class="badge"><?= $types[$r['type']]['label'] ?></span></td>
				<td class="hide-sm nowrap col-author"><?= html_escape($r['author_name']) ?></td>
				<td class="col-status"><span class="badge <?= $r['status'] ?>"><?= $r['status'] === 'publish' ? 'Terbit' : 'Draft' ?></span></td>
				<td class="nowrap muted col-date"><time datetime="<?= date('c', $time) ?>"><?= date('d/m/Y', $time) ?><span class="time-hm"> <?= date('H:i', $time) ?></span></time></td>
				<td class="nowrap col-actions"><a href="<?= $edit ?>">Edit</a><?php if ($r['status'] === 'publish'): ?> · <a href="<?= Dashboard_model::public_url($r) ?>" target="_blank" rel="noopener">Lihat</a><?php endif; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
	<?php endif; ?>
</div>
