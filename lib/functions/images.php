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
	public function the_image_sizes( $image, $ratio = 1, $min = 300, $max = 900, $increase = 100, $class = '', $copyright = true, $fit = false, $link = false ) {

		if ( empty( $image ) ) {
			return false;
		}

		$ratio = floatval( $ratio );

		$data['image'] = $image;
		$data['class'] = $class;
		$data['fit'] = '';
		$data['copyright'] = get_field( 'copyright', $image );
		$data['copyright_link'] = get_field( 'copyright_link', $image );
		$data['sizes_source'] = [];
		$data['sizes_webp'] = [];
		$data['max'] = [
			'w' => $max,
			'h' => round( $max * $ratio ),
		];
		$data['link'] = htmlentities( $link );
		$size = $min;

		if ( $fit == 'cover' ) {
			$data['class'] .= ' cover-img';
			$data['fit'] = ' data-parent-fit="cover"';
		} elseif ( $fit == 'contain' ) {
			$data['class'] .= ' contain-img';
			$data['fit'] = ' data-parent-fit="contain"';
		}

		// Add image sizes
		while ( $size <= $max && $size <= $image->width ) {
			array_push( $data['sizes_source'], '{{ image.src|resize(' . $size . ', ' . round( $size * $ratio ) . ') }} ' . $size . 'w' );
			array_push( $data['sizes_webp'], '{{ image.src|resize(' . $size . ', ' . round( $size * $ratio ) . ')|towebp(70) }} ' . $size . 'w' );
			$size = $size + $increase;
		}

		// If last size was smaller than original image dimensions, add original image
		if ( ( $size - $increase ) < $image->width && ( $size - $increase ) < $max ) {
			array_push( $data['sizes_source'], '{{ image.src|resize(' . $image->width . ', ' . round( $image->width * $ratio ) . ') }} ' . $image->width . 'w' );
			array_push( $data['sizes_webp'], '{{ image.src|resize(' . $image->width . ', ' . round( $image->width * $ratio ) . ')|towebp(70) }} ' . $image->width . 'w' );
		}

		return \Timber::compile_string(
			'
      {% if link is not empty %}
        <a class="media-image__link" href="{{ link|e("esc_url") }}">
      {% endif %}
        <picture>
          <source data-srcset="' . implode( ', ', $data['sizes_webp'] ) . '" type="image/webp">
          <source data-srcset="' . implode( ', ', $data['sizes_source'] ) . '" type="image/jpeg">
          <img
            class="{{ class }} lazyload js-lazyload"
            alt="{{ image.alt }}"
            src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
            data-src="{{ image.src|resize(max.w, max.h) }}"
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

	public function the_cover_image_sizes( $image, $ratio = 1, $min = 300, $max = 900, $increase = 100, $class = '', $copyright = true, $link = false ) {
		return image_sizes( $image, $ratio, $min, $max, $increase, $class, $copyright, 'cover', $link );
	}

	public function the_contain_image_sizes( $image, $ratio = 1, $min = 300, $max = 900, $increase = 100, $class = '', $copyright = true, $link = false ) {
		return image_sizes( $image, $ratio, $min, $max, $increase, $class, $copyright, 'contain', $link );
	}

}

