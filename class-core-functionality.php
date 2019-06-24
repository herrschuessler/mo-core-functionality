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
 * Version:     0.1.0
 * Author:      MONTAGMORGENS GmbH
 * Author URI:  https://www.montagmorgens.com/
 * Text Domain: mo-core
 */

namespace Mo\Core;

defined( 'ABSPATH' ) || die();

require_once( plugin_dir_path( __FILE__ ) . 'lib/vendor/autoload.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/functions/attach_style.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/functions/helpers.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/functions/images.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/functions/svg.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/twig/twig_extensions.php' );

// Init plugin instance.
\add_action( 'plugins_loaded', array( '\Mo\Core\Core_Functionality', 'get_instance' ) );

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
class Core_Functionality {

	use Helpers;

	const PLUGIN_VERSION = '0.1.0';
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

		// Provide Timber
		new \Timber\Timber();
		new \Mo\Core\Twig\Twig_Extensions();
	}
}
