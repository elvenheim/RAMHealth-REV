<?php
    require_once('aq_sensor_connect.php');
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve the updated sensor details from the form
        $sensor_id = $_POST['sensor_id'];
        $new_aq_sensor_id = $_POST['new_aq_sensor_id'];
        $new_aq_sensor_name = $_POST['new_aq_sensor_name'];
        $new_sensor_type = $_POST['sensor_type'];
        $new_room_number = $_POST['room_number'];
        $sensor_added_at = $_POST['sensor_added_at'];
        $sensor_status = $_POST['sensor_status'];
        
        // Disable foreign key checks
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 0");
        
        // Delete the existing row
        $deleteQuery = "DELETE FROM aq_sensor WHERE aq_sensor_id = '$sensor_id'";
        $deleteResult = mysqli_query($con, $deleteQuery);
        
        if ($deleteResult) {
            // Insert the new row with updated data
            $insertQuery = "INSERT INTO aq_sensor (aq_sensor_id, aq_sensor_name, aq_sensor_type, aq_sensor_room_num, aq_sensor_added_at, aq_sensor_status) 
                            VALUES ('$new_aq_sensor_id', '$new_aq_sensor_name', '$new_sensor_type', '$new_room_number', '$sensor_added_at', '$sensor_status')";
            $insertResult = mysqli_query($con, $insertQuery);
            
            if ($insertResult) {
                // Enable foreign key checks
                mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 1");
                echo '<script type="text/javascript">alert("Sensor updated successfully.");
                window.location.href="../aq_tech_sensors.php"</script>';
            } else {
                echo 'Failed to insert new sensor data.';
            }
        } else {
            echo 'Failed to delete existing sensor.';
        }
        

    } else {
        echo 'Invalid request.';
    }
?>
