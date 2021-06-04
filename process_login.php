<?php
include 'dbh.php';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $checkAccount = $mysqli->query("SELECT * FROM accounts WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($checkAccount) > 0) {
        $newAccoount = $checkAccount->fetch_array();

        $_SESSION['username'] = $newAccoount['username'];
        $_SESSION['account_id'] = $newAccoount['id'];
        header("location: index.php");
    }
    else
        {
        $_SESSION['logInError'] = "Credentials do not match our records. Login failed. Please try again";
        header("location: login.php");
    }
}
?>