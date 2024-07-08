<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Database 1 - ramhealth
    $host1 = "localhost";
    $user1 = "root";
    $password1 = "";
    $db_name1 = "ramhealth";

    $con1 = mysqli_connect($host1, $user1, $password1, $db_name1)
        or die("Failed to connect to ramhealth database: " . mysqli_connect_error());

    // Database 2 - ramhealth_energy
    $host2 = "localhost";
    $user2 = "root";
    $password2 = "";
    $db_name2 = "ramhealth_energy";

    $con2 = mysqli_connect($host2, $user2, $password2, $db_name2)
        or die("Failed to connect to ramhealth_energy database: " . mysqli_connect_error());

    // Now $con1 is the connection to ramhealth database
    // And $con2 is the connection to ramhealth_energy database
?>
