<?php
    require_once('aq_sensor_connect.php');

    $rows_per_page = 10;

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $offset = ($page - 1) * $rows_per_page;

    $count_query = "SELECT COUNT(*) as count FROM aq_sensor";
    $count_result = mysqli_query($con, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];

    $total_pages = ceil($total_rows / $rows_per_page);

    $sql = "SELECT aq.*, st.sensor_type_name, rn.room_num, rn.bldg_floor
        FROM aq_sensor aq
        LEFT JOIN room_number rn ON aq_sensor_room_num = rn.room_num
        LEFT JOIN sensor_type st ON aq.aq_sensor_type = st.sensor_type_id
        ORDER BY rn.room_num ASC, aq.aq_sensor_status DESC
        LIMIT $offset, $rows_per_page";
    $result_table = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_assoc($result_table)){
        echo "<tr" . ($row['aq_sensor_status'] == 0 ? " class=\"disabled\"" : '') . ">";
        echo '<td style="width: 100px">' . $row['bldg_floor'] . "</td>";
        echo "<td>" . $row['room_num'] . "</td>";
        echo "<td>" . $row['aq_sensor_id'] . "</td>";
        echo "<td>" . $row['aq_sensor_name'] . "</td>";
        echo "<td>" . $row['sensor_type_name'] . "</td>";
        echo "<td>" . $row['aq_sensor_added_at'] . "</td>";
        echo "<td>";
        echo '<form class="status-form">';
        echo '<input type="hidden" name="aq_sensor_id" value="' . $row['aq_sensor_id'] . '">';
        echo '<select name="aq_sensor_status" onchange="updateStatus(this.form);">';
        echo '<option class="status-enabled" value="1"' . ($row['aq_sensor_status'] == 1 ? ' selected' : '') . '>Enabled</option>';
        echo '<option class="status-disabled" value="0"' . ($row['aq_sensor_status'] == 0 ? ' selected' : '') . '>Disabled</option>';
        echo '</select>';
        echo '</form>';
        echo "</td>";
        echo '<td class="action-buttons">';
        echo '<div>';
        echo '<button class="edit-button" type="button" onclick="editRow(\'' . $row['aq_sensor_id'] . '\')"> 
                <i class="fas fa-edit"></i></button>';
        echo '<button class="delete-button" type="button" onclick="deleteRow(\'' . $row['aq_sensor_id'] . '\')"> 
                <i class="fas fa-trash"></i></button>';
        echo '</div>';
        echo "</td>";
        echo "</tr>";
    }
?>