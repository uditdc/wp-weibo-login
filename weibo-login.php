<?php
/*
Plugin Name: Weibo Login
Plugin URI: http://dev.desirews.com/
Description: Wordpress plugin to login with weibo
Version: 1.0
Author: Desire Web Solutions
Author URI: http://www.desirews.com/
License:

  Copyright 2013 Udit Virwani (udit.cp@desirews.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


require_once 'include/weibo-login-admin.php';
require_once 'sdk/saetv2.ex.class.php';
require_once 'include/weibo-login-settings.php';


class Weibo_Login {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	public function __construct() {

		// Set the plugin path
		$this->plugin_path = dirname( __FILE__ );
		// Set the plugin url
		$this->plugin_url = WP_PLUGIN_URL . DIRECTORY_SEPARATOR . plugin_basename( __DIR__ ) . DIRECTORY_SEPARATOR;

		// Load all the settings for Baidu Maps
		$this->settings = get_option( 'weibo_login_settings' );

		load_plugin_textdomain( 'weibo-login', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );

		// Callback Functions
		add_action('wp_ajax_weibo_login_authorized', array($this, 'weibo_login_authorized'));
		add_action('wp_ajax_weibo_login_failed', array($this, 'weibo_login_failed'));


		// Initialize the admin interface
		$admin = new Weibo_Login_Admin($this->plugin_url, $this->plugin_path);
		$settings = new Weibo_Login_Settings();

		// $this->client_id = '1686503323';
		// $this->client_secret = 'b20453508497ba25e482368aa35ff9df';
		
		$this->client_id = $this->settings['app_id'];
		$this->client_secret = $this->settings['app_secret'];

		$this->callback_url = admin_url("admin-ajax.php?action=weibo_login_authorized");

	}


	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	/**
	 * Upon activation, create and show the options_page with default options.
	 */
	public function activate() {

	}

	/**
	 * Upon deactivation, removes the options page.
	 */
	public function deactivate() {

	}

	public function register_admin_scripts(){

	}

	public function register_scripts(){

	}

	public function do_login($token){
		// Sample code to 
		$c = new SaeTClientV2( $client_id , $client_secret , $token['access_token'] );
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_message = $c->show_user_by_id($uid);

		var_dump($user_message);
	}

	public function weibo_login_authorized(){
		$o = new SaeTOAuthV2($this->client_id, $this->client_secret);

		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $this->callback_url;

			try{
				$token = $o->getAccessToken('code', $keys);
				var_dump($token);
			}catch(OAuthException $e){

			}
		}

		if ($token){
			$weibo_login = new Weibo_Login;
			$weibo_login->do_login($token);
		}else{
			echo 'no';
		}
		die('');
	}

	public function weibo_login_failed(){
		echo 'failed';
		die('');
	}

	public function display_admin_notices() {
		
		$sdk = new SaeTOAuthV2($this->client_id, $this->client_secret);
		$request_uri = $sdk->getAuthorizeURL($this->callback_url);

		$notice[] = "<div class='error'>";
		$notice[] = "<p><a href='" . $request_uri . "'>Login Here</a></p> </div>";

		echo implode( "\n", $notice );

	}

}
new Weibo_Login;