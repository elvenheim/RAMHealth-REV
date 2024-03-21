<?php
    require_once('housekeep_connect.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Disable foreign key checks
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 0");

        // Retrieve data of the room that is being deleted
        $room_number = $_POST['room_num'];

        $select_query = "SELECT * FROM deleted_room_num WHERE room_num = ?";
        $stmt = mysqli_prepare($con, $select_query);
        mysqli_stmt_bind_param($stmt, 's', $room_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Delete the room from the room_number table
        $delete_query = "DELETE FROM deleted_room_num WHERE room_num = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, 's', $room_number);
        mysqli_stmt_execute($stmt);

        // Enable foreign key checks
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 1");

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Room has been deleted successfully.";
        } else {
            echo "Error deleting room: " . mysqli_error($con);
        }
    }
?>
