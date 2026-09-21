<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_new = empty($author['id']);
$v = function ($key) use ($author) { return html_escape(isset($author[$key]) ? (string) $author[$key] : ''); };
$action = $self ? 'admin/users/profile' : ($is_new ? 'admin/users/create' : 'admin/users/edit/'.$author['id']);
?>
<?php if ( ! $self): ?><div class="form-back-row"><a class="btn form-back" href="<?= site_url('admin/users') ?>">&larr; Kembali ke Pengguna</a></div><?php endif; ?>
<div class="card" style="max-width:680px">
	<?= form_open($action) ?>
		<div class="field">
			<label for="display_name">Nama tampilan</label>
			<input type="text" id="display_name" name="display_name" required maxlength="150" value="<?= $v('display_name') ?>">
			<div class="hint">Tampil sebagai author di situs.</div>
		</div>
		<?php if ( ! $self): ?>
		<div class="field">
			<label for="slug">Slug author</label>
			<input type="text" id="slug" name="slug" value="<?= $v('slug') ?>" placeholder="otomatis dari nama">
			<div class="hint">URL: <?= site_url('author/') ?>&lt;slug&gt;</div>
		</div>
		<div class="field">
			<label for="username">Username login</label>
			<input type="text" id="username" name="username" value="<?= $v('username') ?>" autocomplete="off">
			<div class="hint">Kosongkan jika hanya sebagai author tanpa akses admin. Tanpa username, login lewat email pun tidak bisa.</div>
		</div>
		<div class="field">
			<label for="role">Peran</label>
			<select id="role" name="role">
				<option value="editor" <?= ( ! isset($author['role']) OR $author['role'] === 'editor') ? 'selected' : '' ?>>Editor</option>
				<option value="admin" <?= (isset($author['role']) && $author['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
			</select>
		</div>
		<div class="field">
			<label class="check"><input type="checkbox" name="is_active" value="1" <?= ( ! isset($author['is_active']) OR $author['is_active']) ? 'checked' : '' ?>> Akun aktif</label>
		</div>
		<?php endif; ?>
		<div class="field">
			<label for="email">Email</label>
			<input type="email" id="email" name="email" value="<?= $v('email') ?>">
			<div class="hint">Dipakai untuk avatar Gravatar di halaman author, dan bisa dipakai untuk login selain username. Harus unik.</div>
		</div>
		<div class="field">
			<label for="website">Website</label>
			<input type="url" id="website" name="website" value="<?= $v('website') ?>" placeholder="https://">
		</div>
		<?php if ($self): ?>
		<div class="field">
			<label for="current_password">Password saat ini</label>
			<input type="password" id="current_password" name="current_password" autocomplete="current-password">
			<div class="hint">Wajib diisi hanya kalau Anda mengganti password.</div>
		</div>
		<?php endif; ?>
		<div class="field">
			<label for="password"><?= $is_new ? 'Password' : 'Password baru' ?></label>
			<input type="password" id="password" name="password" autocomplete="new-password" minlength="10">
			<div class="hint"><?= $is_new ? 'Minimal 10 karakter.' : 'Kosongkan jika tidak diubah. Minimal 10 karakter.' ?></div>
		</div>
		<div class="field">
			<label for="password_confirm">Ulangi password</label>
			<input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">
		</div>
		<button type="submit" class="btn btn-primary">Simpan</button>
	<?= form_close() ?>
</div>
