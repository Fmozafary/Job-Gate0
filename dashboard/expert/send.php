<?php
ob_clean(); // حذف هر خروجی اضافی قبل
header("Content-Type: application/json; charset=utf-8");
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'expert' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status' => 'error', 'message' => 'دسترسی غیرمجاز']);
  exit;
}

$resume_id = $_POST['resume_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$resume_id || $message === '') {
  echo json_encode(['status' => 'error', 'message' => 'پیام خالی است یا رزومه انتخاب نشده']);
  exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM resumes WHERE id = ?");
$stmt->execute([$resume_id]);
$resumeUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resumeUser) {
  echo json_encode(['status' => 'error', 'message' => 'رزومه پیدا نشد']);
  exit;
}

$user_id = $resumeUser['user_id'];

$stmt = $pdo->prepare("SELECT expert_id FROM user_expert_map WHERE user_id = ?");
$stmt->execute([$user_id]);
$map = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$map || $map['expert_id'] != $_SESSION['user_id']) {
  echo json_encode(['status' => 'error', 'message' => 'این رزومه به شما اختصاص ندارد']);
  exit;

  
}


$stmt = $pdo->prepare("INSERT INTO messages (resume_id, sender_role, sender_id, message) VALUES (?, 'expert', ?, ?)");
$stmt->execute([$resume_id, $_SESSION['user_id'], $message]);


$stmt = $pdo->prepare("UPDATE resumes SET has_new_message = 1 WHERE id = ?");
$stmt->execute([$resume_id]);

echo json_encode(['status' => 'success']);
exit;