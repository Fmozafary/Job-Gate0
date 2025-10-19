<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../../auth/login.php");
  exit;
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!$current || !$new || !$confirm) {
  $_SESSION['flash'] = ['type' => 'error', 'message' => 'همه فیلدها الزامی است.'];
  header("Location: index.php");
  exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !password_verify($current, $user['password'])) {
  $_SESSION['flash'] = ['type' => 'error', 'message' => 'رمز فعلی نادرست است.'];
  header("Location: index.php");
  exit;
}

if ($new !== $confirm) {
  $_SESSION['flash'] = ['type' => 'error', 'message' => 'رمز جدید با تکرار آن مطابقت ندارد.'];
  header("Location: index.php");
  exit;
}

if (password_verify($new, $user['password'])) {
  $_SESSION['flash'] = ['type' => 'error', 'message' => 'رمز جدید نباید با رمز فعلی یکسان باشد.'];
  header("Location: index.php");
  exit;
}

$new_hash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$new_hash, $_SESSION['user_id']]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'رمز عبور با موفقیت تغییر یافت.'];
header("Location: index.php");
exit;