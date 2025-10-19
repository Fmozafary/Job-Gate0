<?php
$path = __DIR__ . "/log.txt";
$result = file_put_contents($path, "سلام فاطمه\n", FILE_APPEND);

if ($result !== false) {
  echo "✅ نوشته شد: $result بایت";
} else {
  echo "❌ خطا در نوشتن فایل";
}
?>