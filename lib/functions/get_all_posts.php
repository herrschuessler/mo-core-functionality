<?php
namespace Mo\Core;

/*
 * Get all posts of a specific $post_type (with transient caching) as Timber\Post.
 */
function get_all_posts( $post_type = false, $post_class = '\Timber\Post' ) {

	if ( is_string( $post_type ) && is_string( $post_class ) ) {

		if ( ! $ids = get_transient( 'mocore_all_' . $post_type ) ) {
			$query = new \WP_Query(
				[
					'post_type'       => $post_type,
					'post_status'     => 'publish',
					'posts_per_page'  => -1,
					'fields'          => 'ids',
				]
			);
			$ids = $query->posts;
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

/*
 * Delete transient cache on save / delete.
 */
function delete_all_posts_transient( $post_id ) {
	$post_type = get_post_type( $post_id );
	delete_transient( 'mocore_all_' . $post_type );
}
add_action( 'save_post', '\Mo\Core\delete_all_posts_transient' );
add_action( 'delete_post', '\Mo\Core\delete_all_posts_transient' );
