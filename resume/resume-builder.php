<?php   
  
session_start();  
include("../php/db.php");  
$resume = [];  

if (isset($_GET['id'])) {  
  $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ?");  
  $stmt->execute([$_GET['id']]);  
  $resume = $stmt->fetch();
}  

$template_id = $_GET['template'] ?? 1;  

if ($template_id == 1) {  
  // قالب رایگان
} else {  
  if (!isset($_SESSION['user_id'])) {  
    header("Location: ../auth/login.php");  
    exit("برای استفاده از این قالب، ابتدا وارد شوید.");  
  }  

  $user_id = $_SESSION['user_id'];  
  $query = $pdo->prepare("SELECT subscription, subscription_expires_at FROM users WHERE id = ?");  
  $query->execute([$user_id]);  
  $user = $query->fetch();  

  $hasActiveSubscription = false;  
  if ($user && $user['subscription'] === 'paid' && strtotime($user['subscription_expires_at']) > time()) {  
    $hasActiveSubscription = true;  
  }  

  if (!$hasActiveSubscription) {  
    header("Location: ../buy.php?template=$template_id");  
    exit;  
  }  
}  
?>  <!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ساخت رزومه</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
    #email-error, #phone-error, #national_code-error, #photo-error {
      display: none;
      margin-top: 4px;
    }
    input.error, textarea.error, select.error {
      border-color: #e53e3e;
    }
  </style>
</head>
<body class="bg-blue-50 font-sans">
<?php include("../assets/header.php"); ?>
<main class="max-w-5xl mx-auto mt-10 bg-white p-8 shadow-xl rounded-xl">
  <h2 class="text-2xl font-bold text-center text-blue-700 mb-8">فرم ساخت رزومه حرفه‌ای</h2>
  <form action="process.php" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <input type="hidden" name="template_id" value="<?= $template_id ?>"><!-- اطلاعات شخصی -->
<div>
  <label class="block font-semibold mb-1">نام و نام خانوادگی</label>
  <input type="text" name="fullname" required class="form-input">
</div>

<div>
  <label class="block font-semibold mb-1">ایمیل</label>
  <input type="email" id="email" name="email" required class="form-input">
  <div id="email-error" class="text-red-600 text-sm mt-1 hidden">لطفاً یک ایمیل معتبر وارد کنید (فقط حروف انگلیسی).</div>
</div>

<div>
  <label class="block font-semibold mb-1">شماره تماس</label>
  <input type="text" id="phone" name="phone" required class="form-input">
  <div id="phone-error" class="text-red-600 text-sm mt-1 hidden">لطفاً یک شماره تماس معتبر وارد کنید.</div>
</div>

<div>
  <label class="block font-semibold mb-1">کد ملی</label>
  <input type="text" id="national_code" name="national_code" required class="form-input">
  <div id="national_code-error" class="text-red-600 text-sm mt-1 hidden">لطفاً یک کد ملی معتبر وارد کنید.</div>
</div>

<div>
  <label class="block font-semibold mb-1">استان</label>
  <input type="text" name="province" class="form-input">
</div>

<div>
  <label class="block font-semibold mb-1">شهر</label>
  <input type="text" name="city" class="form-input">
</div>

<div class="md:col-span-2">
  <label class="block font-semibold mb-1">آدرس کامل</label>
  <textarea name="address" rows="2" class="form-textarea"></textarea>
</div>

<div>
  <label class="block font-semibold mb-1">تاریخ تولد</label>
  <input type="text" name="birth" class="form-input">
</div>

<div>
  <label class="block font-semibold mb-1">جنسیت</label>
  <div class="space-x-4 rtl:space-x-reverse">
    <label><input type="radio" name="gender" value="زن" class="mr-1" checked> زن</label>
    <label><input type="radio" name="gender" value="مرد" class="mr-1"> مرد</label>
  </div>
</div>

<div>
  <label class="block font-semibold mb-1">وضعیت تأهل</label>
  <select name="marital" class="form-input">
    <option value="مجرد">مجرد</option>
    <option value="متأهل">متأهل</option>
  </select>
</div>

<div>
  <label class="block font-semibold mb-1">وضعیت خدمت</label>
  <input type="text" id="military-field" name="military" class="form-input">
</div>


<!-- زبان‌ها -->
<div class="md:col-span-2">
  <label class="block font-semibold mb-2 text-lg">زبان‌های خارجی</label>
  <div id="language-container" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="text" name="language[]" placeholder="مثلاً: انگلیسی" class="form-input" />
      <select name="language_level[]" class="form-input">
        <option value="مقدماتی">مقدماتی</option>
        <option value="متوسط">متوسط</option>
        <option value="پیشرفته">پیشرفته</option>
        <option value="تسلط کامل">تسلط کامل</option>
      </select>
    </div>
  </div>
  <button type="button" onclick="addLanguageField()" class="mt-2 text-blue-600 hover:underline">+ افزودن زبان جدید</button>
</div>


<!-- تصویر -->
<div class="md:col-span-2">
  <label class="block font-semibold mb-1">عکس پروفایل</label>
  <input type="file" id="photo" name="photo" accept="image/*" class="form-input">
  <div id="photo-error" class="text-red-600 text-sm mt-1 hidden">لطفاً یک فرمت عکس معتبر وارد کنید.</div>
</div>

<!-- درباره من -->
<div class="md:col-span-2">
  <label class="block font-semibold mb-1">درباره من</label>
  <textarea name="about" rows="3" class="form-textarea"></textarea>
</div>



<!-- سوابق تحصیلی (قابل نمایش با کلیک) -->
<div class="md:col-span-2">  
  <button type="button" onclick="toggleEducation()" class="text-blue-700 underline mb-4">+ افزودن سابقه تحصیلی</button>  
  <div id="education-section" style="display:none;">  
    <div id="educationContainer" class="space-y-4">  
      <div class="border p-4 rounded-md shadow-sm bg-gray-50">  
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">  
          <input type="text" name="major[]" placeholder="رشته تحصیلی" class="form-input" />  
          <input type="text" name="university[]" placeholder="نام دانشگاه" class="form-input" />  
          <input type="text" name="graduation_year[]" placeholder="سال فارغ‌التحصیلی" class="form-input" />  
        </div>  
        <div class="text-left mt-2">  
          <button type="button" onclick="removeEducation(this)" class="text-red-600 text-sm hover:underline">حذف</button>  
        </div>  
      </div>  
    </div>  
    <button type="button" onclick="addEducation()" class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">+ سابقه جدید</button>  
  </div>  
</div>

<!-- مهارت‌ها -->  
<div class="md:col-span-2">
  <label class="block font-semibold mb-1">مهارت‌ها (با کاما جدا کن)</label>
  <input type="text" name="skills" class="form-input">
</div>

<!-- نرم‌افزارها -->  
<div class="md:col-span-2">  
  <label class="block font-semibold mb-2 text-lg">نرم‌افزارهایی که بلدی</label>  
  <input type="text" id="software-input" placeholder="نام نرم‌افزار را تایپ کنید و Enter بزنید" class="form-input mb-2" onkeydown="handleSoftwareInput(event)" />  
  <div id="software-tags" class="flex flex-wrap gap-2"></div>  
  <input type="hidden" name="software" id="software-hidden" />  
</div>  

<!-- علاقه‌مندی‌ها -->  
<div class="md:col-span-2">
  <label class="block font-semibold mb-1">علاقه‌مندی‌ها</label>
  <textarea name="interests" rows="3" class="form-textarea"></textarea>
</div>  

<!-- دوره‌ها -->  
<div class="md:col-span-2">  
  <label class="block font-semibold mb-2 text-lg">دوره‌های آموزشی</label>  
  <div id="course-container" class="space-y-4">  
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">  
      <input type="text" name="course_name[]" placeholder="نام دوره" class="form-input" />  
      <input type="text" name="institute[]" placeholder="آموزشگاه" class="form-input" />  
      <input type="text" name="year[]" placeholder="سال" class="form-input" />  
    </div>  
  </div>  
  <button type="button" onclick="addCourseField()" class="mt-2 text-blue-600 hover:underline">+ افزودن دوره جدید</button>  
</div>  

<!-- سوابق شغلی (قابل نمایش با کلیک) -->  
<div class="md:col-span-2">  
  <button type="button" onclick="toggleExperience()" class="text-blue-700 underline mb-4">+ افزودن سابقه شغلی</button>  
  <div id="experience-section" style="display:none;">  
    <div id="experienceContainer" class="space-y-4">  
      <div class="border p-4 rounded-md shadow-sm bg-gray-50">  
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">  
          <input type="text" name="job_title[]" placeholder="عنوان شغلی" class="form-input" />  
          <input type="text" name="company[]" placeholder="نام شرکت" class="form-input" />  
          <input type="text" name="from_date[]" placeholder="از سال" class="form-input" />  
          <input type="text" name="to_date[]" placeholder="تا سال" class="form-input" />  
        </div>  
        <textarea name="job_description[]" placeholder="توضیحات" class="form-textarea mt-2 w-full"></textarea>  
        <div class="text-left mt-2">  
          <button type="button" onclick="removeExperience(this)" class="text-red-600 text-sm hover:underline">حذف</button>  
        </div>  
      </div>  
    </div>  
    <button type="button" onclick="addExperience()" class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">+ سابقه جدید</button>  
  </div>  
</div>  

<!-- دکمه ارسال -->  
<div class="md:col-span-2 text-center mt-6">  
  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded shadow">ساخت رزومه</button>  
</div>
  </form>
</main>

<script>






  function addCourseField() {
    const container = document.getElementById('course-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-3 gap-4';
    div.innerHTML = `
      <input type="text" name="course_name[]" placeholder="نام دوره" class="form-input" />
      <input type="text" name="institute[]" placeholder="آموزشگاه" class="form-input" />
      <input type="text" name="year[]" placeholder="سال" class="form-input" />
    `;
    container.appendChild(div);
  }

  function addLanguageField() {
    const container = document.getElementById('language-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';
    div.innerHTML = `
      <input type="text" name="language[]" placeholder="مثلاً: فرانسه" class="form-input" />
      <select name="language_level[]" class="form-input">
        <option value="مقدماتی">مقدماتی</option>
        <option value="متوسط">متوسط</option>
        <option value="پیشرفته">پیشرفته</option>
        <option value="تسلط کامل">تسلط کامل</option>
      </select>
    `;
    container.appendChild(div);
  }

  function toggleExperience() {
    const section = document.getElementById('experience-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
  }

  function addExperience() {
    const container = document.getElementById('experienceContainer');
    const html = `
      <div class="border p-4 rounded-md shadow-sm bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="text" name="job_title[]" placeholder="عنوان شغلی" class="form-input" />
          <input type="text" name="company[]" placeholder="نام شرکت" class="form-input" />
          <input type="text" name="from_date[]" placeholder="از سال" class="form-input" />
          <input type="text" name="to_date[]" placeholder="تا سال" class="form-input" />
        </div>
        <textarea name="job_description[]" placeholder="توضیحات" class="form-textarea mt-2 w-full"></textarea>
        <div class="text-left mt-2">
          <button type="button" onclick="removeExperience(this)" class="text-red-600 text-sm hover:underline">حذف</button>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
  }

  function removeExperience(btn) {
    btn.closest('div.border').remove();
  }

  // نرم‌افزارها
  const softwareInput = document.getElementById('software-input');
  const softwareTags = document.getElementById('software-tags');
  const softwareHidden = document.getElementById('software-hidden');
  let softwareList = [];

  function handleSoftwareInput(e) {
    if (e.key === 'Enter' && softwareInput.value.trim()) {
      e.preventDefault();
      const value = softwareInput.value.trim();
      if (!softwareList.includes(value)) {
        softwareList.push(value);
        renderSoftwareTags();
        softwareInput.value = '';
      }
    }
  }

  function renderSoftwareTags() {
    softwareTags.innerHTML = '';
    softwareList.forEach((name, index) => {
      const tag = document.createElement('span');
      tag.className = 'bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm flex items-center gap-2';
      tag.innerHTML = `${name} 
        <button onclick="removeSoftware(${index})" class="text-red-500 hover:text-red-700">&times;</button>`;
      softwareTags.appendChild(tag);
    });
    softwareHidden.value = softwareList.join(', ');
  }

  function removeSoftware(index) {
    softwareList.splice(index, 1);
    renderSoftwareTags();
  }

  function toggleEducation() {
    const section = document.getElementById('education-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
  }

  function addEducation() {
    const container = document.getElementById('educationContainer');
    const html = `
      <div class="border p-4 rounded-md shadow-sm bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="text" name="major[]" placeholder="رشته تحصیلی" class="form-input" />
          <input type="text" name="university[]" placeholder="نام دانشگاه" class="form-input" />
          <input type="text" name="graduation_year[]" placeholder="سال فارغ‌التحصیلی" class="form-input" />
        </div>
        <div class="text-left mt-2">
          <button type="button" onclick="removeEducation(this)" class="text-red-600 text-sm hover:underline">حذف</button>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
  }

  function removeEducation(btn) {
    btn.closest('div.border').remove();
  }

  // مخفی‌کردن فیلد خدمت برای زنان
  document.querySelectorAll('input[name="gender"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      const militaryField = document.getElementById('military-field');
      if (this.value === 'زن') {
        militaryField.style.display = 'none';
        militaryField.value = '';
      } else {
        militaryField.style.display = 'block';
      }
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("email-error");

    const phoneInput = document.getElementById("phone");
    const phoneError = document.getElementById("phone-error");

    const nationalCodeInput = document.getElementById("national_code");
    const nationalCodeError = document.getElementById("national_code-error");

    const photoInput = document.getElementById("photo");
    const photoError = document.getElementById("photo-error");

    // تابع اعتبارسنجی کد ملی
    function checkIranianNationalCode(input) {
      if (!/^\d{10}$/.test(input)) return false;
      const check = +input[9];
      const sum = input.split("").slice(0, 9).reduce((acc, digit, i) => acc + digit * (10 - i), 0) % 11;
      return (sum < 2 && check == sum) || (sum >= 2 && check == 11 - sum);
    }

    emailInput.addEventListener("blur", function () {
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!emailRegex.test(emailInput.value)) {
        emailError.textContent = "لطفاً یک ایمیل معتبر وارد کنید (فقط حروف انگلیسی).";
        emailError.style.display = "block";
        emailInput.classList.add("error");
      } else {
        emailError.textContent = "";
        emailError.style.display = "none";
        emailInput.classList.remove("error");
      }
    });

    phoneInput.addEventListener("blur", function () {
      const phoneRegex = /^09\d{9}$/;
      if (!phoneRegex.test(phoneInput.value)) {
        phoneError.textContent = "شماره موبایل معتبر نیست! باید با 09 شروع شده و 11 رقم عدد باشد.";
        phoneError.style.display = "block";
        phoneInput.classList.add("error");
      } else {
        phoneError.textContent = "";
        phoneError.style.display = "none";
        phoneInput.classList.remove("error");
      }
    });

    nationalCodeInput.addEventListener("blur", function () {
      const code = nationalCodeInput.value;
      if (!(/^\d{10}$/.test(code) && checkIranianNationalCode(code))) {
        nationalCodeError.textContent = "کد ملی وارد شده معتبر نیست!";
        nationalCodeError.style.display = "block";
        nationalCodeInput.classList.add("error");
      } else {
        nationalCodeError.textContent = "";
        nationalCodeError.style.display = "none";
        nationalCodeInput.classList.remove("error");
      }
    });

    photoInput.addEventListener("change", function () {
      const file = photoInput.files[0];
      if (file) {
        const allowedTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp"];
        if (!allowedTypes.includes(file.type)) {
          photoError.textContent = "فرمت فایل باید jpg یا png یا webp باشد.";
          photoError.style.display = "block";
          photoInput.classList.add("error");
          photoInput.value = "";
        } else {
          photoError.textContent = "";
          photoError.style.display = "none";
          photoInput.classList.remove("error");
        }
      } else {
        photoError.textContent = "";
        photoError.style.display = "none";
        photoInput.classList.remove("error");
      }
    });
  });
</script>
<?php include("../assets/footer.php"); ?>
</body>
</html>