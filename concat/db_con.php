<?php
$server   = "localhost";
$dbmane   = "step_inventory";
$username = "root";
$password = "";

$myCon = "mysql:host=$server;dbname=$dbmane";
$conn = new PDO ($myCon,$username,$password);
?>