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
 * Version:           1.31.0
 * Requires at least: 5.0.0
 * Requires PHP:      7.2
 * Author:            MONTAGMORGENS GmbH
 * Author URI:        https://www.montagmorgens.com/
 * License:           GNU General Public License v.2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mo-core
 * Domain Path:       /languages
 * GitHub Plugin URI: montagmorgens/mo-core-functionality
 */

namespace Mo\Core;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

// Define absolute path to plugin root.
if ( ! defined( 'Mo\Core\PLUGIN_PATH' ) ) {
	define( 'Mo\Core\PLUGIN_PATH', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

// Define URL path to plugin root.
if ( ! defined( 'Mo\Core\PLUGIN_URL' ) ) {
	define( 'Mo\Core\PLUGIN_URL', wp_normalize_path( plugin_dir_url( __FILE__ ) ) );
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
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/password-protected.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/push-assets.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/set-dev-color-scheme.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/set-dev-title-icon.php';
require_once \Mo\Core\PLUGIN_PATH . 'lib/hooked_functions/twig-social-links.php';

// Init plugin instance.
\add_action( 'plugins_loaded', [ '\Mo\Core\Core_Functionality', 'get_instance' ] );

/**
 * Plugin code.
 */
final class Core_Functionality {

	use Helpers;
	use Video_Embed;
	use Youtube_Embed;

	const PLUGIN_VERSION = '1.31.0';

	/**
	 * The plugin slug is an identifier used in the $plugins array in the all_plugins filter hook.
	 *
	 * @var string Plugin slug.
	 */
	private $slug = 'mo-core-functionality/class-core-functionality.php';

	/**
	 * WebP Image Quality.
	 *
	 * @var int The WebP image quality (0-100).
	 */
	public static $webp_quality = 85;

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

		// Get image quality from wp-config.
		if ( defined( 'MO_WEBP_QUALITY' ) && is_int( MO_WEBP_QUALITY ) && ( 0 <= MO_WEBP_QUALITY ) && ( MO_WEBP_QUALITY <= 100 ) ) {
			self::$webp_quality = MO_WEBP_QUALITY;
		} elseif ( defined( 'MO_WEBP_QUALITY' ) && is_int( MO_WEBP_QUALITY ) && ( -1 === MO_WEBP_QUALITY ) ) {
			self::$webp_quality = false;
		}

		// Init Timber.
		new \Timber\Timber();

		// Init Twig Extensions.
		new \Mo\Core\Twig_Extensions();

		// Add action hooks.
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		\add_filter( 'script_loader_tag', [ $this, 'async_theme_scripts' ], 10, 2 );
		\add_filter( 'site_status_tests', [ $this, 'add_env_test' ] );
		\add_action( 'init', [ $this, 'load_textdomain' ] );
		\add_action( 'init', [ $this, 'add_video_embed_endpoint' ] );
		\add_action( 'init', [ $this, 'add_youtube_embed_endpoint' ] );
		\add_action( 'init', [ $this, 'add_youtube_embed_shortcode' ] );
		\add_action( 'template_redirect', [ $this, 'load_video_embed_template' ] );
		\add_action( 'template_redirect', [ $this, 'load_youtube_embed_template' ] );

		// Admin hooks.
		if ( is_admin() ) {
			\add_filter( 'plugin_action_links_' . $this->slug, [ $this, 'plugin_action_links' ], 10, 4 );
		}

		// Whitelabel hooks.
		if ( defined( 'MO_CORE_WHITELABEL' ) && MO_CORE_WHITELABEL === true ) {
			\add_action( 'all_plugins', [ $this, 'filter_plugin_info' ] );
		}

		// Action hooks for get_all_posts().
		\add_action( 'save_post', [ $this, 'delete_all_posts_transient' ] );
		\add_action( 'delete_post', [ $this, 'delete_all_posts_transient' ] );

		// Run custom action hooks.
		\do_action( 'mo_core_cleanup' );

	}

	/**
	 * Load text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mo-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
		$scripts_to_defer = [ 'mo-images' ];
		foreach ( $scripts_to_defer as $defer_script ) {
			if ( $defer_script === $handle ) {
				return str_replace( ' src', ' async src', $tag );
			}
		}
		return $tag;
	}

	/**
	 * Add test for development env to site-health.
	 *
	 * @param array $tests The site-health tests.
	 * @since 1.22.1
	 */
	public function add_env_test( $tests ) {
		$tests['direct']['mo_env'] = [
			'label' => __( 'WordPress development environment test', 'mo-core' ),
			'test'  => [ $this, 'env_test' ],
		];
		return $tests;
	}

	/**
	 * The site-health test for development env adds a recommendation to turn off revelopment mode.
	 *
	 * @since 1.22.1
	 */
	public function env_test() {
		$result = [
			'label'       => __( 'Die Entwicklungsumgebung ist deaktiviert', 'mo-core' ),
			'status'      => 'good',
			'badge'       => [
				'label' => __( 'Performance' ),
				'color' => 'blue',
			],
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'Wenn die Entwicklungsumgebung aktiviert ist, erzeugt das Theme ggf. Output, der in Produktionsumgebungen nicht empfohlen wird.', 'mo-core' ),
				__( 'Die Entwicklungsumgebung ist aktiv, wenn die PHP-Konstantent <code>WP_ENVIRONMENT_TYPE</code>, <code>WP_ENV</code> oder <code>WP_MO_ENV</code> in der <code>wp-config.php</code> auf <code>\'development\'</code> gesetzt sind.', 'mo-core' )
			),
			'actions'     => '',
			'test'        => 'mo_env',
		];

		if ( $this->is_dev() ) {
			$result['status'] = 'recommended';
			$result['label']  = __( 'Die Entwicklungsumgebung sollte deaktiviert werden', 'mo-core' );
		}

		return $result;
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

}
