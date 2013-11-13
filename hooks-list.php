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
* Helper function, list all THA Hooks.
*
* @since    1.0.0
*/
function tha_interface_all_hooks() {

	$plugin_slug = 'tha-hooks-interface';

	$tha_hooks = array(
		'WordPress' => array(
			'name' => __( 'WordPress', $plugin_slug ),
			'hooks' => array(
				'wp_head' => __( 'Inside &lt;head&gt; element', $plugin_slug ),
				'wp_footer' => __( 'Immediately before closing &lt;body&gt; tag', $plugin_slug ),
			)
		),
		'html' => array(
			'name' => __( 'HTML', $plugin_slug ),
			'hooks' => array(
				'tha_html_before' => __( 'Before opening &lt;html&gt; tag', $plugin_slug ),
			)
		),
		'body' => array(
			'name' => __( 'Body', $plugin_slug ),
			'hooks' => array(
				'tha_body_top' => __( 'After opening &lt;body&gt; tag', $plugin_slug ),
				'tha_body_bottom' => __( 'Before closing &lt;/body&gt; tag', $plugin_slug ),
			)
		),
		'head' => array(
			'name' => __( 'Head', $plugin_slug ),
			'hooks' => array(
				'tha_head_top' => __( 'After opening &lt;head&gt; tag', $plugin_slug ),
				'tha_head_bottom' => __( 'Before closing &lt;/head&gt; tag', $plugin_slug ),
			)
		),
		'header' => array(
			'name' => __( 'Header', $plugin_slug ),
			'hooks' => array(
				'tha_header_before' => __( 'Before theme header', $plugin_slug ),
				'tha_header_after' => __( 'After theme header', $plugin_slug ),
				'tha_header_top' => __( 'After opening theme header', $plugin_slug ),
				'tha_header_bottom' => __( 'Before closing theme header', $plugin_slug ),
			)
		),
		'content' => array(
			'name' => __( 'Content', $plugin_slug ),
			'hooks' => array(
				'tha_content_before' => __( 'Before theme content area', $plugin_slug ),
				'tha_content_after' => __( 'After theme content area', $plugin_slug ),
				'tha_content_top' => __( 'After opening theme content area', $plugin_slug ),
				'tha_content_bottom' => __( 'Before closing theme content area', $plugin_slug ),
			)
		),
		'entry' => array(
			'name' => __( 'Entry', $plugin_slug ),
			'hooks' => array(
				'tha_entry_before' => __( 'Before post entry', $plugin_slug ),
				'tha_entry_after' => __( 'After post entry', $plugin_slug ),
				'tha_entry_top' => __( 'Top of post entry', $plugin_slug ),
				'tha_entry_bottom' => __( 'Bottom of post entry', $plugin_slug ),
			)
		),
		'comments' => array(
			'name' => __( 'Comments', $plugin_slug ),
			'hooks' => array(
				'tha_comments_before' => __( 'Before comments', $plugin_slug ),
				'tha_comments_after' => __( 'After comments', $plugin_slug ),
			)
		),
		'sidebar' => array(
			'name' => __( 'Sidebars', $plugin_slug ),
			'hooks' => array(
				'tha_sidebars_before' => __( 'Before sidebars', $plugin_slug ),
				'tha_sidebars_after' => __( 'After sidebars', $plugin_slug ),
				'tha_sidebar_top' => __( 'At the top of each sidebar', $plugin_slug ),
				'tha_sidebar_bottom' => __( 'At the bottom of each sidebar', $plugin_slug ),
			)
		),
		'footer' => array(
			'name' => __( 'Footer', $plugin_slug ),
			'hooks' => array(
				'tha_footer_before' => __( 'Before theme footer', $plugin_slug ),
				'tha_footer_after' => __( 'After theme footer', $plugin_slug ),
				'tha_footer_top' => __( 'After opening theme footer', $plugin_slug ),
				'tha_footer_bottom' => __( 'Before closing theme footer', $plugin_slug ),
			)
		),
	);
	
	return $tha_hooks;

}