<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Get set points for set curve
  $stmt = $conn->prepare("SELECT temp, hours, batch_id, is_running, UNIX_TIMESTAMP(date) FROM point INNER JOIN batch ON batch_id = id WHERE batch_id = (SELECT MAX(id) FROM batch) ORDER BY hours ASC");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    $temp[] = $row[0];
    $hours[] = $row[1];
    $batch_id = $row[2];
    $running = $row[3];
    $batchDate = $row[4];
  }

  // Adjust set points based on time passed
  $hoursPassed = intval((time() - $batchDate) / 3600);
  $j = 0;
  while ($j < count($hours) && $hoursPassed >= $hours[$j]) {  // Find out how far we have come
    $j++;
  }
  if ($j < count($hours) && $running == 1 ) { // If not finished and not aborted

    // Interpolate value between two points
    $derivative = ($temp[$j] - $temp[$j - 1]) / (floatval($hours[$j]) - floatval($hours[$j - 1]));
    $setPoint = $temp[$j - 1] + ($hoursPassed - $hours[$j - 1]) * $derivative;
    // Create a new point for the current set point
    $temp[$j-1] = $setPoint;
    $hours[$j-1] = 0;
    // Adjust rest of the points
    for ($i=$j; $i < count($hours); $i++) {
      $hours[$i] -= $hoursPassed;
    }

    // Print points
    echo ";;$batch_id;";
    for ($i = $j-1; $i < count($temp); $i++) {
      echo "$hours[$i],$temp[$i];";
    }
    echo ":";

  } else {  // Batch is finished or aborted
    echo ";;-1;";
    echo ":";
  }

?>
