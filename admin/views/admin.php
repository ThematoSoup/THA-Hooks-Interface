<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   THA_Hooks_Interface
 * @author    ThematoSoup <contact@thematosoup.com>
 * @license   GPL-2.0+
 * @link      http://thematosoup.com
 * @copyright 2013 ThematoSoup
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'WordPress'; ?>

	<?php $all_tha_hooks = tha_interface_all_hooks(); ?>
	
	<h2 class="nav-tab-wrapper">
		<?php foreach ( $all_tha_hooks as $tha_hooks_group => $tha_hooks_group_values ) : ?>
		<?php if ( current_theme_supports( 'tha_hooks', $tha_hooks_group ) || 'WordPress' == $tha_hooks_group ) : ?>
		<a href="?page=tha-hooks-interface&tab=<?php echo $tha_hooks_group; ?>" class="nav-tab <?php echo $active_tab == $tha_hooks_group ? 'nav-tab-active' : ''; ?>"><?php echo $tha_hooks_group_values['name'] ; ?></a>
		<?php endif; ?>
		<?php endforeach; ?>
	</h2>

	<form method="post" action="options.php">
		<?php
			settings_fields( 'tha_hooks_interface_' . $active_tab ); 
			do_settings_sections( 'tha_hooks_interface_' . $active_tab ); 
			submit_button();
		?>
	</form>

</div>
