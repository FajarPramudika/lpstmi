<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login / logout panel admin.
 */
class Auth extends CI_Controller {

	/** Percobaan login gagal maksimum per sesi sebelum dikunci sementara. */
	const MAX_ATTEMPTS = 5;
	const LOCK_SECONDS = 300;

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
			$locked_until = (int) $this->session->userdata('login_locked_until');
			if ($locked_until > time())
			{
				$error = 'Terlalu banyak percobaan gagal. Coba lagi dalam '.ceil(($locked_until - time()) / 60).' menit.';
			}
			else
			{
				$user = $this->author_model->find_by_username(trim((string) $this->input->post('username')));
				$password = (string) $this->input->post('password');

				if ($user && $user['is_active'] && $user['password_hash'] && password_verify($password, $user['password_hash']))
				{
					$this->session->sess_regenerate(TRUE);
					$this->session->unset_userdata(array('login_attempts', 'login_locked_until'));
					$this->session->set_userdata('admin_id', (int) $user['id']);
					$this->author_model->touch_login($user['id']);

					if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT))
					{
						$this->author_model->update($user['id'], array('password_hash' => password_hash($password, PASSWORD_DEFAULT)));
					}

					$target = (string) $this->session->userdata('admin_redirect');
					$this->session->unset_userdata('admin_redirect');
					redirect(strpos($target, 'admin') === 0 ? $target : 'admin');
				}

				$attempts = (int) $this->session->userdata('login_attempts') + 1;
				$this->session->set_userdata('login_attempts', $attempts);
				if ($attempts >= self::MAX_ATTEMPTS)
				{
					$this->session->set_userdata(array('login_attempts' => 0, 'login_locked_until' => time() + self::LOCK_SECONDS));
				}
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
}
