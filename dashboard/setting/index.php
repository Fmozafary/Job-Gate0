<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../../auth/login.php");
  exit;
}

// نمایش پیام موفقیت یا خطا از session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$stmt = $pdo->prepare("SELECT fullname, email, phone, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$avatarPath = $user['avatar'] ? "../../assets/avatars/" . $user['avatar'] : "../../assets/avatar-default.png";
?><!DOCTYPE html><html lang="fa" dir="rtl"><head>
  <meta charset="UTF-8">
  <title>تنظیمات پروفایل</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
  <link href="../../css/style.css" rel="stylesheet">
  <style>
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      padding: 1rem 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      gap: 1rem;
      animation: fadeIn 0.4s ease-in-out;
    }
    .toast-success {
      background-color: #d1fae5;
      border-left: 6px solid #10b981;
      color: #065f46;
    }
    .toast-error {
      background-color: #fee2e2;
      border-left: 6px solid #ef4444;
      color: #991b1b;
    }
    .toast-progress {
      position: absolute;
      bottom: 0;
      left: 0;
      height: 4px;
      background: rgba(0,0,0,0.2);
      animation: toastBar 6s linear forwards;
    }
    @keyframes toastBar {
      from { width: 100%; }
      to { width: 0%; }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
  <script>
    function togglePasswordVisibility(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }
    function dismissToast() {
      const el = document.getElementById('flashToast');
      if (el) el.remove();
    }
    setTimeout(() => dismissToast(), 6000);
  </script>
</head>
<body class="bg-gray-100 font-sans">
<?php if ($flash): ?>
  <div id="flashToast" class="toast <?php echo $flash['type'] === 'success' ? 'toast-success' : 'toast-error'; ?>">
    <div class="flex-1 text-sm">
      <?php echo htmlspecialchars($flash['message']); ?>
    </div>
    <button onclick="dismissToast()" class="text-xl leading-none">×</button>
    <div class="toast-progress"></div>
  </div>
<?php endif; ?><div class="max-w-5xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- آواتار -->
<div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center">
  <img src="<?php echo $avatarPath; ?>" class="w-32 h-32 rounded-full border-2 object-cover mb-4">
  <form action="upload-avatar.php" method="POST" enctype="multipart/form-data" class="space-y-3 w-full">
    <input type="file" name="avatar" class="w-full text-sm border px-4 py-2 rounded cursor-pointer bg-gray-50">
    <div class="flex gap-2">
        <a href="remove-avatar.php" class="flex-1 bg-red-100 hover:bg-red-200 text-red-600 text-sm px-4 py-2 rounded text-center">حذف آواتار</a>
      <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">ذخیره آواتار جدید</button>
    </div>
  </form>
</div>
<!-- فرم‌ها -->
<div class="md:col-span-2 space-y-8">
  <!-- اطلاعات کاربر -->
  <div class="bg-white rounded-2xl shadow-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">اطلاعات کاربر</h2>
    <form action="update-name.php" method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">نام کامل</label>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"
               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 text-sm">
      </div>
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">شماره تماس</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"
               pattern="^09\d{9}$" title="شماره تماس باید با 09 شروع شود و 11 رقم باشد"
               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 text-sm">
      </div>
      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded text-sm">ذخیره اطلاعات</button>
    </form>
  </div>
  <!-- تغییر رمز عبور -->
  <div class="bg-white rounded-2xl shadow-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">تغییر رمز عبور</h2>
    <form action="change-password.php" method="POST" class="space-y-4">
      <div class="relative">
        <input type="password" name="current_password" id="current_password" placeholder="رمز فعلی"
               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 text-sm" required>
        <span onclick="togglePasswordVisibility('current_password')" class="absolute left-3 top-2.5 cursor-pointer text-gray-500">👁️</span>
      </div>
      <div class="relative">
        <input type="password" name="new_password" id="new_password" placeholder="رمز جدید"
               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 text-sm" required>
        <span onclick="togglePasswordVisibility('new_password')" class="absolute left-3 top-2.5 cursor-pointer text-gray-500">👁️</span>
      </div>
      <div class="relative">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="تکرار رمز جدید"
               class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 text-sm" required>
        <span onclick="togglePasswordVisibility('confirm_password')" class="absolute left-3 top-2.5 cursor-pointer text-gray-500">👁️</span>
      </div>
      <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded text-sm">ذخیره رمز جدید</button>
    </form>
  </div>
  <div class="mt-6 text-center">
    <div class="inline-block bg-blue-100 px-4 py-2 rounded shadow">
      <a href="../user.php" class="text-blue-700 text-sm hover:underline">⬅ بازگشت به داشبورد</a>
    </div>
  </div>
</div>
</div>
<script>
  // 🎯 اعتبارسنجی فرم تغییر رمز عبور
  const changePasswordForm = document.querySelector("form[action='change-password.php']");
  if (changePasswordForm) {
    const errorBox = document.createElement("div");
    errorBox.style.color = "red";
    errorBox.style.marginTop = "10px";
    changePasswordForm.appendChild(errorBox);

    changePasswordForm.addEventListener("submit", function(e) {
      const current = this.current_password.value.trim();
      const newPass = this.new_password.value.trim();
      const confirm = this.confirm_password.value.trim();

      let errors = [];

      if (!current || !newPass || !confirm) {
        errors.push("همه فیلدهای رمز عبور الزامی هستند.");
      }

      if (current.length > 0 && current.length < 8) {
        errors.push("رمز فعلی باید حداقل ۸ کاراکتر باشد.");
      }

      if (newPass.length > 0 && newPass.length < 8) {
        errors.push("رمز جدید باید حداقل ۸ کاراکتر باشد.");
      }

      const persianPattern = /[\u0600-\u06FF]/;
      if (persianPattern.test(current)) {
        errors.push("رمز فعلی نباید شامل حروف فارسی باشد.");
      }
      if (persianPattern.test(newPass)) {
        errors.push("رمز جدید نباید شامل حروف فارسی باشد.");
      }

      if (newPass !== confirm) {
        errors.push("رمز جدید با تکرار آن مطابقت ندارد.");
      }

      if (errors.length > 0) {
        e.preventDefault();
        errorBox.innerHTML = errors.join("<br>");
      } else {
        errorBox.innerHTML = "";
      }
    });
  }

  // 📞 اعتبارسنجی شماره تماس در فرم اطلاعات کاربر
  const updateInfoForm = document.querySelector("form[action='update-name.php']");
  if (updateInfoForm) {
    const phoneInput = updateInfoForm.querySelector("input[name='phone']");
    const errorBoxPhone = document.createElement("div");
    errorBoxPhone.style.color = "red";
    errorBoxPhone.style.marginTop = "5px";
    phoneInput.insertAdjacentElement("afterend", errorBoxPhone);

    updateInfoForm.addEventListener("submit", function(e) {
      const phone = phoneInput.value.trim();
      const phonePattern = /^09\d{9}$/;
      let phoneErrors = [];

      if (!phone) {
        phoneErrors.push("شماره تماس الزامی است.");
      } else if (!phonePattern.test(phone)) {
        phoneErrors.push("شماره تماس باید با 09 شروع شده و 11 رقم باشد.");
      }

      if (phoneErrors.length > 0) {
        e.preventDefault();
        errorBoxPhone.innerHTML = phoneErrors.join("<br>");
      } else {
        errorBoxPhone.innerHTML = "";
      }
    });
  }
</script>

</body>
</html>