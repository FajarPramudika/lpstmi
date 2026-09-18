<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'links';
?>
<style>
	.nav-tabs { border-bottom: 1px solid #ddd; margin-bottom: 20px; display: flex; list-style: none; padding: 0; }
	.nav-tabs li { margin-bottom: -1px; }
	.nav-tabs li a { display: block; padding: 10px 15px; border: 1px solid transparent; border-radius: 4px 4px 0 0; text-decoration: none; color: #555; }
	.nav-tabs li.active a { border-color: #ddd #ddd transparent; background-color: #fff; color: #000; font-weight: bold; }
	.tab-content { display: none; }
	.tab-content.active { display: block; }
</style>

<ul class="nav-tabs">
	<li class="<?= $tab === 'links' ? 'active' : '' ?>"><a href="?tab=links">Layanan (Featured Links)</a></li>
	<li class="<?= $tab === 'programs' ? 'active' : '' ?>"><a href="?tab=programs">Program Studi</a></li>
	<li class="<?= $tab === 'banners' ? 'active' : '' ?>"><a href="?tab=banners">Banners (Slider)</a></li>
	<li class="<?= $tab === 'partners' ? 'active' : '' ?>"><a href="?tab=partners">Mitra Kerjasama</a></li>
	<li class="<?= $tab === 'options' ? 'active' : '' ?>"><a href="?tab=options">Video & Opsi Lain</a></li>
</ul>

<!-- TAB LINKS -->
<div class="tab-content <?= $tab === 'links' ? 'active' : '' ?>">
	<div class="filters">
		<h2 style="margin:0">Daftar Layanan</h2>
		<div style="flex:1"></div>
		<a href="<?= site_url('admin/home_settings/create_link') ?>" class="btn">Tambah Layanan</a>
	</div>
	<div class="card" style="padding:0">
		<div class="table-wrap">
			<table class="list">
				<thead>
					<tr>
						<th class="num" style="width:1%">Urutan</th>
						<th class="num" style="width:1%">Baris</th>
						<th>Gambar</th>
						<th>URL</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($featured_links as $link): ?>
					<tr>
						<td class="num"><?= $link['order_num'] ?></td>
						<td class="num"><?= html_escape(isset($link['row_number']) ? $link['row_number'] : 1) ?></td>
						<td><img src="<?= base_url(html_escape($link['image_path'])) ?>" alt="" style="max-height:40px; background:#ccc; padding:2px"></td>
						<td><?= html_escape($link['url']) ?></td>
						<td>
							<a href="<?= site_url('admin/home_settings/edit_link/'.$link['id']) ?>" class="btn btn-sm">Ubah</a>
							<?= form_open('admin/home_settings/delete_link/'.$link['id'], ['style' => 'display:inline;', 'data-confirm' => 'Apakah Anda yakin ingin menghapus layanan ini?']) ?>
								<button type="submit" class="btn btn-sm btn-danger" style="background:#dc3545;color:white;border:none;">Hapus</button>
							<?= form_close() ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- TAB PROGRAMS -->
<div class="tab-content <?= $tab === 'programs' ? 'active' : '' ?>">
	<div class="filters">
		<h2 style="margin:0">Daftar Program Studi</h2>
		<div style="flex:1"></div>
		<a href="<?= site_url('admin/home_settings/create_program') ?>" class="btn">Tambah Program Studi</a>
	</div>
	<div class="card" style="padding:0">
		<div class="table-wrap">
			<table class="list">
				<thead>
					<tr>
						<th class="num" style="width:1%">Urutan</th>
						<th>Judul</th>
						<th>URL</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($study_programs as $prog): ?>
					<tr>
						<td class="num"><?= $prog['order_num'] ?></td>
						<td><?= html_escape($prog['title']) ?></td>
						<td><?= html_escape($prog['url']) ?></td>
						<td>
							<a href="<?= site_url('admin/home_settings/edit_program/'.$prog['id']) ?>" class="btn btn-sm">Ubah</a>
							<?= form_open('admin/home_settings/delete_program/'.$prog['id'], ['style' => 'display:inline;', 'data-confirm' => 'Apakah Anda yakin ingin menghapus program studi ini?']) ?>
								<button type="submit" class="btn btn-sm btn-danger" style="background:#dc3545;color:white;border:none;">Hapus</button>
							<?= form_close() ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- TAB BANNERS -->
<div class="tab-content <?= $tab === 'banners' ? 'active' : '' ?>">
	<div class="filters">
		<h2 style="margin:0">Daftar Banners (Slider Utama)</h2>
		<div style="flex:1"></div>
		<a href="<?= site_url('admin/home_settings/create_banner') ?>" class="btn">Tambah Banner</a>
	</div>
	<div class="card" style="padding:0">
		<div class="table-wrap">
			<table class="list">
				<thead>
					<tr>
						<th class="num" style="width:1%">Urutan</th>
						<th style="width:200px">Gambar (Preview)</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($banners as $banner): ?>
					<tr>
						<td class="num"><?= $banner['order_num'] ?></td>
						<td><img src="<?= base_url(html_escape($banner['image_path'])) ?>" alt="" style="max-height:80px; max-width:200px; object-fit:contain; background:#f0f0f0;"></td>
						<td>
							<a href="<?= site_url('admin/home_settings/edit_banner/'.$banner['id']) ?>" class="btn btn-sm">Ubah</a>
							<?= form_open('admin/home_settings/delete_banner/'.$banner['id'], ['style' => 'display:inline;', 'data-confirm' => 'Apakah Anda yakin ingin menghapus banner ini?']) ?>
								<button type="submit" class="btn btn-sm btn-danger" style="background:#dc3545;color:white;border:none;">Hapus</button>
							<?= form_close() ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- TAB PARTNERS -->
<div class="tab-content <?= $tab === 'partners' ? 'active' : '' ?>">
	<div class="filters">
		<h2 style="margin:0">Daftar Mitra Kerjasama</h2>
		<div style="flex:1"></div>
		<a href="<?= site_url('admin/home_settings/create_partner') ?>" class="btn">Tambah Mitra</a>
	</div>
	<div class="card" style="padding:0">
		<div class="table-wrap">
			<table class="list">
				<thead>
					<tr>
						<th class="num" style="width:1%">Urutan</th>
						<th style="width:150px">Logo</th>
						<th>Nama Instansi</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($partners as $partner): ?>
					<tr>
						<td class="num"><?= $partner['order_num'] ?></td>
						<td><img src="<?= base_url(html_escape($partner['image_path'])) ?>" alt="<?= html_escape($partner['name']) ?>" style="max-height:50px; background:#fff;"></td>
						<td><?= html_escape($partner['name']) ?></td>
						<td>
							<a href="<?= site_url('admin/home_settings/edit_partner/'.$partner['id']) ?>" class="btn btn-sm">Ubah</a>
							<?= form_open('admin/home_settings/delete_partner/'.$partner['id'], ['style' => 'display:inline;', 'data-confirm' => 'Apakah Anda yakin ingin menghapus mitra ini?']) ?>
								<button type="submit" class="btn btn-sm btn-danger" style="background:#dc3545;color:white;border:none;">Hapus</button>
							<?= form_close() ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- TAB OPTIONS -->
<div class="tab-content <?= $tab === 'options' ? 'active' : '' ?>">
	<div class="filters">
		<h2 style="margin:0">Video & Opsi Lainnya</h2>
		<div style="flex:1"></div>
	</div>
	<?= form_open('admin/home_settings/update_options') ?>
	<div class="card">
		<div class="field">
			<label for="video_url">URL Video Profil (YouTube)</label>
			<input type="url" id="video_url" name="video_url" value="<?= html_escape(isset($options['video_url']) ? $options['video_url'] : '') ?>" required maxlength="255">
			<div class="hint">Tautan video YouTube yang dirender pada beranda. Contoh: <code>https://youtu.be/kTt11d4Twik</code></div>
		</div>
		<button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
	</div>
	<?= form_close() ?>
</div>
