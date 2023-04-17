<?php
/**
 * Custom Taxonomy Factory
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.33.0
 * @phpcs:disable WordPress.Files.FileName
 */

namespace Mo\Core;

/**
 * Our Taxonomy Parent Class.
 */
abstract class Taxonomy {

	/**
	 * The singletons.
	 *
	 * @var array Class instances.
	 */
	protected static $instances = [];

	/**
	 * Constuctor.
	 */
	private function __construct() {
		$this->register(
			$this->get_name(),
			$this->get_args(),
			$this->get_labels(),
		);

		$this->add_hooks();
	}

	/**
	 * Gets a singelton instance of our class.
	 */
	public static function get_instance() {
		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) ) {
				self::$instances[ $class ] = new $class();
		}
		return self::$instances[ $class ];
	}

	/**
	 * Register post type.
	 *
	 * @param string $taxonomy_name Post type name.
	 * @param array  $taxonomy_args Post type args.
	 * @param array  $taxonomy_labels Post type labels.
	 */
	protected function register( string $taxonomy_name, array $taxonomy_args = [], array $taxonomy_labels = [] ) {

		if ( ! is_string( $taxonomy_name ) || empty( $taxonomy_name ) ) {
			return;
		}

		$taxonomy_name = sanitize_title( $taxonomy_name );

		$taxonomy_labels = wp_parse_args(
			$taxonomy_labels,
			[
				'singular' => _x( 'Kategorie', 'Custom Category', 'mo-admin' ),
				'plural'   => _x( 'Kategorien', 'Custom Category', 'mo-admin' ),
				'parent'   => _x( 'Übergeordnete Kategorien', 'Custom Category', 'mo-admin' ),
				'new'      => _x( 'Neue Kategorie', 'Custom Category', 'mo-admin' ),
				'slug'     => _x( 'mo-taxonomy', 'Custom Category', 'mo-admin' ),
			]
		);

		$taxonomy_args = wp_parse_args(
			$taxonomy_args,
			[
				'labels' => $this->compile_labels( $taxonomy_labels ),
			]
		);

		add_action(
			'init',
			function() use ( $taxonomy_name, $taxonomy_args, $taxonomy_labels ) {
				if ( function_exists( 'register_extended_taxonomy' ) ) {
					$post_types    = apply_filters( 'mo_core/register_taxonomy/' . $taxonomy_name . '/post_types', [] ); //phpcs:ignore

					register_extended_taxonomy(
						$taxonomy_name,
						$post_types,
						$taxonomy_args,
						$taxonomy_labels
					);
				}
			},
			11
		);
	}

	/**
	 * Get post type name.
	 *
	 * @param array $taxonomy_labels Labels for singular, plural and image.
	 *
	 * @return array Labels.
	 */
	private function compile_labels( array $taxonomy_labels ) : array {
		return [

			/* translators: Taxonomy singular name */
			'all_items'    => sprintf( _x( 'Alle %s', 'Custom Taxonomy', 'mo-admin' ), $taxonomy_labels['plural'] ),
			/* translators: Taxonomy plural name */
			'edit_item'    => sprintf( _x( '%s bearbeiten', 'Custom Taxonomy', 'mo-admin' ), $taxonomy_labels['singular'] ),
			/* translators: Taxonomy new singular name */
			'add_new_item' => sprintf( _x( ' %s erstellen', 'Custom Taxonomy', 'mo-admin' ), $taxonomy_labels['new'] ),
			'parent_item'  => $taxonomy_labels['parent'],
			'most_used'    => _x( 'Häufig genutzt', 'Custom Taxonomy', 'mo-admin' ),
			'no_item'      => _x( 'Nicht zugeordnet', 'Custom Taxonomy', 'mo-admin' ),
			/* translators: Taxonomy plural name */
			'not_found'    => sprintf( _x( 'Keine %s gefunden', 'Custom Taxonomy', 'mo-admin' ), $taxonomy_labels['singular'] ),
		];
	}

	/**
	 * Add action hooks.
	 */
	abstract protected function add_hooks();

	/**
	 * Get post type name.
	 *
	 * @return string Post type name.
	 */
	abstract public function get_name() : string;

	/**
	 * Get post type args.
	 *
	 * @return array Post type args.
	 */
	abstract protected function get_args() : array;

	/**
	 * Get post type labels.
	 *
	 * @return array Post type labels.
	 */
	abstract protected function get_labels() : array;

}

