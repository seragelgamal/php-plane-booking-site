<?php
// require header template
require('templates/header.php');

// get available routes
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

// get available times
$stmt = $pdo->query('SELECT * FROM routeschedules');
$routeSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($routeSchedules);

?>

<!-- display available flights -->
<h2>Available Flights:</h2>
<?php foreach ($routes as $route) { ?>
  <a href="details.php?routeId=<?= $route->id ?>">
    <div class="flight">
      <h3><?= $route->origin ?> - <?= $route->destination ?></h3>
      <p>$<?= $route->price ?></p>
    </div>
  </a>

<?php } ?>
</body>

</html>