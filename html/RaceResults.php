<div class="section"> 
	<h2 id="jaffa-race-title"></h2>
  <div class="center-panel" id="jaffa-race-info">
	</div>
  <div class="center-panel" id="jaffa-race-winners">
  <table class="table table-striped table-bordered">
    <thead>
      <th>Name</th>
      <th>Club</th>
      <th>Category Position</th>
      <th>Gun Time</th>
      <th>Category Code</th>
      <th>Category Description</th>
      <th>Sex</th>
    </thead>
  </table>
	</div>
	<div class="center-panel" id="jaffa-race-results">
	</div>
</div>
<script type="text/javascript">
	<?php if (isset($_GET['eventId']) && isset($_GET['raceId'])): ?>
	jQuery(document).ready(function ($) {
		  getRaceWinners(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
      getRaceResults(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
			
		function getRaceDetails(eventId, raceId) {
      
    }
    
    function getRaceWinners(eventId, raceId) {
      	$('#jaffa-race-winners table').dataTable({			
			columns:[
			 {
				data: "name"	
			 },
			 {
				data: "club"
			 },
       {         
				data: "categoryPosition"
			 },
       {
				data: "gun_time"
			 },
       {
				data: "categoryCode"
			 },
       {
				data: "categoryDescription"
			 },	
       {
				data: "sex"
			 }	       
			],
			processing    : false,
			autoWidth     : true,
      searching: false,
      ajax : getAjaxRequest('/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId +'/winners')
    });
    }
    
    //https://www.ipswichjaffa.org.uk/jaffa-event-race-results/?eventId=5&raceId=11&chipTime=1&gunTime=1&genderPosition=1&categoryPosition=1&position=1
		function getRaceResults(eventId, raceId) {
      var description = 'Race results for ';
			var tableName = 'jaffa-race-results-table-';
			var tableHtml = '';
			var tableRow = '<tr><th>Position</th><th>Name</th><th>Club</th><th>Sex</th><th>SexId</th><th>Bib</th><th>Chip Time</th><th>Gun Time</th><th>Gender Position</th><th>Category Position</th></tr>';
			tableHtml += '<table class="table table-striped table-bordered" id="' + tableName + raceId + '">';
			tableHtml += '<caption style="text-align:center;font-weight:bold;font-size:1.5em">' + description + '</caption>';
			tableHtml += '<thead>';
			tableHtml += tableRow;
			tableHtml += '</thead>';
			tableHtml += '</table>';
			$('#jaffa-race-results').append(tableHtml);
			
			var table = $('#'+tableName + raceId).DataTable({				
				dom: 'Bfrtip',
        pageLength : 50,
				buttons: {
					buttons: [{
					  extend: 'print',
					  text: '<i class="fa fa-print"></i> Print',
					  title: $('#jaffa-race-title').text() + ': ' + $('#' +tableName + raceId + ' caption').text(),
					  footer: true					  
					}]
				},
				paging : true,
				searching: true,
				serverSide : false,
				columns : [{
            visible : displayColumn('position'),
						data : "position"
					}, {
						data : "name"					
					}, {
						data : "club"		            
					}, {
            data : "sex"		            
					}, {
            visible: false,
            data : "sexId"		            
					}, {
            visible : displayColumn('bibNumber'),
						data : "bibNumber"
					},{
            visible : displayColumn('chipTime'),
						data : "chipTime"
					}, {
            visible : displayColumn('gunTime'),
						data : "gunTime"
					}, {
            visible : displayColumn('genderPosition'),
						data : "genderPosition"
					}, {
            visible : displayColumn('categoryPosition'),
						data : "categoryPosition"
					}						
				],
				processing : true,
				autoWidth : true,
        scrollX: true,
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
