<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contacts extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('contact_model');
	}

	public function index()
	{
		if ($this->input->method() === 'post')
		{
			$data = array(
				'email'        => $this->input->post('email'),
				'phone'        => $this->input->post('phone'),
				'whatsapp'     => $this->input->post('whatsapp'),
				'whatsapp_url' => $this->input->post('whatsapp_url'),
			);

			$this->contact_model->update_all($data);
			$this->session->set_flashdata('success', 'Kontak berhasil diperbarui.');
			redirect('admin/contacts');
		}

		$this->view('admin/contacts/index', array(
			'title'         => 'Pengaturan Kontak',
			'menu_contacts' => TRUE,
			'contacts'      => $this->contact_model->get_all(),
		));
	}
}
