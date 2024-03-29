<?php
/**
 * Print SVG markup
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 * @phpcs:disable WordPress.Files.FileName
 */

namespace Mo\Core;

trait Svg {

	/**
	 * Print SVG icon markup from image sprite.
	 *
	 * @param string       $icon The icon name in the SVG sprite map.
	 * @param string/array $classes CSS class names (optional).
	 * @param string       $title The title (optional).
	 * @param string       $desc The description (optional).
	 * @param string       $role The aria-role (defaults to 'presentation').
	 * @param string       $set The icon set, i.e. the filename of the SVG sprite map (without extension).
	 * @param bool         $basetheme Set to false if the sprite should load from a child theme.
	 */
	public function the_svg_icon( $icon, $classes = null, $title = null, $desc = null, $role = 'presentation', $set = 'ui', $basetheme = true ) {

		if ( ! is_string( $icon ) || empty( $icon ) || ! is_string( $set ) ) {
			return false;
		}

		$path  = $basetheme ? get_template_directory_uri() : get_stylesheet_directory_uri();
		$theme = $basetheme ? wp_get_theme( get_template() ) : wp_get_theme();

		$data['icon'] = $path . filter_var( '/assets/svg-sprite/' . $set . '.svg?v=' . rawurlencode( $theme['Version'] ) . '#' . $icon, FILTER_SANITIZE_URL );

		// The CSS classes.
		$classes = is_array( $classes ) ? join( ' ', $classes ) : $classes;
		if ( is_string( $classes ) && ! empty( $classes ) ) {
			$data['classes'] = trim( htmlspecialchars( $classes ) );
		}

		// The title.
		if ( is_string( $title ) && ! empty( $title ) ) {
			$data['title'] = trim( htmlspecialchars( $title ) );
		}

		// The description.
		if ( is_string( $desc ) && ! empty( $desc ) ) {
			$data['desc'] = trim( htmlspecialchars( $desc ) );
		}

		// The aria-role.
		if ( 'image' === $role ) {
			$data['role'] = 'image';
		} else {
			$data['role'] = 'presentation';
		}

		return \Timber::compile_string(
			'
			{% apply spaceless %}
			<svg role="{{ role }}"{% if classes %} class="{{ classes }}"{% endif %}>

			{% if title %}
			<title>{{ title }}</title>
			{% endif %}

			{% if desc %}
			<desc>{{ desc }}</desc>
			{% endif %}

			<use xlink:href="{{ icon }}"></use>
			</svg>
			{% endapply %}
			',
			$data
		);
	}

	/**
	 * Print SVG image markup from image sprite.
	 *
	 * @param string       $icon The icon name in the SVG sprite map.
	 * @param string/array $classes CSS class names (optional).
	 * @param string       $alt The alt text (optional).
	 * @param string       $role The aria-role (defaults to 'presentation').
	 * @param bool         $basetheme Set to false if the image should load from a child theme.
	 */
	public function the_svg_img( $icon, $classes = null, $alt = null, $role = 'presentation', $basetheme = true ) {

		if ( ! is_string( $icon ) || empty( $icon ) ) {
			return false;
		}

		$path = $basetheme ? get_template_directory_uri() : get_stylesheet_directory_uri();

		$data['src'] = filter_var( $path . '/assets/images/' . $icon . '.svg', FILTER_SANITIZE_URL );

		// The CSS classes.
		$classes = is_array( $classes ) ? join( ' ', $classes ) : $classes;
		if ( is_string( $classes ) && ! empty( $classes ) ) {
			$data['classes'] = trim( htmlspecialchars( $classes ) );
		}

		// The alt text.
		if ( is_string( $alt ) && ! empty( $alt ) ) {
			$data['alt'] = trim( htmlspecialchars( $alt ) );
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

	/**
	 * Get SVG file and return SVG markup.
	 *
	 * @param Timber\Image $image The image object.
	 */
	public function get_svg_content( $image ) {
		if ( empty( $image ) || gettype( $image ) !== 'object' || get_class( $image ) !== 'Timber\Image' || 'image/svg+xml' !== $image->post_mime_type ) {
			return false;
		}

		// Parse SVG ans strip namespace declaration.
		// phpcs:ignore WordPress.WP.AlternativeFunctions
		$svg = file_get_contents( $image->file_loc );
		if ( $svg ) {
			$svg = new \SimpleXMLElement( $svg );
			$dom = dom_import_simplexml( $svg );
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement );
		}
		return false;
	}
}
