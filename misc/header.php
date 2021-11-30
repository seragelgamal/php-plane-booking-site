<?php

// FUNCTIONS:

// multi-use functions:
function pushErrorIfBlank(string $inputString, array &$errorArray, string $fieldName) {
  if ($inputString == '') {
    array_push($errorArray, "$fieldName is required");
    return true;
  } else {
    return false;
  }
}

// document-specific functions:
// index.php:
// import sort algorithm library
require('sortAlgorithms.php');
// origin/destination filter function: pushes values from array of all routes that match with the specified location filter (either 'origin' or 'destination')
function originDestinationFilter(array $totalRouteArray, string $filterLocation, string $formValue, array &$filteredRouteArray) {
  foreach ($totalRouteArray as $route) {
    if ($route->$filterLocation == $formValue) {
      array_push($filteredRouteArray, $route);
    }
  }
}
// price filter function: takes value with specified index out of the specified array and re-keys the array
function priceFilter(array &$array, int $i) {
  unset($array[$i]);
  $array = array_values($array);
}
// echoes a price input field with the specified POST field name to input minimum and maximum price
function echoPriceField(string $POSTfieldName, string $placeholder = '') { ?>
  $<input type="number" name=<?= $POSTfieldName ?> min="0" placeholder="<?= $placeholder ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>>
<?php }
// echoes an origin/destination filter with the specified POST field name to input origin/destination filters
function echoOriginDestinationDropdown(string $POSTfieldName, array $totalRouteArray) { ?>
  <select name=<?= $POSTfieldName ?>>
    <option value="">All <?= $POSTfieldName ?>s</option>
    <?php $originDestinationArray = [];
    foreach ($totalRouteArray as $route) {
      if (!in_array($route->$POSTfieldName, $originDestinationArray)) {
        array_push($originDestinationArray, $route->$POSTfieldName); ?>
        <option <?php if (isset($_POST[$POSTfieldName]) && $_POST[$POSTfieldName] == $route->$POSTfieldName) { ?> selected <?php } ?>><?= $route->$POSTfieldName ?></option>
    <?php }
    } ?>
  </select>
<?php }
// sorts an array of similar objects by a given property
function sortElementsByProperty(array &$array, string $property) {
  $arrayOfProperties = [];
  foreach ($array as $element) {
    array_push($arrayOfProperties, $element->$property);
  }
  insertionSort($arrayOfProperties);
  $copyOfArray = $array;
  $array = [];
  foreach ($arrayOfProperties as $elementProperty) {
    foreach ($copyOfArray as $element) {
      if ($element->$property == $elementProperty && !in_array($element, $array)) {
        array_push($array, $element);
      }
    }
  }
}
// echoes a radio input with a given POST field name, value, and label to choose sort mode
function echoRadioInput(string $POSTfieldName, string $value, string $label) { ?>
  <input type="radio" name="<?= $POSTfieldName ?>" value="<?= $value ?>" <?php if ($_POST[$POSTfieldName] == $value) { ?> checked <?php } ?>>
<?php echo ($label);
}

// bookFlight.php:
// returns an array of the errors for a user-entered name
function nameErrorArray(string $nameVariable, string $fieldName) {
  $errorArray = [];
  // make sure name's not blank
  pushErrorIfBlank($nameVariable, $errorArray, $fieldName);
  // make sure name's only letters
  if (preg_match('~[0-9]~', $nameVariable)) {
    array_push($errorArray, "$fieldName can only contain letters");
  }
  // make sure name isn't longer than 255 characters
  if (strlen($nameVariable) > 255) {
    array_push($errorArray, "$fieldName can only be a maximum of 255 characters long");
  }
  return $errorArray;
}
// returns the properly-formatted version of a user-entered name
function formatName(string $nameVariable) {
  // take out spaces from the start and end of the name if they exist
  $nameVariable = trim($nameVariable);
  // convert all letters to lower case
  $nameVariable = strtolower($nameVariable);
  // set first letter to uppercase
  $nameVariable = ucfirst($nameVariable);
  return $nameVariable;
}
// echoes errors from a specified error array with formatting
function echoErrors(array $errorArray) { ?>
  <p class="errors"><?php foreach ($errorArray as $error) {
                      echo ("$error <br>");
                    } ?></p>
<?php }
// echoes a first/last name field with a given label for the user and POST superglobal index name, as well as the errors from a given error array with formatting
function echoNameField(string $userFieldName, string $POSTfieldName, array $errorArray) { ?>
  <p><?= $userFieldName ?>: <input type="text" name="<?= $POSTfieldName ?>" <?php if (isset($_POST[$POSTfieldName])) { ?> value="<?= $_POST[$POSTfieldName] ?>" <?php } ?>></p>
<?php echoErrors($errorArray);
}

// adminLogin.php
// return an array of all the errors found for an admin-entered username
function usernameErrorArray(string $usernameVariable) {
  $errorArray = [];
  if (!pushErrorIfBlank($usernameVariable, $errorArray, 'Username')) {
    $usernameVariable = trim($usernameVariable);
    if (str_contains($usernameVariable, ' ')) {
      array_push($errorArray, "Username can't contain spaces");
    }
  }
  return $errorArray;
}
// return an array of all the errors found for an admin-entered password
function pwdErrorArray(string $pwdVariable) {
  $errorArray = [];
  if (!pushErrorIfBlank($pwdVariable, $errorArray, 'Password')) {
    if ($pwdVariable[0] == ' ' || $pwdVariable[strlen($pwdVariable) - 1] == ' ') {
      array_push($errorArray, "Password can't start or end with a space");
    }
  }
  return $errorArray;
}

// establish mySQL connection via PDO
$pdo = new PDO("mysql:host=localhost;dbname=flightbooking", 'flightbooking', 'bookflights');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html>

<!-- page header -->

<head>
  <title>Flight Booking</title>
  <link rel="stylesheet" href="style.css">
</head>

<!-- common top bar for all pages -->

<body>
  <a href="index.php">
    <h1>Flight Booking</h1>
  </a>
  <hr>