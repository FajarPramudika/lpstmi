<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola pengguna admin / author (khusus peran admin).
 */
class Users extends Admin_Controller {

	/** Editor hanya untuk profile(); dibatasi per method di bawah. */
	protected $roles = array('admin', 'editor');

	public function __construct()
	{
		parent::__construct();

		// Semua peran boleh mengubah profil sendiri; kelola pengguna lain khusus admin.
		if ($this->router->fetch_method() !== 'profile' && $this->user['role'] !== 'admin')
		{
			show_error('Anda tidak punya akses ke halaman ini.', 403, 'Akses ditolak');
		}
	}

	public function index()
	{
		$this->view('admin/users/index', array(
			'title' => 'Pengguna',
			'users' => $this->author_model->all(),
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$author = $this->author_model->find($id);
		if ( ! $author)
		{
			show_404();
		}
		$this->form($author);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$author = $this->author_model->find($id);
		if ( ! $author)
		{
			show_404();
		}
		if ((int) $author['id'] === (int) $this->user['id'])
		{
			$this->flash('error', 'Tidak bisa menghapus akun sendiri.');
		}
		elseif ($this->db->where('author_id', $author['id'])->count_all_results('posts') > 0)
		{
			$this->flash('error', 'Pengguna masih punya post. Pindahkan atau hapus post-nya dulu, atau nonaktifkan akunnya.');
		}
		else
		{
			$this->author_model->delete($author['id']);
			$this->audit('pengguna dihapus: "'.$author['username'].'" id='.$author['id'].' peran='.$author['role']);
			$this->flash('success', 'Pengguna dihapus.');
		}
		redirect('admin/users');
	}

	/**
	 * Profil sendiri (semua peran): nama, email, password.
	 */
	public function profile()
	{
		$this->form($this->user, TRUE);
	}

	protected function form($author, $self = FALSE)
	{
		$errors = array();

		if ($this->input->method() === 'post')
		{
			$in = $this->input;
			$data = array(
				'display_name' => trim((string) $in->post('display_name')),
				'email'        => trim((string) $in->post('email')),
				'website'      => trim((string) $in->post('website')),
			);
			if ( ! $self)
			{
				$data['username'] = trim((string) $in->post('username'));
				$data['slug'] = slugify(trim((string) $in->post('slug')) !== '' ? $in->post('slug') : $data['display_name']);
				$data['role'] = ($in->post('role') === 'admin') ? 'admin' : 'editor';
				$data['is_active'] = $in->post('is_active') ? 1 : 0;
			}
			$password = (string) $in->post('password');
			$except = $author ? $author['id'] : NULL;

			if ($data['display_name'] === '')
			{
				$errors[] = 'Nama tampilan wajib diisi.';
			}
			if ($data['email'] !== '' && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL))
			{
				$errors[] = 'Email tidak valid.';
			}
			if ($data['website'] !== '' && ! filter_var($data['website'], FILTER_VALIDATE_URL))
			{
				$errors[] = 'Website harus URL lengkap (https://...).';
			}
			if ( ! $self)
			{
				if ($data['username'] === '')
				{
					$data['username'] = NULL;
				}
				elseif ( ! preg_match('/^[a-zA-Z0-9_.-]{3,60}$/', $data['username']))
				{
					$errors[] = 'Username 3-60 karakter: huruf, angka, titik, garis bawah, atau strip.';
				}
				elseif ($this->author_model->username_taken($data['username'], $except))
				{
					$errors[] = 'Username sudah dipakai.';
				}
				if ($data['slug'] === '' OR $this->author_model->slug_taken($data['slug'], $except))
				{
					$errors[] = 'Slug author kosong atau sudah dipakai.';
				}
				if ($author && (int) $author['id'] === (int) $this->user['id'] && ($data['role'] !== 'admin' OR ! $data['is_active']))
				{
					$errors[] = 'Tidak bisa menurunkan peran atau menonaktifkan akun sendiri.';
				}
			}
			if ($password !== '')
			{
				// Mengubah password sendiri wajib menyertakan password saat ini: sesi yang dibajak tidak
				// boleh bisa mengunci pemilik akun keluar. Admin yang mengubah akun orang lain tidak perlu.
				if ($self && ! password_verify((string) $in->post('current_password'), (string) $this->user['password_hash']))
				{
					$errors[] = 'Password saat ini salah.';
				}
				elseif (strlen($password) < 10)
				{
					$errors[] = 'Password minimal 10 karakter.';
				}
				elseif ($password !== (string) $in->post('password_confirm'))
				{
					$errors[] = 'Konfirmasi password tidak sama.';
				}
				else
				{
					$data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
					// Mematikan sesi lain milik akun ini (lihat Admin_Controller).
					$data['password_changed_at'] = date('Y-m-d H:i:s');
				}
			}
			if ( ! $self && ! empty($data['username']) && $password === '' && ( ! $author OR empty($author['password_hash'])))
			{
				$errors[] = 'Isi password agar pengguna bisa login.';
			}

			$data['website'] = ($data['website'] === '') ? NULL : $data['website'];
			$data['email'] = ($data['email'] === '') ? NULL : $data['email'];
			if ($data['email'])
			{
				$data['gravatar_hash'] = Author_model::gravatar_hash($data['email']);
			}

			if (empty($errors))
			{
				if ($author)
				{
					$this->author_model->update($author['id'], $data);

					$changes = array();
					if (isset($data['role']) && $data['role'] !== $author['role'])
					{
						$changes[] = 'peran '.$author['role'].' -> '.$data['role'];
					}
					if (isset($data['is_active']) && (int) $data['is_active'] !== (int) $author['is_active'])
					{
						$changes[] = $data['is_active'] ? 'diaktifkan' : 'dinonaktifkan';
					}
					if (isset($data['password_changed_at']))
					{
						$changes[] = 'password diganti';
					}
					if ($changes)
					{
						$this->audit('akun "'.$author['username'].'" id='.$author['id'].': '.implode(', ', $changes));
					}

					// Kalau yang berubah adalah password akun yang sedang dipakai, perbarui penanda di session ini
					// supaya yang mengubah tidak ikut terlempar ke halaman login; sesi lain tetap terputus.
					if (isset($data['password_changed_at']) && (int) $author['id'] === (int) $this->user['id'])
					{
						$this->session->set_userdata('pw_at', $data['password_changed_at']);
					}
				}
				else
				{
					$data['registered_at'] = date('Y-m-d H:i:s');
					if (empty($data['gravatar_hash']))
					{
						$data['gravatar_hash'] = Author_model::gravatar_hash('');
					}
					$new_id = $this->author_model->create($data);
					$this->audit('pengguna dibuat: "'.$data['username'].'" id='.$new_id.' peran='.$data['role']);
				}
				$this->flash('success', 'Data pengguna disimpan.'
					.(isset($data['password_changed_at']) ? ' Password diganti, jadi sesi lain akun ini diakhiri.' : ''));
				redirect($self ? 'admin/users/profile' : 'admin/users');
			}
			$author = array_merge($author ? $author : array(), $data);
		}

		$this->view('admin/users/form', array(
			'title'  => $self ? 'Profil saya' : ($author && ! empty($author['id']) ? 'Edit pengguna' : 'Tambah pengguna'),
			'author' => $author,
			'self'   => $self,
			'errors' => $errors,
		));
	}
}
