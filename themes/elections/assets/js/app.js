jQuery(function($){
$(document).ready(function() {
$('#election-calendar').on( 'click', '.fc-event', function(e) {
	var $otherDates = $('*[class*="unit-"]').filter(function () {
		return this.className.match(/(?:^|\s)unit-/);
	});

	if ( -1 != $(this).attr('href').indexOf('unit_date') ) {
		e.preventDefault();
		$otherDates.removeClass('selected-election');
		$(this).toggleClass('selected-election');
	}
});

$('#schedule-elections').on( 'click', function() {
	var selectedElections = {};
	selectedElections.elements = $('.selected-election');

	if ( selectedElections.elements.length ) {
		selectedElections.data = [];
		var electionConfirm = confirm('Do you want to schedule these elections? This action cannot be undone, and will notify the units.');

		if (true == electionConfirm) {

			$.each(selectedElections.elements, function() {
				var data = $(this).attr('href').split('_');
				data[0] = data[0].replace('#', '');
				var election = {
					postID: data[0],
					selectedDate: data[3]
				};
				selectedElections.data.push(election);
			});

			$.ajax({
				url: '/wp-json/oa-elections/v1/schedule-election',
				type: 'post',
				data: {
					elections: selectedElections.data
				},
				success: function(response) {
					$('#election-calendar').fullCalendar('refetchEvents');
					$('#schedule-response').text(response.message);
					console.log('scheduled!');
				},
				fail: function(response) {
					alert(response);
				},
			});
		}
	}
});

});
});
