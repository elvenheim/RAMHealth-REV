<?php
require_once 'dashboard_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_table = $_POST['table'];
    $selected_columns = $_POST['columns'];

    // Check if table or columns are empty
    if (empty($selected_table) || empty($selected_columns)) {
        echo json_encode(['error' => 'No table or columns selected']);
        exit;
    }

    // Sanitize inputs
    $selected_table = mysqli_real_escape_string($con2, $selected_table);
    $selected_columns = array_map(function ($column) use ($con2) {
        return "`" . mysqli_real_escape_string($con2, $column) . "`"; // Properly escape and quote column names
    }, $selected_columns);

    // Build the query to fetch the latest values
    $columns_string = implode(', ', $selected_columns); // Properly format the column names
    $query = "SELECT $columns_string FROM `$selected_table` ORDER BY `Date Acquired` DESC LIMIT 1";

    $result = mysqli_query($con2, $query);

    if (!$result) {
        echo json_encode(['error' => "Error fetching data: " . mysqli_error($con2)]);
        exit;
    }

    // Fetch the data
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        // Convert values to Wh and strip units
        $dataPoints = array_map(function ($value) {
            // Strip any whitespace
            $value = trim($value);

            // Determine unit and convert to Wh
            if (strpos($value, 'kWh') !== false) {
                return floatval($value) * 1000; // Convert kWh to Wh
            } elseif (strpos($value, 'mWh') !== false) {
                return floatval($value) / 1000; // Convert mWh to Wh
            } elseif (strpos($value, 'Wh') !== false) {
                return floatval($value); // Already in Wh
            } else {
                return floatval($value); // No unit found, assume Wh
            }
        }, array_values($data));

        // Prepare response array
        $response = [
            'table' => $selected_table,
            'columns' => $selected_columns,
            'dataPoints' => $dataPoints
        ];

        // Output JSON-encoded data
        echo json_encode($response, JSON_NUMERIC_CHECK);
    } else {
        echo json_encode(['error' => 'No data found.']);
    }
    exit; // Ensure no additional output
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var myChart = null;

    function renderBarChart(chartData, columnNames) {
        // Prepare labels and data for Chart.js
        var labels = columnNames;
        var data = chartData;

        var ctx = document.getElementById('myChart').getContext('2d');

        // Check if there's an existing chart instance and destroy it
        if (myChart) {
            myChart.destroy();
        }

        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Energy Consumption (Wh)',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toFixed(2) + ' Wh';
                            }
                        }
                    }
                }
            }
        });
    }

    renderBarChart(chartData, columnNames);
</script>

<canvas id="myChart" width="800" height="400"></canvas>