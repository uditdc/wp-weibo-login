<?php

class Weibo_Login_Settings {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );

		$this->callback_url = site_url('wp-login.php') . '?weibo_login=1';
		$this->options = get_option( 'weibo_login_settings' );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_submenu_page(
			'options-general.php',
			__( 'Weibo Login Settings', 'weibo-login' ),
			__( 'Weibo Login Settings', 'weibo-login' ),
			'manage_options',
			'weibo-login-settings',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( "Weibo Login Settings", 'weibo-login' ); ?></h2>

			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'weibo_login_settings' );
				do_settings_sections( 'weibo-login-settings' );
				submit_button();
				?>
			</form>
		</div>
	<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'weibo_login_settings',
			'weibo_login_settings',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'weibo_login_setting_general',
			__( 'Weibo Login Settings', 'weibo-login' ),
			array( $this, 'print_section_info' ),
			'weibo-login-settings'
		);

		add_settings_field(
			'app_id',
			__( 'Weibo App ID', 'weibo-login' ),
			array( $this, 'render_general_settings_appid' ),
			'weibo-login-settings',
			'weibo_login_setting_general'
		);

		add_settings_field(
			'app_secret',
			__( 'Weibo App Secret', 'weibo-login' ),
			array( $this, 'render_general_settings_appsecret' ),
			'weibo-login-settings',
			'weibo_login_setting_general'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		if ( ! empty( $input['app_id'] ) )
			$input['app_id'] = sanitize_text_field( $input['app_id'] );
		elseif ( ! empty( $input['app_secret'] ) )
			$input['app_secret'] = sanitize_text_field( $input['app_secret'] );

		return $input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		$html[] = "<p>This plugins helps you create Facebook login and register buttons. The login and register process only takes one click and you can fully customize the buttons with images and other assets.</p>";
		$html[] = "<h3>Setup the weibo application</h3>";
		$html[] = "<ol>";
		$html[] = "<li><a href='#'>Create a weibo application</a></li>";
		$html[] = "<li>Register as a weibo developer</li>";
		$html[] = "<li>Select the application to be a 'Web Application' (3rd icon from the left)</li>";
		$html[] = "<li>Fill the application details relating to your website</li>";
		$html[] = "<li>Once the application is created, switch to the advanced settings <i>(<a href='#'>click here for instructions)</a></i></li>";
		$html[] = "<li>Enter the OAuth callback URLs as below";
		$html[] = "<br>";
		$html[] = "Authorization Successfull 	: <code>" . $this->callback_url . "</code>";
		$html[] = "<br>";
		$html[] = "Authorization Failed 			: <code>" . $this->callback_url . "</code>";
		$html[] = "<li><strong>Save the settings, and you are ready to go!</strong></li>";
		$html[] = "</ol>";
		$html[] = "<br>";
		$html[] = "<h3>Embedding in themes</h3>";
		$html[] = "<p>The code below can be used to display a link/button in your theme</p>";
		$html[] = "Button Link 	: <code><a href='" .  $this->callback_url . "'>" .  $this->callback_url . "</a></code>";
		$html[] = "<br>";
		$html[] = "Normal Link	: <code>" . $this->callback_url . "</code>";
		$html[] = "<hr>";
		$html[] = "<br>";
		$html[] = "<h3>Enter your Weibo Application Credientials</h3>";

		echo implode("\n", $html);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function render_general_settings_appid() {
		printf(
			'<input type="text" id="app_id" name="weibo_login_settings[app_id]" value="%s" style="width: 300px;"/>',
			esc_attr( $this->options['app_id'] )
		);
	}

	public function render_general_settings_appsecret() {
		printf(
			'<input type="text" id="app_secret" name="weibo_login_settings[app_secret]" value="%s" style="width: 300px;"/>',
			esc_attr( $this->options['app_secret'] )
		);
	}
}

new Weibo_Login_Settings();
