if ( $('#adult-nominations').length ) {
	$('.status-button').on( 'click', function() {
		var status = $(this).data('status');
		var nomID = $(this).data('id');
		$('button[data-id="' + nomID + '"]').removeClass('active');
		$.ajax({
			url: '/wp-json/oa-elections/v1/set-nomination-status',
			type: 'post',
			data: {
				id: nomID,
				status: status,
			},
			success: function() {
				setTimeout( function() {
					$(this).addClass('active');
				}, 4000);
			},
			fail: function(response) {
				alert(response);
			},
		});
	});
}
