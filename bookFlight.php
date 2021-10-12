<?php

// require header template
require('templates/header.php');

// get seats available for flight selected
if (isset($_GET['flightId'])) {
  $stmt = $pdo->query("SELECT * FROM flight_bookings WHERE flight_id = {$_GET['flightId']}");
  $stmt->fetchAll();
  // echo ("flight id is {$_GET['flightId']}");
}

// form action
// if (isset($_POST['bookButton'])) {
//   filter_var($_POST)
// }

?>

<div class="seatSelection">
  <p>Select a seat:</p>
</div>
<hr>
<div class="personalInfo">
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
    <p>First name: <input type="text" name="firstName"></p>
    <p>Last name: <input type="text" name="lastName"></p>
</div>
<hr>
<input type="submit" value="Book" name="bookButton">
</form>

<?php require('templates/footer.php'); ?>