<?php require_once('../scripts/database_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Building Mangement Head</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="shortcut icon" href="../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <link rel="stylesheet" href="../styles/homepage/homepage.css">
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
        <div class="homepage-card">
            <div class="greet-user">
                <span>Please choose which parameter you want to view</span>
            </div>
            <div class="role-list-card">
                <div class="role-button" onclick="location.href='../Building Management Head/air quality/aq_dashboard.php';">
                    <a class="role-link">
                        <img src='../images/air_quality_icon.svg' class='role-icon'><br>
                        <div class="role-title">Air Quality</div>
                    </a>
                </div>
                <div class="role-button" onclick="location.href='../Building Management Head/energy consumption/ec_dashboard.php';">
                    <a class="role-link">
                        <img src='../images/energy_consumption_icon.svg' class='role-icon'><br>
                        <div class="role-title">Energy Consumption</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>