<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Get set points for set curve
  $stmt = $conn->prepare("SELECT temp, hours, batch_id, is_running FROM point INNER JOIN batch ON batch_id = id WHERE batch_id = (SELECT MAX(id) FROM batch) ORDER BY hours ASC");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    $temp[] = $row[0];
    $hours[] = $row[1];
    $batch_id = $row[2];
    $isRunning = $row[3];
  }

  if ($isRunning == 1) {
    echo ";;$batch_id;";
    for ($i = 0; $i < count($temp); $i++) {
      echo "$hours[$i],$temp[$i];";
    }
    echo ":";
  } else {
    echo ";;-1;";
    echo ":";
  }
?>
