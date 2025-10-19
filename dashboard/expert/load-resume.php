<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'expert') {
  header("Location: ../../auth/login.php");
  exit;
}
include("../../php/db.php");

$stmt = $pdo->query("SELECT resumes.*, users.name AS user_name FROM resumes JOIN users ON resumes.email = users.email ORDER BY resumes.created_at DESC");
$resumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لیست رزومه‌ها | داشبورد کارشناس</title>
  <link href="../../css/tailwind.min.css" rel="stylesheet">
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

<div class="max-w-6xl mx-auto mt-10 bg-white p-6 sm:p-10 shadow-xl rounded-xl">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-bold text-green-700">💼 رزومه‌های ارسال‌شده</h1>
      <p class="text-gray-600 mt-1">در این بخش می‌تونی لیست رزومه کاربران رو ببینی و باهاشون چت کنی.</p>
    </div>
    <img src="../../assets/avatar-expert.png" class="w-12 h-12 rounded-full border-2 border-green-500" alt="کارشناس">
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($resumes as $res): ?>
      <div class="bg-white border rounded-xl p-4 shadow group transition relative">
        <img src="../../assets/images/<?php echo $res['template_id']; ?>.png" class="rounded-xl mb-3 w-full h-40 object-contain" alt="resume">
        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($res['fullname']); ?></h3>
        <p class="text-sm text-gray-600 mt-1 mb-3">کاربر: <span class="text-blue-600"><?php echo htmlspecialchars($res['user_name']); ?></span></p>
        <div class="flex justify-between">
          <a href="../../resume/view-resume.php?id=<?php echo $res['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">مشاهده</a>
          <button onclick="openChatPanel(<?php echo $res['id']; ?>)" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-1 rounded">چت با کاربر</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- پنل چت کشویی -->
<div id="chatPanel" class="slide-panel">
  <div class="p-4 border-b flex justify-between items-center">
    <h2 class="text-lg font-bold text-green-700">💬 پنل گفتگو با کاربر</h2>
    <button onclick="closeChatPanel()" class="text-sm text-gray-500">✖ بستن</button>
  </div>
  <div id="chatContent" class="p-4 overflow-y-auto h-[calc(100vh-130px)]">در حال بارگذاری...</div>
</div>

<script>
function openChatPanel(resumeId) {
  document.getElementById("chatPanel").classList.add("open");
  fetch(`chat-panel.php?resume_id=${resumeId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("chatContent").innerHTML = html;
      setTimeout(() => attachChatPanelHandlers(resumeId), 100);
    });
}

function closeChatPanel() {
  document.getElementById("chatPanel").classList.remove("open");
}

function attachChatPanelHandlers(resumeId) {
  const form = document.getElementById("chatSendForm");
  const input = document.getElementById("messageInput");
  const box = document.getElementById("chatMessages");

  if (!form || !input || !box) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("send.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          input.value = "";
          setTimeout(() => openChatPanel(resumeId), 300);
        } else {
          alert("خطا در ارسال پیام: " + data.message);
        }
      });
  });
}
</script>

</body>
</html>