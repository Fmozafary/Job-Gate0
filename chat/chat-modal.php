<?php
// فایل: start.php
session_start();
include("../php/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  die("\u062f\u0633\u062a\u0631\u0633\u06cc \u063a\u06cc\u0631\u0645\u062c\u0627\u0632.");
}

$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) {
  die("\u0631\u0632\u0648\u0645\u0647\u200c\u0627\u06cc \u0627\u0646\u062a\u062e\u0627\u0628 \u0646\u0634\u062f\u0647.");
}

$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->execute([$resume_id, $_SESSION['user_id']]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resume) {
  die("\u0631\u0632\u0648\u0645\u0647 \u0627\u06cc \u067e\u06cc\u062f\u0627 \u0646\u0634\u062f \u06cc\u0627 \u0628\u0647 \u0634\u0645\u0627 \u062a\u0639\u0644\u0642 \u0646\u062f\u0627\u0631\u062f.");
}
?><!DOCTYPE html><html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>چت با کارشناس - <?php echo htmlspecialchars($resume['fullname']); ?></title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'IRANSans', sans-serif;
      background-color: #e5e7eb;
    }
    .chat-container {
      display: flex;
      flex-direction: column;
      height: 100vh;
      max-width: 600px;
      margin: auto;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .chat-header {
      background-color: #2563eb;
      color: white;
      padding: 16px;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .chat-box {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      background: #f3f4f6;
    }
    .message {
      margin-bottom: 12px;
      max-width: 75%;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 0.875rem;
      line-height: 1.4rem;
    }
    .user-message {
      background-color: #dbeafe;
      align-self: flex-end;
    }
    .expert-message {
      background-color: #e5e7eb;
      align-self: flex-start;
    }
    .chat-footer {
      padding: 12px;
      border-top: 1px solid #ddd;
      display: flex;
      gap: 8px;
    }
    .chat-footer input {
      flex: 1;
    }
  </style>
</head>
<body>
  <div class="chat-container">
    <div class="chat-header">
      <span>چت با کارشناس برای رزومه: <?php echo htmlspecialchars($resume['fullname']); ?></span>
      <a href="../user/user.php" class="text-xs underline">بازگشت</a>
    </div><div id="chat-box" class="chat-box flex flex-col"></div>

<form id="chat-form" class="chat-footer">
  <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
  <input type="text" name="message" id="messageInput" class="border rounded px-3 py-2 text-sm" placeholder="پیام خود را بنویسید..." required>
  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">ارسال</button>
</form>

  </div>  <script>
    function loadMessages() {
      fetch("fetch.php?resume_id=<?php echo $resume_id; ?>")
        .then(res => res.text())
        .then(html => {
          document.getElementById("chat-box").innerHTML = html;
          document.getElementById("chat-box").scrollTop = document.getElementById("chat-box").scrollHeight;
        });
    }

    document.getElementById("chat-form").addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch("send.php", {
        method: "POST",
        body: formData
      }).then(res => {
        if (res.ok) {
          document.getElementById("messageInput").value = "";
          loadMessages();
        }
      });
    });

    loadMessages();
    setInterval(loadMessages, 3000);
  </script></body>
</html>