<div id="myGrid" style="width: 100%; height: 100%" class="ag-theme-quartz"></div>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.3/dist/ag-grid-community.min.js"></script>
<script type="text/javascript">
    // Grid API: Access to Grid API methods
    let gridApi;

    // Row Data Interface

    // Grid Options: Contains all of the grid configurations
    const gridOptions = {
        // Data to be displayed
        rowData: [],
        // Columns to be displayed (Should match rowData properties)
        columnDefs: [{
                field: "id",
                hide: true
            },
            {
                field: "name"
            },
            {
                field: "date"
            },
            {
                field: "venue"
            }
        ],
    };

    // Create Grid: Create new grid within the #myGrid div, using the Grid Options object
    gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);

    // Fetch Remote Data
    fetch("https://www.ipswichjaffa.org.uk/wp-json/ipswich-events-api/v1/events/<?php echo $_GET['eventId']; ?>/meetings")
        .then((response) => response.json())
        .then((data) => gridApi.setGridOption("rowData", data));
</script>