<?php
// بدء الجلسة
session_start();

// تدمير جميع بيانات الجلسة
$_SESSION = array();

// إذا كنت تريد تدمير الجلسة تماماً، قم بحذف كوكيز الجلسة أيضاً
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول مع رسالة خروج ناجح
header('Location: login.php?logout=true');
exit();
?>