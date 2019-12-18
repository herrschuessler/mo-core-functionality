<?php
/**
 * Force admmin color scheme
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since      1.8.0
 */

namespace Mo\Core\Action;

/**
 * Set admmin color scheme to 'ectoplasm' on DEV installs, 'fresh' on live sites.
 *
 * @param string $color_scheme The current color scheme.
 * @return string The new color scheme.
 */
function set_dev_color_scheme( $color_scheme ) {
	if ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
		return 'ectoplasm';
	}
	return 'fresh';
}

\add_filter( 'get_user_option_admin_color', '\Mo\Core\Action\set_dev_color_scheme', 5 );

// Prevent user from changing admin color scheme.
\remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
