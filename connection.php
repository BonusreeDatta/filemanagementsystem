<?php
    $severname = "localhost";
    $username ="root";
    $password = "Admin@123";
    $db_name = "pdfannotation";
    $uploadDir = 'uploads/';
    $conn =new mysqli($severname, $username, $password, $db_name, 3307);
    if($conn->connect_error)
    {
        die("Connection Failed". $conn->connect_error);
    }
    echo " ";
?>