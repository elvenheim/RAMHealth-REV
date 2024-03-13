<?php 
    require_once('admin_connect.php');

    $rows_per_page = 10;

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $offset = ($page - 1) * $rows_per_page;

	$count_query = "SELECT COUNT(*) as count FROM deleted_users";
	$count_result = mysqli_query($con, $count_query);
	$count_row = mysqli_fetch_assoc($count_result);
	$total_rows = $count_row['count'];

	$total_pages = ceil($total_rows / $rows_per_page);

	$sql = "SELECT ul.*, u.user_role, GROUP_CONCAT(r.role_name ORDER BY r.role_name SEPARATOR ', ') AS role_names, u.user_status
			FROM user_list ul
			JOIN user u ON ul.employee_id = u.employee_id
			JOIN role_type r ON FIND_IN_SET(r.role_id, u.user_role) > 0
			GROUP BY ul.employee_id
			ORDER BY u.user_status DESC
			LIMIT $offset, $rows_per_page";

	$result_table = mysqli_query($con, $sql);
	
	if ($total_rows == 0) {
		echo '<span class="table-no-record"> No users are registered in the database...</span>';
	} else {
		while ($row = mysqli_fetch_assoc($result_table)) {
			$highlightClass = ($row['employee_id'] == $_SESSION['employee_id']) ? 'highlight-row' : '';
			$disabledClass = ($row['user_status'] == 0) ? 'disabled' : '';
			echo '<tr class="' . $disabledClass . ' ' . $highlightClass . '">';
			echo '<td style="width: max-content;">' . $row['employee_id'] . '</td>';
			echo '<td>' . $row['employee_fullname'] . '</td>';
			echo '<td>' . $row['employee_email'] . '</td>';
            $roles = explode(',', $row['role_names']);
            $numberOfRoles = count($roles);
            $cellClass = ($numberOfRoles >= 3) ? 'overflow-cell' : '';
            echo '<td class="' . $cellClass . '">' . implode(', ', $roles) . '</td>';
            echo '<td style="max-width: 100px;">' . $row['employee_create_at'] . '</td>';
			echo '<td style="max-width: 100px;">';
			echo '<form class="status-form">';
			echo '<input type="hidden" name="employee_id" value="' . $row['employee_id'] . '">';
			echo '<select name="user_status" onchange="updateStatus(this.form);"';
			if ($row['employee_id'] == $_SESSION['employee_id']) {
				echo ' disabled';
			}
			echo '>';
			echo '<option class="status-enabled" value="1"' . ($row['user_status'] == 1 ? ' selected' : '') . '>Enabled</option>';
			echo '<option class="status-disabled" value="0"' . ($row['user_status'] == 0 ? ' selected' : '') . '>Disabled</option>';
			echo '</select>';
			echo '</form>';
			echo '</td>';
			echo '<td class="action-buttons">';
			echo '<div>';
			echo '<button class="edit-button" type="button" onclick="editRow(' . $row['employee_id'] . ')"> 
					<i class="fas fa-edit"></i></button>';
			echo '<button class="delete-button" type="button" onclick="deleteRow(' . $row['employee_id'] . ')"> 
					<i class="fas fa-trash"></i></button>';
			echo '</div>';
			echo '</td>';
			echo '</tr>';
		}
	}
?>