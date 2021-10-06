<?php

require('templates/header.php');

$stmt = $pdo->query('SELECT * FROM flights');
$flights = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html>

<head>
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <a href="index.php">
    <h1>Title</h1>
  </a>
  <hr>
  <h2>Available Flights:</h2>
  <?php foreach ($flights as $flight) { ?>
    <div>
      <h3><?php echo ($flight->origin); ?> - <?php echo ($flight->destination); ?></h3>
      <h4></h4>
    </div>
  <?php } ?>
</body>

</html>