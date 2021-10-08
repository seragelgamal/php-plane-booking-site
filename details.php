<?php
// require header template
require('templates/header.php');

// get schedule for route clicked
if (isset($_GET['routeId'])) {
  $stmt = $pdo->query("SELECT * FROM routeschedules WHERE route_id = {$_GET['routeId']} && capacity - seats_booked > 0");
  $routeSchedule = $stmt->fetchAll();
} else {
}

print_r($routeSchedule);

// form action

?>

<h2><?= $_GET['origin'] ?> - <?= $_GET['destination'] ?></h2>
<?php if (sizeof($routeSchedule) > 0) { ?>
  <h3 class="scheduleHeading">Schedule:</h3>
  <table>
    <tr>
      <th>Date</th>
      <th>Time</th>
      <th>Seats Available</th>
    </tr>
    <?php foreach ($routeSchedule as $flight) { ?>
      <tr>
        <th><?= $flight->date ?></th>
        <th><?= $flight->time ?></th>
        <th><?= $flight->capacity - $flight->seats_booked ?></th>
        <th>
          <form action="details.php" method="post"><input name="<?= $flight->id ?>" type="submit" value="Book"></form>
        </th>
      </tr>
    <?php } ?>
  </table>
<?php } else { ?>
  <h3>No flights available at the moment for this route</h3>
<?php } ?>





<?php require('templates/footer.php'); ?>