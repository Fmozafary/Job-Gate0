<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

$templates = $pdo->query("SELECT * FROM templates")->fetchAll(PDO::FETCH_ASSOC);
$totalTemplates = count($templates);
$activeTemplates = $pdo->query("SELECT COUNT(*) FROM templates WHERE is_active = 1")->fetchColumn();
$inactiveTemplates = $pdo->query("SELECT COUNT(*) FROM templates WHERE is_active = 0")->fetchColumn();
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>مدیریت قالب‌ها</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex">
    <!-- سایدبار -->
    <div class="w-56 min-h-screen bg-white border-l shadow-lg p-4">
      <h2 class="text-lg font-bold text-blue-600 mb-6">مدیریت</h2>
      <ul class="space-y-2 text-sm">
        <li><a href="admin.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">📊 داشبورد</a></li>
        <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">👥 مدیریت کاربران</a></li>
        <li><a href="experts.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🧑‍💼 مدیریت کارشناسان</a></li>
        <li><a href="resumes.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">📄 مدیریت رزومه‌ها</a></li>
        <li><a href="templates.php" class="block px-4 py-2 rounded bg-blue-100 text-blue-800 font-semibold">🎨 مدیریت قالب‌ها</a></li>
        <li><a href="../../auth/logout.php" class="block px-4 py-2 rounded hover:bg-red-100 text-red-700 transition">🚪 خروج</a></li>
      </ul>
    </div><!-- محتوای اصلی -->
<div class="flex-1 p-6 sm:p-10">
  <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-xl p-6">
    <h1 class="text-2xl font-bold text-blue-700 mb-6">🖼️ مدیریت قالب‌های رزومه</h1>

    <!-- کارت‌های آماری -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div class="bg-green-100 p-4 rounded-xl text-center">
        <div class="text-sm text-green-800">قالب‌های فعال</div>
        <div class="text-2xl font-bold text-green-900"><?php echo $activeTemplates; ?></div>
      </div>
      <div class="bg-red-100 p-4 rounded-xl text-center">
        <div class="text-sm text-red-800">قالب‌های غیرفعال</div>
        <div class="text-2xl font-bold text-red-900"><?php echo $inactiveTemplates; ?></div>
      </div>
      <div class="bg-blue-100 p-4 rounded-xl text-center">
        <div class="text-sm text-blue-800">مجموع قالب‌ها</div>
        <div class="text-2xl font-bold text-blue-900"><?php echo $totalTemplates; ?></div>
      </div>
    </div>

    <!-- جدول قالب‌ها -->
    <table class="min-w-full bg-white text-sm border rounded-xl overflow-hidden">
      <thead class="bg-gray-100 text-right">
        <tr>
          <th class="p-3">شماره</th>
          <th class="p-3">تصویر</th>
          <th class="p-3">عنوان</th>
          <th class="p-3">وضعیت</th>
          <th class="p-3">ویرایش</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($templates as $t): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="p-3 text-gray-700"><?php echo $t['id']; ?></td>
            <td class="p-3">
              <img src="../../<?php echo htmlspecialchars($t['image_path']); ?>" class="w-24 rounded shadow border">
            </td>
            <td class="p-3 text-gray-700"><?php echo htmlspecialchars($t['name']); ?></td>
            <td class="p-3">
              <span class="inline-block px-3 py-1 text-xs rounded-full <?php echo $t['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $t['is_active'] ? 'فعال' : 'غیرفعال'; ?>
              </span>
            </td>
            <td class="p-3">
              <a href="edit-template.php?id=<?php echo $t['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-xs rounded shadow">
                ✏️ ویرایش
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

  </div>
</body>
</html>