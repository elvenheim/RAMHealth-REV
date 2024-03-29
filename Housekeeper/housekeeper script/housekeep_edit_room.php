<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Housekeeper</title> 
        <link rel="stylesheet" href="../../styles/housekeeper/housekeeper_edit_room.css">
        <link rel="shortcut icon" href="../../images/apc-logo.ico"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../housekeeper script/housekeeper.js"></script>
    </head>
    <body>
        <?php
            require_once('housekeep_connect.php');
            // edit_room.php

            // Check if the form is submitted and the room_num parameter is present
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_num'])) {
                $roomNum = $_POST['room_num'];
                
                // Retrieve the room details from the database based on the room number
                $query = "SELECT * FROM room_number WHERE room_num = '$roomNum'";
                $result = mysqli_query($con, $query);
                
                // Check if the room exists
                if (mysqli_num_rows($result) > 0) {
                    $room = mysqli_fetch_assoc($result);
                    
                    // Display the form to edit the room details
                    echo '<div class="form-container">';
                    echo '<form method="post" action="update_room.php">'; // Replace with your update page URL
                    
                    echo '<div class="form-title">';
                    echo 'Edit Facility';
                    echo '</div>';
                    
                    echo '<div class="building-floor-container">';
                    echo '<label for="bldg_floor">Building Floor:</label>';
                    
                    $buildingFloorQuery = "SELECT building_floor, bldg_floor_name FROM building_floor";
                    $buildingFloorResult = mysqli_query($con, $buildingFloorQuery);
                    
                    echo '<select name="bldg_floor">';
                    while ($row = mysqli_fetch_assoc($buildingFloorResult)) {
                        echo '<option value="' . $row['building_floor'] . '"' . ($row['building_floor'] == $room['bldg_floor'] ? ' selected' : '') . '>' . 
                        $row['bldg_floor_name'] . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';

                    echo '<input type="hidden" name="previous_room_num" value="' . $room['room_num'] . '">';

                    echo '<label for="room_num">Facility:</label>';
                    echo '<input type="text" name="room_num" value="' . $room['room_num'] . '">';             

                    echo '<label for="room_type">Facility Type:</label>';
                    echo '<input type="text" name="room_type" value="' . $room['room_type'] . '">';

                    // Add other fields you want to edit

                    echo '<div class="form-buttons">';
                    echo '<input type="submit" value="Update">';
                    echo '<button type="button" class="cancel-edit-button" onclick="goBack()">Cancel</button>';
                    echo '</div>';
                    echo '</form>';
                    echo '</div>';
                } else {
                    echo 'Room not found.';
                }
            } else {
                echo 'Invalid request.';
            }
        ?>
    </body>
</html>