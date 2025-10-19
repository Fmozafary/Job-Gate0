<?php
// فرض بر اینکه $resume و بقیه متغیرها آماده‌اند
?>
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
      background-color: #f0f4f8;
      padding: 2rem;
    }

    .resume-wrapper {
      width: 794px;
      min-height: 1123px;
      margin: auto;
      background: white;
      padding: 40px;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
      display: flex;
      gap: 24px;
    }

    .left, .right {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .left {
      width: 35%;
      background: #dbebff;
      padding: 20px;
      text-align: center;
      border-radius: 8px;
    }

    .right {
      width: 65%;
      padding-top: 10px;
    }

    .section {
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 10px;
    }

    .section h2 {
      font-size: 1rem;
      color: #2563eb;
    }

    .no-print {
      margin-top: 2rem;
      text-align: center;
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      background-color: #2563eb;
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .btn:hover {
      background-color: #1e40af;
    }

    .btn.outline {
      background: white;
      color: #2563eb;
      border: 2px solid #2563eb;
    }

    .btn.outline:hover {
      background-color: #e0edff;
    }
  </style>
</head>
<body>

  <div id="resume" class="resume-wrapper">
    <!-- ستون چپ -->
    <div class="left">
      <?php if (!empty($resume['photo'])): ?>
        <img src="../<?php echo $resume['photo']; ?>" alt="عکس" class="w-32 h-32 rounded-full object-cover mb-4 border-4 border-white shadow mx-auto">
      <?php endif; ?>
      <h1 class="text-xl font-bold text-blue-900"><?php echo htmlspecialchars($resume['fullname']); ?></h1>
      <p class="text-sm text-gray-700"><?php echo htmlspecialchars($resume['email']); ?></p>
      <p class="text-sm text-gray-700"><?php echo htmlspecialchars($resume['phone']); ?></p>

      <div class="text-sm text-gray-600 text-right mt-4 space-y-1">
        <p><strong>کد ملی:</strong> <?= $resume['national_code']; ?></p>
        <p><strong>شهر:</strong> <?= $resume['city']; ?></p>
        <p><strong>تاریخ تولد:</strong> <?= $resume['birth']; ?></p>
        <p><strong>جنسیت:</strong> <?= $resume['gender']; ?></p>
        <p><strong>وضعیت تأهل:</strong> <?= $resume['marital']; ?></p>
        <p><strong>خدمت:</strong> <?= $resume['military']; ?></p>
      </div>

      <?php if (!empty($resume['about'])): ?>
        <div class="section text-right">
          <h2>درباره من</h2>
          <p class="text-sm text-gray-800 leading-relaxed"><?= nl2br(htmlspecialchars($resume['about'])); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- ستون راست -->
    <div class="right">
         <?php if (!empty($educations)): ?>
  <div class="section">
    <h2>تحصیلات</h2>
    <ul class="space-y-1 text-sm text-gray-800">
      <?php foreach ($educations as $edu): ?>
        <li>■ <?= $edu['major']; ?> | <?= $edu['university']; ?> | <?= $edu['graduation_year']; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

      <?php if (!empty($resume['skills'])): ?>
        <div class="section"><h2>مهارت‌ها</h2><p class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($resume['skills'])); ?></p></div>
      <?php endif; ?>

      <?php if (!empty($resume['software'])): ?>
        <div class="section"><h2>نرم‌افزارها</h2><p class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($resume['software'])); ?></p></div>
      <?php endif; ?>
<?php if (!empty($resume['language'])): ?>
        <div class="section"><h2>زبان خارجی</h2><p class="text-sm text-gray-800"><?= $resume['language']; ?> - <?= $resume['language_level']; ?></p></div>
      <?php endif; ?>

      <?php if (!empty($resume['interests'])): ?>
        <div class="section"><h2>علاقه‌مندی‌ها</h2><p class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($resume['interests'])); ?></p></div>
      <?php endif; ?>

      <?php if (!empty($experiences)): ?>
        <div class="section"><h2>سوابق شغلی</h2><ul class="space-y-2 text-sm text-gray-800">
          <?php foreach ($experiences as $exp): ?>
            <li>
              <strong><?= $exp['title']; ?></strong> در <?= $exp['company']; ?><br>
              <span class="text-gray-500 text-xs"><?= $exp['from_date']; ?> تا <?= $exp['to_date']; ?></span>
              <?php if (!empty($exp['description'])): ?>
                <div><?= nl2br(htmlspecialchars($exp['description'])); ?></div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul></div>
      <?php endif; ?>

      <?php if (!empty($courses)): ?>
        <div class="section"><h2>دوره‌های آموزشی</h2><ul class="space-y-1 text-sm">
          <?php foreach ($courses as $course): ?>
            <li>■ <?= $course['course_name']; ?> - <?= $course['institute']; ?> (<?= $course['year']; ?>)</li>
          <?php endforeach; ?>
        </ul></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- دکمه‌ها -->
  <div class="no-print mt-10 text-center flex gap-4 justify-center flex-wrap">
  <button onclick="downloadResumeImage()" class="btn">📸 ذخیره به صورت عکس</button>
  <button onclick="saveImageAsPDF()" class="btn">📄 ذخیره به صورت PDF (از تصویر)</button>
 
  <a href="/jobgate/index.php" class="btn outline">🏠 بازگشت به خانه</a>
</div>



<script>
  function downloadResumeImage() {
    const resumeDiv = document.getElementById("resume");
    html2canvas(resumeDiv, { scale: 2 }).then(canvas => {
      const link = document.createElement("a");
      link.download = "resume.png";
      link.href = canvas.toDataURL("image/png");
      link.click();
    });
  }

  function saveImageAsPDF() {
    const resumeDiv = document.getElementById("resume");
    html2canvas(resumeDiv, { scale: 2 }).then(canvas => {
      const imgData = canvas.toDataURL("image/png");
      const pdf = new jspdf.jsPDF("p", "mm", "a4");
      const imgProps = pdf.getImageProperties(imgData);
      const pdfWidth = 210;
      const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

      pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
      pdf.save("resume.pdf");
    });
  }
</script>
</body>
</html>
