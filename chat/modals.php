<!-- modals.php - شامل دو مودال: انتخاب قالب رزومه و پنل چت کشویی --><!-- ✅ مودال انتخاب قالب رزومه --><div id="templateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden justify-center items-center">
  <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-md p-6 relative animate-fade-in">
    <button onclick="closeTemplateModal()" class="absolute top-2 left-2 text-gray-500 hover:text-red-500">✖</button>
    <h3 class="text-lg font-bold mb-4">انتخاب قالب رزومه</h3>
    <div class="grid grid-cols-2 gap-4">
      <a href="../resume/resume-builder.php?template=1" class="border p-3 rounded-lg text-center hover:bg-gray-100">قالب 1</a>
      <a href="../resume/resume-builder.php?template=2" class="border p-3 rounded-lg text-center hover:bg-gray-100">قالب 2</a>
      <a href="../resume/resume-builder.php?template=3" class="border p-3 rounded-lg text-center hover:bg-gray-100">قالب 3</a>
    </div>
  </div>
</div><!-- ✅ پنل چت کشویی سمت راست --><div id="chatPanel" class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-lg z-50 translate-x-full transition-transform duration-300">
  <div class="flex justify-between items-center p-4 border-b">
    <h2 class="text-lg font-bold text-blue-700">چت با کارشناس</h2>
    <button onclick="closeChatPanel()" class="text-gray-500 hover:text-red-500">✖</button>
  </div>
  <div id="chatContent" class="overflow-y-auto h-[calc(100vh-140px)] p-4 bg-gray-50">در حال بارگذاری...</div>
</div><script>
  // 📌 برای مودال انتخاب قالب
  function openTemplateModal() {
    document.getElementById("templateModal").classList.remove("hidden");
    document.getElementById("templateModal").classList.add("flex");
  }

  function closeTemplateModal() {
    document.getElementById("templateModal").classList.remove("flex");
    document.getElementById("templateModal").classList.add("hidden");
  }

  // 📌 برای پنل چت کشویی
  function openChatPanel(resumeId) {
    const panel = document.getElementById("chatPanel");
    panel.classList.remove("translate-x-full");
    fetch(`../chat/chat-panel.php?resume_id=${resumeId}`)
      .then(res => res.text())
      .then(html => {
        document.getElementById("chatContent").innerHTML = html;
        setTimeout(() => attachChatPanelHandlers(resumeId), 100);
      });
  }

  function closeChatPanel() {
    document.getElementById("chatPanel").classList.add("translate-x-full");
  }

  function attachChatPanelHandlers(resumeId) {
    const form = document.getElementById("chatSendForm");
    const input = document.getElementById("messageInput");
    const box = document.getElementById("chatMessages");

    if (!form || !input || !box) return;

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch("../chat/send.php", {
        method: "POST",
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") {
            input.value = "";
            setTimeout(() => openChatPanel(resumeId), 200);
          } else {
            alert("خطا در ارسال: " + data.message);
          }
        });
    });
  }
</script><style>
  .animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
  }
</style>