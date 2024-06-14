<?php
    require_once('aq_connect.php');

    if (isset($_POST['floor_id'])) {
        $floorId = $_POST['floor_id'];
        $facilityQuery = "SELECT rn.*, bf.building_floor
                        FROM room_number rn
                        JOIN building_floor bf ON rn.bldg_floor = bf.building_floor
                        WHERE rn.bldg_floor = '$floorId'
                        ORDER BY rn.room_num ASC";
        $facilityResult = mysqli_query($con, $facilityQuery);

        if (!$facilityResult) {
            die("Database query failed: " . mysqli_error($con));
        }

        echo '<label for="facility-sort">Facility:</label>';
        echo '<select id="facility-sort" onchange="refreshElements(this.value)">';
        echo '<option value="">Select Facility</option>';
        while ($row = mysqli_fetch_assoc($facilityResult)) {
            echo '<option value="' . $row['room_num'] . '">' . $row['room_num'] . '</option>';
        }
        echo '</select>';
    } else {
        echo '<label for="facility-sort">Facility:</label>';
        echo '<select id="facility-sort">';
        echo '<option value="">No facilities available</option>';
        echo '</select>';
    }
?>