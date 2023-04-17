<?php
/**
 * Core Functionality Helper Functions
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 * @phpcs:disable WordPress.Files.FileName
 */

namespace Mo\Core;

trait Helpers {

	/**
	 * We provide two environments: 'development' and 'production'.
	 * The development environment can be set by defining either WP_ENVIRONMENT_TYPE, WP_ENV or WP_MO_ENV as 'development'.
	 */
	public static function is_dev() {

		// New in WP 5.5.
		if ( function_exists( 'wp_get_environment_type' ) ) {
			if ( wp_get_environment_type() === 'development' ) {
				return true;
			}
		}

		// Check for our own constants.
		if ( ( defined( 'WP_MO_ENV' ) && WP_MO_ENV === 'development' ) || ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prints debug data. If called from the WP Admin, output will be printed to
	 * admin_notices.
	 *
	 * @param mixed $var The variable to be inspected.
	 */
	public static function debug( $var ) {
		ob_start();
		// phpcs:disable
		var_dump( $var );
		// phpcs:enable
		$result = ob_get_clean();

		if ( \is_admin() ) {
			\add_action(
				'admin_notices',
				function() use ( $result ) {
					if ( ! self::is_dev() ) {
						return;
					}
					echo '<div class="notice notice-info is-dismissible"><pre class="debug">';
					echo \esc_html( $result );
					echo '</pre></div>';
				}
			);

		} else {
			echo '<pre class="debug">';
			echo \esc_html( $result );
			echo '</pre>';
		}
	}

	/**
	 * Prints message to WP Admin.
	 *
	 * @param string       $message The message to print.
	 * @param string|false $title The message title.
	 * @param string|null  $type The message type (error, warning or success).
	 */
	public static function admin_message( $message, $title = false, $type = 'info' ) {
		if ( \is_admin() ) {
			\add_action(
				'admin_notices',
				function() use ( $message, $title, $type ) {
					if ( ! self::is_dev() || ! is_string( $message ) || ! is_string( $type ) ) {
						return;
					}
					printf( '<div class="notice notice-%s">', esc_attr( $type ) );
					if ( is_string( $title ) ) {
						printf( '<p><strong>%s</strong></p>', esc_html( $title ) );
					}
					echo '<p>';
					echo \wp_kses_post( $message );
					echo '</p></div>';
				}
			);
		}
	}

	/**
	 * Prints error message to WP Admin.
	 *
	 * @param string       $message The message to print.
	 * @param string|false $title The message title.
	 */
	public static function admin_error_message( $message, $title = false ) {
		self::admin_message( $message, $title, $type = 'error' );
	}

	/**
	 * Prints warning message to WP Admin.
	 *
	 * @param string       $message The message to print.
	 * @param string|false $title The message title.
	 */
	public static function admin_warning_message( $message, $title = false ) {
		self::admin_message( $message, $title, $type = 'warning' );
	}

	/**
	 * Prints success message to WP Admin.
	 *
	 * @param string       $message The message to print.
	 * @param string|false $title The message title.
	 */
	public static function admin_success_message( $message, $title = false ) {
		self::admin_message( $message, $title, $type = 'success' );
	}
}
