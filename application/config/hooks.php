<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/

// Header keamanan untuk semua respons (lihat application/hooks/Security_headers.php).
$hook['pre_controller'][] = array(
	'class'    => 'Security_headers',
	'function' => 'send',
	'filename' => 'Security_headers.php',
	'filepath' => 'hooks',
);
