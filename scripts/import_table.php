<?php
    require_once('../scripts/database_connect.php');

    // Get the table name from the form submission
    $tableName = $_POST['table_name'];

    // Disable foreign key checks
    mysqli_query($con, "SET foreign_key_checks = 0");

    // Get the column names from the database table
    $columnsQuery = "SHOW COLUMNS FROM $tableName";
    $columnsResult = mysqli_query($con, $columnsQuery);
    if (!$columnsResult) {
        die("Error fetching columns: " . mysqli_error($con));
    }
    $tableColumns = [];
    while ($row = mysqli_fetch_assoc($columnsResult)) {
        $tableColumns[] = strtolower($row['Field']);
    }

    // Import the CSV file into the table
    $importSuccessful = false;
    $errorMessage = "";

    if ($_FILES['csv_file']['size'] > 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        if ($handle === FALSE) {
            $errorMessage = "Failed to open the CSV file.";
        } else {
            // Check if the column headers match the database table columns
            $csvColumns = fgetcsv($handle, 1000, ",");
            if ($csvColumns === FALSE) {
                $errorMessage = "Failed to read the CSV file headers.";
            } else {
                $matchedColumns = [];
                foreach ($csvColumns as $column) {
                    // Trim whitespace and convert to lowercase for comparison
                    $trimmedColumn = strtolower(trim($column));
                    if (in_array($trimmedColumn, $tableColumns)) {
                        $matchedColumns[] = $trimmedColumn;
                    }
                }

                if (empty($matchedColumns)) {
                    $errorMessage = "CSV headers do not match the database table columns.";
                } else {
                    $importSuccessful = true;
                    $numColumns = count($matchedColumns);
                    $placeholders = implode(',', array_fill(0, $numColumns, '?'));
                    $insertQuery = "INSERT IGNORE INTO $tableName (" . implode(',', $matchedColumns) . ") VALUES ($placeholders)";
                    $stmt = mysqli_prepare($con, $insertQuery);

                    if (!$stmt) {
                        $importSuccessful = false;
                        $errorMessage = "Failed to prepare the SQL statement for table $tableName: " . mysqli_error($con);
                    } else {
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            if (count($data) != $numColumns) {
                                // Skip rows with a mismatched number of columns
                                continue;
                            }
                            mysqli_stmt_bind_param($stmt, str_repeat('s', $numColumns), ...$data);
                            if (!mysqli_stmt_execute($stmt)) {
                                $importSuccessful = false;
                                $errorMessage = "Failed to execute the SQL statement for table $tableName: " . mysqli_stmt_error($stmt);
                                break;
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }
            fclose($handle);
        }
    } else {
        $errorMessage = "No file uploaded or the file is empty.";
    }

    // Enable foreign key checks
    mysqli_query($con, "SET foreign_key_checks = 1");

    mysqli_close($con);

    $referrer = $_SERVER['HTTP_REFERER'];

    if ($importSuccessful) {
        echo "<script>alert('Import successful'); window.location.href = '$referrer';</script>";
    } else {
        echo "<script>alert('Import unsuccessful: $errorMessage'); window.location.href = '$referrer';</script>";
    }
?>
