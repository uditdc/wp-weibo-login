<?php

class Weibo_Login_Admin {

	public function __construct( $plugin_url, $plugin_path ) {

		$this->plugin_url = $plugin_url;
		$this->plugin_path = $plugin_path;
		$this->callback_url = admin_url("admin-ajax.php?action=weibo_login_authorized");

		add_action('login_form', array($this, 'weibo_login_form_modification'));
	}



	public function weibo_login_form_modification() {
		echo "<p><a href='" . $this->callback_url . "'>Login Here</a></p>";
	}
}