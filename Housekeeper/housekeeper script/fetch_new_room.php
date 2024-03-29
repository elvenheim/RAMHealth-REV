<?php
    require_once('housekeep_connect.php');

    $room_number = $_POST['room_name'];
    $room_floor = $_POST['bldg_floor'];
    $room_type = $_POST['room_type'];
    $room_created_at = date('Y-m-d');

    $select_query = "SELECT room_num FROM room_number WHERE room_num = ?";
    $stmt = mysqli_prepare($con, $select_query);
    mysqli_stmt_bind_param($stmt, 's', $room_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($result) > 0) {
        echo '<script type="text/javascript">alert("Room already exists");
            window.location.href="housekeeper.php"</script>';
        exit;
    }

    $insert_query = "INSERT INTO room_number (room_num, bldg_floor, room_type, room_added_at) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, 'siss', $room_number, $room_floor, $room_type, $room_created_at);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo '<script type="text/javascript">alert("Room successfuly added");
            window.location.href="../housekeeper_page.php"</script>';
        exit;
    } else {
        echo '<script type="text/javascript">alert("Error adding room...");
            window.location.href="../housekeeper_page.php"</script>';
        exit;
    }
?>
