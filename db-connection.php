<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'eventease_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Karachi');

// NO session_start() HERE!
?>