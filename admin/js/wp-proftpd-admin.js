(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$(function() {
		function getRandomColor() {
			var letters = '0123456789ABCDEF'.split('');
			var color = '#';
			for (var i = 0; i < 6; i++ ) {
				color += letters[Math.floor(Math.random() * 16)];
			}
			return color;
		}
		
		if ( $('#proftpd_logs').length ) {
			$('#proftpd_logs').dataTable({
				dom: 'Bfrtip',
				processing: true,
				serverSide: true,
				buttons: [
					{
						text: 'Refresh',
						action: function ( e, dt, node, config ) {
							dt.ajax.reload();
						}
					},
					{
						text: 'Crear Log',
						action: function ( e, dt, node, config ) {
							if (confirm('Are you sure you want clear all logs?')) {
								$.ajax({
									type : "GET",
									dataType : "json",
									url : wp_proftp.ajax_url_logs_clear,
									success: function(response) {
										if(response.success){
											dt.ajax.reload();
										}
									}
								});
							}
						},
						footer: true
					},
					'copyHtml5',
					'csvHtml5',
					'pdfHtml5'
				],
				"order": [[ 0, "desc" ]],
				ajax: wp_proftp.ajax_url_logs_load
			});
		}
		
		if ( $('#proftpd_login_chart').length ) {
			$.ajax({
				url: wp_proftp.ajax_url_login_chart,
				type: 'GET',
				success:function(response){
					var users = [];
					var sum = [];
					
					var data = response.data;
					var i;
					for(i=0; i < data.length; i++){
						users.push(response.data[i].user_login);
						sum.push(response.data[i].count);
					}
					
					//console.log(users);
					
					var chartdata = {
						labels: users,
						datasets: [
							{
							label: 'Users',
							backgroundColor: '#49e2ff',
                            borderColor: '#46d5f1',
                            hoverBackgroundColor: '#CCCCCC',
                            hoverBorderColor: '#666666',
							data:sum
							}
						] 
					};

					var ctx = $('#proftpd_login_chart');

					var barGraph = new Chart(ctx, {
						type:'bar',
						data: chartdata,
						options: {
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true
									}
								}]
							}
						}
					});
				},
				error:function(data){
					console.log(data);
				}
			});
		}
		
		if ( $('#proftpd_operations_chart').length ) {
			$.ajax({
				url: wp_proftp.ajax_url_operations_chart,
				type: 'GET',
				success:function(response){
					
					var users = [];
					var sum = [];
					var colors = [];
					
					var data = response.data;
					var i;
					for(i=0; i < data.length; i++){
						users.push(response.data[i].user_login);
						sum.push(response.data[i].operations);
						colors.push(getRandomColor());
					}
					
					var chartdata = {
						labels: users,
						datasets: [
							{
							backgroundColor: colors,	
							data:sum,
							borderWidth: 1
							}
						] 
					};

					var ctx = $('#proftpd_operations_chart');

					var pieGraph = new Chart(ctx, {
						type:'pie',
						data: chartdata
					});
				},
				error:function(data){
					console.log(data);
				}
			});
		}
		
		if ( $('#proftpd_activity_chart').length ) {
			$.ajax({
				url: wp_proftp.ajax_url_activity_chart,
				type: 'GET',
				success:function(response){
					
					var dates = [];
					var operations = [];
					var colors = [];
					
					console.log(response.data);
					var data = response.data;
					var i;
					for(i=0; i < data.length; i++){
						console.log(i);
						console.log(response.data[i]);
						var dt = new Date(response.data[i].date),
							month = '' + (dt.getMonth() + 1),
							day = '' + dt.getDate(),
							year = dt.getFullYear();
						var dt_formatted = [month, day, year].join('-');
						dates.push(dt_formatted);
						operations.push(response.data[i].operations);
					}
					
					var chartdata = {
						labels: dates,
						datasets: [
							{
							label: 'Operations',
							data:operations,
							borderColor: '#fe8b36',
							backgroundColor: '#fe8b36',
							}
						] 
					};

					var ctx = $('#proftpd_activity_chart');
					
					var lineGraph = new Chart(ctx, {
						type:'line',
						data: chartdata,
						fill: false,
						responsive: true,
						scales: {
							xAxes: [{
								type: 'time',
								display: true,
								scaleLabel: {
									display: true,
									labelString: "Date",
								}
							}],
							yAxes: [{
								ticks: {
									beginAtZero: true,
								},
								display: true,
								scaleLabel: {
									display: true,
									labelString: "Operations",
								}
							}]
						}
					});
					
					console.log(dates);
				},
				error:function(data){
					console.log(data);
				}
			});
		}
		
		
		
		// Admin Settings 
		$('.button-counter').click(function(e){
			e.preventDefault();
			var button_classes, value = +$('.counter').val();
			button_classes = $(e.currentTarget).prop('class');        
			if(button_classes.indexOf('up_count') !== -1){
				value = (value) + 1;            
			} else {
				value = (value) - 1;            
			}
			value = value < 0 ? 0 : value;
			$('.counter').val(value);
		});  
		$('.counter').click(function(){
			$(this).focus().select();
		});
		 
	
	});
})( jQuery );
