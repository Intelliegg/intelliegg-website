<?php
require 'config.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM tbl_temperature ORDER BY id DESC LIMIT 1";
$result = $db->query($sql);

if (!$result) {
    echo json_encode(array("error" => "Database query error"));
    exit();
}

$row = $result->fetch_assoc();
if ($row) {
    $data = array(
        "temperature" => $row['temperature'],
        "humidity" => $row['humidity']
    );
    echo json_encode($data);
} else {
    echo json_encode(array("error" => "No data found"));
}
?>
