<?php
include 'check.php'; // الاتصال بقاعدة البيانات

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $condition = "WHERE id = $id";
    $query->delete('services', $condition);
}

header('Location: services.php'); // إعادة التوجيه بعد الحذف
exit;
