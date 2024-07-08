<?php
require_once('dashboard_connect.php');

// Initialize default values
$dataPoints = [];

// Check if room_id and interval are received via POST
if (isset($_POST['room_num']) && isset($_POST['interval'])) {
    // Sanitize input
    $roomId = mysqli_real_escape_string($con, $_POST['room_num']);
    $interval = mysqli_real_escape_string($con, $_POST['interval']);

    // Construct SQL query based on the interval
    if ($interval == 'aq_param_five') {
        // Query for five minutes interval
        $query = "SELECT DATE_FORMAT(apf.date_acquired, '%H:%i') AS formatted_time, 
                          apf.pm_one, apf.pm_two_five, apf.pm_ten
                  FROM aq_param_five apf 
                  WHERE room_id = '$roomId' 
                  ORDER BY date_acquired DESC 
                  LIMIT 5";
    } elseif ($interval == 'aq_param_daily') {
        // Query for daily interval
        $query = "SELECT DATE_FORMAT(apd.date_acquired, '%m-%d') AS formatted_time, 
                          apd.pm_one, apd.pm_two_five, apd.pm_ten
                  FROM aq_param_daily apd 
                  WHERE room_id = '$roomId'
                  GROUP BY date_acquired
                  ORDER BY date_acquired DESC 
                  LIMIT 5";
    } else {
        // Default to five minutes interval
        $query = "SELECT DATE_FORMAT(apf.date_acquired, '%H:%i') AS formatted_time, 
                          apf.pm_one, apf.pm_two_five, apf.pm_ten
                  FROM aq_param_five apf 
                  WHERE room_id = '$roomId' 
                  ORDER BY date_acquired DESC 
                  LIMIT 5";
    }

    // Execute query
    $result = mysqli_query($con, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch data
        while ($row = mysqli_fetch_assoc($result)) {
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
    var myPMChart = null;
    // Function to update line chart
    function initializePMChart(labelsPM, pmOneData, pmTwoFiveData, pmTenData) {
        var ctx = document.getElementById('linePMChart').getContext('2d');

        // Check if there's an existing chart instance and destroy it
        if (myPMChart) {
            myPMChart.destroy();
        }

        myPMChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsPM,
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
                        value: labelsPM[5], // Initial value, adjust as needed
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

    // Print initial chart with dummy data on page load
    document.addEventListener('DOMContentLoaded', function() {
        var labelsPM = ['00:00', '00:00', '00:00', '00:00', '00:00'];
        var pmOneData = [0, 0, 0, 0, 0];
        var pmTwoFiveData = [0, 0, 0, 0, 0];
        var pmTenData = [0, 0, 0, 0, 0];

        initializePMChart(labelsPM, pmOneData, pmTwoFiveData, pmTenData);
    });
</script>

<canvas id="linePMChart" width="800" height="450"></canvas>