<?php


class Weibo_Login_API {

	public function __construct( $token, $sdk ) {
		$this->token = $token;
		$this->sdk = $sdk;
	}

	public function do_login(){
		// Sample code to 
		$c = new SaeTClientV2( $this->client_id , $this->client_secret , $this->token['access_token'] );
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user = $c->show_user_by_id($uid); // $user = $c->account_profile_basic($uid); (After Authentication)
		$user['email'] = 'it01@konaxis.com'; // Temp till authentication		
		// var_dump($user);

		// Check if Weibo ID Exists in database.
		$user_id = $this->get_user_id_from_weibo_id($uid);
		if($user_id) $this->login_user($user_id);
		elseif(email_exists($user['email'])){
			$user_info = get_user_by('email', $user['email']);
			$this->insert_weibo_user($user_info->ID, $uid);
			$this->login_user($user_info->ID);
		}else{
			$user_id = $this->register_webio_user($uid, $user);
		}


		// $email = $user['email'];
		// $email = 'udit.cp@gmail.com';
		// echo $email;

		/**
		*		@TODO - If Email doesn not exist, input the email form the user.
		*/

		/**
		*	@TODO - Create the actual login function with email.
		*/
	}

	public function get_token_info(){
		$params = array();
		$params['access_token'] = $this->token['access_token'];

		$url = "https://api.weibo.com/oauth2/get_token_info";
		$method = "POST";

		$headers = array();
		$body = http_build_query($params);

		return $this->sdk->http($url, $method, $body, $headers);
	}

	private function login_user($user_id){
		// Check if user is logged in 
		$user_info = get_userdata($user_id);

		
		$secure_cookie = is_ssl();
		$secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
		global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie

		$auth_secure_cookie = $secure_cookie;
		wp_set_auth_cookie($user_id, true, $secure_cookie);
		do_action('wp_login', $user_info->user_login, $user_info);


		wp_redirect(site_url());
	}

	private function get_user_id_from_weibo_id($uid){
		global $wpdb;
		$table = $wpdb->prefix . 'weibo_login';

		$user = $wpdb->get_row("SELECT ID FROM wp_weibo_login WHERE weibo_id = '$uid'");
		return $user->ID;
	}

	private function register_webio_user($uid, $user){

		$userdata = array(
			'user_login'    =>  $user['name'],
			'display_name' 	=>  $user['screen_name'],
			'user_email' 		=>	$user['email'],
		);

		$user_id = wp_insert_user( $userdata );

		if( !is_wp_error($user_id) ) {
			$this->insert_weibo_user($user_id, $uid);
			$this->login_user($user_id);
		} 
	}

	private function insert_weibo_user($user_id, $weibo_id){
		global $wpdb;

		$result = $wpdb->insert($wpdb->prefix . 'weibo_login', array(
		  'ID' => $user_id,
		  'weibo_id' => $weibo_id,
		) , array(
		  '%d',
		  '%s',
		));

		return $result;
	}
}