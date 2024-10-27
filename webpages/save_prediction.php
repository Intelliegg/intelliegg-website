<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "intelliegg";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO egg_predictions (class, confidence, row, egg_column) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdii", $class, $confidence, $row, $column);

// Set parameters and execute
$class = $_POST['class'];
$confidence = $_POST['confidence'];
$row = $_POST['row'];
$column = $_POST['column'];

echo "Class: $class, Confidence: $confidence, Row: $row, Column: $column";

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>