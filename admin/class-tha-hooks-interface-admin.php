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
		add_action( 'admin_notices', array( $this, 'add_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'dismiss_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_pointer' ) );
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
		
			// Check if theme declares support for this section
			if ( current_theme_supports( 'tha_hooks', $tha_hooks_group ) || 'WordPress' == $tha_hooks_group ) :
			
				// First, we register a section. This is necessary since all future options must belong to one.  
				add_settings_section(  
					'tha_hooks_interface_section_' . $tha_hooks_group,
					'',
					'',
					'tha_hooks_interface_' . $tha_hooks_group
				); 
		
				// For each hook in hooks group, add settings field
				foreach ( $tha_hooks_group_values['hooks'] as $hook_name => $hook_description ) :
	
					// Next, we will introduce the fields for toggling the visibility of content elements.
					add_settings_field(	
						$hook_name,
						$hook_name,
						array( $this, 'field_cb' ),
						'tha_hooks_interface_' . $tha_hooks_group,
						'tha_hooks_interface_section_' . $tha_hooks_group,
						array(
							$tha_hooks_group,
							$hook_name,
							$hook_description
						)
					);
	
				endforeach;
		
				// Finally, we register the fields with WordPress  
				register_setting(  
				    'tha_hooks_interface_' . $tha_hooks_group,  
				    'tha_hooks_interface_' . $tha_hooks_group,
				    array( $this, 'sanitize_field' )
				); 
			
			endif; // Theme support check		
		
		endforeach;
			    
	}
	

	/**
	 * Adds admin notice to plugin settings screen.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_notice() {

		global $pagenow;
		$page_filter = ( isset( $_GET['page'] ) ? $_GET['page'] : false );

		// Don't do anything if not THA Interface settings page
		if ( isset( $page_filter ) ) :
			global $current_user;
			$userid = $current_user->ID;
			$tha_current_theme = wp_get_theme();
			$tha_support = false;
			$tha_support_string = '';
			
			if ( current_theme_supports( 'tha_hooks', 'html' ) ) :
				$tha_support_string .= '<li>html</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'body' ) ) :
				$tha_support_string .= '<li>body</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'head' ) ) :
				$tha_support_string .= '<li>head</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'header' ) ) :
				$tha_support_string .= '<li>header</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'content' ) ) :
				$tha_support_string .= '<li>content</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'entry' ) ) :
				$tha_support_string .= '<li>entry</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'comments' ) ) :
				$tha_support_string .= '<li>comments</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'sidebar' ) ) :
				$tha_support_string .= '<li>sidebar</li>';
				$tha_support = true;
			endif;

			if ( current_theme_supports( 'tha_hooks', 'sidebar' ) ) :
				$tha_support_string .= '<li>footer</li>';
				$tha_support = true;
			endif;
			
			// This notice will only be shown in THA Hooks Interface
			// settings page, if current user hasn't disabled it.
			if ( 'options-general.php' == $pagenow && $this->plugin_slug == $page_filter && ! get_user_meta( $userid, 'ignore_tha_hooks_interface_notice' ) ) :
				echo '<div class="updated">';
					if ( $tha_support ) : 
						echo '<p>Your current theme, ' . $tha_current_theme->Name . ' declares support for following <a href="https://github.com/zamoose/themehookalliance">Theme Hook Alliance</a> hooks:</p>';
						echo '<ul style="list-style:circle;margin-left:2em;">';
							echo $tha_support_string;
						echo '</ul>';
						echo '<p>You can also use this plugin to hook into wp_head and wp_footer hooks which are supported by most themes.</p>';
					else : 
						echo '<p>Your current theme, ' . $tha_current_theme->Name . ' does not declare support for <a href="https://github.com/zamoose/themehookalliance">Theme Hook Alliance</a> hooks, but you can still use this plugin to hook into wp_head and wp_footer hooks which are supported by most themes.</p>';
					endif;
					echo '<p><a href="?page=tha-hooks-interface&dismiss_tha_interface_notice=yes">Dismiss this message</a>.</p>';
				echo '</div>';
			endif;
		endif;
		
	}
	

	/**
	 * Dismis admin notice.
	 *
	 * @since    1.0.0
	 */
	public function dismiss_admin_notice() {

		global $current_user;
		$userid = $current_user->ID;
		
		// If "Dismiss" link has been clicked, user meta field is added
		if ( isset( $_GET['dismiss_tha_interface_notice'] ) && 'yes' == $_GET['dismiss_tha_interface_notice'] ) :
			add_user_meta( $userid, 'ignore_tha_hooks_interface_notice', 'yes', true );
		endif;

	}


    /**
     * Sticky Header admin pointer
     *
     * @since    1.0.0
     */
    public function admin_pointer() {
            // Assume pointer shouldn't be shown
            $enqueue_pointer_script_style = false;
    
            // First check if current user can edit theme options
            if ( user_can( get_current_user_id(), 'manage_options' ) ) :
                    // Get array list of dismissed pointers for current user and convert it to array
                    $dismissed_pointers = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
            
                    // Check if our pointer is not among dismissed ones
                    if( ! in_array( 'tha_hooks_interface_pointer', $dismissed_pointers ) ) :
                            $enqueue_pointer_script_style = true;
                            // Add footer scripts using callback function
                            add_action( 'admin_print_footer_scripts', array( $this, 'print_admin_pointer_scripts' ) );
                    endif;
            
                    // Enqueue pointer CSS and JS files, if needed
                    if( $enqueue_pointer_script_style ) :
                            wp_enqueue_style( 'wp-pointer' );
                            wp_enqueue_script( 'wp-pointer' );
                    endif;
            endif;
    }

    /**
     * Print Sticky Header admin pointer scripts
     *
     * @since    1.0.0
     */
    public function print_admin_pointer_scripts() {
        $pointer_content  = '<h3>' . __( 'THA Hooks Interface', $this->plugin_slug ) . '</h3>';
        $pointer_content .= sprintf(
                '<p>Thank you for installing THA Hooks Interface plugin! You can reach its settings page at Settings > THA Hooks screen. If you have any questions about it please use our <a href="%1$s" target="_blank">dedicated support forum</a>.</p><p>For any suggestions on how to make the plugin better, you can get in touch with us on <a href="%2$s" target="_blank">Twitter</a>.</p><p>If you find our plugin useful, please consider <a href="%3$s" target="_blank">rating it at WordPress.org</a> or subscribing to our <a href="%4$s" target="_blank">mailing list</a>.</p>',
                'http://support.thematosoup.com',
                'http://twitter.com/ThematoSoup',
                'http://wordpress.org/plugins/sticky-header/',
                'http://thematosoup.com/mailing-list/'
        );
        ?>
        
        <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            $('#menu-settings').pointer({
                content:	'<?php echo $pointer_content; ?>',
                position:	{
								edge: 'left', // arrow direction
								align: 'center' // vertical alignment
							},
                pointerWidth:	400,
                close:			function() {
									$.post( ajaxurl, {
										pointer: 'tha_hooks_interface_pointer', // pointer ID
										action: 'dismiss-wp-pointer'
									});
								}
            }).pointer('open');
        });
        //]]>
        </script>        
    <?php }
        
        
	/**
	 * Callback function for settings fields for each hook.
	 *
	 * @since    1.0.0
	 */
	public function field_cb( $args ) {
		$hooks_group = $args[0];
		$hook_name = $args[1];
		$hook_description = $args[2];
		
		$output_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][output]';
		$php_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][php]';
		$shortcode_field_name = 'tha_hooks_interface_' . $hooks_group . '[' . $hook_name . '][shortcode]';
		$tha_interface_settings = get_option( 'tha_hooks_interface_' . $hooks_group );

		$shortcode_checked = isset( $tha_interface_settings[ $hook_name ]['shortcode'] ) ? $tha_interface_settings[ $hook_name ]['shortcode'] : '';
		$php_checked = isset( $tha_interface_settings[ $hook_name ]['php'] ) ? $tha_interface_settings[ $hook_name ]['php'] : '';
		?>
		
		<p><?php echo $hook_description; ?></p>

		<p>
		<textarea style="font-family:monospace" rows="10" class="widefat" name="<?php echo $output_field_name; ?>" id="<?php echo $output_field_name; ?>"><?php echo htmlentities( $tha_interface_settings[ $hook_name ]['output'], ENT_QUOTES, 'UTF-8' ); ?></textarea>
		</p>
		
		<?php if ( current_user_can( 'unfiltered_html' ) ) : ?>
		<p>
		<label for="<?php echo $php_field_name; ?>">
			<input type="checkbox" name="<?php echo $php_field_name; ?>" id="<?php echo $php_field_name; ?>" value="1" <?php checked( $php_checked, 1 ); ?> />
			<?php _e( 'Execute PHP in this hook (must be enclodes in opening and closing PHP tags)', $this->plugin_slug ); ?>
		</label>
		</p>
		<?php endif; ?>

		<p>
		<label for="<?php echo $shortcode_field_name; ?>">
			<input type="checkbox" name="<?php echo $shortcode_field_name; ?>" id="<?php echo $shortcode_field_name; ?>" value="1" <?php checked( $shortcode_checked, 1 ); ?> />
			<?php _e( 'Run shortcodes in this hook', $this->plugin_slug ); ?>
		</label>
		</p>
	<?php }
	
	
	/**
	 * Sanitize the field, filter out HTML if a user can't post HTML markup.
	 *
	 * @since    1.0.0
	 */
	public function sanitize_field( $field ) {
		
		if ( ( current_user_can( 'unfiltered_html' ) ) ) :
			return $field;
		else :
			return stripslashes( wp_filter_post_kses( $field ) );
		endif;
		
	}

}