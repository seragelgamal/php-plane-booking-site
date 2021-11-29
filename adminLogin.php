<?php

// Require header template
require('misc/header.php');

$loginErrors = [];
$usernameErrors = [];
$pwdErrors = [];


function usernameErrorArray(string $usernameVariable) {
  $errorArray = [];
  pushErrorIfBlank($usernameVariable, $errorArray, 'Username');
  $usernameVariable = trim($usernameVariable);
  if (str_contains($usernameVariable, ' ')) {
    array_push($usernameErrors, "username can't contain spaces");
  }
  return $errorArray;
}

function pwdErrorArray(string $pwdVariable) {
  $errorArray = [];
  pushErrorIfBlank($pwdVariable, $errorArray, 'Password');

}

// form action
if (isset($_POST['logIn'])) {
  $username = $_POST['username'];
  $usernameErrors = usernameErrorArray($username);

  $pwd = $_POST['pwd'];
  $pwd = trim($pwd);

  $pwd = $_POST['pwd'];
  pushErrorIfBlank($pwd, $pwdErrors, 'Password');

  if (sizeof($usernameErrors) != 0 && sizeof($pwdErrors) != 0) {
    
  }
  $stmt = $pdo->prepare("SELECT 1 FROM admin_credentials WHERE username = :username && pwd = :pwd");
  $stmt->execute(['username' => $_POST['username'], 'pwd' => $_POST['pwd']]);
  if ($stmt->rowCount() == 0) {
    array_push($loginErrors, 'Incorrect username or password');
  } else {
    echo ("successful login");
  }
}
?>

<h2>Admin Login</h2>
<?php echoErrors($loginErrors); ?>
<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
  <p>Username: <input type="text" name="username"></p>
  <?php echoErrors($usernameErrors); ?>
  <p>Password: <input type="password" name="pwd"></p>
  <?php echoErrors($pwdErrors); ?>
  <p><input type="submit" name="logIn" value="Sign in"></p>
</form>