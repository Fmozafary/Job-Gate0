<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
include("../../php/db.php");

// فیلتر، جستجو و مرتب‌سازی
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'desc';
$search = $_GET['search'] ?? '';

$conditions = [];
$params = [];

if ($filter === 'premium') {
  $conditions[] = "subscription = 'paid'";
} elseif ($filter === 'free') {
  $conditions[] = "subscription = 'free'";
} elseif (in_array($filter, ['admin', 'expert', 'user'])) {
  $conditions[] = "role = ?";
  $params[] = $filter;
}

if ($search !== '') {
  $conditions[] = "(fullname LIKE ? OR email LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';
$stmt = $pdo->prepare("SELECT * FROM users $where ORDER BY created_at $sort");
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// آمار کلی
$count_free = $pdo->query("SELECT COUNT(*) FROM users WHERE subscription = 'free'")->fetchColumn();
$count_paid = $pdo->query("SELECT COUNT(*) FROM users WHERE subscription = 'paid'")->fetchColumn();
$count_total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$count_admins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$count_experts = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'expert'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>مدیریت کاربران</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex">
  <!-- سایدبار -->
  <div class="w-56 min-h-screen bg-white border-l shadow-lg p-4">
    <h2 class="text-lg font-bold text-blue-600 mb-6">مدیریت</h2>
    <ul class="space-y-2 text-sm">
      <li><a href="../admin.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">📊 داشبورد</a></li>
      <li><a href="users.php" class="block px-4 py-2 rounded bg-blue-100 text-blue-800 font-semibold">👥 مدیریت کاربران</a></li>
      <li><a href="experts.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🧑‍💼 مدیریت کارشناسان</a></li>
  <li><a href="admin/templates.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🎨 مدیریت قالب‌ها</a></li>
      <li><a href="../../auth/logout.php" class="block px-4 py-2 rounded hover:bg-red-100 text-red-700 transition">🚪 خروج</a></li>
    </ul>
  </div>

  <!-- محتوای اصلی -->
  <div class="flex-1 p-6">
    <h1 class="text-2xl font-bold text-blue-700 mb-6">👥 مدیریت کاربران</h1>

    <!-- آمار کلی -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-blue-100 p-4 rounded-xl text-center">
        <div class="text-sm text-blue-800">کل کاربران</div>
        <div class="text-xl font-bold text-blue-900"><?= $count_total ?></div>
      </div>
      <div class="bg-green-100 p-4 rounded-xl text-center">
        <div class="text-sm text-green-800">اشتراکی</div>
        <div class="text-xl font-bold text-green-900"><?= $count_paid ?></div>
      </div>
      <div class="bg-yellow-100 p-4 rounded-xl text-center">
        <div class="text-sm text-yellow-800">رایگان</div>
        <div class="text-xl font-bold text-yellow-900"><?= $count_free ?></div>
      </div>
      <div class="bg-purple-100 p-4 rounded-xl text-center">
        <div class="text-sm text-purple-800">کارشناسان</div>
        <div class="text-xl font-bold text-purple-900"><?= $count_experts ?></div>
      </div>
    </div>

    <!-- فرم فیلتر -->
    <form class="flex flex-wrap gap-4 items-center mb-6">
      <input type="text" name="search" placeholder="جستجو نام یا ایمیل" value="<?= htmlspecialchars($search) ?>" class="form-input px-4 py-2 rounded border" />
      <select name="filter" class="form-select px-3 py-2 rounded border">
        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>همه</option>
        <option value="premium" <?= $filter === 'premium' ? 'selected' : '' ?>>اشتراکی</option>
        <option value="free" <?= $filter === 'free' ? 'selected' : '' ?>>رایگان</option>
        <option value="user" <?= $filter === 'user' ? 'selected' : '' ?>>کاربر</option>
        <option value="expert" <?= $filter === 'expert' ? 'selected' : '' ?>>کارشناس</option>
        <option value="admin" <?= $filter === 'admin' ? 'selected' : '' ?>>مدیر</option>
      </select>
      <select name="sort" class="form-select px-3 py-2 rounded border">
        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>جدیدترین</option>
        <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>قدیمی‌ترین</option>
      </select>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">فیلتر</button>
    </form>

    <!-- جدول کاربران -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white text-sm border rounded-xl overflow-hidden">
        <thead>
          <tr class="bg-gray-100 text-right">
            <th class="p-3">نام</th>
            <th class="p-3">ایمیل</th>
            <th class="p-3">نقش</th>
            <th class="p-3">اشتراک</th>
            <th class="p-3">پایان اشتراک</th>
            <th class="p-3">تاریخ عضویت</th>
            <th class="p-3 text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="p-3 text-gray-700"><?= htmlspecialchars($user['fullname']) ?></td>
            <td class="p-3 text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
            <td class="p-3 text-blue-600 font-semibold"><?= $user['role'] ?></td>
            <td class="p-3 text-green-600 font-medium"><?= $user['subscription'] === 'paid' ? 'اشتراکی' : 'رایگان' ?></td>
            <td class="p-3 text-gray-500"><?= ($user['subscription'] === 'paid') ? ($user['subscription_expires_at'] ?? '---') : '---' ?></td>
            <td class="p-3 text-gray-500"><?= $user['created_at'] ?></td>
            <td class="p-3 flex justify-center gap-2">
  <a href="edit_user.php?id=<?= $user['id'] ?>" class="bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs px-3 py-1 rounded">📝 ویرایش</a>
  <a href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('آیا مطمئنی می‌خواهی این کاربر حذف شود؟')" class="bg-red-100 hover:bg-red-200 text-red-700 text-xs px-3 py-1 rounded">🗑 حذف</a>
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