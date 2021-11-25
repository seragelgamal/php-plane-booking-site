<?php

// require header template
require('misc/header.php');

// get all routes from database
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();

// set global variable for relevant routes based on search
$searchedRoutes = [];

// FUNCTIONS:
// origin/destination filter function: pushes values from array of all routes that match with the specified location filter (either 'origin' or 'destination')
function originDestinationFilter(array $totalRouteArray, string $filterLocation, string $formValue, array &$filteredRouteArray) {
  foreach ($totalRouteArray as $route) {
    if ($route->$filterLocation == $formValue) {
      array_push($filteredRouteArray, $route);
    }
  }
}
// price filter function: takes value with specified index out of the specified array and re-keys the array
function priceFilter(array &$array, int $i) {
  unset($array[$i]);
  $array = array_values($array);
}
function echoPriceField(string $POSTfieldName) { ?>
  $<input type="number" name=<?= $POSTfieldName ?> min="0" placeholder="any price" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>>
<?php }
function echoOriginDestinationDropdown(string $POSTfieldName, array $totalRouteArray) { ?>
  <select name=<?= $POSTfieldName ?>>
    <option value="">All <?= $POSTfieldName ?>s</option>
    <?php $originDestinationArray = [];
    foreach ($totalRouteArray as $route) {
      if (!in_array($route->$POSTfieldName, $originDestinationArray)) {
        array_push($originDestinationArray, $route->$POSTfieldName); ?>
        <option <?php if (isset($_POST[$POSTfieldName]) && $_POST[$POSTfieldName] == $route->$POSTfieldName) { ?> selected <?php } ?>><?= $route->$POSTfieldName ?></option>
    <?php }
    } ?>
  </select>
<?php }
function sortElementsByProperty(array &$array, string $property) {
  $arrayOfProperties = [];
  foreach ($array as $element) {
    array_push($arrayOfProperties, $element->$property);
  }
  insertionSort($arrayOfProperties);
  $copyOfArray = $array;
  $array = [];
  foreach ($arrayOfProperties as $elementProperty) {
    foreach ($copyOfArray as $element) {
      if ($element->$property == $elementProperty && !in_array($element, $array)) {
        array_push($array, $element);
      }
    }
  }
}
function echoRadioInput(string $POSTfieldName, string $value, string $label) { ?>
  <input type="radio" name="<?= $POSTfieldName ?>" value="<?= $value ?>" <?php if ($_POST[$POSTfieldName] == $value) { ?> checked <?php } ?>>
<?php echo ($label);
}

// form action: get available flights based on specified filters and sort the results
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
    originDestinationFilter($routes, 'origin', $_POST['origin'], $searchedRoutes);
  } else if ($_POST['destination'] != '') {
    // if only destination is set
    originDestinationFilter($routes, 'destination', $_POST['destination'], $searchedRoutes);
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
        priceFilter($searchedRoutes, $i);
        $i--;
      }
    }
  }
  if ($_POST['maximumPrice'] != '') {
    // if max price is set
    for ($i = 0; $i < sizeof($searchedRoutes); $i++) {
      if ($searchedRoutes[$i]->price > $_POST['maximumPrice']) {
        priceFilter($searchedRoutes, $i);
        $i--;
      }
    }
  }

  // sorting
  if ($_POST['sortMode'] != 'default') {
    if ($_POST['sortMode'] == 'cheapToExpensive') {
      sortElementsByProperty($searchedRoutes, 'price');
    } else if ($_POST['sortMode'] == 'expensiveToCheap') {
      sortElementsByProperty($searchedRoutes, 'price');
      $searchedRoutes = array_reverse($searchedRoutes);
    } else if ($_POST['sortMode'] == 'a-zOrigin') {
      sortElementsByProperty($searchedRoutes, 'origin');
    } else if ($_POST['sortMode'] == 'z-aOrigin') {
      sortElementsByProperty($searchedRoutes, 'origin');
      $searchedRoutes = array_reverse($searchedRoutes);
    } else if ($_POST['sortMode'] == 'a-zDestination') {
      sortElementsByProperty($searchedRoutes, 'destination');
    } else if ($_POST['sortMode'] == 'z-aDestination') {
      sortElementsByProperty($searchedRoutes, 'destination');
      $searchedRoutes = array_reverse($searchedRoutes);
    }
  }
} else {
  // if no filters are set
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
  // if search doesn't yield any results
} else if ((!($_POST['minimumPrice'] <= $_POST['maximumPrice'])) && $_POST['minimumPrice'] != '' && $_POST['maximumPrice'] != '') { ?>
  <h2>Please ensure maximum price is greater than minimum price.</h2>
<?php } else if (($_POST['origin'] == $_POST['destination']) && ($_POST['origin'] != '')) { ?>
  <h2>Please select different cities for the origin and destination.</h2>
<?php } else { ?>
  <h2>No flights found based on the selected filters. Try changing the specified origin, destination, or price range</h2>
<?php }
require('misc/footer.php'); ?>