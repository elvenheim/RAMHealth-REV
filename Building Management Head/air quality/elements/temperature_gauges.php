<?php
require_once('dashboard_connect.php');

// Initialize default values
$temperatureValue = 0;
$heatIndexValue = 0;

// Check if room_id is received via POST
if (isset($_POST['room_num'])) {
    // Sanitize input (optional but recommended)
    $roomId = mysqli_real_escape_string($con, $_POST['room_num']);

    // Construct SQL query
    $apfQuery = "SELECT apf.* FROM aq_param_five apf WHERE room_id = '$roomId' ORDER BY date_acquired DESC LIMIT 1";

    // Execute query
    $apfResult = mysqli_query($con, $apfQuery);

    // Check if query executed successfully
    if ($apfResult) {
        // Fetch data
        $apfData = mysqli_fetch_assoc($apfResult);
        $temperatureValue = $apfData['param_temp'];
        $heatIndexValue = $apfData['heat_index'];

        // Prepare JSON response
        $response = array(
            'paramTemperature' => $temperatureValue,
            'paramHeatIndex' => $heatIndexValue,
        );

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/justgage@1.4.0/dist/justgage.min.js"></script>
<script type="text/javascript">
    // Function to draw JustGauge
    function drawJustGaugeTemp(elementId, value, label) {
        var gaugeTemp = new JustGage({
            id: elementId,
            value: value,
            title: label,
            symbol: ' °C',
            min: 0,
            max: 50,
            label: '',
            labelFontColor: '#000000',
            labelMinFontSize: 12,
            relativeGaugeSize: true,
            gaugeWidthScale: 0.6,
            counter: true,
            decimals: 2, // Adjust decimals as needed
            animationSpeed: 5000,
            levelColors: ['#E7AE41', '#ff8200', '#ff1100'],
            pointer: true,
            pointerOptions: {
                toplength: -15,
                bottomlength: 10,
                bottomwidth: 5,
                color: '#E7AE41',
                stroke: '#343A40',
                stroke_width: 0.5
            }
        });
    }

    // On document load
    document.addEventListener('DOMContentLoaded', function() {
        // Draw gauges
        drawJustGaugeTemp('gauge-div-temperature', 0, 'Temperature (Latest)');
        drawJustGaugeTemp('gauge-div-heatIndex', 0, 'Heat Index (Latest)');
    });
</script>
<div class="temperature-gauge-container">
    <div>
        <h3 style="margin-left: 30px;">Temperature (Latest)</h3>
        <div id="gauge-div-temperature" style="width: 250px; height: 200px; margin-top: 10px"></div>
    </div>
    <div>
        <h3 style="margin-left: 40px;">Heat Index (Latest)</h3>
        <div id="gauge-div-heatIndex" style="width: 250px; height: 200px; margin-top: 10px"></div>
    </div>
</div>