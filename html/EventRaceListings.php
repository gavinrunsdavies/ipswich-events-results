<div class="section" id="event-race-listings-section"> 
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {	
  
    var sectionElement = $('#event-race-listings-section');
    
    $.getJSON(
			  '<?php echo esc_url( home_url() ); ?>/wp-json/ipswich-events-api/v1/events',
          function(data) {	
            //sectionElement.empty();          
            $.each(data, function(i, event){
				       createEventRaceListingsTable(event);
            });		
          }
			);	
      
    function createEventRaceListingsTable(event) {
      
      var tableId = 'event-listings-table-' + event.id;
      var html = '<div class="center-panel"><h2>' + event.name + '</h2>';
      html += '<p class="info">' + nullToEmptyString(event.info) + '</p>';
      
      var table = '<table class="table table-striped table-bordered" id="' + tableId + '">';
			table += '<thead><tr><th></th><th>Date</th><th>Description</th><th>Course Type</th><th>Venue</th><th>Course Number</th><th>Action</th></tr></thead><tbody></tbody></table>';
      
      html += table;
      html += '</div>';
    
      var tempDom = sectionElement.append($.parseHTML(html));
      
      createDatatable($('#'+tableId, tempDom), event.id);
    }
    
    function createDatatable(tableElement, eventId) {
      		tableElement.dataTable({
			pageLength : 25,
			columns:[
			 {
				data: "raceId",
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
				class: "left",
				searchable: false,
				render: function ( data, type, row, meta ) {		
            var anchor = '<?php echo $eventraceresultspageurl; ?>';
						
						if (anchor.indexOf("?") >= 0) {
							anchor += '&raceid=' + row.raceId;
						} else {
							anchor += '?raceid=' + row.raceId;
						}
						
						var slink = '<a href="' + anchor + '" target="_blank">view</a>';					
					
					return slink;
				}
			 }
			],
			processing    : true,
			autoWidth     : true,
      searching: false,
			ajax    : getAjaxRequest('/wp-json/ipswich-events-api/v1/events/'+eventId+'/races')
		});
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

    function nullToEmptyString(value) {
			return (value == null) ? "" : value;
		}    
	});
</script>
