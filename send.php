<?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  if (isset($_GET["temp"])) {
    try {
      $temp = $_GET["temp"];

      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("INSERT INTO measurement (temp, time, batch_id) VALUES (:temp, CURRENT_TIMESTAMP, (SELECT MAX(id) FROM batch))");
      $stmt->bindParam(':temp', $temp);
      $stmt->execute();

      echo "OK";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  } else if (isset($_GET["status"])) {
    try {
      $status = $_GET["status"];

      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("SELECT MAX(id) FROM batch");
      $stmt->execute();
      $id = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)[0];

      $stmt = $conn->prepare("UPDATE `batch` SET `is_running` = :status WHERE `id` = :id");
      $stmt->bindParam(':status', $status);
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      echo "OK";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  } else {
      echo "Argument \"temp\" or \"status\" not recieved.";
  }
?>
