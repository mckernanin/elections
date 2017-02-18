<?php
/**
 * Election Calendar Shortcode.
 *
 * @package OA_Elections
 */

if ( ! is_user_logged_in() ) {
	 echo 'You must be <a href="/wp-admin/">logged in</a> to view this page.';
} elseif ( ! OAE_Util::user_election_rights() ) {
	 echo 'You are not authorized to view this page.';
} else {
	wp_enqueue_script( 'moment' );
	wp_enqueue_script( 'fullcalendar' );
	wp_enqueue_style( 'fullcalendar' );
	if ( current_user_can( 'chapter-admin' ) ) {
		$chapter = OAE_Util::get_user_chapter( get_current_user_id() );
	} elseif ( current_user_can( 'administrator' ) ) {
		$chapter = 'all';
	}
?>
<script>
jQuery(document).ready( function($) {

		$('#election-calendar').fullCalendar({
			weekends: false,
			businessHours: {
				dow: [ 1, 2, 3, 4, 5 ],
				start: '17:00',
				end: '22:00',
			},
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			eventSources: [{
				url: '/wp-json/oa-elections/v1/election-dates?chapter=<?php echo esc_js( $chapter ); ?>'
			}]
		});
		$(window).trigger('resize');
});
</script>
<div id="election-calendar"></div>
<button id="schedule-elections">Schedule Selected Elections</button><span id="schedule-response"></span>
<?php } // End if().
