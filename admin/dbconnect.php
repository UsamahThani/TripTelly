<?php
    $hostname = "localhost";
    $user = "root";
    $password = "";
    $database = "triptelly";

    $conn = mysqli_connect($hostname, $user, $password, $database) OR DIE ("Connection failed!");
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>