<?php
    require_once('../scripts/database_connect.php');
    
    $user_id = $_SESSION['employee_id'];

    $sql = "SELECT u.*, r.role_name, r.role_url, r.role_icon
            FROM user u
            JOIN user_list ul ON u.employee_id = ul.employee_id AND u.employee_id = ul.employee_id
            JOIN role_type r ON u.user_role = r.role_id
            WHERE u.employee_id = '$user_id'
            ORDER BY r.role_id";
                
    $result = mysqli_query($con, $sql);
                
    // Display the roles as links/buttons
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $role_name = $row['role_name'];
            $role_url = $row['role_url'];
            $role_icon = $row['role_icon'];
            echo "<div class='role-button' onclick=\"location.href='$role_url';\">
                <a class='role-link'>
                    <img src='$role_icon' class='role-icon'>
                    <div class='role-title'>$role_name</div>
                </a>
            </div>";
        }
    }
?>