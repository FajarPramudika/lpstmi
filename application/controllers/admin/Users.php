<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola pengguna admin / author (khusus peran admin).
 */
class Users extends Admin_Controller {

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
				if (strlen($password) < 10)
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
				}
				else
				{
					$data['registered_at'] = date('Y-m-d H:i:s');
					if (empty($data['gravatar_hash']))
					{
						$data['gravatar_hash'] = Author_model::gravatar_hash('');
					}
					$this->author_model->create($data);
				}
				$this->flash('success', 'Data pengguna disimpan.');
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
