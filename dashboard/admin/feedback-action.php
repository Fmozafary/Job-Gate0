<?php
include("../../php/db.php");

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id) exit;

if ($action === "approve") {
  // بررسی تعداد تاییدشده‌ها
  $approved = $pdo->query("SELECT id FROM feedback WHERE is_approved = 1 ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);

  if (count($approved) < 3) {
    // اگر کمتر از ۳ تاست، فقط تایید کن
    $stmt = $pdo->prepare("UPDATE feedback SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$id]);
  } else {
    // حذف قدیمی‌ترین کارت
    $oldest_id = $approved[0]['id'];
    $pdo->prepare("UPDATE feedback SET is_approved = 0 WHERE id = ?")->execute([$oldest_id]);

    // تایید جدید
    $pdo->prepare("UPDATE feedback SET is_approved = 1 WHERE id = ?")->execute([$id]);
  }

  echo "approved";
}

elseif ($action === "reject") {
  $pdo->prepare("DELETE FROM feedback WHERE id = ?")->execute([$id]);
  echo "deleted";
}