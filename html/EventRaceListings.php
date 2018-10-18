<div class="section" id="event-race-listings-section"> 
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {	
  
    var sectionElement = $('#event-race-listings-section');
    
    $.getJSON(
			  '<?php echo esc_url( home_url() ); ?>/wp-json/ipswich-events-api/v1/events',
          function(data) {	
            sectionElement.empty();          
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
			table += '<thead><tr><th>Date</th><th>Description</th><th>Course Type</th><th>Venue</th><th>Course Number</th><th>Action</th></tr></thead><tbody></tbody></table>';
      
      html += table;
      html += '</div>';
    
      var tempDom = sectionElement.append($.parseHTML(html));
      
      createDatatable($('#'+tableId, tempDom), event.id);
    }
    
    function createDatatable(tableElement, eventId) {
      		tableElement.dataTable({
			pageLength : 10,
			columnDefs:[
			 {
         targets: [0],
				data: "date"	
			 },
			 {
         targets: [1],
				data: "description"
			 },
       {
         targets: [2],
				data: "courseType"
			 },
       {
         targets: [3],
				data: "venue"
			 },
       {
         targets: [4],
				data: "courseNumber"
			 },
			 {
         targets: [-1],
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
      rowGroup: {
            startRender : function (rows, group) {
              if (group == null) return $('<tr/>');
              
              return $('<tr/>')
                    .append( '<td style="font-weight:bold;background-color:#e0e0e0">'+rows.data().pluck('meetingDate')[0]+'</td>' )
                    .append( '<td colspan="5" style="font-weight:bold;background-color:#e0e0e0">'+rows.data().pluck('meetingName')[0]+'</td>' );
            },
            dataSrc: 'meetingId'
        },
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
