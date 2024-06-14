<?php
    require_once('dashboard_connect.php');

    // Initialize default values
    $dataPoints = [];

    // Check if room_id is received via POST
    if (isset($_POST['room_num'])) {
        // Sanitize input (optional but recommended)
        $roomId = mysqli_real_escape_string($con, $_POST['room_num']);

        // Construct SQL query with DATE_FORMAT to get %H:%i only
        $apfQuery = "SELECT DATE_FORMAT(apf.date_acquired, '%H:%i') AS formatted_time, 
                                apf.pm_one, apf.pm_two_five, apf.pm_ten 
                        FROM aq_param_five apf 
                        WHERE room_id = '$roomId' 
                        ORDER BY date_acquired DESC 
                        LIMIT 10";

        // Execute query
        $apfResult = mysqli_query($con, $apfQuery);

        // Check if query executed successfully
        if ($apfResult) {
            // Fetch data
            while ($row = mysqli_fetch_assoc($apfResult)) {
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
    var myChart = null;
    // Function to update line chart
    function initializeChart(labels, pmOneData, pmTwoFiveData, pmTenData) {
        var ctx = document.getElementById('lineChart').getContext('2d');

        // Check if there's an existing chart instance and destroy it
        if (myChart) {
            myChart.destroy();
        }

        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'PM 1',
                        data: pmOneData,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'PM 2.5',
                        data: pmTwoFiveData,
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255, 187, 16, 0.2)',
                        borderWidth: 1
                    },
                    {
                        label: 'PM 10',
                        data: pmTenData,
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
                            text: 'Time'
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
    var labels = ['00:00', '00:00', '00:00', '00:00', '00:00', '00:00', '00:00'];
    var pmOneData = [0, 0, 0, 0, 0, 0, 0];
    var pmTwoFiveData = [0, 0, 0, 0, 0, 0, 0];
    var pmTenData = [0, 0, 0, 0, 0, 0, 0];


    // Call initializeChart() when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart(labels, pmOneData, pmTwoFiveData, pmTenData);
    });
</script>

<h3>Particulate Matter Reading (5-Minutes Interval)</h3>
<canvas id="lineChart" width="800" height="350"></canvas>