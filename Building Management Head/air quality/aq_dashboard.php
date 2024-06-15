<?php require_once('aq_connect.php'); ?>

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
    <link rel="stylesheet" href="../../styles/building_head/aq_dashboard.css">
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
            <?php require_once('input_floor.php'); ?>
        </div>
        <div class="dashboard-content">
            <div class="left-dashboard">
                <?php require_once('elements/pm_gauges.php'); ?>
            </div>
            <div class="middle-dashboard">
                <div class="line-chart-container" style="width: 800px; margin: 20px auto; margin-top: 8px;">
                    <?php require_once('elements/pm_charts_five.php'); ?>
                    <?php require_once('elements/pm_charts_daily.php'); ?>
                </div>
            </div>
            <div class="right-dashboard">
                <div class="right-dashboard-one">
                    <?php require_once('elements/temperature_gauges.php'); ?>
                    <?php require_once('elements/others.php'); ?>
                </div>
                <div class="right-dashboard-two">
                    <div class="rd-header">
                        <h3>Latest Data Summary</h3>
                    </div>
                    <div class="rd-selection">
                        <div class="sorting-dropdown" style="margin-left: 40px; margin-right: 20px;">
                            <label for="data-select">Interval:</label>
                            <select id="data-select" onchange="chooseData()">
                                <option value=" " disabled selected>Select Interval</option>
                                <option value="aq_param_five">5-Minutes</option>
                                <option value="aq_param_daily">Daily</option>
                            </select>
                        </div>
                        <form class="export-table" method="POST" action="elements/aq_export.php" id="exportForm">
                            <label class="export-btn" for="exportBtn">
                                <span class="fas fa-file-export"></span>
                                <span style="display: inline-block;">Export</span>
                            </label>
                            <input type="hidden" id="table_export" name="table_name" value="aq_param_five">
                            <input type="hidden" id="table_room" name="table_room" value="0">
                            <button type="submit" id="exportBtn" style="display: none;"></button>
                        </form>
                    </div>
                    <div id="data-summary" class="data-summary-content">
                        <?php require_once('elements/aq_param_summary.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    // Function for choosing a data
    function chooseData() {
        var selectedData = document.getElementById('data-select').value;

        document.getElementById("table_export").value = selectedData;

        var selectedInterval = document.getElementById('table_export').value;
        var selectedRoom = document.getElementById('table_room').value;

        document.getElementById("selected_interval").value = selectedInterval;

        // aq parameter summary ajax callback
        $.ajax({
            type: 'POST',
            url: 'elements/aq_param_summary.php',
            data: {
                table_interval: selectedInterval,
                selected_room: selectedRoom
            },
            success: function(response) {
                // Update the content of the data-summary-content div with the response
                document.getElementById('data-summary').innerHTML = response;
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
</script>