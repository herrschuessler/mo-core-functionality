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
 * Plugin Name:       MONTAGMORGENS Core Functionality
 * Description:       Dieses Plugin stellt die benötigten Funktionen für alle MONTAGMORGENS-WordPress-Themes zur Verfügung.
 * Version:           1.19.2
 * Requires at least: 5.0.0
 * Requires PHP:      7.2
 * Author:            MONTAGMORGENS GmbH
 * Author URI:        https://www.montagmorgens.com/
 * License:           GNU General Public License v.2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mo-core
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

// Require functions.
require_once \Mo\Core\PLUGIN_PATH . 'lib/functions/attach-style.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/functions/get-asset.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/functions/support.php';

// Require hooked functions.
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/acf.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/cleanup.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/grant-editor-menu-cap.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/grant-editor-privacy-cap.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/push-assets.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/set-dev-color-scheme.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/set-dev-title-icon.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/twig-social-links.php';


// Init plugin instance.
\add_action( 'plugins_loaded', array( '\Mo\Core\Core_Functionality', 'get_instance' ) );

/**
 * Plugin code.
 */
final class Core_Functionality {

	use Helpers;

	const PLUGIN_VERSION = '1.19.2';

	/**
	 * The plugin slug is an identifier used in the $plugins array in the all_plugins filter hook.
	 *
	 * @var string Plugin slug.
	 */
	private $slug = 'mo-core-functionality/class-core-functionality.php';

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
		new \Mo\Core\Twig_Extensions();

		// Add action hooks.
		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		\add_filter( 'script_loader_tag', array( $this, 'async_theme_scripts' ), 10, 2 );

		// Admin hooks.
		if ( is_admin() ) {
			\add_filter( 'plugin_action_links_' . $this->slug, array( $this, 'plugin_action_links' ), 10, 4 );
			\add_action( 'mo_core_cleanup', array( $this, 'cleanup_admin' ) );
		}

		// Whitelabel hooks.
		if ( defined( 'MO_CORE_WHITELABEL' ) && MO_CORE_WHITELABEL === true ) {
			\add_action( 'all_plugins', array( $this, 'filter_plugin_info' ) );
		}

		// Action hooks for get_all_posts().
		\add_action( 'save_post', array( $this, 'delete_all_posts_transient' ) );
		\add_action( 'delete_post', array( $this, 'delete_all_posts_transient' ) );

		// Run custom action hooks.
		\do_action( 'mo_core_cleanup' );

	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @param array  $actions associative array of action names to anchor tags.
	 * @param string $plugin_file plugin file name.
	 * @param array  $plugin_data associative array of plugin data from the plugin file headers.
	 * @param string $context plugin status context, ie 'all', 'active', 'inactive', 'recently_active'.
	 *
	 * @return array associative array of plugin action links.
	 */
	public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
		return array_merge( [ 'documentation' => '<a href="' . plugin_dir_url( __FILE__ ) . 'doc/namespaces/Mo.Core.html">' . __( 'Documentation' ) . '</a>' ], $actions );
	}

	/**
	 * Whitelabel plugin info.
	 *
	 * To activate whitelableing of this plugin, the following constants should be defined
	 * in wp-config.php:
	 *
	 * define( 'MO_CORE_WHITELABEL', true );
	 * define( 'MO_CORE_WHITELABEL_THEME', 'Name of our theme' ); // optional
	 *
	 * @param array $plugins Installed plugins.
	 */
	public function filter_plugin_info( $plugins ) {
		if ( ! isset( $plugins[ $this->slug ] ) ) {
				return $plugins;
		}

		$theme_name = 'Theme';

		if ( defined( 'MO_CORE_WHITELABEL_THEME' ) && is_string( MO_CORE_WHITELABEL_THEME ) ) {
			$theme_name = MO_CORE_WHITELABEL_THEME;
		}

		$plugins[ $this->slug ]['Name']        = sprintf( '%s Core Functionality', $theme_name );
		$plugins[ $this->slug ]['Title']       = sprintf( '%s Core Functionality', $theme_name );
		$plugins[ $this->slug ]['Description'] = sprintf( 'Dieses Plugin stellt die benötigten Grund-Funktionen für das WordPress-Theme %s zur Verfügung.', '<em>' . $theme_name . '</em>' );
		$plugins[ $this->slug ]['Author']      = '';
		$plugins[ $this->slug ]['AuthorURI']   = '';
		$plugins[ $this->slug ]['AuthorName']  = '';

		return $plugins;
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
	 * @param string|bool $post_type The post type.
	 * @param string      $post_class The post class. Default is \Timber\Post.
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
	 * Hide other plugins promo and advertisement crap in WP Admin.
	 */
	public static function cleanup_admin() {

		// iThemes Security.
		if ( is_plugin_active( 'better-wp-security/better-wp-security.php' ) ) {
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'itsec' ) ) {
						\wp_enqueue_style( 'mo-plugin-itsec-cleanup', \plugins_url( '/assets/css/plugin-itsec-cleanup.css', __FILE__ ), null, self::PLUGIN_VERSION );
					}
				}
			);
		}

		// Yoast SEO.
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'wpseo' ) ) {
						\wp_enqueue_style( 'mo-plugin-wpseo-cleanup', \plugins_url( '/assets/css/plugin-wpseo-cleanup.css', __FILE__ ), null, self::PLUGIN_VERSION );
					}
				}
			);
		}

		// Google Analytics Germanized.
		if ( is_plugin_active( 'ga-germanized/ga-germanized.php' ) ) {
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'ga-germanized' ) ) {
						\wp_enqueue_style( 'mo-plugin-ga-germanized-cleanup', \plugins_url( '/assets/css/plugin-ga-germanized-cleanup.css', __FILE__ ), null, self::PLUGIN_VERSION );
					}
				}
			);
		}

	}
}
