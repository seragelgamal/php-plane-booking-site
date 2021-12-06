<?php

require('misc/header.php');

$addTripErrors = $modifyTripErrors = $editPassengersErrors = $blacklistErrors = [];
$feedbackMessage = '';

// get an array of all the routes
$stmt = $pdo->query('SELECT * FROM routes');
$routes = $stmt->fetchAll();
// var_dump($routes);

// get all available trips for the modify trip section
$stmt = $pdo->query("SELECT * FROM flights");
$trips = $stmt->fetchAll();
// sort alphabetically by origin to make trips easier to browse
// $originsOfTrips = [];
// foreach ($trips as $trip) {
//   $route = searchArrayOfObjects($routes, 'id', $trip->route_id);
//   array_push($originsOfTrips, [$route->id, $route->origin]);
// }
// insertionSort($originsOfTrips);
// // now have a sorted array of the origins of all the trips
// $copyOfTrips = $trips;
// $trips = [];
// // foreach ($copyOfTrips as $trip) {
// //   foreach ()
// //   array_push($trips, searchArrayOfObjects($copyOfTrips, 'route_id', searchArrayOfObjects($routes, 'origin', $trip[1])));
// // }
// // var_dump($originsOfTrips);
// // sortElementsByProperty($trips, 'origin');

// form action
if (isset($_POST['submit'])) {
  if ($_POST['submit'] == 'Add Trip') {
    // store origin and get any errors 
    $origin = $_POST['addTripOrigin'];
    $addTripErrors = originDestinationErrorArray($origin, 'Origin');

    // store destination and get any errors 
    $destination = $_POST['addTripDestination'];
    $addTripErrors = array_merge($addTripErrors, originDestinationErrorArray($destination, 'Destination'));

    // check if price's blank
    pushErrorIfBlank($_POST['addTripPrice'], $addTripErrors, 'Price');

    // store date and check if it's blank
    pushErrorIfBlank($_POST['addTripDate'], $addTripErrors, 'Date');

    // store time and check if it's blank
    pushErrorIfBlank($_POST['addTripTime'], $addTripErrors, 'Time');

    // store number of rows and columns and check if they're blank
    pushErrorIfBlank($_POST['addTripNumberOfRows'], $addTripErrors, 'Number of rows');
    pushErrorIfBlank($_POST['addTripNumberOfColumns'], $addTripErrors, 'Number of columns');

    if (sizeof($addTripErrors) == 0) {
      // trim origin and destination
      $origin = formatNameOriginDestination($origin);
      $destination = formatNameOriginDestination($destination);

      // try storing the route with the specified origin, destination, and price. if it doesn't exist, create it
      if (!($route = getRouteFromDatabase($pdo, 'routes', $origin, $destination, $_POST['addTripPrice']))) {
        // add a new route with the given origin, destination, and price, and add a trip to the new route with the given date, time, and aircraft info
        $stmt = $pdo->prepare('INSERT INTO routes (origin, destination, price) VALUES (:origin, :destination, :price)');
        $stmt->execute(['origin' => $origin, 'destination' => $destination, 'price' => $_POST['addTripPrice']]);
        $feedbackMessage = $feedbackMessage . 'No route was found with the specified origin, destination, and price - a new route was added<br>';

        // get the route from the database now that it exists
        $route = getRouteFromDatabase($pdo, 'routes', $origin, $destination, $_POST['addTripPrice']);
      }
      // add a trip to the route with the specified origin, destination, and price with the given date, time, and aircraft info
      $stmt = $pdo->prepare('INSERT INTO flights (route_id, date, time, number_of_rows, number_of_columns, capacity) VALUES (:routeId, :date, :time, :numberOfRows, :numberOfColumns, :capacity)');
      $stmt->execute(['routeId' => $route->id, 'date' => $_POST['addTripDate'], 'time' => $_POST['addTripTime'], 'numberOfRows' => $_POST['addTripNumberOfRows'], 'numberOfColumns' => $_POST['addTripNumberOfColumns'], 'capacity' => ($_POST['addTripNumberOfRows'] * $_POST['addTripNumberOfColumns'])]);

      $feedbackMessage = $feedbackMessage . 'Trip successfully added<br>';

      unset($_POST);
    }
  } else if ($_POST['submit'] == 'Modify Trip') {
  }
}

function searchArrayOfObjects(array $arrayOfObjects, string $property, mixed $value) {
  foreach ($arrayOfObjects as $object) {
    if ($object->$property === $value) {
      return $object;
    }
  }
  return false;
}

?>

<h2 style="color: blue;"><?= $feedbackMessage ?></h2>
<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
  <h2>Add a trip</h2>
  <h3>A new route will be created if no route already exists with the given origin, destination and price</h3>
  <?= echoErrors($addTripErrors); ?>
  <h4>Add a trip from <?= echoTextField('addTripOrigin', 'origin'); ?>
    to
    <?= echoTextField('addTripDestination', 'destination'); ?>:
  </h4>
  <p>Price: <?= echoPriceField('addTripPrice', 'price'); ?></p>
  <p><?= echoDateField('addTripDate') ?> <?= echoTimeField('addTripTime') ?></p>
  <h4>Aircraft info:</h4>
  <p>Number of rows: <?= echoRowColumnNumberField('addTripNumberOfRows', 'rows', 90); ?></p>
  <p>Number of columns: <?= echoRowColumnNumberField('addTripNumberOfColumns', 'columns', 10); ?></p>
  <input type="submit" name="submit" value="Add Trip">
  <hr>
  <h2>Modify trip info</h2>
  <h3>Trip to modify:</h3>
  <select name="tripToModify">
    <option value="">Select a trip to modify...</option>
    <?php foreach ($trips as $trip) {
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id); ?>
      <option value="<?= $trip->id ?>"><?= $route->origin ?> to <?= $route->destination ?> ($<?= $route->price ?>) - <?= $trip->date ?> at <?= $trip->time ?></option>
    <?php } ?>
  </select>
  <input type="submit" name="submit" value="Modify Trip">
  <div <?php if (!isset($_POST['tripToModify']) || $_POST['tripToModify'] == '') { ?> hidden <?php } ?>>
    <h4>Trip info:</h4>
    <p>Origin: <?= echoTextField('modifyTripOrigin', 'origin'); ?></p>
    <p>Destination: <?= echoTextField('modifyTripDestination', 'destination'); ?></p>
    <p>Price: <?= echoPriceField('modifyTripPrice', 'price'); ?></p>
    <p><?= echoDateField('modifyTripDate') ?> <?= echoTimeField('modifyTripTime') ?></p>
    <h4>Aircraft info:</h4>
    <p>Number of rows: <?= echoRowColumnNumberField('modifyTripNumberOfRows', 'rows', 90); ?></p>
    <p>Number of columns: <?= echoRowColumnNumberField('modifyTripNumberOfColumns', 'columns', 10); ?></p>
    <input type="submit" name="submit" value="Save">
  </div>
  <hr>
  <h2>Edit passenger list/delete passengers</h2>
  <hr>
  <h2>Edit blacklist</h2>
</form>


<?php require('misc/footer.php'); ?>