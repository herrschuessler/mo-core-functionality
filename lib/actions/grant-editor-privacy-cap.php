<?php
/**
 * Grant editors access to privacy policy
 *
 * This doesn't take effect until https://core.trac.wordpress.org/ticket/44176 is hopefully resolved.
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.13.0
 * @see        https://core.trac.wordpress.org/ticket/44176
 */

namespace Mo\Core\Action;

/**
 * Grant editors access to menus.
 */
function grant_editor_privacy_cap() {
	$role = \get_role( 'editor' );
	$role->add_cap( 'manage_privacy_options' );
}

\add_action( 'admin_init', '\Mo\Core\Action\grant_editor_privacy_cap' );

