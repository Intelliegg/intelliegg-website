<?php
require_once '../classes/database.php';

// Initialize the database
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from the request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (isset($data['image'])) {
        // Decode the base64 image
        $imageData = base64_decode($data['image']);

        // Get the start and end of the current day
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';

        // Check if a picture for the current date already exists in the database
        $stmt = $conn->prepare("SELECT * FROM images WHERE detection_Date BETWEEN :start_date AND :end_date");
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        $existingImage = $stmt->fetch();

        if (!$existingImage) {
            // Insert the image into the database with the capture date
            $stmt = $conn->prepare("INSERT INTO images (image_data, detection_Date) VALUES (:image_data, NOW())");
            $stmt->bindParam(':image_data', $imageData, PDO::PARAM_LOB);
            $stmt->execute();
            echo "Image saved successfully.";
        } else {
            echo "Image for this date already exists.";
        }
    } else {
        echo "Invalid data.";
    }
} else {
    echo "Invalid request method.";
}
?>