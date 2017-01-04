<?php
if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view or edit an election.';
} else {
	$object_id  = get_the_ID();
	$election = cmb2_get_metabox( 'chapter_fields', $object_id );
	$election->add_hidden_field( array(
		'field_args'  => array(
			'id'    => '_post_id',
			'type'  => 'hidden',
			'default' => $object_id,
		),
	) );


	echo '<h2>' . esc_html( $election->meta_box['title'] ) . '</h2>';
	echo cmb2_get_metabox_form( $election, 'chapter_fields' );
}
