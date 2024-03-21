<?php require_once('../scripts/database_connect.php');?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Air Quality Technician</title>
        <link rel="stylesheet" href="../styles/general.css">
        <link rel="stylesheet" href="../styles/aqtech/sensors_page.css">
        <link rel="stylesheet" href="../styles/aqtech/aq_tech_table.css">
        <link rel="shortcut icon" href="../images/apc-logo.ico"/>
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
                    <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" 
                        onselectstart="return false;" onclick="collapse_logout()">
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
                <a class="param-table" href="aq_tech_page.php">
                    Parameter Tables
                </a>
                <a class="sensor-table" href="aq_tech_sensors.php">
                    Sensor Management
                </a>
                <a class="deleted-sensor" href="aq_tech_deleted_sensors.php">
                    Deleted Sensors
                </a>
            </div>
            <a style="opacity: 1;" id="add-user-btn" class="add-btn" href="../Housekeeper/housekeeper script/add_room.php">
                <span class="fas fa-plus"></span>
                <span style="display: inline-block;">Add Sensor</span>
            </a>
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
                            <?php include '../Air Quality Technician/sensor tables/sensor_manage_table.php'?>
                        </tbody>
                    </table>
                    <ul id="pagination" class="pagination">
                    </ul>
                </table>
            </div>
        </div>
    </body>
</html>