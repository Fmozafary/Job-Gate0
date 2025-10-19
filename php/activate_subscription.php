<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit;
}

$user_id = $_SESSION['user_id'];
$expire_date = date('Y-m-d H:i:s', strtotime('+1 month'));

$stmt = $pdo->prepare("UPDATE users SET subscription = 'paid', is_premium = 1, subscription_expires_at = ? WHERE id = ?");
$stmt->execute([$expire_date, $user_id]);

echo "اشتراک فعال شد";