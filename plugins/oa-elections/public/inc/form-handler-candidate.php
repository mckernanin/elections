<?php
/**
 * Form hanlder for candidate form.
 *
 * @package OA_Elections
 */

/**
 * Validate the input data.
 */
if ( empty( $_POST ) ) {
	return false;
}

if ( ! isset( $_POST['submit-cmb'], $_POST['object_id'], $_POST['_form_action'] ) ) {
	return false;
}

if ( 'candidate_fields' !== $_POST['object_id'] ) {
	return false;
}

/**
 * Get metabox, check nonces.
 */
$post_id = $_POST['_post_id'];
unset( $_POST['_post_id'] );
$cmb = cmb2_get_metabox( 'candidate_fields', $post_id );
if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
	return $cmb->prop( 'submission_error', wp_die( 'security_fail', esc_html( 'Security check failed.' ) ) );
}

$action = $_POST['_form_action'];
unset( $_POST['_form_action'] );

/**
 * Fetch sanitized values
 */
$sanitized_values = $cmb->get_sanitized_values( $_POST );

if ( 'create' === $action ) {
	$election_id = $post_id;
	$user_id = get_current_user_id();
	$post_data = array(
		'post_type'   => 'oae_candidate',
		'post_status' => 'publish',
		'post_author' => $user_id ? $user_id : 1,
		'post_title'  => $sanitized_values['_oa_candidate_fname'] . ' ' . $sanitized_values['_oa_candidate_lname'],
		'post_name'   => $sanitized_values['_oa_candidate_bsa_id'],
	);
	$post_id = wp_insert_post( $post_data, true );
	$candidates = OAE_Fields::get( 'candidates', $election_id );
	if ( ! is_array( $candidates ) ) {
		$candidates = [$post_id];
	} else {
		$candidates[] = $post_id;
	}

	OAE_Fields::update( 'candidates', $candidates, $election_id );
	if ( ! is_int( $post_id ) ) {
		wp_die( var_dump( $post_id ) );
	}
}

// Get CMB2 metabox object
$post_data = array();

// Loop through remaining (sanitized) data, and save to post-meta
foreach ( $sanitized_values as $key => $value ) {
	if ( is_array( $value ) ) {
		$value = array_filter( $value );
		var_dump($key);
		if ( ! empty( $value ) ) {
			$value = serialize($value);
			$update = update_post_meta( $post_id, $key, $value );
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
	'editing_section' => 'add-candidate',
);

wp_safe_redirect( esc_url_raw( add_query_arg( $args ) ) );
exit;
