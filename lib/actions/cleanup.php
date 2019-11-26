<?php
namespace Mo\Core\Action;

/**
 * Disable nag notices.
 * Make sure they aren't already defined!
 */
if ( ! defined( 'DISABLE_NAG_NOTICES' ) ) {
	define( 'DISABLE_NAG_NOTICES', true );
}

/**
 * Cleanup WordPress HTML.
 */
function cleanup() {
	// Remove really simple discovery link.
	\remove_action( 'wp_head', 'rsd_link' );

	// Remove wlwmanifest.xml (needed to support windows live writer).
	\remove_action( 'wp_head', 'wlwmanifest_link' );

	// Remove generator tag from RSS feeds.
	\remove_action( 'atom_head', 'the_generator' );
	\remove_action( 'comments_atom_head', 'the_generator' );
	\remove_action( 'rss_head', 'the_generator' );
	\remove_action( 'rss2_head', 'the_generator' );
	\remove_action( 'commentsrss2_head', 'the_generator' );
	\remove_action( 'rdf_header', 'the_generator' );
	\remove_action( 'opml_head', 'the_generator' );
	\remove_action( 'app_head', 'the_generator' );
	\add_filter( 'the_generator', '__return_false' );

	// Remove the next and previous post links.
	\remove_action( 'wp_head', 'adjacent_posts_rel_link', 10 );
	\remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	\remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	\remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

	// Remove the shortlink url from header.
	\remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	\remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

	// Remove WordPress generator version.
	\remove_action( 'wp_head', 'wp_generator' );
	if ( array_key_exists( 'sitepress', $GLOBALS ) ) {
		\remove_action( 'wp_head', array( $GLOBALS['sitepress'], 'meta_generator_tag' ) );
	}

	// Remove the annoying:
	// <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>.
	\add_filter( 'show_recent_comments_widget_style', '__return_false' );

	// Remove emoji styles and script from header.
	if ( ! is_admin() ) {
		\remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		\remove_action( 'wp_print_styles', 'print_emoji_styles' );
		\remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		\remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		\remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		\add_filter( 'emoji_svg_url', '__return_false' );
		\add_filter(
			'xmlrpc_methods',
			function( $methods ) {
				unset( $methods['pingback.ping'] );
				return $methods;
			}
		);
	}

}

\add_action( 'mo_core_cleanup', '\Mo\Core\Action\cleanup' );
