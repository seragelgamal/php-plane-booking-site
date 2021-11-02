<?php

// require header template
require('templates/header.php');

// get all routes from database
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

// set global variable for relevant routes based on search
$searchedRoutes = [];

// get available flights for selected origin and destination
if (isset($_POST['submit'])) {
  if ($_POST['origin'] != '' && $_POST['destination'] != '') {
    // if both origin and destination are set
    foreach ($routes as $route) {
      if ($route->origin == $_POST['origin'] && $route->destination == $_POST['destination']) {
        array_push($searchedRoutes, $route);
      }
    }
  } else if ($_POST['origin'] != '') {
    // if only origin is set
    foreach ($routes as $route) {
      if ($route->origin == $_POST['origin']) {
        array_push($searchedRoutes, $route);
      }
    }
  } else if ($_POST['destination'] != '') {
    // if only destination is set
    foreach ($routes as $route) {
      if ($route->destination == $_POST['destination']) {
        array_push($searchedRoutes, $route);
      }
    }
  } else {
    // neither are set
    foreach ($routes as $route) {
      array_push($searchedRoutes, $route);
    }
  }
} else {
  foreach ($routes as $route) {
    array_push($searchedRoutes, $route);
  }
}

?>

<!-- search section -->
<h2>Search flights by origin and destination:</h2>
<p>
<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
  <select name="origin">
    <option value="">All origins</option>
    <?php foreach ($routes as $route) { ?>
      <option <?php if (isset($_POST['origin']) && $_POST['origin'] == $route->origin) { ?> selected <?php } ?>><?= $route->origin ?></option>
    <?php } ?>
  </select>
  to
  <select name="destination">
    <option value="">All destinations</option>
    <?php foreach ($routes as $route) { ?>
      <option <?php if (isset($_POST['destination']) && $_POST['destination'] == $route->destination) { ?> selected <?php } ?>><?= $route->destination ?></option>
    <?php } ?>
  </select>
  <input type="submit" value="Search" name="submit">
</form>
</p>


<hr>

<!-- display available flights -->
<?php if (sizeof($searchedRoutes) > 0) { ?>
  <h2>Available Flights:</h2>
  <?php foreach ($searchedRoutes as $route) { ?>
    <a href="details.php?routeId=<?= $route->id ?>">
      <div class="flight">
        <h3 class="routeDescription"><?= $route->origin ?> to <?= $route->destination ?></h3>
        <p>$<?= $route->price ?></p>
      </div>
    </a>
  <?php }
} else { ?>
  <h2>No available flights for the selected origin and destination. Try changing your search</h2>
<?php }
require('templates/footer.php'); ?>