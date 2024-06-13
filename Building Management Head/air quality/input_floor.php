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
    <select id="facility-sort" onchange="refreshGauges(this.value)">
        <option value="">Select Facility</option>
        <!-- Options will be populated by AJAX -->
    </select>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function refreshGauges(roomNumber) {
        $.ajax({
            type: 'POST',
            url: 'gauges/pm_gauges.php',
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
    }
</script>