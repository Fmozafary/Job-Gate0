<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: ../auth/login.php");
  exit;
}
include("../php/db.php");
$from_dashboard = true; 
include("../template-modal.php"); 


// گرفتن اطلاعات کاربر برای نام و آواتار
$stmt = $pdo->prepare("SELECT fullname, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$_SESSION['user_name'] = $user['fullname'];
$avatar = $user['avatar'] ? "../assets/avatars/" . $user['avatar'] : "../assets/avatar.png";
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, fullname, created_at, template_id, has_new_message FROM resumes WHERE user_id = ?");
$stmt->execute([$user_id]);
$resumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$resume_count = count($resumes);
$last_resume = $resumes ? $resumes[0]['created_at'] : null;
?>
<!DOCTYPE html><html lang="fa" dir="rtl">
  <head>
  <meta charset="UTF-8">
  <title>داشبورد کاربر</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
    .slide-panel {
      position: fixed;
      top: 0;
      right: -100%;
      width: 100%;
      max-width: 500px;
      height: 100vh;
      background: white;
      box-shadow: -2px 0 15px rgba(0, 0, 0, 0.1);
      z-index: 100;
      transition: right 0.4s ease-in-out;
    }
    .slide-panel.open {
      right: 0;
    }
  </style>

</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-7xl mx-auto mt-10 bg-white p-6 sm:p-10 shadow-xl rounded-xl">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-blue-700">سلام <?php echo $_SESSION['user_name']; ?> 👋</h1>
        <p class="text-gray-600">به داشبورد خودت خوش اومدی. اینجا می‌تونی رزومه‌هات رو ببینی یا با کارشناس صحبت کنی ✨</p>
      </div>
      <div class="mt-6 text-center">
         <img src="<?php echo $avatar; ?>" class="w-20 h-20 rounded-full border-2 border-blue-500 object-cover" alt="پروفایل">
  <a href="setting/index.php" class="text-blue-600 hover:underline text-sm">تنظیمات پروفایل</a>
</div>
    </div><div class="grid sm:grid-cols-3 gap-4 mb-8">
  <div class="bg-blue-600 text-white p-4 rounded-lg shadow text-center cursor-pointer hover:bg-blue-700 transition" onclick="openTemplateModal()">
    + ساخت رزومه جدید
  </div>
  <div class="bg-blue-100 p-4 rounded-lg shadow text-center">
    <div class="text-sm text-blue-800">تعداد رزومه‌ها</div>
    <div class="text-2xl font-bold text-blue-900"><?php echo $resume_count; ?></div>
  </div>
  <div class="bg-green-100 p-4 rounded-lg shadow text-center">
    <?php
$stmt = $pdo->prepare("SELECT COUNT(*) FROM resumes WHERE user_id = ? AND has_new_message = 1");
$stmt->execute([$user_id]);
$unread = $stmt->fetchColumn();
?>
<div class="text-sm text-green-800">پیام‌های جدید</div>
<div class="text-2xl font-bold text-green-900">
  <?php echo $unread > 0 ? $unread : "نداری هنوز 😅"; ?>
</div>
  </div>
</div>

<?php if ($resume_count > 0): ?>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($resumes as $res): ?>
      <div class="bg-white border rounded-xl p-4 shadow resume-card group transition relative">
        <?php if ($res['has_new_message']): ?>
  <div class="absolute top-2 left-2 w-3 h-3 bg-red-600 rounded-full animate-ping z-10"></div>
  <div class="absolute top-2 left-2 w-3 h-3 bg-red-600 rounded-full z-20"></div>
<?php endif; ?>
        <img src="../assets/images/<?php echo $res['template_id']; ?>.png" class="rounded-xl mb-3 w-full h-48 object-contain" alt="resume">
        <h3 class="text-lg font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($res['fullname']); ?></h3>
        <p class="text-sm text-gray-600 mb-8">تاریخ ایجاد: <?php echo $res['created_at']; ?></p>
        <div class="absolute bottom-4 left-4 right-4 flex justify-center gap-2">
          <a href="../resume/view-resume.php?id=<?php echo $res['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1 rounded">دانلود</a>
          <a href="actions/delete.php?id=<?php echo $res['id']; ?>" onclick="return confirm('رزومه حذف شود؟')" class="bg-red-600 hover:bg-red-700 text-white text-xs px-4 py-1 rounded">حذف</a>
          <button onclick="openChatPanel(<?php echo $res['id']; ?>)" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-4 py-1 rounded">کارشناس</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="text-gray-500 text-center mt-10">فعلاً رزومه‌ای نداری! با دکمه بالا یکی بساز 💼</p>
<?php endif; ?>

<div class="mt-10 text-center">
  <a href="actions/request-expert.php" class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded shadow">همکاری با ما</a>
</div>



<div class="mt-4 text-center">
  <a href="../auth/logout.php" class="text-red-600 hover:underline text-sm">خروج از حساب</a>
</div>

  </div>  <div id="chatPanel" class="slide-panel">
    <div class="p-4 border-b flex justify-between items-center">
      <h2 class="text-lg font-bold text-blue-700">چت با کارشناس</h2>
      <button onclick="closeChatPanel()" class="text-sm text-gray-500">✖ بستن</button>
    </div>
    <div id="chatContent" class="p-4 overflow-y-auto h-[calc(100vh-130px)]">در حال بارگذاری...</div>
  </div>  <script>
    function openChatPanel(resumeId) {
      document.getElementById("chatPanel").classList.add("open");
      fetch(`../chat/chat-panel.php?resume_id=${resumeId}`)
        .then(res => res.text())
        .then(html => {
          document.getElementById("chatContent").innerHTML = html;
          setTimeout(() => attachChatPanelHandlers(resumeId), 100);
        });
    }

   function closeChatPanel() {
  document.getElementById("chatPanel").classList.remove("open");
  setTimeout(() => {
    location.reload();
  }, 300); // یه کوچولو صبر کن بعد رفرش بده
}
    function attachChatPanelHandlers(resumeId) {
      const form = document.getElementById("chatSendForm_chat");
      const input = document.getElementById("messageInput_chat");
      const box = document.getElementById("chatMessages");

      if (!form || !input || !box) return;

      form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("../chat/send.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status === "success") {
              input.value = "";
              setTimeout(() => openChatPanel(resumeId), 200);
            } else {
              alert("خطا در ارسال: " + data.message);
            }
          });
      });
    }
  </script>
  
</body>
</html>