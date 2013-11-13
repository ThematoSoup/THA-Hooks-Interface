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

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$all_tha_hooks = tha_interface_all_hooks();
foreach ( $all_tha_hooks as $hooks_group => $hooks_group_values ) :
	// Delete all the options on plugin ununstall
	delete_option( 'tha_hooks_interface_' . $hooks_group );
endforeach;