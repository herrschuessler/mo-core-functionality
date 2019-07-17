<?php
/**
 * Core Functionality
 *
 * @package     Core_Functionality
 * @author      MONTAGMORGENS GmbH
 * @copyright   2019 MONTAGMORGENS GmbH
 *
 * @wordpress-plugin
 * Plugin Name: MONTAGMORGENS Core Functionality
 * Description: Dieses Plugin stellt die benötigten Funktionen für alle MONTAGMORGENS-WordPress-Themes zur Verfügung.
 * Version:     1.1.0
 * Author:      MONTAGMORGENS GmbH
 * Author URI:  https://www.montagmorgens.com/
 * License:     GNU General Public License v.2
 * Text Domain: mo-core
 */

namespace Mo\Core;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

// Define absolute path to plugin root.
define( 'Mo\Core\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Require composer autoloader.
require_once( \Mo\Core\PLUGIN_PATH . 'lib/vendor/autoload.php' );

// Require globing function.
require_once( \Mo\Core\PLUGIN_PATH . 'lib/glob_require.php' );

// Require subdirectories.
glob_require( array( 'functions', 'twig', 'actions', 'filter' ) );

// Init plugin instance.
\add_action( 'plugins_loaded', array( '\Mo\Core\Core_Functionality', 'get_instance' ) );

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Core_Functionality {

	use Helpers;

	const PLUGIN_VERSION = '1.1.0';
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Core_Functionality
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Init Timber
		new \Timber\Timber();

		// Init Twig Extensions
		new \Mo\Core\Twig\Twig_Extensions();

		// Add action hooks
		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		\add_filter( 'script_loader_tag', array( $this, 'async_theme_scripts' ), 10, 2 );

	}

	/**
	 * Enqueue plugin assets if shortcode is present.
	 */
	public function enqueue_assets() {

		// Enqueue plugin scripts.
		\wp_enqueue_script( 'mo-images', \plugins_url( '/assets/js/images.js', __FILE__ ), null, self::PLUGIN_VERSION, true );
	}

	/**
	 * Add defer attribute to scripts.
	 */
	public function async_theme_scripts( $tag, $handle ) {
		$scripts_to_defer = array( 'mo-images' );
		foreach ( $scripts_to_defer as $defer_script ) {
			if ( $defer_script === $handle ) {
				return str_replace( ' src', ' async src', $tag );
			}
		}
		return $tag;
	}

}
