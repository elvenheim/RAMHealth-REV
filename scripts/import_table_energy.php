<?php
require_once('../scripts/database_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && isset($_POST['table_name'])) {
    $tableName = mysqli_real_escape_string($con2, $_POST['table_name']);
    $csvFile = $_FILES['csv_file']['tmp_name'];

    // Open the CSV file
    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        // Get the first row to use as column names
        $columns = fgetcsv($handle, 1000, ',');

        if ($columns !== FALSE) {
            // Sanitize and remove any invisible characters (like BOM) from column names
            $columns = array_map(function ($column) use ($con2) {
                $column = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $column); // Remove invisible characters
                $column = trim($column, "\""); // Remove leading/trailing quotes
                return mysqli_real_escape_string($con2, $column);
            }, $columns);

            // Create the table with the specified column names
            $columnDefinitions = [];
            $totalColumns = count($columns);

            foreach ($columns as $index => $column) {
                if ($index == $totalColumns - 1) {
                    $columnDefinitions[] = "`$column` VARCHAR(255) UNIQUE";
                } else {
                    $columnDefinitions[] = "`$column` VARCHAR(255) NOT NULL";
                }
            }

            $createQuery = "CREATE TABLE IF NOT EXISTS `$tableName` (" . implode(', ', $columnDefinitions) . ")";
            if (!mysqli_query($con2, $createQuery)) {
                die("Error creating table: " . mysqli_error($con2));
            }

            // Prepare the insert statement
            $placeholders = array_fill(0, $totalColumns, '?');
            $insertQuery = "INSERT INTO `$tableName` (" . implode(', ', array_map(function ($col) {
                return "`$col`";
            }, $columns)) . ") VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = mysqli_prepare($con2, $insertQuery);

            if ($stmt === FALSE) {
                die("Error preparing insert statement: " . mysqli_error($con2));
            }

            // Bind parameters dynamically with references
            $bindTypes = str_repeat('s', $totalColumns);  // All columns are VARCHAR
            $bindParams = array_merge([$bindTypes], array_fill(0, $totalColumns, null));

            // Create array of references for bind_param
            $refs = [];
            foreach ($bindParams as $key => $value) {
                $refs[$key] = &$bindParams[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $refs);

            // Insert the data
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                foreach ($data as $index => $value) {
                    $bindParams[$index + 1] = $value;
                }
                call_user_func_array([$stmt, 'bind_param'], $refs);

                if (!mysqli_stmt_execute($stmt)) {
                    die("Error inserting data: " . mysqli_error($con2));
                }
            }

            mysqli_stmt_close($stmt);

            // Update date column to proper DATETIME format
            $lastColumn = end($columns);
            $updateDateQuery = "UPDATE `$tableName` SET `$lastColumn` = STR_TO_DATE(`$lastColumn`, '%m/%d/%Y %H:%i')";
            if (!mysqli_query($con2, $updateDateQuery)) {
                die("Error updating date column: " . mysqli_error($con2));
            }

            // Alter the last column to be DATETIME
            $alterDateColumnQuery = "ALTER TABLE `$tableName` MODIFY `$lastColumn` DATETIME";
            if (!mysqli_query($con2, $alterDateColumnQuery)) {
                die("Error altering date column: " . mysqli_error($con2));
            }

            mysqli_close($con2);

            // Success message and redirect
            echo "<script>alert('Import successful!'); window.location.href = document.referrer;</script>";
            exit;
        } else {
            echo "Error reading the CSV file.";
        }

        fclose($handle);
    } else {
        echo "Error opening the CSV file.";
    }
} else {
    echo "Invalid request.";
}
