<?php
include("../../php/db.php");
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM expert_requests WHERE id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();
if ($request) {
  $pdo->prepare("UPDATE users SET role = 'expert' WHERE id = ?")->execute([$request['user_id']]);
  $pdo->prepare("UPDATE expert_requests SET status = 'approved' WHERE id = ?")->execute([$id]);
}
header("Location: ../admin/experts.php");