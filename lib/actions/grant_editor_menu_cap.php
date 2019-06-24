<?php
namespace Mo\Core\Action;

/*
 * Grant editors access to menus.
 */
function grant_editor_menu_cap() {
	$role = \get_role( 'editor' );
	$role->add_cap( 'edit_theme_options' );
}

\add_action( 'admin_init', '\Mo\Core\Action\grant_editor_menu_cap' );

