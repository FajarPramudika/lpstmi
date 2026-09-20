<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login / logout panel admin.
 */
class Auth extends CI_Controller {

	/**
	 * Batas percobaan gagal dalam satu jendela waktu (LOCK_SECONDS).
	 * Dicatat di tabel login_attempts, bukan di session: hitungan di session ada di sisi penyerang,
	 * sehingga bisa dilewati hanya dengan membuang cookie di setiap percobaan.
	 * Batas per-username melindungi satu akun; batas per-IP melindungi dari penyapuan banyak username.
	 */
	const MAX_ATTEMPTS = 5;
	const MAX_IP_ATTEMPTS = 10;
	const LOCK_SECONDS = 300;

	/**
	 * Hash umpan (bcrypt cost 10, dari string acak yang dibuang) supaya password_verify() tetap dijalankan
	 * walau username tidak ada. Tanpa ini, request untuk username yang ada terukur lebih lambat karena
	 * hanya di situ bcrypt berjalan, dan selisihnya bisa dipakai memilah username yang valid.
	 */
	const DUMMY_HASH = '$2y$10$MNOXaX8lLcyu/S.zAyTNzeQIBEiwobSvCAEwM5TsaOMzSCKTGHX5.';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('form');
		$this->load->model('author_model');
		$this->output->set_header('Cache-Control: no-store');
	}

	public function login()
	{
		if ($this->session->userdata('admin_id'))
		{
			redirect('admin');
		}

		$error = NULL;
		if ($this->input->method() === 'post')
		{
			$username = trim((string) $this->input->post('username'));
			$password = (string) $this->input->post('password');

			$this->purge_attempts();

			if ($this->is_throttled($username))
			{
				$this->audit('login ditolak (terkunci)', $username);
				$error = 'Terlalu banyak percobaan gagal. Coba lagi dalam '.ceil(self::LOCK_SECONDS / 60).' menit.';
			}
			else
			{
				$user = $this->author_model->find_by_username($username);

				// Selalu jalankan satu verifikasi bcrypt, walau akunnya tidak ada / tidak aktif / tanpa password.
				$usable = ($user && $user['is_active'] && $user['password_hash']);
				$verified = password_verify($password, $usable ? $user['password_hash'] : self::DUMMY_HASH);

				if ($usable && $verified)
				{
					$this->audit('login berhasil', $username, 'id='.$user['id'].' peran='.$user['role']);
					$this->clear_attempts($username);
					$this->session->sess_regenerate(TRUE);
					$this->session->set_userdata('admin_id', (int) $user['id']);
					// Penanda versi password: session menjadi tidak berlaku kalau password diubah di tempat lain.
					$this->session->set_userdata('pw_at', $user['password_changed_at']);
					$this->author_model->touch_login($user['id']);

					if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT))
					{
						$this->author_model->update($user['id'], array('password_hash' => password_hash($password, PASSWORD_DEFAULT)));
					}

					$target = (string) $this->session->userdata('admin_redirect');
					$this->session->unset_userdata('admin_redirect');
					redirect(strpos($target, 'admin') === 0 ? $target : 'admin');
				}

				$this->record_attempt($username);
				$this->audit('login gagal', $username);
				$error = 'Username atau password salah.';
			}
		}

		$this->load->view('admin/login', array('error' => $error));
	}

	public function logout()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Metode tidak diizinkan.', 405);
		}
		$this->session->sess_destroy();
		redirect('admin/login');
	}

	/**
	 * Catat peristiwa keamanan ke application/logs/. Memakai level 'error' karena itu satu-satunya
	 * level yang aktif (log_threshold = 1); level lain ikut membawa log internal CI yang sangat berisik.
	 * Password tidak pernah ikut dicatat.
	 */
	protected function audit($event, $username, $extra = '')
	{
		log_message('error', '[keamanan] '.$event.': username="'.$username.'" ip='.$this->input->ip_address()
			.($extra !== '' ? ' '.$extra : ''));
	}

	/* ------------------------------------------------------------------
	 * Pembatasan percobaan login (tabel login_attempts)
	 * ------------------------------------------------------------------ */

	/** Bentuk biner IP peminta; string kosong kalau IP tidak valid. */
	protected function ip()
	{
		$packed = @inet_pton($this->input->ip_address());

		return ($packed === FALSE) ? '' : $packed;
	}

	protected function window()
	{
		return date('Y-m-d H:i:s', time() - self::LOCK_SECONDS);
	}

	/** Buang catatan yang sudah lewat jendela waktu, supaya tabel tidak tumbuh terus. */
	protected function purge_attempts()
	{
		$this->db->where('attempted_at <', $this->window())->delete('login_attempts');
	}

	protected function is_throttled($username)
	{
		$window = $this->window();

		$by_ip = $this->db->where('ip', $this->ip())->where('attempted_at >=', $window)->count_all_results('login_attempts');
		if ($by_ip >= self::MAX_IP_ATTEMPTS)
		{
			return TRUE;
		}

		if ($username === '')
		{
			return FALSE;
		}

		return $this->db->where('username', $username)->where('attempted_at >=', $window)->count_all_results('login_attempts') >= self::MAX_ATTEMPTS;
	}

	protected function record_attempt($username)
	{
		$this->db->insert('login_attempts', array(
			'ip'           => $this->ip(),
			'username'     => mb_substr($username, 0, 60),
			'attempted_at' => date('Y-m-d H:i:s'),
		));
	}

	/** Login berhasil: bersihkan catatan untuk username itu dan untuk IP ini. */
	protected function clear_attempts($username)
	{
		$this->db->group_start()->where('username', $username)->or_where('ip', $this->ip())->group_end()->delete('login_attempts');
	}
}
