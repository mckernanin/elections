<?php
/**
 * Election Calendar Shortcode.
 *
 * @package OA_Elections
 */

if ( ! is_user_logged_in() ) {
	$message = 'You must be logged in to view this page.';
	echo $message;
} else {
	wp_enqueue_script( 'moment' );
	wp_enqueue_script( 'fullcalendar' );
	wp_enqueue_style( 'fullcalendar' );
?>
<div id="election-calendar"></div>
<button id="schedule-elections">Schedule Selected Elections</button><span id="schedule-response"></span>
<?php } ?>
