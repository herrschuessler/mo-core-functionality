<?php
/**
 * Core Functionality SVG Functions
 *
 * @package     Core_Functionality
 * @author      MONTAGMORGENS GmbH
 * @copyright   2019 MONTAGMORGENS GmbH
 */

namespace Mo\Core;

trait SVG {

	// Print SVG icon
	public function the_svg_icon( $icon, $classes = null, $title = null, $desc = null, $role = 'presentation', $set = 'ui' ) {

		if ( empty( $icon ) ) {
			return false;
		}

		$data['icon'] = get_template_directory_uri() . '/assets/svg-sprite/' . $set . '.svg#' . $icon;
		$data['classes'] = is_array( $classes ) ? join( ', ', $classes ) : $classes;
		$data['title'] = $title;
		$data['desc'] = $desc;
		$data['role'] = $role;

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

	// Print SVG icon
	public function the_svg_img( $icon, $classes = null, $alt = null, $role = 'presentation' ) {

		if ( empty( $icon ) ) {
			return false;
		}

		$data['src']    = esc_attr( get_template_directory_uri() . '/assets/images/' . $icon . '.svg' );
		$data['classes'] = esc_attr( is_array( $classes ) ? join( ', ', $classes ) : $classes );
		$data['alt']     = esc_attr( $alt );
		$data['role']    = esc_attr( $role );

		return \Timber::compile_string(
			'
      <img
      src="{{ src }}"
      class="{{ classes ? classes ~ " js-inline-svg" : "js-inline-svg" }}"
      {% if alt %}alt="{{ alt }}"{% endif %}
      role="{{ role }}">
      ',
			$data
		);
	}

}

