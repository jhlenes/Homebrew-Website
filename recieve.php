<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Get set points for set curve
  $stmt = $conn->prepare("SELECT temp, hours FROM point WHERE batch_id = (SELECT MAX(id) FROM batch) ORDER BY hours ASC");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    $temp[] = $row[0];
    $hours[] = $row[1];
  }

  echo ";;";
  for ($i = 0; $i < count($temp); $i++) {
    echo "$hours[$i],$temp[$i];";
  }
  echo ":";

?>
