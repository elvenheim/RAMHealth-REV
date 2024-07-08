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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function refreshElements(roomNumber) {

        var interval = document.getElementById('selected_interval').value;

        console.log('Interval:', interval);

        // 1st gauges ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/gauges_one.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                // Clear existing gauges for Particulate Matter
                $('#gauge-div-pmOne').empty();
                $('#gauge-div-pmTwoFive').empty();
                $('#gauge-div-pmTen').empty();

                // Update each Particulate Matter gauge with respective data
                drawJustGauge('gauge-div-pmOne', response.pmOne, 'Particulate Matter 1 (Latest)');
                drawJustGauge('gauge-div-pmTwoFive', response.pmTwoFive, 'Particulate Matter 2.5 (Latest)');
                drawJustGauge('gauge-div-pmTen', response.pmTen, 'Particulate Matter 10 (Latest)');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // 2nd gauges ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/gauges_two.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                // Clear Existing Gauges for Gas Levels
                $('#gauge-div-co2').empty();
                $('#gauge-div-humidity').empty();

                // Update each Gas Level gauge with respective data
                drawJustGaugeCo2('gauge-div-co2', response.paramCo2Level, 'Co2 Level (Latest)');
                drawJustGaugeHumidity('gauge-div-humidity', response.paramHumidity, 'Relative Humidity (Latest)');

                // Clear existing gauge for temperature
                $('#gauge-div-temperature').empty();
                $('#gauge-div-heatIndex').empty();

                // Update temperature gauge with respective data
                drawJustGaugeTemp('gauge-div-temperature', response.paramTemperature, 'Temperature (Latest)');
                drawJustGaugeTemp('gauge-div-heatIndex', response.paramHeatIndex, 'Heat Index (Latest)');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // pm charts ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/pm_charts.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                var dataPoints = response.dataPoints;

                // Process data for line chart
                var labelsPM = dataPoints.map(function(point) {
                    return point.formatted_time;
                });
                var pmOneData = dataPoints.map(function(point) {
                    return point.pm_one;
                });
                var pmTwoFiveData = dataPoints.map(function(point) {
                    return point.pm_two_five;
                });
                var pmTenData = dataPoints.map(function(point) {
                    return point.pm_ten;
                });

                // Update line chart
                initializePMChart(labelsPM, pmOneData, pmTwoFiveData, pmTenData);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // temperature charts ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/temp_charts.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                var dataPoints = response.dataPoints;

                // Process data for line chart
                var labelsTemp = dataPoints.map(function(point) {
                    return point.formatted_time;
                });
                var dataTemp = dataPoints.map(function(point) {
                    return point.param_temp;
                });
                var dataHeatIndex = dataPoints.map(function(point) {
                    return point.heat_index;
                });

                // Update line chart
                initializeTempChart(labelsTemp, dataTemp, dataHeatIndex);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // co2 charts ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/co2_charts.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                var dataPoints = response.dataPoints;

                // Process data for line chart
                var labelsCo2 = dataPoints.map(function(point) {
                    return point.formatted_time;
                });
                var dataCo2 = dataPoints.map(function(point) {
                    return point.co2_level;
                });

                // Update line chart
                initializeCo2Chart(labelsCo2, dataCo2);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

        // humidity charts ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/humidity_charts.php',
            data: {
                interval: interval,
                room_num: roomNumber
            },
            success: function(response) {
                var dataPoints = response.dataPoints;

                var labelsHumidity = dataPoints.map(function(point) {
                    return point.formatted_time;
                });
                var dataHumidity = dataPoints.map(function(point) {
                    return point.rel_humid;
                });

                // Update the chart with new data
                initializeHumidityChart(labelsHumidity, dataHumidity);
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
                interval: interval,
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
    }
</script>