<?php
session_start();
include("../php/db.php"); // اتصال به دیتابیس
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['user_id'] ?? null;

// وصل کردن کاربر به کارشناس در صورتی که هنوز وصل نشده
$stmt = $pdo->prepare("SELECT expert_id FROM user_expert_map WHERE user_id = ?");
$stmt->execute([$user_id]);
$expert_id = $stmt->fetchColumn();
if (!$expert_id) {
  // انتخاب تصادفی یک کارشناس
  $expert_stmt = $pdo->query("SELECT id FROM users WHERE role = 'expert'");
  $experts = $expert_stmt->fetchAll(PDO::FETCH_COLUMN);
  if (!empty($experts)) {
    $expert_id = $experts[array_rand($experts)];
    // ثبت در جدول user_expert_map
    $insert_map = $pdo->prepare("INSERT INTO user_expert_map (user_id, expert_id) VALUES (?, ?)");
    $insert_map->execute([$user_id, $expert_id]);
  }
}

// گرفتن اطلاعات اصلی از فرم
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$province = $_POST['province'];
$address = $_POST['address'];
$national_code = $_POST['national_code'];
$birth = $_POST['birth'];
$gender = $_POST['gender'];
$marital = $_POST['marital'];
$military = $_POST['military'];
$about = $_POST['about'];
$skills = $_POST['skills'];
$software = $_POST['software'];
$language = implode(', ', $_POST['language']);
$language_level = implode(', ', $_POST['language_level']);
$interests = $_POST['interests'];
$template_id = $_POST['template_id'] ?? 1;

// آپلود عکس
$photo_path = "";
if (!empty($_FILES['photo']['name'])) {
    $photo_name = time() . '_' . basename($_FILES['photo']['name']);
    $upload_dir = "../assets/uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name);
    $photo_path = "assets/uploads/" . $photo_name;
}

// اگر قالب پولی بود کاربر اشتراکی بشه
if ($template_id > 1 && $user_id) {
    $stmt = $pdo->prepare("UPDATE users SET is_premium = 1, subscription = 'paid' WHERE id = ?");
    $stmt->execute([$user_id]);
}


// ثبت رزومه
$stmt = $pdo->prepare("INSERT INTO resumes 
(user_id, fullname, email, phone, city, province, address, national_code, birth, gender, marital, military, photo, about, skills, software, language, language_level, interests, template_id, created_at) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$stmt->execute([
    $user_id,
    $fullname,
    $email,
    $phone,
    $city,
    $province,
    $address,
    $national_code,
    $birth,
    $gender,
    $marital,
    $military,
    $photo_path,
    $about,
    $skills,
    $software,
    $language,
    $language_level,
    $interests,
    $template_id
]);
if ($stmt->rowCount() == 0) {
  echo "هیچ رزومه‌ای ذخیره نشد!";
  exit;
}


$resume_id = $pdo->lastInsertId();

// ثبت سوابق تحصیلی
if (isset($_POST['major']) && is_array($_POST['major'])) {
    foreach ($_POST['major'] as $index => $major) {
        $university = $_POST['university'][$index] ?? '';
        $graduation_year = $_POST['graduation_year'][$index] ?? '';

        if (!empty($major) || !empty($university)) {
            $stmt = $pdo->prepare("INSERT INTO educations (resume_id, major, university, graduation_year) VALUES (?, ?, ?, ?)");
            $stmt->execute([$resume_id, $major, $university, $graduation_year]);
        }
    }
}

// ثبت سوابق شغلی
if (isset($_POST['job_title']) && is_array($_POST['job_title'])) {
    foreach ($_POST['job_title'] as $index => $title) {
        $company = $_POST['company'][$index] ?? '';
        $from_date = $_POST['from_date'][$index] ?? '';
        $to_date = $_POST['to_date'][$index] ?? '';
        $desc = $_POST['job_description'][$index] ?? '';

        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO experiences (resume_id, title, company, from_date, to_date, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$resume_id, $title, $company, $from_date, $to_date, $desc]);
        }
    }
}

// ثبت دوره‌های آموزشی
if (isset($_POST['course_name']) && is_array($_POST['course_name'])) {
    foreach ($_POST['course_name'] as $index => $course_name) {
        $institute = $_POST['institute'][$index] ?? '';
        $year = $_POST['year'][$index] ?? '';

        if (!empty($course_name)) {
            $stmt = $pdo->prepare("INSERT INTO courses (resume_id, course_name, institute, year) VALUES (?, ?, ?, ?)");
            $stmt->execute([$resume_id, $course_name, $institute, $year]);
        }
    }
}

// انتقال به صفحه نمایش رزومه
header("Location: view-resume.php?id=$resume_id");
exit;
?>