<?php 

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "db_pajak";
    

    $conn = mysqli_connect($hostname, $username, $password, $database);

    if ($conn-> connect_error){
        echo "error connecting to database";
        die("error");
    }

?>
