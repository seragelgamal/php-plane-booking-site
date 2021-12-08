<?php

require('misc/header.php');

// error arrays
$addTripErrors = $modifyTripErrors = $editPassengersErrors = $blacklistErrors = [];

// global variables for trip modification section
// $modifyTripOrigin = $modifyTripDestination = $modifyTripPrice = $modifyTripDate = $modifyTripTime = $modifyTripNumberOfRows = $modifyTripNumberOfColumns = '';
// $modifyTripPOSTfieldNames = ['modifyTripOrigin', 'modifyTripDestination', 'modifyTripPrice', 'modifyTripDate']

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

    // check if date's blank
    pushErrorIfBlank($_POST['addTripDate'], $addTripErrors, 'Date');

    // check if time's blank
    pushErrorIfBlank($_POST['addTripTime'], $addTripErrors, 'Time');

    // check if number of rows and number of columns are blank
    pushErrorIfBlank($_POST['addTripNumberOfRows'], $addTripErrors, 'Number of rows');
    pushErrorIfBlank($_POST['addTripNumberOfColumns'], $addTripErrors, 'Number of columns');

    if (sizeof($addTripErrors) == 0) {
      // trim origin and destination
      $origin = formatNameOriginDestination($origin);
      $destination = formatNameOriginDestination($destination);

      // try storing the route with the specified origin, destination, and price. if it doesn't exist, create it
      if (!($route = getRouteFromDatabase($pdo, 'routes', $origin, $destination, $_POST['addTripPrice']))) {
        // add a new route with the given origin, destination, and price
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
    if (!pushErrorIfBlank($_POST['tripToModify'], $modifyTripErrors, 'Trip selection')) {
      $trip = searchArrayOfObjects($trips, 'id', $_POST['tripToModify']);
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id);
      $_POST['modifyTripOrigin'] = $route->origin;
      $_POST['modifyTripDestination'] = $route->destination;
      $_POST['modifyTripPrice'] = $route->price;
      $_POST['modifyTripDate'] = $trip->date;
      $_POST['modifyTripTime'] = $trip->time;
      $_POST['modifyTripNumberOfRows'] = $trip->number_of_rows;
      $_POST['modifyTripNumberOfColumns'] = $trip->number_of_columns;
    }
  } else if ($_POST['submit'] == 'Save Changes') {
    // store origin and get any errors 
    $origin = $_POST['modifyTripOrigin'];
    $modifyTripErrors = originDestinationErrorArray($origin, 'Origin');

    // store destination and get any errors 
    $destination = $_POST['modifyTripDestination'];
    $modifyTripErrors = array_merge($modifyTripErrors, originDestinationErrorArray($destination, 'Destination'));

    // check if price's blank
    pushErrorIfBlank($_POST['modifyTripPrice'], $modifyTripErrors, 'Price');

    // check if date's blank
    pushErrorIfBlank($_POST['modifyTripDate'], $modifyTripErrors, 'Date');

    // check if time's blank
    pushErrorIfBlank($_POST['modifyTripTime'], $modifyTripErrors, 'Time');

    // check if number of rows and number of columns are blank
    pushErrorIfBlank($_POST['modifyTripNumberOfRows'], $modifyTripErrors, 'Number of rows');
    pushErrorIfBlank($_POST['modifyTripNumberOfColumns'], $modifyTripErrors, 'Number of columns');

    if (sizeof($modifyTripErrors) == 0) {
      // trim origin and destination
      $origin = formatNameOriginDestination($origin);
      $destination = formatNameOriginDestination($destination);

      // save old trip
      $oldTrip = searchArrayOfObjects($trips, 'id', $_POST['tripToModify']);

      // check if a route exists with the specified origin, destination, and price. if it doesn't, create it
      if (!($route = getRouteFromDatabase($pdo, 'routes', $origin, $destination, $_POST['modifyTripPrice']))) {
        // if no such route exists:
        // make a new route with the specified origin, destination, and price
        $stmt = $pdo->prepare('INSERT INTO routes (origin, destination, price) VALUES (:origin, :destination, :price)');
        $stmt->execute(['origin' => $origin, 'destination' => $destination, 'price' => $_POST['modifyTripPrice']]);
        $feedbackMessage = $feedbackMessage . 'No route was found with the new origin, destination, and price - a new route was added<br>';

        // get the route object from the database now that the route exists
        $route = getRouteFromDatabase($pdo, 'routes', $origin, $destination, $_POST['modifyTripPrice']);
      }

      // update the trip
      $stmt = $pdo->prepare('UPDATE flights SET route_id = :routeId, date = :date, time = :time, number_of_rows = :numberOfRows, number_of_columns = :numberOfColumns, capacity = :capacity WHERE id = :id');
      $stmt->execute(['routeId' => $route->id, 'date' => $_POST['modifyTripDate'], 'time' => $_POST['modifyTripTime'], 'numberOfRows' => $_POST['modifyTripNumberOfRows'], 'numberOfColumns' => $_POST['modifyTripNumberOfColumns'], 'capacity' => $_POST['modifyTripNumberOfRows'] * $_POST['modifyTripNumberOfColumns'], 'id' => $_POST['tripToModify']]);
      $feedbackMessage = $feedbackMessage . "Trip successfully modified<br>";

      // check if the old route now has 0 trips, and if so, delete it
      $stmt = $pdo->prepare("SELECT * FROM flights WHERE route_id=:routeId");
      $stmt->execute(['routeId' => $oldTrip->route_id]);
      if ($stmt->rowCount() == 0) {
        // delete the route
        $stmt = $pdo->prepare("DELETE FROM routes WHERE id=:id");
        $stmt->execute(['id' => $oldTrip->route_id]);
      }

      // if row/column number has decreased, delete all bookings for the trip
      if ($_POST['modifyTripNumberOfRows'] < $oldTrip->number_of_rows || $_POST['modifyTripNumberOfColumns'] < $oldTrip->number_of_columns) {
        $stmt = $pdo->prepare('DELETE FROM flight_bookings WHERE flight_id = :flightId');
        $stmt->execute(['flightId' => $oldTrip->id]);
      }
    }
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

function checkPOSTvalue(string $POSTfieldName, mixed $value) {
  if (isset($_POST[$POSTfieldName]) && $_POST[$POSTfieldName] == $value) {
    return 'selected';
  }
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
  <h2>Modify/delete trip</h2>
  <?= echoErrors($modifyTripErrors) ?>
  <h3>Select a trip:</h3>
  <select name="tripToModify">
    <option value="">Select a trip to modify/delete...</option>
    <?php foreach ($trips as $trip) {
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id); ?>
      <option value="<?= $trip->id ?>" <?= checkPOSTvalue('tripToModify', $trip->id) ?>><?= $route->origin ?> to <?= $route->destination ?> ($<?= $route->price ?>) - <?= $trip->date ?> at <?= $trip->time ?></option>
    <?php } ?>
  </select>
  <input type="submit" name="submit" value="Modify Trip">
  <input type="submit" name="submit" value="Delete Trip">
  <div <?php if (!isset($_POST['tripToModify']) || $_POST['tripToModify'] == '') { ?> hidden <?php } ?>>
    <h4>Trip info:</h4>
    <h5>A new route will be created if the changed trip's origin, destination, and price no longer match an existing route</h5>
    <h5>If aircraft row or column number decreases, all current bookings for the trip will be deleted to avoid passengers having non-existent seat numbers</h5>
    <p>Origin: <?= echoTextField('modifyTripOrigin', 'origin'); ?></p>
    <p>Destination: <?= echoTextField('modifyTripDestination', 'destination'); ?></p>
    <p>Price: <?= echoPriceField('modifyTripPrice', 'price'); ?></p>
    <p><?= echoDateField('modifyTripDate') ?> <?= echoTimeField('modifyTripTime') ?></p>
    <h4>Aircraft info:</h4>
    <p>Number of rows: <?= echoRowColumnNumberField('modifyTripNumberOfRows', 'rows', 90); ?></p>
    <p>Number of columns: <?= echoRowColumnNumberField('modifyTripNumberOfColumns', 'columns', 10); ?></p>
    <input type="submit" name="submit" value="Save Changes">
  </div>
  <hr>
  <h2>Edit passenger list/delete passengers</h2>
  <hr>
  <h2>Edit blacklist</h2>
</form>


<?php require('misc/footer.php'); ?>