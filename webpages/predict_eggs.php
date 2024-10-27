<?php
require_once '../classes/database.php';

// Initialize the database
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from the request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (isset($data['fertility_data']) && isset($data['image'])) {
        // Decode the base64 image
        $imageData = base64_decode($data['image']);

        try {
            // Insert the image into the database with the capture date
            $stmt = $conn->prepare("INSERT INTO images (image_data) VALUES (:image_data)");
            $stmt->bindParam(':image_data', $imageData, PDO::PARAM_LOB);
            $stmt->execute();
            $image_id = $conn->lastInsertId();

            // Insert fertility data into the database
            foreach ($data['fertility_data'] as $eggData) {
                $row = $eggData['row'];
                $col = $eggData['col'];
                $status = $eggData['status'];
                $confidence = $eggData['confidence'];

                $stmt = $conn->prepare("INSERT INTO fertility_status (image_id, row, col, status, confidence)
                                        VALUES (:image_id, :row, :col, :status, :confidence)");
                $stmt->bindParam(':image_id', $image_id);
                $stmt->bindParam(':row', $row);
                $stmt->bindParam(':col', $col);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':confidence', $confidence);
                $stmt->execute();
            }

            echo "Data saved successfully.";
        } catch (PDOException $e) {
            echo "Error saving data: " . $e->getMessage();
        }
    } else {
        echo "Invalid data.";
    }
} else {
    echo "Invalid request method.";
}
?>