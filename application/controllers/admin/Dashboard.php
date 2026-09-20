<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	/** Editor ikut mengelola konten di sini. */
	protected $roles = array('admin', 'editor');

	public function index()
	{
		$this->load->model(array('post_model', 'term_model', 'media_model'));
		list($recent) = $this->post_model->admin_list(array(), 1);

		$this->view('admin/dashboard', array(
			'title'      => 'Dasbor',
			'counts'     => $this->post_model->counts(),
			'categories' => count($this->term_model->all('category')),
			'tags'       => count($this->term_model->all('post_tag')),
			'media'      => $this->db->count_all('media'),
			'recent'     => array_slice($recent, 0, 8),
		));
	}
}
