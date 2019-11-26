<?php
namespace Mo\Core\Action;

/*
 * Set admmin color scheme to 'ectoplasm' on DEV installs, 'fresh' on live sites.
 */
function set_dev_color_scheme( $color_scheme ) {
	if ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
		return 'ectoplasm';
	}
	return 'fresh';
}

\add_filter( 'get_user_option_admin_color', '\Mo\Core\Action\set_dev_color_scheme', 5 );

// Prevent user from changing admin color scheme
\remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
