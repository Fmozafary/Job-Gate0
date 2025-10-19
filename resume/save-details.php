<?php
include("../php/db.php");

// گرفتن آیدی رزومه از فرم
$resume_id = $_POST['resume_id'] ?? null;
if (!$resume_id) {
  die("رزومه‌ای انتخاب نشده است!");
}

// ذخیره سوابق شغلی
if (!empty($_POST['job_title'])) {
  $count = count($_POST['job_title']);
  for ($i = 0; $i < $count; $i++) {
    $title = $_POST['job_title'][$i];
    $company = $_POST['job_company'][$i];
    $from = $_POST['job_from'][$i];
    $to = $_POST['job_to'][$i];
    $desc = $_POST['job_desc'][$i];

    if ($title && $company) {
      $stmt = $pdo->prepare("INSERT INTO experiences (resume_id, title, company, from_date, to_date, description) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->execute([$resume_id, $title, $company, $from, $to, $desc]);
    }
  }
}

// ذخیره دوره‌های آموزشی
if (!empty($_POST['course_name'])) {
  $count = count($_POST['course_name']);
  for ($i = 0; $i < $count; $i++) {
    $course = $_POST['course_name'][$i];
    $institute = $_POST['course_institute'][$i];
    $year = $_POST['course_year'][$i];

    if ($course && $institute) {
      $stmt = $pdo->prepare("INSERT INTO courses (resume_id, course_name, institute, year) VALUES (?, ?, ?, ?)");
      $stmt->execute([$resume_id, $course, $institute, $year]);
    }
  }
}

// هدایت به صفحه نمایش رزومه
header("Location: view-resume.php?id=$resume_id");
exit;
?>