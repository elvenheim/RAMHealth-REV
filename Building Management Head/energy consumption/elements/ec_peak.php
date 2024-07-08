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

    // Fetch the data
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $max_value = 0; // Initialize max value tracker
        $max_column = ''; // Initialize variable to store column with max value
        $max_unit = ''; // Initialize variable to store unit of max value

        foreach ($data as $column => $value) {
            // Extract the numeric part and unit
            if (preg_match('/([\d\.]+)\s*(kWh|Wh|mWh)/i', $value, $matches)) {
                $number = floatval($matches[1]);
                $unit = strtolower($matches[2]);

                // Convert to Wh for comparison
                switch ($unit) {
                    case 'kwh':
                        $value_in_wh = $number * 1000;
                        break;
                    case 'mwh':
                        $value_in_wh = $number / 1000;
                        break;
                    case 'wh':
                    default:
                        $value_in_wh = $number;
                        break;
                }

                // Check if this value is the highest found so far
                if ($value_in_wh > $max_value) {
                    $max_value = $value_in_wh;
                    $max_column = $column;
                    $max_unit = $unit;
                }
            }
        }

        // Determine the best unit for display
        if ($max_value >= 1000) {
            $max_display = $max_value / 1000;
            $unit_display = 'kWh';
        } elseif ($max_value < 1) {
            $max_display = $max_value * 1000;
            $unit_display = 'mWh';
        } else {
            $max_display = $max_value;
            $unit_display = 'Wh';
        }

        echo '<h2 style="margin-top: 10px; margin-bottom: 10px;">Highest Energy Consumption</h2>';
        echo "<h3>$max_column</h3>";
        echo "<div class='highest-consume-box'>{$max_display} {$unit_display}</div>";
    } else {
        echo "No data found.";
    }
}
