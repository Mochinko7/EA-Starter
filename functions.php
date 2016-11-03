<?php
/**
 * EA Starter
 *
 * @package      EAStarter
 * @since        1.0.0
 * @copyright    Copyright (c) 2014, Contributors to EA Genesis Child project
 * @license      GPL-2.0+
 */

// Theme Hooks
require get_template_directory() . '/inc/tha-theme-hooks.php';

// WordPress Cleanup
require get_template_directory() . '/inc/wordpress-cleanup.php';

// Helper Functions
require get_template_directory() . '/inc/helper-functions.php';

// Navigation
require get_template_directory() . '/inc/navigation.php';

// Main Loop Functions
require get_template_directory() . '/inc/loop.php';


if ( ! function_exists( 'ea_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function ea_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on ea, use a find and replace
	 * to change 'ea' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'ea', get_template_directory() . '/languages' );

	// Structural Wraps
	add_theme_support( 'ea-structural-wraps', array( 'header', 'site-inner', 'footer' ) );

	// Editor Styles
	add_editor_style( 'css/editor-style.css' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

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

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'ea' ),
		'mobile'  => esc_html__( 'Mobile Menu', 'ea' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

}
endif;
add_action( 'after_setup_theme', 'ea_setup' );

/**
 * Dont Update the Theme
 *
 * If there is a theme in the repo with the same name, this prevents WP from prompting an update.
 *
 * @since  1.0.0
 * @author Bill Erickson
 * @link   http://www.billerickson.net/excluding-theme-from-updates
 * @param  array $r Existing request arguments
 * @param  string $url Request URL
 * @return array Amended request arguments
 */
function ea_dont_update_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) )
 		return $r; // Not a theme update request. Bail immediately.
 	$themes = json_decode( $r['body']['themes'] );
 	$child = get_option( 'stylesheet' );
	unset( $themes->themes->$child );
 	$r['body']['themes'] = json_encode( $themes );
 	return $r;
 }
add_filter( 'http_request_args', 'ea_dont_update_theme', 5, 2 );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function ea_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ea_content_width', 640 );
}
add_action( 'after_setup_theme', 'ea_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function ea_widgets_init() {

	register_sidebar( ea_widget_area_args( array(
		'name' => esc_html__( 'Primary Sidebar', 'ea' ),
	) ) );

}
add_action( 'widgets_init', 'ea_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function ea_scripts() {
	wp_enqueue_style( 'ea-style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), '1.0' );

	wp_enqueue_script( 'fitvids', get_stylesheet_directory_uri() . '/js/jquery.fitvids.js', array( 'jquery' ), '1.1', true );
	wp_enqueue_script( 'sidr', get_stylesheet_directory_uri() . '/js/jquery.sidr.min.js', array( 'jquery' ), '2.2.1', true );
	wp_enqueue_script( 'ea-global', get_stylesheet_directory_uri() . '/js/global.js', array( 'jquery', 'sidr' ), '1.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ea_scripts' );

/**
 * Add "Styles" drop-down to TinyMCE
 *
 * @since 1.0.0
 * @param array $buttons
 * @return array
 */
function ea_mce_editor_buttons( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'ea_mce_editor_buttons' );

/**
 * Add styles/classes to the TinyMCE "Formats" drop-down
 *
 * @since 1.0.0
 * @param array $settings
 * @return array
 */
function ea_mce_before_init( $settings ) {

	$style_formats = array(
		array(
			'title'    => 'Button',
			'selector' => 'a',
			'classes'  => 'button',
		),
	);
	$settings['style_formats'] = json_encode( $style_formats );
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'ea_mce_before_init' );
