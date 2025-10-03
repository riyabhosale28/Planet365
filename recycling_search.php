<?php
header('Content-Type: application/json');

$location = $_GET['location'] ?? 'New York';

// Use Nominatim to geocode
$opts = [
    "http" => [
        "header" => "User-Agent: Planet365/1.0\r\n"
    ]
];
$context = stream_context_create($opts);

$url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($location);

$data = file_get_contents($url, false, $context);

if ($data === false) {
    echo json_encode([]);
} else {
    echo $data;
}
?>
