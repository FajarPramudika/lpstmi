<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_settings extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('home_featured_link_model');
		$this->load->model('home_study_program_model');
		$this->load->model('home_banner_model');
		$this->load->model('home_partner_model');
		$this->load->model('home_option_model');
	}

	public function index()
	{
		$data['title'] = 'Pengaturan Beranda';
		$data['featured_links'] = $this->home_featured_link_model->all();
		$data['study_programs'] = $this->home_study_program_model->all();
		$data['banners'] = $this->home_banner_model->all();
		$data['partners'] = $this->home_partner_model->all();
		$data['options'] = $this->home_option_model->get_all();
		$this->view('admin/home_settings/index', $data);
	}

	public function edit_link($id)
	{
		$link = $this->home_featured_link_model->find($id);
		if (!$link) show_404();

		$data['title'] = 'Edit Layanan';
		$data['link'] = $link;
		$this->view('admin/home_settings/form_link', $data);
	}

	public function create_link()
	{
		$data['title'] = 'Tambah Layanan';
		$data['link'] = null; // null indicates create mode
		$this->view('admin/home_settings/form_link', $data);
	}

	public function store_link()
	{
		$this->require_post();
		$data = array(
			'url' => $this->input->post('url'),
			'image_path' => $this->input->post('image_path'),
			'image_srcset' => $this->input->post('image_srcset') ?: null,
			'image_class' => $this->input->post('image_class'),
			'img_hint_key' => $this->input->post('img_hint_key') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
			'row_number' => (int) $this->input->post('row_number') ?: 1,
		);
		$this->home_featured_link_model->insert($data);
		$this->flash('success', 'Tautan layanan berhasil ditambahkan.');
		redirect('admin/home_settings?tab=links');
	}

	public function delete_link($id)
	{
		$this->require_post();
		$this->home_featured_link_model->delete($id);
		$this->flash('success', 'Tautan layanan berhasil dihapus.');
		redirect('admin/home_settings?tab=links');
	}

	public function update_link($id)
	{
		$this->require_post();
		$data = array(
			'url' => $this->input->post('url'),
			'image_path' => $this->input->post('image_path'),
			'image_srcset' => $this->input->post('image_srcset') ?: null,
			'image_class' => $this->input->post('image_class'),
			'img_hint_key' => $this->input->post('img_hint_key') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
			'row_number' => (int) $this->input->post('row_number') ?: 1,
		);
		$this->home_featured_link_model->update($id, $data);
		$this->flash('success', 'Tautan layanan berhasil diperbarui.');
		redirect('admin/home_settings?tab=links');
	}

	public function edit_program($id)
	{
		$program = $this->home_study_program_model->find($id);
		if (!$program) show_404();

		$data['title'] = 'Edit Program Studi';
		$data['program'] = $program;
		$this->view('admin/home_settings/form_program', $data);
	}

	public function create_program()
	{
		$data['title'] = 'Tambah Program Studi';
		$data['program'] = null;
		$this->view('admin/home_settings/form_program', $data);
	}

	public function store_program()
	{
		$this->require_post();
		$data = array(
			'title' => $this->input->post('title'),
			'description' => $this->input->post('description'),
			'url' => $this->input->post('url'),
			'icon_svg' => $this->input->post('icon_svg'),
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_study_program_model->insert($data);
		$this->flash('success', 'Program Studi berhasil ditambahkan.');
		redirect('admin/home_settings?tab=programs');
	}

	public function delete_program($id)
	{
		$this->require_post();
		$this->home_study_program_model->delete($id);
		$this->flash('success', 'Program Studi berhasil dihapus.');
		redirect('admin/home_settings?tab=programs');
	}

	public function update_program($id)
	{
		$this->require_post();
		$data = array(
			'title' => $this->input->post('title'),
			'description' => $this->input->post('description'),
			'url' => $this->input->post('url'),
			'icon_svg' => $this->input->post('icon_svg'),
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_study_program_model->update($id, $data);
		$this->flash('success', 'Program Studi berhasil diperbarui.');
		redirect('admin/home_settings?tab=programs');
	}

	public function edit_banner($id)
	{
		$banner = $this->home_banner_model->find($id);
		if (!$banner) show_404();
		$data['title'] = 'Edit Banner';
		$data['banner'] = $banner;
		$this->view('admin/home_settings/form_banner', $data);
	}

	public function create_banner()
	{
		$data['title'] = 'Tambah Banner';
		$data['banner'] = null;
		$this->view('admin/home_settings/form_banner', $data);
	}

	public function store_banner()
	{
		$this->require_post();
		$data = array(
			'image_path' => $this->input->post('image_path'),
			'image_srcset' => $this->input->post('image_srcset') ?: null,
			'image_class' => $this->input->post('image_class'),
			'url' => $this->input->post('url') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_banner_model->insert($data);
		$this->flash('success', 'Banner berhasil ditambahkan.');
		redirect('admin/home_settings?tab=banners');
	}

	public function delete_banner($id)
	{
		$this->require_post();
		$this->home_banner_model->delete($id);
		$this->flash('success', 'Banner berhasil dihapus.');
		redirect('admin/home_settings?tab=banners');
	}

	public function update_banner($id)
	{
		$this->require_post();
		$data = array(
			'image_path' => $this->input->post('image_path'),
			'image_srcset' => $this->input->post('image_srcset') ?: null,
			'image_class' => $this->input->post('image_class'),
			'url' => $this->input->post('url') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_banner_model->update($id, $data);
		$this->flash('success', 'Banner berhasil diperbarui.');
		redirect('admin/home_settings?tab=banners');
	}

	public function edit_partner($id)
	{
		$partner = $this->home_partner_model->find($id);
		if (!$partner) show_404();
		$data['title'] = 'Edit Mitra Kerjasama';
		$data['partner'] = $partner;
		$this->view('admin/home_settings/form_partner', $data);
	}

	public function create_partner()
	{
		$data['title'] = 'Tambah Mitra Kerjasama';
		$data['partner'] = null;
		$this->view('admin/home_settings/form_partner', $data);
	}

	public function store_partner()
	{
		$this->require_post();
		$data = array(
			'name' => $this->input->post('name'),
			'image_path' => $this->input->post('image_path'),
			'url' => $this->input->post('url') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_partner_model->insert($data);
		$this->flash('success', 'Mitra kerjasama berhasil ditambahkan.');
		redirect('admin/home_settings?tab=partners');
	}

	public function delete_partner($id)
	{
		$this->require_post();
		$this->home_partner_model->delete($id);
		$this->flash('success', 'Mitra kerjasama berhasil dihapus.');
		redirect('admin/home_settings?tab=partners');
	}

	public function update_partner($id)
	{
		$this->require_post();
		$data = array(
			'name' => $this->input->post('name'),
			'image_path' => $this->input->post('image_path'),
			'url' => $this->input->post('url') ?: null,
			'order_num' => (int) $this->input->post('order_num'),
		);
		$this->home_partner_model->update($id, $data);
		$this->flash('success', 'Mitra kerjasama berhasil diperbarui.');
		redirect('admin/home_settings?tab=partners');
	}

	public function update_options()
	{
		$this->require_post();
		$video_url = $this->input->post('video_url');
		$this->home_option_model->set('video_url', $video_url);
		$this->flash('success', 'Pengaturan opsi berhasil disimpan.');
		redirect('admin/home_settings?tab=options');
	}
}
