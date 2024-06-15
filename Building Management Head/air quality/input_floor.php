<?php
require_once('aq_connect.php');

$roomQuery = "SELECT bf.* FROM building_floor bf";
$roomResult = mysqli_query($con, $roomQuery);

if (!$roomResult) {
    die("Database query failed: " . mysqli_error($con));
}
?>

<div class="sorting-dropdown" style="margin-left: 10px; margin-right: 10px;">
    <label for="sort-by">Floor:</label>
    <select id="sort-by" onchange="chooseFloor()">
        <option value="">Select Floor</option>
        <?php
        while ($row = mysqli_fetch_assoc($roomResult)) {
            echo '<option value="' . $row['building_floor'] . '">' . $row['bldg_floor_name'] . '</option>';
        }
        ?>
    </select>
</div>

<script>
    function chooseFloor() {
        var floorId = document.getElementById('sort-by').value;
        if (floorId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_facilities.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('facility-dropdown').innerHTML = xhr.responseText;
                }
            };
            xhr.send('floor_id=' + floorId);
        }
    }
</script>

<div id="facility-dropdown" class="sorting-dropdown">
    <label for="facility-sort">Facility:</label>
    <select id="facility-sort" onchange="refreshElements(this.value)">
        <option value=" " disabled selected>Select Facility</option>
        <!-- Options will be populated by AJAX -->
    </select>
</div>

<input type="hidden" id="selected_interval" value="">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function refreshElements(roomNumber) {
        // pm gauges ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/pm_gauges.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Clear existing gauges
                $('#gauge-div-pmOne').empty();
                $('#gauge-div-pmTwoFive').empty();
                $('#gauge-div-pmTen').empty();

                // Update each gauge with respective data
                drawJustGauge('gauge-div-pmOne', response.pmOne, 'Particulate Matter 1 (Latest)');
                drawJustGauge('gauge-div-pmTwoFive', response.pmTwoFive, 'Particulate Matter 2.5 (Latest)');
                drawJustGauge('gauge-div-pmTen', response.pmTen, 'Particulate Matter 10 (Latest)');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // temperature gauges ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/temperature_gauges.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Clear existing gauges
                $('#gauge-div-temperature').empty();
                $('#gauge-div-heatIndex').empty();

                drawJustGaugeTemp('gauge-div-temperature', response.paramTemperature, 'Temperature (Latest)');
                drawJustGaugeTemp('gauge-div-heatIndex', response.paramHeatIndex, 'Heat Index (Latest)');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // co2 and relative humidity gauges ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/others.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Clear existing gauges
                $('#gauge-div-co2').empty();
                $('#gauge-div-humidity').empty();

                drawJustGaugeCo2('gauge-div-co2', response.paramCo2Level, 'Co2 Level (Latest)');
                drawJustGaugeHumidity('gauge-div-humidity', response.paramHumidity, 'Relative Humidity (Latest)');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // pm five minutes ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/pm_charts_five.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Process data for line chart
                var labels = response.dataPoints.map(function(point) {
                    return point.formatted_time;
                });
                var pmOneData = response.dataPoints.map(function(point) {
                    return point.pm_one;
                });
                var pmTwoFiveData = response.dataPoints.map(function(point) {
                    return point.pm_two_five;
                });
                var pmTenData = response.dataPoints.map(function(point) {
                    return point.pm_ten;
                });

                // Update line chart
                initializeChart(labels, pmOneData, pmTwoFiveData, pmTenData);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // pm daily chart ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/pm_charts_daily.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Process data for line chart
                var labelsDaily = response.dataPoints.map(function(point) {
                    return point.formatted_date;
                });
                var pmOneDataDaily = response.dataPoints.map(function(point) {
                    return point.avg_pm_one;
                });
                var pmTwoFiveDataDaily = response.dataPoints.map(function(point) {
                    return point.avg_pm_two_five;
                });
                var pmTenDataDaily = response.dataPoints.map(function(point) {
                    return point.avg_pm_ten;
                });

                // Update line chart
                initializeDailyChart(labelsDaily, pmOneDataDaily, pmTwoFiveDataDaily, pmTenDataDaily);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // ajax post for aq export csv file
        $.ajax({
            type: 'POST',
            url: 'aq_dashboard.php',
            data: {
                room_num: roomNumber
            },
            success: function(response) {
                // Handle the response if needed
                document.getElementById('table_room').value = roomNumber;
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        var selectedIntervalFacility = document.getElementById('selected_interval').value;

        // aq parameter summary ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/aq_param_summary.php',
            data: {
                table_interval: selectedIntervalFacility,
                selected_room: roomNumber
            },
            success: function(response) {
                // Update the content of the data-summary-content div with the response
                document.getElementById('data-summary').innerHTML = response;
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

    }
</script>