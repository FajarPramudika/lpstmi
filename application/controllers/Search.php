<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pencarian: halaman hasil (/page/N?s=, /search/<kata>) dan endpoint live search Blocksy (wp-json/wp/v2/search).
 * Halaman hasil untuk /?s= ditangani Pages::index; semuanya memakai MY_Controller::render_search().
 */
class Search extends MY_Controller {

	/**
	 * /page/N?s=<kata>
	 */
	public function index($page = 1)
	{
		if ($this->input->get('s') === NULL)
		{
			return $this->render_not_found();
		}
		$this->render_search((string) $this->input->get('s'), $page);
	}

	/**
	 * /search/<kata>[/page/N] — format search_url Blocksy (tautan "Show more" live search).
	 */
	public function term($query = '', $page = 1)
	{
		$this->render_search(urldecode($query), $page);
	}

	/**
	 * Pengganti REST API WordPress wp/v2/search untuk live search Blocksy: hanya post & halaman (seperti di WordPress,
	 * paket download tidak ikut), urutan sama dengan halaman hasil. Header X-WP-Total dipakai tombol "Show more".
	 */
	public function rest()
	{
		$this->load->model(array('search_model', 'media_model'));
		$query = trim((string) $this->input->get('search'));
		$per_page = min(100, max(1, (int) ($this->input->get('per_page') ?: 10)));
		$page = max(1, (int) ($this->input->get('page') ?: 1));

		// Endpoint publik tanpa autentikasi dan paling mahal di situs: batasi halaman & laju dulu.
		if ($page > Search_model::MAX_PAGE)
		{
			return $this->rest_error(400, 'rest_invalid_param', 'Parameter page di luar batas.');
		}
		if ( ! $this->search_allowed())
		{
			return $this->rest_error(429, 'too_many_requests', 'Terlalu banyak permintaan pencarian.');
		}

		list($rows, $total) = $this->search_model->search($query, $page, $per_page, array('post', 'page'));

		$out = array();
		foreach ($rows as $r)
		{
			$media = $r['featured_media_id'] ? $this->media_model->find($r['featured_media_id']) : NULL;
			$out[] = array(
				'id'                => (int) $r['id'],
				'title'             => wp_texturize($r['title']),
				'url'               => site_url($r['kind'] === 'page' ? ($r['slug'] === 'home' ? '' : $r['slug']) : $r['slug']),
				'type'              => 'post',
				'subtype'           => $r['kind'],
				'ct_featured_media' => $media ? $this->media_json($media) : NULL,
			);
		}

		$this->output
			->set_content_type('application/json', 'UTF-8')
			->set_header('X-WP-Total: '.$total)
			->set_header('X-WP-TotalPages: '.(int) ceil($total / $per_page))
			->set_output(json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Galat bergaya REST API WordPress (live search Blocksy hanya membaca status HTTP-nya).
	 */
	protected function rest_error($status, $code, $message)
	{
		$this->output
			->set_status_header($status)
			->set_content_type('application/json', 'UTF-8')
			->set_output(json_encode(array('code' => $code, 'message' => $message, 'data' => array('status' => $status))));

		if ($status === 429)
		{
			$this->output->set_header('Retry-After: '.$this->rate_limit->retry_after());
		}
	}

	/**
	 * Bagian data media yang dipakai JS live search: media_details.sizes.<ukuran>.source_url.
	 */
	protected function media_json(array $media)
	{
		$dir = dirname($media['file']);
		$sizes = array();
		foreach ((array) json_decode($media['sizes'], TRUE) as $name => $size)
		{
			$sizes[$name] = array(
				'file'       => $size['file'],
				'width'      => (int) $size['width'],
				'height'     => (int) $size['height'],
				'source_url' => base_url('wp-content/uploads/'.$dir.'/'.$size['file']),
			);
		}
		$sizes['full'] = array(
			'file'       => basename($media['file']),
			'width'      => (int) $media['width'],
			'height'     => (int) $media['height'],
			'source_url' => base_url('wp-content/uploads/'.$media['file']),
		);

		return array(
			'id'            => (int) $media['id'],
			'alt_text'      => $media['alt'],
			'media_type'    => 'image',
			'mime_type'     => $media['mime_type'],
			'source_url'    => base_url('wp-content/uploads/'.$media['file']),
			'media_details' => array('width' => (int) $media['width'], 'height' => (int) $media['height'], 'sizes' => $sizes),
		);
	}
}
