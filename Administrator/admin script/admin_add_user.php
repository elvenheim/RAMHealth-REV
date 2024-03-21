<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RAM Health</title> 
    <link rel="stylesheet" href="../../styles/administrator/admin_edit_user.css">
    <link rel="shortcut icon" href="../../images/apc-logo.ico"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../admin script/admin.js"></script>
</head>
<body>
    <div class="form-container">
        <form method="post" action="admin_fetch_new_user_input.php">
            <div class="form-title">
                Add User
            </div>
            
            <label for="employee_id">Employee ID:</label>
            <input type="number" id="employee_id" name="employee_id" required><br>

            <label for="employee_fullname">Full Name:</label>
            <input type="text" id="employee_fullname" name="employee_fullname" required><br>

            <label for="employee_email">Email Address:</label>
            <input type="email" id="employee_email" name="employee_email" required><br>

            <label for="employee_password">Password:</label>
            <input type="text" id="employee_password" name="employee_password" required><br>

            <div class="assign-roles-container">
                <label for="role_name">Assign Roles:</label>
                <label>
                    <input type="checkbox" id="uncheck_all" onclick="uncheckAll(this)"> Select All
                </label>
            </div>
            <div class="checkbox-list">
                <?php
                    require_once('admin_connect.php');

                    $roleIdsQuery = "SELECT role_id, role_name FROM role_type";
                    $roleIdsResult = mysqli_query($con, $roleIdsQuery);
                    
                    while ($row = mysqli_fetch_assoc($roleIdsResult)) {
                        echo '<label>';
                        echo '<input type="checkbox" name="role_name[]" value="' . $row['role_id'] . '"> ' . $row['role_name'];
                        echo '</label>';
                    }
                ?>
            </div>

            <div class="form-buttons">
                <input type="submit" value="Add User">
                <button type="button" class="cancel-edit-button" onclick="goBack()">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
