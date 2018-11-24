<div class="section"> 
	<h2 id="jaffa-race-title"></h2>
  <h3 id="jaffa-race-date"></h3>
  <div class="center-panel" id="jaffa-race-info">
	</div>
  <div class="center-panel" id="jaffa-race-winners">
    <table class="table table-striped table-bordered" style="display: none" id="jaffa-race-winners-gender-male">
      <caption style="text-align:center;font-weight:bold;font-size:1.5em">Male winners</caption>
    </table>
    <table class="table table-striped table-bordered" style="display: none" id="jaffa-race-winners-gender-female">
     <caption style="text-align:center;font-weight:bold;font-size:1.5em">Female winners</caption>      
    </table>
    
    <table class="table table-striped table-bordered" style="display: none" id="jaffa-race-winners-category">
    <caption style="text-align:center;font-weight:bold;font-size:1.5em">Category winners</caption>
    </table>
	</div>
	<div class="center-panel" id="jaffa-race-results">
	</div>
</div>
<script type="text/javascript">
	<?php if (isset($_GET['eventId']) && isset($_GET['raceId'])): ?>
	jQuery(document).ready(function ($) {
      if (getQueryVariable('categoryPrizes') > 0) {
        getRaceWinners(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
        getCategoryWinners(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
      }
      getRaceDetails(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>)
      getRaceResults(<?php echo $_GET['eventId']; ?>, <?php echo $_GET['raceId']; ?>);	
			
		function getRaceDetails(eventId, raceId) {
      $.getJSON(
			  '/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId,   
         function (data ) {           
          $('#jaffa-race-info').text(data.info);
          $('#jaffa-race-title').text(data.description);
          var d = new Date(data.date);        
          $('#jaffa-race-date').text(d.toDateString());

          if (data.meetingName != '') {
            $('#jaffa-race-info').append('<h5>Meeting Name: '+data.meetingName+'</h5>');
          }
          $('#jaffa-race-info').append('<br/><br/>');
         }          
			);
    }
    
    function getRaceWinners(eventId, raceId) {
      $.getJSON(
			  '/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId +'/winners',   
         function (data ) {           
           if (data.male.length > 0) 
            createEventRaceGenderWinnersTable(data.male, $('#jaffa-race-winners-gender-male'));
          if (data.female.length > 0) 
            createEventRaceGenderWinnersTable(data.female, $('#jaffa-race-winners-gender-female'));            
         }
			);
    }
    
    function getCategoryWinners(eventId, raceId) {
       $.getJSON(
			  '/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId +'/categorywinners',   
         function (data ) {
           if (data.length > 0) 
            createEventRaceCategoryWinnersTable(data, $('#jaffa-race-winners-category'));                             
         }
			);
    }
    
    function createEventRaceGenderWinnersTable(data, table) {
      
      	table.dataTable({			
          columnDefs:[
          {
            targets: [0],
            data: "genderPosition",
            title: "Position"
         },
         {
           targets: [1],
          data: "name",
          title: "Name"
         },
         {
           targets: [2],
          data: "club",
          title: "Club"
         },      
         {
           targets: [3],
          data: "time",
          title: "Time"
         }        
        ],
        processing    : false,
        autoWidth     : true,
        paging : false,
        searching: false,
         data: data
      });
       table.show();
    }
    
        function createEventRaceCategoryWinnersTable(data, table) {
      
      	table.dataTable({			
        			columns:[
			 {
				data: "name",
        title: "Name"
			 },
			 {
				data: "club",
        title: "Club"
			 },
       {         
				data: "categoryPosition",
        title: "Category Position"
			 },
       {
				data: "time",
        title: "Time"
			 },
       {
				data: "categoryCode",
        title: "Category Code"
			 },
       {
				data: "categoryDescription",
        title: "Category Description"        
			 },	
       {
				data: "sex",
        title: "Sex"
			 }	       
			],
        processing    : false,        
        paging : false,
        autoWidth     : true,
        searching: false,
        data: data
      });
       table.show();
    }
    
    //https://www.ipswichjaffa.org.uk/jaffa-event-race-results/?eventId=5&raceId=11&chipTime=1&gunTime=1&genderPosition=1&categoryPosition=1&position=1
		function getRaceResults(eventId, raceId) {      
			var tableName = 'jaffa-race-results-table-';
			var tableHtml = '';
			var tableRow = '<tr><th>Position</th><th>Name</th><th>Team</th><th>Club</th><th>Sex</th><th>SexId</th><th>Bib</th><th>Chip Time</th><th>Gun Time</th><th>Gender Position</th><th>Category Position</th></tr>';
			tableHtml += '<table class="table table-striped table-bordered" id="' + tableName + raceId + '">';
			tableHtml += '<caption style="text-align:center;font-weight:bold;font-size:1.2em">Full Results</caption>';
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
						data : "position"
					},{
            visible : !displayColumn('teamResult'),
						data : "name"					
					}, {
            visible : displayColumn('teamResult'),
						data : "team"					
					}, {
						data : "club"		            
					}, {
            visible : !displayColumn('singleGenderRace'),
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
				autoWidth : false,
        scrollX: true,
				order : [[0, "asc"], [2, "asc"]],
				ajax : getAjaxRequest('/wp-json/ipswich-events-api/v1/events/' + eventId +'/races/'+ raceId +'/results')
			});
		}
    
    function displayColumn(variable) {
        var result = getQueryVariable(variable);
        return result !== false && result != "0";
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
