<?php

// require header template
require('misc/header.php');

// make sure that the given id is actually a real id of a flight
$stmt = $pdo->prepare('SELECT * FROM flights WHERE id=:flightId');
$stmt->execute(['flightId' => $_GET['flightId']]);
if ($stmt->rowCount() == 0) {
  // if flight id is unknown, redirect to home page
  header('Location: index.php');
}

// set global form and seat info variables and error arrays
$seat = $row = $column = $firstName = $lastName = $availabilityInfo = '';
$seatErrors = $firstNameErrors = $lastNameErrors = [];

// form action
if (isset($_POST['bookButton'])) {
  // make sure a seat is selected
  if (!isset($_POST['seat'])) {
    // if seat isn't selected: push seat selection requirement notice
    array_push($seatErrors, 'Seat selection is required');
  } else {
    // separate seat row and column and store them
    $seat = strlen($_POST['seat']) > 2 ? chunk_split($_POST['seat'], 2, ' ') : chunk_split($_POST['seat'], 1, ' ');
    $row = strtok($seat, ' ');
    $column = strtok(' ');
  }

  // store first name
  $firstName = $_POST['firstName'];
  // push any first name errors to first name error array
  $firstNameErrors = nameErrorArray($firstName, 'First name');

  // store last name
  $lastName = $_POST['lastName'];
  // push any last name errors to last name error array
  $lastNameErrors = nameErrorArray($lastName, 'Last name');

  // if there's no errors: begin process to register the booking
  if (sizeof($seatErrors) == 0 && sizeof($firstNameErrors) == 0 && sizeof($lastNameErrors) == 0) {
    // format first and last name
    $firstName = formatNameOriginDestination($firstName);
    $lastName = formatNameOriginDestination($lastName);

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

// code for seat selector:
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
for ($seatRow = 1; $seatRow < ($availabilityInfo->number_of_rows + 1); $seatRow++) {
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



?>

<!-- seat selector: -->
<div class="seatSelection">
  <p class="seatSelection">Select a seat from the following availablities:</p>
  <?php echoErrors($seatErrors); ?>
  <form action="<?= htmlspecialchars("{$_SERVER['PHP_SELF']}?flightId={$_GET['flightId']}") ?>" method="post">
    <!-- put in a radio input for each seat, faded out if the seat is already booked -->
    <?= echoSeatSelector($availabilityInfo, $bookings, $row, $column) ?>
    <hr>
    <!-- personal info input: -->
    <?php echoNameField('First name', 'firstName', $firstNameErrors); ?>
    <?php echoNameField('Last name', 'lastName', $lastNameErrors); ?>
    <hr>
    <input type="submit" value="Book" name="bookButton">
  </form>
</div>

<!-- footer -->
<?php require('misc/footer.php'); ?>