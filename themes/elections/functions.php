<?php

class ElectionTheme {

	function __construct() {

		add_filter( 'upload_mimes', 		array( $this, 'mime_types' ) );
		add_filter( 'body_class',   		array( $this, 'add_slug_body_class' ) );
		add_action( 'send_headers', 		array( $this, 'custom_headers' ) );
		add_action( 'wp_enqueue_scripts', 	array( $this, 'typekit' ) );
		add_action( 'wp_enqueue_scripts', 	array( $this, 'scripts_and_styles' ) );
		add_action( 'phpmailer_init', array( $this, 'send_smtp_email' ) );


		$this->roots_support();
	}

	/**
	 * Add custom allowed media upload file types.
	 */

	public function mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * Add page slug as a body class.
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
	 */

	public function roots_support() {
		add_theme_support( 'soil-clean-up' );
		add_theme_support( 'soil-disable-asset-versioning' );
		add_theme_support( 'soil-disable-trackbacks' );
		// add_theme_support( 'soil-google-analytics', 'UA-XXXXXXXX-Y' );
		add_theme_support( 'soil-jquery-cdn' );
		add_theme_support( 'soil-js-to-footer' );
		add_theme_support( 'soil-nav-walker' );
		add_theme_support( 'soil-nice-search' );
		add_theme_support( 'soil-relative-urls' );
	}

	/**
	 * Add typekit support if needed. Replace URL with one from the kit you create.
	 */
	public function typekit() {
		wp_enqueue_script( 'theme_typekit', 'https://use.typekit.net/xbk1ivk.js' );
		wp_add_inline_script( 'theme_typekit', 'try{Typekit.load({ async: true });}catch(e){}' );
	}

	public function scripts_and_styles() {
		wp_enqueue_script( 'elections-theme-js', get_stylesheet_directory_URI() . '/assets/js/app.js' );
	}

	function send_smtp_email( $phpmailer ) {

		// Define that we are sending with SMTP
		$phpmailer->isSMTP();

		// The hostname of the mail server
		$phpmailer->Host = "smtp.mailgun.org";

		// Use SMTP authentication (true|false)
		$phpmailer->SMTPAuth = true;

		// SMTP port number - likely to be 25, 465 or 587
		$phpmailer->Port = "587";

		// Username to use for SMTP authentication
		$phpmailer->Username = "postmaster@stagewp.co";

		// Password to use for SMTP authentication
		$phpmailer->Password = "6145c3e334497ba6201708630c71e38d";

		// Encryption system to use - ssl or tls
		$phpmailer->SMTPSecure = "tls";

		$phpmailer->From = "kevin@stagewp.co";
		$phpmailer->FromName = "Kevin McKernan";
	}
}

new ElectionTheme();
