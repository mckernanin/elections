<?php
$object_id  = get_the_ID();

$election = cmb2_get_metabox( 'user_fields', $object_id );

$election->add_hidden_field( array(
	'field_args'  => array(
		'id'    => '_post_id',
		'type'  => 'hidden',
		'default' => $object_id,
	),
));

$election->add_field( array(
	'name' => 'First Name',
	'id'   => 'fname',
	'type' => 'text',
) );

echo '<h2>' . esc_html( $election->meta_box['title'] ) . '</h2>';
echo cmb2_get_metabox_form( $election, 'user_fields' );
