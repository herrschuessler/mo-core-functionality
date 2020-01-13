<?php
/**
 * Core Functionality
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: MONTAGMORGENS Core Functionality
 * Description: Dieses Plugin stellt die benötigten Funktionen für alle MONTAGMORGENS-WordPress-Themes zur Verfügung.
 * Version:     1.13.1
 * Author:      MONTAGMORGENS GmbH
 * Author URI:  https://www.montagmorgens.com/
 * License:     GNU General Public License v.2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mo-core
 * GitHub Plugin URI: montagmorgens/mo-core-functionality
 */

namespace Mo\Core;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

// Define absolute path to plugin root.
if ( ! defined( 'Mo\Core\PLUGIN_PATH' ) ) {
	define( 'Mo\Core\PLUGIN_PATH', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

// Require composer autoloader.
require_once \Mo\Core\PLUGIN_PATH . 'lib/vendor/autoload.php';

// Require globing function.
require_once \Mo\Core\PLUGIN_PATH . 'lib/require-folders.php';

// Require subdirectories.
require_folders( array( 'functions', 'twig', 'actions', 'filter', 'hooks' ) );

// Init plugin instance.
\add_action( 'plugins_loaded', array( '\Mo\Core\Core_Functionality', 'get_instance' ) );

/**
 * Plugin code.
 */
final class Core_Functionality {

	use Helpers;

	const PLUGIN_VERSION = '1.13.1';

	/**
	 * The plugin singleton.
	 *
	 * @var Core_Functionality Class instance.
	 */
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

		// Init Timber.
		new \Timber\Timber();

		// Init Twig Extensions.
		new \Mo\Core\Twig\Twig_Extensions();

		// Add action hooks.
		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		\add_filter( 'script_loader_tag', array( $this, 'async_theme_scripts' ), 10, 2 );

		// Action hooks for get_all_posts().
		\add_action( 'save_post', array( $this, 'delete_all_posts_transient' ) );
		\add_action( 'delete_post', array( $this, 'delete_all_posts_transient' ) );

		// Run custom action hooks.
		\do_action( 'mo_core_cleanup' );

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
	 *
	 * @param string $tag The <script> tag for the enqueued script.
	 * @param string $handle The script's registered handle.
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

	/**
	 * Get all posts of a specific $post_type (with transient caching) as Timber\Post.
	 *
	 * @param string $post_type The post type.
	 * @param string $post_class The post class. Default is \Timber\Post.
	 */
	public static function get_all_posts( $post_type = false, $post_class = '\Timber\Post' ) {

		if ( is_string( $post_type ) && is_string( $post_class ) ) {

			$ids = get_transient( 'mocore_all_' . $post_type );
			if ( ! $ids ) {
				$query = new \WP_Query(
					[
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'fields'         => 'ids',
					]
				);
				$ids   = $query->posts;
				set_transient( 'mocore_all_' . $post_type, $ids, MONTH_IN_SECONDS );
			}

			$posts = new \Timber\PostQuery(
				[
					'post_type' => $post_type,
					'post__in'  => $ids,
					'orderby'   => 'menu_order',
					'order'     => 'ASC',
				],
				$post_class
			);

			return $posts;
		}
		return false;
	}

	/**
	 * Delete transient cache from get_all_posts() on save / update / delete.
	 *
	 * @param int $post_id The post id.
	 */
	public static function delete_all_posts_transient( $post_id ) {

		if ( ! is_int( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		// Delete transient for specific post type.
		if ( $post_type ) {
			delete_transient( 'mocore_all_' . $post_type );
		}
	}

	/**
	 * We provide two environments: 'development' and 'production'.
	 * The development environment can be set by defining either WP_ENV or WP_MO_ENV as 'development'.
	 */
	public static function is_dev() {

		if ( ( defined( 'WP_MO_ENV' ) && WP_MO_ENV === 'development' ) || ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) ) {
			return true;
		}

		return false;
	}
}
