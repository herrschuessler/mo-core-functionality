<?php
/**
 * Run ACF filters
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 */

namespace Mo\Core\Filter;

/**
 * Improve backend performance
 *
 * @see https://www.advancedcustomfields.com/blog/acf-pro-5-5-13-update/
 */
\add_filter( 'acf/settings/remove_wp_meta_box', '__return_true' );
