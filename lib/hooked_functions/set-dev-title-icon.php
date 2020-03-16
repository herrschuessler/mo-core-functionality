<?php
/**
 * Add icon to wp_title and admin_title in DEV MODE
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.18.0
 */

namespace Mo\Core\Action;

use \Mo\Core\Core_Functionality as Core;

/**
 * Add "hammer & wrench" icon before title on DEV installs.
 *
 * @param string $title Page title.
 * @return string The title.
 */
function set_dev_title_icon( $title ) {
	if ( Core::is_dev() ) {
		$title = '&#x1f6e0; ' . $title;
	}
	return $title;
}

\add_filter( 'wp_title', '\Mo\Core\Action\set_dev_title_icon', 999, 1 );
\add_filter( 'admin_title', '\Mo\Core\Action\set_dev_title_icon', 999, 1 );

