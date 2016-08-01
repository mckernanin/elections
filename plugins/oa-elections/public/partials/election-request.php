<?php
$atts = shortcode_atts( array(
	'post_author' => $user_id ? $user_id : 1, // Current user, or admin
	'post_status' => 'pending',
	'post_type'   => 'oa_election', // Only use first object_type in array
), $atts, 'cmb-frontend-form' );

$metabox_id = $atts['metabox_id'];
$object_id  = get_the_ID();

$cmb = cmb2_get_metabox( 'unit_metabox', $object_id );
echo '<h2>' . $cmb->meta_box['title'] . '</h2>';
echo cmb2_get_metabox_form( $cmb, 'unit_metabox' );
