<?php
session_start();
include 'db.php';
if(!isset($_SESSION['userid'])) exit();
$userid = $_SESSION['userid'];

// Fetch last 7 days energy data
$energy = $conn->query("SELECT electricity_kwh, water_liters, gas_units FROM user_energy_usage WHERE userid=$userid ORDER BY date DESC LIMIT 7");

$totalElectricity=0; $totalWater=0; $totalGas=0; $count=0;
while($row = $energy->fetch_assoc()){
    $totalElectricity += $row['electricity_kwh'];
    $totalWater += $row['water_liters'];
    $totalGas += $row['gas_units'];
    $count++;
}

if($count>0){
    $avgElectricity = $totalElectricity/$count;
    $avgWater = $totalWater/$count;
    $avgGas = $totalGas/$count;

    $tips = [];
    if($avgElectricity>100) $tips[]="Consider reducing electricity usage. Switch to LED lights.";
    if($avgWater>200) $tips[]="Reduce water wastage. Fix leaking taps.";
    if($avgGas>50) $tips[]="Check your gas usage. Ensure appliances are efficient.";

    echo json_encode(['tips'=>$tips,'avgElectricity'=>$avgElectricity,'avgWater'=>$avgWater,'avgGas'=>$avgGas]);
} else {
    echo json_encode(['tips'=>["No data yet. Start logging your usage!"]]);
}

$conn->close();
?>
