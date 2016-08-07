(function( $ ) {
	'use strict';

	$(document).ready( function() {

		if ( $('#election-calendar').length ) {
			$('#election-calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				eventSources: [{
					url: '/wp-json/oa-elections/v1/election-dates'}]
			});
			$(window).trigger('resize');
		}
	});

})( jQuery );
