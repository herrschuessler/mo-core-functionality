<?php
/**
 * Core Functionality Twig Extensions
 *
 * @package     Core_Functionality
 * @author      MONTAGMORGENS GmbH
 * @copyright   2019 MONTAGMORGENS GmbH
 */

namespace Mo\Core\Twig;

class Twig_Extensions {

	use \Mo\Core\Helpers;
	use \Mo\Core\Images;
	use \Mo\Core\SVG;

	/**
	 * Constructor.
	 */
	function __construct() {
		add_filter( 'timber/twig', array( $this, 'init_twig_extension_stringloader' ) );
		add_filter( 'timber/twig', array( $this, 'add_twig_filters' ) );
		add_filter( 'timber/twig', array( $this, 'add_twig_functions' ) );
	}

	/*
	* Init Twig_Extension_StringLoader.
	*/
	function init_twig_extension_stringloader( $twig ) {
		$twig->addExtension( new \Twig_Extension_StringLoader() );

		return $twig;
	}

	/*
	* Add twig filters.
	*/
	function add_twig_filters( $twig ) {
		$twig->addFilter( new \Twig_SimpleFilter( 'menu_item_classes', array( $this, 'menu_item_classes' ) ) );
		$twig->addFilter( new \Twig_SimpleFilter( 'shuffle', array( $this, 'shuffle' ) ) );
		$twig->addFilter( new \Twig_SimpleFilter( 'the_content', array( $this, 'the_content' ) ) );

		return $twig;
	}

	/*
	* Add twig functions.
	*/
	function add_twig_functions( $twig ) {
		$twig->addFunction( new \Timber\Twig_Function( 'attach_style', '\Mo\Core\attach_style' ) );
		$twig->addFunction( new \Timber\Twig_Function( 'contain_image_sizes', array( $this, 'the_contain_image_sizes' ) ) );
		$twig->addFunction( new \Timber\Twig_Function( 'cover_image_sizes', array( $this, 'the_cover_image_sizes' ) ) );
		$twig->addFunction( new \Timber\Twig_Function( 'debug', array( $this, 'debug' ) ) );
		$twig->addFunction( new \Timber\Twig_Function( 'image_sizes', array( $this, 'the_image_sizes' ) ) );
		$twig->addFunction( new \Timber\Twig_Function( 'svg_icon', array( $this, 'the_svg_icon' ) ) );
		$twig->addFunction( new \Timber\Twig_Function( 'svg_img', array( $this, 'the_svg_img' ) ) );

		return $twig;
	}

	/**
	 * Convert menu item classes to BEM.
	 *
	 * @var array $classes The menu classes.
	 * @var string|null $modifier A class name modifier (default: menu).
	 */
	public function menu_item_classes( $classes, $modifier = null ) {
		if ( ! is_array( $classes ) ) {
			return false;
		}
		$modifier = is_string( $modifier ) ? $modifier : 'menu';
		$str = implode( ' ', $classes );
		$search = [
			'current-menu-ancestor',
			'current-menu-parent',
			'current-menu-item',
			'menu-item-',
			'menu-item',
		];
		$replace = [
			$modifier . '__item--current-ancestor',
			$modifier . '__item--current-parent',
			$modifier . '__item--current',
			$modifier . '__item--',
			$modifier . '__item',
		];
		$str = trim( str_replace( $search, $replace, $str ) );
		return $str;
	}

	/**
	 * Shuffle array.
	 *
	 * @var array $arr The array.
	 */
	public function shuffle( $arr ) {
		shuffle( $arr );
		return $arr;
	}

	/**
	 * Run string through the_content filter.
	 *
	 * @var string $content The content.
	 */
	public function the_content( $content ) {
		return \apply_filters( 'the_content', $content );
	}

}

