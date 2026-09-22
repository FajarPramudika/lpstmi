<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	/** Editor ikut mengelola konten di sini. */
	protected $roles = array('admin', 'editor');

	public function index()
	{
		$this->load->model('dashboard_model');

		$this->view('admin/dashboard', array(
			'title'          => 'Dasbor',
			'counts'         => $this->dashboard_model->status_counts(),
			'download_total' => $this->dashboard_model->download_total(),
			'media'          => $this->db->count_all('media'),
			'recent'         => $this->dashboard_model->recent_changes(10),
		));
	}
}
