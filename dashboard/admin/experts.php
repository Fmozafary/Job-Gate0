<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
include("../../php/db.php");

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

$stmt = $pdo->prepare("SELECT u.*, COUNT(r.id) as resume_count FROM users u LEFT JOIN resumes r ON r.user_id = u.id WHERE u.role = 'expert' AND (u.fullname LIKE ? OR u.email LIKE ?) GROUP BY u.id ORDER BY u.created_at $sort");
$stmt->execute(["%$search%", "%$search%"]); 
$experts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($experts);
$new = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'expert' AND created_at >= NOW() - INTERVAL 30 DAY")->fetchColumn();
$resume_submissions = $pdo->query("SELECT COUNT(*) FROM expert_requests WHERE status = 'pending'")->fetchColumn();

$requests = $pdo->query("SELECT er.*, u.fullname, u.email FROM expert_requests er JOIN users u ON er.user_id = u.id WHERE er.status = 'pending' ORDER BY er.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html><html lang="fa" dir="rtl"><head>
  <meta charset="UTF-8">
  <title>مدیریت کارشناسان</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
  <style>
    .modal-bg { background-color: rgba(0, 0, 0, 0.5); }
    .badge {
      background-color: red;
      color: white;
      border-radius: 50%;
      padding: 0.2em 0.6em;
      font-size: 0.75rem;
      position: absolute;
      top: -8px;
      right: -8px;
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex">
  <!-- سایدبار -->
  <div class="w-56 min-h-screen bg-white border-l shadow-lg p-4">
    <h2 class="text-lg font-bold text-blue-600 mb-6">مدیریت</h2>
    <ul class="space-y-2 text-sm">
      <li><a href="../admin.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">📊 داشبورد</a></li>
      <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">👥 مدیریت کاربران</a></li>
      <li><a href="experts.php" class="block px-4 py-2 rounded bg-blue-100 text-blue-800 font-semibold">🧑‍💼 مدیریت کارشناسان</a></li>
   <li><a href="admin/templates.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🎨 مدیریت قالب‌ها</a></li>
      <li><a href="../../auth/logout.php" class="block px-4 py-2 rounded hover:bg-red-100 text-red-700 transition">🚪 خروج</a></li>
    </ul>
  </div>
  <!-- محتوا -->
  <div class="flex-1 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-blue-700">🧑‍💼 مدیریت کارشناسان</h1>
      <div class="relative">
        <button onclick="openRequests()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 text-sm rounded relative">
          📂 درخواست‌های کارشناسی
          <?php if ($resume_submissions > 0): ?>
            <span class="badge"><?= $resume_submissions ?></span>
          <?php endif; ?>
        </button>
      </div>
    </div>
    <!-- کارت‌های آماری -->
    <div id="mainTable" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-blue-100 p-4 rounded-xl text-center">
        <div class="text-sm text-blue-800">تعداد کل کارشناسان</div>
        <div class="text-2xl font-bold text-blue-900"><?= $total ?></div>
      </div>
      <div class="bg-green-100 p-4 rounded-xl text-center">
        <div class="text-sm text-green-800">کارشناسان جدید این ماه</div>
        <div class="text-2xl font-bold text-green-900"><?= $new ?></div>
      </div>
      <div class="bg-purple-100 p-4 rounded-xl text-center">
        <div class="text-sm text-purple-800">میانگین رزومه‌های بررسی‌شده</div>
        <div class="text-2xl font-bold text-purple-900"><?= round(array_sum(array_column($experts, 'resume_count')) / max(1, $total), 1) ?></div>
      </div>
      <div class="bg-yellow-100 p-4 rounded-xl text-center">
<div class="text-sm text-yellow-800">درخواست‌های کارشناسی</div>
        <div class="text-2xl font-bold text-yellow-900"><?= $resume_submissions ?></div>
      </div>
    </div><!-- جدول کارشناسان -->
<div id="expertsTable">
  <form class="flex flex-wrap gap-4 items-center mb-6">
    <input type="text" name="search" placeholder="جستجوی نام یا ایمیل" value="<?= htmlspecialchars($search) ?>" class="form-input px-4 py-2 rounded border" />
    <select name="sort" class="form-select px-3 py-2 rounded border">
      <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>جدیدترین</option>
      <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>قدیمی‌ترین</option>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">فیلتر</button>
  </form>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white text-sm border rounded-xl overflow-hidden">
      <thead>
        <tr class="bg-gray-100 text-right">
          <th class="p-3">نام</th>
          <th class="p-3">ایمیل</th>
          <th class="p-3">رزومه بررسی‌شده</th>
          <th class="p-3">تاریخ عضویت</th>
          <th class="p-3 text-center">عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($experts as $expert): ?>
        <tr class="border-b hover:bg-gray-50">
          <td class="p-3 text-gray-700 font-medium flex items-center gap-2">
            <img src="../../uploads/<?= $expert['photo'] ?? 'default.png' ?>" class="w-8 h-8 rounded-full border" alt="avatar">
            <?= htmlspecialchars($expert['fullname']) ?>
          </td>
          <td class="p-3 text-gray-600"><?= htmlspecialchars($expert['email']) ?></td>
          <td class="p-3 text-green-600 font-bold text-center"><?= $expert['resume_count'] ?></td>
          <td class="p-3 text-gray-500"><?= $expert['created_at'] ?></td>
          <td class="p-3 flex justify-center gap-2">
            <a href="edit_user.php?id=<?= $expert['id'] ?>" class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded">✏ ویرایش</a>
            <a href="delete.php?id=<?= $expert['id'] ?>" onclick="return confirm('آیا از حذف مطمئنی؟')" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded">🗑 حذف</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- پوشه درخواست‌ها -->
<div id="requestsBox" class="hidden mt-10">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-purple-700">📂 درخواست‌های اخیر کارشناسی</h2>
    <button onclick="closeRequests()" class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-1 rounded">✖ بستن</button>
  </div>
  <?php if ($requests): ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($requests as $req): ?>
    <div class="bg-white p-4 rounded-xl shadow border">
      <p class="mb-1 font-semibold text-gray-700">👤 <?= htmlspecialchars($req['fullname']) ?></p>
      <p class="text-sm text-gray-600 mb-1">📧 <?= htmlspecialchars($req['email']) ?></p>
      <p class="text-sm text-gray-500 mb-1">🕓 <?= $req['created_at'] ?></p>
      <p class="text-sm text-gray-600 mb-2">🎓 <?= $req['degree'] ?> - 💼 <?= $req['skills'] ?></p>
      <a href="../../assets/expert_cv/<?= $req['cv_path'] ?>" target="_blank" class="text-blue-600 text-sm underline">📄 مشاهده فایل رزومه</a>
      <div class="mt-3 flex gap-2">
        <a href="../actions/approve_expert.php?id=<?= $req['id'] ?>" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">✔ تایید</a>
        <a href="../actions/reject_expert.php?id=<?= $req['id'] ?>" onclick="return confirm('آیا مطمئن هستید؟')" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">✖ رد</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="text-center text-gray-500">درخواستی یافت نشد.</p>
  <?php endif; ?>
</div>
</div>
</div>
<script>
function openRequests() {
  document.getElementById('requestsBox').classList.remove('hidden');
  document.getElementById('mainTable').classList.add('hidden');
  document.getElementById('expertsTable').classList.add('hidden');
}
function closeRequests() {
  document.getElementById('requestsBox').classList.add('hidden');
  document.getElementById('mainTable').classList.remove('hidden');
  document.getElementById('expertsTable').classList.remove('hidden');
}
</script>
</body>
</html>