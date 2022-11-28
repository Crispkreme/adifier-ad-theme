document.addEventListener("DOMContentLoaded", function(event) { 

	var $ = jQuery;

	var $chartSelect = $('.advert-chart');
	var $noData = $('.no-data');
	var $chartPanel = $('#dashboard-chart');
	var $toRemove = $('.to-remove');
	var $months = $('.advert-chart-months');
	var chart;
	var data;

	$months.on('change', function(){
		let val = $(this).val();
		let max = Math.max( ...data[val].values );

		if( typeof chart !== 'undefined' ){
			chart.destroy();
		}
		$chartPanel.show();
		$noData.addClass('hidden');

		chart = new Chart($chartPanel, {
			type: 'line',
			data: {
				labels: data[val].labels,
				datasets: [{
					data: data[val].values,
					backgroundColor: adifier_data.main_color,
					borderColor: adifier_data.main_color,
					fill: false,
					borderWidth: 2,
					tension: 0
				}]
			},
			options: {
				responsive: true,
				legend:{
					display: false
				},							
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							max: max < 10 ? max + 1 : ( max + 3 ) % 2 == 0 ? max + 3 : max + 2,
							stepSize: max < 10 ? 1 : Math.round( ( max + 3) / 10 )
						}
					}]
				}
			}
		});
	});

	$chartSelect.on('change', function(){
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data:{
				action: 'chart_data',
				adifier_nonce: adifier_data.adifier_nonce,
				advert_id: $chartSelect.val()
			},
			dataType: 'JSON',
			success: function( response ){
				$toRemove.remove();
				if( response.empty == true ){
					$chartPanel.hide();
					$noData.removeClass('hidden');
				}
				else{
					let val = '';

					data = response.data;

					$months.html('');
					response.months.forEach(function(item){
						$months.append( `<option value="${item}">${item}</option>` );
						val = item;
					});

					$months.prop('disabled', false);
					$months.val( val ).trigger('change');
				}
			}
		});
	});

	$(window).load(function(){
		$chartSelect.prop('disabled', false);
	});
});