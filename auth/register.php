<?php
session_start();
include("../php/db.php");

// فعال‌سازی نمایش خطاها برای بررسی بهتر (فقط در حالت توسعه)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// گرفتن اطلاعات از فرم
$fullname = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

// اعتبارسنجی اولیه
if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
  $_SESSION['error'] = "لطفاً تمام فیلدها را پر کنید.";
  header("Location: ../registerlogin.php");
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['error'] = "ایمیل وارد شده معتبر نیست.";
  header("Location: ../registerlogin.php");
  exit;
}

if ($password !== $confirm) {
  $_SESSION['error'] = "رمز عبور و تکرار آن مطابقت ندارند.";
  header("Location: ../registerlogin.php");
  exit;
}

// چک ایمیل تکراری
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
  $_SESSION['error'] = "این ایمیل قبلاً ثبت شده است.";
  header("Location: ../registerlogin.php");
  exit;
}

// هش کردن رمز عبور
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// درج در دیتابیس
$stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())");
$stmt->execute([$fullname, $email, $phone, $hashed_password]);

$_SESSION['success'] = "ثبت‌نام با موفقیت انجام شد. حالا وارد شوید.";
header("Location: ../registerlogin.php");
exit;
?>