<?php
    $servername = "localhost";
    $user_name = "root";
    $pass = "";
    $user_database = "user_account"; //db ng uses side
    $admin_database = "admin_account"; //db ng admin side
    
    //connection for user db
    $conn = mysqli_connect($servername, $user_name, $pass, $user_database);
    if (!$conn ) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //connection for admin acc
    $conn2 = mysqli_connect($servername, $user_name, $pass, $admin_database);
    //check if conneccted
    if (!$conn2) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ?>