<?php

// require header template
require('templates/header.php');

// get seats available for flight selected
if (isset($_GET['flightId'])) {
  $stmt = $pdo->query("SELECT * FROM flight_bookings WHERE flight_id = {$_GET['flightId']}");
  $bookings = $stmt->fetchAll();
}

// set global array to store all possible seats
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
// print_r($seatsBooked);

// take out booked seats from the $seats array
$seats = array_diff($seats, $seatsBooked);

// form action
if (isset($_POST['bookButton'])) {
  $row = $column = $firstName = $lastname = '';
  $firstNameErrors = $lastNameErrors = [];

  // validate first and last name
  $firstName = $_POST['firstName'];
  trim($firstName);
  if (strlen($firstName > 255)) {
    array_push($firstNameErrors, 'First name can only be a maximum of 255 characters long');
  }
  for ($letter = 0; $letter < strlen($firstName); $letter++) {
    if (gettype($firstName[$letter]) != 'string') {
      array_push($firstNameErrors, 'First name can only contain letters');
    }
  }

  $lastName = $_POST['lastName'];
  trim($lastName);
  if (strlen($lastName > 255)) {
    array_push($lastNameErrors, 'Last name can only be a maximum of 255 characters long');
  }
  for ($letter = 0; $letter < strlen($lastName); $letter++) {
    if (gettype($lastName[$letter]) != 'string') {
      array_push($lastNameErrors, 'Last name can only contain letters');
    }
  }

  // separate seat row and column
  // $column = 

  if (sizeof($firstNameErrors) == 0 && sizeof($lastNameErrors) == 0) {
    ucfirst($firstName);
    ucfirst($lastName);
    echo ('form validation success');
  }
}

?>

<div class="seatSelection">
  <p>Select a seat from the following availablities:</p>
  <form action="<?= htmlspecialchars("{$_SERVER['PHP_SELF']}?flightId={$_GET['flightId']}") ?>" method="post">
    <select name="row">
      <?php foreach ($seats as $seat) { ?>
        <option value="<?= $seat ?>"><?= $seat ?></option>
      <?php } ?>
    </select>
    <hr>
    <p>First name: <input type="text" name="firstName"></p>
    <p>Last name: <input type="text" name="lastName"></p>
    <hr>
    <input type="submit" value="Book" name="bookButton">
  </form>
</div>

<?php require('templates/footer.php'); ?>