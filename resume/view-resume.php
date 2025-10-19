<?php
// اتصال به دیتابیس
include("../php/db.php");

// گرفتن id از URL
$id = $_GET['id'] ?? null;
if (!$id) {
  die("رزومه‌ای یافت نشد.");
}

// گرفتن اطلاعات رزومه
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ?");
$stmt->execute([$id]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$resume) {
  die("رزومه‌ای با این مشخصات پیدا نشد.");
}


// گرفتن تحصیلات مربوط به رزومه
$stmt = $pdo->prepare("SELECT * FROM educations WHERE resume_id = ?");
$stmt->execute([$id]);
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);



// گرفتن سوابق کاری
$stmt = $pdo->prepare("SELECT * FROM experiences WHERE resume_id = ?");
$stmt->execute([$id]);
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// گرفتن دوره‌های آموزشی
$stmt = $pdo->prepare("SELECT * FROM courses WHERE resume_id = ?");
$stmt->execute([$id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// شناسایی قالب
$template_id = $resume['template_id'] ?? 1;
$template_file = "templates/template-" . $template_id . ".php";

if (!file_exists($template_file)) {
  die("قالب انتخاب‌شده وجود ندارد.");
}
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نمایش رزومه</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body class="bg-gray-100">  <div id="resume1" class="max-w-5xl mx-auto my-10 bg-white shadow-lg rounded-lg p-6">
    <?php include($template_file); ?>
  </div>  <div class="text-center my-6">
   
   
    
  </div>  <script>
    function downloadPDF() {
      const element = document.getElementById('resume');
      const opt = {
        margin:       0.5,
        filename:     'resume.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
      };
      html2pdf().set(opt).from(element).save();
    }
  </script></body>
</html>