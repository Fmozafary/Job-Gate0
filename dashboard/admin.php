
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
include("../php/db.php");

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalResumes = $pdo->query("SELECT COUNT(*) FROM resumes")->fetchColumn();
$totalExperts = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'expert'")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
$resumes = $pdo->query("SELECT * FROM resumes ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html><html lang="fa" dir="rtl"><head>  <meta charset="UTF-8">
  <title>داشبورد ادمین</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex">
  <!-- سایدبار -->
  <div class="w-56 min-h-screen bg-white border-l shadow-lg p-4">
    <h2 class="text-lg font-bold text-blue-600 mb-6">مدیریت</h2>
    <ul class="space-y-2 text-sm">
      <li><a href="admin.php" class="block px-4 py-2 rounded bg-blue-100 text-blue-800 font-semibold">📊 داشبورد</a></li>
      <li><a href="admin/users.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">👥 مدیریت کاربران</a></li>
      <li><a href="admin/experts.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🧑‍💼 مدیریت کارشناسان</a></li>
    <li><a href="admin/templates.php" class="block px-4 py-2 rounded hover:bg-gray-100 transition">🎨 مدیریت قالب‌ها</a></li>
     <li><a href="feedbacks.php" class="block px-4 py-2 rounded  ">💬 نظرات و پیشنهادات</a></li>
      <li><a href="../auth/logout.php" class="block px-4 py-2 rounded hover:bg-red-100 text-red-700 transition">🚪 خروج</a></li>
    </ul>
  </div>  <!-- محتوای داشبورد -->  <div class="flex-1 p-6 sm:p-10">
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-xl p-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-6">🎯 داشبورد مدیریت سایت</h1><!-- آمار کلی -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-blue-100 p-4 rounded-xl text-center">
      <div class="text-sm text-blue-800">کاربران</div>
      <div class="text-2xl font-bold text-blue-900"><?php echo $totalUsers; ?></div>
    </div>
    <div class="bg-green-100 p-4 rounded-xl text-center">
      <div class="text-sm text-green-800">رزومه‌ها</div>
      <div class="text-2xl font-bold text-green-900"><?php echo $totalResumes; ?></div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-xl text-center">
      <div class="text-sm text-yellow-800">کارشناس‌ها</div>
      <div class="text-2xl font-bold text-yellow-900"><?php echo $totalExperts; ?></div>
    </div>
    <div class="bg-red-100 p-4 rounded-xl text-center">
      <div class="text-sm text-red-800">مدیران</div>
      <div class="text-2xl font-bold text-red-900"><?php echo $totalAdmins; ?></div>
    </div>
  </div>

  <!-- نمودار آماری -->
  <div class="bg-white p-6 rounded-xl shadow mb-12">
    <canvas id="adminChart" class="w-full max-w-4xl mx-auto"></canvas>
  </div>

  <!-- لیست کاربران اخیر -->
  <h2 class="text-xl font-bold text-gray-700 mb-4">👥 کاربران اخیر</h2>
  <div class="overflow-x-auto mb-10">
    <table class="min-w-full bg-white text-sm border rounded-xl overflow-hidden">
      <thead>
        <tr class="bg-gray-100 text-right">
          <th class="p-3">نام</th>
          <th class="p-3">ایمیل</th>
          <th class="p-3">نقش</th>
          <th class="p-3">تاریخ عضویت</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
        <tr class="border-b hover:bg-gray-50">
          <td class="p-3 text-gray-700"><?php echo htmlspecialchars($user['fullname']); ?></td>
          <td class="p-3 text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
          <td class="p-3 text-blue-600 font-semibold"><?php echo $user['role']; ?></td>
          <td class="p-3 text-gray-500"><?php echo $user['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- لیست رزومه‌های اخیر -->
  <h2 class="text-xl font-bold text-gray-700 mb-4">📄 آخرین رزومه‌ها</h2>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($resumes as $res): ?>
    <div class="bg-gray-50 p-4 rounded-xl shadow hover:shadow-md transition">
      <h3 class="font-bold text-gray-800 text-lg mb-1"><?php echo htmlspecialchars($res['fullname']); ?></h3>
      <p class="text-sm text-gray-600">ایمیل: <?php echo $res['email']; ?></p>
      <p class="text-sm text-gray-600">قالب: <?php echo $res['template_id']; ?></p>
      <p class="text-xs text-gray-500 mt-1">ایجاد: <?php echo $res['created_at']; ?></p>
    </div>
    <?php endforeach; ?>
  </div>

</div>

  </div>
</div>
<script>
  const ctx = document.getElementById('adminChart');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['کاربران', 'رزومه‌ها', 'کارشناس‌ها', 'مدیران'],
      datasets: [{
        label: 'تعداد',
        data: [<?php echo $totalUsers; ?>, <?php echo $totalResumes; ?>, <?php echo $totalExperts; ?>, <?php echo $totalAdmins; ?>],
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ctx.raw + ' عدد' } }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>
</body>
</html>