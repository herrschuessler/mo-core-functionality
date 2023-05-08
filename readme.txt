=== MONTAGMORGENS Core Functionality ===
Contributors: herrschuessler
Requires at least: 5.8.0
Tested up to: 6.2.0
Requires PHP: 8.0.0
Stable tag: 1.37.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==

= 1.37.2 =
* Add Mastodon to social_links

= 1.37.1 =
* Update deprecated FILTER_SANITIZE_STRING filter_var constant
* Update dependencies

= 1.37.0 =
* Set coffee admin color scheme for staging sites
* Update dependencies

= 1.36.4 =
* Add a post display state for custom archive page in the page list table
* Update dependencies

= 1.36.3 =
* Add custom post type archive to Yoast SEO breadcrumb path
* Fix rewrite rules for paged archive
* Flush rewrite rules after archive page updates
* Update dependencies

= 1.36.2 =
* Add post_type_archive_permastruct member var to PostType class

= 1.36.1 =
* Update dependencies
* Update Timber to 1.22.1

= 1.36.0 =
* Implement custom post type archive
* Update Timber to 1.21.0 and update other dependencies

= 1.35.0 =
* Implement Borlabs Cookie in youtube_embed function
* Update dependencies

= 1.34.0 =
* Do not generate JPG image sizes if WebP is supported
* Fix fallback image width calculation

= 1.33.0 =
* Add custom taxonomy factory
* Update dependencies

= 1.32.1 =
* Update dependencies
* Update Timber to 1.20.0

= 1.32.0 =
* Add fix for svg-sprite loaded from external domain

= 1.31.4 =
* Implement PR#41 from Upstatement/routes

= 1.31.3 =
* Add instagram and youtube to twig social_links variable

= 1.31.2 =
* Update dependencies
* Adapt twig social_links variable to Yoast updates

= 1.31.1 =
* Update dependencies
* Update Timber to 1.19.2

= 1.31.0 =
* Set unique password protected cookie name to allow multiple instances on dev server to user their own cookie

= 1.30.3 =
* Add social_links Twig var to global namespace

= 1.30.2 =
* Update dependencies (including extended-cpts 5.0)

= 1.30.1 =
* Update dependencies
* Add Borlabs content blocker compatibility to video_embed

= 1.30.0 =
* Add mo_youtube_embed shortcode

= 1.29.2 =
* Add archives label parameter to PostType class

= 1.29.1 =
* PostType->get_name() should be public

= 1.29.0 =
* Add custom post type parent class as wrapper for johnbillion/extended-cpts

= 1.28.5 =
* Update dependencies
* Update Timber to 1.19.1

= 1.28.4 =
* Update dependencies
* Fix fallback image url in the_image_sizes()

= 1.28.3 =
* Fix video_embed layout if no cover image is given

= 1.28.2 =
* Update dependencies
* Update Timber to 1.19.0

= 1.28.1 =
* Update dependencies

= 1.28.0 =
* Add crop parameter to the_image_sizes().

= 1.27.0 =
* Add video_embed() Twig function.

= 1.26.0 =
* Allow passing of link array to the_image_sizes()
* Update dependencies

= 1.25.9 =
* Allow umlaut URLs in SVG paths
* Update dependencies

= 1.25.8 =
* Add type check in tel_link functions
* Update dependencies

= 1.25.7 =
* Add option to disable WebP images by setting `MO_WEBP_QUALITY` to `-1`

= 1.25.6 =
* Add basetheme parameter to the_svg_icon(), the_svg_img(), get_css_asset() and get_js_asset() functions

= 1.25.5 =
* Update dependencies

= 1.25.4 =
* Add aria-hidden to image copyright

= 1.25.3 =
* Update dependencies

= 1.25.2 =
* Add `mo_core_push_style` filter

= 1.25.1 =
* Update Timber to 1.18.2

= 1.25.0 =
* Move admin area cleanup to MONTAGMORGENS Support Plugin

= 1.24.1 =
* Set default WebP quality to 85

= 1.24.0 =
* Allow changing of WebP quality by setting MO_WEBP_QUALITY

= 1.23.0 =
* Add youtube_embed() Twig function.

= 1.22.1 =
* Add test for development env to site-health.

= 1.22.0 =
* Include check for wp_get_environment_type() in is_dev()
* Update Timber to 1.18.1

= 1.21.5 =
* Remove unneccesary WPML admin bar stylesheet

= 1.21.4 =
* Update plugin assets

= 1.21.3 =
* Update Timber to 1.18.0

= 1.21.2 =
* Update Timber to 1.17.0

= 1.21.1 =
* Bugfix release

= 1.21.0 =
* Add Twig function get_svg_content()

= 1.20.0 =
* Cleanup ads in WP admin
* Handle SVG images in the_image_sizes()
* Add Twig function get_image_placeholder_height()

= 1.19.2 =
* Update dependencies

= 1.19.1 =
* Update Timber to 1.16.0
* Update lazysizes

= 1.19.0 =
* Update dependencies, add documentation link to plugin list

= 1.18.2 =
* Update Timber to 1.15.2

= 1.18.1 =
* Add "hammer & wrench" icon before admin_title on DEV installs

= 1.18.0 =
* Add "hammer & wrench" icon before wp_title on DEV installs

= 1.17.2 =
* Fix social_links twig output

= 1.17.1 =
* Add `mo_social_links` filter
* Update Timber to 1.15.1

= 1.17.0 =
* Add whitelabeling

= 1.16.1 =
* Fix stable tag number

= 1.16.0 =
* Update Timber to 1.15.0

= 1.15.0 =
* Prepare for Twig 3

= 1.14.0 =
* Use PSR4 composer autoloading and direct inclusion of required files

= 1.13.2 =
* Get SVG icon path via get_stylesheet_directory_uri(), not get_template_directory_uri()

= 1.13.0 =
* Grant editors access to privacy policy

= 1.12.0 =
* Update Timber to 1.14.0
* Allow development environment to be set by either WP_ENV or WP_MO_ENV

= 1.11.2 =
* Enable fixed HTTP/2 Push of enqueued assets

= 1.11.1 =
* Disable HTTP/2 Push of enqueued assets for now

= 1.11.0 =
* HTTP/2 Push enqueued assets

= 1.10.0 =
* Add function get_js_public_path

= 1.9.0 =
* Add webp image versions ONLY for jpeg images

= 1.8.0 =
* Force different admin color schemes for DEV and LIVE sites

= 1.7.1 =
* Update dependencies (incl. Timber 1.13.0)

= 1.7.0 =
* Add width and height attributes to responsive image source

= 1.6.7 =
* Update dependencies (incl. Timber 1.12.1)

= 1.6.6 =
* Update dependencies

= 1.6.5 =
* Fix template error in image_sizes

= 1.6.4 =
* Fix image_sizes for missing webp-support

= 1.6.3 =
* Fix social_links twig var in case Yoast SEO is not installed

= 1.6.2 =
* Check server webp support

= 1.6.1 =
* Add h-descriptor to lazy-loaded images to fix object-fitted images

= 1.6.0 =
* Update Timber to 1.11.0

= 1.5.0 =
* Cleanup WordPress HTML by removing <head> links and styles. Can be disabled by unhooking mo_core_cleanup action.

= 1.4.0 =
* Add twig filter tel_link

= 1.3.0 =
* Add twig function get_all_posts

= 1.2.0 =
* Add header for Github Updater plugin
* Add license file
* Revert webpack bundle to production mode

= 1.1.0 =
* Pass the_image_sizes() args as array

= 1.0.1 =
* Fix lazyloading

= 1.0.0 =
* Initial release

== Description ==

### Twig functions

#### `attach_style( string stylesheet_name )`

Prints an inline stylesheet link to a theme stylesheet in `assets/css/{dev|dist}/` for modular CSS code.

An explanation of the purpose can be found in [Jake Archibalds blog post “The future of loading CSS”](https://jakearchibald.com/2016/link-in-body/).

#### `debug( mixed var )`
Prints a formated dump of a variable.

#### `image_sizes( Timber\Image image, array args )`

```PHP
// Default args
$args = [
	'ratio'   => null, // Apsect ratio ot the image to generate, null or 0 means no cropping
	'min'     => 300, // Minimal image size to generate
	'max'     => 900, // Maximum image size to generate
	'steps'   => 100, // Increase in image size for each step
	'classes' => null, // CSS class names
	'style'   => null, // Inline style
	'fit'     => false, // object-fitting, can also be 'cover' or 'contain'
	'link'    => false, // A link URL to wrap the image with. Can be a string with a URL or an array with 'url', 'tabindex' and 'target'.
];
```

Prints a picture element with various image sizes and formats, includes lazy-loading via [lazysizes](https://github.com/aFarkas/lazysizes).

You can also use `cover_image_sizes( Timber\Image image, array args )` or `contain_image_sizes( Timber\Image image, array args )` to user object-fitting methods (include CSS in theme stylesheet).

#### `youtube_embed( string youtube_id, int image_id = false )`

Prints a responsive 16:9 iframe element with either a video preview image loaded from YouTube servers or a WordPress image, if passed an optional image id.

On the image, there's a YouTube-style play button. When the user clicks the image or button, the iframe source will be swapped for the actual YouTube player.

**Notice**: Permalink rules have to be flushed once after plugin activation to make this work.

### Image quality settings

The default WebP image quality of 85 can be changed by setting the constant `MO_WEBP_QUALITY` to any integer value between 0 and 100. WebP images can be disabled altogether by setting `MO_WEBP_QUALITY` to `-1`.