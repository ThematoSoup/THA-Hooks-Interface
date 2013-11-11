<?php
/**
 * Plugin Name.
 *
 * @package   THA_Hooks_Interface_Admin
 * @author    ThematoSoup <contact@thematosoup.com>
 * @license   GPL-2.0+
 * @link      http://thematosoup.com
 * @copyright 2013 ThematoSoup
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package THA_Hooks_Interface_Admin
 * @author  ThematoSoup <contact@thematosoup.com>
 */
class THA_Hooks_Interface_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = THA_Hooks_Interface::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_init', array( $this, 'plugin_options' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), THA_Hooks_Interface::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), THA_Hooks_Interface::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'THA Hooks Interface', $this->plugin_slug ),
			__( 'THA Hooks', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function plugin_options() {

		$all_tha_hooks = tha_interface_all_hooks();
		// Register a settings section for each hooks group
		foreach ( $all_tha_hooks as $tha_hooks_group => $tha_hooks_group_values ) :
		
			// First, we register a section. This is necessary since all future options must belong to one.  
			add_settings_section(  
				'tha_hooks_interface_section_' . $tha_hooks_group, // ID used to identify this section and with which to register options  
				$tha_hooks_group_values['name'],         // Title to be displayed on the administration page  
				'',              // Callback used to render the description of the section  
				'tha_hooks_interface_' . $tha_hooks_group         // Page on which to add this section of options  
			); 
	
			// For each hook in hooks group, add settings field
			foreach ( $tha_hooks_group_values['hooks'] as $hook_name => $hook_description ) :

				// Next, we will introduce the fields for toggling the visibility of content elements.
				add_settings_field(	
					$hook_name,				     		     // ID used to identify the field throughout the theme
					$hook_name,							     // The label to the left of the option interface element
					array( $this, 'field_cb' ),     	     // The name of the function responsible for rendering the option interface
					'tha_hooks_interface_' . $tha_hooks_group,	     // The page on which this option will be displayed
					'tha_hooks_interface_section_' . $tha_hooks_group, // The name of the section to which this field belongs
					array(								     // The array of arguments to pass to the callback. In this case, just a description.
						$tha_hooks_group,
						$hook_name,
						$hook_description
					)
				);

			endforeach;
	
			// Finally, we register the fields with WordPress  
			register_setting(  
			    'tha_hooks_interface_' . $tha_hooks_group,  
			    'tha_hooks_interface_' . $tha_hooks_group  
			); 		
		
		endforeach;
			    
	}
	

	public function field_cb( $args ) {
		$hooks_group = $args[0];
		$hook_name = $args[1];
		$hook_description = $args[2];
		$output_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][output]';
		$php_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][php]';
		$shortcode_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][shortcode]';
		$tha_interface_settings = get_option( 'tha_hooks_interface_' . $hooks_group );
		?>
		
		<p><?php echo $hook_description; ?></p>

		<p>
		<textarea style="font-family:monospace" rows="10" class="widefat" name="<?php echo $output_field_name; ?>" id="<?php echo $output_field_name; ?>"><?php echo htmlentities( $tha_interface_settings[ $hook_name ]['output'], ENT_QUOTES, 'UTF-8' ); ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $php_field_name; ?>">
			<input type="checkbox" name="<?php echo $php_field_name; ?>" id="<?php echo $php_field_name; ?>" value="1" <?php checked( $tha_interface_settings[ $hook_name ]['php'], 1 ); ?> />
			<?php _e( 'Execute PHP in this hook', $this->plugin_slug ); ?>
		</label>
		</p>

		<p>
		<label for="<?php echo $php_field_name; ?>">
			<input type="checkbox" name="<?php echo $shortcode_field_name; ?>" id="<?php echo $shortcode_field_name; ?>" value="1" <?php checked( $tha_interface_settings[ $hook_name ]['shortcode'], 1 ); ?> />
			<?php _e( 'Run shortcodes in this hook', $this->plugin_slug ); ?>
		</label>
		</p>
	<?php }

}