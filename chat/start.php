<?php
session_start();
include("../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  die("دسترسی غیرمجاز.");
}

$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) {
  die("رزومه‌ای انتخاب نشده.");
}

// گرفتن اطلاعات رزومه برای نمایش
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->execute([$resume_id, $_SESSION['user_id']]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resume) {
  die("رزومه‌ای پیدا نشد یا به شما تعلق ندارد.");
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>چت با کارشناس - <?php echo $resume['fullname']; ?></title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <style>
    .chat-box {
      height: 400px;
      overflow-y: auto;
      border: 1px solid #ddd;
      padding: 10px;
      background: #f9f9f9;
    }
  </style>
</head>
<body class="bg-gray-100 p-6 font-sans">

  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-lg font-bold mb-4 text-blue-700">چت درباره رزومه: <?php echo $resume['fullname']; ?></h2>

    <div id="chat" class="chat-box mb-4 rounded"></div>

    <form id="chatForm" class="flex gap-2">
      <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
      <input type="text" name="message" id="messageInput" class="flex-1 border rounded px-3 py-2" placeholder="پیامت رو بنویس..." required>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">ارسال</button>
    </form>
  </div>

<script>
function fetchMessages() {
  fetch("fetch.php?resume_id=<?php echo $resume_id; ?>")
    .then(res => res.text())
    .then(data => {
      document.getElementById("chat").innerHTML = data;
      document.getElementById("chat").scrollTop = 9999;
    });
}

document.getElementById("chatForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch("send.php", {
    method: "POST",
    body: formData
  }).then(() => {
    document.getElementById("messageInput").value = "";
    fetchMessages();
  });
});

setInterval(fetchMessages, 1500);
fetchMessages();
</script>

</body>
</html>