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
