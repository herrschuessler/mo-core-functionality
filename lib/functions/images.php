<?php
/**
 * Core Functionality Image Functions
 *
 * @package     Core_Functionality
 * @author      MONTAGMORGENS GmbH
 * @copyright   2019 MONTAGMORGENS GmbH
 */

namespace Mo\Core;

trait Images {

	/*
	* Build responsive image markup for use in twig templates.
	*/
	public function the_image_sizes( $image, $args = [] ) {

		if ( empty( $image ) || gettype( $image ) !== 'object' || get_class( $image ) !== 'Timber\Image' || ! is_array( $args ) ) {
			return false;
		}

		$defaults = [
			'ratio'   => 1,
			'min'     => 300,
			'max'     => 900,
			'steps'   => 100,
			'classes' => null,
			'style'   => null,
			'fit'     => false,
			'link'    => false,
		];

		$args = wp_parse_args( $args, $defaults );

		$args['ratio'] = floatval( $args['ratio'] );

		$data['image']          = $image;
		$data['class']          = $args['classes'] ? esc_attr( $args['classes'] ) : '';
		$data['style']          = $args['style'] ? esc_attr( $args['style'] ) : false;
		$data['link']           = $args['link'] ? esc_attr( $args['link'] ) : false;
		$data['fit']            = '';
		$data['copyright']      = get_field( 'copyright', $image );
		$data['copyright_link'] = get_field( 'copyright_link', $image );
		$data['sizes_source']   = [];
		$data['sizes_webp']     = [];
		$data['max'] = [
			'w' => $args['max'],
			'h' => round( $args['max'] * $args['ratio'] ),
		];
		$size = $args['min'];

		if ( $args['fit'] == 'cover' ) {
			$data['class'] .= ' cover-img';
			$data['fit'] = ' data-parent-fit="cover"';
		} elseif ( $args['fit'] == 'contain' ) {
			$data['class'] .= ' contain-img';
			$data['fit'] = ' data-parent-fit="contain"';
		}

		// Add image sizes
		while ( $size <= $args['max'] && $size <= $image->width ) {
			array_push( $data['sizes_source'], '{{ image.guid|resize(' . $size . ', ' . round( $size * $args['ratio'] ) . ') }} ' . $size . 'w' );
			array_push( $data['sizes_webp'], '{{ image.guid|resize(' . $size . ', ' . round( $size * $args['ratio'] ) . ')|towebp(70) }} ' . $size . 'w' );
			$size = $size + $args['steps'];
		}

		// If last size was smaller than original image dimensions, add original image
		if ( ( $size - $args['steps'] ) < $image->width && ( $size - $args['steps'] ) < $args['max'] ) {
			array_push( $data['sizes_source'], '{{ image.guid|resize(' . $image->width . ', ' . round( $image->width * $args['ratio'] ) . ') }} ' . $image->width . 'w' );
			array_push( $data['sizes_webp'], '{{ image.guid|resize(' . $image->width . ', ' . round( $image->width * $args['ratio'] ) . ')|towebp(70) }} ' . $image->width . 'w' );
		}

		return \Timber::compile_string(
			'
      {% if link is not empty %}
        <a class="media-image__link" href="{{ link|e("esc_url") }}">
      {% endif %}
        <picture>
          {#<source data-srcset="' . implode( ', ', $data['sizes_webp'] ) . '" type="image/webp">#}
          <source data-srcset="' . implode( ', ', $data['sizes_source'] ) . '" type="image/jpeg">
          <img
            class="{% if class is not empty %}{{ class }} {% endif %}lazyload js-lazyload"
            {% if style is not empty %}
            style="{{ style }}"
            {% endif %}
            alt="{{ image.alt }}"
            src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
            data-src="{{ image.guid|resize(max.w, max.h) }}"
            data-sizes="auto"
            {{ fit }}>
        </picture>
      {% if link is not empty %}
        </a>
      {% endif %}
      {% if copyright %}
        <footer class="media-image__footer">
          <small class="copyright">
            {% if copyright_link %}
              <a class="copyright__link" href="{{ copyright_link|e("esc_url") }}">{{ copyright }}</a>
            {% else %}
              {{ copyright }}
            {% endif %}
          </small>
        </footer>
      {% endif %}
      ',
			$data
		);
	}

	public function the_cover_image_sizes( $image, $args = [] ) {
		$args = wp_parse_args( $args );
		$args['fit'] = 'cover';
		return $this->the_image_sizes( $image, $args );
	}

	public function the_contain_image_sizes( $image, $args = [] ) {
		$args = wp_parse_args( $args );
		$args['fit'] = 'contain';
		return $this->the_image_sizes( $image, $args );
	}

}

