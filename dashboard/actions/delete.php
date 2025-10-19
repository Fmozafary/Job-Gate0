<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
  die("دسترسی غیرمجاز.");
}

$resume_id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM resumes WHERE id = ? AND user_id = ?");
$stmt->execute([$resume_id, $_SESSION['user_id']]);

header("Location: ../user.php");
exit;