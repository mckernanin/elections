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

		add_filter( 'upload_mimes', 		array( $this, 'mime_types' ) );
		add_filter( 'body_class',   		array( $this, 'add_slug_body_class' ) );
		add_action( 'send_headers', 		array( $this, 'custom_headers' ) );
		add_action( 'wp_enqueue_scripts', 	array( $this, 'typekit' ) );
		add_action( 'wp_enqueue_scripts', 	array( $this, 'scripts_and_styles' ) );
		add_action( 'phpmailer_init', 		array( $this, 'send_smtp_email' ) );
		add_action( 'template_redirect', 	array( $this, 'home_redirect' ) );
		add_action( 'login_head', 			array( $this, 'my_custom_login' ) );
		add_action( 'login_head', 			array( $this, 'typekit' ) );

		add_filter( 'wp_mail_from', 			array( $this, 'mail_from_filter' ) );
		add_filter( 'wp_mail_content_type', 	array( $this, 'mail_content_type_filter' ) );

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

	/**
	 * Enqueue scripts and styles.
	 */
	public function scripts_and_styles() {
		wp_enqueue_script( 'elections-theme-js', get_stylesheet_directory_URI() . '/assets/js/app.js' );
	}

	/**
	 * Send all emails via mailgun.
	 *
	 * @param object $phpmailer Object containing phpmailer configuration.
	 */
	function send_smtp_email( $phpmailer ) {
		// @codingStandardsIgnoreStart
		// Define that we are sending with SMTP.
		$phpmailer->isSMTP();

		// The hostname of the mail server.
		$phpmailer->Host = 'smtp.mailgun.org';

		// Use SMTP authentication (true|false).
		$phpmailer->SMTPAuth = true;

		// SMTP port number - likely to be 25, 465 or 587.
		$phpmailer->Port = '587';

		// Username to use for SMTP authentication.
		$phpmailer->Username = 'postmaster@tahosalodge.org';

		// Password to use for SMTP authentication.
		$phpmailer->Password = MAILGUN_PASSWORD;

		// Encryption system to use - ssl or tls.
		$phpmailer->SMTPSecure = 'tls';

		$phpmailer->setFrom = 'elections@tahosalodge.org';
		$phpmailer->FromName = 'Tahosa Lodge Elections Team';
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Home page redirects for logged in users
	 */
	public function home_redirect() {
		if ( is_front_page() && is_home() ) {
			if ( is_current_user_logged_in() && current_user_can( 'chapter_admin' ) ) {
				$user = wp_get_current_user();
				$query = new WP_Query( array(
					'author'         => $user->data->ID,
					'post_type'      => 'oae_election',
					'posts_per_page' => 1,
					'no_found_rows'  => true,
					'fields' 		 => 'ids',
				));
				$election = current( $query->posts );
				wp_safe_redirect( get_the_permalink( $election->ID ) );
				exit;
			}
		}
	}

	function my_custom_login() {
		echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/login.css" />';
	}

	function mail_from_filter() {
		return 'elections@tahosalodge.org';
	}

	function mail_content_type_filter() {
		return 'text/html';
	}
}

new ElectionTheme();
