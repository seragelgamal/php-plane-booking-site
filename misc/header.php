<?php

// MULTI-USE FUNCTIONS:
// pushes appropriate error if something is blank
function pushErrorIfBlank(mixed $input, array &$errorArray, string $fieldName) {
  if ($input == '') {
    array_push($errorArray, "$fieldName is required");
    return true;
  }
  return false;
}
// returns the properly-formatted version of a user-entered name
function formatNameOriginDestination(string $nameVariable) {
  // take out spaces from the start and end of the name if they exist
  $nameVariable = trim($nameVariable);
  // convert all letters to lower case
  $nameVariable = strtolower($nameVariable);
  // set first letter to uppercase
  $nameVariable = ucwords($nameVariable);
  return $nameVariable;
}
// 
function echoSeatSelector(object $flight, array $bookings, string $POSTfieldName, mixed $row, string $column, bool $flightBookingPage = true, object $bookingToModify = NULL) {
  // set global array '$seats' to store all possible seats
  $columnLegend = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K'];
  $seats = [];
  for ($seatRow = 1; $seatRow < ($flight->number_of_rows + 1); $seatRow++) {
    for ($seatColumn = 0; $seatColumn < 6; $seatColumn++) {
      array_push($seats, $seatRow . $columnLegend[$seatColumn]);
    }
  }
  // set global array '$seatsBooked' to store the seats that are already booked
  $seatsBooked = [];
  foreach ($bookings as $booking) {
    array_push($seatsBooked, $booking->seat_row . $booking->seat_column);
  }
  // take out already-booked seats from the $seats array
  $seats = array_diff($seats, $seatsBooked);

  for ($r = 1; $r < ($flight->number_of_rows + 1); $r++) { ?>
    <p><?= $r ?></p>
    <div class="seatRow">
      <div class="seatColumn">
        <?php for ($c = 0; $c < ($flight->number_of_columns); $c++) { ?>
          <input type="radio" name="<?= $POSTfieldName ?>" value="<?= "$r{$columnLegend[$c]}" ?>" <?php if ($flightBookingPage) {
                                                                                    foreach ($bookings as $booking) {
                                                                                      if ("$r{$columnLegend[$c]}" == "{$booking->seat_row}{$booking->seat_column}") { ?> disabled <?php }
                                                                                                                                                                              }
                                                                                                                                                                            } else {
                                                                                                                                                                              foreach ($bookings as $booking) {
                                                                                                                                                                                if ("$r{$columnLegend[$c]}" == "{$booking->seat_row}{$booking->seat_column}" && "{$bookingToModify->seat_row}{$bookingToModify->seat_column}" != "$r{$columnLegend[$c]}") { ?> disabled <?php }
                                                                                                                                                                                                                                                                      }
                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                    if ("$r{$columnLegend[$c]}" == "$row$column") { ?> checked <?php } ?>>
          <?= $columnLegend[$c] ?>
        <?php } ?>
      </div>
    </div>
  <?php }
}
// echoes a price input field with the specified POST field name to input minimum and maximum price
function echoPriceField(string $POSTfieldName, string $placeholder = '') { ?>
  $<input type="number" name=<?= $POSTfieldName ?> min="0" placeholder="<?= $placeholder ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>>
<?php }

// document-specific functions:
// bookFlight.php:
// returns an array of the errors for a user-entered name
function nameErrorArray(string $nameVariable, string $fieldName) {
  $errorArray = originDestinationErrorArray($nameVariable, $fieldName);
  // make sure name's only letters
  if (preg_match('~[0-9]~', $nameVariable)) {
    array_push($errorArray, "$fieldName can only contain letters");
  }
  return $errorArray;
}
// echoes errors from a specified error array with formatting
function echoErrors(array $errorArray) { ?>
  <p class="errors"><?php foreach ($errorArray as $error) {
                      echo ("$error <br>");
                    } ?></p>
<?php }
// echoes a first/last name field with a given label for the user and POST superglobal index name, as well as the errors from a given error array with formatting
function echoNameField(string $userFieldName, string $POSTfieldName, array $errorArray) { ?>
  <p><?= $userFieldName ?>: <input type="text" name="<?= $POSTfieldName ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>></p>
<?php echoErrors($errorArray);
}
// return an array of all the errors found for an admin-entered username
function usernameErrorArray(string $usernameVariable) {
  $errorArray = [];
  if (!pushErrorIfBlank($usernameVariable, $errorArray, 'Username')) {
    $usernameVariable = trim($usernameVariable);
    if (str_contains($usernameVariable, ' ')) {
      array_push($errorArray, "Username can't contain spaces");
    }
  }
  return $errorArray;
}
// returns an error of the arrays for an admin-entered origin/destination name
function originDestinationErrorArray(string $originDestinationVariable, string $fieldName) {
  $errorArray = [];
  // make sure name's not blank
  pushErrorIfBlank($originDestinationVariable, $errorArray, $fieldName);
  // make sure name isn't longer than 255 characters
  if (strlen($originDestinationVariable) > 255) {
    array_push($errorArray, "$fieldName can only be a maximum of 255 characters long");
  }
  return $errorArray;
}

// establish mySQL connection via PDO
$pdo = new PDO("mysql:host=localhost;dbname=flightbooking", 'flightbooking', 'bookflights');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html>

<!-- page header -->

<head>
  <title>Flight Booking</title>
  <link rel="stylesheet" href="style.css">
</head>

<!-- common top bar for all pages -->

<body>
  <a href="index.php">
    <h1>Flight Booking</h1>
  </a>
  <hr>