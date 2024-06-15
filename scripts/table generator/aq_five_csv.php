<?php 
    // Generate randomized data for indoor_temp table
    $csvData = "room_id,co2_level,co_level,rel_humid,ozone_level,pm_one,pm_two_five,pm_ten,param_temp,param_tvoc,heat_index,date_acquired" . PHP_EOL;

    $startDate = strtotime('2024-04-07 15:05:00'); // Start at 15:05:00
    $endDate = strtotime('2024-05-12 15:10:00');   // End at 15:10:00

    $startRoom = 801;
    $endRoom = 814;

    // Interval between timestamps (in seconds), set to 5 minutes (300 seconds)
    $interval = 300;

    // Generate data for 1000 records
    for ($i = 0; $i < 1000; $i++) {
        $room_id = rand($startRoom, $endRoom);
        $co2_level = rand(300, 600);
        $co_level = rand(14, 27);
        $rel_humid = rand(30, 80);
        $ozone_level = rand(0, 25);
        $pm_one = rand(5, 43);
        $pm_two_five = rand(5, 65);
        $pm_ten = rand(5, 53);
        $param_temp = rand(18, 35);
        $param_tvoc = rand(0, 30);
        $heat_index = rand(20, 35);
        
        // Adjust the date acquired
        $date_acquired = date('Y-m-d H:i:s', $startDate + ($i * $interval));

        // Append data row to CSV string
        $csvData .= "$room_id,$co2_level,$co_level,$rel_humid,$ozone_level,$pm_one,$pm_two_five,$pm_ten,$param_temp,$param_tvoc,$heat_index,$date_acquired" . PHP_EOL;
    }

    // Save the randomized data to a CSV file
    $fileName = "aq_param_five.csv";
    $file = fopen($fileName, 'w');
    fwrite($file, $csvData);
    fclose($file);

    // Provide download link to the generated CSV file
    echo "Randomized Air Quality 5-minute interval data has been exported to <a href='$fileName' download>CSV file</a>.";
?>
