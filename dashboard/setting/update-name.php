<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../../auth/login.php");
  exit;
}

$fullname = $_POST['fullname'] ?? null;
$phone = $_POST['phone'] ?? null;

if (!$fullname || !$phone) {
  die("نام کامل و شماره تماس الزامی است.");
}

$stmt = $pdo->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
$stmt->execute([$fullname, $phone, $_SESSION['user_id']]);

// برای به‌روزرسانی خوشامدگویی در داشبورد
$_SESSION['user_name'] = $fullname;

header("Location: index.php");

$_SESSION['flash'] = [
  'type' => 'success', // یا 'error'
  'message' => 'نام و رمز عبور با موفقیت بروزرسانی شدند '
];
header("Location: index.php");
exit;
