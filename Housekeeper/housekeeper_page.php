<?php require_once('../scripts/database_connect.php');?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Housekeeper</title>
        <link rel="stylesheet" href="../styles/general.css">
        <link rel="stylesheet" href="../styles/housekeeper/main_page.css">
        <link rel="stylesheet" href="../styles/housekeeper/housekeeper_table.css">
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
                <a class="room-management" href="housekeeper_page.php">
                    Room Management
                </a>
                <a class="deleted-room" href="housekeeper_delete_rooms.php">
                    Deleted Rooms
                </a>
            </div>
            <a style="opacity: 1;" id="add-user-btn" class="add-btn" href="../Housekeeper/housekeeper script/add_room.php">
                <span class="fas fa-plus"></span>
                <span style="display: inline-block;">Add Room</span>
            </a>
            <div class="room-table-container">
                <table class="room-management-table">
                        <thead>
                            <tr>
                                <th>Building Floor</th>
                                <th>Facility</th>
                                <th>Facility Type</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="room-manage-table">
                            <?php include '../Housekeeper/housekeeper script/room_manage_table.php'?>
                        </tbody>
                    </table>
                    <ul id="pagination" class="pagination">
                    </ul>
                </table>
            </div>
        </div>
    </body>
</html>