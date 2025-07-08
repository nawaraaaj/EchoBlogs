<?php

$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "echoblog";

$conn = new mysqli ($servername,$username,$password,$dbname);

if($conn->connect_error){
    die("Connection error" . $e->connect_error);
}
