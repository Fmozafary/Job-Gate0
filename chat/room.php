<!-- room.php -->
<?php
session_start();
$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) {
  die("رزومه مشخص نیست.");
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>چت با کارشناس</title>
  <link href="../css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 p-6 font-sans">
  <div class="max-w-3xl mx-auto bg-white shadow rounded-xl p-6">
    <h2 class="text-xl font-bold text-blue-700 mb-4">گفت‌و‌گو درباره رزومه #<?php echo $resume_id; ?></h2>

    <div id="chat-box" class="border rounded h-96 p-4 overflow-y-auto space-y-3 text-sm bg-gray-50">
      <!-- پیام‌ها اینجا با JS/AJAX نمایش داده میشن -->
    </div>

    <form id="chat-form" class="mt-4 flex gap-2">
      <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
      <input type="text" name="message" placeholder="پیامت رو بنویس..." class="flex-1 border rounded px-3 py-2 text-sm focus:outline-none">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">ارسال</button>
    </form>
  </div>

  <script>
    // ارسال پیام با AJAX
    document.getElementById("chat-form").addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch("send.php", { method: "POST", body: formData })
        .then(() => {
          this.message.value = "";
          loadMessages();
        });
    });

    // گرفتن پیام‌ها
    function loadMessages() {
      fetch("fetch.php?resume_id=<?php echo $resume_id; ?>")
        .then(res => res.text())
        .then(data => document.getElementById("chat-box").innerHTML = data);
    }

    setInterval(loadMessages, 1500);
    loadMessages();
  </script>
</body>
</html>