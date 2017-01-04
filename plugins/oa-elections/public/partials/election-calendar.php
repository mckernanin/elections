<?php
/**
 * Election Calendar Shortcode.
 *
 * @package OA_Elections
 */

if ( ! is_user_logged_in() ) {
	echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} else {
	wp_enqueue_script( 'moment' );
	wp_enqueue_script( 'fullcalendar' );
	wp_enqueue_style( 'fullcalendar' );
?>
<div id="election-calendar"></div>
<button id="schedule-elections">Schedule Selected Elections</button><span id="schedule-response"></span>
<?php } ?>
