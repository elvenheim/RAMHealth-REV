<?php
require_once('dashboard_connect.php');

// Initialize default values
$dataPoints = [];

// Check if room_id is received via POST
if (isset($_POST['room_num'])) {
    // Sanitize input (optional but recommended)
    $roomId = mysqli_real_escape_string($con, $_POST['room_num']);

    // Construct SQL query to get daily data
    $apdQuery = "SELECT DATE_FORMAT(apd.date_acquired, '%Y-%m-%d') AS formatted_date, 
                            AVG(apd.pm_one) AS avg_pm_one, 
                            AVG(apd.pm_two_five) AS avg_pm_two_five, 
                            AVG(apd.pm_ten) AS avg_pm_ten 
                     FROM aq_param_daily apd 
                     WHERE room_id = '$roomId' 
                     GROUP BY formatted_date 
                     ORDER BY formatted_date DESC 
                     LIMIT 10";

    // Execute query
    $apdResult = mysqli_query($con, $apdQuery);

    // Check if query executed successfully
    if ($apdResult) {
        // Fetch data
        while ($row = mysqli_fetch_assoc($apdResult)) {
            $dataPoints[] = $row;
        }

        // Reverse the array to have the latest data at the end for chronological order
        $dataPoints = array_reverse($dataPoints);

        // Prepare JSON response
        $response = [
            'dataPoints' => $dataPoints
        ];

        // Output JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; // Ensure no further output beyond this point
    } else {
        // Handle query error
        die("Database query failed: " . mysqli_error($con));
    }
}
?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var myChartDaily = null;
    // Function to update line chart
    function initializeDailyChart(labelsDaily, pmOneDataDaily, pmTwoFiveDataDaily, pmTenDataDaily) {
        var ctxDaily = document.getElementById('lineDailyChart').getContext('2d');

        // Check if there's an existing chart instance and destroy it
        if (myChartDaily) {
            myChartDaily.destroy();
        }

        myChartDaily = new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: labelsDaily,
                datasets: [{
                        label: 'PM 1',
                        data: pmOneDataDaily,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'PM 2.5',
                        data: pmTwoFiveDataDaily,
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255, 187, 16, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'PM 10',
                        data: pmTenDataDaily,
                        borderColor: 'red',
                        backgroundColor: 'rgba(207, 32, 32, 0.2)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'PM Readings per 5 Minutes'
                    }
                },
                annotation: {
                    annotations: [{
                        type: 'line',
                        mode: 'vertical',
                        scaleID: 'x',
                        value: labels[10], // Initial value, adjust as needed
                        borderColor: 'gray',
                        borderWidth: 1,
                        label: {
                            content: 'Start', // Annotation label
                            enabled: true,
                            position: 'top'
                        }
                    }]
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date Acquired'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Particulate Matter Level (µg/m³)'
                        }
                    }
                }
            }
        });
    }

    // Dummy data if no specific room data is available
    var labelsDaily = ['0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00'];
    var pmOneDataDaily = [0, 0, 0, 0, 0, 0, 0];
    var pmTwoFiveDataDaily = [0, 0, 0, 0, 0, 0, 0];
    var pmTenDataDaily = [0, 0, 0, 0, 0, 0, 0];


    // Call initializeChart() when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeDailyChart(labelsDaily, pmOneDataDaily, pmTwoFiveDataDaily, pmTenDataDaily);
    });
</script>

<h3>Particulate Matter Reading (Daily Interval)</h3>
<canvas id="lineDailyChart" width="800" height="350"></canvas>