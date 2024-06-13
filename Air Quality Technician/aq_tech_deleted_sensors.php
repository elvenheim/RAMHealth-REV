<?php require_once('../scripts/database_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Air Quality Technician</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/aqtech/deleted_sensors.css">
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
                <label for="sort-by">Sort By:</label>
                <select id="sort-by" onchange="sortTable()">
                    <option value="bldg_floor">Building Floor</option>
                    <option value="room_num">Facility</option>
                    <option value="deleted_aq_sensor_id">Sensor ID</option>
                    <option value="deleted_aq_sensor_name">Sensor Name</option>
                    <option value="sensor_type_name">Sensor Type</option>
                    <option value="deleted_aq_sensor_add_At">Date Added</option>
                    <option value="deleted_aq_sensor_deleted_at">Deleted At</option>
                </select>
            </div>
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
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sensor-manage-table">
                    <?php include '../Air Quality Technician/sensor tables/deleted_sensor_table.php' ?>
                </tbody>
            </table>
            <ul id="pagination" class="pagination">
            </ul>
            </table>
        </div>
    </div>
</body>

</html>