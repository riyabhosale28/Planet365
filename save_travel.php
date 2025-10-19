<?php
session_start();
include 'db.php';
if(!isset($_SESSION['userid'])) exit();

$data = json_decode(file_get_contents("php://input"), true);
$userid = $_SESSION['userid'];
$mode = $data['mode'] ?? 'car';
$distance = $data['distance'] ?? 0;

// CO2 calculation (simplified)
$co2_rates = ['car'=>0.21, 'bus'=>0.1, 'bike'=>0, 'walk'=>0, 'flight'=>0.5];
$co2 = $distance * ($co2_rates[$mode] ?? 0.21);

$stmt = $conn->prepare("INSERT INTO user_travel_log (userid, date, transport_mode, distance_km, co2_kg) VALUES (?, CURDATE(), ?, ?, ?)");
$stmt->bind_param("isdd", $userid, $mode, $distance, $co2);

if($stmt->execute()){
    echo "Travel data saved successfully!";
} else {
    echo "Error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
