<!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>رزومه <?php echo htmlspecialchars($resume['fullname']); ?></title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      font-family: 'IRANSans', sans-serif;
      background-color: #f0fdfa;
      color: #1f2937;
    }
    .tag {
      background-color: #a7f3d0;
      color: #065f46;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.875rem;
      display: inline-block;
      margin: 0.25rem;
    }
    .progress-bar {
      background-color: #d1fae5;
      height: 0.75rem;
      border-radius: 9999px;
      overflow: hidden;
    }
    .progress-bar-fill {
      background-color: #10b981;
      height: 100%;
      border-radius: 9999px;
    }
    .menu-btn {
      position: relative;
    }
    .menu-options {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background-color: white;
      border: 1px solid #ccc;
      z-index: 10;
      width: 150px;
      text-align: right;
      border-radius: 0.5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .menu-options button {
      display: block;
      width: 100%;
      padding: 10px;
      text-align: right;
      font-size: 14px;
      background: white;
      border: none;
      cursor: pointer;
    }
    .menu-options button:hover {
      background-color: #f0f0f0;
    }
    @media print {
      body {
        zoom: 90%;
      }
    }
  </style>
</head>
<body class="p-6">
<div id="resume" class="max-w-4xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
  <!-- هدر رزومه -->
  <div class="bg-green-100 px-6 py-8 flex flex-col md:flex-row items-center justify-between">
    <div class="text-center md:text-right">
      <h1 class="text-2xl font-bold text-green-900 mb-1"><?php echo htmlspecialchars($resume['fullname']); ?></h1>
      <p class="text-sm text-gray-800"><?php echo htmlspecialchars($resume['email']); ?> | <?php echo htmlspecialchars($resume['phone']); ?></p>
      <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($resume['city']); ?> | <?php echo htmlspecialchars($resume['birth']); ?></p>
      <p class="text-sm text-gray-700 mt-1">کد ملی: <?php echo htmlspecialchars($resume['national_code']); ?></p>
      <p class="text-sm text-gray-700 mt-1">جنسیت: <?php echo htmlspecialchars($resume['gender']); ?> | وضعیت تأهل: <?php echo htmlspecialchars($resume['marital']); ?></p>
      <?php if ($resume['gender'] === 'مرد'): ?>
        <p class="text-sm text-gray-700 mt-1">وضعیت خدمت: <?php echo htmlspecialchars($resume['military']); ?></p>
      <?php endif; ?>
    </div>
    <?php if (!empty($resume['photo'])): ?>
      <div class="mt-4 md:mt-0">
        <img src="../<?php echo $resume['photo']; ?>" alt="عکس" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow">
      </div>
    <?php endif; ?>
  </div>  <!-- درباره من -->  <?php if (!empty($resume['about'])): ?>  <div class="px-6 py-4 border-b">
    <h2 class="text-green-700 font-semibold text-lg mb-2">درباره من</h2>
    <p class="text-sm text-gray-800 leading-relaxed"><?php echo nl2br(htmlspecialchars($resume['about'])); ?></p>
  </div>
  <?php endif; ?>  <!-- همه بخش‌ها در یک کادر سبز در پایین -->  
    <?php if (!empty($resume['skills'])): ?>
      <section><h2 class="text-green-700 font-semibold text-md mb-2">مهارت‌ها</h2>
      <p class="text-sm text-gray-800"><?php echo nl2br(htmlspecialchars($resume['skills'])); ?></p></section>
    <?php endif; ?><?php if (!empty($resume['software'])): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">نرم‌افزارها</h2>
  <?php foreach (explode(',', $resume['software']) as $s): ?>
    <?php if ($s = trim($s)) echo '<span class="tag">'.htmlspecialchars($s).'</span>'; ?>
  <?php endforeach; ?></section>
<?php endif; ?>

<?php if (!empty($resume['language'])): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">زبان‌های خارجی</h2>
  <?php 
    $langs = explode(',', $resume['language']);
    $levels = explode(',', $resume['language_level']);
    foreach ($langs as $i => $lang):
      $level = $levels[$i] ?? '';
      $percent = match($level) {
        'متوسط' => 50, 'پیشرفته' => 75, 'تسلط کامل' => 100, default => 25
      };
  ?>
    <div class="mb-2">
      <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($lang); ?></span>
      <div class="progress-bar mt-1"><div class="progress-bar-fill" style="width: <?php echo $percent; ?>%"></div></div>
    </div>
  <?php endforeach; ?></section>
<?php endif; ?>

<?php if (!empty($resume['interests'])): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">علاقه‌مندی‌ها</h2>
  <p class="text-sm text-gray-800"><?php echo nl2br(htmlspecialchars($resume['interests'])); ?></p></section>
<?php endif; ?>

<?php if (!empty($educations)): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">تحصیلات</h2>
  <ul class="space-y-1 text-sm text-gray-800">
    <?php foreach ($educations as $edu): ?>
      <li>■ <?php echo htmlspecialchars($edu['major']); ?> | <?php echo htmlspecialchars($edu['university']); ?> | <?php echo htmlspecialchars($edu['graduation_year'] ?? ''); ?></li>
    <?php endforeach; ?>
  </ul></section>
<?php endif; ?>

<?php if (!empty($experiences)): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">سوابق شغلی</h2>
  <ul class="space-y-3 text-sm">
    <?php foreach ($experiences as $exp): ?>
      <li>
        <strong><?php echo htmlspecialchars($exp['title']); ?></strong> در <?php echo htmlspecialchars($exp['company']); ?><br>
        <span class="text-gray-500 text-xs"><?php echo htmlspecialchars($exp['from_date']); ?> تا <?php echo htmlspecialchars($exp['to_date']); ?></span><br>
        <?php if (!empty($exp['description'])): ?>
          <div class="text-gray-700 text-sm"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul></section>
<?php endif; ?>

<?php if (!empty($courses)): ?>
  <section><h2 class="text-green-700 font-semibold text-md mb-2">دوره‌های آموزشی</h2>
  <ul class="space-y-1 text-sm">
    <?php foreach ($courses as $course): ?>
      <li>■ <?php echo htmlspecialchars($course['course_name']); ?> - <?php echo htmlspecialchars($course['institute']); ?> (<?php echo htmlspecialchars($course['year']); ?>)</li>
    <?php endforeach; ?>
  </ul></section>
<?php endif; ?>
<div class="bg-green-100 px-6 py-6 grid grid-cols-1 gap-6">


  </div>
</div><!-- دکمه‌ها --><div class="text-center mt-8 pb-8 space-x-4 rtl:space-x-reverse">
  <div class="inline-block relative menu-btn">
    <button onclick="toggleMenu()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow text-sm">💾 ذخیره</button>
    <div id="menu" class="menu-options">
      <button onclick="saveAsImage()">📸 ذخیره عکس</button>
      <button onclick="saveAsPDF()">📄 ذخیره PDF</button>
    </div>
  </div>
  <a href="/jobgate/index.php" class="bg-red-100 hover:bg-red-200 text-red-700 px-5 py-2 rounded text-sm">🏠 بازگشت به صفحه اصلی</a>
</div><script>
  function toggleMenu() {
    document.getElementById("menu").classList.toggle("hidden");
    document.getElementById("menu").style.display = 
      document.getElementById("menu").style.display === "block" ? "none" : "block";
  }

  function saveAsImage() {
    const resume = document.getElementById("resume");
    html2canvas(resume, { scale: 2, useCORS: true, backgroundColor: "#ffffff" }).then(canvas => {
      const link = document.createElement("a");
      link.download = "resume-image.png";
      link.href = canvas.toDataURL("image/png");
      link.click();
    });
  }

  function saveAsPDF() {
    const element = document.getElementById("resume");
    html2pdf().set({
      margin: 0.5,
      filename: 'resume.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(element).save();
  }
</script></body>
</html>