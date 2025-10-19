<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ورود / ثبت‌نام</title>
  <link href="css/tailwind.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>

<body class="bg-blue-50 font-sans">
  <?php include("assets/header.php"); ?>


  <main class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white shadow-xl rounded-xl w-full max-w-md p-8 space-y-6 form-container">

      <h2 id="formTitle" class="text-2xl font-bold text-center text-gray-800">ورود به حساب</h2>

      <?php if (isset($_SESSION['error'])): ?>
  <div class="bg-red-100 text-red-700 p-3 rounded text-center mb-4">
    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
  </div>
<?php elseif (isset($_SESSION['success'])): ?>
  <div class="bg-green-100 text-green-700 p-3 rounded text-center mb-4">
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>



   

      <!-- فرم ورود -->
<form id="loginForm" action="auth/login.php" method="post" class=" space-y-4 text-center">
  <input type="email" name="email" placeholder="ایمیل" class="form-input" required>
  <input type="password" name="password" placeholder="رمز عبور" class="form-input" required>
  <button type="submit" class="text-sm font-semibold text-blue-600 border border-blue-600 px-3 py-1 rounded hover:bg-blue-600 hover:text-white transition">ورود</button>
  <!-- ارور فرم ورود -->
<div id="loginError" class="text-red-600 text-sm mt-2 text-center"></div>


</form>
      <!-- فرم ثبت‌نام -->
    <form id="signupForm" action="auth/register.php" method="post" class="space-y-4 hidden text-center">
  <input type="text" name="name" placeholder="نام کامل" class="form-input" required>
  <input type="email" name="email" placeholder="ایمیل" class="form-input" required>
  <input type="text" name="phone" placeholder="شماره تماس" class="form-input" required>
  <input type="password" name="password" placeholder="رمز عبور" class="form-input" required>
  <input type="password" name="confirm" placeholder="تکرار رمز عبور" class="form-input" required>
  <button type="submit" class=" text-sm font-semibold text-blue-600 border border-blue-600 px-3 py-1 rounded hover:bg-blue-600 hover:text-white transition">ثبت‌نام</button>
</form>

      <!-- دکمه سوییچ -->
      <div class="text-center text-sm text-gray-600">
        <span id="toggleText">حساب کاربری ندارید؟</span>
        <button id="toggleBtn" class="text-blue-600 font-semibold hover:underline">ثبت‌نام کنید</button>
        
<!-- ارور فرم ثبت‌نام -->
<div id="signupError" class="text-red-600 text-sm mt-2 text-center"></div>
      </div>
    </div>
  </main>

  <?php include("assets/footer.php"); ?>

  <script>
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    const toggleBtn = document.getElementById("toggleBtn");
    const toggleText = document.getElementById("toggleText");
    const formTitle = document.getElementById("formTitle");

    toggleBtn.addEventListener("click", () => {
      if (signupForm.classList.contains("hidden")) {
        // نمایش فرم ثبت‌نام با انیمیشن
        loginForm.classList.add("fade-out");
        setTimeout(() => {
          loginForm.classList.add("hidden");
          signupForm.classList.remove("hidden");
          signupForm.classList.add("fade-in");
          loginForm.classList.remove("fade-out");

          formTitle.textContent = "ساخت حساب کاربری";
          toggleText.textContent = "قبلاً حساب دارید؟";
          toggleBtn.textContent = "ورود کنید";
        }, 300);
      } else {
        // نمایش فرم ورود با انیمیشن
        signupForm.classList.add("fade-out");
        setTimeout(() => {
          signupForm.classList.add("hidden");
          loginForm.classList.remove("hidden");
          loginForm.classList.add("fade-in");
          signupForm.classList.remove("fade-out");

          formTitle.textContent = "ورود به حساب";
          toggleText.textContent = "حساب کاربری ندارید؟";
          toggleBtn.textContent = "ثبت‌نام کنید";
        }, 300);
      }
    });

  // اعتبارسنجی فرم ورود
  loginForm.addEventListener("submit", function (e) {
    const email = loginForm.email.value.trim();
    const password = loginForm.password.value;
    const errorBox = document.getElementById("loginError");
    let errors = [];

    // خالی بودن فیلدها
    if (!email || !password) {
      errors.push("ایمیل و رمز عبور الزامی است.");
    }

    // فرمت ایمیل
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (email && !emailPattern.test(email)) {
      errors.push("فرمت ایمیل معتبر نیست.");
    }

    // حداقل طول پسورد
    if (password && password.length < 8) {
      errors.push("رمز عبور باید حداقل ۸ کاراکتر باشد.");
    }

    // بررسی حروف فارسی در پسورد
    const persianPattern = /[\u0600-\u06FF]/;
    if (persianPattern.test(password)) {
      errors.push("رمز عبور نباید شامل حروف فارسی باشد.");
    }

    if (errors.length > 0) {
      e.preventDefault();
      errorBox.innerHTML = errors.join("<br>");
    } else {
      errorBox.innerHTML = "";
    }
  });

  // اعتبارسنجی فرم ثبت‌نام
  signupForm.addEventListener("submit", function (e) {
    const name = signupForm.name.value.trim();
    const email = signupForm.email.value.trim();
    const phone = signupForm.phone.value.trim();
    const password = signupForm.password.value;
    const confirm = signupForm.confirm.value;
    const errorBox = document.getElementById("signupError");

    let errors = [];

    if (!name || !email || !phone || !password || !confirm) {
      errors.push("همه فیلدها باید پر شوند.");
    }

    // فرمت ایمیل
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (email && !emailPattern.test(email)) {
      errors.push("ایمیل وارد شده معتبر نیست.");
    }

    // شماره تلفن: فقط اعداد، حداقل 10 رقم
    const phonePattern = /^[0-9]{10,15}$/;
    if (phone && !phonePattern.test(phone)) {
      errors.push("شماره تلفن باید فقط عدد باشد و حداقل ۱۰ رقم.");
    }

    // حداقل طول پسورد
    if (password.length < 8) {
      errors.push("رمز عبور باید حداقل ۸ کاراکتر باشد.");
    }

    // حروف فارسی در پسورد
    const persianPattern = /[\u0600-\u06FF]/;
    if (persianPattern.test(password)) {
      errors.push("رمز عبور نباید شامل حروف فارسی باشد.");
    }

    // مطابقت پسورد
    if (password !== confirm) {
      errors.push("رمز عبور و تکرار آن یکسان نیستند.");
    }

    if (errors.length > 0) {
      e.preventDefault();
      errorBox.innerHTML = errors.join("<br>");
    } else {
      errorBox.innerHTML = "";
    }
  });



    
  </script>
  
  <style>
    .fade-in {
  animation: fadeIn 0.3s ease forwards;
}
.fade-out {
  animation: fadeOut 0.3s ease forwards;
}

@keyframes fadeIn {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
  0% { opacity: 1; transform: translateY(0); }
  100% { opacity: 0; transform: translateY(20px); }
}
  </style>
</body>
</html>