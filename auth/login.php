<?php
session_start();
include("../php/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $_SESSION['error'] = "ایمیل و رمز عبور الزامی است.";
    header("Location: ../registerlogin.php");
    exit;
  }

  // گرفتن اطلاعات کاربر
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['fullname'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email']; 

    header("Location: ../index.php");
    exit;
  } else {
    $_SESSION['error'] = "ایمیل یا رمز عبور اشتباه است.";
    header("Location: ../registerlogin.php");
    exit;
  }
}
?>