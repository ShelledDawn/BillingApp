<?php
$conn = new mysqli("localhost", "root", "", "billing");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>