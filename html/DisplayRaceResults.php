<?php
$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;
$meetingId = isset($_GET['meetingId']) ? intval($_GET['meetingId']) : 0;
$raceId = isset($_GET['raceId']) ? intval($_GET['raceId']) : 0;
$title = isset($_GET['title']) ? $_GET['title'] : 'Race Results';
$apiEndpoint = esc_url(home_url('/wp-json/ipswich-events-api/v1/events/' . $eventId . '/meetings/' . $meetingId . '/races/' . $raceId . '/results'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.12.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h1><?php echo esc_html($title); ?></h1>
    <div style="overflow: auto;">
        <table id="raceResultsTable" class="display" style="width: 100%;"></table>
    </div>

    <script>
        const apiEndpoint = '<?php echo $apiEndpoint; ?>';

        fetch(apiEndpoint)
            .then((response) => response.json())
            .then((data) => {
                if (!Array.isArray(data) || data.length === 0) {
                    document.body.insertAdjacentHTML('beforeend', '<p>No results found.</p>');
                    return;
                }

                const columns = Object.keys(data[0]).map((field) => ({
                    data: field,
                    title: field
                        .replace(/([a-z])([A-Z])/g, '$1 $2')
                        .replace(/[_-]+/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim()
                        .replace(/^./, (char) => char.toUpperCase())
                }));

                $('#raceResultsTable').DataTable({
                    data: data,
                    columns: columns,
                    paging: true,
                    searching: true,
                    ordering: true,
                    responsive: true
                });
            })
            .catch((error) => {
                console.error('Error fetching race results:', error);
                document.body.insertAdjacentHTML('beforeend', '<p>Unable to load results.</p>');
            });
    </script>
</body>
</html>
