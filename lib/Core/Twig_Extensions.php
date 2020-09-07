<?php
/**
 * Core Functionality Twig Extensions
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 */

namespace Mo\Core;

/**
 * Extends Twig.
 */
final class Twig_Extensions {

	use \Mo\Core\Helpers;
	use \Mo\Core\Images;
	use \Mo\Core\Svg;
	use \Mo\Core\Youtube_Embed;

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'timber/twig', [ $this, 'init_twig_extension_stringloader' ] );
		\add_filter( 'timber/twig', [ $this, 'add_twig_filters' ] );
		\add_filter( 'timber/twig', [ $this, 'add_twig_functions' ] );
	}

	/**
	 * Init Twig_Extension_StringLoader.
	 *
	 * @param object $twig The Twig instance.
	 * @return object The Twig instance.
	 */
	public function init_twig_extension_stringloader( $twig ) {
		$twig->addExtension( new \Twig\Extension\StringLoaderExtension() );

		return $twig;
	}

	/**
	 * Add twig filters.
	 *
	 * @param object $twig The Twig instance.
	 * @return object The Twig instance.
	 */
	public function add_twig_filters( $twig ) {
		$twig->addFilter( new \Twig\TwigFilter( 'menu_item_classes', [ $this, 'menu_item_classes' ] ) );
		$twig->addFilter( new \Twig\TwigFilter( 'shuffle', [ $this, 'shuffle' ] ) ); // @todo Use native PHP function.
		$twig->addFilter( new \Twig\TwigFilter( 'tel_link', [ $this, 'tel_link' ] ) );
		$twig->addFilter( new \Twig\TwigFilter( 'the_content', [ $this, 'the_content' ] ) );

		return $twig;
	}

	/**
	 * Add twig functions.
	 *
	 * @param object $twig The Twig instance.
	 * @return object The Twig instance.
	 */
	public function add_twig_functions( $twig ) {
		$twig->addFunction( new \Timber\Twig_Function( 'attach_style', '\Mo\Core\attach_style' ) );
		$twig->addFunction( new \Timber\Twig_Function( 'contain_image_sizes', [ $this, 'the_contain_image_sizes' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'cover_image_sizes', [ $this, 'the_cover_image_sizes' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'debug', [ $this, 'debug' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'image_sizes', [ $this, 'the_image_sizes' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'svg_icon', [ $this, 'the_svg_icon' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'svg_img', [ $this, 'the_svg_img' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'youtube_embed', [ $this, 'the_youtube_embed' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'get_all_posts', '\Mo\Core\Core_Functionality::get_all_posts' ) );
		$twig->addFunction( new \Timber\Twig_Function( 'get_image_placeholder_height', [ $this, 'get_image_placeholder_height' ] ) );
		$twig->addFunction( new \Timber\Twig_Function( 'get_svg_content', [ $this, 'get_svg_content' ] ) );

		return $twig;
	}

	/**
	 * Convert menu item classes to BEM.
	 *
	 * @param array       $classes The menu classes.
	 * @param string|null $modifier A class name modifier (default: menu).
	 * @return string The modified menu classes.
	 */
	public function menu_item_classes( $classes, $modifier = null ) {
		if ( ! is_array( $classes ) ) {
			return false;
		}
		$modifier = is_string( $modifier ) ? $modifier : 'menu';
		$str      = implode( ' ', $classes );
		$search   = [
			'current-menu-ancestor',
			'current-menu-parent',
			'current-menu-item',
			'menu-item-',
			'menu-item',
		];
		$replace  = [
			$modifier . '__item--current-ancestor',
			$modifier . '__item--current-parent',
			$modifier . '__item--current',
			$modifier . '__item--',
			$modifier . '__item',
		];
		$str      = trim( str_replace( $search, $replace, $str ) );
		return $str;
	}

	/**
	 * Shuffle array.
	 *
	 * @param array $arr The array.
	 * @return array The shuffeled array.
	 */
	public function shuffle( $arr ) {
		shuffle( $arr );
		return $arr;
	}

	/**
	 * Run string through the_content filter.
	 *
	 * @param string $content The content.
	 * @return string The filtered content.
	 */
	public function the_content( $content ) {
		return \apply_filters( 'the_content', $content );
	}

	/**
	 * Run phone number string through libphonenumber-for-php.
	 *
	 * @param string $tel_string A phone number string.
	 * @return string A phone number suitable for tel links.
	 */
	public function tel_link( $tel_string ) {
		if ( is_string( $tel_string ) || is_int( $tel_string ) ) {
			$tel_object = false;
			$phone_util = \libphonenumber\PhoneNumberUtil::getInstance();
      // phpcs:disable
			try {
				$tel_object = $phone_util->parse( $tel_string, 'DE' );
			} catch ( \libphonenumber\NumberParseException $e ) {
			}
      // phpcs:enable
			if ( $phone_util->isValidNumber( $tel_object ) ) {
				$tel_string = $phone_util->format( $tel_object, \libphonenumber\PhoneNumberFormat::E164 );
			}
		}
		return $tel_string;
	}
}

