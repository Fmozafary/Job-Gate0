<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
include("../../php/db.php");

$id = $_GET['id'] ?? null;
if (!$id) exit("شناسه نامعتبر");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $role = $_POST['role'];
  $subscription = $_POST['subscription'];
  $expires = $_POST['subscription_expires_at'];

  $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, role = ?, subscription = ?, subscription_expires_at = ? WHERE id = ?");
  $stmt->execute([$fullname, $email, $role, $subscription, $expires, $id]);
  header("Location: users.php");
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) exit("کاربر پیدا نشد");
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ویرایش کاربر</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
  <style>
    input:disabled, select:disabled {
      background-color: #f3f4f6;
      cursor: not-allowed;
    }
  </style>
</head>
<body class="bg-gradient-to-tr from-blue-50 to-white min-h-screen flex items-center justify-center p-6 font-sans">
  <div class="bg-white shadow-2xl rounded-3xl p-8 max-w-2xl w-full">
    <div class="text-center mb-6">
      <img src="../../uploads/<?= $user['photo'] ?? 'default.png' ?>" alt="Avatar" class="mx-auto w-28 h-28 rounded-full border-4 border-blue-200 shadow">
      <h1 class="text-2xl font-bold text-blue-700 mt-4">ویرایش اطلاعات کاربر</h1>
    </div>
    <form method="post" class="grid grid-cols-1 gap-4">
      <div>
        <label class="block font-semibold mb-1">نام و نام خانوادگی</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" class="form-input w-full">
      </div>
      <div>
        <label class="block font-semibold mb-1">ایمیل</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-input w-full">
      </div>
      <div>
        <label class="block font-semibold mb-1">نقش</label>
        <select name="role" class="form-input w-full">
          <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>کاربر</option>
          <option value="expert" <?= $user['role'] === 'expert' ? 'selected' : '' ?>>کارشناس</option>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>مدیر</option>
        </select>
      </div>
      <div>
        <label class="block font-semibold mb-1">نوع اشتراک</label>
        <select name="subscription" id="subscription" class="form-input w-full" onchange="toggleDateInput()">
          <option value="free" <?= $user['subscription'] === 'free' ? 'selected' : '' ?>>رایگان</option>
          <option value="paid" <?= $user['subscription'] === 'paid' ? 'selected' : '' ?>>اشتراکی</option>
        </select>
      </div>
      <div>
        <label class="block font-semibold mb-1">تاریخ پایان اشتراک</label>
        <input type="datetime-local" name="subscription_expires_at" id="expires_at" 
          value="<?= $user['subscription_expires_at'] ? date('Y-m-d\TH:i', strtotime($user['subscription_expires_at'])) : '' ?>" 
          min="<?= date('Y-m-d\TH:i') ?>" 
          class="form-input w-full">
      </div>
      <div class="text-center mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">ثبت تغییرات</button>
        <a href="users.php" class="text-sm text-gray-600 hover:underline ml-4">بازگشت</a>
      </div>
    </form>
  </div>  <script>
    function toggleDateInput() {
      const sub = document.getElementById('subscription');
      const expires = document.getElementById('expires_at');
      if (sub.value === 'paid') {
        expires.removeAttribute('disabled');
      } else {
        expires.setAttribute('disabled', 'disabled');
      }
    }
    // اجرای اولیه برای وضعیت فعلی کاربر
    window.onload = toggleDateInput;
  </script></body>
</html>