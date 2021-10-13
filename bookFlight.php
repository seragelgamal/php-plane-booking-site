<?php

// require header template
require('templates/header.php');

// set global array
$seatsBooked = [];
for ($i = 0; $i < 30; $i++) {
  array_push($seatsBooked, ['A', 'B', 'C', 'D', 'E', 'F']);
}

// get seats available for flight selected
if (isset($_GET['flightId'])) {
  $stmt = $pdo->query("SELECT * FROM flight_bookings WHERE flight_id = {$_GET['flightId']}");
  $bookings = $stmt->fetchAll();
}

// form action
// if (isset($_POST['bookButton'])) {
//   filter_var($_POST)
// }

?>

<div class="seatSelection">
  <?php foreach ($bookings as $booking) {
    unset($seatsBooked[$booking->row])
    for ($row = 0; $row < sizeof($seatsBooked); $row++) {
      if ($booking->row)
    }
  } ?>
  <p>Select a row:</p>
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
    <select name="row">
      <option value="1">1</option>
    </select>
</div>
<hr>
<div class="personalInfo">
  <p>First name: <input type="text" name="firstName"></p>
  <p>Last name: <input type="text" name="lastName"></p>
</div>
<hr>
<input type="submit" value="Book" name="bookButton">
</form>

<?php require('templates/footer.php'); ?>