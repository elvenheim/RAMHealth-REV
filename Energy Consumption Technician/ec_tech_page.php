<?php
require_once('../scripts/database_connect.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Energy Consumption Technician</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/ectech/main_page.css">
    <link rel="stylesheet" href="../styles/ectech/ec_tech_table.css">
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
            <span><a class="param-table" href="ec_tech_page.php">Energy Consumption Parameters</a></span>
            <span><a class="sensor-table" href="ec_tech_sensors.php">Sensors Management</a></span>
            <span><a class="deleted-sensor" href="ec_tech_deleted_sensors.php">Deleted Sensors</a></span>
        </div>
        <div class="button-container">
            <?php include '../Energy Consumption Technician/ec tech script/input_table.php'; ?>
            <form class="import-table" method="POST" enctype="multipart/form-data" action="../scripts/import_table_energy.php" id="importForm">
                <label class="import-btn" style="margin-right: 20px; margin-left: 1225px;">
                    <span class="fas fa-file-import"></span>
                    <span style="display: inline-block;">Import</span>
                    <input type="hidden" id="table_name" name="table_name">
                    <input type="file" name="csv_file" id="csvFile" style="display: none;" required accept=".csv" onchange="updateTableName()">
                </label>
            </form>
            <form class="export-table" method="POST" action="../scripts/export_table_energy.php" id="exportForm">
                <label class="export-btn" for="exportBtn">
                    <span class="fas fa-file-export"></span>
                    <span style="display: inline-block;">Export</span>
                </label>
                <input type="hidden" id="table_export" name="table_name" value="<?php echo htmlspecialchars($table); ?>">
                <button type="submit" id="exportBtn" style="display: none;"></button>
            </form>
        </div>
        <div class="table-container">
            <table class="table" id="ec-param-table">
                <?php include 'parameters/ec_tech_table.php'; ?>
            </table>
            <ul id="pagination" class="pagination"></ul>
        </div>
    </div>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Import form submission
    function updateTableName() {
        // Get the selected file
        const fileInput = document.getElementById('csvFile');
        const fileName = fileInput.files[0].name;

        // Remove the file extension
        const fileNameWithoutExtension = fileName.split('.').slice(0, -1).join('.');

        // Update the value of table_name input
        document.getElementById('table_name').value = fileNameWithoutExtension;

        // Submit form
        document.getElementById('importForm').submit();
    }
</script>