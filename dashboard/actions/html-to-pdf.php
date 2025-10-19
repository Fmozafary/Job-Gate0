<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
  die("دسترسی غیرمجاز");
}

$resume_id = intval($_GET['id']);
$url = "http://localhost/jobgate/resume/view.php?id=" . $resume_id;

// مسیر ذخیره موقت فایل PDF
$outputPath = __DIR__ . "/resume-$resume_id.pdf";

// اجرای wkhtmltopdf
$cmd = "wkhtmltopdf \"$url\" \"$outputPath\"";
exec($cmd, $output, $resultCode);

if ($resultCode !== 0) {
  die("تبدیل به PDF انجام نشد.");
}

// ارسال فایل برای دانلود
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=resume-$resume_id.pdf");
readfile($outputPath);

// حذف فایل موقت
unlink($outputPath);
exit;