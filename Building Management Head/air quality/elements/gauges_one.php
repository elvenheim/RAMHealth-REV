<?php
require_once('dashboard_connect.php');

// Initialize default values
$pmOneValue = 0;
$pmTwoFiveValue = 0;
$pmTenValue = 0;

$co2Value = 0;
$humidityValue = 0;

// Check if room_id and interval are received via POST
if (isset($_POST['room_num']) && isset($_POST['interval'])) {
    // Sanitize input
    $roomId = mysqli_real_escape_string($con, $_POST['room_num']);
    $interval = mysqli_real_escape_string($con, $_POST['interval']);

    // Construct SQL query based on the interval
    if ($interval == 'aq_param_five') {
        // Query for five minutes interval
        $query = "SELECT apf.pm_one, apf.pm_two_five, apf.pm_ten
                  FROM aq_param_five apf 
                  WHERE room_id = '$roomId' 
                  ORDER BY date_acquired DESC 
                  LIMIT 1";
    } elseif ($interval == 'aq_param_daily') {
        // Query for daily interval
        $query = "SELECT apd.pm_one, apd.pm_two_five, apd.pm_ten
                  FROM aq_param_daily apd 
                  WHERE room_id = '$roomId'
                  ORDER BY date_acquired DESC 
                  LIMIT 1";
    } else {
        // Default to five minutes interval
        $query = "SELECT apf.pm_one, apf.pm_two_five, apf.pm_ten
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
            $pmOneValue = $data['pm_one'];
            $pmTwoFiveValue = $data['pm_two_five'];
            $pmTenValue = $data['pm_ten'];
        }

        // Prepare JSON response
        $response = array(
            'pmOne' => $pmOneValue,
            'pmTwoFive' => $pmTwoFiveValue,
            'pmTen' => $pmTenValue,
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
    function drawJustGauge(elementId, value, label) {
        var gauge = new JustGage({
            id: elementId,
            value: value,
            title: label,
            symbol: ' µg/m³',
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
        // Draw gauges Particulate Matter
        drawJustGauge('gauge-div-pmOne', 0, 'Particulate Matter 1 (Latest)');
        drawJustGauge('gauge-div-pmTwoFive', 0, 'Particulate Matter 2.5 (Latest)');
        drawJustGauge('gauge-div-pmTen', 0, 'Particulate Matter 10 (Latest)');
    });
</script>

<div class="left-gauge-container-one">
    <h3>Particulate Matter 1</h3>
    <div id="gauge-div-pmOne"></div>
    <h3 style="margin-top: 30px;">Particulate Matter 2.5</h3>
    <div id="gauge-div-pmTwoFive"></div>
    <h3 style="margin-top: 30px;">Particulate Matter 10</h3>
    <div id="gauge-div-pmTen"></div>
</div>