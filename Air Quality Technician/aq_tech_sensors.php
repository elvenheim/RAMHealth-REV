<?php require_once('../scripts/database_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Air Quality Technician</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/aqtech/sensors_page.css">
    <link rel="stylesheet" href="../styles/aqtech/aq_tech_table.css">
    <link rel="shortcut icon" href="../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <script src="../scripts/logout.js"></script>
</head>

<body>
    <div class="navbar">
        <div class="logo-container" onclick="location.href='../Home/homepage.php';">
            <div class="ram-health-logo">RAM Health</div>
        </div>
        <div class="log-out-container">
            <div class="log-out">
                <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" onselectstart="return false;" onclick="collapse_logout()">
                </span>
            </div>
            <ul id="btn_logout" class="log-out-display" style="display: none;">
                <form id="logout" name="logout-form" method="POST" action="../Login/session_logout.php">
                    <button class="logout-button" type="submit" name="logout">
                        <span class="fas fa-power-off"></span>
                        Logout
                    </button>
                </form>
            </ul>
        </div>
    </div>
    <div class="page-content">
        <div class="header-container">
            <span><a class="param-table" href="aq_tech_page.php">Air Quality Parameters</a></span>
            <span><a class="sensor-table" href="aq_tech_sensors.php">Sensors Management</a></span>
            <span><a class="deleted-sensor" href="aq_tech_deleted_sensors.php">Deleted Sensors</a></span>
        </div>
        <div class="button-container">
            <div class="sorting-dropdown">
                <label for="floor-filter">Floor:</label>
                <select id="floor-filter" onchange="sortFloor()">
                    <?php
                        $sql = "SELECT bf.*
                                FROM building_floor bf";
                        $result_table = mysqli_query($con, $sql);

                        echo '<option value="none">-Select-</option>';

                        while ($row = mysqli_fetch_assoc($result_table)) {
                            echo '<option value="' . $row['building_floor'] . '">' . $row['bldg_floor_name'] . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="sorting-dropdown">
                <label for="sensor-type-filter">&nbsp &nbsp Sensor Type:</label>
                <select id="sensor-type-filter" onchange="sortSensorType()">
                    <?php
                        $sql = "SELECT st.*
                                FROM sensor_type st
                                WHERE st.sensor_class_id = 1";
                        $result_table = mysqli_query($con, $sql);

                        echo '<option value="none">-Select-</option>';

                        while ($row = mysqli_fetch_assoc($result_table)) {
                            echo '<option value="' . $row['sensor_type_id'] . '">' . $row['sensor_type_name'] . '</option>';
                        }
                    ?>
                </select>
            </div>
            <a style="opacity: 1;" id="add-user-btn" class="add-btn" href="../Housekeeper/housekeeper script/add_room.php">
                <span class="fas fa-plus"></span>
                <span style="display: inline-block;">Add Sensors</span>
            </a>
            <form class="import-table" method="POST" enctype="multipart/form-data" action="../scripts/import_table.php">
                <label class="import-btn">
                    <span class="fas fa-file-import"></span>
                    <span style="display: inline-block;"> Import</span>
                    <input type="hidden" id="table_name" name="table_name" value="room_number">
                    <input type="file" name="csv_file" style="display: none;" required accept=".csv" onchange="submitForm()">
                </label>
            </form>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Building Floor</th>
                        <th>Facility</th>
                        <th>Sensor ID</th>
                        <th>Sensor Name</th>
                        <th>Sensor Type</th>
                        <th>Date Added</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sensor-manage-table">
                    <?php include '../Air Quality Technician/sensor tables/sensor_manage_table.php' ?>
                </tbody>
            </table>
            <ul id="pagination" class="pagination">
            </ul>
        </div>
    </div>
</body>

</html>