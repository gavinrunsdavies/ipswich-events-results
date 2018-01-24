<div class="section"> 
	<h2 id="ekiden-race-title"></h2>
	<div class="center-panel" id="ekiden-race-results">
	</div>
</div>
<script type="text/javascript">
	<?php if (isset($_GET['eventId']) && isset($_GET['period']) && isset($_GET['leg'])): ?>
	jQuery(document).ready(function ($) {
		
			getRaceResult(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['period']; ?>, <?php echo $_GET['leg']; ?>);		
		
		function setEventName(name) {			
			$('#ekiden-race-title').html(name);
		}		
		
		function getRaceResult(eventId, year, leg) {
      var description = 'Race results for ' + year + ', leg ' + leg;
			var tableName = 'ekiden-race-results-table-';
			var tableHtml = '';
			var tableRow = '<tr><th>Position</th><th>Name</th><th>Time</th><th>Category</th><th>Info</th></tr>';
			tableHtml += '<table class="table table-striped table-bordered no-wrap" id="' + tableName + eventId + '">';
			tableHtml += '<caption style="text-align:center;font-weight:bold;font-size:1.5em">' + description + '</caption>';
			tableHtml += '<thead>';
			tableHtml += tableRow;
			tableHtml += '</thead>';
			tableHtml += '</table>';
			$('#ekiden-race-results').append(tableHtml);
			
			var table = $('#'+tableName + eventId).DataTable({				
				dom: 'tBip',
				buttons: {
					buttons: [{
					  extend: 'print',
					  text: '<i class="fa fa-print"></i> Print',
					  title: $('#ekiden-race-title').text() + ': ' + $('#' +tableName + eventId + ' caption').text(),
					  footer: true					  
					}]
				},
				paging : false,
				searching: true,
				serverSide : false,
				columns : [{
						data : "position"
					}, {
						data : "runnerName"						
					}, {
						data : "time"
					},{
						data : "club"
					}, {
						data : "categoryCode"
					}, {
						data : "info"
					}					
				],
				processing : true,
				autoWidth : false,
				scrollX : true,
				order : [[0, "asc"], [2, "asc"]],
				ajax : getAjaxRequest('/wp-json/ipswich-events-api/v1/results/' + eventId +'/'+ year +'/'+ leg)
			});
		}

		function formatDate(date) {
			return (new Date(date)).toDateString();
		}
		
		function getAjaxRequest(url) {
			return {				
				"url" : '<?php echo esc_url( home_url() ); ?>' + url,
				"method" : "GET",
				"headers" : {
					"cache-control" : "no-cache"
				},
				"dataSrc" : ""
			}
		}
	});
	<?php endif; ?>
</script>
