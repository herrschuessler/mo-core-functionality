<?php
/**
 * Implements a performant and secure video embed
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.27.0
 * @phpcs:disable WordPress.Files.FileName
 */

namespace Mo\Core;

trait Video_Embed {

	/**
	 * Endpoint.
	 *
	 * @var string Endpoint.
	 */
	protected static $video_embed_endpoint = 'mo-video-embed';

	/**
	 * Print video embed markup.
	 *
	 * @param string     $embed_url The video embed url.
	 * @param string/int $image_id The ID of the image to use as cover image.
	 * @param string     $text A text to display before loading the embed.
	 */
	public function the_video_embed( $embed_url, $image_id, $text = false ) {
		if ( ! is_string( $embed_url ) ) {
			return false;
		}

		if ( ! empty( $text ) ) {
			$data['text'] = $text;
		}

		// Try to let Borlabs Content Blocker handle things.
		if ( shortcode_exists( 'borlabs-cookie' ) ) {
			$data['src'] = $embed_url;

			if ( ! empty( $image_id ) ) {
				$thumbnail = wp_get_attachment_image_src( $image_id, 'large' );
				if ( isset( $thumbnail[0] ) ) {
					$data['thumbnail'] = $thumbnail[0];
				}
			}

			return \Timber::compile_string(
				'
				{% apply spaceless|the_content %}
				<figure class="wp-block-embed is-type-video wp-embed-aspect-16-9 wp-has-aspect-ratio">
					<div class="wp-block-embed__wrapper">
						[borlabs-cookie type="content-blocker"{% if thumbnail %} thumbnail="{{ thumbnail }}"{% endif %}]
						<iframe src="{{ src }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						[/borlabs-cookie]
						</div>
					{% if text %}
						<figcaption class="mo-embed__caption">
							<span class="mo-embed__caption-inner">{{ text }}</span>
						</figcaption>
					{% endif %}
				</figure>
				{% endapply %}
				',
				$data
			);
		}

		// We need to replace %2F in rawurlencoded string as it leads to a 404 (possibly WP core issue?).
		$data['src'] = get_home_url( null, self::$video_embed_endpoint . '/' . str_replace( '%2F', '!2F', rawurlencode( $embed_url ) ) );

		if ( ! empty( $image_id ) ) {
			$data['src'] .= '/' . rawurlencode( $image_id );
		}

		return \Timber::compile_string(
			'
			{% apply spaceless %}
			<figure class="mo-embed mo-embed--video">
				<div class="mo-embed__wrapper" style="position:relative;height:0;padding-bottom:56.25%;">
					<iframe class="mo-embed__iframe" style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:0;" src="{{ src }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
				{% if text %}
					<figcaption class="mo-embed__caption">
						<span class="mo-embed__caption-inner">{{ text }}</span>
					</figcaption>
				{% endif %}
			</figure>
			{% endapply %}
			',
			$data
		);
	}

	/**
	 * Add endpoint.
	 */
	public function add_video_embed_endpoint() {
		add_rewrite_endpoint( self::$video_embed_endpoint, EP_ROOT, 'mo_video_embed' );
	}

	/**
	 * Load template.
	 */
	public function load_video_embed_template() {
		$endpoint = get_query_var( 'mo_video_embed', false );

		if ( ! empty( $endpoint ) ) {

			$endpoint  = explode( '/', $endpoint );
			$embed_url = rawurldecode( str_replace( '!2F', '%2F', ( $endpoint[0] ) ) );
			$image_id  = isset( $endpoint[1] ) ? (int) rawurldecode( $endpoint[1] ) : false;
			$image     = new \Timber\Image( $image_id );

			// Get Timber context.
			$data              = \Timber::context();
			$data['script']    = \Mo\Core\PLUGIN_URL . '/assets/js/images.js?ver=' . self::PLUGIN_VERSION;
			$data['embed_url'] = $embed_url;
			$data['image']     = $image;

			\Timber::render( \Mo\Core\PLUGIN_PATH . 'views/video-embed.twig', $data );
			die;
		}
	}
}

