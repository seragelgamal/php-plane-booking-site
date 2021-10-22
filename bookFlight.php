<?php

// require header template
require('templates/header.php');


// set global form variables
$seat = $row = $column = $firstName = $lastname = '';
$firstNameErrors = $lastNameErrors = [];

// form action
if (isset($_POST['bookButton'])) {
  

  // separate seat row and column and store them
  $seat = strlen($_POST['seat']) > 2 ? chunk_split($_POST['seat'], 2, ' ') : chunk_split($_POST['seat'], 1, ' ');
  $row = strtok($seat, ' ');
  $column = strtok(' ');

  // validate first and last name
  $firstName = $_POST['firstName'];
  if (preg_match('~[0-9]~', $firstName)) {
    array_push($firstNameErrors, 'First name can only contain letters');
  }
  if (strlen($firstName) > 255) {
    array_push($firstNameErrors, 'First name can only be a maximum of 255 characters long');
  }

  $lastName = $_POST['lastName'];
  if (preg_match('~[0-9]~', $lastName)) {
    array_push($lastNameErrors, 'Last name can only contain letters');
  }
  if (strlen($lastName) > 255) {
    array_push($lastNameErrors, 'Last name can only be a maximum of 255 characters long');
  }

  if (sizeof($firstNameErrors) == 0 && sizeof($lastNameErrors) == 0) {
    // format first and last name
    $firstName = trim($firstName);
    $firstName = ucfirst($firstName);

    $lastName = trim($lastName);
    $lastName = ucfirst($lastName);

    // push name and seat to server
    $stmt = $pdo->prepare('INSERT INTO flight_bookings(first_name, last_name, flight_id, seat_row, seat_column) VALUES(:flightId, :firstName, :lastName, :seatRow, :seatColumn)');
    $stmt->execute(['firstName' => $firstName, 'lastName' => $lastName, 'flightId' => $_GET['flightId'], 'seatRow' => $row, 'seatColumn' => $column]);
  }
}

// get seats available for flight selected
if (isset($_GET['flightId'])) {
  $stmt = $pdo->query("SELECT * FROM flight_bookings WHERE flight_id = {$_GET['flightId']}");
  $bookings = $stmt->fetchAll();
}

// set global array '$seats' to store all possible seats
$columnLegend = ['A', 'B', 'C', 'D', 'E', 'F'];
$seats = [];
for ($row = 1; $row < 31; $row++) {
  for ($column = 0; $column < 6; $column++) {
    array_push($seats, $row . $columnLegend[$column]);
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
  <p>Select a seat from the following availablities:</p>
  <form action="<?= htmlspecialchars("{$_SERVER['PHP_SELF']}?flightId={$_GET['flightId']}") ?>" method="post">
    <select name="seat">
      <?php foreach ($seats as $seat) { ?>
        <option value="<?= $seat ?>"<?php if ((isset($row) && isset($column)) && $seat == "$row$column") {echo('selected');} ?>><?= $seat ?></option>
      <?php } ?>
    </select>
    <hr>
    <p>First name: <input type="text" name="firstName"></p>
    <p><?php foreach ($firstNameErrors as $error) {
          echo ("$error <br>");
        } ?></p>
    <p>Last name: <input type="text" name="lastName"></p>
    <p><?php foreach ($lastNameErrors as $error) {
          echo ("$error <br>");
        } ?></p>
    <hr>
    <input type="submit" value="Book" name="bookButton">
  </form>
</div>

<?php require('templates/footer.php'); ?>