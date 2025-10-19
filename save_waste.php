<?php
session_start();
include 'db.php';
if(!isset($_SESSION['userid'])) exit();

$data = json_decode(file_get_contents("php://input"), true);
$userid = $_SESSION['userid'];
$plastic = $data['plastic'] ?? 0;
$organic = $data['organic'] ?? 0;
$metal = $data['metal'] ?? 0;
$ewaste = $data['ewaste'] ?? 0;

$stmt = $conn->prepare("INSERT INTO user_waste_log (userid, date, plastic_kg, organic_kg, metal_kg, e_waste_kg) VALUES (?, CURDATE(), ?, ?, ?, ?)");
$stmt->bind_param("iddd", $userid, $plastic, $organic, $metal, $ewaste);

if($stmt->execute()){
    echo "Waste data saved successfully!";
} else {
    echo "Error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
