<?php
session_start();

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

if ($email === "admin@demo.local" && $password === "1234") {
  $_SESSION["auth"] = true;
  $_SESSION["email"] = $email;
  header("Location: dashboard.php");
  exit;
}

header("Location: index.html?error=1");
exit;
