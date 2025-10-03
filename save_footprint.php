<?php
session_start();
if(!isset($_SESSION['username'])){
    echo "User not logged in";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$footprint = $data['footprint'] ?? 0;

$username = $_SESSION['username'];

// Connect to DB
$conn = new mysqli("localhost","root","","planet365");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$stmt = $conn->prepare("INSERT INTO user_footprints (username, footprint, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("sd", $username, $footprint);
if($stmt->execute()){
    echo "Footprint saved successfully!";
} else {
    echo "Error saving footprint.";
}
$stmt->close();
$conn->close();
?>
