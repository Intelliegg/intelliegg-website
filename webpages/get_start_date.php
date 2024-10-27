<?php
// get_start_date.php
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

$sql = "SELECT start_date, end_date FROM calendar";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo json_encode($row);
    }
} else {
    echo json_encode(array('start_date' => null, 'end_date' => null));
}
$conn->close();
?>