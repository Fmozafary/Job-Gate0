<?php if (isset($_GET['call_success'])): ?>
  <p class="text-green-600 mb-4">درخواست تماس شما با موفقیت ثبت شد.</p>
<?php elseif (isset($_GET['call_error'])): ?>
  <p class="text-red-600 mb-4">لطفاً نام و شماره تماس را وارد کنید.</p>
<?php endif; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تماس با ما | Job Gate</title>
  <link href="css/tailwind.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<?php include("assets/header.php");?>
<body class="font-sans">

  <!-- تماس با ما -->
  <section class=" py-20">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-start gap-12">

      <!-- متن سمت راست -->
      <div class="md:w-1/2">
        <h2 class="text-3xl font-bold text-blue-700 mb-4">با ما در تماس باشید</h2>
        <p class="text-gray-700 leading-7 text-sm mb-6">
          اگر سوال، پیشنهاد یا مشکلی در ساخت رزومه یا استفاده از سایت دارید، تیم پشتیبانی ما آماده پاسخ‌گویی سریع به شماست. لطفاً فرم مقابل را پر کرده یا از بخش درخواست تماس، شماره خود را برای ارتباط وارد کنید.
        </p>
        <p class="text-gray-700 leading-7 text-sm mb-2">
          ایمیل: jobgate@gmail.com
        </p>
        <p class="text-gray-700 leading-7 text-sm">
          تلفن: 09360321774
        </p>
      </div>

      <!-- فرم تماس سمت چپ -->
      <div class="md:w-1/2">
        <section class="bg- py-10">
  <div class="max-w-xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4"> نظرات و پیشنهادات شما</h2>
    <form action="submit-feadback.php" method="post" class="space-y-4 bg-gray-50 p-6 rounded-xl shadow">
      <input type="text" name="fullname" placeholder="نام کامل" required class="w-full border rounded px-4 py-2">
      <input type="email" name="email" placeholder="ایمیل" required class="w-full border rounded px-4 py-2">
      <textarea name="message" rows="4" placeholder="پیام شما..." required class="w-full border rounded px-4 py-2"></textarea>
     
    
  </div>
</section>
          <!-- دکمه‌ها کنار هم -->
          <div class="flex gap-4">
            <button type="submit" class="btn-primary">ارسال پیام</button>
            <button type="button" onclick="togglePhoneForm()" class="btn-secondary">درخواست تماس تلفنی</button>
          </div>
        </form>

        <!-- فرم پشتیبانی تلفنی (نمایش بعد از کلیک) -->
     <div id="phoneForm" class="space-y-4 mt-6 hidden">
  <form method="POST" action="php/save-call.php" class="space-y-4">
    <input type="text" name="fullname" class="form-input" placeholder="نام شما" required>
    <input type="tel" name="phone" class="form-input" placeholder="شماره تماس (مثلاً 09121234567)" required>
    <input type="text" name="subject" class="form-input" placeholder="موضوع تماس">
    <button type="submit" class="btn-primary">ثبت درخواست</button>
  </form>
</div>
      </div>

    </div>
  </section>

  <script>
    function togglePhoneForm() {
      const form = document.getElementById("phoneForm");
      form.classList.toggle("hidden");
    }
  </script>
<div id="header"></div>
<!-- محتوای اصلی صفحه -->
<div id="footer"></div>

<?php include("assets/footer.php");?>
</body>
</html>