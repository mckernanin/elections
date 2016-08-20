<?php
/**
 * Form submission handler for Unit Edit form.
 *
 * @package OA_Elections
 */

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
	wp_set_object_terms( $post_id, 'requested', 'oae_status' );
}
// var_dump( get_post($post_id) );

wp_redirect( esc_url_raw( add_query_arg( $args ) ) );
exit;
