<?php
/**
 * Support Functions
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.11.0
 */

namespace Mo\Core;

/**
 * Determine if a given string ends with a given substring.
 *
 * @param  string       $haystack The string.
 * @param  string|array $needles The substring(s).
 * @return bool
 * @link   https://github.com/laravel/framework/blob/6.x/src/Illuminate/Support/Str.php
 */
function ends_with( $haystack, $needles ) {
	foreach ( (array) $needles as $needle ) {
		if ( substr( $haystack, -strlen( $needle ) ) === (string) $needle ) {
			return true;
		}
	}
	return false;
}
