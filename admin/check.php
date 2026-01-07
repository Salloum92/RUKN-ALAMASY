<?php

session_start();

include '../config.php';
$query = new Database();

// التحقق إذا كان المستخدم مسجل دخول كمسؤول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // إذا لم يكن مسجل دخول، إعادة التوجيه لصفحة تسجيل الدخول
    header('Location: login.php');
    exit();
}

// التحقق من وقت تسجيل الدخول (اختياري - لإضافة طبقة أمان إضافية)
if (isset($_SESSION['login_time'])) {
    $session_duration = 3600; // 1 ساعة بالثواني
    
    // إذا تجاوزت مدة الجلسة الوقت المسموح
    if (time() - $_SESSION['login_time'] > $session_duration) {
        // تدمير الجلسة وإعادة التوجيه
        session_destroy();
        header('Location: login.php?expired=true');
        exit();
    }
    
    // تجديد وقت الجلسة مع كل طلب
    $_SESSION['login_time'] = time();
}


// منع التخزين المؤقت للصفحات
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// حماية ضد هجمات XSS
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');