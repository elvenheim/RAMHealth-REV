<?php
    require_once('housekeep_connect.php');

    // Check if the form is submitted and the necessary parameters are present
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_num'], $_POST['bldg_floor'], $_POST['room_type'])) {
        $prevRoomNum =$_POST['previous_room_num'];
        $roomNum = $_POST['room_num'];
        $bldgFloor = $_POST['bldg_floor'];
        $roomType = $_POST['room_type'];
        $roomCreatedAt = date('Y-m-d');

        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 0");

        // Perform the necessary validation and sanitization of the inputs

        // Update the room record with the new details
        $updateQuery = "UPDATE room_number 
                        SET room_num = '$roomNum', bldg_floor = '$bldgFloor', room_type = '$roomType', room_added_at = '$roomCreatedAt' 
                        WHERE room_num = '$prevRoomNum'";
        
        if (mysqli_query($con, $updateQuery)) {
            echo '<script type="text/javascript">alert("Room updated successfully.");
            window.location.href="../housekeeper_page.php"</script>';
        } else {
            echo 'Error updating room: ' . mysqli_error($con);
        }
    } else {
        echo 'Invalid request.';
    }
?>
