<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include(__DIR__ . "/../php/db.php");

// آواتار پیش‌فرض
$avatar = "/assets/avatar.png";
$_SESSION['user_name'] = "مهمان";

$isLoggedIn = false;
if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT fullname, avatar FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();

  if ($user) {
    $_SESSION['user_name'] = $user['fullname'];
    $avatar = $user['avatar'] ? "assets/avatars/" . $user['avatar'] : "/assets/avatar.png";
    $isLoggedIn = true;
  }
}
?>
<header class="bg-white shadow z-50 relative">
  <div class="container mx-auto px-4 py-4 flex items-center justify-between">

    <!--  بخش راست: لوگو + منوها -->
    <div class="flex items-center gap-8">
      <!-- لوگو -->
      <div class="text-2xl font-bold text-blue-600">JOB GATE</div>

      <!-- منوها -->
      <nav class="hidden md:flex items-center gap-6 text-gray-700 text-sm font-medium">
        <a href="/jobgate/index.php" class="hover:text-blue-600 transition">خانه</a>
        <a href="#" onclick="openTemplateModal()" class="hover:text-blue-600 transition">ساخت رزومه</a>
        <a href="/jobgate/about.php" class="hover:text-blue-600 transition">درباره ما</a>
        <a href="/jobgate/contactUS.php" class="hover:text-blue-600 transition">تماس با ما</a>
      </nav>
    </div>

    <!-- بخش چپ: آواتار یا دکمه ورود -->
    <div>
      <?php if ($isLoggedIn): ?>
        <div class="relative group flex flex-col items-center text-center">
          <button onclick="toggleUserMenu()" class="focus:outline-none">
            <img src="/jobgate/<?php echo $avatar; ?>" class="w-14 h-14 rounded-full border-2 border-blue-500 object-cover" alt="پروفایل">
          </button>
          <div class="text-sm text-gray-800 mt-1 font-semibold"><?php echo $_SESSION['user_name']; ?></div>

          <div id="userMenu" class="absolute left-0 top-20 bg-white shadow-md rounded-md w-44 hidden z-50 text-sm text-gray-700">
            <div class="px-4 py-2 border-b font-semibold text-blue-700"><?php echo $_SESSION['user_name']; ?></div>
            <a href="/jobgate/dashboard/index.php" class="block px-4 py-2 text-black-600 hover:bg-red-100">داشبورد</a>
            <a href="/jobgate/dashboard/setting/index.php" class="block px-4 py-2 text-black-600 hover:bg-red-100">تنظیمات</a>
            <a href="/jobgate/auth/logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-100">خروج</a>
          </div>
        </div>
      <?php else: ?>
        <a href="/jobgate/registerlogin.php" class="text-sm font-semibold text-blue-600 border border-blue-600 px-3 py-1 rounded hover:bg-blue-600 hover:text-white transition">
          ورود / ثبت‌نام
        </a>
      <?php endif;?>
    </div>

  </div>
</header>

<!--  لود مودال فقط برای کاربر واردشده -->
<?php if ($isLoggedIn) include(__DIR__ . "/../template-modal.php"); ?>

<!-- منوی کاربر جاوااسکریپت -->
<script>
  function toggleUserMenu() {
    const menu = document.getElementById("userMenu");
    menu.classList.toggle("hidden");
  }

  // بستن منو با کلیک بیرون
  document.addEventListener("click", function (e) {
    const menu = document.getElementById("userMenu");
    const btn = e.target.closest("button");

    if (!menu.contains(e.target) && !btn) {
      menu.classList.add("hidden");
    }
  });
</script>
