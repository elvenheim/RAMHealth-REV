<?php require_once('../scripts/database_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrator</title>
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/administrator/admin_manage_user.css">
    <link rel="stylesheet" href="../styles/administrator/admin_table.css">
    <link rel="shortcut icon" href="../images/apc-logo.ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../scripts/logout.js"></script>
    <script src="../Administrator/admin script/admin.js"></script>
</head>

<body>
    <div class="navbar">
        <div class="logo-container" onclick="location.href='../Home/homepage.php';">
            <div class="ram-health-logo">RAM Health</div>
        </div>
        <div class="log-out-container">
            <div class="log-out">
                <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" onselaectstart="return false;" onclick="collapse_logout()">
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
            <a class="user-management" href="admin_page.php">
                User Management
            </a>
            <a class="deleted-users" href="admin_deleted_users.php">
                Deleted Users
            </a>
        </div>
        <div class="button-container">
            <div class="sorting-dropdown">
                <label for="sort-by">Sort By:</label>
                <select id="sort-by" onchange="sortTable()">
                    <option value="employee_id">User ID</option>
                    <option value="employee_fullname">Full Name</option>
                    <option value="employee_email">Email Address</option>
                    <option value="role_names">Role</option>
                    <option value="employee_create_at">Created At</option>
                    <option value="user_status">Account Status</option>
                </select>
            </div>
            <a id="add-user-btn" class="add-btn" href="../Administrator/admin script/admin_add_user.php">
                <span class="fas fa-plus"></span>
                <span style="display: inline-block;">Add User</span>
            </a>
        </div>
        <div class="user-table-container">
            <table class="user-management-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Account Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table">
                    <?php include '../Administrator/admin script/users_table.php' ?>
                </tbody>
            </table>
            <ul id="pagination" class="pagination">
            </ul>
            </table>
        </div>
    </div>
</body>

</html>