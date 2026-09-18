<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Social_links extends CI_Migration {

	public function up()
	{
		$this->db->insert_batch('contacts', array(
			array('key' => 'social_twitter', 'value' => 'https://twitter.com/stmijakarta?lang=en'),
			array('key' => 'social_instagram', 'value' => 'https://www.instagram.com/stmijakarta/?hl=en'),
			array('key' => 'social_facebook', 'value' => 'https://www.facebook.com/PoliteknikSTMIJakarta'),
			array('key' => 'social_youtube', 'value' => 'https://www.youtube.com/channel/UCFalakPYmXniFeqHapt1k8w'),
		));
	}

	public function down()
	{
		$this->db->where_in('key', array('social_twitter', 'social_instagram', 'social_facebook', 'social_youtube'))->delete('contacts');
	}
}
