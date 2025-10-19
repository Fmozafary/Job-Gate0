<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? '');
    $phone = trim($_POST["phone"] ?? '');
    $subject = trim($_POST["subject"] ?? '');

    if ($fullname && $phone) {
        $stmt = $pdo->prepare("INSERT INTO call_requests (fullname, phone, subject) VALUES (?, ?, ?)");
        $stmt->execute([$fullname, $phone, $subject]);

        header("Location: ../contactUS.php?call_success=1");
        exit;
    } else {
        header("Location: ../contactUS.php?call_error=1");
        exit;
    }
} else {
    header("Location: ../contactUS.php");
    exit;
}