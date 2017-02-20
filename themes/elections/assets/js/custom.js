jQuery(function($){
$(document).ready(function() {
if ( $('body').hasClass('section-ballots') ) {
	var $ballot 		  = $('.ballots');
	var pageHeight        = $('.site-main').outerHeight();
	var ballots           = Math.floor( $('.ballots:nth-of-type(odd)').length );
	var ballotHeight      = $('.ballots').outerHeight();
	var totalBallotHeight = ballots * ballotHeight;
	while (totalBallotHeight < pageHeight) {
		$ballot.clone().insertAfter('.ballots:first-of-type');
		ballots           = Math.floor( $('.ballots:nth-of-type(odd)').length );
		totalBallotHeight = ballots * ballotHeight;
	}
	$ballot.clone().insertAfter('.ballots:first-of-type');
}




function scheduled_elections() {

	$.ajax({
		url: '/wp-json/oa-elections/v1/reports/elections_by_chapter',
		dataType: 'json',
	}).done(function(response) {
		$('.scheduled').text(response.sum);
		// Split timestamp and data into separate arrays
		var labels = [],
			data = [];
		$.each( response.results, function(i, count) {
			labels.push(i);
			data.push(count);
		});

		// Create the chart.js data structure using 'labels' and 'data'
		var tempData = {
			labels: labels,
			legend: '',
			datasets: [{
				backgroundColor: [
					'rgba(255, 99, 132, 0.6)',
					'rgba(54, 162, 235, 0.6)',
					'rgba(255, 206, 86, 0.6)',
					'rgba(75, 192, 192, 0.6)',
					'rgba(153, 102, 255, 0.6)',
					'rgba(255, 159, 64, 0.6)',
					'rgba(134,189,74, 0.6)'
				],
				strokeColor: 'rgba(151,187,205,1)',
				pointColor: 'rgba(151,187,205,1)',
				pointStrokeColor: '#fff',
				pointHighlightFill: '#fff',
				pointHighlightStroke: 'rgba(151,187,205,1)',
				data: data
			}]
		};

		// Get the context of the canvas element we want to select
		var ctx = document.getElementById('scheduled_elections');

		// Instantiate a new chart
		new Chart( ctx, {
			type: 'pie',
			data: tempData
		});
	});
}

function elected_candidates() {

	$.ajax({
		url: '/wp-json/oa-elections/v1/reports/candidates_by_chapter',
		dataType: 'json',
	}).done(function(response) {
		$('.elected').text(response.sum);

		// Split timestamp and data into separate arrays
		var labels = [],
			data = [];
		$.each( response.results, function(i, count) {
			labels.push(i);
			data.push(count);
		});

		// Create the chart.js data structure using 'labels' and 'data'
		var tempData = {
			labels: labels,
			legend: '',
			datasets: [{
				backgroundColor: [
					'rgba(255, 99, 132, 0.6)',
					'rgba(54, 162, 235, 0.6)',
					'rgba(255, 206, 86, 0.6)',
					'rgba(75, 192, 192, 0.6)',
					'rgba(153, 102, 255, 0.6)',
					'rgba(255, 159, 64, 0.6)',
					'rgba(134,189,74, 0.6)'
				],
				strokeColor: 'rgba(151,187,205,1)',
				pointColor: 'rgba(151,187,205,1)',
				pointStrokeColor: '#fff',
				pointHighlightFill: '#fff',
				pointHighlightStroke: 'rgba(151,187,205,1)',
				data: data
			}]
		};

		// Get the context of the canvas element we want to select
		var ctx = document.getElementById('candidates_elected');

		// Instantiate a new chart
		new Chart( ctx, {
			type: 'pie',
			data: tempData
		});
	});
}
if ( $('body').hasClass('page-stats') ) {
	Chart.defaults.global.legend.position = 'bottom';

	scheduled_elections();
	elected_candidates();
}

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

if ( $('body').hasClass('section-report') ) {

	var electionReport = {
		ID: document.body.className.match(/(^|\s)postid-(\d+)(\s|$)/)[2], // get post ID from body class
		registeredActiveYouth: 0,
		youthAttendance: 0,
		electionOne: {
			ballots: 0,
			required: 0
		},
		electionTwo: {
			ballots: 0,
			required: 0
		},
		candidates: []
	};

	$('#registeredActiveYouth').on( 'change', function() {
		electionReport.registeredActiveYouth = Math.abs( $(this).val() );
	});

	$('#youthAttendance').on( 'change', function() {
		electionReport.youthAttendance = Math.abs( $(this).val() );
		if ( electionReport.youthAttendance <= ( electionReport.registeredActiveYouth / 2 ) ) {
			alert( 'Please check your input, over 50% of active members must be in attendance.' );
		}
	});

	$('#electionOneBallots').on( 'change', function() {
		electionReport.electionOne.ballots = Math.abs( $(this).val() );
		electionReport.electionOne.required = Math.ceil( electionReport.electionOne.ballots / 2 );
		$('#electionOneRequired').val(electionReport.electionOne.required);
	});

	$('#electionTwoBallots').on( 'change', function() {
		electionReport.electionTwo.ballots = Math.abs( $(this).val() );
		electionReport.electionTwo.required = Math.ceil( electionReport.electionTwo.ballots / 2 );
		$('#electionTwoRequired').val(electionReport.electionTwo.required);
	});

	$('input[type="checkbox"]').on( 'change', function() {
		if (this.checked) {
			electionReport.candidates.push( Math.abs( $(this).attr('name') ) );
		}
	});

	$('#submit-election-results').on( 'click', function() {
		$(this).attr('disabled', true);
		$.ajax({
			url: '/wp-json/oa-elections/v1/set-election-results',
			type: 'post',
			data: {
				report: electionReport
			},
			success: function() {
				Raven.captureMessage( 'Election Report Submitted', {
					level: 'info',
					extra: electionReport,
				});
				setTimeout( function() {
					location.reload();
				}, 4000);
			},
			fail: function(response) {
				Raven.captureMessage( response, {
					level: 'error',
					extra: electionReport,
				});
				alert(response);
			},
		});
	});

}

$('#_oa_election_unit_location').attr('required', true);
$('#_oa_candidate_address_address_2').attr('required', false);
$('#_oa_election_unit_location').on('change', function() {
	var location = $(this).val();
	$('#_oa_election_unit_address_text').val(location);
});
if ( $('.cmb-form').length ) {
	$('.cmb-form').parsley();
}

});
});