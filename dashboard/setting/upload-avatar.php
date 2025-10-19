<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
  $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
  $allowed = ['jpg', 'jpeg', 'png', 'webp'];

  if (!in_array(strtolower($ext), $allowed)) {
    die("فرمت تصویر مجاز نیست.");
  }

  $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
  $destination = '../../assets/avatars/' . $filename;

  if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->execute([$filename, $user_id]);
    header("Location: index.php?avatar=ok");
    exit;
  } else {
    die("خطا در ذخیره فایل.");
  }
}
$_SESSION['flash'] = [
  'type' => 'success', // یا 'error'
  'message' => 'آواتار اکانت شما با موفقیت بروزرسانی شد'
];
header("Location: index.php");