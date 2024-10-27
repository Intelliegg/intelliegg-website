<?php
// Include your database connection file
require_once 'database.php'; // Adjust the path as necessary

// Create a new Database instance
$database = new Database();
$pdo = $database->connect(); // Establish the PDO connection

if (isset($_GET['incubatorNo'])) {
    $incubatorNo = $_GET['incubatorNo'];
    $events = []; // Initialize the $events array

    $sql = "SELECT start_date, end_date FROM calendar WHERE incubatorNo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$incubatorNo]);
    $incubation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($incubation) {
        // Return incubation event
        $events[] = [
            'title' => 'Incubating',
            'start' => $incubation['start_date'],
            'end' => $incubation['end_date'],
            'backgroundColor' => '#FFCC00', // Customize color
            'allDay' => true
        ];
    }

    echo json_encode($events);
}
?>
