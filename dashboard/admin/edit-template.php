<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

$id = $_GET['id'] ?? null;

if (!$id) {
  header("Location: templates.php");
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ?");
$stmt->execute([$id]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$template) {
  echo "قالب پیدا نشد!";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  if (!empty($_FILES['image']['name'])) {
    $imageName = basename($_FILES['image']['name']);
    $targetPath = "../../assets/images/" . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    $image_path = "assets/images/" . $imageName;
  } else {
    $image_path = $template['image_path'];
  }

  $stmt = $pdo->prepare("UPDATE templates SET name = ?, image_path = ?, is_active = ? WHERE id = ?");
  $stmt->execute([$name, $image_path, $is_active, $id]);

  header("Location: templates.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ویرایش قالب</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen font-sans">
<div class="flex">
  <!-- سایدبار -->


  <!-- محتوای اصلی -->
  <div class="flex-1 p-6 sm:p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-2xl border border-gray-200">
      <h1 class="text-3xl font-extrabold text-blue-700 mb-8 text-center">ویرایش قالب رزومه</h1>

      <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- عنوان -->
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-2">عنوان قالب</label>
          <input type="text" name="name" value="<?= htmlspecialchars($template['name']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="مثلاً قالب شیک">
        </div>

        <!-- تصویر فعلی -->
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-2">تصویر فعلی</label>
          <img src="../../<?= $template['image_path'] ?>" alt="قالب" class="w-40 rounded-xl shadow border">
        </div>

        <!-- آپلود تصویر جدید -->
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-2">تصویر جدید (اختیاری)</label>
          <input type="file" name="image" class="text-sm text-gray-700">
        </div>

        <!-- وضعیت -->
        <div class="flex items-center gap-2">
          <input type="checkbox" name="is_active" id="is_active" <?= $template['is_active'] ? 'checked' : '' ?> class="w-4 h-4 accent-blue-600">
          <label for="is_active" class="text-sm text-gray-700">قالب فعال باشد</label>
        </div>

        <!-- دکمه‌ها -->
        <div class="flex justify-between items-center pt-4">
          <a href="templates.php" class="text-sm text-gray-500 hover:text-gray-800 transition">⬅ بازگشت</a>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow font-semibold transition">
            💾 ذخیره تغییرات
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>