<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: "dejavu sans", sans-serif;
      direction: rtl;
      text-align: right;
      padding: 30px;
    }
    h1 { color: #1f2937; font-size: 20px; margin-bottom: 10px; }
    h2 { color: #3b82f6; font-size: 16px; margin-top: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    p, li { font-size: 13px; line-height: 1.7; }
    .section { margin-bottom: 15px; }
    ul { padding-right: 18px; }
  </style>
</head>
<body>

  <h1>رزومه: <?php echo $resume['fullname']; ?></h1>
  <p>ایمیل: <?php echo $resume['email']; ?></p>
  <p>شماره تماس: <?php echo $resume['phone']; ?></p>
  <p>کد ملی: <?php echo $resume['national_code']; ?></p>
  <p>شهر: <?php echo $resume['city']; ?> - استان: <?php echo $resume['province']; ?></p>
  <p>جنسیت: <?php echo $resume['gender']; ?> | تأهل: <?php echo $resume['marital']; ?> | خدمت: <?php echo $resume['military']; ?></p>

  <div class="section">
    <h2>درباره من</h2>
    <p><?php echo nl2br(htmlspecialchars($resume['about'])); ?></p>
  </div>

  <div class="section">
    <h2>تحصیلات</h2>
    <p>مدرک: <?php echo $resume['education']; ?> - رشته: <?php echo $resume['major']; ?> - دانشگاه: <?php echo $resume['university']; ?> - سال: <?php echo $resume['graduation_year']; ?></p>
  </div>

  <div class="section">
    <h2>مهارت‌ها</h2>
    <p><?php echo nl2br(htmlspecialchars($resume['skills'])); ?></p>
  </div>

  <div class="section">
    <h2>نرم‌افزارها</h2>
    <p><?php echo nl2br(htmlspecialchars($resume['software'])); ?></p>
  </div>

  <div class="section">
    <h2>زبان‌های خارجی</h2>
    <p><?php echo $resume['language']; ?> - <?php echo $resume['language_level']; ?></p>
  </div>

  <div class="section">
    <h2>علاقه‌مندی‌ها</h2>
    <p><?php echo nl2br(htmlspecialchars($resume['interests'])); ?></p>
  </div>

  <?php if ($experiences): ?>
  <div class="section">
    <h2>سوابق شغلی</h2>
    <ul>
      <?php foreach ($experiences as $exp): ?>
        <li>
          <strong><?php echo $exp['title']; ?></strong> در <?php echo $exp['company']; ?> (<?php echo $exp['from_date']; ?> تا <?php echo $exp['to_date']; ?>)
          <?php if (!empty($exp['description'])): ?>
          <br><?php echo nl2br(htmlspecialchars($exp['description'])); ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <?php if ($courses): ?>
  <div class="section">
    <h2>دوره‌های آموزشی</h2>
    <ul>
      <?php foreach ($courses as $course): ?>
        <li><?php echo $course['course_name']; ?> - <?php echo $course['institute']; ?> (<?php echo $course['year']; ?>)</li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

</body>
</html>