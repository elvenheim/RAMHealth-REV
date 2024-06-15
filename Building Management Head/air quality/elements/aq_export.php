<?php
    require_once('dashboard_connect.php');

    // Check if POST data exists
    if (isset($_POST['table_name']) && isset($_POST['table_room'])) {
        $tableName = $_POST['table_name'];
        $roomNum = $_POST['table_room'];

        $exportSuccessful = false;
        $errorMessage = "";

        // Fetch the table data with an optional room number filter
        $query = "SELECT * FROM $tableName";
        if (!empty($roomNum)) {
            $query .= " WHERE room_id = '" . mysqli_real_escape_string($con, $roomNum) . "'";
        }
        $result = mysqli_query($con, $query);

        if ($result) {
            $csvFileName = $tableName . '_' . $roomNum. '.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . $csvFileName);

            $output = fopen('php://output', 'w');

            // Fetch the table headers
            $fields = mysqli_fetch_fields($result);
            $headers = [];
            foreach ($fields as $field) {
                $headers[] = $field->name;
            }
            fputcsv($output, $headers);

            // Fetch the table rows
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, $row);
            }

            fclose($output);
            $exportSuccessful = true;
        } else {
            $errorMessage = "Error fetching table data: " . mysqli_error($con);
            echo $errorMessage;
        }

        mysqli_close($con);
    } else {
        echo "Required POST data missing.";
        var_dump($_POST);  // Debugging: output the POST data to verify
    }
?>
