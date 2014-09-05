<?php
/**
 * Based on The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   THA_Hooks_Interface
 * @author    ThematoSoup <contact@thematosoup.com>
 * @license   GPL-2.0+
 * @link      http://thematosoup.com
 * @copyright 2013 ThematoSoup
 *
 * @wordpress-plugin
 * Plugin Name:       THA Hooks Interface
 * Plugin URI:        http://wordpress.org/plugins/tha-hooks-interface/
 * Description:       Allows you to hook into Theme Hook Alliance hooks using a simple interface. Also works with standard WordPress hooks wp_head and wp_footer.
 * Version:           1.1
 * Author:            ThematoSoup
 * Author URI:        http://thematosoup.com
 * Text Domain:       tha-hooks-interface
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/ThematoSoup/THA-Hooks-Interface
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . '/public/class-tha-hooks-interface.php' );
require_once( plugin_dir_path( __FILE__ ) . '/hooks-list.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
register_activation_hook( __FILE__, array( 'THA_Hooks_Interface', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'THA_Hooks_Interface', 'deactivate' ) );

/*
 * TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'THA_Hooks_Interface', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . '/admin/class-tha-hooks-interface-admin.php' );
	add_action( 'plugins_loaded', array( 'THA_Hooks_Interface_Admin', 'get_instance' ) );

}