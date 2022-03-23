<?php
/**
 * Core Functionality Twig Social Links
 *
 * Adds social links from Yoast SEO plugin to timber context
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 */

namespace Mo\Core\Twig;

/**
 * Adds social links from Yoast SEO plugin to timber context.
 *
 * @param array $data The timber context variables.
 */
function social_links( $data ) {
	$seo_data = get_option( 'wpseo_social' );

	$social_links = [];

	if ( is_array( $seo_data ) ) {
		foreach ( $seo_data as $profile => $value ) {
			if ( ! empty( $value ) ) {
				switch ( $profile ) {
					case 'facebook_site':
					case 'instagram_url':
					case 'linkedin_url':
					case 'myspace_url':
					case 'pinterest_url':
					case 'youtube_url':
					case 'wikipedia_url':
						$social_links[ explode( '_', $profile )[0] ] = $value;
						break;
					case 'twitter_site':
						$social_links[ explode( '_', $profile )[0] ] = 'https://twitter.com/' . $value . '/';
						break;
				}
			}
		}
	}

	$data['social_links'] = apply_filters( 'mo_social_links', $social_links );

	// Add to global namespace.
	$data['global']['social_links'] = $data['social_links'];

	return $data;
}

add_filter( 'timber_context', 'Mo\Core\Twig\social_links' );
