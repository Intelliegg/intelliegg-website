<?php
require 'database.php';

class FetchData {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->connect();
    }

    public function getData() {
        $sql = "SELECT humidity, temperature FROM incubator ORDER BY id DESC LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return json_encode([
                'humidity' => $result['humidity'],
                'temperature' => $result['temperature']
            ]);
        } else {
            return json_encode([
                'humidity' => 0,
                'temperature' => 0
            ]);
        }
    }
}

$fetchData = new FetchData();
echo $fetchData->getData();
?>
