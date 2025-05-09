<?php 

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$api_key = escape_data($_POST["api_key"]);

	if($api_key == PROJECT_API_KEY) {
		if (isset($_POST['temperature']) && isset($_POST['humidity'])) {
			$temperature = escape_data($_POST["temperature"]);
			$humidity = escape_data($_POST["humidity"]);
		
			$sql = "INSERT INTO incubator(incubatorNo, temperature, humidity, date) 
				VALUES('incubator1', '".$temperature."','".$humidity."','".date("Y-m-d H:i:s")."')";

			if($db->query($sql) === FALSE) {
				echo "Error: " . $sql . "<br>" . $db->error;
			}

			echo "OK. INSERT ID: ";
			echo $db->insert_id;
		} else {
			echo "Missing temperature or humidity data";
		}
	} else {
		echo "Wrong API Key";
	}
}
else {
	echo "No HTTP POST request found";
}
function escape_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
