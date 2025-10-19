<?php
session_start();
if(!isset($_SESSION['username'])){
    echo "User not logged in";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$username = $_SESSION['username'];
$travel = $data['travel'] ?? 0;
$electricity = $data['electricity'] ?? 0;
$water = $data['water'] ?? 0;
$diet = $data['diet'] ?? 'veg';
$footprint = $data['footprint'] ?? 0;

// Connect to DB
$conn = new mysqli("localhost","root","","planet365");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$stmt = $conn->prepare("INSERT INTO user_footprints (username,travel, electricity, water, diet,  footprint) VALUES (?, ?, ?, ?, ?, ? )");
if(!$stmt){
    die("Prepare failed: ".$conn->error);
}
$stmt->bind_param("sdddsd", $username, $travel, $electricity, $water, $diet,$footprint );
if($stmt->execute()){
    echo "Footprint saved successfully!";
} else {
    echo "Error saving footprint.";
}
$stmt->close();
$conn->close();
?>


