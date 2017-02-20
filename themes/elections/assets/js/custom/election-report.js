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
