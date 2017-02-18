

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
