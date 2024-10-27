<?php
include '../classes/database.php';

$db = new Database();
$conn = $db->connect();

$query = "SELECT incubatorNo FROM incubator";
$stmt = $conn->prepare($query);
$stmt->execute();
$incubators = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(value: $incubators);
?>
