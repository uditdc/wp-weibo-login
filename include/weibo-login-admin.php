<?php

class Weibo_Login_Admin {

	public function __construct( $plugin_url, $request_url ) {
		$this->plugin_url = $plugin_url;
		$this->request_url = $request_url;
		$this->setup_hooks();
	}

	public function setup_hooks(){
		// Load jQuery on Login Pages
		add_action('login_form_login', array( $this, 'loadjQueryLibrary') );
		add_action('login_form_register', array( $this, 'loadjQueryLibrary') );


		// Modify the hooks on Login
		add_action('login_form', array($this, 'weibo_login_form_modification'));

		//Modfiy Profile Pages for Weibo Connect
	}

	function loadjQueryLibrary() {
		wp_enqueue_script('jquery');
	}

	public function weibo_login_form_modification() {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					(function($) {
						var loginForm = $('#loginform,#registerform,#front-login-form');
						var weiboLoginButton = $("<?php echo $this->newWeiboLoginButton(); ?>").css({
							'text-align' : 'center',
							'margin-bottom' : '20px'
						});
						if(loginForm.find('input').length > 0)
							loginForm.prepend("<h3 style='text-align:center;'>OR</h3>");
						loginForm.prepend(weiboLoginButton);
					}) (window.jQuery)
				});
			</script>
		<?php
	}

	public function newWeiboLoginButton(){
		$html = "<p><a href='" . $this->request_url . "'><img src='" . $this->plugin_url . '/images/login-btn.png' ."' ></a></p>";
		return $html;
	}
}
