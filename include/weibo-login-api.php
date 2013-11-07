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
			'weibo-login-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'weibo_login_settings' );
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( "Weibo Login", 'weibo-login' ); ?></h2>

			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'weibo_login' );
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
			'weibo-login',
			'weibo_login_settings',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'weibo_login_setting_general',
			__( 'Weibo Login General Settings', 'weibo-login' ),
			array( $this, 'print_section_info' ),
			'weibo-login-settings'
		);

		add_settings_field(
			'app_id',
			__( 'Weibo App ID', 'weibo-login' ),
			array( $this, 'render_general_setting' ),
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

		return $input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		echo _e( 'Enter your settings below:', 'weibo-login' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="app_id" name="weibo_login_settings[app_id]" value="%s" style="width: 300px;"/>',
			esc_attr( $this->options['app_id'] )
		);
	}
}
