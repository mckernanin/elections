<?php
/**
 * Template Name: Election Redirect
 */

if ( is_user_logged_in() && current_user_can( 'unit-leader' ) ) {
	$user = wp_get_current_user();
	$query = new WP_Query( array(
		'author'         => $user->data->ID,
		'post_type'      => 'oae_election',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'fields'         => 'ids',
	));
	$election = current( $query->posts );
	wp_safe_redirect( get_the_permalink( $election ) );
	exit;
} elseif ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url() . '/login' );
} else {
	wp_safe_redirect( home_url() );
}
