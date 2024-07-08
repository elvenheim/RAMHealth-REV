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
        $total_wh = 0; // Initialize total in Wh

        foreach ($data as $column => $value) {
            // Extract the numeric part and unit
            if (preg_match('/([\d\.]+)\s*(kWh|Wh|mWh)/i', $value, $matches)) {
                $number = floatval($matches[1]);
                $unit = strtolower($matches[2]);

                // Convert to Wh
                switch ($unit) {
                    case 'kwh':
                        $total_wh += $number * 1000;
                        break;
                    case 'mwh':
                        $total_wh += $number / 1000;
                        break;
                    case 'wh':
                    default:
                        $total_wh += $number;
                        break;
                }
            }
        }

        // Determine the best unit for display
        if ($total_wh >= 1000) {
            $total_display = $total_wh / 1000;
            $unit_display = 'kWh';
        } elseif ($total_wh < 1) {
            $total_display = $total_wh * 1000;
            $unit_display = 'mWh';
        } else {
            $total_display = $total_wh;
            $unit_display = 'Wh';
        }

        echo '<h2 style="margin-top: 10px; margin-bottom: 30px;">Total Energy Consumption</h2>';
        echo "<div class='total-consume-box'>{$total_display} {$unit_display}</div>";
    } else {
        echo "No data found.";
    }
}
?>
