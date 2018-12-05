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
      html += '<div class="info">' + stringToParagrapghs(event.info) + '</div>';
      
      var table = '<table class="table table-striped table-bordered" id="' + tableId + '">';
			table += '<thead><tr><th>Date</th><th>Description</th><th>Course Type</th><th>Venue</th><th>Course Number</th><th># of Results</th><th>Action</th></tr></thead><tbody></tbody></table>';
      
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
         targets: [5],
				data: "numberOfResults"
			 },
			 {
         targets: [-1],
				class: "left",
				searchable: false,
				render: function ( data, type, row, meta ) {	
          if (row.numberOfResults == '0')
            return "";
          var anchor = '<?php echo $eventRaceResultsPageUrl; ?>';
          
          if (anchor.indexOf("?") >= 0) {
            anchor += '&';
          } else {
            anchor += '?';
          }
          
          anchor += 'eventId=' + eventId + '&raceId=' + row.raceId;
          
          anchor += '&gunTime='+row.gunTime;
          anchor += '&genderPosition='+row.genderPosition;
          anchor += '&categoryPosition='+row.categoryPosition;
          anchor += '&bibNumber='+row.bibNumber;
          anchor += '&chipTime='+row.chipTime;
          anchor += '&categoryPrizes='+row.categoryPrizes;            
          anchor += '&singleGenderRace='+row.singleGenderRace;            
          
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
                    .append( '<td colspan="6" style="font-weight:bold;background-color:#e0e0e0">'+rows.data().pluck('meetingName')[0]+'</td>' );
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

    function stringToParagrapghs(value) {
			var text = (value == null) ? "" : value;
      var lines = text.split("\r\n");
      var paras = '';
      $.each(lines, function(i, line) {
        if (line) {
            paras += '<p>' + line + '</p>';
        }
      });
      
      return paras;
		}    
	});
</script>
