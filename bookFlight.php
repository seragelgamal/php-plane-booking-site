<?php

// require header template
require('templates/header.php');

// make sure that the given id is actually a real id of a flight
$stmt = $pdo->prepare('SELECT * FROM flights WHERE id=:flightId');
$stmt->execute(['flightId' => $_GET['flightId']]);
if ($stmt->rowCount() == 0) {
  header('Location: index.php');
}

// set global form and seat info variables
$seat = $row = $column = $firstName = $lastName = '';
$seatErrors = $firstNameErrors = $lastNameErrors = [];
$availabilityInfo = '';

// form action
if (isset($_POST['bookButton'])) {
  // make sure a seat is selected
  if (!isset($_POST['seat'])) {
    array_push($seatErrors, 'Seat selection is required');
  } else {
    // separate seat row and column and store them
    $seat = strlen($_POST['seat']) > 2 ? chunk_split($_POST['seat'], 2, ' ') : chunk_split($_POST['seat'], 1, ' ');
    $row = strtok($seat, ' ');
    $column = strtok(' ');
  }

  // store first name
  $firstName = $_POST['firstName'];
  // make sure first name's not blank
  if ($firstName == '') {
    array_push($firstNameErrors, 'First name is required');
  }
  // make sure first name's only letters
  if (preg_match('~[0-9]~', $firstName)) {
    array_push($firstNameErrors, 'First name can only contain letters');
  }
  // make sure first name isn't longer than 255 characters
  if (strlen($firstName) > 255) {
    array_push($firstNameErrors, 'First name can only be a maximum of 255 characters long');
  }

  // store last name
  $lastName = $_POST['lastName'];
  // make sure last name isn't blank
  if ($lastName == '') {
    array_push($lastNameErrors, 'Last name is required');
  }
  // make sure last name is only letters
  if (preg_match('~[0-9]~', $lastName)) {
    array_push($lastNameErrors, 'Last name can only contain letters');
  }

  // make sure last name isn't longer than 255 characters
  if (strlen($lastName) > 255) {
    array_push($lastNameErrors, 'Last name can only be a maximum of 255 characters long');
  }

  // if there's no errors: begin process to register the booking
  if (sizeof($seatErrors) == 0 && sizeof($firstNameErrors) == 0 && sizeof($lastNameErrors) == 0) {

    // format first and last name
    $firstName = trim($firstName);
    $firstName = ucfirst($firstName);

    $lastName = trim($lastName);
    $lastName = ucfirst($lastName);

    // push name and seat to server
    $stmt = $pdo->prepare('INSERT INTO flight_bookings (first_name, last_name, flight_id, seat_row, seat_column) VALUES (:firstName, :lastName, :flightId, :seatRow, :seatColumn)');
    $stmt->execute(['firstName' => $firstName, 'lastName' => $lastName, 'flightId' => $_GET['flightId'], 'seatRow' => $row, 'seatColumn' => $column]);

    // update seats booked in database
    $stmt = $pdo->prepare('UPDATE flights SET seats_booked = seats_booked + 1 WHERE id = :flightId');
    $stmt->execute(['flightId' => $_GET['flightId']]);

    // redirect to thank-you page
    header('Location: thanks.php');
  }
}

// get seat layout for flight selected, as well as booked seats
if (isset($_GET['flightId'])) {
  $stmt = $pdo->query("SELECT number_of_rows, number_of_columns, seats_booked FROM flights WHERE id = {$_GET['flightId']}");
  $availabilityInfo = $stmt->fetch();

  $stmt = $pdo->query("SELECT * FROM flight_bookings WHERE flight_id = {$_GET['flightId']}");
  $bookings = $stmt->fetchAll();
}

// set global array '$seats' to store all possible seats
$columnLegend = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K'];
$seats = [];
for ($seatRow = 1; $seatRow < 31; $seatRow++) {
  for ($seatColumn = 0; $seatColumn < 6; $seatColumn++) {
    array_push($seats, $seatRow . $columnLegend[$seatColumn]);
  }
}

// set global array to store the seats that are already booked
$seatsBooked = [];
foreach ($bookings as $booking) {
  array_push($seatsBooked, $booking->seat_row . $booking->seat_column);
}

// take out booked seats from the $seats array
$seats = array_diff($seats, $seatsBooked);
?>

<div class="seatSelection">
  <p class="seatSelection">Select a seat from the following availablities:</p>
  <p class="errors"><?php foreach ($seatErrors as $error) {
                      echo ("$error <br>");
                    } ?></p>
  <form action="<?= htmlspecialchars("{$_SERVER['PHP_SELF']}?flightId={$_GET['flightId']}") ?>" method="post">
    <?php for ($r = 1; $r < ($availabilityInfo->number_of_rows + 1); $r++) { ?>
      <p><?= $r ?></p>
      <div class="seatRow">
        <div class="seatColumn">
          <?php for ($c = 0; $c < ($availabilityInfo->number_of_columns); $c++) { ?>
            <input type="radio" name="seat" value="<?= "$r{$columnLegend[$c]}" ?>" <?php foreach ($bookings as $booking) {
                                                                                      if ("$r{$columnLegend[$c]}" == "{$booking->seat_row}{$booking->seat_column}") { ?> disabled <?php }
                                                                                                                                                                              }
                                                                                                                                                                              if ("$r{$columnLegend[$c]}" == "$row$column") { ?> checked <?php } ?>><?= $columnLegend[$c] ?>
          <?php } ?>
        </div>

      </div>
    <?php } ?>
    <hr>
    <p>First name: <input type="text" name="firstName" value='<?php if (isset($_POST['firstName'])) {
                                                                echo ($_POST['firstName']);
                                                              } ?>'></p>
    <p class="errors"><?php foreach ($firstNameErrors as $error) {
                        echo ("$error <br>");
                      } ?></p>
    <p>Last name: <input type="text" name="lastName" value='<?php if (isset($_POST['lastName'])) {
                                                              echo ($_POST['lastName']);
                                                            } ?>'></p>
    <p class="errors"><?php foreach ($lastNameErrors as $error) {
                        echo ("$error <br>");
                      } ?></p>
    <hr>
    <input type="submit" value="Book" name="bookButton">
  </form>
</div>

<?php require('templates/footer.php'); ?>