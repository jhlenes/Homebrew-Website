<?php

if (isset($_GET["temp"])) {
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

    echo "OK";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}else{
  echo "Argument \"temp\" not recieved.";
}

?>
