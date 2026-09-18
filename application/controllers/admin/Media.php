<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pustaka media: upload gambar/dokumen, alt text, hapus.
 */
class Media extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('media_model');
	}

	public function index()
	{
		$type = in_array($this->input->get('type'), array('image', 'file'), TRUE) ? $this->input->get('type') : NULL;
		$q = trim((string) $this->input->get('q'));
		$page = max(1, (int) $this->input->get('page'));
		list($items, $total) = $this->media_model->paginate($page, $type, $q);

		$this->view('admin/media/index', array(
			'title'       => 'Media',
			'items'       => $items,
			'total'       => $total,
			'type'        => $type,
			'q'           => $q,
			'page'        => $page,
			'total_pages' => max(1, (int) ceil($total / Media_model::PER_PAGE)),
		));
	}

	/**
	 * Upload dari form (banyak file, field "files[]") atau AJAX (field "file", respons JSON).
	 */
	public function upload()
	{
		$this->require_post();
		$this->load->library('media_uploader');
		$ajax = ($this->input->is_ajax_request() || $this->input->get('json'));

		$files = $this->normalize_files();
		if (empty($files))
		{
			return $this->respond($ajax, FALSE, 'Tidak ada file yang dipilih (atau melebihi batas post_max_size server).');
		}

		$uploaded = array();
		$errors = array();
		foreach ($files as $file)
		{
			$row = $this->media_uploader->handle($file);
			if ($row === FALSE)
			{
				$errors[] = $file['name'].': '.$this->media_uploader->error();
				continue;
			}
			$id = $this->media_model->create($row);
			$uploaded[] = $this->item_json($this->media_model->find($id));
		}

		if ($ajax)
		{
			if (empty($uploaded))
			{
				return $this->respond(TRUE, FALSE, implode("\n", $errors));
			}
			// "location" dipakai TinyMCE untuk menyisipkan gambar.
			return $this->json(array('ok' => TRUE, 'items' => $uploaded, 'location' => $uploaded[0]['url'], 'errors' => $errors));
		}

		if ( ! empty($uploaded))
		{
			$this->flash('success', count($uploaded).' file diunggah.'.( ! empty($errors) ? ' Gagal: '.implode('; ', $errors) : ''));
		}
		else
		{
			$this->flash('error', implode('; ', $errors));
		}
		redirect('admin/media');
	}

	/**
	 * JSON untuk pemilih gambar unggulan / sisip media di editor.
	 */
	public function browse()
	{
		$page = max(1, (int) $this->input->get('page'));
		$type = ($this->input->get('type') === 'file') ? 'file' : (($this->input->get('type') === 'all') ? NULL : 'image');
		list($items, $total) = $this->media_model->paginate($page, $type, trim((string) $this->input->get('q')));

		$this->json(array(
			'items'       => array_map(array($this, 'item_json'), $items),
			'page'        => $page,
			'total_pages' => max(1, (int) ceil($total / Media_model::PER_PAGE)),
		));
	}

	public function update($id = NULL)
	{
		$this->require_post();
		$media = $this->media_model->find($id);
		if ( ! $media)
		{
			show_404();
		}
		$this->media_model->update($media['id'], array('alt' => mb_substr(trim((string) $this->input->post('alt')), 0, 255)));
		$this->flash('success', 'Alt text disimpan.');
		redirect($this->back_url());
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$media = $this->media_model->find($id);
		if ( ! $media)
		{
			show_404();
		}
		$usage = $this->media_model->usage($media['id']);
		$this->media_model->delete($media['id']);
		$this->flash('success', basename($media['file']).' dihapus.'.($usage ? ' Gambar unggulan dilepas dari '.$usage.' post.' : ''));
		redirect($this->back_url());
	}

	protected function item_json(array $m)
	{
		$urls = Media_model::urls($m);

		return array(
			'id'       => (int) $m['id'],
			'name'     => basename($m['file']),
			'url'      => $urls['url'],
			'thumb'    => $urls['thumb'],
			'is_image' => strpos($m['mime_type'], 'image/') === 0,
			'width'    => (int) $m['width'],
			'height'   => (int) $m['height'],
			'alt'      => $m['alt'],
		);
	}

	/**
	 * Satukan $_FILES['files'] (multiple) dan $_FILES['file'] menjadi daftar file.
	 */
	protected function normalize_files()
	{
		$out = array();
		if (isset($_FILES['file']) && is_array($_FILES['file']) && ! is_array($_FILES['file']['name']))
		{
			$out[] = $_FILES['file'];
		}
		if (isset($_FILES['files']) && is_array($_FILES['files']['name']))
		{
			foreach ($_FILES['files']['name'] as $i => $name)
			{
				if ($_FILES['files']['error'][$i] === UPLOAD_ERR_NO_FILE)
				{
					continue;
				}
				$out[] = array(
					'name'     => $name,
					'type'     => $_FILES['files']['type'][$i],
					'tmp_name' => $_FILES['files']['tmp_name'][$i],
					'error'    => $_FILES['files']['error'][$i],
					'size'     => $_FILES['files']['size'][$i],
				);
			}
		}

		return $out;
	}

	protected function respond($ajax, $ok, $message)
	{
		if ($ajax)
		{
			return $this->json(array('ok' => $ok, 'error' => $message), $ok ? 200 : 422);
		}
		$this->flash($ok ? 'success' : 'error', $message);
		redirect('admin/media');
	}

	protected function json(array $data, $status = 200)
	{
		$this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($data));
	}

	protected function back_url()
	{
		$back = (string) $this->input->post('back');

		return (strpos($back, 'admin/') === 0) ? $back : 'admin/media';
	}
}
