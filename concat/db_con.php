<?php
$server   = "localhost";
$dbmane   = "sabbirhossen_inventory";
$username = "root";
$password = "";

$myCon = "mysql:host=$server;dbname=$dbmane";
$conn = new PDO ($myCon,$username,$password);
?>