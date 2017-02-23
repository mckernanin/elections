<?php
/**
 * Theme functions
 *
 * @package ElectionTheme
 */

/**
 * Theme functions
 */
class ElectionTheme {

	/**
	 * ElectionTheme Constructor
	 */
	function __construct() {

		add_filter( 'upload_mimes', 		 [ $this, 'mime_types' ] );
		add_filter( 'body_class',   		 [ $this, 'add_slug_body_class' ] );
		add_action( 'send_headers', 	 	 [ $this, 'custom_headers' ] );
		add_action( 'wp_enqueue_scripts', 	 [ $this, 'typekit' ] );
		add_action( 'wp_enqueue_scripts', 	 [ $this, 'scripts_and_styles' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'login_scripts_styles' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'typekit' ] );

		$this->roots_support();
	}

	/**
	 * Add custom allowed media upload file types.
	 *
	 * @param Array $mimes Array of mime types registered.
	 */
	public function mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * Add page slug as a body class.
	 *
	 * @param Array $classes Array of body classes.
	 */
	public function add_slug_body_class( $classes ) {
		global $post;
		if ( isset( $post ) ) {
			$classes[] = $post->post_type . '-' . $post->post_name;
		}
		return $classes;
	}

	/**
	 * Add custom headers.
	 */
	public function custom_headers() {
		header( 'Access-Control-Allow-Origin: *' );
	}

	/**
	 * Add theme support for Soil functions. Helps clean up WordPress markup.
	 * To enable google analytics, add theme support like this: add_theme_support( 'soil-google-analytics', 'UA-XXXXXXXX-Y' );
	 */
	public function roots_support() {
		add_theme_support( 'soil-clean-up' );
		add_theme_support( 'soil-disable-asset-versioning' );
		add_theme_support( 'soil-disable-trackbacks' );
		// add_theme_support( 'soil-jquery-cdn' );
		// add_theme_support( 'soil-js-to-footer' );
		add_theme_support( 'soil-nav-walker' );
		add_theme_support( 'soil-nice-search' );
		add_theme_support( 'soil-relative-urls' );
		add_theme_support( 'soil-google-analytics', 'UA-52435052-4' );
	}

	/**
	 * Add typekit support if needed. Replace URL with one from the kit you create.
	 */
	public function typekit() {
		wp_enqueue_script( 'theme_typekit', 'https://use.typekit.net/xbk1ivk.js' );
		wp_add_inline_script( 'theme_typekit', 'try{Typekit.load({ async: true });}catch(e){}' );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function scripts_and_styles() {
		wp_enqueue_script( 'elections-theme-js', get_stylesheet_directory_URI() . '/assets/js/app.js', [], filemtime( get_stylesheet_directory() . '/assets/js/app.min.js' ) );
		wp_enqueue_style( 'dashicons' );
	}

	public function login_scripts_styles() {
		wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login.css' );
	}
}

new ElectionTheme();
