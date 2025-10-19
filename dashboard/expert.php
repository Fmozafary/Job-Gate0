<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'expert') {
  header("Location: ../auth/login.php");
  exit;
}
include("../php/db.php");

$expert_id = $_SESSION['user_id'];

// گرفتن رزومه‌هایی که به این کارشناس اختصاص داده شدن
$stmt = $pdo->prepare("
  SELECT 
    r.*, 
    u.fullname AS user_fullname,
    (
      SELECT COUNT(*) FROM messages 
      WHERE resume_id = r.id 
        AND sender_role = 'user' 
        AND is_seen_by_expert = 0
    ) AS unread_messages
  FROM resumes r 
  JOIN user_expert_map map ON r.user_id = map.user_id 
  JOIN users u ON r.user_id = u.id 
  WHERE map.expert_id = ? 
  ORDER BY r.created_at DESC
");
$stmt->execute([$expert_id]);
$resumes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// دسته‌بندی رزومه‌ها و پیام‌های جدید
$resumeFolders = [];
$newMessages = [];
foreach ($resumes as $res) {
  $uid = $res['user_id'];

  // این قسمت برای دسته‌بندی رزومه‌هاست
  if (!isset($resumeFolders[$uid])) {
    $resumeFolders[$uid] = [
      'user_name' => $res['user_fullname'],
      'resumes' => []
    ];
  }
  $resumeFolders[$uid]['resumes'][] = $res;

  // این شرط فقط اونهارو تو تب پیام‌های جدید میاره که unread دارن
  if ($res['unread_messages'] > 0) {
    $newMessages[] = $res;
  }
}
// گرفتن درخواست تماس‌ها
$callStmt = $pdo->query("SELECT * FROM call_requests ORDER BY created_at DESC");
$call_requests = $callStmt->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>داشبورد کارشناس</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <style>
    .tab-button { background: #e5e7eb; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
    .tab-button.active { background-color: #22c55e; color: white; }
    .folder-toggle { cursor: pointer; background: #f3f4f6; padding: 10px; border-radius: 8px; margin-bottom: 5px; display: flex; justify-content: space-between; }
    .folder-content { display: none; margin-bottom: 20px; }
  </style>
</head>
<body class="bg-gray-100 font-sans"><div class="max-w-6xl mx-auto mt-10 bg-white p-6 shadow-xl rounded-xl">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-2xl font-bold text-green-700">سلام کارشناس عزیز 👩‍💼</h1>
      <p class="text-gray-600">در این بخش می‌تونی رزومه، پیام‌ها و تماس‌های کاربران رو ببینی.</p>
    </div>
  <?php
// گرفتن اطلاعات کارشناس
$expert_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT fullname, email, avatar FROM users WHERE id = ?");
$stmt->execute([$expert_id]);
$expert = $stmt->fetch(PDO::FETCH_ASSOC);

// آدرس آواتار (اگه نداشت پیش‌فرض)
$avatar = $expert['avatar'] ? "../assets/avatars/" . $expert['avatar'] : "../assets/avatar.png";
?>

<div class=" ">
  <img src="<?= $avatar ?>" alt="آواتار" class="w-16 h-16 rounded-full border object-cover">
  <div>

       
  <a href="setting/index.php" class="text-blue-600 hover:underline text-sm">تنظیمات پروفایل</a>

    
  </div>
</div>
  </div>  <div class="flex gap-4 mb-6">
    <button onclick="switchTab('resumesTab')" class="tab-button active" id="btn-resumes">📂 رزومه‌ها</button>
    <button onclick="switchTab('messagesTab')" class="tab-button" id="btn-messages">📨 پیام‌های جدید (<?= count($newMessages) ?>)</button>
    <button onclick="switchTab('callsTab')" class="tab-button" id="btn-calls">📞 درخواست تماس</button>
  </div>  <!-- تب رزومه‌ها -->  <div id="resumesTab">
    <?php if (empty($resumeFolders)): ?>
      <p class="text-gray-500 text-center">هیچ رزومه‌ای برای شما ثبت نشده 😕</p>
    <?php else: ?>
      <?php foreach ($resumeFolders as $uid => $folder): ?>
        <div class="mb-4">
          <div class="folder-toggle" onclick="toggleFolder('user<?= $uid ?>')">
            <span>📂 <?= htmlspecialchars($folder['user_name']) ?></span>
            <span>⬇</span>
          </div>
          <div class="folder-content" id="folder-user<?= $uid ?>">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <?php foreach ($folder['resumes'] as $res): ?>
                <?php
  $borderClass = !empty($res['has_new_message']) ? 'border-orange-400' : '';
?>
<div class="bg-white border rounded-xl p-4 shadow <?= $borderClass ?>">
                  <h3 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($res['fullname']); ?></h3>
                  <p class="text-sm text-gray-600">ایمیل: <?= htmlspecialchars($res['email']); ?></p>
                  <p class="text-sm text-gray-600">شماره: <?= htmlspecialchars($res['phone']); ?></p>
                  <p class="text-xs text-gray-400 mt-1">ایجاد: <?= $res['created_at']; ?></p>
                  <div class="mt-4 flex justify-between">
                    <a href="../resume/view-resume.php?id=<?= $res['id']; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1 rounded">مشاهده رزومه</a>
                    <button onclick="openChatPanel(<?= $res['id']; ?>)" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-4 py-1 rounded">چت</button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>  <!-- تب پیام‌های جدید -->  <div id="messagesTab" class="hidden">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (empty($newMessages)): ?>
        <p class="text-gray-500 text-center w-full">هیچ پیامی وجود ندارد.</p>
      <?php else: ?>
        <?php foreach ($newMessages as $res): ?>
          <div class="bg-white border border-orange-400 rounded-xl p-4 shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($res['fullname']); ?></h3>
            <p class="text-sm text-gray-600">ایمیل: <?= htmlspecialchars($res['email']); ?></p>
            <p class="text-sm text-gray-600">شماره: <?= htmlspecialchars($res['phone']); ?></p>
            <div class="mt-4 flex justify-between">
              <a href="../resume/view-resume.php?id=<?= $res['id']; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1 rounded">مشاهده رزومه</a>
              <button onclick="openChatPanel(<?= $res['id']; ?>)" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-4 py-1 rounded">چت</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>  <!-- تب تماس -->  <div id="callsTab" class="hidden">
    <?php if (empty($call_requests)): ?>
      <p class="text-gray-500 text-center">هیچ درخواستی ثبت نشده.</p>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($call_requests as $call): ?>
          <div class="bg-white border rounded-xl p-4 shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($call['fullname']); ?></h3>
            <p class="text-sm text-gray-600">شماره تماس: <?= htmlspecialchars($call['phone']); ?></p>
            <p class="text-sm text-gray-600">موضوع: <?= htmlspecialchars($call['subject']); ?></p>
            <p class="text-xs text-gray-400 mt-1">تاریخ: <?= $call['created_at']; ?></p>
          </div>
        <?php endforeach; ?>
        
      </div>
    <?php endif; ?>
  </div>
  <div class="mt-4 text-center">
  <a href="../auth/logout.php" class="text-red-600 hover:underline text-sm">خروج از حساب</a>
</div>
</div><!-- چت پنل --><div id="chatPanel" class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-lg z-50 hidden flex flex-col">
  <div class="p-4 border-b flex justify-between items-center">
    <h2 class="text-lg font-bold text-green-700">چت با کاربر</h2>
    <button onclick="closeChatPanel()" class="text-sm text-gray-500">✖ بستن</button>
  </div>
  <div id="chatContent" class="p-4 overflow-y-auto flex-1">در حال بارگذاری...</div>
</div>


<script>
function switchTab(tabId) {
  ['resumesTab', 'messagesTab', 'callsTab'].forEach(id => {
    document.getElementById(id).classList.add('hidden');
    document.getElementById('btn-' + id.replace('Tab', '')).classList.remove('active');
  });
  document.getElementById(tabId).classList.remove('hidden');
  document.getElementById('btn-' + tabId.replace('Tab', '')).classList.add('active');
}

function toggleFolder(id) {
  const el = document.getElementById("folder-" + id);
  if (el) el.style.display = (el.style.display === "none" || el.style.display === "") ? "block" : "none";
}

function openChatPanel(resumeId) {
  document.getElementById("chatPanel").classList.remove("hidden");
  fetch(`expert/chat-panel.php?resume_id=${resumeId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("chatContent").innerHTML = html;
      attachChatPanelHandlers(resumeId);
    });
}

function closeChatPanel() {
  document.getElementById("chatPanel").classList.add("hidden");


}

function attachChatPanelHandlers(resumeId) {
  const form = document.getElementById("chatSendForm");
  const input = document.getElementById("messageInput");
  const box = document.getElementById("chatMessages");
  if (!form || !input || !box) return;

form.addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(form);
  const msg = input.value.trim();

  fetch("expert/send.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      console.log("🔵 وضعیت پاسخ:", res.status);
      return res.text();
    })
    .then((text) => {
      console.log("🟢 پاسخ خام سرور:", text);

      try {
        const data = JSON.parse(text);
        if (data.status === "success") {
          input.value = "";

          const msgBox = document.createElement("div");
          msgBox.className = "p-3 rounded-md max-w-[80%] bg-green-100 ml-auto text-right";
          msgBox.innerHTML = `
            <div class="text-sm">${msg.replace(/\n/g, "<br>")}</div>
            <div class="text-xs text-gray-500 mt-1">اکنون</div>
          `;
          box.appendChild(msgBox);
          box.scrollTop = box.scrollHeight;
        } else {
          alert("❌ پیام ارسال نشد: " + data.message);
        }
      } catch (e) {
        alert("⚠️ خروجی سرور JSON نبود: " + text);
        console.error("Parse error:", e);
      }
    })
    .catch((err) => {
      alert("❌ مشکل در ارسال پیام (کچ):");
      console.error("❌ fetch error:", err);
    });
});
}

switchTab('resumesTab');
</script>

</body>

</html>