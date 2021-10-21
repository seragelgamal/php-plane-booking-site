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

  $firstNameErrors = $lastNameErrors = [];

// form action
if (isset($_POST['bookButton'])) {
  $seat = $row = $column = $firstName = $lastname = '';

  // validate first and last name
  $firstName = $_POST['firstName'];
  // preg_match()

  // if (gettype($firstName) != 'string') {
  //   echo('not a string');
  // }
  // for ($letter = 0; $letter < strlen($firstName); $letter++) {
  //   if (gettype($firstName[$letter]) != 'string') {
  //     array_push($firstNameErrors, 'First name can only contain letters');
  //   }
  // }
  trim($firstName);
  ucfirst($firstName);
  if (strlen($firstName) > 255) {
    array_push($firstNameErrors, 'First name can only be a maximum of 255 characters long');
  }

  $lastName = $_POST['lastName'];
  for ($letter = 0; $letter < strlen($lastName); $letter++) {
    if (gettype($lastName[$letter]) != 'string') {
      array_push($lastNameErrors, 'Last name can only contain letters');
    }
  }
  trim($lastName);
  ucfirst($lastName);
  if (strlen($lastName) > 255) {
    array_push($lastNameErrors, 'Last name can only be a maximum of 255 characters long');
  }

  if (sizeof($firstNameErrors) > 0 || sizeof($lastNameErrors) > 0) {
    print_r($firstNameErrors);
    print_r($lastNameErrors);
  }

  // separate seat row and column
  $seat = strlen($_POST['seat']) > 2 ? chunk_split($_POST['seat'], 2, ' ') : chunk_split($_POST['seat'], 1, ' ');
  $row = strtok($seat, ' ');
  $column = strtok(' ');
}

?>

<div class="seatSelection">
  <p>Select a seat from the following availablities:</p>
  <form action="<?= htmlspecialchars("{$_SERVER['PHP_SELF']}?flightId={$_GET['flightId']}") ?>" method="post">
    <select name="seat">
      <?php foreach ($seats as $seat) { ?>
        <option value="<?= $seat ?>"><?= $seat ?></option>
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