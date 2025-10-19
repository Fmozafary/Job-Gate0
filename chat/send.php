<?php
session_start();
include("../php/db.php");

header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status' => 'error', 'message' => 'دسترسی غیرمجاز']);
  exit;
}

$resume_id = $_POST['resume_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$resume_id || $message === '') {
  echo json_encode(['status' => 'error', 'message' => 'پیام خالی است یا رزومه انتخاب نشده']);
  exit;
}

// بررسی اینکه رزومه متعلق به کاربر هست یا نه
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->execute([$resume_id, $_SESSION['user_id']]);
$resume = $stmt->fetch();

if (!$resume) {
  echo json_encode(['status' => 'error', 'message' => 'رزومه یافت نشد یا متعلق به شما نیست']);
  exit;
}

// ثبت پیام
$stmt = $pdo->prepare("INSERT INTO messages (resume_id, sender_role, sender_id, message) VALUES (?, 'user', ?, ?)");
$stmt->execute([$resume_id, $_SESSION['user_id'], $message]);



// بررسی وجود مپ بین کاربر و کارشناس
$stmt = $pdo->prepare("SELECT * FROM user_expert_map WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$existingMap = $stmt->fetch();

if (!$existingMap) {
    // فقط یک کارشناس داریم، پس همونو انتخاب می‌کنیم
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'expert' LIMIT 1");
    $stmt->execute();
    $expert = $stmt->fetch();

    if ($expert) {
        $stmt = $pdo->prepare("INSERT INTO user_expert_map (user_id, expert_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $expert['id']]);
    }
}



// اگر فرستنده کاربر بود، پیام جدید ثبت شود
// علامت‌گذاری رزومه با پیام جدید
$stmt = $pdo->prepare("UPDATE resumes SET has_new_message = 1 WHERE id = ?");
$stmt->execute([$resume_id]);

// تنظیم فلگ has_new_message = 1
$stmt = $pdo->prepare("UPDATE resumes SET has_new_message = 1 WHERE id = ?");
$stmt->execute([$resume_id]);

echo json_encode(['status' => 'success']);