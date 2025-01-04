<?php
require_once '../classes/database.php';

// Initialize the database
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from the request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (isset($data['detection_date'])) {
        // Get the start and end of the current day
        $startDate = $data['detection_date'] . ' 00:00:00';
        $endDate = $data['detection_date'] . ' 23:59:59';

        // Check if a picture for the current date already exists in the database
        $stmt = $conn->prepare("SELECT * FROM images WHERE detection_Date BETWEEN :start_date AND :end_date");
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        $existingImage = $stmt->fetch();

        if ($existingImage) {
            // An entry for this date already exists
            echo json_encode(['exists' => true]);
        } else {
            // No entry for this date exists
            echo json_encode(['exists' => false]);
        }
    } else {
        // Invalid data
        echo json_encode(['error' => 'Invalid data: detection_date missing']);
    }
} else {
    // Invalid request method
    echo json_encode(['error' => 'Invalid request method']);
}
?>