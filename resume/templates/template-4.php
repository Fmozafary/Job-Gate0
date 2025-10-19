<!-- فایل: templates/template-4.php -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>رزومه <?php echo htmlspecialchars($resume['fullname']); ?></title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      font-family: 'IRANSans', sans-serif;
      background: linear-gradient(to right, #ffe3e3, #e0f7fa);
      color: #1e293b;
    }
    .tag {
      background-color: #fcd34d;
      color: #7c2d12;
      padding: 0.3rem 0.8rem;
      border-radius: 9999px;
      font-size: 0.875rem;
      display: inline-block;
      margin: 0.25rem;
    }
    .progress-bar {
      background-color: #e0f2fe;
      height: 0.75rem;
      border-radius: 9999px;
      overflow: hidden;
    }
    .progress-bar-fill {
      background: linear-gradient(to right, #60a5fa, #3b82f6);
      height: 100%;
      border-radius: 9999px;
    }
    .dropdown:hover .dropdown-menu {
      display: block;
    }
    .dropdown-menu {
      display: none;
    }
  </style>
</head>
<body class="p-6">

<div id="resume" class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden border-4 border-yellow-200">

  <!-- هدر -->
  <div class="bg-gradient-to-l from-yellow-300 to-pink-300 px-6 py-8 flex flex-col md:flex-row items-center justify-between">
    <div class="text-center md:text-right">
      <h1 class="text-3xl font-bold text-pink-900 mb-1"><?php echo htmlspecialchars($resume['fullname']); ?></h1>
      <p class="text-sm text-gray-700"><?php echo htmlspecialchars($resume['email']); ?> | <?php echo htmlspecialchars($resume['phone']); ?></p>
      <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($resume['city']); ?> | <?php echo htmlspecialchars($resume['birth']); ?></p>
      <p class="text-sm text-gray-700 mt-1">کد ملی: <?php echo htmlspecialchars($resume['national_code']); ?></p>
      <p class="text-sm text-gray-700 mt-1">جنسیت: <?php echo htmlspecialchars($resume['gender']); ?> | وضعیت تأهل: <?php echo htmlspecialchars($resume['marital']); ?></p>
      <?php if ($resume['gender'] === 'مرد'): ?>
        <p class="text-sm text-gray-700 mt-1">وضعیت خدمت: <?php echo htmlspecialchars($resume['military']); ?></p>
      <?php endif; ?>
    </div>
    <?php if (!empty($resume['photo'])): ?>
      <div class="mt-4 md:mt-0">
        <img src="../<?php echo $resume['photo']; ?>" alt="عکس" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md">
      </div>
    <?php endif; ?>
  </div>

  <!-- درباره من -->
  <?php if (!empty($resume['about'])): ?>
    <div class="px-6 py-4 border-b border-yellow-100">
      <h2 class="text-pink-600 font-semibold text-xl mb-2">درباره من</h2>
      <p class="text-sm text-gray-800 leading-relaxed"><?php echo nl2br(htmlspecialchars($resume['about'])); ?></p>
    </div>
  <?php endif; ?>

  <!-- بدنه -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-6 py-6">

    <!-- ستون چپ -->
    <div class="space-y-6">
      <?php if (!empty($resume['skills'])): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">مهارت‌ها</h2>
          <p class="text-sm text-gray-800"><?php echo nl2br(htmlspecialchars($resume['skills'])); ?></p>
        </section>
      <?php endif; ?>
      
      <?php if (!empty($resume['software'])): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">نرم‌افزارها</h2>
          <?php foreach (explode(',', $resume['software']) as $s): ?>
            <span class="tag"><?php echo htmlspecialchars(trim($s)); ?></span>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>

      <?php if (!empty($resume['language'])): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">زبان‌ها</h2>
          <?php
            $langs = explode(',', $resume['language']);
            $levels = explode(',', $resume['language_level']);
            foreach ($langs as $i => $lang):
              $level = $levels[$i] ?? '';
              $percent = ['پایه' => 25, 'متوسط' => 50, 'پیشرفته' => 75, 'تسلط کامل' => 100][$level] ?? 0;
          ?>
            <div class="mb-2">
              <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($lang); ?></span>
              <div class="progress-bar mt-1">
                <div class="progress-bar-fill" style="width: <?php echo $percent; ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>

      <?php if (!empty($resume['interests'])): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">علاقه‌مندی‌ها</h2>
          <p class="text-sm text-gray-800"><?php echo nl2br(htmlspecialchars($resume['interests'])); ?></p>
        </section>
      <?php endif; ?>
    </div>

    <!-- ستون راست -->
    <div class="space-y-6">
 <?php if (!empty($educations)): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">تحصیلات</h2>
  <ul class="space-y-1 text-sm text-gray-800">
    <?php foreach ($educations as $edu): ?>
      <li>■ <?php echo htmlspecialchars($edu['major']); ?> | <?php echo htmlspecialchars($edu['university']); ?> | <?php echo htmlspecialchars($edu['graduation_year'] ?? ''); ?></li>
    <?php endforeach; ?>
  </ul></section>
<?php endif; ?>


      <?php if ($experiences): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">سوابق شغلی</h2>
          <ul class="space-y-3">
            <?php foreach ($experiences as $exp): ?>
              <li class="text-sm">
                <strong><?php echo $exp['title']; ?></strong> در <?php echo $exp['company']; ?><br>
                <span class="text-gray-500 text-xs"><?php echo $exp['from_date']; ?> تا <?php echo $exp['to_date']; ?></span><br>
                <?php if (!empty($exp['description'])): ?>
                  <div class="text-gray-700 text-sm"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></div>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <?php if ($courses): ?>
        <section>
          <h2 class="text-pink-600 font-semibold text-md mb-2">دوره‌ها</h2>
          <ul class="space-y-1 text-sm">
            <?php foreach ($courses as $course): ?>
              <li>🎓 <?php echo $course['course_name']; ?> - <?php echo $course['institute']; ?> (<?php echo $course['year']; ?>)</li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>
    </div>

  </div>

  <!-- دکمه‌ها -->
  
</div>
<div class="text-center mt-10 pb-8 space-x-3 rtl:space-x-reverse">
    <div class="dropdown inline-block relative">
      <button class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-6 rounded-xl">
        ذخیره ▼
      </button>
      <ul class="dropdown-menu absolute bg-white border rounded-xl shadow p-2 right-0 mt-2 w-40 text-sm z-10">
        <li><a href="#" onclick="downloadPDF()" class="block px-4 py-2 hover:bg-pink-100 text-gray-800">دانلود PDF</a></li>
        <li><a href="#" onclick="downloadImage()" class="block px-4 py-2 hover:bg-pink-100 text-gray-800">دانلود تصویر</a></li>
      </ul>
    </div>

   
    <a href="../index.php" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-2 rounded-xl text-sm">بازگشت به صفحه اصلی</a>
  </div>
<!-- اسکریپت‌ها -->
<script>
  function downloadPDF() {
    const element = document.getElementById('resume');
    html2pdf().from(element).set({
      margin: 0.5,
      filename: 'resume-<?php echo $resume["fullname"]; ?>.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).save();
  }

  function downloadImage() {
    const element = document.getElementById('resume');
    html2canvas(element, { scale: 2, backgroundColor: '#ffffff' }).then(canvas => {
      const link = document.createElement('a');
      link.download = 'resume-<?php echo $resume["fullname"]; ?>.png';
      link.href = canvas.toDataURL();
      link.click();
    });
  }
</script>

</body>
</html>