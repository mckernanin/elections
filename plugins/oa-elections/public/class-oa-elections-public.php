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
class OA_Elections_Public {

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

		add_action( 'init', array( $this, 'rewrites' ) );
		add_action( 'cmb2_init', array( $this, 'unit_edit_form_submission_handler' ) );
		add_action( 'cmb2_init', array( $this, 'candidate_edit_form_submission_handler' ) );
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
		wp_enqueue_style( 'selectize', 			plugin_dir_url( __FILE__ ) . '/css/selectize.min.css' );
		wp_enqueue_script( 'selectize', 		plugin_dir_url( __FILE__ ) . '/js/selectize.min.js', array( 'jquery' ) );
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
	 * Election edit form save action
	 */
	public function unit_edit_form_submission_handler() {

		if ( empty( $_POST ) ) {
			return false;
		}

		if ( ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
			return false;
		}

		if ( 'unit_fields' !== $_POST['object_id'] ) {
			return false;
		}

		$email_address = $_POST['_oa_election_leader_email'];

		if ( null === username_exists( $email_address ) ) {

			$password = wp_generate_password( 12, false );
			$user_id  = wp_create_user( $email_address, $password, $email_address );

			wp_update_user(
				array(
					'ID'          => $user_id,
					'nickname'    => $email_address,
					'first_name'  => $_POST['_oa_election_leader_fname'],
					'last_name'	  => $_POST['_oa_election_leader_lname'],
				)
			);

			$user = new WP_User( $user_id );
			$user->set_role( 'unit-leader' );
			wp_new_user_notification( $user_id, null, 'both' );

		} else {
			$user = get_user_by( 'email', $email_address );
			$user_id = $user->ID;
		}

		$post_id = $_POST['_post_id'];
		$current_post_type = get_post_type( $post_id );
		if ( 'oae_election' !== $current_post_type ) {
			$user_id = get_current_user_id();
			$post_data = array(
				'post_type'   => 'oae_election',
				'post_status' => 'publish',
				'post_author' => $user_id ? $user_id : 1,
				'post_title'  => 'Troop ' . $_POST['_oa_election_unit_number'],
				'post_name'   => 'troop-' . $_POST['_oa_election_unit_number'] . '-' . date( 'Y' ),
			);
			$post_id = wp_insert_post( $post_data, true );

			if ( ! is_int( $post_id ) ) {
				wp_die( var_dump( $post_id ) );
			}
		}
		unset( $_POST['_post_id'] );

		// Get CMB2 metabox object
		$cmb = cmb2_get_metabox( 'unit_fields', $post_id );
		$post_data = array();
		// Check security nonce
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return $cmb->prop( 'submission_error', wp_die( 'security_fail', __( 'Security check failed.' ) ) );
		}

		/**
		 * Fetch sanitized values
		 */
		$sanitized_values = $cmb->get_sanitized_values( $_POST );

		// Loop through remaining (sanitized) data, and save to post-meta
		foreach ( $sanitized_values as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = array_filter( $value );
				if ( ! empty( $value ) ) {
					update_post_meta( $post_id, $key, $value );
				}
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
		/*
		 * Redirect back to the form page with a query variable with the new post ID.
		 * This will help double-submissions with browser refreshes
		 */
		$args = array(
			'p' => $post_id,
			'update' => true,
		);

		if ( 'oae_election' !== $current_post_type ) {
			$args['new_election'] = true;
			$args['update'] = false;
			$this->new_election_notification( $post_id );
			wp_set_object_terms( $post_id, 'requested', 'oa_election_status' );
		}
		// var_dump( get_post($post_id) );

		wp_redirect( esc_url_raw( add_query_arg( $args ) ) );
		exit;
	}

	/**
	 * New election notification function.
	 * @todo create a class of notifications.
	 */
	public function new_election_notification( $post_id ) {
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ){
			return;
		}

		$fields = get_post_custom( $post_id );

		$post_title = get_the_title( $post_id );
		$post_url = get_permalink( $post_id );
		$subject = 'Election Submitted for  ' . $post_title;

		$message = print_r( $fields, true );

		// Send email to admin.
		$mail = wp_mail( 'kevin@mckernan.in', $subject, $message );

		$copied_message = 'You were copied on this message by ' . get_the_author( $post_id ) . '<br />' . $message;
		$copied_recipients = get_post_meta( $post_id, '_oa_election_leader_copied_emails', true );

		foreach ( $copied_recipients as $email ) {
			$mail = wp_mail( $email, $subject, $copied_message );
		}
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

	public function candidate_edit_form_submission_handler() {

		// If no form submission, bail
		if ( empty( $_POST ) ) {
			return false;
		}

		if ( ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
			return false;
		}

		if ( 'candidate_fields' !== $_POST['object_id'] ) {
			return false;
		}

		$post_id = $_POST['_post_id'];
		unset( $_POST['_post_id'] );

		// Get CMB2 metabox object
		$cmb = cmb2_get_metabox( 'candidate_fields', $post_id );
		$post_data = array();
		// Check security nonce
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return $cmb->prop( 'submission_error', wp_die( 'security_fail', __( 'Security check failed.' ) ) );
		}

		/**
		 * Fetch sanitized values
		 */
		$sanitized_values = $cmb->get_sanitized_values( $_POST );

		// Loop through remaining (sanitized) data, and save to post-meta
		foreach ( $sanitized_values as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = array_filter( $value );
				var_dump($key);
				if ( ! empty( $value ) ) {
					$value = serialize($value);
					$update = update_post_meta( $post_id, $key, $value );
					var_dump($post_id);
					var_dump($update);
				}
			} else {
				$update = update_post_meta( $post_id, $key, $value );
			}
		}
		/*
		 * Redirect back to the form page with a query variable with the new post ID.
		 * This will help double-submissions with browser refreshes
		 */
		$args = array(
			'p'               => $post_id,
			'update'          => true,
			'editing_section' => 'candidates',
		);

		// wp_redirect( esc_url_raw( add_query_arg( $args ) ) );
		exit;
	}

}
