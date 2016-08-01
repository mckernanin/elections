<?php
if ( ! is_user_logged_in() ) {
	$message = 'You must be logged in to view this page.';
	echo $message;
} else {
?>

<script>
jQuery(document).ready(function($) {

	// page is now ready, initialize the calendar...

	$('#calendar').fullCalendar({

		eventSources: [
			{
				url: '/wp-json/oa-elections/v1/election-dates'
			}
		]
	})

});
</script>
<div id="calendar">

</div>

<?php } ?>
