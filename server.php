<?php
/**
 * Router untuk PHP built-in server (hanya untuk development).
 *
 *   php -S localhost:8000 server.php
 *
 * File statis yang ada (wp-content, wp-includes) dilayani langsung,
 * request lainnya diteruskan ke front controller CodeIgniter.
 */
$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if (strpos($path, '/stmi.ac.id-clone') === 0)
{
	http_response_code(403);
	return TRUE;
}

if ($path !== '/' && is_file(__DIR__.$path))
{
	return FALSE;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__.'/index.php';
