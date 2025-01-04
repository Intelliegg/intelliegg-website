<?php
require_once '../classes/database.php';

// Create a new instance of the Database class
$db = new Database();
$conn = $db->connect();

// Check connection
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

// Fetch unprocessed images
$sql = "SELECT id, image_data, detection_Date FROM images WHERE id NOT IN (SELECT DISTINCT image_id FROM fertility_status)";
$stmt = $conn->prepare($sql);
$stmt->execute();

$unprocessed_images = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Convert BLOB data to base64 for JSON encoding
    $row['image_data'] = base64_encode($row['image_data']);
    $unprocessed_images[] = $row;
}

// Close connection
$conn = null;

// Return unprocessed images as JSON with success message
header('Content-Type: application/json');
echo json_encode(["success" => true, "message" => "Unprocessed images fetched successfully", "data" => $unprocessed_images]);
?>