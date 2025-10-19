<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

// تأیید یا رد درخواست
if (isset($_GET['action'], $_GET['id'])) {
  $id = (int) $_GET['id'];
  $action = $_GET['action'];

  if ($action === 'approve') {
    // تغییر نقش کاربر و وضعیت درخواست
    $stmt = $pdo->prepare("SELECT user_id FROM expert_requests WHERE id = ?");
    $stmt->execute([$id]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
      $pdo->prepare("UPDATE users SET role = 'expert' WHERE id = ?")->execute([$userId]);
      $pdo->prepare("UPDATE expert_requests SET status = 'approved' WHERE id = ?")->execute([$id]);
    }
  } elseif ($action === 'reject') {
    $pdo->prepare("UPDATE expert_requests SET status = 'rejected' WHERE id = ?")->execute([$id]);
  }

  header("Location: expert-requests.php");
  exit;
}

$requests = $pdo->query("SELECT r.*, u.fullname, u.email FROM expert_requests r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>درخواست‌های کارشناس شدن</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-6xl mx-auto mt-10 bg-white p-6 sm:p-10 shadow-xl rounded-xl">
    <h1 class="text-2xl font-bold text-blue-700 mb-6">📥 درخواست‌های تبدیل به کارشناس</h1><div class="overflow-x-auto">
  <table class="min-w-full bg-white text-sm border rounded-xl overflow-hidden">
    <thead>
      <tr class="bg-gray-100 text-right">
        <th class="p-3">نام</th>
        <th class="p-3">ایمیل</th>
        <th class="p-3">مدرک</th>
        <th class="p-3">مهارت‌ها</th>
        <th class="p-3">رزومه</th>
        <th class="p-3">توضیحات</th>
        <th class="p-3">وضعیت</th>
        <th class="p-3">عملیات</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $req): ?>
      <tr class="border-b hover:bg-gray-50">
        <td class="p-3 text-gray-700 font-medium"><?= htmlspecialchars($req['fullname']) ?></td>
        <td class="p-3 text-gray-600"><?= htmlspecialchars($req['email']) ?></td>
        <td class="p-3 text-blue-700"><?= htmlspecialchars($req['degree']) ?></td>
        <td class="p-3 text-sm text-gray-600"><?= htmlspecialchars($req['skills']) ?></td>
        <td class="p-3">
          <?php if ($req['cv_path']): ?>
          <a href="../../uploads/<?= $req['cv_path'] ?>" target="_blank" class="text-blue-600 hover:underline">مشاهده</a>
          <?php else: ?>
          <span class="text-gray-400">ندارد</span>
          <?php endif; ?>
        </td>
        <td class="p-3 text-xs text-gray-500 line-clamp-3 max-w-xs">
          <?= nl2br(htmlspecialchars($req['about'])) ?>
        </td>
        <td class="p-3 text-sm font-bold <?php
          if ($req['status'] === 'pending') echo 'text-yellow-600';
          elseif ($req['status'] === 'approved') echo 'text-green-600';
          else echo 'text-red-600';
        ?>">
          <?= $req['status'] === 'pending' ? 'در انتظار' : ($req['status'] === 'approved' ? 'تأیید شده' : 'رد شده') ?>
        </td>
        <td class="p-3 flex gap-2">
          <?php if ($req['status'] === 'pending'): ?>
          <a href="?action=approve&id=<?= $req['id'] ?>" class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">✅ تأیید</a>
          <a href="?action=reject&id=<?= $req['id'] ?>" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">❌ رد</a>
          <?php else: ?>
          <span class="text-gray-400 text-xs">پایان یافته</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

  </div>
</body>
</html>