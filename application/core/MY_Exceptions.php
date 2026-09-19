<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * show_404() di controller publik (turunan MY_Controller) menampilkan halaman 404 bergaya WordPress.
 * Panel admin, CLI, dan error sebelum controller dibuat tetap memakai halaman 404 bawaan CodeIgniter.
 */
class MY_Exceptions extends CI_Exceptions {

	public function show_404($page = '', $log_error = TRUE)
	{
		$CI = class_exists('CI_Controller', FALSE) ? CI_Controller::get_instance() : NULL;

		if ( ! is_cli() && $CI instanceof MY_Controller)
		{
			if ($log_error)
			{
				log_message('error', '404 Page Not Found: '.$page);
			}
			// Buang output yang mungkin sudah dibuat sebelumnya, lalu render halaman 404 situs.
			while (ob_get_level() > 1)
			{
				ob_end_clean();
			}
			$CI->render_not_found();
			$CI->output->_display();
			exit(4);
		}

		parent::show_404($page, $log_error);
	}
}
