<div class="section"> 
	<div class="center-panel">
    <h2>Twilight Races</h2>
    <p class="info"></p>
		<table class="table table-striped table-bordered" id="event-listings-table">
			<thead>
				<tr>					
					<th>Date</th>
					<th>Description</th>					
          <th>Course Type</th>					
          <th>Venue</th>					
          <th>Course Number</th>					
          <th>Action</th>					
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Date</th>
					<th>Description</th>					
          <th>Course Type</th>					
          <th>Venue</th>					
          <th>Course Number</th>					
          <th>Action</th>	
				</tr>
			</tfoot>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {	

		var tableElement = $('#event-listings-table');
		
		var eventTable = tableElement.dataTable({
			pageLength : 25,
			columns:[
			 {
				data: "id",
				visible : false,
				searchable: false,
				sortable: false 
			 },
			 {
				data: "date"	
			 },
			 {
				data: "description"
			 },
       {
				data: "courseType"
			 },
       {
				data: "venue"
			 },
       {
				data: "courseNumber"
			 },
			 {
				data: "website",
				"class": "left",
				"searchable": false,
				"render": function ( data, type, row, meta ) {		
            var eventRaceResultsUrl = '<?php echo $eventRaceResultsPageUrl; ?>';
						var anchor = '<a href="' + eventRaceResultsUrl;
						if (eventResultsUrl.indexOf("?") >= 0) {
							anchor += '&raceId=' + data[i].id;
						} else {
							anchor += '?raceId=' + data[i].id;
						}
						
						var sLink = '<a href="' + anchor + '" target="_blank">View</a>';					
					
					return sLink;
				}
			 }
			],
			processing    : true,
			autoWidth     : true,
			ajax    : getAjaxRequest('/wp-json/ipswich-events-api/v1/events/1/races')
		});
		
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
    
    function nullToEmptyString(value) {
			return (value == null) ? "" : value;
		}
	});
</script>
