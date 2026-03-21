<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_GET['title']; ?></title>
    <!-- Include AG Grid JS -->
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
</head>
<body>
    <h1><?php echo $_GET['title']; ?></h1>
    <div id="resultsGrid" class="ag-theme-alpine" style="height: 500px; width: 100%;"></div>

    <script>
        const apiEndpoint = '<?php echo esc_url(home_url()); ?>/wp-json/ipswich-events-api/v1/events/<?php echo $_GET['eventId']; ?>/meetings/<?php echo $_GET['meetingId']; ?>/races/<?php echo $_GET['raceId']; ?>/results'; 

        // Fetch data from API
        fetch(apiEndpoint)
            .then(response => response.json())
            .then(data => {
                // Extract column definitions dynamically from the first object
                const columnDefs = Object.keys(data[0]).map(field => ({
                    headerName: field, // Use field name as header
                    field: field       // Map to the field in data
                }));

                // Set up the grid options
                const gridOptions = {
                    columnDefs: columnDefs,
                    rowData: data,
                    defaultColDef: {
                        sortable: true,
                        filter: true,
                        resizable: true
                    }
                };
              
                new agGrid.createGrid(document.querySelector("#resultsGrid"), gridOptions);
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>
</body>
</html>
