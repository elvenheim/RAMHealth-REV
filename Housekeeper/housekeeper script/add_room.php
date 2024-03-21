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
    <div class="form-container">
        <form method="post" action="fetch_new_room.php">
            <div class="form-title">
                Edit Facility
            </div>

            <label for="room_name">Facility Name:</label>
            <input type="text" name="room_name"><br>

            <div class="building-floor-container">
                <label for="bldg_floor">Building Floor:</label>
                <select name="bldg_floor" style="background-color: white;">
                    <?php 
                        require_once('housekeep_connect.php');
                        
                        $buildingFloorQuery = "SELECT bf.* FROM building_floor bf";
                        $buildingFloorResult = mysqli_query($con, $buildingFloorQuery);
                        
                        while ($row = mysqli_fetch_assoc($buildingFloorResult)) {
                            echo '<option value="' . $row['building_floor'] . '">' . $row['bldg_floor_name'] . '</option>';
                        }
                    ?>
                </select>
            </div>

            <label for="room_type">Facility Type:</label>
            <input type="text" name="room_type"><br>

            <!-- Add other fields you want to edit -->

            <div class="form-buttons">
                <input type="submit" value="Update">
                <button type="button" class="cancel-edit-button" onclick="goBack()">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
