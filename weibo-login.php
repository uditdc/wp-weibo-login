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


require_once 'sdk/saetv2.ex.class.php';
require_once 'include/weibo-login-api.php';
require_once 'include/weibo-login-admin.php';
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
		// Load Textdomain
		load_plugin_textdomain( 'weibo-login', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		// Register Activation Function
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		
		// Load all the settings for Weibo Login
		$this->settings = get_option( 'weibo_login_settings' );
		
		$this->client_id = $this->settings['app_id'];
		$this->client_secret = $this->settings['app_secret'];
		$this->callback_url = site_url('wp-login.php') . '?weibo_login=1';

		$this->sdk = new SaeTOAuthV2($this->client_id, $this->client_secret);
		$this->request_url = $this->sdk->getAuthorizeURL($this->callback_url);

		// Initialize the admin interface
		$this->admin = new Weibo_Login_Admin($this->plugin_url, $this->request_url);


		// Setup Hooks
		$this->setup_hooks();
	}


	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	/**
	 * Upon activation, create and show the options_page with default options.
	 */
	public function activate() {
		global $wpdb;
		$weibo_login = $wpdb->prefix . "weibo_login";
		
		
			//include the wordpress db functions
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	 
		//check if there are any tables of that name already
		if($wpdb->get_var("show tables like '$weibo_login'") !== $weibo_login) 
		{
			//create your sql
			$sql =  "CREATE TABLE ". $weibo_login . " (
						  ID int(11) NOT NULL, 
						  type varchar(20) NOT NULL,
						  userid varchar(100) NOT NULL,
						  PRIMARY KEY ID (ID));";
			dbDelta($sql);

		}

		if (!isset($wpdb->weibo_login))
		{
			$wpdb->weibo_login = $weibo_login; 
			$wpdb->tables[] = str_replace($wpdb->prefix, '', $weibo_login); 
		}
		
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


	private function setup_hooks(){
		// Enqueue Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// Callback Functions
		add_action('login_init', array($this, 'weibo_login_authorized'));
		// add_action('wp_ajax_nopriv_weibo_login_authorized', array($this, 'weibo_login_authorized'));
		// add_action('wp_ajax_weibo_login_failed', array($this, 'weibo_login_failed'));
		// add_action('wp_ajax_nopriv_weibo_login_failed', array($this, 'weibo_login_failed'));
	}


	

	public function weibo_login_authorized(){

		if (isset($_REQUEST['code']) && $_REQUEST['weibo_login'] == 1) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $this->callback_url;

			try{
				$token = $this->sdk->getAccessToken('code', $keys);

			}catch(OAuthException $e){

			}
		}

		if ($token){
			$weibo_login = new Weibo_Login_API($token, $this->sdk);
			$weibo_login->do_login();
		}
	}

	public function weibo_login_failed(){
		echo 'failed';
		die('');
	}

}


// Call the plugin!
new Weibo_Login;

