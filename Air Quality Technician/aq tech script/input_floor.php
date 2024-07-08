<?php
require_once('aq_param_connect.php');

$roomQuery = "SELECT bf.* FROM building_floor bf";
$roomResult = mysqli_query($con, $roomQuery);

if (!$roomResult) {
    die("Database query failed: " . mysqli_error($con));
}
?>

<div class="sorting-dropdown" style="margin-left: 10px; margin-right: 10px;">
    <label for="floor-by">Floor:</label>
    <select id="floor-by" onchange="chooseFloor()">
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
        var floorId = document.getElementById('floor-by').value;
        if (floorId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'aq tech script/get_facilities.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('facility-sort').innerHTML = xhr.responseText;
                }
            };
            xhr.send('floor_id=' + floorId);
        }
    }
</script>

<div id="facility-dropdown" class="sorting-dropdown">
    <label for="facility-sort">Facility:</label>
    <select id="facility-sort" onchange="selectRoom()">
        <option value=" " disabled selected>Select Facility</option>
        <!-- Options will be populated by AJAX -->
    </select>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>