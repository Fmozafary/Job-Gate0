<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>رزومه <?php echo htmlspecialchars($resume['fullname']); ?></title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    body {
      font-family: 'IRANSans', sans-serif;
      background-color: #f2f2f2;
      color: #1f2937;
    }
    .resume-container {
      max-width: 850px;
      margin: 40px auto;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    .section-title {
      font-weight: bold;
      font-size: 18px;
      margin-bottom: 10px;
      border-bottom: 2px solid #2563eb;
      padding-bottom: 5px;
      color: #2563eb;
    }
    .section {
      margin-bottom: 30px;
    }
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
      font-size: 14px;
    }
    .profile-photo {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #ccc;
    }
    .label {
      font-weight: bold;
      color: #374151;
    }

    .save-menu {
      position: relative;
      display: inline-block;
    }
    .save-menu-content {
      display: none;
      position: absolute;
      bottom: 100%;
      right: 0;
      background-color: white;
      min-width: 160px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      z-index: 1;
      border-radius: 8px;
      overflow: hidden;
    }
    .save-menu-content button {
      width: 100%;
      text-align: right;
      padding: 10px 16px;
      font-size: 14px;
      border: none;
      background: white;
      cursor: pointer;
    }
    .save-menu-content button:hover {
      background-color: #f0f0f0;
    }
    .save-menu:hover .save-menu-content {
      display: block;
    }
  </style>
</head>
<body>

<div class="resume-container" id="resume">
  <!-- هدر -->
  <div class="flex justify-between items-center mb-6 border-b pb-4">
    <div>
      <h1 class="text-2xl font-bold text-blue-700"><?php echo htmlspecialchars($resume['fullname']); ?></h1>
      <p class="text-sm mt-1"><?php echo htmlspecialchars($resume['email']); ?> | <?php echo htmlspecialchars($resume['phone']); ?></p>
      <p class="text-sm"><?php echo htmlspecialchars($resume['city']); ?>، <?php echo htmlspecialchars($resume['province']); ?></p>
    </div>
    <?php if (!empty($resume['photo'])): ?>
<img src="/jobgate/<?= htmlspecialchars($resume['photo']) ?>" alt="عکس پروفایل" class="w-32 h-32 rounded-md border" />
    <?php endif; ?>
  </div>

  <!-- اطلاعات -->
  <div class="section">
    <h2 class="section-title">اطلاعات فردی</h2>
    <div class="info-grid">
      <div><span class="label">تاریخ تولد:</span> <?php echo $resume['birth']; ?></div>
      <div><span class="label">جنسیت:</span> <?php echo $resume['gender']; ?></div>
      <div><span class="label">وضعیت تأهل:</span> <?php echo $resume['marital']; ?></div>
      <div><span class="label">وضعیت خدمت:</span> <?php echo $resume['military']; ?></div>
      <div><span class="label">کد ملی:</span> <?php echo $resume['national_code']; ?></div>
      <div class="col-span-2"><span class="label">آدرس:</span> <?php echo nl2br(htmlspecialchars($resume['address'])); ?></div>
    </div>
  </div>

  <?php if (!empty($resume['about'])): ?>
  <div class="section">
    <h2 class="section-title">درباره من</h2>
    <p class="text-sm leading-7"><?php echo nl2br(htmlspecialchars($resume['about'])); ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume['education'])): ?>
  <div class="section">
    <h2 class="section-title">سوابق تحصیلی</h2>
    <p class="text-sm"><?php echo nl2br(htmlspecialchars($resume['education'])); ?></p>
    <p class="text-sm"><strong>رشته:</strong> <?php echo $resume['major']; ?> | <strong>دانشگاه:</strong> <?php echo $resume['university']; ?> | <strong>سال:</strong> <?php echo $resume['graduation_year']; ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume['skills'])): ?>
  <div class="section">
    <h2 class="section-title">مهارت‌ها</h2>
    <p class="text-sm"><?php echo nl2br(htmlspecialchars($resume['skills'])); ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume['software'])): ?>
  <div class="section">
    <h2 class="section-title">نرم‌افزارها</h2>
    <p class="text-sm"><?php echo nl2br(htmlspecialchars($resume['software'])); ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume['language'])): ?>
  <div class="section">
    <h2 class="section-title">زبان‌های خارجی</h2>
    <p class="text-sm"><strong><?php echo $resume['language']; ?></strong> – <?php echo $resume['language_level']; ?></p>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume['interests'])): ?>
  <div class="section">
    <h2 class="section-title">علاقه‌مندی‌ها</h2>
    <p class="text-sm"><?php echo nl2br(htmlspecialchars($resume['interests'])); ?></p>
  </div>
  <?php endif; ?>

  <?php if ($experiences): ?>
  <div class="section">
    <h2 class="section-title">سوابق شغلی</h2>
    <?php foreach ($experiences as $exp): ?>
    <div class="text-sm mb-2">
      <strong><?php echo $exp['title']; ?></strong> در <strong><?php echo $exp['company']; ?></strong><br>
      <span class="text-xs text-gray-500"><?php echo $exp['from_date']; ?> تا <?php echo $exp['to_date']; ?></span>
      <?php if (!empty($exp['description'])): ?>
        <div><?php echo nl2br(htmlspecialchars($exp['description'])); ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($courses): ?>
  <div class="section">
    <h2 class="section-title">دوره‌های آموزشی</h2>
    <?php foreach ($courses as $course): ?>
      <p class="text-sm mb-1">■ <?php echo $course['course_name']; ?> - <?php echo $course['institute']; ?> (<?php echo $course['year']; ?>)</p>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- دکمه ذخیره -->
  
</div>
<div class="text-center mt-10">
    <div class="save-menu">
      <button class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">💾 ذخیره</button>
      <div class="mt-4 flex justify-center gap-4">
  <a href="/jobgate/index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-2 rounded shadow text-sm">🏠 بازگشت به صفحه اصلی</a>
  
</div>
      <div class="save-menu-content text-right">
        <button onclick="saveAsImage()">📸 ذخیره به‌صورت عکس</button>
        <button onclick="saveAsPDF()">🧾 ذخیره به‌صورت PDF</button>
      </div>
      
    </div>
  </div>

<script>
  function saveAsImage() {
    const el = document.getElementById('resume');
    html2canvas(el, { scale: 2 }).then(canvas => {
      const link = document.createElement('a');
      link.download = 'resume.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
    });
  }

  async function saveAsPDF() {
    const el = document.getElementById('resume');
    const canvas = await html2canvas(el, { scale: 2 });
    const imgData = canvas.toDataURL('image/png');

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    const imgProps = pdf.getImageProperties(imgData);
    const pdfWidth = 210;
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
    pdf.save('resume.pdf');
  }
</script>
</body>
</html>