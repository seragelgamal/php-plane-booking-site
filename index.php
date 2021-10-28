<?php

// require header template
require('templates/header.php');

// get available routes
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

?>

<!-- display available flights -->
<h2>Available Flights:</h2>
<?php foreach ($routes as $route) { ?>
  <a href="details.php?routeId=<?= $route->id ?>&origin=<?= $route->origin ?>&destination=<?= $route->destination ?>">
    <div class="flight">
      <h3 class="routeDescription"><?= $route->origin ?> - <?= $route->destination ?></h3>
      <p>$<?= $route->price ?></p>
    </div>
  </a>
<?php }
require('templates/footer.php');
?>