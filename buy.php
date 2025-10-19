
    
     <?php
session_start();
include("php/db.php");

$isLoggedIn = isset($_SESSION['user_id']);
$templateId = $_GET['template'] ?? null;

if (!$templateId || $templateId == 1) {
  header("Location: index.php");
  exit;
}

// بررسی اشتراک فعال
$isSubscribed = false;
if ($isLoggedIn) {
  $stmt = $pdo->prepare("SELECT subscription_expires_at FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $data = $stmt->fetch();
  $isSubscribed = ($data && strtotime($data['subscription_expires_at']) > time());

  // اگر اشتراک داره، بفرستش به رزومه‌ساز
  if ($isSubscribed) {
    header("Location: resume/resume-builder.php?template=$templateId");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>خرید اشتراک قالب‌ها</title>
  <link href="css/tailwind.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <style>
    .pay-btn {
      position: relative;
      transition: all 0.3s ease-in-out;
    }
    .pay-btn.loading {
      opacity: 0.6;
      pointer-events: none;
    }
    .pay-btn.loading::after {
      content: "";
      position: absolute;
      top: 50%;
      right: 50%;
      width: 1rem;
      height: 1rem;
      border: 3px solid #fff;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      transform: translate(50%, -50%);
    }
    @keyframes spin {
      to { transform: rotate(360deg) translate(50%, -50%); }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-white min-h-screen font-sans">
  <div class="max-w-3xl mx-auto mt-16 bg-white shadow-2xl rounded-3xl p-8">
    <h1 class="text-3xl font-bold text-blue-700 text-center mb-6">💎 دسترسی به همه قالب‌های حرفه‌ای</h1>
    <p class="text-center text-gray-600 mb-10 max-w-md mx-auto text-sm leading-6">
      با خرید اشتراک، به تمام قالب‌های رزومه حرفه‌ای ما دسترسی پیدا می‌کنی و می‌تونی بی‌نهایت رزومه بسازی، ویرایش کنی و دانلود بگیری.
    </p>

    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 flex flex-col items-center space-y-4 text-center shadow-md">
  <div class="flex flex-row items-center gap-6 w-full"> <!-- تغییر اینجا -->
    <img src="assets/images/<?= $templateId ?>.png" alt="قالب انتخاب‌شده" class="rounded-xl w-40 border shadow">
    <ul class="text-sm text-gray-700 leading-6 text-right mt-0 space-y-2 flex-col justify-center"> <!-- حذف mt-4 -->
      <li>✅ دسترسی نامحدود به تمام قالب‌ها</li>
      <li>✅ امکان ویرایش و دانلود به‌صورت PDF</li>
      <li>✅ پشتیبانی اولویت‌دار</li>
      <li>✅ اشتراک فعال تا ۳۰ روز</li>
    </ul>
  </div>
  <div class="text-2xl font-bold text-green-600">۸۰٬۰۰۰ تومان</div>

  <?php if ($isLoggedIn): ?>
    <form id="payForm" action="#" onsubmit="return false;" class="w-full max-w-xs">
      <input type="hidden" name="template" value="<?= $templateId ?>">
      <button type="submit" id="payBtn" class="pay-btn bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-full shadow w-full transition">
        پرداخت و شروع ساخت رزومه
      </button>
    </form>
  <?php else: ?>
    <div class="mt-4 w-full max-w-xs">
      <a href="registerlogin.php" class="block bg-yellow-100 text-yellow-900 border border-yellow-300 px-4 py-3 rounded-xl text-sm text-center hover:bg-yellow-200 transition">
        ابتدا وارد شوید یا ثبت‌نام کنید
      </a>
    </div>
  <?php endif; ?>
</div>
    <div class="text-center mt-10">
      <a href="index.php" class="text-sm text-gray-500 hover:underline">⬅ بازگشت به صفحه اصلی</a>
    </div>
  </div>

<?php if ($isLoggedIn && !$isSubscribed): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("payForm");
  const btn = document.getElementById("payBtn");

  form.addEventListener("submit", function(e) {
    e.preventDefault();
    btn.classList.add("loading");
// شبیه‌سازی پرداخت و فعال‌سازی اشتراک
    setTimeout(() => {
      fetch("php/activate_subscription.php").then(() => {
        const templateId = form.querySelector("input[name=template]").value;
        window.location.href = "/jobgate/resume/resume-builder.php?template=" + templateId;
      });
    }, 1000);
  });
});
</script>
<?php endif; ?>
</body>
</html>
