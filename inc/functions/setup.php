<?php
/**
 * Voting App functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package JobstairsVoting
 * @subpackage Voting_App
 * @since 1.0.0
 * 
 * Sets up theme defaults and registers support for various WordPress features.
 * Make theme available for translation.
 * Let WordPress manage the document title.
 * Enable support for Post Thumbnails on posts and pages.
 * Switch default core markup for search form, comment form, and comments to output valid HTML5.
 * Enqueue scripts and styles.
 * Dequeue jQuery Migrate Script in WordPress.
 * Customize Login Body CSS.
 * Customize Login Header Url.
 * Customize Login Header Title.
 * Remove WP Version Name.
 * Disable Xmlrpc.
 * Disable the emoji's.
 * Filter function used to remove the tinymce emoji plugin.
 * Remove emoji CDN hostname from DNS prefetching hints.
 */

if ( ! function_exists( 'jobstairs_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function jobstairs_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty Nineteen, use a find and replace
		 * to change 'jobstairs' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'jobstairs', get_template_directory() . '/languages' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);	
	}
endif;
add_action( 'after_setup_theme', 'jobstairs_setup' );

/**
 * Sets up theme custome post types and registers them.
 */
function jobstairs_post_types() {

    // Image Post Type
    register_post_type('image', array(
		"description" => "",
        'public' => true,
		'show_ui' => true,
		"show_in_menu" => true,
		'has_archive' => false,
		'show_in_rest' => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"query_var" => false,
        'labels' => array (
            'name' => 'Images',
            'add_new_item' => 'Add New Image',
            'edit_item' => 'Edit Image',
            'all_items' => 'All Images',
            'singular_name' => 'Image'
		),
		'supports' => array('title'),
		'menu_position' => 4,
        'menu_icon' => 'dashicons-format-image'
	));

    // Like Post Type
    register_post_type('like', array(
		"description" => "",
        'public' => false,
		'show_ui' => true,
		"show_in_menu" => true,
		'has_archive' => false,
		'show_in_rest' => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"query_var" => false,
        'labels' => array(
            'name' => 'Likes',
            'add_new_item' => 'Add New Like',
            'edit_item' => 'Edit Like',
            'all_items' => 'All Likes',
            'singular_name' => 'Like'
		),
		'supports' => array('title'),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-heart'
    ));
}
add_action('init', 'jobstairs_post_types');

/**
 * Enqueue scripts and styles.
 */
function jobstairs_scripts() {
	wp_enqueue_style( 'jobstairs-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'jobstairs_scripts' );

/**
 * Dequeue jQuery Migrate Script in WordPress.
 */
function binspired_remove_jquery_migrate( &$scripts) {
    if(!is_admin()) {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.4.1' );
    }
}
add_filter( 'wp_default_scripts', 'binspired_remove_jquery_migrate' );

/**
 * Customize Login Body CSS.
 */
function binspired_custom_login_body_css() {
	wp_enqueue_style('binspired_main_styles', get_stylesheet_uri(), NULL, '1.0');
}
add_action( 'login_enqueue_scripts', 'binspired_custom_login_body_css' );

/**
 * Customize Login Header Url.
 */
function binspired_custom_login_header_url() {
	return esc_url(site_url('/'));
}
add_filter( 'login_headerurl', 'binspired_custom_login_header_url' );

/**
 * Customize Login Header Title.
 */
function binspired_custom_login_header_title() {
	return get_bloginfo('name');
}
add_filter( 'login_headertitle', 'binspired_custom_login_header_title' );

/**
 * Remove WP Version Name.
 */
function binspired_remove_version() {
	return '';
}
add_filter('the_generator', 'binspired_remove_version');

/**
 * Disable Xmlrpc.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

function binspired_remove_pingback($headers) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'binspired_remove_pingback' );

/**
 * Disable the emoji's.
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}
   	return $urls;
}