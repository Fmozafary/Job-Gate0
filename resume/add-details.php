<?php
include("../php/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("رزومه یافت نشد.");
}

$resume_id = intval($_GET['id']);

// بررسی وجود رزومه در دیتابیس
$stmt = $pdo->prepare("SELECT id FROM resumes WHERE id = ?");
$stmt->execute([$resume_id]);
if (!$stmt->fetch()) {
  die("رزومه یافت نشد.");
}
?>

<!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>جزئیات رزومه</title>
  <link rel="stylesheet" href="../css/tailwind.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .section-title {
      font-size: 1.25rem;
      font-weight: bold;
      color: #1e3a8a;
      margin-bottom: 1rem;
    }
    .form-box {
      background-color: #f8fafc;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body class="bg-blue-50">
  <?php include("../assets/header.php"); ?>  <main class="max-w-4xl mx-auto mt-10 bg-white p-8 shadow-xl rounded-xl">
    <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">افزودن جزئیات رزومه</h2><form action="save-details.php" method="post">
  <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">

  <!-- سوابق شغلی -->
  <div id="experiences" class="form-box">
    <div class="section-title">سوابق شغلی</div>
    <div class="experience-group space-y-4">
      <div class="grid md:grid-cols-2 gap-4">
        <input type="text" name="job_title[]" class="form-input" placeholder="عنوان شغلی">
        <input type="text" name="company[]" class="form-input" placeholder="نام شرکت">
        <input type="text" name="from_date[]" class="form-input" placeholder="از تاریخ">
        <input type="text" name="to_date[]" class="form-input" placeholder="تا تاریخ">
        <textarea name="job_description[]" rows="2" class="form-textarea md:col-span-2" placeholder="توضیحات"></textarea>
      </div>
    </div>
    <button type="button" onclick="addExperience()" class="mt-4 text-blue-600 hover:underline">+ افزودن سابقه جدید</button>
  </div>

  <!-- دوره‌های آموزشی -->
  <div id="courses" class="form-box">
    <div class="section-title">دوره‌های آموزشی</div>
    <div class="course-group space-y-4">
      <div class="grid md:grid-cols-2 gap-4">
        <input type="text" name="course_name[]" class="form-input" placeholder="نام دوره">
        <input type="text" name="institute[]" class="form-input" placeholder="آموزشگاه">
        <input type="text" name="course_year[]" class="form-input" placeholder="سال">
      </div>
    </div>
    <button type="button" onclick="addCourse()" class="mt-4 text-blue-600 hover:underline">+ افزودن دوره جدید</button>
  </div>

  <!-- ارسال -->
  <div class="text-center">
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">ثبت نهایی رزومه</button>
  </div>
</form>

  </main>  <script>
    function addExperience() {
      const container = document.querySelector(".experience-group");
      const html = `
        <div class="grid md:grid-cols-2 gap-4">
          <input type="text" name="job_title[]" class="form-input" placeholder="عنوان شغلی">
          <input type="text" name="company[]" class="form-input" placeholder="نام شرکت">
          <input type="text" name="from_date[]" class="form-input" placeholder="از تاریخ">
          <input type="text" name="to_date[]" class="form-input" placeholder="تا تاریخ">
          <textarea name="job_description[]" rows="2" class="form-textarea md:col-span-2" placeholder="توضیحات"></textarea></div>`;
  container.insertAdjacentHTML('beforeend', html);
}

function addCourse() {
  const container = document.querySelector(".course-group");
  const html = `
    <div class="grid md:grid-cols-2 gap-4">
      <input type="text" name="course_name[]" class="form-input" placeholder="نام دوره">
      <input type="text" name="institute[]" class="form-input" placeholder="آموزشگاه">
      <input type="text" name="course_year[]" class="form-input" placeholder="سال">
    </div>`;
  container.insertAdjacentHTML('beforeend', html);
}

  </script>
</body>
</html>