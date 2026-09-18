<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller dasar untuk halaman publik: merender layout hasil migrasi WordPress.
 */
class MY_Controller extends CI_Controller {

	/**
	 * Render halaman ke dalam layout.
	 *
	 * $page (lihat application/config/pages.php):
	 *   view        : view isi halaman (bagian di antara header dan footer)
	 *   title       : isi <title> (sudah dalam bentuk HTML)
	 *   body_attrs  : atribut tag <body> apa adanya
	 *   head / foot : nama varian aset di views/layouts/head/ dan views/layouts/foot/
	 *   menu_active : kelas menu aktif per ID menu-item (lihat wp_menu_active())
	 *   img_hints   : fetchpriority/loading per <img> di header/drawer/footer (lihat wp_img_hint())
	 */
	protected function render(array $page, array $data = array())
	{
		$uri = $this->uri->uri_string();

		$this->load->model('footer_link_model');
		$this->load->model('contact_model');

		$data = array_merge(array(
			'canonical'              => current_url(),
			'gt_orig_url'            => ($uri === '') ? '/' : '/'.$uri.'/',
			'menu_active'            => array(),
			'img_hints'              => array(),
			'footer_logo_post_image' => FALSE,
			'footer_links'           => $this->footer_link_model->all(),
			'contacts'               => $this->contact_model->get_all(),
		), $page, $data);

		$html = $this->load->view('layouts/main', $data, TRUE);

		$this->output->set_output(wp_menu_active($html, $data['menu_active']));
	}
}

/**
 * Controller dasar panel admin (/admin): wajib login, memuat session & form helper.
 */
class Admin_Controller extends CI_Controller {

	/** @var array|null author yang sedang login */
	protected $user;

	/** Peran yang boleh mengakses controller ini. */
	protected $roles = array('admin', 'editor');

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('session', 'form_validation'));
		$this->load->helper(array('form', 'admin'));
		$this->load->model('author_model');

		$id = $this->session->userdata('admin_id');
		$this->user = $id ? $this->author_model->find($id) : NULL;

		if ( ! $this->user OR ! $this->user['is_active'] OR $this->user['password_hash'] === NULL)
		{
			$this->session->unset_userdata('admin_id');
			$this->session->set_userdata('admin_redirect', $this->uri->uri_string());
			redirect('admin/login');
		}

		if ( ! in_array($this->user['role'], $this->roles, TRUE))
		{
			show_error('Anda tidak punya akses ke halaman ini.', 403, 'Akses ditolak');
		}

		$this->output->set_header('Cache-Control: no-store');
	}

	/**
	 * Render halaman admin di dalam views/admin/layout.php.
	 */
	protected function view($view, array $data = array())
	{
		$data['user'] = $this->user;
		$data['content_view'] = $view;
		$this->load->view('admin/layout', $data);
	}

	protected function flash($type, $message)
	{
		$this->session->set_flashdata('flash', array('type' => $type, 'message' => $message));
	}

	/**
	 * Tolak request non-POST untuk aksi yang mengubah data (hapus, dsb.).
	 */
	protected function require_post()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Metode tidak diizinkan.', 405);
		}
	}
}
