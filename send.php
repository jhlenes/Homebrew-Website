<?php

  try {
      $temp = $_GET["temp"];

      $servername = "localhost";
      $username = "root";
      $password = "";
      $database = "homebrew";

      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $conn->prepare("INSERT INTO `temperatur` (`temp`, `tid`) VALUES ('" . $temp . "', CURRENT_TIMESTAMP);");
      $stmt->execute();

      echo "Succeded by sending: temp=" . $_GET["temp"];
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

?>
