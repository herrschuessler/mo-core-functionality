<?php
/**
 * Implements a performant and secure YouTube video embed
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.23.0
 */

namespace Mo\Core;

trait Youtube_Embed {

	/**
	 * Endpoint.
	 *
	 * @var string Endpoint.
	 */
	protected static $youtube_embed_endpoint = 'mo-youtube-embed';

	/**
	 * Print video embed markup for YouTube videos.
	 *
	 * @param string     $youtube_id The youtube ID.
	 * @param string/int $image_id The ID of the image to use as cover image.
	 */
	public function the_youtube_embed( $youtube_id, $image_id = false ) {
		if ( ! is_string( $youtube_id ) ) {
			return false;
		}

		// Try to let Borlabs Content Blocker handle things.
		if ( shortcode_exists( 'borlabs-cookie' ) ) {
			$data['src'] = 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $youtube_id );
		} else {

			$data['src'] = get_home_url( null, self::$youtube_embed_endpoint . '/' . rawurlencode( $youtube_id ) );
		}

		if ( ! empty( $image_id ) ) {
			$data['src'] .= '/' . rawurlencode( $image_id );
		}

		return \Timber::compile_string(
			'
			{% apply spaceless|the_content %}
			<figure class="mo-embed mo-embed--youtube" style="position:relative;height:0;padding-bottom:56.25%;">
				[borlabs-cookie id="youtube" type="content-blocker"]
					<iframe style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:0;" src="{{ src }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				[/borlabs-cookie]
				</figure>
			{% endapply %}
			',
			$data
		);
	}

	/**
	 * Add endpoint.
	 */
	public function add_youtube_embed_endpoint() {
		add_rewrite_endpoint( self::$youtube_embed_endpoint, EP_ROOT, 'mo_youtube_embed' );
	}

	/**
	 * Add shortcode.
	 */
	public function add_youtube_embed_shortcode() {
		add_shortcode(
			'mo_youtube_embed',
			[ $this, 'youtube_embed_shortcode_callback' ]
		);
	}

	/**
	 * Add shortcode callback.
	 *
	 * @param array $atts The shortcode parameters.
	 */
	public function youtube_embed_shortcode_callback( $atts ) {
		$atts = shortcode_atts(
			[
				'youtube_id' => false,
				'image_id'   => false,
			],
			$atts,
			'mo_youtube_embed'
		);

		if ( ! $atts['youtube_id'] ) {
			return __( 'YouTube-ID fehlt', 'mo-core' );
		}

		return $this->the_youtube_embed( $atts['youtube_id'], $atts['image_id'] );
	}

	/**
	 * Load template.
	 */
	public function load_youtube_embed_template() {
		$endpoint = get_query_var( 'mo_youtube_embed', false );

		if ( ! empty( $endpoint ) ) {

			$endpoint   = explode( '/', $endpoint );
			$youtube_id = $endpoint[0];
			$image_id   = isset( $endpoint[1] ) ? (int) $endpoint[1] : false;
			$image      = new \Timber\Image( $image_id );

			// Get Timber context.
			$data                  = \Timber::context();
			$data['script']        = \Mo\Core\PLUGIN_URL . '/assets/js/images.js?ver=' . self::PLUGIN_VERSION;
			$data['youtube_id']    = $youtube_id;
			$data['image']         = $image;
			$data['youtube_image'] = 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg';

			\Timber::render( \Mo\Core\PLUGIN_PATH . 'views/youtube-embed.twig', $data );
			die;
		}
	}
}

