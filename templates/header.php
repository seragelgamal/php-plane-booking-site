<?php

// establish mySQL connection via pdo
$pdo = new PDO("mysql:host=localhost;dbname=flightbooking", 'flightbooking', 'bookflights');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html>

<!-- page header -->

<head>
  <title>Flight Booking</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <a href="index.php">
    <h1>Flight Booking</h1>
  </a>
  <hr>