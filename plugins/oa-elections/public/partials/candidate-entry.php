<?php
/**
 * Candidate entry shortcode content.
 *
 * @package OA_Elections
 */

if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} elseif ( ! OAE_Util::user_election_rights() && ! current_user_can( 'unit_leader' ) ) {
	echo 'You are not authorized to view this page.';
} else {

	$post_type  = get_post_type();
	$object_id  = get_the_ID();
	$candidates = cmb2_get_metabox( 'candidate_fields', $object_id );
	$candidates->add_hidden_field( array(
		'field_args'  => array(
			'id'      => '_post_id',
			'type'    => 'hidden',
			'default' => $object_id,
		),
	));

	if ( 'oae_election' === $post_type ) {
		$candidates->add_hidden_field( array(
			'field_args'  => array(
				'id'      => 'election_id',
				'type'    => 'hidden',
				'default' => $object_id,
			),
		));

		$metabox_form_options = array(
			'save_button' => 'Add Candidate',
		);
		$form_action = 'create';
	} elseif ( 'oae_candidate' === $post_type ) {
		$metabox_form_options = array(
			'save_button' => 'Save Candidate',
		);
		$form_action = 'update';
	}

	$candidates->add_hidden_field( array(
		'field_args'  => array(
			'id'      => '_form_action',
			'type'    => 'hidden',
			'default' => $form_action,
		),
	));

	echo '<h2>' . esc_html( $candidates->meta_box['title'] ) . '</h2>';
	echo cmb2_get_metabox_form( $candidates, 'candidate_fields', $metabox_form_options );

}
