<?php
require_once('dashboard_connect.php');

// Initialize default values
$co2Value = 0;
$humidityValue = 0;
$temperatureValue = 0;
$heatIndexValue = 0;

// Check if room_id and interval are received via POST
if (isset($_POST['room_num']) && isset($_POST['interval'])) {
    // Sanitize input
    $roomId = mysqli_real_escape_string($con, $_POST['room_num']);
    $interval = mysqli_real_escape_string($con, $_POST['interval']);

    // Construct SQL query based on the interval
    if ($interval == 'aq_param_five') {
        // Query for five minutes interval
        $query = "SELECT apf.co2_level, apf.rel_humid, apf.param_temp, apf.heat_index
                  FROM aq_param_five apf 
                  WHERE room_id = '$roomId' 
                  ORDER BY date_acquired DESC 
                  LIMIT 1";
    } elseif ($interval == 'aq_param_daily') {
        // Query for daily interval
        $query = "SELECT apd.co2_level, apd.rel_humid, apd.param_temp, apd.heat_index
                  FROM aq_param_daily apd 
                  WHERE room_id = '$roomId'
                  ORDER BY date_acquired DESC 
                  LIMIT 1";
    } else {
        // Default to five minutes interval
        $query = "SELECT apf.co2_level, apf.rel_humid, apf.param_temp, apf.heat_index
                  FROM aq_param_five apf 
                  WHERE room_id = '$roomId' 
                  ORDER BY date_acquired DESC 
                  LIMIT 1";
    }

    // Execute query
    $result = mysqli_query($con, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch data
        $data = mysqli_fetch_assoc($result);
        if ($data) {
            $co2Value = $data['co2_level'];
            $humidityValue = $data['rel_humid'];
            $temperatureValue = $data['param_temp'];
            $heatIndexValue = $data['heat_index'];
        }

        // Prepare JSON response
        $response = array(
            'paramCo2Level' => $co2Value,
            'paramHumidity' => $humidityValue,
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

    function drawJustGaugeHeat(elementId, value, label) {
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
        // Draw gauges Other Gasses
        drawJustGaugeCo2('gauge-div-co2', 0, 'Co2 Level (Latest)');
        drawJustGaugeHumidity('gauge-div-humidity', 0, 'Relative Humidity (Latest)');

        // Draw gauges Temperature
        drawJustGaugeTemp('gauge-div-temperature', 0, 'Temperature (Latest)');
        drawJustGaugeHeat('gauge-div-heatIndex', 0, 'Heat Index (Latest)');
    });
</script>

<div class="left-gauge-container-two">
    <h3>Co2 Level</h3>
    <div id="gauge-div-co2"></div>
    <h3 style="margin-top: 30px;">Relative Humidity</h3>
    <div id="gauge-div-humidity"></div>
    <h3 style="margin-top: 30px;">Temperature</h3>
    <div id="gauge-div-temperature"></div>
    <h3 style="margin-top: 30px;">Heat Index</h3>
    <div id="gauge-div-heatIndex"></div>
</div>