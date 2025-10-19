<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

// دریافت سه نظر تأیید شده
$approved = $pdo->query("SELECT * FROM feedback WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// دریافت نظرات تأیید نشده
$pending = $pdo->query("SELECT * FROM feedback WHERE is_approved = 0 ORDER BY created_at ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>مدیریت نظرات کاربران</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex">
    <!-- سایدبار -->
    <div class="w-56 min-h-screen bg-white border-l shadow-lg p-4">
      <h2 class="text-lg font-bold text-blue-600 mb-6">مدیریت</h2>
      <ul class="space-y-2 text-sm">
        <li><a href="admin.php" class="block px-4 py-2 rounded hover:bg-gray-100">📊 داشبورد</a></li>
        <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-100">👥 مدیریت کاربران</a></li>
        <li><a href="experts.php" class="block px-4 py-2 rounded hover:bg-gray-100">🧑‍💼 مدیریت کارشناسان</a></li>
        <li><a href="resumes.php" class="block px-4 py-2 rounded hover:bg-gray-100">📄 مدیریت رزومه‌ها</a></li>
        <li><a href="templates.php" class="block px-4 py-2 rounded hover:bg-gray-100">🧩 مدیریت قالب‌ها</a></li>
        <li><a href="feedbacks.php" class="block px-4 py-2 rounded bg-blue-100 text-blue-800 font-semibold">💬 نظرات و پیشنهادات</a></li>
        <li><a href="../../auth/logout.php" class="block px-4 py-2 rounded hover:bg-red-100 text-red-700">🚪 خروج</a></li>
      </ul>
    </div>

    <!-- محتوای اصلی -->
    <div class="flex-1 p-6 sm:p-10">
      <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-xl p-6">
        <h1 class="text-2xl font-bold text-blue-700 mb-6">💬 مدیریت نظرات کاربران</h1>

        <!-- نظرات تأییدشده (برای صفحه اصلی) -->
        <h2 class="text-xl font-bold text-gray-700 mb-4">✅ نظرات قرارگرفته در صفحه اصلی</h2>
        <div class="grid md:grid-cols-3 gap-4 mb-10">
          <?php foreach ($approved as $a): ?>
            <div class="bg-gray-50 border p-4 rounded-xl shadow text-sm">
              <p class="text-gray-700 mb-3">"<?php echo nl2br(htmlspecialchars($a['message'])); ?>"</p>
              <div class="font-bold text-blue-700"><?php echo htmlspecialchars($a['fullname']); ?></div>
              <div class="text-xs text-gray-500"><?php echo htmlspecialchars($a['email']); ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- نظرات جدید -->
        <h2 class="text-xl font-bold text-gray-700 mb-4">🕒 نظرات جدید</h2>
        <?php if (empty($pending)): ?>
          <p class="text-sm text-gray-500">فعلاً نظری برای بررسی وجود ندارد.</p>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded-xl text-sm text-right">
              <thead class="bg-gray-100">
                <tr>
                  <th class="p-3">نام</th>
                  <th class="p-3">ایمیل</th>
                  <th class="p-3">پیام</th>
                  <th class="p-3">عملیات</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pending as $p): ?>
                  <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($p['fullname']); ?></td>
                    <td class="p-3 text-gray-600"><?php echo htmlspecialchars($p['email']); ?></td>
                    <td class="p-3 text-gray-700"><?php echo nl2br(htmlspecialchars($p['message'])); ?></td>
                    <td class="p-3 space-x-2 space-y-2">
                      <form action="feedback-action.php" method="post" class="inline">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
   
                      </form>
                                       <td class="p-3 space-x-2 space-y-2">
  <button onclick="approveFeedback(<?= $p['id'] ?>, this)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded text-xs shadow">✅ تایید</button>
     <button onclick="rejectFeedback(<?= $p['id'] ?>, this)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded text-xs">✖ رد</button>

</td>
                      <form action="feedback-action.php" method="post" class="inline">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                         
                      
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <script>

function approveFeedback(id, btn) {
  fetch("feedback-action.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}&action=approve`
  }).then(res => res.text()).then(data => {
    if (data.trim() === "approved") {
      btn.parentElement.innerHTML = '<span class="text-green-600 text-sm font-bold">تایید شد ✅</span>';
      setTimeout(() => location.reload(), 1000); // برای تازه‌سازی کارت‌ها
    }
  });
}

function rejectFeedback(id, btn) {
  if (!confirm("آیا از حذف این پیام مطمئن هستید؟")) return;

  fetch("feedback-action.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}&action=reject`
  }).then(res => res.text()).then(data => {
    if (data.trim() === "deleted") {
      btn.closest("tr").remove(); // حذف سطر از جدول
    }
  });
}

</script>
</body>
</html>