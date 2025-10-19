<?php
session_start();
require_once("../../vendor/autoload.php");

include("../../php/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
  die("دسترسی غیرمجاز.");
}

$resume_id = $_GET['id'];

// گرفتن اطلاعات رزومه
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND email = ?");
$stmt->execute([$resume_id, $_SESSION['user_email']]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resume) {
  die("رزومه پیدا نشد.");
}

$template_id = $resume['template_id'] ?? 1;
$template_file = "../../resume/pdf-templates/pdf-template-" . $template_id . ".php";

if (!file_exists($template_file)) {
  die("قالب رزومه یافت نشد.");
}

// گرفتن سوابق کاری
$stmt = $pdo->prepare("SELECT * FROM experiences WHERE resume_id = ?");
$stmt->execute([$resume_id]);
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// گرفتن دوره‌ها
$stmt = $pdo->prepare("SELECT * FROM courses WHERE resume_id = ?");
$stmt->execute([$resume_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// بارگذاری قالب و خروجی HTML رزومه
ob_start();
include($template_file); // داخلش از متغیر $resume و $experiences و $courses استفاده می‌کنیم
$html = ob_get_clean();

// ساخت فایل PDF
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output("resume-{$resume_id}.pdf", 'D'); // دانلود مستقیم
exit;