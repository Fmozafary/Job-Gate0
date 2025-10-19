<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $degree = $_POST['degree'];
  $skills = $_POST['skills'];
  $about = $_POST['about'];
  $status = 'pending';
  $cv = '';

  if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
    $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
    $cv = 'cv_' . time() . "." . $ext;
    
    // مسیر درست برای ذخیره فایل
    $targetDir = '../../assets/expert_cv/';
    $targetPath = $targetDir . $cv;

    // اگر مسیر وجود نداشت، بسازش
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath);
  }

  $stmt = $pdo->prepare("INSERT INTO expert_requests (user_id, degree, skills, about, cv_path, status) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$user_id, $degree, $skills, $about, $cv, $status]);
  $success = true;
}

?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>درخواست کارشناس شدن</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-tr from-blue-50 to-white min-h-screen font-sans">
  <div class="max-w-2xl mx-auto mt-12 bg-white p-8 rounded-3xl shadow-2xl">
    <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">📋 درخواست تبدیل به کارشناس</h1><?php if ($success): ?>
  <div class="bg-green-100 border border-green-300 text-green-800 text-sm p-4 rounded-lg mb-6 text-center">
    ✅ درخواست شما با موفقیت ثبت شد و در انتظار بررسی ادمین است.
  </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="space-y-6">
  <div>
    <label class="block font-semibold mb-1 text-gray-700">🎓 مدرک تحصیلی</label>
    <input type="text" name="degree" class="form-input w-full px-4 py-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-400" required>
  </div>
  <div>
    <label class="block font-semibold mb-1 text-gray-700">💡 مهارت‌ها (با کاما جدا کن)</label>
    <input type="text" name="skills" class="form-input w-full px-4 py-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-400" required>
  </div>
  <div>
    <label class="block font-semibold mb-1 text-gray-700">📝 درباره تخصص</label>
    <textarea name="about" rows="4" class="form-textarea w-full px-4 py-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-400" required></textarea>
  </div>
  <div>
    <label class="block font-semibold mb-1 text-gray-700">📎 بارگذاری رزومه (PDF)</label>
    <input type="file" name="cv" accept="application/pdf" class="block w-full text-sm text-gray-700 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none">
  </div>
  <div class="text-center mt-6">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-full shadow">📨 ارسال درخواست</button>
  </div>
</form>

  </div>
</body>
</html>