<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Halaman paket Download Manager (/download/<slug>) dan pengiriman file (?wpdmdl=<ID>).
 */
class Downloads extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('download_model', 'author_model', 'media_model'));
		$this->config->load('site');
	}

	/**
	 * /download/<slug>            : halaman paket
	 * /download/<slug>?wpdmdl=ID  : unduh file paket ID (juga dari link lama /download/<slug>/index<hash>.html?wpdmdl=ID)
	 */
	public function single($slug)
	{
		$wpdmdl = $this->input->get('wpdmdl');
		if ($wpdmdl !== NULL)
		{
			return $this->deliver($wpdmdl);
		}

		if ($this->uri->total_segments() > 2)
		{
			show_404();
		}

		$download = $this->download_model->find_published($slug);
		if ( ! $download)
		{
			show_404();
		}

		$author = $this->author_model->find($download['author_id']);
		$media = $download['featured_media_id'] ? $this->media_model->find($download['featured_media_id']) : NULL;
		$featured = $media ? wpdm_featured_image($media) : '';
		$description = wp_content($download['description']);

		$this->render(array(
			'view'                   => 'downloads/single',
			'title'                  => wp_document_title(array($download['title'], $this->config->item('site_title'))),
			'body_attrs'             => ' class="wp-singular wpdmpro-template-default single single-wpdmpro postid-'.$download['id']
				.' wp-custom-logo wp-embed-responsive wp-theme-blocksy elementor-default elementor-kit-9 ct-elementor-default-template"'
				.' data-link="type-2" data-prefix="wpdmpro_single" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/Blog"',
			'head'                   => 'download',
			'foot'                   => (strpos($description, 'class="pdfemb-viewer"') !== FALSE) ? 'download-pdf' : 'download',
			// Gambar unggulan (jika ada) mendapat fetchpriority; tanpa itu logo header yang mendapatkannya.
			'img_hints'              => ($featured ? array() : array('header:0' => 'fetchpriority="high" ', 'header:2' => 'fetchpriority="high" '))
				+ array('footer:0' => 'loading="lazy" '),
			'footer_logo_post_image' => FALSE,
		), array(
			'download'    => $download,
			'author'      => $author,
			'featured'    => $featured,
			'description' => $description,
			// Parameter anti-cache WPDM (uniqid + time), seperti di WordPress; tidak dipakai saat unduh.
			'refresh'     => uniqid().time(),
		));
	}

	/**
	 * Tambah hitungan lalu kirim file lokal, atau arahkan ke URL luar (mis. Google Drive).
	 */
	protected function deliver($id)
	{
		$download = ctype_digit((string) $id) ? $this->download_model->find_published_id($id) : NULL;
		if ( ! $download)
		{
			show_404();
		}

		if (Download_model::is_external($download))
		{
			$this->download_model->increment($download['id']);
			header('Location: '.$download['file'], TRUE, 302);
			exit;
		}

		$path = Download_model::local_path($download);
		if ($path === NULL)
		{
			log_message('error', 'File paket download '.$download['id'].' tidak ditemukan: '.$download['file']);
			show_404();
		}

		$this->download_model->increment($download['id']);

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $path) ?: 'application/octet-stream';
		finfo_close($finfo);
		$name = basename($path);

		while (ob_get_level() > 0)
		{
			ob_end_clean();
		}
		header('Content-Type: '.$mime);
		header('Content-Length: '.filesize($path));
		header('Content-Disposition: attachment; filename="'.str_replace('"', '', $name).'"; filename*=UTF-8\'\''.rawurlencode($name));
		header('X-Content-Type-Options: nosniff');
		header('Cache-Control: private, no-store');
		readfile($path);
		exit;
	}
}
