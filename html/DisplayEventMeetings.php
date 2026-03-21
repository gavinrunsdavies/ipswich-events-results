<div id="raceListingGrid" style="width: 100%; height: 500px" class="ag-theme-quartz"></div>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.3/dist/ag-grid-community.min.js"></script>
<style>
    .clickable {
        cursor: pointer;
        background-color: #f0f8ff; /* Light blue for visibility */
    }
    .clickable:hover {
        background-color: #add8e6; /* Highlight on hover */
    }
</style>
<script>

class MeetingRacesTooltip {
    eGui;
    init(params) {
        // Extract data from params
        const tooltipData = params.data.results;

        // Create the table
        const table = document.createElement('table');
        table.style.borderCollapse = 'collapse';
        table.style.width = '100%';
        table.style.backgroundColor = '#fff';
        table.style.border = '1px solid #000'; 

        // Add table header
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        headerRow.innerHTML = `
            <th style="border: 1px solid #ccc; padding: 8px; text-align: left; background-color: #f4f4f4;">Race</th>
            <th style="border: 1px solid #ccc; padding: 8px; text-align: left; background-color: #f4f4f4;">Results</th>
        `;
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Add table body
        const tbody = document.createElement('tbody');
        tooltipData.forEach((result) => {
            const row = document.createElement('tr');

            // Race name column
            const raceCell = document.createElement('td');
            raceCell.textContent = result.name;
            raceCell.style.border = '1px solid #ccc';
            raceCell.style.padding = '8px';
            row.appendChild(raceCell);

            // Hyperlink column
            const linkCell = document.createElement('td');
            linkCell.style.border = '1px solid #ccc';
            linkCell.style.padding = '8px';                    

            const link = document.createElement('a');            
            if (result.type == 'pdf') {
                link.href=`<?php echo esc_url(home_url()); ?>/wp-json/ipswich-events-api/v1/events/`+eventId+`/meetings/`+meetingId+`/races/`+result.id+`/results/pdf`;
                link.textContent = 'PDF';
            } else {<a href="<?php echo plugins_url('fileB.php', __FILE__); ?>">Go to File B</a>
                link.href=`<?php echo plugins_url('DisplayRaceResults.php', __FILE__); ?>?title=&eventId=`+eventId+`&meetingId=`+meetingId+`&raceId=`+result.id;                        
                link.textContent = 'CSV';
            }
            
            linkCell.appendChild(link);

            row.appendChild(linkCell);

            tbody.appendChild(row);
        });
        table.appendChild(tbody);

        // Create tooltip container
        this.tooltipContainer = document.createElement('div');
        this.tooltipContainer.style.position = 'absolute';
        this.tooltipContainer.style.backgroundColor = '#fff';
        this.tooltipContainer.style.border = '1px solid #ccc';
        this.tooltipContainer.style.padding = '10px';
        this.tooltipContainer.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
        this.tooltipContainer.style.pointerEvents = 'auto'; // Allow interaction
        this.tooltipContainer.style.zIndex = '1000'; // Ensure it appears above other elements
        this.tooltipContainer.appendChild(table);

        // Attach tooltip to the document body
        document.body.appendChild(this.tooltipContainer);

        this.eGui = {
            getGui: () => this.tooltipContainer
        };
    }

    getGui() {
        return this.tooltipContainer;
    }
}

const eventMeetingGridOptions = {            
    rowData: [],      
    defaultColDef: {
        flex: 1
    },
    tooltipShowDelay: 200,
    tooltipInteraction: true,
    columnDefs: [{
            field: "meetingId",
            hide: true                  
        },
        {
            headerName: "Meeting",
            field: "meetingName",
            tooltipField: "meetingName",
            tooltipComponent: MeetingRacesTooltip,
            tooltipComponentParams: {
                eventId: <?php echo $_GET['eventId']; ?>
            }
        },
        {
            headerName: "Date",
            field: "meetingDate"
        },
        {
            headerName: "Venue",
            field: "meetingVenue"
        },
        {
            field: "results",
            hide: true,
            valueFormatter: (params) => params.value?.name || 'N/A', // Needed if tooltips reference it             
        }
    ]
};

let eventMeetingGridApi = agGrid.createGrid(document.querySelector("#eventMeetings"), eventMeetingGridOptions);

fetch("<?php echo esc_url(home_url()); ?>/wp-json/ipswich-events-api/v1/events/<?php echo $_GET['eventId']; ?>/meetings")
    .then((response) => response.json())
    .then((data) => eventMeetingGridApi.setGridOption("rowData", data));
</script>
