<?php
session_start();

// توابع کمکی برای اعتبارسنجی
function validatePhone($phone) {
    // شماره موبایل ایرانی با 09 شروع و 11 رقم
    return preg_match('/^09\d{9}$/', $phone);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateNationalCode($code) {
    // کد ملی باید 10 رقم باشه
    return preg_match('/^\d{10}$/', $code);
}

function validateImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    return in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize;
}

// اعتبارسنجی فیلدهای اصلی
$errors = [];

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$national_code = trim($_POST['national_code'] ?? '');

// اعتبارسنجی: نام و نام خانوادگی
if (empty($fullname)) {
    $errors[] = "لطفاً نام و نام خانوادگی را وارد کنید.";
}

// اعتبارسنجی: ایمیل
if (!validateEmail($email)) {
    $errors[] = "ایمیل واردشده معتبر نیست.";
}

// اعتبارسنجی: شماره تماس
if (!validatePhone($phone)) {
    $errors[] = "شماره تماس باید با 09 شروع شود و 11 رقم داشته باشد.";
}

// اعتبارسنجی: کد ملی
if (!validateNationalCode($national_code)) {
    $errors[] = "کد ملی باید ۱۰ رقم عددی باشد.";
}

// اعتبارسنجی: تصویر پروفایل (در صورت ارسال)
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    if (!validateImage($_FILES['photo'])) {
        $errors[] = "فایل عکس باید JPG، PNG یا WebP باشد و حجم آن کمتر از ۲ مگابایت.";
    }
}

// اگر خطایی وجود دارد، نمایش و توقف
if (!empty($errors)) {
    echo "<!DOCTYPE html><html lang='fa' dir='rtl'><head><meta charset='UTF-8'><title>خطا در اطلاعات</title>
    <link href='../css/tailwind.min.css' rel='stylesheet'></head><body class='bg-red-50 p-10'>";
    echo "<div class='max-w-xl mx-auto bg-white p-6 shadow rounded'><h2 class='text-xl font-bold text-red-700 mb-4'>خطاهای فرم:</h2><ul class='list-disc text-red-600 pl-6'>";
    foreach ($errors as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul><div class='mt-6'><a href='javascript:history.back()' class='text-blue-600 hover:underline'>بازگشت و ویرایش اطلاعات</a></div></div></body></html>";
    exit;
}

// در صورتی که همه اعتبارسنجی‌ها موفق باشند، ارسال به فایل اصلی
include("process.php");