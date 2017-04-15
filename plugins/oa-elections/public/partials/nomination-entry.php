<?php
/**
 * Nomination entry shortcode content.
 *
 * @package OA_Elections
 */

if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} else {

	$post_type  = get_post_type();
	$object_id  = get_the_ID();
	$nominations = cmb2_get_metabox( 'nomination_fields', $object_id );
	$nominations->add_hidden_field( array(
		'field_args'  => array(
			'id'      => '_post_id',
			'type'    => 'hidden',
			'default' => $object_id,
		),
	));

	if ( 'oae_election' === $post_type ) {
		$nominations->add_hidden_field( array(
			'field_args'  => array(
				'id'      => 'election_id',
				'type'    => 'hidden',
				'default' => $object_id,
			),
		));

		$metabox_form_options = array(
			'save_button' => 'Add Nomination',
		);
		$form_action = 'create';
	} elseif ( 'oae_nomination' === $post_type ) {
		$metabox_form_options = array(
			'save_button' => 'Save Nomination',
		);
		$form_action = 'update';
	}

	$nominations->add_hidden_field( array(
		'field_args'  => array(
			'id'      => '_form_action',
			'type'    => 'hidden',
			'default' => $form_action,
		),
	));

	echo '<h2>' . esc_html( $nominations->meta_box['title'] ) . '</h2>';
	echo cmb2_get_metabox_form( $nominations, 'nomination_fields', $metabox_form_options );

} // End if().
