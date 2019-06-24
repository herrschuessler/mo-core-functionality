<?php
namespace Mo\Core\Filter;

/*
* Improve backend performance
* @see https://www.advancedcustomfields.com/blog/acf-pro-5-5-13-update/
*/
\add_filter( 'acf/settings/remove_wp_meta_box', '__return_true' );
