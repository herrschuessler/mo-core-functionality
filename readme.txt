=== MONTAGMORGENS Core Functionality ===
Contributors: herrschuessler
Requires at least: 5.0.0
Tested up to: 5.3.2
Requires PHP: 7.0.0
Stable tag: 1.12.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==

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
