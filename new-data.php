<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (isset($_GET["time"])) {
		$currentTime = $_GET["time"];

    // Get measurements
  	$stmt = $conn->prepare("SELECT temp, UNIX_TIMESTAMP(time) FROM measurement WHERE batch_id = (SELECT MAX(id) FROM batch) AND UNIX_TIMESTAMP(time) > :tid");
    $stmt->bindParam(':tid', $currentTime);
  	$stmt->execute();
  	$data = array();
  	while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
  		$temp = $row[0];
  		$time = $row[1];
  		$time *= 1000; // convert from Unix timestamp to JavaScript time
      echo "$time,$temp;";
  	}
  }
?>
