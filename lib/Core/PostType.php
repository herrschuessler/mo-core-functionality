<?php
/**
 * Custom Post Type Factory
 *
 * @category   Plugin
 * @package    Mo\Core
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.29.0
 */

namespace Mo\Core;

/**
 * Our Post Type Parent Class.
 */
abstract class PostType {

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
	 * @param string $post_type_name Post type name.
	 * @param array  $post_type_args Post type args.
	 * @param array  $post_type_labels Post type labels.
	 */
	protected function register( string $post_type_name, array $post_type_args = [], array $post_type_labels = [] ) {

		$post_type_labels = wp_parse_args(
			$post_type_labels,
			[
				'singular'         => _x( 'Artikel', 'Custom Post Type', 'mo-admin' ),
				'plural'           => _x( 'Artikel', 'Custom Post Type', 'mo-admin' ),
				'image'            => _x( 'Bild', 'Custom Post Type', 'mo-admin' ),
				'slug'             => _x( 'mo', 'Custom Post Type slug', 'mo-admin' ),
				'enter_title_here' => _x( 'Titel hier eingeben', 'Custom Post Type Enter title here', 'mo-admin' ),
			]
		);

		$post_type_args = wp_parse_args(
			$post_type_args,
			[
				'labels'           => $this->compile_labels( $post_type_labels ),
				'enter_title_here' => $post_type_labels['enter_title_here'],
				'admin_cols'       => $this->get_admin_cols(),
				'admin_filters'    => $this->get_admin_filters(),
			]
		);

		add_action(
			'init',
			function() use ( $post_type_name, $post_type_args, $post_type_labels ) {
				if ( function_exists( 'register_extended_post_type' ) ) {

					// Register post type.
					register_extended_post_type(
						$post_type_name,
						$post_type_args,
						$post_type_labels
					);

					// Add taxonomies.
					$taxonomies = $this->get_taxonomies();
					if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
						foreach ( $taxonomies as $taxonomy ) {
							add_filter( 'mo_core/register_taxonomy/' . sanitize_title( $taxonomy ) . '/post_types', [ $this, 'add_taxonomy' ], 10, 1 );
						}
					}
				}
			},
			10
		);
	}

	/**
	 * Get post type name.
	 *
	 * @param array $post_type_labels Labels for singular, plural and image.
	 *
	 * @return array Labels.
	 */
	private function compile_labels( array $post_type_labels ) : array {
		return [
			'add_new'                  => _x( 'Erstellen', 'Custom Post Type', 'mo-admin' ),
			'add_new_item'             => _x( 'Neuer Inhalt', 'Custom Post Type', 'mo-admin' ),
			'new_item'                 => _x( 'Neuer Inhalt', 'Custom Post Type', 'mo-admin' ),
			/* translators: Post type singular name */
			'edit_item'                => sprintf( _x( '%s bearbeiten', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
			/* translators: Post type singular name */
			'view_item'                => sprintf( _x( '%s ansehen', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
			/* translators: Post type plural name */
			'view_items'               => sprintf( _x( '%s ansehen', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			/* translators: Post type plural name */
			'search_items'             => sprintf( _x( '%s suchen', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			/* translators: Post type plural name */
			'not_found'                => sprintf( _x( 'Keine %s gefunden', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			/* translators: Post type plural name */
			'not_found_in_trash'       => sprintf( _x( 'Keine %s im Papierkorb gefunden', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			'parent_item_colon'        => _x( 'Übergeordneter Inhalt', 'Custom Post Type', 'mo-admin' ),
			/* translators: Post type plural name */
			'all_items'                => sprintf( _x( 'Alle %s', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			/* translators: Post type plural name */
			'archives'                 => isset( $post_type_labels['archives'] ) ? $post_type_labels['archives'] : sprintf( _x( 'Listenseite %s', 'Custom Post Type', 'mo-admin' ), $post_type_labels['plural'] ),
			'attributes'               => _x( 'Attribute', 'Custom Post Type Category', 'mo-admin' ),
			'insert_into_item'         => _x( 'In Inhalt einfügen', 'Custom Post Type Category', 'mo-admin' ),
			'uploaded_to_this_item'    => _x( 'Zu diesem Inhalt hochgeladen', 'Custom Post Type Category', 'mo-admin' ),
			'featured_image'           => $post_type_labels['image'],
			/* translators: Post type singular name */
			'get_featured_image'       => sprintf( _x( '%s auswählen', 'Custom Post Type', 'mo-admin' ), $post_type_labels['image'] ),
			/* translators: Post type singular name */
			'remove_featured_image'    => sprintf( _x( '%s entfernen', 'Custom Post Type', 'mo-admin' ), $post_type_labels['image'] ),
			/* translators: Post type singular name */
			'use_featured_image'       => sprintf( _x( 'Als %s verwenden', 'Custom Post Type', 'mo-admin' ), $post_type_labels['image'] ),
			/* translators: Post type singular name */
			'item_published'           => sprintf( _x( '%s veröffentlicht', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
			/* translators: Post type singular name */
			'item_published_privately' => sprintf( _x( '%s privat veröffentlicht', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
			/* translators: Post type singular name */
			'item_reverted_to_draft'   => sprintf( _x( '%s als Entwurf gespeichert', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
			'item_scheduled'           => _x( 'Veröffentlichung des Inhalts geplant', 'Custom Post Type Category', 'mo-admin' ),
			/* translators: Post type singular name */
			'item_updated'             => sprintf( _x( '%s aktualisiert', 'Custom Post Type', 'mo-admin' ), $post_type_labels['singular'] ),
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

	/**
	 * Get admin columns.
	 *
	 * @return array Admin columns setting.
	 */
	abstract protected function get_admin_cols() : array;

	/**
	 * Get admin filters.
	 *
	 * @return array Admin filters setting.
	 */
	abstract protected function get_admin_filters() : array;

	/**
	 * Get taxonomies.
	 *
	 * @return array Taxonomies names.
	 */
	protected function get_taxonomies() {
		return [];
	}

	/**
	 * Add taxonomy callback.
	 * Hooked into 'mo_core/register_taxonomy/{$taxonomy}/post_types.
	 *
	 * @param array $post_types Post types.
	 *
	 * @return array Post types.
	 */
	public function add_taxonomy( $post_types ) {
		array_push( $post_types, $this->get_name() );
		return $post_types;
	}

}
