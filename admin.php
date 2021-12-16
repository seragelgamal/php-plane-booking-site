<?php

require('misc/header.php');

// error arrays
$addTripErrors = $modifyTripErrors = $deleteTripErrors = $modifyBookingsErrors = $blacklistErrors = [];

// global variables for trip modification section
// $modifyTripOrigin = $modifyTripDestination = $modifyTripPrice = $modifyTripDate = $modifyTripTime = $modifyTripNumberOfRows = $modifyTripNumberOfColumns = '';
// $modifyTripPOSTfieldNames = ['modifyTripOrigin', 'modifyTripDestination', 'modifyTripPrice', 'modifyTripDate']

$feedbackMessage = $booking = '';
$bookings = [];

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

      $_POST = [];
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

        $feedbackMessage = $feedbackMessage . "The old trip's route no longer has any trips and has been deleted<br>";
      }

      // if row/column number has decreased, delete all bookings for the trip
      if ($_POST['modifyTripNumberOfRows'] < $oldTrip->number_of_rows || $_POST['modifyTripNumberOfColumns'] < $oldTrip->number_of_columns) {
        $stmt = $pdo->prepare('DELETE FROM flight_bookings WHERE flight_id = :flightId');
        $stmt->execute(['flightId' => $oldTrip->id]);

        $feedbackMessage = $feedbackMessage . 'Row or column number was decreased - all bookings for the modified trip have been deleted<br>';
      }

      $_POST = [];
    }
  } else if ($_POST['submit'] == 'Delete Trip') {
    pushErrorIfBlank($_POST['tripToDelete'], $deleteTripErrors, 'Trip selection');
  } else if ($_POST['submit'] == 'Cancel') {
    $_POST = [];
  } else if ($_POST['submit'] == 'Yes, permanently delete this trip') {
    // get the trip object
    $trip = searchArrayOfObjects($trips, 'id', $_POST['tripToDelete']);

    // delete the trip
    $stmt = $pdo->prepare('DELETE FROM flights WHERE id = :id');
    $stmt->execute(['id' => $trip->id]);

    $feedbackMessage = $feedbackMessage . 'Trip successfully deleted<br>';

    // check if the trip's route now has 0 trips, and delete it if it does
    $stmt = $pdo->prepare('SELECT * FROM flights WHERE route_id = :routeId');
    $stmt->execute(['routeId' => $trip->route_id]);
    if ($stmt->rowCount() == 0) {
      // delete the route
      $stmt = $pdo->prepare('DELETE FROM routes WHERE id = :id');
      $stmt->execute(['id' => $trip->route_id]);

      $feedbackMessage = $feedbackMessage . "The deleted trip's route no longer has any trips and has been deleted<br>";
    }

    $_POST = [];
  } else if ($_POST['submit'] == 'Modify Bookings') {
    if (!pushErrorIfBlank($_POST['passengerListToModify'], $modifyBookingsErrors, 'Trip selection')) {
      // get bookings for the selected flight
      $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE flight_id = :flightId");
      $stmt->execute(['flightId' => $_POST['passengerListToModify']]);
      $bookings = $stmt->fetchAll();
    }
  } else if ($_POST['submit'] == 'Modify Booking') {
    if (!pushErrorIfBlank($_POST['bookingToModify'], $modifyBookingsErrors, 'Booking selection')) {
      // get bookings for the selected flight
      $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE flight_id = :flightId");
      $stmt->execute(['flightId' => $_POST['passengerListToModify']]);
      $bookings = $stmt->fetchAll();

      $booking = searchArrayOfObjects($bookings, 'id', $_POST['bookingToModify']);
      $_POST['modifyBookingFirstName'] = $booking->first_name;
      $_POST['modifyBookingLastName'] = $booking->last_name;
      $_POST['modifyBookingSeatRow'] = $booking->seat_row;
      $_POST['modifyBookingSeatColumn'] = $booking->seat_column;
    }
  } else if ($_POST['submit'] == 'Delete Booking') {
    if (!pushErrorIfBlank($_POST['bookingToModify'], $modifyBookingsErrors, 'Booking selection')) {
      // get bookings for the selected flight
      $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE flight_id = :flightId");
      $stmt->execute(['flightId' => $_POST['passengerListToModify']]);
      $bookings = $stmt->fetchAll();

      $booking = searchArrayOfObjects($bookings, 'id', $_POST['bookingToModify']);
    }
  } else if ($_POST['submit'] == 'Yes, permanently delete this booking') {
    // get bookings for the selected flight
    $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE flight_id = :flightId");
    $stmt->execute(['flightId' => $_POST['passengerListToModify']]);
    $bookings = $stmt->fetchAll();

    $booking = searchArrayOfObjects($bookings, 'id', $_POST['bookingToModify']);

    $stmt = $pdo->prepare("DELETE FROM flight_bookings WHERE id = :id");
    $stmt->execute(['id' => $booking->id]);

    $feedbackMessage = $feedbackMessage . 'Booking successfully deleted<br>';

    $_POST = [];
  } else if ($_POST['submit'] == 'Update Booking') {
    // get bookings for the selected flight
    $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE flight_id = :flightId");
    $stmt->execute(['flightId' => $_POST['passengerListToModify']]);
    $bookings = $stmt->fetchAll();

    pushErrorIfBlank($_POST['modifyBookingFirstName'], $modifyBookingsErrors, 'First name');
    pushErrorIfBlank($_POST['modifyBookingLastName'], $modifyBookingsErrors, 'Last name');

    $firstName = $_POST['modifyBookingFirstName'];
    $lastName = $_POST['modifyBookingLastName'];

    $modifyBookingsErrors = array_merge($modifyBookingsErrors, nameErrorArray($firstName, 'First name'), nameErrorArray($lastName, 'Last name'));

    // separate seat row and column and store them
    $seat = strlen($_POST['modifyBookingSeat']) > 2 ? chunk_split($_POST['modifyBookingSeat'], 2, ' ') : chunk_split($_POST['modifyBookingSeat'], 1, ' ');
    $row = strtok($seat, ' ');
    $column = strtok(' ');

    if (sizeof($modifyBookingsErrors) == 0) {
      $firstName = formatNameOriginDestination($firstName);
      $lastName = formatNameOriginDestination($lastName);

      $stmt = $pdo->prepare("UPDATE flight_bookings SET first_name = :firstName, last_name = :lastName, seat_row = :seatRow, seat_column = :seatColumn WHERE id = :id");
      $stmt->execute(['firstName' => $firstName, 'lastName' => $lastName, 'seatRow' => $row, 'seatColumn' => $column, 'id' => $_POST['bookingToModify']]);

      $feedbackMessage = $feedbackMessage . 'Booking successfully modified<br>';

      $_POST = [];
    } else {
      $_POST['modifyBookingSeatRow'] = $row;
      $_POST['modifyBookingSeatColumn'] = $column;
    }
  } else if ($_POST['submit'] == 'Blacklist') {
    pushErrorIfBlank($_POST['addToBlacklistFirstName'], $modifyBookingsErrors, 'First name');
    pushErrorIfBlank($_POST['modifyBookingLastName'], $modifyBookingsErrors, 'Last name');
  }
}

// returns the object from an array of similar objects whose specified property is a given value
function searchArrayOfObjects(array $arrayOfObjects, string $property, mixed $value) {
  foreach ($arrayOfObjects as $object) {
    if ($object->$property === $value) {
      return $object;
    }
  }
  return false;
}

// to be used inside the brackets of an input element: echoes 'selected' if the POST value matches the value of the inputted element
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
  <h2>Modify trip</h2>
  <?= echoErrors($modifyTripErrors) ?>
  <h3>Select a trip:</h3>
  <select name="tripToModify">
    <option value="">Select a trip to modify...</option>
    <?php foreach ($trips as $trip) {
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id); ?>
      <option value="<?= $trip->id ?>" <?= checkPOSTvalue('tripToModify', $trip->id) ?>><?= $route->origin ?> to <?= $route->destination ?> ($<?= $route->price ?>) - <?= $trip->date ?> at <?= $trip->time ?></option>
    <?php } ?>
  </select>
  <input type="submit" name="submit" value="Modify Trip">
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
  <h2>Delete trip</h2>
  <?= echoErrors($deleteTripErrors) ?>
  <h3>Select a trip:</h3>
  <select name="tripToDelete">
    <option value="">Select a trip to delete...</option>
    <?php foreach ($trips as $trip) {
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id); ?>
      <option value="<?= $trip->id ?>" <?= checkPOSTvalue('tripToDelete', $trip->id) ?>><?= $route->origin ?> to <?= $route->destination ?> ($<?= $route->price ?>) - <?= $trip->date ?> at <?= $trip->time ?></option>
    <?php } ?>
  </select>
  <input type="submit" name="submit" value="Delete Trip">
  <div <?php if (!isset($_POST['tripToDelete']) || $_POST['tripToDelete'] == '') { ?> hidden <?php } ?>>
    <h4>Are you sure you want to delete this trip? <em>This is permanent</em></h4>
    <input type="submit" name="submit" value="Cancel">
    <input type="submit" name="submit" value="Yes, permanently delete this trip">
  </div>
  <hr>
  <h2>Modify/delete bookings</h2>
  <?= echoErrors($modifyBookingsErrors) ?>
  <h3>Select a trip:</h3>
  <select name="passengerListToModify">
    <option value="">Select a trip to modify its passenger list...</option>
    <?php foreach ($trips as $trip) {
      $route = searchArrayOfObjects($routes, 'id', $trip->route_id); ?>
      <option value="<?= $trip->id ?>" <?= checkPOSTvalue('passengerListToModify', $trip->id) ?>><?= $route->origin ?> to <?= $route->destination ?> ($<?= $route->price ?>) - <?= $trip->date ?> at <?= $trip->time ?></option>
    <?php } ?>
  </select>
  <input type="submit" name="submit" value="Modify Bookings">
  <div <?php if (!isset($_POST['passengerListToModify']) || $_POST['passengerListToModify'] == '') { ?> hidden <?php } ?>>
    <!-- booking selection -->
    <h4>Select a booking from the selected trip:</h4>
    <select name="bookingToModify">
      <option value="">Select a booking to modify/delete...</option>
      <?php foreach ($bookings as $booking) { ?>
        <option value="<?= $booking->id ?>" <?= checkPOSTvalue('bookingToModify', $booking->id) ?>><?= $booking->first_name ?> <?= $booking->last_name ?> - seat <?= $booking->seat_row ?><?= $booking->seat_column ?></option>
      <?php } ?>
    </select>
    <input type="submit" name="submit" value="Modify Booking">
    <input type="submit" name="submit" value="Delete Booking">
    <div <?php if (!isset($_POST['bookingToModify']) || $_POST['bookingToModify'] == '' || $_POST['submit'] == 'Delete Booking') { ?> hidden <?php } ?>>
      <h5>Booking info:</h5>
      <p>First name: <?= echoTextField('modifyBookingFirstName', 'First name') ?></p>
      <p>Last name: <?= echoTextField('modifyBookingLastName', 'Last name') ?></p>
      <p>Seat: <?php if ($_POST['submit'] == 'Modify Booking' || $_POST['submit'] == 'Update Booking') {
                  echoSeatSelector(searchArrayOfObjects($trips, 'id', $_POST['passengerListToModify']), $bookings, 'modifyBookingSeat', $_POST['modifyBookingSeatRow'], $_POST['modifyBookingSeatColumn'], false, searchArrayOfObjects($bookings, 'id', $_POST['bookingToModify']));
                } ?></p>
      <input type="submit" name="submit" value="Update Booking">
    </div>
    <div <?php if (!isset($_POST['bookingToModify']) || $_POST['bookingToModify'] == '' || $_POST['submit'] == 'Modify Booking') { ?> hidden <?php } ?>>
      <h5>Are you sure you want to delete this booking? <em>This is permanent</em></h5>
      <input type="submit" name="submit" value="Cancel">
      <input type="submit" name="submit" value="Yes, permanently delete this booking">
    </div>
  </div>
  <hr>
  <h2>Add to blacklist</h2>
  <?= echoErrors($blacklistErrors) ?>
  <p>First name: <?= echoTextField('addToBlacklistFirstName', 'first name') ?></p>
  <p>Last name: <?= echoTextField('addToBlacklistLastName', 'last name') ?></p>
  <input type="submit" name="submit" value="Blacklist">
  <hr>
  <h2>Remove from blacklist</h2>
</form>


<?php

// if (!isset($_POST['bookingToModify']) || $_POST['bookingToModify'] == '' || $_POST['submit'] == 'Modify Booking') { 
//  hidden
//  }

require('misc/footer.php');
?>