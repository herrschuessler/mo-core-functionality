<?php
/**
 * Attach Style
 *
 * Print stylesheet link to html body for non-blocking download.
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @see        https://jakearchibald.com/2016/link-in-body/
 * @since      1.0.0
 */

namespace Mo\Core;

/**
 * Print stylesheet link.
 *
 * @param string $module The stylesheet file name without or without extension.
 */
function attach_style( $module ) {

	if ( ! is_string( $module ) || empty( $module ) ) {
		return false;
	}

	// Sanitize file name.
	$module = trim( filter_var( $module, FILTER_SANITIZE_ENCODED ) );

	// Add extension if not present.
	if ( ! ends_with( $module, '.css' ) ) {
		$module .= '.css';
	}

	// Get full path.
	$module = get_css_asset( $module );

	// @codingStandardsIgnoreStart
	return sprintf( '<link rel="stylesheet" href="%s"><script> </script>', $module );
	// @codingStandardsIgnoreEnd
}
