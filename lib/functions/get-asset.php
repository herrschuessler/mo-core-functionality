<?php
/**
 * Get Asset URLs
 *
 * Functions return assets URLs depending on WP_ENV. If WP_ENV is
 * set to development, an unminified dev asset will be returned, else
 * the production asset will be used.
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.11.0
 */

namespace Mo\Core;

use \Mo\Core\Core_Functionality as Core;

/**
 * Get CSS asset path.
 *
 * @param array $file The file name.
 * @param bool  $version Wether to append version.
 * @param bool  $basetheme Set to false if the asset should load from a child theme.
 */
function get_css_asset( $file = null, $version = true, $basetheme = true ) {

	if ( ! is_string( $file ) ) {
		return false;
	}

	// Use minified style if not in dev mode.
	$folder = Core::is_dev() ? 'dev/' : 'dist/';
	$path   = $basetheme ? get_template_directory_uri() : get_stylesheet_directory_uri();
	$path   = $path . '/assets/css/' . $folder . trim( filter_var( $file, FILTER_SANITIZE_ENCODED ) );
	$theme  = $basetheme ? wp_get_theme( get_template() ) : wp_get_theme();

	// Append version.
	if ( $version ) {
		if ( is_string( $version ) ) {
			// If $version is a string, use it as version number.
			$version = esc_attr( $version );
		} else {
			// If $version is not a string, use theme version number as asset version.
			$version = $theme['Version'];
		}
		$path .= '?ver=' . $version;
	}

	return $path;
}

/**
 * Get JS asset path.
 *
 * @param array $file The file name.
 * @param bool  $version Wether to append version.
 * @param bool  $basetheme Set to false if the asset should load from a child theme.
 */
function get_js_asset( $file = null, $version = true, $basetheme = true ) {

	if ( ! is_string( $file ) ) {
		return false;
	}

	// Use minified script if not in dev mode.
	$folder = Core::is_dev() ? 'dev/' : 'dist/';
	$path   = $basetheme ? get_template_directory_uri() : get_stylesheet_directory_uri();
	$path   = $path . '/assets/js/' . $folder . trim( filter_var( $file, FILTER_SANITIZE_ENCODED ) );
	$theme  = $basetheme ? wp_get_theme( get_template() ) : wp_get_theme();

	// Append version.
	if ( $version ) {
		if ( is_string( $version ) ) {
			// If $version is a string, use it as version number.
			$version = esc_attr( $version );
		} else {
			// If $version is not a string, use theme version number as asset version.
			$version = $theme['Version'];
		}
		$path .= '?ver=' . $version;
	}

	return $path;
}

/**
 * Get JS public_path.
 *
 * @todo This always retuns the path to the parent theme. Need to adapt it for child themes.
 */
function get_js_public_path() {

	// use minified script if not in dev mode.
	$folder = Core::is_dev() ? 'dev/' : 'dist/';
	$path   = get_template_directory_uri() . '/assets/js/' . $folder;

	return $path;
}
