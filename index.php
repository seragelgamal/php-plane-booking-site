<?php

// require header template
require('templates/header.php');

// get available flights for selected origin and destination
if (isset($_POST['submit'])) {
  if (isset($_POST['origin']) != '' && isset($_POST['destination']) != '') {

  }
}

$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

?>

<!-- search section -->
<h2>Search flights by origin and destination:</h2>
<form action="index.php" method="post">
  <select name="origin">
    <option value="">All origins</option>
    <?php foreach ($routes as $route) { ?>
      <option value="<?= $route->origin ?>"><?= $route->origin ?></option>
    <?php } ?>
  </select>
  to
  <select name="destination">
    <option value="">All destinations</option>
    <?php foreach ($routes as $route) { ?>
      <option value="<?= $route->destination ?>"><?= $route->destination ?></option>
    <?php } ?>
  </select>
  <input type="submit" value="Search" name="submit">
</form>

<!-- display available flights -->
<h2>Available Flights:</h2>
<?php foreach ($routes as $route) { ?>
  <a href="details.php?routeId=<?= $route->id ?>&origin=<?= $route->origin ?>&destination=<?= $route->destination ?>">
    <div class="flight">
      <h3 class="routeDescription"><?= $route->origin ?> to <?= $route->destination ?></h3>
      <p>$<?= $route->price ?></p>
    </div>
  </a>
<?php }
require('templates/footer.php');
?>