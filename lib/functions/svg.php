<?php
/**
 * Print SVG markup
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since      1.0.0
 */

namespace Mo\Core;

trait SVG {

	/**
	 * Print SVG icon markup.
	 *
	 * @param string       $icon The icon name in the SVG sprite map.
	 * @param string/array $classes CSS class names (optional).
	 * @param string       $title The title (optional).
	 * @param string       $desc The description (optional).
	 * @param string       $role The aria-role (defaults to 'presentation').
	 * @param string       $set The icon set, i.e. the filename of the SVG sprite map (without extension).
	 */
	public function the_svg_icon( $icon, $classes = null, $title = null, $desc = null, $role = 'presentation', $set = 'ui' ) {

		if ( ! is_string( $icon ) || empty( $icon ) || ! is_string( $set ) ) {
			return false;
		}

		$data['icon'] = filter_var( get_template_directory_uri() . '/assets/svg-sprite/' . $set . '.svg#' . $icon, FILTER_SANITIZE_URL );

		// The CSS classes.
		$classes = is_array( $classes ) ? join( ' ', $classes ) : $classes;
		if ( is_string( $classes ) && ! empty( $classes ) ) {
			$data['classes'] = trim( filter_var( $classes, FILTER_SANITIZE_STRING ) );
		}

		// The title.
		if ( is_string( $title ) && ! empty( $title ) ) {
			$data['title'] = trim( filter_var( $title, FILTER_SANITIZE_STRING ) );
		}

		// The description.
		if ( is_string( $desc ) && ! empty( $desc ) ) {
			$data['desc'] = trim( filter_var( $desc, FILTER_SANITIZE_STRING ) );
		}

		// The aria-role.
		if ( 'image' === $role ) {
			$data['role'] = 'image';
		} else {
			$data['role'] = 'presentation';
		}

		return \Timber::compile_string(
			'
			{% spaceless %}
			<svg role="{{ role }}"{% if classes %} class="{{ classes }}"{% endif %}>

			{% if title %}
			<title>{{ title }}</title>
			{% endif %}

			{% if desc %}
			<desc>{{ desc }}</desc>
			{% endif %}

			<use xlink:href="{{ icon }}"></use>
			</svg>
			{% endspaceless %}
			',
			$data
		);
	}

	/**
	 * Print SVG image markup.
	 *
	 * @param string       $icon The icon name in the SVG sprite map.
	 * @param string/array $classes CSS class names (optional).
	 * @param string       $alt The alt text (optional).
	 * @param string       $role The aria-role (defaults to 'presentation').
	 */
	public function the_svg_img( $icon, $classes = null, $alt = null, $role = 'presentation' ) {

		if ( ! is_string( $icon ) || empty( $icon ) ) {
			return false;
		}

		$data['src'] = filter_var( get_template_directory_uri() . '/assets/images/' . $icon . '.svg', FILTER_SANITIZE_URL );

		// The CSS classes.
		$classes = is_array( $classes ) ? join( ' ', $classes ) : $classes;
		if ( is_string( $classes ) && ! empty( $classes ) ) {
			$data['classes'] = trim( filter_var( $classes, FILTER_SANITIZE_STRING ) );
		}

		// The alt text.
		if ( is_string( $alt ) && ! empty( $alt ) ) {
			$data['alt'] = trim( filter_var( $alt, FILTER_SANITIZE_STRING ) );
		}

		// The aria-role.
		if ( 'image' === $role ) {
			$data['role'] = 'image';
		} else {
			$data['role'] = 'presentation';
		}

		return \Timber::compile_string(
			'<img src="{{ src }}" class="{{ classes ? classes ~ " js-inline-svg" : "js-inline-svg" }}"{% if alt %} alt="{{ alt }}"{% endif %} role="{{ role }}">',
			$data
		);
	}
}
