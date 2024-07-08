<?php
require_once('ec_connect.php');

$table = isset($_GET['table']) ? $_GET['table'] : 'Selected Table';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Building Mangement Head</title>
    <link rel="stylesheet" href="../../styles/general.css">
    <link rel="shortcut icon" href="../../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <link rel="stylesheet" href="../../styles/homepage/homepage.css">
    <link rel="stylesheet" href="../../styles/building_head/ec_dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/logout.js"></script>
</head>

<body>
    <div class="navbar">
        <div class="logo-container" onclick="location.href='../../Home/homepage.php';">
            <div class="ram-health-logo">RAM Health</div>
        </div>
        <div class="log-out-container">
            <div class="log-out">
                <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" onselectstart="return false;" onclick="collapse_logout()">
                </span>
            </div>
            <ul id="btn_logout" class="log-out-display" style="display: none;">
                <form id="logout" name="logout-form" method="POST" action="../../Login/session_logout.php">
                    <button class="logout-button" type="submit" name="logout">
                        <span class="fas fa-power-off"></span>
                        Logout
                    </button>
                </form>
            </ul>
        </div>
    </div>
    <div class="page-content">
        <div class="content-header">
            <div class="return-button">
                <button id="back-button" class="export-btn" onclick="location.href='../building_head_menu.php';">
                    <span class="fas fa-arrow-left" style="margin-top: 1.1px; margin-left: 10px; margin-right: 5px;"></span> Go Back </button>
            </div>
            <?php require_once('input_table.php'); ?>
            <div class="rd-selection" style="margin-top: 0px; margin-left: 1195px;">
                <form class="export-table" method="POST" action="elements/ec_export.php" id="exportForm">
                    <label class="export-btn" for="exportBtn">
                        <span class="fas fa-file-export"></span>
                        <span style="display: inline-block;">Download Data</span>
                    </label>
                    <input type="hidden" id="table_export" name="table_name" value="<?php echo htmlspecialchars($table); ?>">
                    <button type="submit" id="exportBtn" style="display: none;"></button>
                </form>
            </div>
        </div>
        <div class="dashboard-content">
            <div class="left-dashboard">
                <div class="left-dashboard-one">
                    <div class="total-consume">
                        <h2 style="margin-top: 10px; margin-bottom: 10px;">Total Energy Consumption</h2>
                        <h4>Please choose a specific table and room...</h4>
                        <?php require_once 'elements/ec_current_total.php'; ?>
                    </div>
                    <div class="peak-consume">
                        <h2 style="margin-top: 10px; margin-bottom: 10px;">Highest Energy Consumption</h2>
                        <h4>Please choose a specific table and room...</h4>
                        <?php require_once 'elements/ec_peak.php'; ?>
                    </div>
                </div>
                <div class="left-dashboard-two" id="dashboard-two">
                    <h2 style="margin-top: 10px; margin-bottom: 10px;">Energy Consumption Readings</h3>
                        <h4>Please choose a specific table and room...</h4>
                        <div id="ec-rooms-bar">
                            <?php require_once 'elements/ec_rooms_bar.php'; ?>
                        </div>
                </div>
            </div>
            <div class="right-dashboard">
                <h2>Latest Energy Consumption Reading</h2>
                <h3>Please choose a specific table and room...</h3>
                <?php require_once 'elements/ec_rooms_monitor.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Function for choosing a data
    function chooseData() {
        var selectedData = document.getElementById('data-select').value;

        document.getElementById("table_export").value = selectedData;

        var selectedInterval = document.getElementById('table_export').value;
        var selectedRoom = document.getElementById('table_room').value;

        document.getElementById("selected_interval").value = selectedInterval;

        refreshElements(selectedRoom);
    }

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