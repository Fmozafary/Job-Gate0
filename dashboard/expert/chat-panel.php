<?php
session_start();
include("../../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'expert') {
  http_response_code(403);
  exit("دسترسی غیرمجاز");
}

$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) exit("رزومه مشخص نشده.");

// گرفتن رزومه
$stmt = $pdo->prepare("
  SELECT r.*, u.fullname AS user_name
  FROM resumes r
  JOIN users u ON r.user_id = u.id
  WHERE r.id = ?
");
$stmt->execute([$resume_id]);
$resume = $stmt->fetch();

if (!$resume) exit("رزومه پیدا نشد.");

// بررسی مجوز: این رزومه باید به همین کارشناس وصل شده باشه
$check = $pdo->prepare("SELECT * FROM user_expert_map WHERE user_id = ? AND expert_id = ?");
$check->execute([$resume['user_id'], $_SESSION['user_id']]);
if (!$check->fetch()) exit("این رزومه به شما اختصاص داده نشده.");


// گرفتن پیام‌ها
$stmt = $pdo->prepare("SELECT * FROM messages WHERE resume_id = ? ORDER BY created_at ASC");
$stmt->execute([$resume_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// علامت‌گذاری پیام‌های دیده‌نشده به عنوان دیده‌شده
$markSeen = $pdo->prepare("
  UPDATE messages 
  SET is_seen_by_expert = 1 
  WHERE resume_id = ? AND sender_role = 'user'
");
$markSeen->execute([$resume_id]);
?>

<div class="flex flex-col h-full">
  <div class="flex items-center justify-between border-b p-4 bg-white shadow text-blue-700">
    <h2 class="font-bold text-lg">رزومه: <?= htmlspecialchars($resume['fullname']); ?> (<?= htmlspecialchars($resume['user_name']); ?>)</h2>
    <button onclick="closeChatPanel()" class="text-gray-500 hover:text-red-500">✖</button>
  </div>

  <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
    <?php foreach ($messages as $msg): ?>
      <div class="p-3 rounded-md max-w-[80%] <?= $msg['sender_role'] === 'expert' ? 'bg-green-100 ml-auto text-right' : 'bg-gray-200 mr-auto text-left'; ?>">
        <div class="text-sm"><?= nl2br(htmlspecialchars($msg['message'])); ?></div>
        <div class="text-xs text-gray-500 mt-1"><?= $msg['created_at']; ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="chatSendForm" class="border-t p-3 flex gap-2 bg-white">
    <input type="hidden" name="resume_id" value="<?= $resume_id; ?>">
    <input type="text" name="message" id="messageInput" class="flex-1 border rounded px-3 py-2 text-sm" placeholder="پاسخ خود را بنویسید..." required>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">ارسال</button>
  </form>
</div>

<script>
  const chatMessages = document.getElementById("chatMessages");
  chatMessages.scrollTop = chatMessages.scrollHeight;
</script>