<?php

// require header template
require('misc/header.php');

// get all routes from database
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

// set global variable for relevant routes based on search
$searchedRoutes = [];

// form action: get available flights based on specified filters and sort the results
if (isset($_POST['submit'])) {
  // origin/destination filters
  if ($_POST['origin'] != '' && $_POST['destination'] != '') {
    // if both origin and destination are set: display all the routes with the specified origin and destination
    foreach ($routes as $route) {
      if ($route->origin == $_POST['origin'] && $route->destination == $_POST['destination']) {
        array_push($searchedRoutes, $route);
      }
    }
  } else if ($_POST['origin'] != '') {
    // if only origin is set: filter out the routes that don't depart from the specified origin
    originDestinationFilter($routes, 'origin', $_POST['origin'], $searchedRoutes);
  } else if ($_POST['destination'] != '') {
    // if only destination is set: filter out the routes that don't arrive at the specified destination
    originDestinationFilter($routes, 'destination', $_POST['destination'], $searchedRoutes);
  } else {
    // if neither origin nor destination are set: displaty all the routes
    foreach ($routes as $route) {
      array_push($searchedRoutes, $route);
    }
  }

  // price filters
  // if min price is set: filter out the routes whose ticket price is more expensive than it
  if ($_POST['minimumPrice'] != '') {
    for ($i = 0; $i < sizeof($searchedRoutes); $i++) {
      if ($searchedRoutes[$i]->price < $_POST['minimumPrice']) {
        priceFilter($searchedRoutes, $i);
        $i--;
      }
    }
  }
  // if max price is set: filter out the routes whose ticket price is cheaper than it
  if ($_POST['maximumPrice'] != '') {
    for ($i = 0; $i < sizeof($searchedRoutes); $i++) {
      if ($searchedRoutes[$i]->price > $_POST['maximumPrice']) {
        priceFilter($searchedRoutes, $i);
        $i--;
      }
    }
  }

  // sorting of final filtered result
  if ($_POST['sortMode'] != 'default') {
    if ($_POST['sortMode'] == 'cheapToExpensive') {
      // if low-high price sorting is selected: sort the flights by price
      sortElementsByProperty($searchedRoutes, 'price');
    } else if ($_POST['sortMode'] == 'expensiveToCheap') {
      // if high-low price sorting is selected: sort the flights by price then reverse them
      sortElementsByProperty($searchedRoutes, 'price');
      $searchedRoutes = array_reverse($searchedRoutes);
    } else if ($_POST['sortMode'] == 'a-zOrigin') {
      // if alphabetic origin sorting is selected: sort the flights by origin name
      sortElementsByProperty($searchedRoutes, 'origin');
    } else if ($_POST['sortMode'] == 'z-aOrigin') {
      // if reverse alphabetic origin sorting is selected: sort the flights by origin name then reverse them
      sortElementsByProperty($searchedRoutes, 'origin');
      $searchedRoutes = array_reverse($searchedRoutes);
    } else if ($_POST['sortMode'] == 'a-zDestination') {
      // if alphabetic destination sorting is selected: sort the flights by destination name
      sortElementsByProperty($searchedRoutes, 'destination');
    } else if ($_POST['sortMode'] == 'z-aDestination') {
      // if alphabetic destination sorting is selected: sort the flights by destination name then reverse them
      sortElementsByProperty($searchedRoutes, 'destination');
      $searchedRoutes = array_reverse($searchedRoutes);
    }
  }
} else {
  // if no filters are set: display all routes
  foreach ($routes as $route) {
    array_push($searchedRoutes, $route);
  }
  // set sort mode to 'default' by default
  $_POST['sortMode'] = 'default';
}
?>

<!-- search section -->
<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
  <h2>Filter by origin and/or destination:</h2>
  <p>
    <?php echoOriginDestinationDropdown('origin', $routes); ?>
    to
    <?php echoOriginDestinationDropdown('destination', $routes); ?>
  </p>
  <h2>Filter by price:</h2>
  <p>
    <?php echoPriceField('minimumPrice'); ?> to <?php echoPriceField('maximumPrice'); ?>
  </p>
  <h2>Sort by:</h2>
  <p>
    <?php echoRadioInput('sortMode', 'default', 'Default'); ?>
    <?php echoRadioInput('sortMode', 'cheapToExpensive', 'Price: low-high'); ?>
    <?php echoRadioInput('sortMode', 'expensiveToCheap', 'Price: high-low'); ?>
    <?php echoRadioInput('sortMode', 'a-zOrigin', 'Alphabetically by origin: A-Z'); ?>
    <?php echoRadioInput('sortMode', 'z-aOrigin', 'Alphabetically by origin: Z-A'); ?>
    <?php echoRadioInput('sortMode', 'a-zDestination', 'Alphabetically by destination: A-Z'); ?>
    <?php echoRadioInput('sortMode', 'z-aDestination', 'Alphabetically by destination: Z-A'); ?>
  </p>
  <p>
    <input type="submit" value="Apply Filters" name="submit">
  </p>
</form>

<hr>

<!-- results section -->
<!-- if search yields at least 1 result: -->
<?php if (sizeof($searchedRoutes) > 0) { ?>
  <h2>Flights:</h2>
  <p>(showing <?= sizeof($searchedRoutes) ?> flights out of <?= sizeof($routes) ?> total)</p>
  <?php foreach ($searchedRoutes as $route) { ?>
    <a href="details.php?routeId=<?= $route->id ?>">
      <div class="flight">
        <h3 class="routeDescription"><?= $route->origin ?> to <?= $route->destination ?></h3>
        <p class="routePrice">$<?= $route->price ?></p>
      </div>
    </a>
  <?php }
  // if search doesn't yield any results:
} else if ((!($_POST['minimumPrice'] <= $_POST['maximumPrice'])) && $_POST['minimumPrice'] != '' && $_POST['maximumPrice'] != '') { ?>
  <!-- if the issue is with the price filters: -->
  <h2>Please ensure maximum price is greater than minimum price.</h2>
<?php } else if (($_POST['origin'] == $_POST['destination']) && ($_POST['origin'] != '')) { ?>
  <!-- if the issue is with the origin/destination filters: -->
  <h2>Please select different cities for the origin and destination.</h2>
<?php } else { ?>
  <!-- if there's no issue with the filters, but there was just no matching flight: -->
  <h2>No flights found based on the selected filters. Try changing the specified origin, destination, or price range</h2>
<?php }
// footer
require('misc/footer.php'); ?>