<?php
session_start();
include 'db.php';
if(!isset($_SESSION['userid'])){
    echo "User not logged in";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$userid = $_SESSION['userid'];
$electricity = $data['electricity'] ?? 0;
$water = $data['water'] ?? 0;
$gas = $data['gas'] ?? 0;

$stmt = $conn->prepare("INSERT INTO user_energy_usage (userid, date, electricity_kwh, water_liters, gas_units) VALUES (?, CURDATE(), ?, ?, ?)");
$stmt->bind_param("iddd", $userid, $electricity, $water, $gas);

if($stmt->execute()){
    echo "Energy data saved successfully!";
} else {
    echo "Error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
