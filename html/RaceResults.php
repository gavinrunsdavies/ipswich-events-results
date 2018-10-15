<div class="section"> 
	<h2 id="jaffa-race-title"></h2>
  <div class="center-panel" id="jaffa-race-info">
	</div>
  <div class="center-panel" id="jaffa-race-winners">
	</div>
	<div class="center-panel" id="jaffa-race-results">
	</div>
</div>
<script type="text/javascript">
	<?php if (isset($_GET['eventId']) && isset($_GET['raceId'])): ?>
	jQuery(document).ready(function ($) {
		  getRaceDetails(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
			
		function getRaceDetails(eventId, raceId) {
      $.getJSON(
			  '<?php echo esc_url( home_url() ); ?>/wp-json/ipswich-jaffa-api/v2/results/grandprix/' + year + '/' + 3,
			  function(data) {		
				ladiesGPData = data.results;
				
				ladiesOrderedRacesIds = data.races;
				populateRacesTable('ladies-grand-prix-races', data.races);
				createDataTable('ladies-grand-prix-results-table', data.results, 3);
				
				$('#ladies-grand-prix-results').show();
			  }
			);
    }
    
    function getRaceWinners(eventId, raceId) {
      
    }
    
		function getRaceResults(eventId, raceId) {
      var description = 'Race results for ';
			var tableName = 'jaffa-race-results-table-';
			var tableHtml = '';
			var tableRow = '<tr><th>Position</th><th>Name</th><th>Time</th><th>Category</th><th>Info</th></tr>';
			tableHtml += '<table class="stripe row-border display no-wrap" cellspacing="0" width="100%" id="' + tableName + raceId + '">';
			tableHtml += '<caption style="text-align:center;font-weight:bold;font-size:1.5em">' + description + '</caption>';
			tableHtml += '<thead>';
			tableHtml += tableRow;
			tableHtml += '</thead>';
			tableHtml += '</table>';
			$('#jaffa-race-results').append(tableHtml);
			
			var table = $('#'+tableName + raceId).DataTable({				
				dom: 'Bfrtip',
				buttons: {
					buttons: [{
					  extend: 'print',
					  text: '<i class="fa fa-print"></i> Print',
					  title: $('#jaffa-race-title').text() + ': ' + $('#' +tableName + raceId + ' caption').text(),
					  footer: true					  
					}]
				},
				paging : false,
				searching: true,
				serverSide : false,
				columns : [{
            visible : displayColumn('position'),
						data : "position"
					}, {
            visible : displayColumn('name'),
						data : "name"						
					}, {
            visible : displayColumn('time'),
						data : "time"
					},{
            visible : displayColumn('club'),
						data : "club"
					}, {
            visible : displayColumn('categoryCode'),
						data : "categoryCode"
					}, {
            visible : displayColumn('info'),
						data : "info"
					}					
				],
				processing : true,
				autoWidth : false,
				scrollX : true,
        responsive: true,
				order : [[0, "asc"], [2, "asc"]],
				ajax : getAjaxRequest('/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId +'/results')
			});
		}
    
    function displayColumn(variable) {
        return getQueryVariable(variable) !== false;
    }
    
    function getQueryVariable(variable) {
      var query = window.location.search.substring(1);
      var vars = query.split("&");
      for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) { return pair[1]; }
      }
      return(false);
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
