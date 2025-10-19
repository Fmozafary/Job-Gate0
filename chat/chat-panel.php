<?php
session_start();
include("../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  http_response_code(403);
  exit("دسترسی غیرمجاز");
}

$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) exit("رزومه مشخص نشده.");

// گرفتن اطلاعات رزومه
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->execute([$resume_id, $_SESSION['user_id']]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$resume) exit("رزومه یافت نشد.");

// گرفتن نام و آواتار کاربر
$stmt = $pdo->prepare("SELECT fullname, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$name = $user['fullname'];
$avatar = $user['avatar'] ? "../assets/avatars/" . $user['avatar'] : "../assets/avatar.png";

// گرفتن پیام‌ها
$stmt = $pdo->prepare("SELECT * FROM messages WHERE resume_id = ? ORDER BY created_at ASC");
$stmt->execute([$resume_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// وقتی کاربر چت رو باز کرد، has_new_message صفر بشه
$update = $pdo->prepare("UPDATE resumes SET has_new_message = 0 WHERE id = ?");
$update->execute([$resume_id]);
?>

<div class="flex flex-col h-full">
  <div class="flex items-center justify-between border-b p-4 bg-white shadow text-blue-700">
    <div>
      <h2 class="font-bold text-lg">رزومه: <?= htmlspecialchars($resume['fullname']); ?></h2>
      <div class="flex items-center gap-2 mt-1">
        <img src="<?= $avatar; ?>" class="w-8 h-8 rounded-full border object-cover">
        <span class="text-sm text-gray-700"><?= htmlspecialchars($name); ?></span>
      </div>
    </div>
    <button onclick="closeChatPanel()" class="text-gray-500 hover:text-red-500">✖</button>
  </div>

  <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" style="margin-bottom: 100px;">
    <?php foreach ($messages as $msg): ?>
      <?php
        $is_user = $msg['sender_role'] === 'user';
        $msg_avatar = $is_user ? $avatar : "../../assets/avatars/expert.png";
      ?>
      <div class="flex <?= $is_user ? 'justify-end' : 'justify-start'; ?>">
        <div class="flex items-start gap-2 max-w-[80%] <?= $is_user ? 'flex-row-reverse' : ''; ?>">
          <img src="<?= $msg_avatar; ?>" class="w-8 h-8 rounded-full object-cover border mt-1">
          <div class="p-3 rounded-md <?= $is_user ? 'bg-blue-100 text-right' : 'bg-gray-200 text-left'; ?>">
            <div class="text-sm"><?= nl2br(htmlspecialchars($msg['message'])); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?= $msg['created_at']; ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="chatSendForm_chat" style="bottom: 0; background: white; z-index: 10; padding: 1rem; display: flex; gap: 0.5rem;">
    <input type="hidden" name="resume_id" value="<?= $resume_id; ?>">
    <input type="text" name="message" id="messageInput_chat"
           class="flex-1 bg-gray-100 border border-gray-300 text-sm px-4 py-2 rounded focus:outline-none focus:ring focus:border-blue-400"
           placeholder="پیامت رو بنویس..." required>
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition">ارسال</button>
  </form>
</div>

<style>
#chatSendForm_chat {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 60;
  background: white;
  padding: 1rem;
  display: flex;
  gap: 0.5rem;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
}
</style>

<script>function fetchChatMessages(resumeId) {
  fetch("fetch.php?resume_id=" + resumeId)
    .then(res => res.text())
    .then(html => {
      document.getElementById("chatMessages").innerHTML = html;
      document.getElementById("chatMessages").scrollTop = 9999;
    });
}

function attachChatForm(resumeId) {
  const form = document.getElementById("chatSendForm_chat");
  const input = document.getElementById("messageInput_chat");

  form.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    fetch("send.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        input.value = "";
        fetchChatMessages(resumeId); // ✅ همون لحظه پیام‌ها رو دوباره بخون
      } else {
        alert(data.message || "خطا در ارسال پیام");
      }
    });
  });
}

// وقتی پنل چت لود شد، پیام‌ها رو بیاره
fetchChatMessages(<?php echo $resume_id; ?>);
attachChatForm(<?php echo $resume_id; ?>);
</script>
