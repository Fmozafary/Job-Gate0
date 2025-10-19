<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
  header("Location: ../auth/login.php");
  exit;
}

switch ($_SESSION['user_role']) {
  case 'admin':
    header("Location: admin.php");
    break;
  case 'expert':
    header("Location: expert.php");
    break;
  default:
    header("Location: user.php");
    break;
}
exit;