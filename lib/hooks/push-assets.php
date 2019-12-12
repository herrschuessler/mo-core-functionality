<?php
/**
 * HTTP/2 Push Assets
 *
 * Push all enqueued assets to browser over HTTP/2 pipeline.
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Mo\Core
 * @author     Tom J Nowell <contact@tomjn.com>
 * @link       https://gist.github.com/tomjn/7fe22a4ec20f2565004bd216e9d1f497 GitHub Gist
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since      1.11.0
 */

namespace Mo\Core;

/**
 * Get asset URL.
 *
 * @param WP_Dependency $dep WP_Dependency class instance.
 */
function get_dep_url( $dep ) {
	global $wp_version;
	$relative = str_replace( site_url(), '', $dep->src );
	$ver      = $dep->ver;
	if ( false === $ver ) {
		$ver = $wp_version;
	}
	return $relative . '?ver=' . $ver;
}

/**
 * Push scripts.
 */
function push_scripts() {
	global $wp_scripts, $wp_styles;
	foreach ( $wp_scripts->queue as $handle ) {
		header( 'Link: <' . get_dep_url( $wp_scripts->registered[ $handle ] ) . '>; rel=preload; as=script', false );
	}
}

/**
 * Push styles.
 */
function push_styles() {
	global $wp_styles;
	foreach ( $wp_styles->queue as $handle ) {
		header( 'Link: <' . get_dep_url( $wp_styles->registered[ $handle ] ) . '>; rel=preload; as=style', false );
	}
}

add_action( 'wp_enqueue_scripts', '\Mo\Core\push_scripts', PHP_INT_MAX );
add_action( 'wp_enqueue_scripts', '\Mo\Core\push_styles', PHP_INT_MAX );
