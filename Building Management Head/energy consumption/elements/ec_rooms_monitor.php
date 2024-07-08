<?php
require_once 'dashboard_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_table = $_POST['table'];
    $selected_columns = $_POST['columns'];

    if (empty($selected_table) || empty($selected_columns)) {
        echo 'No table or columns selected';
        exit;
    }

    // Sanitize inputs
    $selected_table = mysqli_real_escape_string($con2, $selected_table);
    $selected_columns = array_map(function ($column) use ($con2) {
        return '`' . mysqli_real_escape_string($con2, $column) . '`';
    }, $selected_columns);

    // Build the query to fetch the latest values
    $columns_string = implode(', ', $selected_columns);
    $query = "SELECT $columns_string FROM `$selected_table` ORDER BY `Date Acquired` DESC LIMIT 1";
    $result = mysqli_query($con2, $query);

    if (!$result) {
        echo "Error fetching data: " . mysqli_error($con2);
        exit;
    }

    // Fetch the data and display it
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $count = 0; // Initialize column count
        echo '<h2>Latest Energy Consumption Reading</h2>';
        foreach ($data as $column => $value) {
            if ($count % 5 === 0) {
                echo '<div class="room-consume-row">'; // Start a new row after every 5 columns
            }
            echo "<div class='room-consume-box'><strong>$column </strong>$value</div>";
            $count++;

            if ($count % 5 === 0) {
                echo '</div>'; // Close the row after every 5 columns
            }
        }

        // If the loop ends with an incomplete row, close it
        if ($count % 5 !== 0) {
            echo '</div>';
        }
    } else {
        echo "No data found.";
    }
}
