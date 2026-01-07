<?php
session_start();
include 'check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['orders']) && is_array($data['orders'])) {
        try {
            foreach ($data['orders'] as $order) {
                $id = intval($order['id']);
                $display_order = intval($order['order']);
                
                $sql = "UPDATE services SET display_order = ? WHERE id = ?";
                $query->eQuery($sql, [$display_order, $id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير مسموحة']);
}