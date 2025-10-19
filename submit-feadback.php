<?php
session_start();
include("php/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $message = trim($_POST['message']);

  if ($fullname && $email && $message) {
    $stmt = $pdo->prepare("INSERT INTO feedback (fullname, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$fullname, $email, $message]);
    $_SESSION['feedback_success'] = true;
  }
}

header("Location: contactUS.php#feedback");
exit;