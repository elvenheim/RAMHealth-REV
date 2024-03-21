<?php
    require_once('admin_connect.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Disable foreign key checks
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 0");

        $user_id = $_POST['employee_id'];

        // Get user data before deleting
        $select_query = "SELECT * FROM deleted_users WHERE deleted_employee_id = ?";
        $stmt = mysqli_prepare($con, $select_query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Delete related data from user_list tables
        $delete_query2 = "DELETE FROM user WHERE deleted_employee_id = ?";
        $stmt4 = mysqli_prepare($con, $delete_query2);
        mysqli_stmt_bind_param($stmt4, 'i', $user_id);
        mysqli_stmt_execute($stmt4);

        // Delete related data from user_list table
        $delete_query2 = "DELETE FROM deleted_users WHERE deleted_employee_id = ?";
        $stmt4 = mysqli_prepare($con, $delete_query2);
        mysqli_stmt_bind_param($stmt4, 'i', $user_id);
        mysqli_stmt_execute($stmt4);
        
        // Enable foreign key checks
        mysqli_query($con, "SET FOREIGN_KEY_CHECKS = 1");
    
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo 'User has been deleted successfully.';
        } else {
            echo "Error deleting user: " . mysqli_error($con);
        } 
    }
?>
