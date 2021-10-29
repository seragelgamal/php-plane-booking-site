<?php

// require header template
require('templates/header.php');

// get schedule for route clicked and record origin and destination
if (isset($_GET['routeId'])) {
  $stmt = $pdo->query("SELECT * FROM flights WHERE route_id = {$_GET['routeId']} && capacity - seats_booked > 0");
  $flights = $stmt->fetchAll();

  $stmt = $pdo->query("SELECT * FROM routes WHERE id = {$_GET['routeId']}");
  $route = $stmt->fetch();
} else {
  // if no route is selected, red
}

print_r($route);

// form action
if (isset($_POST['bookButton'])) {
  header("Location: bookFlight.php?flightId={$_POST['flightId']}");
}

?>

<?php if (isset($route->origin) && isset($route->destination)) { ?>
  <h2><?= $route->origin ?> to <?= $route->destination ?></h2>
<?php } ?>
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
  <h3>Error: the requested flight was not found</h3>
  <a href="index.php"><button>Return to homepage</button></a>
<?php } ?>

<?php require('templates/footer.php'); ?>