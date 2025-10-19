
<!DOCTYPE html>  
<html lang="fa" dir="rtl">  
<head>  
  <meta charset="UTF-8" />  
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />  
  <title>Job Gate | دروازه‌ای به شغل رویایی‌ات</title>  
  <link href="css/tailwind.min.css" rel="stylesheet">  
  <link href="css/style.css" rel="stylesheet">  
  <style>  
   .slider-container {
  overflow: hidden;
  width: 100%;
  direction: ltr; /* مهم برای اسکرول افقی درست */
}

.slider-track {
  display: flex;
  width: max-content;
  animation: scrollLoop 40s linear infinite;
}

.slide {

  flex: 0 0 auto;
  margin: 0 0.5rem;  /* فاصله افقی بیشتر بین اسلایدها */
  padding: 1.50em;  /* فاصله داخلی بین مرز و عکس */
  text-align: center;
  box-sizing: border-box; /* تا padding داخل اندازه کل لحاظ بشه */
/* می‌تونی پس‌زمینه بدی تا فضای داخلی دیده بشه */
  border-radius: 0.75rem; /* اگر دوست داری گوشه گرد داشته باشه */
 

}
.slide img {
  width: 270px;
  height: 350px;  /* اینجا px فراموش شده بود */
  border-radius: 0.75rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background-color: white; 
  object-fit: contain; /* اگه میخوای کشیده بشن بذار contain، اگه بخوای پر کنه از cover استفاده کن */
}

.slide-box {
  background-color: #f1f5f9;
  padding: 0.5rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  color: #1e293b;
  font-weight: 500;
  margin-top: 0.5rem;
  transition: background-color 0.3s ease;
}

.slide-box:hover {
  background-color: #e2e8f0;
}

@keyframes scrollLoop {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

    #templateModal {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 50;
      display: none; /* پیش‌فرض مخفی */
    }

    #templateModal.flex {
      display: flex;
    }

    .modal-box {
      background-color: white;
      border-radius: 1rem;
      padding: 2rem;
      max-width: 90vw;
      max-height: 90vh;
      overflow-y: auto;
      width: 800px;
      scrollbar-width: thin;
    }
    .modal-box::-webkit-scrollbar {
      width: 6px;
    }
    .modal-box::-webkit-scrollbar-thumb {
      background-color: #60a5fa;
      border-radius: 4px;
    }
    body.modal-open {
      overflow: hidden;
      position: fixed;
      width: 100%;
      /* حفظ موقعیت صفحه */
      top: 0;
      left: 0;
    }
    .custom-scroll::-webkit-scrollbar {
      width: 8px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
      background-color: #60a5fa;
      border-radius: 4px;
    }
    .custom-scroll {
      scrollbar-width: thin;
      scrollbar-color: #60a5fa transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
      background-color: red !important;
    }
    #modalContact{
      max-height: 90vh;
      overflow:auto;
    }



    .modal-box img {
  width: 100%;
  max-width: 200; /* یا هر سایز دلخواه */
  height: 300px;     /* ارتفاع ثابت */
  object-fit: cover; /* یا contain بسته به نیازت */
  border-radius: 0.5rem;
  display: block;
  margin: 0 auto;
}
  </style>  
</head>  
<body class="bg-gray-50 font-sans overflow-x-hidden">    
  <!-- هدر -->   
  <?php include("assets/header.php");

 $from_dashboard = false; include("template-modal.php"); 
  
  ?> 

  <div id="header"></div>    

  <!-- بنر معرفی -->    
  <section class="bg-gradient-to-l from-blue-500 to-blue-700 text-white py-20">  
    <div class="container mx-auto px-4 text-center">  
      <h1 class="text-4xl font-bold mb-4">وقتشه بدرخشی!</h1>  
      <p class="text-lg mb-8 max-w-2xl mx-auto">  
        با جاب گیت، رزومه ساختن نه‌تنها آسونه، بلکه لذت‌بخشه! فقط چند دقیقه وقت بذار، رزومه‌تو بساز، دانلود کن، و اولین قدم به سمت شغل رؤیایی‌تو بردار.  
      </p>  
      <a onclick="openTemplateModal()" class="open-template-modal bg-white text-blue-600 px-6 py-3 rounded-full font-semibold shadow-md hover:bg-blue-100 transition">ساخت رزومه</a>  
    </div>  
  </section>    

  <!-- اسلایدر قالب رزومه‌ها -->    
          <section class="bg-gray-100 py-10">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-xl font-bold mb-4 text-center">قالب‌های رزومه</h2>
    <div class="slider-container overflow-hidden relative">
      <div class="slider-track">
        <!-- یک سری قالب -->
        <div class="slide"><img src="assets/images/1.png"><div class="slide-box">قالب اداری(رایکان) </div></div>
        <div class="slide"><img src="assets/images/2.png"><div class="slide-box">قالب ساده </div></div>
        <div class="slide"><img src="assets/images/3.png"><div class="slide-box"> قالب شیک</div></div>
        <div class="slide"><img src="assets/images/4.png"><div class="slide-box"> قالب خلاقانه</div></div>
        <div class="slide "><img src="assets/images/5.png"><div class="slide-box"> قالب انگلیسی</div></div>
        <!-- کپی سری اول برای لوپ -->
       <div class="slide"><img src="assets/images/1.png"><div class="slide-box">قالب اداری(رایکان) </div></div>
        <div class="slide"><img src="assets/images/2.png"><div class="slide-box">قالب ساده </div></div>
        <div class="slide"><img src="assets/images/3.png"><div class="slide-box"> قالب شیک</div></div>
        <div class="slide"><img src="assets/images/4.png"><div class="slide-box"> قالب خلاقانه</div></div>
        <div class="slide "><img src="assets/images/5.png"><div class="slide-box"> قالب انگلیسی</div></div>
      </div>
    </div>
  </div>
</section>
      


  <!-- چرا جاب گیت؟ -->  
  <section class="py-16 bg-white">  
    <div class="max-w-6xl mx-auto px-6">  
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">چرا Job Gate؟</h2>  
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">  
        <div class="text-center p-6 border rounded-xl shadow hover:shadow-lg transition">  
          <h3 class="text-xl font-semibold mb-2 text-gray-800">رزومه‌ساز حرفه‌ای</h3>  
          <p class="text-gray-600 text-sm">ساخت رزومه‌ با قالب‌های متنوع و قابل دانلود تنها با چند کلیک.</p>  
        </div>  
        <div class="text-center p-6 border rounded-xl shadow hover:shadow-lg transition">  
          <h3 class="text-xl font-semibold mb-2 text-gray-800">ارتباط با کارشناس</h3>  
          <p class="text-gray-600 text-sm">کارشناسان ما رزومه‌ات رو بررسی و به شرکت‌های مناسب معرفی می‌کنن.</p>  
        </div>  
        <div class="text-center p-6 border rounded-xl shadow hover:shadow-lg transition">  
          <h3 class="text-xl font-semibold mb-2 text-gray-800">امنیت و حریم خصوصی</h3>  
          <p class="text-gray-600 text-sm">اطلاعاتت کاملاً محفوظ می‌مونه، چون امنیت ما اولویته.</p>  
        </div>  
      </div>  
    </div>  
  </section>  

  <!-- امکانات حرفه‌ای -->  
  <section class="bg-gray-50 py-16">  
    <div class="max-w-6xl mx-auto px-6 md:flex md:items-center">  
      <div class="md:w-1/2 mb-10 md:mb-0">  
        <img src="assets/images/file_00000000fa1461f4b91582e60ccc0617.png" style="height: 500px; width:500px;" alt="job gate features" class="rounded-xl shadow-lg">  
      </div>  
      <div class="md:w-1/2 md:pl-12">  
        <h2 class="text-3xl font-bold text-gray-800 mb-6">امکانات حرفه‌ای در اختیار شما</h2>  
        <ul class="space-y-4 text-gray-700 text-sm leading-6">  
          <li>✔ ساخت رزومه با فرمت PDF و امکان ویرایش سریع</li>  
          <li>✔ ارسال مستقیم رزومه به شرکت‌ها و دریافت بازخورد</li>  
          <li>✔ پنل کاربری حرفه‌ای برای مدیریت رزومه‌ها و درخواست‌ها</li>  
          <li>✔ پشتیبانی آنلاین و پاسخ‌گویی سریع از سوی کارشناسان</li>  
        </ul>  
      </div>  
    </div>  
  </section>  



  
  <!-- نظرات کاربران -->  
<?php
include("php/db.php");
$comments = $pdo->query("SELECT * FROM feedback WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
?>

<section class="bg-white py-16">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">نظرات کاربران</h2>

    <div class="grid md:grid-cols-3 gap-6 justify-center">
      <?php foreach ($comments as $c): ?>
        <div class="bg-gray-50 p-6 rounded-xl shadow-md text-center flex flex-col items-center">
          <div class="text-blue-600 font-bold text-lg mb-2"><?php echo htmlspecialchars($c['fullname']); ?></div>
          <p class="text-gray-700 text-sm leading-relaxed mb-2"><?php echo nl2br(htmlspecialchars($c['message'])); ?></p>
          <p class="text-xs text-gray-500"><?php echo htmlspecialchars($c['email']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

 <!-- فراخوان ساخت رزومه -->  
  <section class="bg-blue-100 py-10 mt-20 rounded-xl mx-4">  
    <div class="max-w-4xl mx-auto text-center px-4">  
      <h2 class="text-3xl font-bold text-blue-700 mb-6">آماده‌ای رزومه‌ حرفه‌ای‌ات رو بسازی؟</h2>  
      <p class="text-blue-600 mb-8 text-lg">وقتشه اولین قدم برای رسیدن به شغل رویایی‌ات رو برداری. رزومه‌تو بساز و بدرخش!</p>  
      <a onclick="openTemplateModal()" class="open-template-modal bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full shadow-md transition">ساخت رزومه رایگان</a>  
    </div>  
  </section>  

  <!-- فوتر -->  
  <?php include("assets/footer.php"); ?>  

  
<!-- مودال انتخاب قالب رزومه -->
<div id="templateModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50 transition-opacity duration-300">
  <div class="modal-box relative bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-lg animate-scaleIn">
    
    <!-- عنوان -->
    <h3 class="text-2xl font-bold mb-4 text-center pt-8 text-gray-800">انتخاب قالب رزومه</h3>
    
    <!-- لیست قالب‌ها -->
    <div id="templatesContainer" class="flex-grow overflow-y-auto px-6 pb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div class="cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 template-option transition duration-300 bg-white" data-template="1">
        <img src="assets/images/1.png" alt="قالب اداری" class="rounded-md mb-2 w-full object-contain" />
        <div class="text-center font-medium text-gray-700">قالب اداری(رایگان)</div>
      </div>
      <div class="cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 template-option transition duration-300 bg-white" data-template="2">
        <img src="assets/images/2.png" alt="قالب سازمانی" class="rounded-md mb-2 w-full object-contain" />
        <div class="text-center font-medium text-gray-700">قالب ساده</div>
      </div>
      <div class="cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 template-option transition duration-300 bg-white" data-template="3">
        <img src="assets/images/3.png" alt="قالب رسمی" class="rounded-md mb-2 w-full object-contain" />
        <div class="text-center font-medium text-gray-700">قالب شیک</div>
      </div>
      <div class="cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 template-option transition duration-300 bg-white" data-template="4">
        <img src="assets/images/4.png" alt="قالب ساده" class="rounded-md mb-2 w-full object-contain" />
        <div class="text-center font-medium text-gray-700">قالب خلاقانه</div>
      </div>
      <div class="cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 template-option transition duration-300 bg-white" data-template="5">
        <img src="assets/images/5.png" alt="قالب خلاقانه" class="rounded-md mb-2 w-full object-contain" />
        <div class="text-center font-medium text-gray-700">قالب انگلیسی</div>
      </div>
    </div>

    <!-- نوار دکمه‌ها -->
    <div id="actionBar" class="hidden bg-white border-t border-gray-200 px-6 py-4 flex justify-end items-center gap-3 shadow-inner">
      <button id="cancelBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-6 rounded transition">
        انصراف
      </button>
      <button id="confirmBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded disabled:opacity-50 transition" disabled>
        تایید
      </button>
    </div>
  </div>
</div>

<!-- انیمیشن ورود -->
<style>
  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  .animate-scaleIn {
    animation: scaleIn 0.25s ease-out;
  }
</style>

<!-- اسکریپت -->
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
</body>  
</html>


