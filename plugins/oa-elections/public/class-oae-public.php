<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mckernan.in
 * @since      1.0.0
 *
 * @package    OA_Elections
 * @subpackage OA_Elections/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OA_Elections
 * @subpackage OA_Elections/public
 * @author     Kevin McKernan <kevin@mckernan.in>
 */
class OAE_Public {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_shortcode( 'unit-edit-form', array( $this, 'unit_edit_form' ) );
		add_shortcode( 'election-calendar', array( $this, 'election_calendar' ) );
		add_shortcode( 'election-list', array( $this, 'election_list' ) );
		add_shortcode( 'candidate-entry', array( $this, 'shortcode_candidate_entry' ) );
		add_shortcode( 'election-team-signup', array( $this, 'shortcode_election_team_signup' ) );

		add_action( 'init', array( $this, 'rewrites' ) );
		add_action( 'cmb2_init', array( $this, 'form_submission_handler' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

		add_filter( 'body_class', array( $this, 'section_body_class' ) );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function scripts_and_styles() {

		wp_register_script( 'moment', 			plugin_dir_url( __FILE__ ) . '/js/moment.min.js', array( 'jquery' ) );
		wp_register_script( 'fullcalendar', 	plugin_dir_url( __FILE__ ) . '/js/fullcalendar.min.js', array( 'jquery' ) );
		wp_register_style( 'fullcalendar', 		plugin_dir_url( __FILE__ ) . '/css/fullcalendar.min.css' );
		wp_enqueue_script( 'election-scripts',  plugin_dir_url( __FILE__ ) . '/js/oa-elections-public.js', array( 'jquery' ) );
		wp_enqueue_script( 'selectize', 		plugin_dir_url( __FILE__ ) . '/js/selectize.min.js', array( 'jquery' ) );
		wp_enqueue_style( 'selectize', 			plugin_dir_url( __FILE__ ) . '/css/selectize.min.css' );
		wp_add_inline_script( 'selectize', 		'jQuery(document).ready( function($) { $("select").selectize(); });', array( 'selectize' ) );
	}

	/**
	 * Add editing_section rewrite
	 */
	function rewrites() {
		add_rewrite_rule(
			'^election/([^/]*)/([^/]*)/?',
			'index.php?oae_election=$matches[1]&editing_section=$matches[2]',
			'top'
		);
		add_rewrite_tag( '%editing_section%', '([^&]+)' );
	}

	/**
	 * Unit edit form shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string       Form HTML markup
	 */
	function unit_edit_form( $atts = array() ) {
		ob_start();
		include( 'partials/unit-edit-form.php' );
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Election calendar shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string       Form HTML markup
	 */
	function election_calendar( $atts = array() ) {
		ob_start();
		include( 'partials/election-calendar.php' );
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Election list shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string       Form HTML markup
	 */
	function election_list( $atts = array() ) {
		ob_start();
		include( 'partials/election-list.php' );
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Election Team Signup shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string       Form HTML markup
	 */
	function shortcode_election_team_signup( $atts = array() ) {
		ob_start();
		include( 'partials/election-team-signup.php' );
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Shortcode to display a CMB2 form for a post ID.
	 * @param  array  $atts Shortcode attributes
	 * @return string       Form HTML markup
	 */
	public function shortcode_candidate_entry( $atts = array() ) {
		ob_start();
		include( 'partials/candidate-entry.php' );
		$output = ob_get_clean();
		return $output;
	}

	public function section_body_class( $classes ) {
		$section = get_query_var( 'editing_section' );
		if ( $section ) {
			$classes[] = 'section-' . $section;
		}
		return $classes;
	}

	public function form_submission_handler() {
		if ( empty( $_POST ) ) {
			return false;
		}

		if ( ! isset( $_POST['submit-cmb'], $_POST['object_id'], $_POST['_form_action'] ) ) {
			return false;
		}
		new OAE_CMB_Form_Handler( $_POST );
	}

}