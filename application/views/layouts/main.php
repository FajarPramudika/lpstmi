<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Susunan halaman. Jangan menambah HTML/whitespace di file ini:
 * output harus sama persis dengan clone WordPress.
 */
$this->load->view('layouts/partials/document_open');
echo $title;
$this->load->view('layouts/head/'.$head);
echo '<body'.$body_attrs.'>';
$this->load->view('layouts/partials/drawer');
$this->load->view('layouts/partials/header');
$this->load->view($view);
$this->load->view('layouts/partials/footer');
$this->load->view('layouts/foot/'.$foot);
