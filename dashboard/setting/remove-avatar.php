<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../../auth/login.php");
  exit;
}

$stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user && $user['avatar']) {
  $file = "../../assets/avatars/" . $user['avatar'];
  if (file_exists($file)) {
    unlink($file);
  }

  $stmt = $pdo->prepare("UPDATE users SET avatar = NULL WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);

  $_SESSION['flash'] = ['type' => 'success', 'message' => 'آواتار شما حذف شد.'];
} else {
  $_SESSION['flash'] = ['type' => 'error', 'message' => 'آواتاری برای حذف یافت نشد.'];
}

header("Location: index.php");
exit;