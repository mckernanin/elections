$(document).ready( function() {

	if ( $('#election-calendar').length ) {
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
				url: '/wp-json/oa-elections/v1/election-dates'}]
		});
		$(window).trigger('resize');
	}
});
