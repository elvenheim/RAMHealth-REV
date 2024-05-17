<?php
    require_once('../scripts/database_connect.php');

    // Get the table name from the form submission
    $tableName = $_POST['table_name'];

    // Get the column names from the database table
    $columnsQuery = "SHOW COLUMNS FROM $tableName";
    $columnsResult = mysqli_query($con, $columnsQuery);
    $tableColumns = [];
    while ($row = mysqli_fetch_assoc($columnsResult)) {
        $tableColumns[] = $row['Field'];
    }

    // Import the CSV file into the table
    $importSuccessful = false;
    if ($_FILES['csv_file']['size'] > 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        $header = true;

        // Check if the column headers match the database table columns
        $csvColumns = fgetcsv($handle, 1000, ",");
        $adjustedData = [];

        if ($csvColumns !== false) {
            foreach ($csvColumns as $column) {
                // Trim whitespace and convert to lowercase for comparison
                $trimmedColumn = strtolower(trim($column));
                if (in_array($trimmedColumn, $tableColumns)) {
                    $adjustedData[] = $column;
                }
            }
        }

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if ($header) {
                $header = false;
                continue;
            }

            // Prepare and execute the INSERT IGNORE query dynamically
            $numColumns = count($adjustedData);
            $placeholders = array_fill(0, $numColumns, '?');
            $insertQuery = "INSERT IGNORE INTO $tableName (" . implode(',', $adjustedData) . ") VALUES (" . implode(',', $placeholders) . ")";
            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($stmt, str_repeat('s', $numColumns), ...$data);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        fclose($handle);
        $importSuccessful = true;
    }

    mysqli_close($con);

    $referrer = $_SERVER['HTTP_REFERER'];

    if ($importSuccessful) {
        echo "<script>alert('Import successful'); window.location.href = '$referrer';</script>";
        exit;
    } else {
        echo "<script>alert('Import unsuccessful'); window.location.href = '$referrer';</script>";
        exit;
    }
?>