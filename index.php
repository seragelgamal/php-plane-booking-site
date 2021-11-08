<?php

// require header template
require('templates/header.php');

// get all routes from database
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

// set global variable for relevant routes based on search
$searchedRoutes = [];

// form action: get available flights based on specified filters
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

  if ($_POST['minimumPrice'] != '') {
    // if min price is set
    for ($i = 0; $i < sizeof($searchedRoutes); $i++) {
      if ($searchedRoutes[$i]->price < $_POST['minimumPrice']) {
        unset($searchedRoutes[$i]);
        $searchedRoutes = array_values($searchedRoutes);
        $i--;
      }
    }
  }

  // if max price is set
  if ($_POST['maximumPrice'] != '') {
    for ($i = 0; $i < sizeof($searchedRoutes); $i++) {
      if ($searchedRoutes[$i]->price > $_POST['maximumPrice']) {
        unset($searchedRoutes[$i]);
        $searchedRoutes = array_values($searchedRoutes);
        $i--;
      }
    }
  }
} else {
  // if no filters are set
  foreach ($routes as $route) {
    array_push($searchedRoutes, $route);
  }
}

?>

<!-- search section -->
<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
  <h2>Filter by origin and/or destination:</h2>
  <p>
    <select name="origin">
      <option value="">All origins</option>
      <?php $origins = [];
      foreach ($routes as $route) {
        if (!in_array($route->origin, $origins)) {
          array_push($origins, $route->origin); ?>
          <option <?php if (isset($_POST['origin']) && $_POST['origin'] == $route->origin) { ?> selected <?php } ?>><?= $route->origin ?></option>
      <?php }
      } ?>
    </select>
    to
    <select name="destination">
      <option value="">All destinations</option>
      <?php $destinations = [];
      foreach ($routes as $route) {
        if (!in_array($route->destination, $destinations)) {
          array_push($destinations, $route->destination); ?>
          <option <?php if (isset($_POST['destination']) && $_POST['destination'] == $route->destination) { ?> selected <?php } ?>><?= $route->destination ?></option>
      <?php }
      } ?>
    </select>
  </p>
  <h2>Filter by price:</h2>
  <p>
    $<input type="number" name="minimumPrice" min="0" placeholder="minimum price" <?php if (isset($_POST['minimumPrice'])) { ?> value="<?= $_POST['minimumPrice'] ?>" <?php } ?>>
    to
    $<input type="number" name="maximumPrice" min="0" placeholder="maximum price" <?php if (isset($_POST['maximumPrice'])) { ?> value="<?= $_POST['maximumPrice'] ?>" <?php } ?>>
  </p>
  <p>
    <input type="submit" value="Search" name="submit">
  </p>
</form>

<hr>

<!-- display section -->
<?php if (sizeof($searchedRoutes) > 0) { ?>
  <h2>Flights:</h2>
  <p>(<?= sizeof($searchedRoutes) ?> flights out of <?= sizeof($routes) ?> total)</p>
  <?php foreach ($searchedRoutes as $route) { ?>
    <a href="details.php?routeId=<?= $route->id ?>">
      <div class="flight">
        <h3 class="routeDescription"><?= $route->origin ?> to <?= $route->destination ?></h3>
        <p class="routePrice">$<?= $route->price ?></p>
      </div>
    </a>
  <?php }
} else if ((!($_POST['minimumPrice'] <= $_POST['maximumPrice'])) && $_POST['minimumPrice'] != '' && $_POST['maximumPrice'] != '') { ?>
  <h2>Please ensure maximum price is greater than minimum price.</h2>
<?php } else if (($_POST['origin'] == $_POST['destination']) && ($_POST['origin'] != '')) { ?>
  <h2>Please select different cities for the origin and destination.</h2>
<?php } else { ?>
  <h2>No flights found based on the selected filters. Try changing the specified origin, destination, or price range</h2>
<?php }
require('templates/footer.php'); ?>