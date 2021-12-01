<?php

require('misc/header.php');

$addTripErrors = $modifyTripErrors = $editPassengersErrors = $blacklistErrors = [];

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
    $date = $_POST['addTripDate'];
    pushErrorIfBlank($date, $addTripErrors, 'Date');

    // store time and check if it's blank
    $time = $_POST['addTripTime'];
    pushErrorIfBlank($time, $addTripErrors, 'Time');

    // store number of rows and columns and check if they're blank
    $numberOfRows = $_POST['addTripNumberOfRows'];
    pushErrorIfBlank($numberOfRows, $addTripErrors, 'Number of rows');
    $numberOfColumns = $_POST['addTripNumberOfColumns'];
    pushErrorIfBlank($numberOfColumns, $addTripErrors, 'Number of columns');

    if (sizeof($addTripErrors) == 0) {
      // trim origin and destination
      $origin = formatNameOriginDestination($origin);
      $destination = formatNameOriginDestination($destination);

      // check if a route already exists with the specified origin, destination, and price
      $stmt = $pdo->prepare('SELECT 1 FROM routes WHERE origin=:origin && destination=:destination && price=:price');
      $stmt->execute(['origin' => $origin, 'destination' => $destination, 'price' => $_POST['addTripPrice']]);
      if ($stmt->rowCount() == 0) {
        echo ('row count is 0');
      } else {
      }
    }
  }
}

function echoDateField(string $POSTfieldName) { ?>
  Date: <input type="date" name="<?= $POSTfieldName ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>>
<?php }

function echoTimeField(string $POSTfieldName) { ?>
  Time: <input type="time" name="<?= $POSTfieldName ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>>
<?php }

?>

<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
  <h2>Add a trip</h2>
  <h3>A new route will be created if no route already exists with the given origin, destination and price</h3>
  <?php echoErrors($addTripErrors); ?>
  <h4>Add a trip from <?= echoTextField('addTripOrigin', 'origin'); ?>
    to
    <?= echoTextField('addTripDestination', 'destination'); ?>:
  </h4>
  <p>Price: <?php echoPriceField('addTripPrice', 'Price'); ?></p>
  <p><?= echoDateField('addTripDate') ?> <?= echoTimeField('addTripTime') ?></p>
  <h4>Aircraft info:</h4>
  <p>Number of rows: <input type="number" name="addTripNumberOfRows" placeholder="rows" min='1' max='90'></p>
  <p>Number of columns: <input type="number" name="addTripNumberOfColumns" placeholder="columns" min='1' max='10'></p>
  <input type="submit" name="submit" value="Add Trip">
  <hr>
  <h2>Modify trip info</h2>
  <hr>
  <h2>Edit passenger list/delete passengers</h2>
  <hr>
  <h2>Edit blacklist</h2>
</form>


<?php require('misc/footer.php'); ?>