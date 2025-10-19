<?php
session_start();
include("../php/db.php");

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  exit("دسترسی غیرمجاز");
}

$resume_id = $_GET['resume_id'] ?? null;
if (!$resume_id) {
  http_response_code(400);
  exit("رزومه مشخص نشده");
}

$stmt = $pdo->prepare("SELECT * FROM messages WHERE resume_id = ? ORDER BY created_at ASC");
$stmt->execute([$resume_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($messages as $msg): ?>
  <div class="px-3 py-2 rounded-md shadow-sm w-fit <?php echo $msg['sender_role'] === 'user' ? 'bg-blue-100 self-end' : 'bg-gray-200 self-start'; ?>">
    <div class="text-sm"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
    <div class="text-xs text-gray-500 text-left mt-1"><?php echo $msg['created_at']; ?></div>
  </div>
<?php endforeach; ?>