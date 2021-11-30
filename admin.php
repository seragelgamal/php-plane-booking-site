<?php

require('misc/header.php');

?>

<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
  <h2>Add a trip</h2>
  <h3>A new route will be created if no route already exists with the given origin, destination and price</h3>
  <h4>Add a trip from <input type="text" name="tripAddOrigin" placeholder="Origin">
    to
    <input type="text" name="tripAddDestination" placeholder="Destination">:
</h4>
  <p>Price: <?php echoPriceField('addTripPrice', 'Price'); ?></p>
  <p>Date: <input type="date" name="addTripDate"> Time: <input type="time" name="addTripTime"></p>
  <h4>Aircraft info:</h4>
  <p>Number of rows: <input type="number" name="addTripNumberOfRows" placeholder="rows" min='1' max='90'></p>
  <p>Number of columns: <input type="number" name="addTripNumberOfColumns" placeholder="columns" min='1' max='10'></p>
  <input type="submit" name="submit" value="Add Trip">
  <hr>
  <h2>Modify trip info</h2>
  <hr>
  <h2>Edit passenger list/delete passengers</h2>
  <hr>
  <h2>Edit blacklist</h2>
</form>


<?php require('misc/footer.php'); ?>