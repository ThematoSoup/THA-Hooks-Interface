<?php
/**
 * THA Hooks Interface.
 *
 * @package   THA_Hooks_Interface
 * @author    ThematoSoup <contact@thematosoup.com>
 * @license   GPL-2.0+
 * @link      http://thematosoup.com
 * @copyright 2013 ThematoSoup
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * @package THA_Hooks_Interface
 * @author  ThematoSoup <contact@thematosoup.com>
 */
class THA_Hooks_Interface {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'tha-hooks-interface';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		/* Add our code blocks to THA hooks.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		$all_tha_hooks = tha_interface_all_hooks();
		foreach ( $all_tha_hooks as $hooks_group => $hooks_group_values ) :
			$tha_interface_settings = get_option( 'tha_hooks_interface_' . $hooks_group );
			foreach ( $hooks_group_values['hooks'] as $hook_name => $hook_description ) :
				// Check if there's an action to add
				if ( isset( $tha_interface_settings[ $hook_name ]['output'] ) && '' != $tha_interface_settings[ $hook_name ]['output'] ) :
					add_action( $hook_name, array( $this, 'add_' . $hooks_group . '_' . $hook_name ), 10 );
				endif;
			endforeach;
		endforeach;

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	
	// WordPress
	public function add_WordPress_wp_head() {
		$this->tha_action( 'WordPress', 'wp_head' );
	}
	public function add_WordPress_wp_footer() {
		$this->tha_action( 'WordPress', 'wp_footer' );
	}

	// HTML
	public function add_html_tha_html_before() {
		$this->tha_action( 'html', 'tha_html_before' );
	}
	
	// <body>
	public function add_body_tha_body_top() {
		$this->tha_action( 'body', 'tha_body_top' );
	}
	public function add_body_tha_body_bottom() {
		$this->tha_action( 'body', 'tha_body_bottom' );
	}
	
	// <head>
	public function add_head_tha_head_top() {
		$this->tha_action( 'head', 'tha_head_top' );
	}
	public function add_head_tha_head_bottom() {
		$this->tha_action( 'head', 'tha_head_bottom' );
	}
	
	// Header
	public function add_header_tha_header_before() {
		$this->tha_action( 'header', 'tha_header_before' );
	}
	public function add_header_tha_header_after() {
		$this->tha_action( 'header', 'tha_header_after' );
	}
	public function add_header_tha_header_top() {
		$this->tha_action( 'header', 'tha_header_top' );
	}
	public function add_header_tha_header_bottom() {
		$this->tha_action( 'header', 'tha_header_bottom' );
	}
	
	// Content
	public function add_content_tha_content_before() {
		$this->tha_action( 'content', 'tha_content_before' );
	}
	public function add_content_tha_content_after() {
		$this->tha_action( 'content', 'tha_content_after' );
	}
	public function add_content_tha_content_top() {
		$this->tha_action( 'content', 'tha_content_top' );
	}
	public function add_content_tha_content_bottom() {
		$this->tha_action( 'content', 'tha_content_bottom' );
	}
	
	// Entry
	public function add_entry_tha_entry_before() {
		$this->tha_action( 'entry', 'tha_entry_before' );
	}
	public function add_entry_tha_entry_after() {
		$this->tha_action( 'entry', 'tha_entry_after' );
	}
	public function add_entry_tha_entry_top() {
		$this->tha_action( 'entry', 'tha_entry_top' );
	}
	public function add_entry_tha_entry_bottom() {
		$this->tha_action( 'entry', 'tha_entry_bottom' );
	}
	
	// Comments
	public function add_comments_tha_comments_before() {
		$this->tha_action( 'comments', 'tha_comments_before' );
	}
	public function add_comments_tha_comments_after() {
		$this->tha_action( 'comments', 'tha_comments_after' );
	}
	
	// Sidebar
	public function add_sidebar_tha_sidebars_before() {
		$this->tha_action( 'sidebar', 'tha_sidebars_before' );
	}
	public function add_sidebar_tha_sidebars_after() {
		$this->tha_action( 'sidebar', 'tha_sidebars_after' );
	}
	public function add_sidebar_tha_sidebar_top() {
		$this->tha_action( 'sidebar', 'tha_sidebar_top' );
	}
	public function add_sidebar_tha_sidebar_bottom() {
		$this->tha_action( 'sidebar', 'tha_sidebar_bottom' );
	}
	
	// Footer
	public function add_footer_tha_footer_before() {
		$this->tha_action( 'footer', 'tha_footer_before' );
	}
	public function add_footer_tha_footer_after() {
		$this->tha_action( 'footer', 'tha_footer_after' );
	}
	public function add_footer_tha_footer_top() {
		$this->tha_action( 'footer', 'tha_footer_top' );
	}
	public function add_footer_tha_footer_bottom() {
		$this->tha_action( 'footer', 'tha_footer_bottom' );
	}
	
	/**
	 * Adds an action to THA hook.
	 *
	 * @since    1.0.0
	 */
	public function tha_action( $hooks_group, $hook_name ) {

		$tha_interface_settings = get_option( 'tha_hooks_interface_' . $hooks_group );
		$tha_interface_setting = $tha_interface_settings[ $hook_name ];
		$tha_output = $tha_interface_setting['output'];
		
		if ( isset( $tha_interface_setting['php'] ) ) :
			ob_start();
			eval( '?>' . $tha_output );
			$tha_output = ob_get_contents();
			ob_end_clean();
			echo $tha_output;
		elseif ( isset( $tha_interface_setting['shortcode'] ) ) :
			echo do_shortcode( $tha_output );
		else :
			echo $tha_output;
		endif;

	}	

}