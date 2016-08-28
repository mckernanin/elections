<?php
$object_id  = get_current_user_id();

$prefix = '_oa_election_user_';

$team = cmb2_get_metabox( 'user_fields', $object_id );

$team->add_hidden_field( array(
	'field_args'  => array(
		'id'      => '_post_id',
		'type'    => 'hidden',
		'default' => $object_id,
	),
));

$team->add_hidden_field( array(
	'field_args'  => array(
		'id'      => '_form_action',
		'type'    => 'hidden',
		'default' => 'create',
	),
));

echo '<h2>' . esc_html( $team->meta_box['title'] ) . '</h2>';
echo cmb2_get_metabox_form( $team, 'user_fields' );
