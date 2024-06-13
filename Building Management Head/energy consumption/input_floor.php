<?php
require_once('ec_connect.php');

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
    <select id="facility-sort">
        <option value="">Select Facility</option>
        <!-- Options will be populated by AJAX -->
    </select>
</div>