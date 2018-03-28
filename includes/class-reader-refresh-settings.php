<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Reader_Refresh_Settings {

	/**
	 * The single instance of Reader_Refresh_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpt_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$icon_url = plugins_url( 'wordrpress-plugin-reader-refresh/assets/images/reader-refresh.png' );
		$page = add_menu_page( __( 'Reader Refresh', 'reader-refresh' ) , __( 'Reader Refresh', 'reader-refresh' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ), $icon_url );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	public function google_fonts() {

	}



	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		  // We're including the farbtastic script & styles here because they're needed for the colour picker
		  // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		  wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

			$query_args = array('family' => 'Montserrat:400,700');
			wp_register_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
			add_action('wp_enqueue_scripts', 'google_fonts');

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'reader-refresh' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['standard'] = array(
			'title'					=> __( 'General Settings', 'reader-refresh' ),
			'description'			=> __( 'General Plugin Settings', 'reader-refresh' ),
			'fields'				=> array(
				array(
					'id' 			=> 'enable_refresh',
					'label'			=> __( 'Enable Refresh', 'reader-refresh' ),
					'description'	=> __( 'Enable plugin', 'reader-refresh' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'continuous_refresh',
					'label'			=> __( 'Continuous Refresh', 'reader-refresh' ),
					'description'	=> __( '(Adds URL Parameter to redirect)', 'reader-refresh' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'user_disable_refresh',
					'label'			=> __( 'User Disable Refresh', 'reader-refresh' ),
					'description'	=> __( 'Allow User to cancel refresh (defaut: click to cancel refresh in %count%)', 'reader-refresh' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'delay',
					'label'			=> __( 'Delay', 'reader-refresh' ),
					'description'	=> __( 'Seconds of Delay', 'reader-refresh' ),
					'type'			=> 'number',
					'placeholder'	=> __( '30', 'reader-refresh' )
				),
				array(
					'id' 			=> 'random',
					'label'			=> __( 'Randomize Delay', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'min_refresh',
					'label'			=> __( 'Minimum Refresh Time', 'reader-refresh' ),
					'description'	=> __( 'Seconds of Delay', 'reader-refresh' ),
					'type'			=> 'text',
					'placeholder'	=> __( '30', 'reader-refresh' )
				),
				array(
					'id' 			=> 'max_refresh',
					'label'			=> __( 'Max Refresh Rime', 'reader-refresh' ),
					'description'	=> __( 'Seconds of Delay', 'reader-refresh' ),
					'type'			=> 'text',
					'placeholder'	=> __( '30000', 'reader-refresh' )
				),
				array(
					'id' 			=> 'triggers',
					'label'			=> __( 'Triggers for refresh', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'click' => 'On Click', 'scroll' => 'On Scroll', 'keypress' => 'On Keypress', 'touch' => 'On Touch' ),
					'default'		=>  array( 'click')
				),
				array(
					'id' 			=> 'redirect',
					'label'			=> __( 'Redirect to', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'radio',
					'options'		=> array( 'same' => 'Same URL', 'specific' => 'Specific URL', 'random' => 'Random Internal URL', 'list' => 'White List' ),
					'default'		=> 'same'
				),
				array(
					'id' 			=> 'specific_url',
					'label'			=> __( 'Specific URL', 'reader-refresh' ),
					'description'	=> __( 'Specific URL to redirect to (if that option is selected)', 'reader-refresh' ),
					'type'			=> 'text',
					'placeholder'	=> __( 'http://google.com', 'reader-refresh' )
				),
				array(
					'id' 			=> 'white_list',
					'label'			=> __( 'White List', 'reader-refresh' ),
					'description'	=> __( 'A  comma seperated list of URl\'s', 'reader-refresh' ),
					'type'			=> 'textarea',
					'placeholder'	=> __( 'http://google.com, http://youtube.com', 'reader-refresh' )
				),
			)
		);
		$settings['popup'] = array(
			'title'					=> __( 'Popup Styles', 'reader-refresh' ),
			'description'			=> __( 'These the styles for the pop up for the user to disable the plugin.', 'reader-refresh' ),
			'fields'				=> array(
				array(
					'id' 			=> 'pop_show_count',
					'label'			=> __( 'Show Countdown', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'pop_count',
					'label'			=> __( 'Countdown Time', 'reader-refresh' ),
					'description'	=> __( 'seconds Time before force refresh', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '8',
					'placeholder'	=> __( '8', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_title',
					'label'			=> __( 'Title', 'reader-refresh' ),
					'description'	=> __( 'Header text for the popup.', 'reader-refresh' ),
					'type'			=> 'text',
					'default'		=> 'Hello',
					'placeholder'	=> __( 'Hello', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_message',
					'label'			=> __( 'Message', 'reader-refresh' ),
					'description'	=> __( 'Body text for the popup.', 'reader-refresh' ),
					'type'			=> 'textarea',
					'default'		=> 'You are about to be redirected.',
					'placeholder'	=> __( 'You are about to be redirected.', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_cancel',
					'label'			=> __( 'Cancel', 'reader-refresh' ),
					'description'	=> __( 'Cancel button text for the popup.', 'reader-refresh' ),
					'type'			=> 'text',
					'default'		=> 'Cancel',
					'placeholder'	=> __( 'Cancel', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_continue',
					'label'			=> __( 'Continue', 'reader-refresh' ),
					'description'	=> __( 'Continue button text for the popup.', 'reader-refresh' ),
					'type'			=> 'text',
					'default'		=> 'Continue',
					'placeholder'	=> __( 'Continue', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_font',
					'label'			=> __( 'Font', 'reader-refresh' ),
					'description'	=> __( 'Cancel button text for the popup.', 'reader-refresh' ),
					'type'			=> 'text',
					'default'		=> 'arial',
					'placeholder'	=> __( 'Monserrat', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_mobile',
					'label'			=> __( 'Mobile Width', 'reader-refresh' ),
					'description'	=> __( '% Mobile width for the popup.', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '90',
					'placeholder'	=> __( '90', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_max_width',
					'label'			=> __( 'Max Width', 'reader-refresh' ),
					'description'	=> __( 'px max-width for the popup.', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '600',
					'placeholder'	=> __( '600', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_padding',
					'label'			=> __( 'Padding', 'reader-refresh' ),
					'description'	=> __( 'px padding around the popup.', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '20',
					'placeholder'	=> __( '20', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_radius',
					'label'			=> __( 'Border Radius', 'reader-refresh' ),
					'description'	=> __( 'px  Curve of the corners', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '15',
					'placeholder'	=> __( '15', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_border_width',
					'label'			=> __( 'Popup Border Width', 'reader-refresh' ),
					'description'	=> __( 'px', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '2',
					'placeholder'	=> __( '2', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_border_color',
					'label'			=> __( 'Popup Border Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#000000',
				),
				array(
					'id' 			=> 'pop_bg_color',
					'label'			=> __( 'Background Color', 'reader-refresh' ),
					'description'	=> __( '' ),
					'type'			=> 'color',
					'default'		=> '#FFFFFF'
				),
				array(
					'id' 			=> 'pop_color',
					'label'			=> __( 'Text Color', 'reader-refresh' ),
					'description'	=> __( '' ),
					'type'			=> 'color',
					'default'		=> '#000000'
				),
				array(
					'id' 			=> 'pop_button_bg_color',
					'label'			=> __( 'Button Background Color', 'reader-refresh' ),
					'description'	=> __( '' ),
					'type'			=> 'color',
					'default'		=> '#000000'
				),
				array(
					'id' 			=> 'pop_button_color',
					'label'			=> __( 'Button Text Color', 'reader-refresh' ),
					'description'	=> __( '' ),
					'type'			=> 'color',
					'default'		=> '#FFFFFF'
				),
				array(
					'id' 			=> 'pop_button_radius',
					'label'			=> __( 'Button Border Radius', 'reader-refresh' ),
					'description'	=> __( 'px  Curve of the corners', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '3',
					'placeholder'	=> __( '3', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_button_border_width',
					'label'			=> __( 'Button Border Width', 'reader-refresh' ),
					'description'	=> __( 'px', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '1',
					'placeholder'	=> __( '1', 'reader-refresh' )
				),
				array(
					'id' 			=> 'pop_button_border_color',
					'label'			=> __( 'Button Border Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#000000',
				),
				array(
					'id' 			=> 'pop_button_color',
					'label'			=> __( 'Button Text Color', 'reader-refresh' ),
					'description'	=> __( '' ),
					'type'			=> 'color',
					'default'		=> '#FFFFFF'
				),
				array(
					'id' 			=> 'pop_button_hover_bg_color',
					'label'			=> __( 'Button Border Hover Background Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#444444',
				),
				array(
					'id' 			=> 'pop_button_hover_color',
					'label'			=> __( 'Button Hover Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#dbdbdb',
				),
				array(
					'id' 			=> 'pop_button_hover_border_color',
					'label'			=> __( 'Button Hover Border Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#000000',
				),
				array(
					'id' 			=> 'overlay',
					'label'			=> __( 'Overlay Color', 'reader-refresh' ),
					'description'	=> __( '', 'reader-refresh' ),
					'type'			=> 'color',
					'default'		=> '#00000',
				),
				array(
					'id' 			=> 'overlay_opacity',
					'label'			=> __( 'Overlay Opacity', 'reader-refresh' ),
					'description'	=> __( '%', 'reader-refresh' ),
					'type'			=> 'number',
					'default'		=> '90',
					'placeholder'	=> __( '90', 'reader-refresh' )
				),


			)
		);
		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Reader Refresh Settings' , 'reader-refresh' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" class="' . esc_attr( $tab ) . '" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'reader-refresh' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Reader_Refresh_Settings Instance
	 *
	 * Ensures only one instance of Reader_Refresh_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Reader_Refresh()
	 * @return Main Reader_Refresh_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
