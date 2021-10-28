<?php

// require header template
require('templates/header.php');

// get schedule for route clicked
if (isset($_GET['routeId'])) {
  $stmt = $pdo->query("SELECT * FROM flights WHERE route_id = {$_GET['routeId']} && capacity - seats_booked > 0");
  $flights = $stmt->fetchAll();
} else {
}

print_r($flights);

// form action
if (isset($_POST['bookButton'])) {
  header("Location: bookFlight.php?flightId={$_POST['flightId']}");
}

?>

<h2><?= $_GET['origin'] ?> - <?= $_GET['destination'] ?></h2>
<?php if (sizeof($flights) > 0) { ?>
  <h3 class="scheduleHeading">Schedule:</h3>
  <table>
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th>Seats Available</th>
    </tr>
    <?php foreach ($flights as $flight) {
      if ($flight->capacity - $flight->seats_booked > 0) { ?>
        <tr>
          <td><?= $flight->date ?></td>
          <td><?= $flight->time ?></td>
          <td><?= $flight->capacity - $flight->seats_booked ?></td>
          <td>
            <form action="details.php" method="post">
              <input type="submit" name='bookButton' value="Book">
              <input type="hidden" name="flightId" value="<?= $flight->id ?>">
            </form>
          </td>
        </tr>
    <?php }
    } ?>
  </table>
<?php } else { ?>
  <h3>No flights available at the moment for this route</h3>
<?php } ?>

<?php require('templates/footer.php'); ?>