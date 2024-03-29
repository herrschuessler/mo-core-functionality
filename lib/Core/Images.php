<?php
/**
 * Print image markup
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 * @phpcs:disable WordPress.Files.FileName
 */

namespace Mo\Core;

trait Images {

	/**
	 * Build responsive image markup for use in twig templates.
	 *
	 * @param Timber\Image $image The image object.
	 * @param array        $args {
	 *     Array of arguments.
	 *     @type float      $ratio The desired aspect ratio (optional, default: The original aspect ratio).
	 *     @type int        $min The minimum image width (optional, default: 300).
	 *     @type int        $max The maximum image width (optional, default: 900).
	 *     @type int        $steps The interval between image sizes (optional, default: 100).
	 *     @type mixed      $classes The CSS classes (optional).
	 *     @type mixed      $style The CSS style attribute (optional).
	 *     @type mixed      $fit Wether to use object-fitting (can be false, 'cover', 'contain) (optional).
	 *     @type mixed      $link A link URL to wrap the image with (optional). Can be a string with a URL or an array with 'url', 'tabindex' and 'target'.
	 *     @type string     $crop cropping method, one of: 'default', 'center', 'top', 'bottom', 'left', 'right', 'top-center', 'bottom-center'.
	 * }
	 */
	public function the_image_sizes( $image, $args = [] ) {

		if ( empty( $image ) || gettype( $image ) !== 'object' || get_class( $image ) !== 'Timber\Image' || ! is_array( $args ) ) {
			return false;
		}
		$defaults = [
			'ratio'   => null,
			'min'     => 300,
			'max'     => 900,
			'steps'   => 100,
			'classes' => null,
			'style'   => null,
			'fit'     => false,
			'link'    => false,
			'crop'    => 'default',
		];

		$args = wp_parse_args( $args, $defaults );

		$allowed_crop_positions = [ 'default', 'center', 'top', 'bottom', 'left', 'right', 'top-center', 'bottom-center' ];

		// Parse ratio to float or null.
		$args['ratio'] = is_numeric( $args['ratio'] ) && $args['ratio'] > 0 ? floatval( $args['ratio'] ) : null;

		$data['image']          = $image;
		$data['class']          = $args['classes'] ? esc_attr( $args['classes'] ) : '';
		$data['style']          = $args['style'] ? esc_attr( $args['style'] ) : false;
		$data['fit']            = '';
		$data['copyright']      = get_field( 'copyright', $image );
		$data['copyright_link'] = get_field( 'copyright_link', $image );
		$data['sizes_source']   = [];
		$data['crop']           = in_array( $args['crop'], $allowed_crop_positions, true ) ? $args['crop'] : 'default';

		// Parse link.
		$data['link'] = false;

		if ( is_string( $args['link'] ) ) {
			// The link arg can be a simple string – treat it as link URL.
			$data['link'] = esc_url( $args['link'] );
		} elseif ( is_array( $args['link'] ) ) {
			// The link arg can be an array with further link options.
			if ( isset( $args['link']['url'] ) ) {
				$data['link'] = esc_url( $args['link']['url'] );
			}
			if ( isset( $args['link']['tabindex'] ) ) {
				$data['link_tabindex'] = esc_attr( $args['link']['tabindex'] );
			}
			if ( isset( $args['link']['target'] ) ) {
				$data['link_target'] = esc_attr( $args['link']['target'] );
			}
		}

		// Object fitting.
		if ( 'cover' === $args['fit'] ) {
			$data['class'] .= ' cover-img';
			$data['fit']    = ' data-parent-fit="cover"';
		} elseif ( 'contain' === $args['fit'] ) {
			$data['class'] .= ' contain-img';
			$data['fit']    = ' data-parent-fit="contain"';
		}

		// Handle SVGs.
		if ( 'image/svg+xml' === $image->post_mime_type ) {

			// Parse SVG width and height from viewbox attribute.
			$svg = file_get_contents( $image->file_loc );
			if ( $svg ) {
				$svg      = new \SimpleXMLElement( $svg );
				$view_box = explode( ' ', (string) $svg->attributes()->viewBox );
				if ( isset( $view_box[2] ) && isset( $view_box[3] ) ) {
					$data['width']  = $view_box[2];
					$data['height'] = $args['ratio'] ? round( $view_box[2] * $args['ratio'] ) : $view_box[3];
				}
			}
			return \Timber::compile_string(
				'
				{% if link is not empty %}
				<a class="media-image__link" href="{{ link }}"{% if link_target %} target="{{ link_target }}"{% endif %}{% if link_tabindex %} tabindex="{{ link_tabindex }}"{% endif %}>
				{% endif %}
				<img
				class="{% if class is not empty %}{{ class }} {% endif %}lazyload js-lazyload"
				{% if style is not empty %}
				style="{{ style }}"
				{% endif %}
				alt="{{ image.alt }}"
				src="{{ image.src }}"
				{% if width and height %}
				width="{{ width }}"
				height="{{ height }}"
				{% endif %}
				{{ fit }}>
				{% if link is not empty %}
				</a>
				{% endif %}
				{% if copyright %}
				<footer class="media-image__footer">
				<small class="copyright" aria-hidden="true">
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

		// Handle bitmap images.
		// Add webp if server supports it and image is a jpeg.
		$webp_quality = Core_Functionality::$webp_quality;

		if ( function_exists( 'imagewebp' ) && 'image/jpeg' === $image->post_mime_type && is_int( $webp_quality ) ) {
			$data['sizes_webp'] = [];
		} else {
			$data['sizes_webp'] = false;
		}

		$data['max']    = [
			'w' => $args['max'],
			'h' => $args['ratio'] ? round( $args['max'] * $args['ratio'] ) : 0,
		];
		$width          = $args['min'];
		$original_ratio = $image->width > 0 ? $image->height / $image->width : 0;
		$resize_height  = 0;
		$fallback       = '';

		// Add image sizes.
		while ( $width <= $args['max'] && $width <= $image->width ) {
			$resize_height = $args['ratio'] ? round( $width * $args['ratio'] ) : 0;
			$height        = $args['ratio'] ? round( $width * $args['ratio'] ) : round( $width * $original_ratio );
			if ( is_array( $data['sizes_webp'] ) ) {
				array_push( $data['sizes_webp'], '{{ image.src|towebp(' . $webp_quality . ')|resize(' . $width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }} ' . $width . 'w ' . $height . 'h' );
			} else {
				array_push( $data['sizes_source'], '{{ image.src|resize(' . $width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }} ' . $width . 'w ' . $height . 'h' );
			}
			$data['width']  = $width;
			$data['height'] = $height;
			$fallback       = '{{ image.src|resize(' . $width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }}';
			$width          = $width + $args['steps'];
		}

		// If last size was smaller than original image dimensions and original image is smaller
		// than max requested size, add original image.
		if ( ( $width - $args['steps'] ) < $image->width && ( $width - $args['steps'] ) < $args['max'] ) {
			$resize_height = $args['ratio'] ? round( $image->width * $args['ratio'] ) : 0;
			$height        = $args['ratio'] ? round( $image->width * $args['ratio'] ) : $image->height;
			if ( is_array( $data['sizes_webp'] ) ) {
				array_push( $data['sizes_webp'], '{{ image.src|towebp(' . $webp_quality . ')|resize(' . $image->width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }} ' . $image->width . 'w ' . $height . 'h' );
			} else {
				array_push( $data['sizes_source'], '{{ image.src|resize(' . $image->width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }} ' . $image->width . 'w ' . $height . 'h' );
			}
			$data['width']  = $image->width;
			$data['height'] = $height;
			$fallback       = '{{ image.src|resize(' . $image->width . ', ' . $resize_height . ', \'' . $data['crop'] . '\') }}';
		}

		// Compile image sources.
		$data['fallback']     = \Timber::compile_string( $fallback, $data );
		$data['sizes_source'] = \Timber::compile_string( implode( ', ', $data['sizes_source'] ), $data );
		if ( $data['sizes_webp'] ) {
			$data['sizes_webp'] = \Timber::compile_string( implode( ', ', $data['sizes_webp'] ), $data );
		}

		return \Timber::compile_string(
			'
			{% if link is not empty %}
			<a class="media-image__link" href="{{ link }}"{% if link_target %} target="{{ link_target }}"{% endif %}{% if link_tabindex %} tabindex="{{ link_tabindex }}"{% endif %}>
			{% endif %}
			<picture>
			{% if sizes_webp is not empty %}
				<source data-srcset="{{ sizes_webp }}" type="image/webp">
			{% endif %}
			{% if sizes_source is not empty %}
				<source data-srcset="{{ sizes_source }}" type="image/jpeg">
			{% endif %}
			<img
			class="{% if class is not empty %}{{ class }} {% endif %}lazyload js-lazyload"
			{% if style is not empty %}
			style="{{ style }}"
			{% endif %}
			alt="{{ image.alt }}"
			src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
			data-src="{{ fallback }}"
			data-sizes="auto"
			width="{{ width }}"
			height="{{ height }}"
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

	/**
	 * Build responsive cover image markup for use in twig templates.
	 *
	 * @param Timber\Image $image The image object.
	 * @param array        $args Array of arguments, @see the_image_sizes().
	 */
	public function the_cover_image_sizes( $image, $args = [] ) {
		$args        = wp_parse_args( $args );
		$args['fit'] = 'cover';
		return $this->the_image_sizes( $image, $args );
	}

	/**
	 * Build responsive contain image markup for use in twig templates.
	 *
	 * @param Timber\Image $image The image object.
	 * @param array        $args Array of arguments, @see the_image_sizes().
	 */
	public function the_contain_image_sizes( $image, $args = [] ) {
		$args        = wp_parse_args( $args );
		$args['fit'] = 'contain';
		return $this->the_image_sizes( $image, $args );
	}

	/**
	 * Get height in percentage for image placeholders.
	 *
	 * @param Timber\Image $image The image object.
	 */
	public function get_image_placeholder_height( $image ) {
		if ( empty( $image ) || gettype( $image ) !== 'object' || get_class( $image ) !== 'Timber\Image' ) {
			return false;
		}

		// Handle SVG.
		if ( 'image/svg+xml' === $image->post_mime_type ) {

			// Parse SVG width and height from viewbox attribute.
			$svg = file_get_contents( $image->file_loc );
			if ( $svg ) {
				$svg      = new \SimpleXMLElement( $svg );
				$view_box = explode( ' ', (string) $svg->attributes()->viewBox );
				if ( isset( $view_box[2] ) && isset( $view_box[3] ) ) {
					return ( (float) $view_box[3] / (float) $view_box[2] * 100 ) . '%';
				}
			}
		} else {
			return ( $image->height / $image->width * 100 ) . '%';
		}
		return false;
	}
}

