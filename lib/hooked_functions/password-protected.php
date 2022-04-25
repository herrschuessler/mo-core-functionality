<?php
/**
 * Settings for Password Protected plugin
 *
 * @see        https://de.wordpress.org/plugins/password-protected
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.31.0
 */

namespace Mo\Core\Action;

/**
 * Set unique cookie name to allow multiple instances on dev server to user their own cookie.
 *
 * @param string $string The cookie name.
 */
function set_password_protected_cookie_name( $string ) {
	$theme = wp_get_theme();
	if ( $theme ) {
		$string .= '_' . sanitize_title( $theme->get( 'Name' ) );
	}
	return $string;
}

\add_filter( 'password_protected_cookie_name', '\Mo\Core\Action\set_password_protected_cookie_name' );
