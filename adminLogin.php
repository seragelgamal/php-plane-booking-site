<?php

// Require header template
require('misc/header.php');

$loginErrors = $usernameErrors = $pwdErrors = [];

// form action
if (isset($_POST['logIn'])) {
  $username = $_POST['username'];
  $usernameErrors = usernameErrorArray($username);

  $pwd = $_POST['pwd'];
  $pwdErrors = pwdErrorArray($pwd);

  if (sizeof($usernameErrors) == 0 && sizeof($pwdErrors) == 0) {
    $username = trim($username);

    // attempt login
    $stmt = $pdo->prepare("SELECT 1 FROM admin_credentials WHERE username = :username && pwd = :pwd");
    $stmt->execute(['username' => $_POST['username'], 'pwd' => $_POST['pwd']]);

    if ($stmt->rowCount() == 0) {
      // if login fails, notify the user
      array_push($loginErrors, 'Unknown username or incorrect password');
    } else {
      // if login is successful: take user to admin page
      header('Location: admin.php');
    }
  }
}
?>

<h2>Admin Login</h2>
<?php echoErrors($loginErrors); ?>
<form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
  <p>Username: <input type="text" name="username" value="<?php if (isset($_POST['username'])) {
                                                            echo ($_POST['username']);
                                                          } ?>"></p>
  <?php echoErrors($usernameErrors); ?>
  <p>Password: <input type="password" name="pwd"></p>
  <?php echoErrors($pwdErrors); ?>
  <p><input type="submit" name="logIn" value="Sign in"></p>
</form>

<?php require('misc/footer.php'); ?>