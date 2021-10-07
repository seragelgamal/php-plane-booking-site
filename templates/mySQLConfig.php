<?php

// establish mySQL connection
$pdo = new PDO("mysql:host=localhost;dbname=flightbooking", 'flightbooking', 'bookflights');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
