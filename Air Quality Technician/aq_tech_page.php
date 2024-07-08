<?php require_once('../scripts/database_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Air Quality Technician</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/aqtech/main_page.css">
    <link rel="stylesheet" href="../styles/aqtech/aq_param_table.css">
    <link rel="shortcut icon" href="../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <script src="../scripts/logout.js"></script>
    <script src="../scripts/table.js"></script>
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
            <?php include '../Air Quality Technician/aq tech script/input_floor.php' ?>
            <div class="sorting-dropdown" style="margin-left: 10px;">
                <label for="sort-by">Sort By:</label>
                <select id="sort-by" onchange="sortTable()">
                    <option value="room_id">Facility</option>
                    <option value="co2_level">CO2 Level (ppm)</option>
                    <option value="rel_humid">Relative Humidity (%)</option>
                    <option value="pm_one">PM1 (ug/m3)</option>
                    <option value="pm_two_five">PM2.5 (ug/m3)</option>
                    <option value="pm_ten">PM10 (ug/m3)</option>
                    <option value="param_temp">Temperature (°C)</option>
                    <option value="heat_index">Heat Index (°C)</option>
                    <option value="date_acquired">Date Acquired</option>
                </select>
            </div>
            <div class="sorting-dropdown" style="margin-left: 10px;">
                <label for="table-select">Interval:</label>
                <select id="table-select" onchange="changeTable()">
                    <option value="aq_param_five">5-Minutes</option>
                    <option value="aq_param_daily">Daily</option>
                </select>
            </div>
            <form class="import-table" method="POST" enctype="multipart/form-data" action="../scripts/import_table.php" id="importForm">
                <label class="import-btn" style="margin-right: 20px; margin-left: 595px;">
                    <span class="fas fa-file-import"></span>
                    <span style="display: inline-block;">Import</span>
                    <input type="hidden" id="table_name" name="table_name">
                    <input type="file" name="csv_file" id="csvFile" style="display: none;" required accept=".csv" onchange="updateTableName()">
                </label>
            </form>
            <form class="export-table" method="POST" action="../scripts/export_table.php" id="exportForm">
                <label class="export-btn" for="exportBtn">
                    <span class="fas fa-file-export"></span>
                    <span style="display: inline-block;">Export</span>
                </label>
                <input type="hidden" id="table_export" name="table_name" value="aq_param_five">
                <input type="hidden" id="table_room" name="table_room" value="0">
                <button type="submit" id="exportBtn" style="display: none;"></button>
            </form>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Facility</th>
                        <th>CO2 Level (ppm)</th>
                        <th>Relative Humidity (%)</th>
                        <th>PM1 (µg/m³)</th>
                        <th>PM2.5 (µg/m³)</th>
                        <th>PM10 (µg/m³)</th>
                        <th>Temperature (°C)</th>
                        <th>Heat Index (°C)</th>
                        <th>Date Acquired</th>
                    </tr>
                </thead>
                <tbody id="aq-param-table">
                    <?php include '../Air Quality Technician/aq tech script/aq_param_table_five.php' ?>
                </tbody>
            </table>
            <ul id="pagination" class="pagination">
            </ul>
        </div>
    </div>
</body>

</html>