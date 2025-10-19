<?php

$isLoggedIn = isset($_SESSION['user_email']); 
?>
<!-- مودال انتخاب قالب رزومه -->
<div id="templateModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50 transition-opacity duration-300">
  <div class="modal-box relative bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-lg animate-scaleIn">
    
    <!-- عنوان -->
    <h3 class="text-2xl font-bold mb-4 text-center pt-8 text-gray-800">انتخاب قالب رزومه</h3>
    
    <!-- لیست قالب‌ها -->
    <div id="templatesContainer" class="flex-grow overflow-y-auto px-6 pb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    
      

<?php
include("php/db.php");
$templates = $pdo->query("SELECT * FROM templates WHERE is_active = 1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>



<?php foreach ($templates as $tpl): 
  $id = $tpl['id'];
  $label = $tpl['name'];
  $imgPath = $tpl['image_path'];
  $locked = !$isLoggedIn && $id !== 1;
?>
  <div class="template-option relative cursor-pointer hover:shadow-lg p-3 rounded-lg border border-gray-400 transition duration-300 bg-white <?php echo $locked ? 'opacity-60 pointer-events-none' : ''; ?>" data-template="<?= $id ?>">
    <img src="<?= isset($from_dashboard) && $from_dashboard ? '../' : '' ?><?= $imgPath ?>" alt="<?= $label ?>" class="rounded-md mb-2 w-full object-contain" style="width: 270px; height: 350px; border-radius: 0.75rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: white; object-fit: contain;" />
    <div class="text-center font-medium text-gray-700"><?= $label ?></div>
    <?php if ($locked): ?>
      <div class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded shadow">🔒 فقط با ورود</div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
    </div>

    <!-- نوار دکمه‌ها -->
    <div id="actionBar" class="bg-white border-t border-gray-200 px-6 py-4 flex justify-end items-center gap-3 shadow-inner">
      <button id="cancelBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-6 rounded transition">
        انصراف
      </button>
      <button id="confirmBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded disabled:opacity-50 transition" disabled>
        تایید
      </button>
    </div>
  </div>
</div>

<!-- انیمیشن -->
<style>
  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  .animate-scaleIn {
    animation: scaleIn 0.25s ease-out;
  }
</style>

<!-- اسکریپت -->
 <?php
  $resumePath = isset($from_dashboard) && $from_dashboard ? "../resume/resume-builder.php" : "resume/resume-builder.php";
 $buyPath = isset($from_dashboard) && $from_dashboard ? "../buy.php" : "buy.php";

  
?>
<script>
  console.log("Template modal script loaded");
  const modal = document.getElementById('templateModal');
  const cancelBtn = document.getElementById('cancelBtn');
  const confirmBtn = document.getElementById('confirmBtn');
  const templateOptions = document.querySelectorAll('.template-option');
  const actionBar = document.getElementById('actionBar');
if (typeof selectedTemplate === 'undefined') {
  var selectedTemplate = null;
}
  const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

  function openTemplateModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('modal-open');

    selectedTemplate = null;
    confirmBtn.disabled = true;
    confirmBtn.innerText = "تایید";
    confirmBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    actionBar.classList.add('hidden');
    templateOptions.forEach(opt => opt.classList.remove('border-blue-600', 'ring-2', 'ring-blue-400'));
  }

  function closeTemplateModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('modal-open');
  }

  cancelBtn.addEventListener('click', closeTemplateModal);

  templateOptions.forEach(opt => {
    opt.addEventListener('click', () => {
      const isSelected = opt.classList.contains('border-blue-600');
      templateOptions.forEach(o => o.classList.remove('border-blue-600', 'ring-2', 'ring-blue-400'));

      if (isSelected) {
        selectedTemplate = null;
        confirmBtn.disabled = true;
        actionBar.classList.add('hidden');
      } else {
        opt.classList.add('border-blue-600', 'ring-2', 'ring-blue-400');
        selectedTemplate = opt.getAttribute('data-template');

        if (isLoggedIn) {
          confirmBtn.disabled = false;
          confirmBtn.innerText = "تایید";
          actionBar.classList.remove('hidden');
        } else {
          confirmBtn.disabled = true;
          confirmBtn.innerText = "برای استفاده وارد شوید";
          confirmBtn.classList.add("bg-gray-400", "cursor-not-allowed");
          actionBar.classList.remove('hidden');
        }
      }
    });
  });

 confirmBtn.addEventListener('click', () => {
  if (!selectedTemplate) return;

  // قالب 1 همیشه رایگانه
  if (selectedTemplate === "1") {
  window.location.href = "<?= $resumePath ?>?template=1";
  return;
}
  // اگه وارد نشده باشه هیچ کاری نکن
  if (!isLoggedIn) return;

  // قالب‌های 2 به بعد → فرستادن به buy.php
window.location.href = "<?= $buyPath ?>?template=" + selectedTemplate;
});


  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      closeTemplateModal();
    }
  });
</script>