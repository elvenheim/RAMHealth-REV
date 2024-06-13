<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Air Quality Technician</title>
    <link rel="stylesheet" href="../../styles/aqtech/sensor_add.css">
    <link rel="shortcut icon" href="../../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <script src="../scripts/logout.js"></script>
</head>

<body>
    <div class="form-container">
        <form id="add_user" method="POST" class="user-input" action="fetch_sensor_input.php">
            <div class="form-title">
                Add Sensor
            </div>

            <label for="sensor-id">Sensor ID:</label>
            <input type="text" id="sensor_id" name="sensor_id" required><br>

            <label for="sensor-name">Sensor Name:</label>
            <input type="text" id="sensor_name" name="sensor_name" required><br>

            <?php
            require_once('aq_sensor_connect.php');

            // Sensor Type Dropdown
            $sensortypeIdsQuery = "SELECT sensor_type_id, sensor_type_name FROM sensor_type WHERE sensor_type_id BETWEEN 1 AND 5";
            $sensortypeIdsResult = mysqli_query($con, $sensortypeIdsQuery);

            echo '<label for="sensor_type">Sensor Type:</label>';
            echo '<select id="sensor_type" name="sensor_type" class="sensor_type" required>';
            echo '<option value="" disabled selected>-Select Sensor Type-</option>';
            while ($row = mysqli_fetch_assoc($sensortypeIdsResult)) {
                echo '<option value="' . $row['sensor_type_id'] . '">' . $row['sensor_type_name'] . '</option>';
            }
            echo '</select><br>';

            // Room Number Dropdown
            $roomQuery = "SELECT room_num FROM room_number";
            $roomResult = mysqli_query($con, $roomQuery);

            echo '<label for="room_number">Room Number:</label>';
            echo '<select id="room_number" name="room_number" class="room_number" required>';
            echo '<option value="" disabled selected>-Select Room-</option>';
            while ($row = mysqli_fetch_assoc($roomResult)) {
                echo '<option value="' . $row['room_num'] . '">' . $row['room_num'] . '</option>';
            }
            echo '</select><br>';
            ?>
            
            <div class="form-buttons">
                <button class="save-details" type="submit">Add Sensor</button>
            </div>
        </form>
    </div>
</body>

</html>