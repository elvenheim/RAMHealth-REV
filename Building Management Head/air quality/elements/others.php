<?php
require_once('dashboard_connect.php');

// Initialize default values
$co2Value = 0;
$humidityValue = 0;

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
        $co2Value = $apfData['co2_level'];
        $humidityValue = $apfData['rel_humid'];

        // Prepare JSON response
        $response = array(
            'paramCo2Level' => $co2Value,
            'paramHumidity' => $humidityValue,
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
    function drawJustGaugeCo2(elementId, value, label) {
        var gaugeCo2 = new JustGage({
            id: elementId,
            value: value,
            title: label,
            symbol: ' ppm',
            min: 0,
            max: 10000,
            label: '',
            labelFontColor: '#000000',
            labelMinFontSize: 12,
            relativeGaugeSize: true,
            gaugeWidthScale: 0.6,
            counter: true,
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

    function drawJustGaugeHumidity(elementId, value, label) {
        var gaugeHumidity = new JustGage({
            id: elementId,
            value: value,
            title: label,
            symbol: ' %',
            min: 0,
            max: 100,
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
        drawJustGaugeCo2('gauge-div-co2', 0, 'Co2 Level (Latest)');
        drawJustGaugeHumidity('gauge-div-humidity', 0, 'Relative Humidity (Latest)');
    });
</script>
<div class="others-gauge-container">
    <div>
        <h3 style="margin-left: 45px; margin-top: -5px;">Co2 Level (Latest)</h3>
        <div id="gauge-div-co2" style="width: 250px; height: 200px; margin-top: 0px"></div>
    </div>
    <div>
        <h3 style="margin-left: 47px; justify-content: center; align-items: center;">Relative Humidity</h3>
        <h3 style="margin-top: 10px; margin-left: 88px; justify-content: center; align-items: center;">(Latest)</h3>
        <div id="gauge-div-humidity" style="width: 250px; height: 200px; margin-top: 0px"></div>
    </div>
</div>