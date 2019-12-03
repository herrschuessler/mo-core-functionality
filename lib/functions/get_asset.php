<?php
namespace Mo\Core;

/**
 * Get CSS asset path
 */
function get_css_asset( $file = null, $version = true ) {

	if ( ! is_string( $file ) ) {
		return false;
	}

	// use minified style if not in dev mode
	$folder = ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) ? 'dev/' : 'dist/';
	$path = get_template_directory_uri() . '/assets/css/' . $folder . filter_var( $file, FILTER_SANITIZE_ENCODED );

	// Append version
	if ( $version ) {
		// If $version is a string, use it as version number
		if ( is_string( $version ) ) {
			$version = esc_attr( $version );
		}
		// If $version is not a string, use theme version number as asset version
		else {
			$version = wp_get_theme()['Version'];
		}
		$path .= '?ver=' . $version;
	}

	return $path;
}

/**
 * Get JS asset path
 */
function get_js_asset( $file = null, $version = true ) {

	if ( ! is_string( $file ) ) {
		return false;
	}

	// use minified script if not in dev mode
	$folder = ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) ? 'dev/' : 'dist/';
	$path = get_template_directory_uri() . '/assets/js/' . $folder . filter_var( $file, FILTER_SANITIZE_ENCODED );

	// Append version
	if ( $version ) {
		// If $version is a string, use it as version number
		if ( is_string( $version ) ) {
			$version = esc_attr( $version );
		}
		// If $version is not a string, use theme version number as asset version
		else {
			$version = wp_get_theme()['Version'];
		}
		$path .= '?ver=' . $version;
	}

	return $path;
}
