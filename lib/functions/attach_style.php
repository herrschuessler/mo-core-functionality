<?php
namespace Mo\Core;

/*
* Attach modular stylesheet to content
*/
function attach_style( $module ) {

	if ( ! is_string( $module ) ) {
		return false;
	}

	// use minified style if not in dev mode
	$folder = ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) ? 'dev/' : 'dist/';

	// Script version = theme version
	$version = wp_get_theme()['Version'];

	$path = get_template_directory_uri() . '/assets/css/' . $folder . filter_var( $module, FILTER_SANITIZE_ENCODED ) . '.css?ver=' . $version;

	// @see https://jakearchibald.com/2016/link-in-body/
	return sprintf( '<link rel="stylesheet" href="%s"><script> </script>', $path );
}


