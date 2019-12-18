<?php
/**
 * Require folders
 *
 * Require all files in folders.
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since      1.0.0
 */

namespace Mo\Core;

/**
 * Include files by globbing pattern
 *
 * @param array $paths Subfolders of /lib/ to require.
 */
function require_folders( $paths ) {
	if ( is_array( $paths ) ) {
		foreach ( $paths as $path ) {
			$files = glob( \Mo\Core\PLUGIN_PATH . '/lib/' . $path . '/*.php' );
			if ( ! empty( $files ) ) {
				foreach ( $files as $file ) {
					$file = \Mo\Core\PLUGIN_PATH . '/lib/' . $path . '/' . basename( $file );
					require_once $file;
				}
			}
		}
	}
}
