<?php
    require_once('../scripts/database_connect.php');

    // Get the table name from the form submission
    $tableName = $_POST['table_name'];

    $exportSuccessful = false;
    $errorMessage = "";

    // Fetch the table data
    $query = "SELECT * FROM $tableName";
    $result = mysqli_query($con, $query);

    if ($result) {
        $csvFileName = $tableName . '.csv';
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
    }

    mysqli_close($con);

    $referrer = $_SERVER['HTTP_REFERER'];
?>