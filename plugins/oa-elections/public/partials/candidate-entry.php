<?php
if ( ! is_user_logged_in() ) {
	echo 'You must be logged in to view this page.';
} else {

	$object_id = get_the_ID();

	$candidates = cmb2_get_metabox( 'candidate_fields', $object_id );

	$candidates->add_hidden_field( array(
		'field_args'  => array(
			'id'      => '_post_id',
			'type'    => 'hidden',
			'default' => $object_id,
		),
	) );

	echo '<h2>' . $candidates->meta_box['title'] . '</h2>';
	echo cmb2_get_metabox_form( $candidates, 'candidate_fields' );

}
