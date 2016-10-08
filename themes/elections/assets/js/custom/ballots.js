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
		console.log(totalBallotHeight);
	}
}
