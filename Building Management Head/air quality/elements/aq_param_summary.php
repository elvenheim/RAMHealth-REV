<?php
    require_once('dashboard_connect.php');

    // Check if room_id is received via POST
    if (isset($_POST['table_interval']) && isset($_POST['selected_room'])) {
        $tableName = $_POST['table_interval'];
        $roomId = $_POST['selected_room'];

        // Initialize query variables
        $apfQuery = "";
        $apdQuery = "";

        // Conditional queries based on table_name
        if ($tableName === 'aq_param_five') {
            // SQL Query for latest 5-minute interval
            $apfQuery = "SELECT ROUND(apf.co2_level, 2) AS co2_level, ROUND(apf.rel_humid, 2) AS rel_humid, 
                                ROUND(apf.pm_one, 2) AS pm_one, ROUND(apf.pm_two_five, 2) AS pm_two_five, 
                                ROUND(apf.pm_ten, 2) AS pm_ten, ROUND(apf.param_temp, 2) AS param_temp, 
                                ROUND(apf.heat_index, 2) AS heat_index
                        FROM aq_param_five apf 
                        WHERE room_id = $roomId 
                        ORDER BY date_acquired DESC 
                        LIMIT 1";
        } elseif ($tableName === 'aq_param_daily') {
            // SQL Query for latest daily interval
            $apdQuery = "SELECT ROUND(AVG(apd.pm_one), 2) AS avg_pm_one, 
                                ROUND(AVG(apd.pm_two_five), 2) AS avg_pm_two_five, 
                                ROUND(AVG(apd.pm_ten), 2) AS avg_pm_ten, 
                                ROUND(AVG(apd.co2_level), 2) AS avg_co2_level, 
                                ROUND(AVG(apd.rel_humid), 2) AS avg_rel_humid, 
                                ROUND(AVG(apd.param_temp), 2) AS avg_param_temp, 
                                ROUND(AVG(apd.heat_index), 2) AS avg_heat_index
                        FROM aq_param_daily apd 
                        WHERE room_id = $roomId
                        GROUP BY date_acquired 
                        ORDER BY date_acquired DESC 
                        LIMIT 1";
        } elseif ($tableName === ''){
            // Print choose interval
            echo "Particulate Matter 1: choose a time interval... <br>";
            echo "Particulate Matter 2.5: choose a time interval... <br>";
            echo "Particulate Matter 10: choose a time interval... <br>";
            echo "Relative Humidity: choose a time interval... <br>";
            echo "Temperature: choose a time interval... <br>";
            echo "Heat Index: choose a time interval... <br>";
            echo "CO2 Level: choose a time interval... <br>";
        }

        // Execute queries if they are set
        if (!empty($apfQuery)) {
            $apfResult = mysqli_query($con, $apfQuery);
            if ($apfResult) {
                // Check if rows are found
                if (mysqli_num_rows($apfResult) > 0) {
                    $row = mysqli_fetch_assoc($apfResult);
                    echo "Particulate Matter 1: {$row['pm_one']} ug/m3<br>";
                    echo "Particulate Matter 2.5: {$row['pm_two_five']} ug/m3<br>";
                    echo "Particulate Matter 10: {$row['pm_ten']} ug/m3<br>";
                    echo "Relative Humidity: {$row['rel_humid']}%<br>";
                    echo "Temperature: {$row['param_temp']} C<br>";
                    echo "Heat Index: {$row['heat_index']} C<br>";
                    echo "CO2 Level: {$row['co2_level']} ppm<br>";
                } else {
                    // Print no data found
                    echo "Particulate Matter 1: no data found... <br>";
                    echo "Particulate Matter 2.5: no data found... <br>";
                    echo "Particulate Matter 10: no data found... <br>";
                    echo "Relative Humidity: no data found... <br>";
                    echo "Temperature: no data found... <br>";
                    echo "Heat Index: no data found... <br>";
                    echo "CO2 Level: no data found... <br>";
                }
            } else {
                echo "Error executing aq_param_five query: " . mysqli_error($con);
            }
        }

        if (!empty($apdQuery)) {
            $apdResult = mysqli_query($con, $apdQuery);
            if ($apdResult) {
                // Check if rows are found
                if (mysqli_num_rows($apdResult) > 0) {
                    $row = mysqli_fetch_assoc($apdResult);
                    echo "Particulate Matter 1: {$row['avg_pm_one']} ug/m3<br>";
                    echo "Particulate Matter 2.5: {$row['avg_pm_two_five']} ug/m3<br>";
                    echo "Particulate Matter 10: {$row['avg_pm_ten']} ug/m3<br>";
                    echo "Relative Humidity: {$row['avg_rel_humid']}%<br>";
                    echo "Temperature: {$row['avg_param_temp']} C<br>";
                    echo "Heat Index: {$row['avg_heat_index']} C<br>";
                    echo "CO2 Level: {$row['avg_co2_level']} ppm<br>";
                } else {
                    // Print no data found
                    echo "Particulate Matter 1: no data found... <br>";
                    echo "Particulate Matter 2.5: no data found... <br>";
                    echo "Particulate Matter 10: no data found... <br>";
                    echo "Relative Humidity: no data found... <br>";
                    echo "Temperature: no data found... <br>";
                    echo "Heat Index: no data found... <br>";
                    echo "CO2 Level: no data found... <br>";
                }
            } else {
                echo "Error executing aq_param_daily query: " . mysqli_error($con);
            }
        }
    } else {
        echo "Particulate Matter 1: choose a facility... <br>";
        echo "Particulate Matter 2.5: choose a facility... <br>";
        echo "Particulate Matter 10: choose a facility... <br>";
        echo "Relative Humidity: choose a facility... <br>";
        echo "Temperature: choose a facility... <br>";
        echo "Heat Index: choose a facility... <br>";
        echo "CO2 Level: choose a facility... <br>";
    }

    mysqli_close($con);
?>
