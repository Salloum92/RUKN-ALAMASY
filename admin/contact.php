<?php
include 'check.php';

// جلب معلومات الاتصال
$contact = $query->select('contact', "*")[0] ?? [];
$contact_box = $query->select('contact_box', "*") ?? [];

// أنواع وسائل التواصل الاجتماعي المدعومة
$social_types = [
    'twitter' => ['icon' => 'bi bi-twitter-x', 'label' => 'تويتر', 'color' => '#1DA1F2'],
    'facebook' => ['icon' => 'bi bi-facebook', 'label' => 'فيسبوك', 'color' => '#1877F2'],
    'instagram' => ['icon' => 'bi bi-instagram', 'label' => 'انستغرام', 'color' => '#E4405F'],
    'linkedin' => ['icon' => 'bi bi-linkedin', 'label' => 'لينكد إن', 'color' => '#0A66C2'],
    'youtube' => ['icon' => 'bi bi-youtube', 'label' => 'يوتيوب', 'color' => '#FF0000'],
    'whatsapp' => ['icon' => 'bi bi-whatsapp', 'label' => 'واتساب', 'color' => '#25D366'],
    'telegram' => ['icon' => 'bi bi-telegram', 'label' => 'تيليجرام', 'color' => '#0088CC'],
    'tiktok' => ['icon' => 'bi bi-tiktok', 'label' => 'تيك توك', 'color' => '#000000'],
    'snapchat' => ['icon' => 'bi bi-snapchat', 'label' => 'سناب شات', 'color' => '#FFFC00'],
    'pinterest' => ['icon' => 'bi bi-pinterest', 'label' => 'بنترست', 'color' => '#E60023']
];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_google_maps'])) {
    try {
        $data = [
            'type' => 'google_maps',
            'value' => 'https://www.google.com/maps/',
            'label' => 'خرائط جوجل',
            'icon' => 'bi bi-geo-alt-fill'
        ];
        
        $result = $query->insert('contact_box', $data);
        
        if ($result) {
            $_SESSION['success'] = "تم إنشاء سجل خرائط جوجل بنجاح";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
    }
}

// التحقق من وجود سجل Google Maps
$google_maps_exists = false;
foreach ($contact_box as $item) {
    if (isset($item['type']) && $item['type'] === 'google_maps') {
        $google_maps_exists = true;
        break;
    }
}
// أنواع معلومات الاتصال المدعومة
$contact_types = [
    'phone' => ['icon' => 'bi bi-telephone', 'label' => 'رقم الجوال', 'placeholder' => '+966 5X XXX XXXX'],
    'email' => ['icon' => 'bi bi-envelope', 'label' => 'البريد الإلكتروني', 'placeholder' => 'example@domain.com'],
    'location' => ['icon' => 'bi bi-geo-alt', 'label' => 'الموقع', 'placeholder' => 'العنوان، المدينة، البلد'],
    'working_hours' => ['icon' => 'bi bi-clock', 'label' => 'ساعات العمل', 'placeholder' => 'من الساعة 9 صباحاً إلى 6 مساءً'],
    'support_email' => ['icon' => 'bi bi-headset', 'label' => 'البريد للدعم', 'placeholder' => 'support@domain.com'],
    'fax' => ['icon' => 'bi bi-printer', 'label' => 'فاكس', 'placeholder' => '+966 X XXX XXXX'],
    'whatsapp_business' => ['icon' => 'bi bi-whatsapp', 'label' => 'واتساب للأعمال', 'placeholder' => '+966 5X XXX XXXX'],
    'google_maps' => ['icon' => 'bi bi-geo-alt-fill', 'label' => 'رابط خرائط جوجل', 'placeholder' => 'https://maps.google.com/?q=العنوان']
];

// معالجة إضافة وسائل التواصل الاجتماعي
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_social'])) {
    $social_type = trim($_POST['new_social_type'] ?? '');
    $social_link = trim($_POST['new_social_link'] ?? '');
    
    if (!empty($social_type) && !empty($social_link)) {
        try {
            // التحقق إذا كان النوع موجوداً في القائمة
            if (array_key_exists($social_type, $social_types)) {
                // التحقق من وجود سجل في الجدول
                $existing = $query->select('contact', "*", "WHERE id = 1");
                
                if (empty($existing)) {
                    // إنشاء سجل جديد
                    $data = [
                        $social_type => $social_link,
                        'id' => 1
                    ];
                    $result = $query->insert('contact', $data);
                    
                    if ($result) {
                        $_SESSION['success'] = "تم إضافة وسيلة التواصل بنجاح";
                        header("Location: " . $_SERVER['PHP_SELF'] . "?added=true&type=social");
                        exit();
                    }
                } else {
                    // تحديث السجل الموجود
                    $sql = "UPDATE contact SET $social_type=? WHERE id=1";
                    $result = $query->eQuery($sql, [$social_link]);
                    
                    if ($result) {
                        $_SESSION['success'] = "تم تحديث وسيلة التواصل بنجاح";
                        header("Location: " . $_SERVER['PHP_SELF'] . "?added=true&type=social");
                        exit();
                    }
                }
            } else {
                $_SESSION['error'] = "نوع وسيلة التواصل غير صحيح";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "حدث خطأ أثناء إضافة وسيلة التواصل: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "جميع الحقول مطلوبة";
    }
}

// معالجة حذف وسائل التواصل الاجتماعي
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_social'])) {
    $social_type = $_POST['social_type'] ?? '';
    
    if (array_key_exists($social_type, $social_types)) {
        try {
            $sql = "UPDATE contact SET $social_type=NULL WHERE id=1";
            $result = $query->eQuery($sql, []);
            
            if ($result) {
                $_SESSION['success'] = "تم حذف وسيلة التواصل بنجاح";
                header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=true&type=social");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "حدث خطأ أثناء الحذف: " . $e->getMessage();
        }
    }
}

// معالجة إضافة معلومات الاتصال
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_contact'])) {
    $contact_type = trim($_POST['new_contact_type'] ?? '');
    $contact_value = trim($_POST['new_contact_value'] ?? '');
    $contact_label = trim($_POST['new_contact_label'] ?? '');
    
    if (!empty($contact_type) && !empty($contact_value)) {
        try {
            $data = [
                'type' => $contact_type,
                'value' => $contact_value,
                'label' => $contact_label ?: ($contact_types[$contact_type]['label'] ?? $contact_type),
                'icon' => $contact_types[$contact_type]['icon'] ?? 'bi bi-info-circle'
            ];
            
            $result = $query->insert('contact_box', $data);
            
            if ($result) {
                $_SESSION['success'] = "تم إضافة معلومات الاتصال بنجاح";
                header("Location: " . $_SERVER['PHP_SELF'] . "?added=true&type=contact");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "حدث خطأ أثناء إضافة معلومات الاتصال: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "جميع الحقول المطلوبة يجب ملؤها";
    }
}

// معالجة حذف معلومات الاتصال
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_contact' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    try {
        $result = $query->eQuery('DELETE FROM contact_box WHERE id = ?', [$delete_id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'تم حذف معلومات الاتصال بنجاح']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في حذف معلومات الاتصال']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
    }
    exit;
}

// معالجة تحديث وسائل التواصل الاجتماعي
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_social'])) {
    $social_type = trim($_POST['social_type'] ?? '');
    $social_link = trim($_POST['social_link'] ?? '');
    
    if (!empty($social_type) && array_key_exists($social_type, $social_types)) {
        try {
            $sql = "UPDATE contact SET $social_type=? WHERE id=1";
            $result = $query->eQuery($sql, [$social_link]);
            
            if ($result) {
                $_SESSION['success'] = "تم تحديث وسيلة التواصل بنجاح";
                header("Location: " . $_SERVER['PHP_SELF'] . "?updated=true&type=social");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "حدث خطأ أثناء التحديث: " . $e->getMessage();
        }
    }
}

// معالجة تحديث معلومات الاتصال
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_contact'])) {
    $id = intval($_POST['id'] ?? 0);
    $value = trim($_POST['value'] ?? '');
    $label = trim($_POST['label'] ?? '');
    $type = trim($_POST['type'] ?? '');
    
    if ($id > 0 && !empty($value)) {
        try {
            $data = [
                'value' => $value,
                'label' => $label,
                'type' => $type
            ];
            
            $result = $query->update('contact_box', $data, "WHERE id = {$id}");
            
            if ($result) {
                $_SESSION['success'] = "تم تحديث معلومات الاتصال بنجاح";
                header("Location: " . $_SERVER['PHP_SELF'] . "?updated=true&type=contact");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "حدث خطأ أثناء تحديث معلومات الاتصال: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "جميع الحقول مطلوبة";
    }
}

// جلب بيانات للتحرير
$edit_social = null;
$edit_contact = null;

if (isset($_GET['edit_social'])) {
    $social_type = $_GET['edit_social'];
    if (array_key_exists($social_type, $social_types)) {
        $edit_social = [
            'type' => $social_type,
            'link' => $contact[$social_type] ?? ''
        ];
    }
}

if (isset($_GET['edit_contact'])) {
    $contact_id = intval($_GET['edit_contact']);
    $edit_contact = $query->select('contact_box', '*', "WHERE id = {$contact_id}")[0] ?? null;
}

// حساب إحصائيات وسائل التواصل
$active_socials = 0;
foreach ($social_types as $type => $info) {
    if (!empty($contact[$type])) {
        $active_socials++;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" >
    <?php if (!$google_maps_exists): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>ملاحظة:</strong> لم يتم إنشاء رابط خرائط جوجل بعد. هذا الرابط سيتم استخدامه في الهيدر الرئيسي.
    <form method="POST" class="d-inline">
        <input type="hidden" name="create_google_maps" value="1">
        <button type="submit" class="btn btn-sm btn-warning ms-2">إنشاء رابط خرائط جوجل</button>
    </form>
</div>
<?php endif; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>إدارة معلومات الاتصال - ركن الأماسي</title>
    <link href="../favicon.ico" rel="icon">
    
    <!-- مكتبات CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #e76a04;
            --primary-dark: #d45f00;
            --secondary-color: rgb(243, 212, 23);
            --dark-color: #144734ff;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --teal-color: #20c997;
            --border-color: #dee2e6;
            --border-radius: 8px;
            --border-radius-lg: 15px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* تصميم الشريط الجانبي */
        .sidebar {
            background-color: var(--dark-color);
            color: white;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            width: 250px;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(-5px);
        }
        
        .sidebar .nav-link i {
            font-size: 1.2rem;
        }
        
        .main-content {
            margin-right: 250px;
            padding: 20px;
            min-height: 100vh;
            transition: margin-right 0.3s ease;
        }
        
        /* بطاقة وسائل التواصل */
        .social-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .social-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--teal-color), var(--info-color));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }
        
        .social-card:hover::before {
            transform: scaleX(1);
        }
        
        .social-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(32, 201, 151, 0.15);
        }
        
        .social-icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .social-name {
            color: var(--dark-color);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .social-link {
            color: #666;
            font-size: 0.9rem;
            text-decoration: none;
            word-break: break-all;
            display: block;
            text-align: center;
            line-height: 1.4;
        }
        
        .social-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        .social-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        /* بطاقة معلومات الاتصال */
        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }
        
        .contact-card:hover::before {
            transform: scaleX(1);
        }
        
        .contact-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(231, 106, 4, 0.15);
        }
        
        .contact-icon-container {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
        }
        
        .contact-type {
            color: var(--dark-color);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .contact-value {
            color: #666;
            font-size: 0.95rem;
            text-align: center;
            line-height: 1.4;
        }
        
        .location-link {
            color: var(--info-color);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .location-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        .contact-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--teal-color), var(--info-color));
            color: white;
            padding: 6px 15px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .contact-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-action {
            flex: 1;
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--teal-color), #1baa8e);
            color: white;
        }
        
        .btn-edit:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        /* بطاقات الإحصائيات */
        .stats-card {
            background: linear-gradient(135deg, var(--teal-color), #1baa8e);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(32, 201, 151, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite linear;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(32, 201, 151, 0.3);
        }
        
        .stats-number {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* زر إضافة */
        .btn-add-contact {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--teal-color), var(--primary-color));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(32, 201, 151, 0.4);
            z-index: 100;
            transition: all 0.3s ease;
            animation: float 3s ease-in-out infinite;
            border: none;
            cursor: pointer;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        
        .btn-add-contact:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 15px 40px rgba(32, 201, 151, 0.6);
        }
        
        /* التصميم المتجاوب */
        @media (max-width: 1200px) {
            .sidebar {
                width: 220px;
            }
            
            .main-content {
                margin-right: 220px;
            }
            
            .stats-number {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .btn-add-contact {
                left: 20px;
                bottom: 20px;
                width: 60px;
                height: 60px;
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .stats-card {
                padding: 20px;
            }
            
            .stats-number {
                font-size: 2.2rem;
            }
            
            .social-card,
            .contact-card {
                padding: 15px;
            }
            
            .social-icon-container,
            .contact-icon-container {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .social-actions,
            .contact-actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 15px;
            }
            
            .stats-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .social-name,
            .contact-type {
                font-size: 1rem;
            }
            
            .btn-add-contact {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
                left: 15px;
                bottom: 15px;
            }
        }
        
        /* حالة عدم وجود بيانات */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        .empty-state-icon {
            font-size: 5rem;
            color: var(--teal-color);
            margin-bottom: 20px;
            opacity: 0.7;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }
        
        .empty-state-title {
            color: var(--dark-color);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .empty-state-text {
            color: #666;
            font-size: 1.1rem;
            max-width: 500px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }
        
        /* رسائل التنبيه */
        .alert-message {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease;
            border-right: 4px solid;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            border-right-color: var(--success-color);
            color: #155724;
        }
        
        .alert-error {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
            border-right-color: var(--danger-color);
            color: #721c24;
        }
        
        /* تأثيرات إضافية */
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stagger-animation > * {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }
        
        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
        
        /* زر القائمة المتنقلة */
        .mobile-menu-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--teal-color), var(--primary-color));
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            z-index: 1001;
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-btn:hover {
            transform: rotate(90deg);
        }
        
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: flex;
            }
        }
        
        /* أزرار مخصصة */
        .btn-teal {
            background: linear-gradient(135deg, var(--teal-color), #1baa8e);
            border-color: var(--teal-color);
            color: white;
        }
        
        .btn-teal:hover {
            background: linear-gradient(135deg, #1baa8e, #169177);
            border-color: #1baa8e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.3);
        }
        
        .text-teal {
            color: var(--teal-color) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #c35400);
            border-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
        }
    </style>
</head>

<body>
    <!-- زر القائمة المتنقلة -->
    <div class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </div>
    
    <!-- الشريط الجانبي -->
    <div class="sidebar" id="sidebar">
        <div class="text-center mb-4">
            <div class="admin-avatar mx-auto mb-2" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-size: 2rem; font-weight: bold;">أد</span>
            </div>
            <h5 class="mb-1">مسؤول النظام</h5>
            <small class="text-muted">ركن الأماسي</small>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="products.php">
                    <i class="bi bi-box-seam"></i>
                    إدارة المنتجات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="category.php">
                    <i class="bi bi-tags"></i>
                    إدارة الأقسام
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="banner.php">
                    <i class="bi bi-images"></i>
                    إدارة البنرات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_statistics.php">
                    <i class="bi bi-graph-up"></i>
                    إدارة الإحصائيات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="contact.php">
                    <i class="bi bi-telephone"></i>
                    إدارة الاتصال
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-left"></i>
                    تسجيل الخروج
                </a>
            </li>
        </ul>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="main-content fade-in">
        <!-- رسائل النجاح والخطأ -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php 
                if (isset($_GET['type'])) {
                    echo $_GET['type'] == 'social' ? 'تم إضافة وسيلة التواصل بنجاح.' : 'تم إضافة معلومات الاتصال بنجاح.';
                } else {
                    echo 'تمت العملية بنجاح.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php 
                if (isset($_GET['type'])) {
                    echo $_GET['type'] == 'social' ? 'تم تحديث وسيلة التواصل بنجاح.' : 'تم تحديث معلومات الاتصال بنجاح.';
                } else {
                    echo 'تمت العملية بنجاح.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php 
                if (isset($_GET['type'])) {
                    echo $_GET['type'] == 'social' ? 'تم حذف وسيلة التواصل بنجاح.' : 'تم حذف معلومات الاتصال بنجاح.';
                } else {
                    echo 'تمت العملية بنجاح.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-2" style="color: var(--dark-color); font-weight: 800;">
                    <i class="bi bi-telephone text-teal me-3"></i>
                    إدارة معلومات الاتصال
                </h1>
                <p class="text-muted mb-0">إدارة وسائل التواصل الاجتماعي ومعلومات الاتصال الخاصة بالمتجر</p>
            </div>
            <div class="d-none d-md-flex gap-2">
                <button type="button" class="btn btn-teal" onclick="openAddSocialModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة وسيلة تواصل
                </button>
                <button type="button" class="btn btn-teal" onclick="openAddContactModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة معلومات اتصال
                </button>
            </div>
        </div>

        <!-- إحصائيات -->
        <div class="row stagger-animation">
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element">
                    <div class="stats-number"><?php echo $active_socials; ?></div>
                    <div class="stats-label">وسائل التواصل</div>
                    <i class="bi bi-share display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                    <div class="stats-number"><?php echo count($contact_box); ?></div>
                    <div class="stats-label">معلومات الاتصال</div>
                    <i class="bi bi-telephone display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--success-color), #1e7e34);">
                    <div class="stats-number"><?php echo $active_socials + count($contact_box); ?></div>
                    <div class="stats-label">النشطة</div>
                    <i class="bi bi-check-circle display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--warning-color), #e0a800);">
                    <div class="stats-number"><?php echo count($social_types); ?></div>
                    <div class="stats-label">متاحة للإضافة</div>
                    <i class="bi bi-plus-circle display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
        </div>

        <!-- وسائل التواصل الاجتماعي -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 style="color: var(--teal-color); font-weight: 700;">
                    <i class="bi bi-share me-2"></i>
                    وسائل التواصل الاجتماعي
                </h3>
                <button type="button" class="btn btn-teal btn-sm" onclick="openAddSocialModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة وسيلة جديدة
                </button>
            </div>

            <?php if ($active_socials == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-share empty-state-icon"></i>
                    <h3 class="empty-state-title">لا توجد وسائل تواصل</h3>
                    <p class="empty-state-text">ابدأ بإضافة وسائل التواصل الاجتماعي الخاصة بمتجرك.</p>
                    <button type="button" class="btn btn-teal" onclick="openAddSocialModal()">
                        <i class="bi bi-plus-circle me-2"></i>
                        إضافة أول وسيلة توصل
                    </button>
                </div>
            <?php else: ?>
                <div class="row stagger-animation">
                    <?php foreach ($social_types as $type => $info): ?>
                        <?php if (!empty($contact[$type])): ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="social-card">
                                    <div class="social-icon-container" style="background-color: <?php echo $info['color']; ?>">
                                        <i class="<?php echo $info['icon']; ?>"></i>
                                    </div>
                                    
                                    <div class="social-name"><?php echo $info['label']; ?></div>
                                    
                                    <a href="<?php echo htmlspecialchars($contact[$type]); ?>" 
                                       target="_blank" class="social-link">
                                        <?php echo htmlspecialchars($contact[$type]); ?>
                                    </a>
                                    
                                    <div class="social-actions">
                                        <button type="button" class="btn-action btn-edit" onclick="editSocial('<?php echo $type; ?>')">
                                            <i class="bi bi-pencil"></i>
                                            تعديل
                                        </button>
                                        <button type="button" class="btn-action btn-delete" onclick="deleteSocial('<?php echo $type; ?>')">
                                            <i class="bi bi-trash"></i>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- معلومات الاتصال -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 style="color: var(--primary-color); font-weight: 700;">
                    <i class="bi bi-telephone me-2"></i>
                    معلومات الاتصال
                </h3>
                <button type="button" class="btn btn-teal btn-sm" onclick="openAddContactModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة معلومات جديدة
                </button>
            </div>

            <?php if (empty($contact_box)): ?>
                <div class="empty-state">
                    <i class="bi bi-telephone empty-state-icon"></i>
                    <h3 class="empty-state-title">لا توجد معلومات اتصال</h3>
                    <p class="empty-state-text">ابدأ بإضافة معلومات الاتصال الخاصة بمتجرك مثل رقم الجوال والبريد الإلكتروني.</p>
                    <button type="button" class="btn btn-teal" onclick="openAddContactModal()">
                        <i class="bi bi-plus-circle me-2"></i>
                        إضافة أول معلومة اتصال
                    </button>
                </div>
            <?php else: ?>
                <div class="row stagger-animation">
                    <?php foreach ($contact_box as $index => $contact_item): ?>
                        <?php 
                        // التحقق من وجود المفتاح 'type' قبل استخدامه
                        $contact_type = $contact_item['type'] ?? '';
                        
                        // استخدام الحقل 'label' أو 'title' أو قيمة افتراضية
                        $display_label = $contact_item['label'] ?? ($contact_item['title'] ?? ($contact_types[$contact_type]['label'] ?? 'معلومة اتصال'));
                        
                        // تحديد الأيقونة المناسبة
                        $display_icon = 'bi bi-info-circle'; // قيمة افتراضية
                        if (!empty($contact_item['icon'])) {
                            $display_icon = $contact_item['icon'];
                        } elseif (isset($contact_types[$contact_type]['icon'])) {
                            $display_icon = $contact_types[$contact_type]['icon'];
                        }
                        
                        // قيمة الاتصال
                        $contact_value = $contact_item['value'] ?? '';
                        ?>
                        
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="contact-card">
                                <div class="contact-badge">
                                    <?php echo $index + 1; ?>
                                </div>
                                
                                <div class="contact-icon-container">
                                    <i class="<?php echo htmlspecialchars($display_icon); ?>"></i>
                                </div>
                                
                                <div class="contact-type"><?php echo htmlspecialchars($display_label); ?></div>
                                
                                <div class="contact-value">
                                    <?php 
                                    // إذا كان نوع المعلومات هو "location"، قم بتحويله إلى رابط خرائط Google
                                    if ($contact_type === 'location' && !empty($contact_value)): 
                                        // تشفير العنوان لاستخدامه في رابط خرائط Google
                                        $encoded_address = urlencode($contact_value);
                                        $google_maps_url = "https://www.google.com/maps/search/?api=1&query=" . $encoded_address;
                                    ?>
                                        <a href="<?php echo $google_maps_url; ?>" 
                                           target="_blank" 
                                           class="location-link">
                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                            <?php echo htmlspecialchars($contact_value); ?>
                                        </a>
                                    <?php elseif ($contact_type === 'google_maps' && !empty($contact_value)): ?>
                                        <a href="<?php echo htmlspecialchars($contact_value); ?>" 
                                           target="_blank" 
                                           class="location-link">
                                            <i class="bi bi-map me-1"></i>
                                            انقر لفتح الخرائط
                                        </a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($contact_value); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contact-actions">
                                    <button type="button" class="btn-action btn-edit" onclick="editContact(<?php echo $contact_item['id']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                        تعديل
                                    </button>
                                    <button type="button" class="btn-action btn-delete" onclick="deleteContact(<?php echo $contact_item['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                        حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- زر إضافة عائم -->
    <div class="btn-add-contact" onclick="openAddMenu()">
        <i class="bi bi-plus-lg"></i>
    </div>

    <!-- مكتبات JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // إدارة القائمة المتنقلة
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // إغلاق القائمة عند النقر خارجها
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.getElementById('mobileMenuBtn');
            
            if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // فتح قائمة الإضافة
        function openAddMenu() {
            Swal.fire({
                title: 'ماذا تريد إضافته؟',
                showCancelButton: true,
                confirmButtonText: 'وسيلة تواصل',
                cancelButtonText: 'معلومات اتصال',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-teal me-2',
                    cancelButton: 'btn btn-teal'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    openAddSocialModal();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    openAddContactModal();
                }
            });
        }

        // حذف وسيلة التواصل
        function deleteSocial(type) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذه الوسيلة!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذفها!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('delete_social', '1');
                    formData.append('social_type', type);
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            return response.text();
                        }
                    })
                    .then(data => {
                        if (data) {
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                        console.error('Error:', error);
                    });
                }
            });
        }

        // حذف معلومات الاتصال
        function deleteContact(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذه المعلومة!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذفها!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete_contact');
                    formData.append('delete_id', id);
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // إزالة البطاقة مع تأثير
                            const contactCard = document.querySelector(`[onclick="deleteContact(${id})"]`).closest('.contact-card');
                            if (contactCard) {
                                contactCard.style.opacity = '0';
                                contactCard.style.transform = 'translateY(-20px) scale(0.95)';
                                
                                setTimeout(() => {
                                    contactCard.remove();
                                    
                                    // التحقق إذا لم تعد هناك معلومات اتصال
                                    if (document.querySelectorAll('.contact-card').length === 0) {
                                        location.reload();
                                    }
                                    
                                    // تحديث الإحصائيات
                                    updateStats();
                                    
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }, 300);
                            }
                        } else {
                            Swal.fire('خطأ!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                        console.error('Error:', error);
                    });
                }
            });
        }

        // تعديل وسيلة التواصل
        function editSocial(type) {
            window.location.href = '?edit_social=' + type;
        }

        // تعديل معلومات الاتصال
        function editContact(id) {
            window.location.href = '?edit_contact=' + id;
        }

        // فتح مودال إضافة وسيلة تواصل
        function openAddSocialModal() {
            createSocialModal();
            const modal = new bootstrap.Modal(document.getElementById('socialModal'));
            modal.show();
        }

        // فتح مودال إضافة معلومات اتصال
        function openAddContactModal() {
            createContactModal();
            const modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }

        // إنشاء مودال وسائل التواصل
        function createSocialModal() {
            // إزالة المودال السابق إذا كان موجوداً
            const existingModal = document.getElementById('socialModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const socialTypes = <?php echo json_encode($social_types); ?>;
            const existingSocials = <?php echo json_encode($contact); ?>;
            
            // تصفية الوسائل غير المضافة
            const availableSocials = {};
            for (const [key, value] of Object.entries(socialTypes)) {
                if (!existingSocials[key]) {
                    availableSocials[key] = value;
                }
            }
            
            const isEditMode = window.location.search.includes('edit_social');
            const editType = isEditMode ? new URLSearchParams(window.location.search).get('edit_social') : null;
            const editData = isEditMode && editType ? {type: editType, link: existingSocials[editType] || ''} : null;
            
            const modalHTML = `
                <div class="modal fade" id="socialModal" tabindex="-1" aria-labelledby="socialModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="socialModalLabel">
                                    <i class="bi ${isEditMode ? 'bi-pencil' : 'bi-plus-circle'} me-2"></i>
                                    ${isEditMode ? 'تعديل وسيلة التواصل' : 'إضافة وسيلة تواصل جديدة'}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" id="socialForm">
                                ${isEditMode ? '<input type="hidden" name="update_social" value="1">' : '<input type="hidden" name="add_social" value="1">'}
                                ${isEditMode && editData ? '<input type="hidden" name="social_type" value="' + editData.type + '">' : ''}
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="social_type" class="form-label">نوع الوسيلة *</label>
                                        ${isEditMode && editData ? 
                                            `<input type="text" class="form-control" value="${socialTypes[editData.type]?.label || editData.type}" readonly>
                                             <input type="hidden" name="social_type" value="${editData.type}">` :
                                            `<select class="form-select" name="new_social_type" id="social_type" required ${Object.keys(availableSocials).length === 0 ? 'disabled' : ''}>
                                                <option value="">اختر وسيلة التواصل</option>
                                                ${Object.entries(availableSocials).map(([key, info]) => 
                                                    `<option value="${key}" data-color="${info.color}" data-icon="${info.icon}">
                                                        ${info.label}
                                                    </option>`
                                                ).join('')}
                                             </select>
                                             ${Object.keys(availableSocials).length === 0 ? 
                                                '<div class="alert alert-info mt-2">جميع وسائل التواصل متاحة بالفعل</div>' : ''}`
                                        }
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="social_link" class="form-label">رابط الوسيلة *</label>
                                        <input type="url" class="form-control" name="${isEditMode ? 'social_link' : 'new_social_link'}" 
                                               id="social_link" 
                                               value="${isEditMode && editData ? editData.link : ''}"
                                               placeholder="https://example.com/username" 
                                               required>
                                    </div>
                                    
                                    ${!isEditMode ? `
                                        <div class="mb-3">
                                            <label class="form-label mb-2">معاينة الوسيلة</label>
                                            <div class="d-flex justify-content-center">
                                                <div id="socialPreview" class="social-icon-container" style="background-color: #ccc; width: 80px; height: 80px;">
                                                    <i id="socialIconPreview" class="bi bi-link-45deg"></i>
                                                </div>
                                            </div>
                                            <div id="socialNamePreview" class="text-center mt-2 text-muted">اختر وسيلة تواصل</div>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-teal">
                                        ${isEditMode ? 'تحديث الوسيلة' : 'إضافة الوسيلة'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // إضافة معالج الحدث لمعاينة الوسيلة
            if (!isEditMode) {
                const socialSelect = document.getElementById('social_type');
                const previewContainer = document.getElementById('socialPreview');
                const iconPreview = document.getElementById('socialIconPreview');
                const namePreview = document.getElementById('socialNamePreview');
                
                if (socialSelect) {
                    socialSelect.addEventListener('change', function(e) {
                        const selectedOption = this.options[this.selectedIndex];
                        const color = selectedOption.dataset.color;
                        const icon = selectedOption.dataset.icon;
                        const name = selectedOption.textContent;
                        
                        if (color && icon) {
                            previewContainer.style.backgroundColor = color;
                            iconPreview.className = icon;
                            namePreview.textContent = name;
                        }
                    });
                }
            }
            
            // معالجة إغلاق المودال
            const modalElement = document.getElementById('socialModal');
            modalElement.addEventListener('hidden.bs.modal', function () {
                // تنظيف الرابط
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('edit_social')) {
                    urlParams.delete('edit_social');
                    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                }
                
                // إزالة المودال من DOM
                setTimeout(() => {
                    if (modalElement && modalElement.parentNode) {
                        modalElement.remove();
                    }
                }, 300);
            });
        }

        // إنشاء مودال معلومات الاتصال
        function createContactModal() {
            // إزالة المودال السابق إذا كان موجوداً
            const existingModal = document.getElementById('contactModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const contactTypes = <?php echo json_encode($contact_types); ?>;
            const existingContacts = <?php echo json_encode($contact_box); ?>;
            const existingTypes = existingContacts.map(c => c.type);
            
            // تصفية الأنواع غير المستخدمة
            const availableTypes = {};
            for (const [key, value] of Object.entries(contactTypes)) {
                if (!existingTypes.includes(key)) {
                    availableTypes[key] = value;
                }
            }
            
            const isEditMode = window.location.search.includes('edit_contact');
            const editId = isEditMode ? new URLSearchParams(window.location.search).get('edit_contact') : null;
            const editData = isEditMode && editId ? <?php echo isset($edit_contact) ? json_encode($edit_contact) : 'null'; ?> : null;
            
            const modalHTML = `
                <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="contactModalLabel">
                                    <i class="bi ${isEditMode ? 'bi-pencil' : 'bi-plus-circle'} me-2"></i>
                                    ${isEditMode ? 'تعديل معلومات الاتصال' : 'إضافة معلومات اتصال جديدة'}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" id="contactForm">
                                ${isEditMode ? '<input type="hidden" name="update_contact" value="1">' : '<input type="hidden" name="add_contact" value="1">'}
                                ${isEditMode && editData ? '<input type="hidden" name="id" value="' + editData.id + '">' : ''}
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="contact_type" class="form-label">نوع المعلومات *</label>
                                        ${isEditMode && editData ? 
                                            `<input type="text" class="form-control" value="${contactTypes[editData.type]?.label || editData.type}" readonly>
                                             <input type="hidden" name="type" value="${editData.type}">
                                             <input type="hidden" name="new_contact_type" value="${editData.type}">` :
                                            `<select class="form-select" name="new_contact_type" id="contact_type" required ${Object.keys(availableTypes).length === 0 ? 'disabled' : ''}>
                                                <option value="">اختر نوع المعلومات</option>
                                                ${Object.entries(availableTypes).map(([key, info]) => 
                                                    `<option value="${key}" data-icon="${info.icon}" data-placeholder="${info.placeholder}">
                                                        ${info.label}
                                                    </option>`
                                                ).join('')}
                                             </select>
                                             ${Object.keys(availableTypes).length === 0 ? 
                                                '<div class="alert alert-info mt-2">جميع أنواع المعلومات متاحة بالفعل</div>' : ''}`
                                        }
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contact_label" class="form-label">التسمية</label>
                                        <input type="text" class="form-control" name="${isEditMode ? 'label' : 'new_contact_label'}" id="contact_label"
                                               value="${isEditMode && editData ? (editData.label || editData.title || '') : ''}"
                                               placeholder="مثال: رقم الجوال الرئيسي">
                                        <small class="text-muted">اتركه فارغاً لاستخدام التسمية الافتراضية</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contact_value" class="form-label">القيمة *</label>
                                        <input type="text" class="form-control" name="${isEditMode ? 'value' : 'new_contact_value'}" 
                                               id="contact_value"
                                               value="${isEditMode && editData ? editData.value : ''}"
                                               placeholder="${isEditMode && editData ? (contactTypes[editData.type]?.placeholder || 'أدخل القيمة') : 'أدخل القيمة'}"
                                               required>
                                    </div>
                                    
                                    ${!isEditMode ? `
                                        <div class="mb-3">
                                            <label class="form-label mb-2">معاينة المعلومات</label>
                                            <div class="d-flex justify-content-center">
                                                <div id="contactPreview" class="contact-icon-container" style="width: 80px; height: 80px;">
                                                    <i id="contactIconPreview" class="bi bi-telephone"></i>
                                                </div>
                                            </div>
                                            <div id="contactNamePreview" class="text-center mt-2 text-muted">اختر نوع المعلومات</div>
                                        </div>
                                    ` : ''}
                                    
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>ملاحظة:</strong> بالنسبة لرابط خرائط جوجل، يمكنك نسخ الرابط من موقع Google Maps والصقه هنا
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-teal">
                                        ${isEditMode ? 'تحديث المعلومات' : 'إضافة المعلومات'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // إضافة معالج الحدث لمعاينة المعلومات
            if (!isEditMode) {
                const contactSelect = document.getElementById('contact_type');
                const previewContainer = document.getElementById('contactPreview');
                const iconPreview = document.getElementById('contactIconPreview');
                const namePreview = document.getElementById('contactNamePreview');
                const valueInput = document.getElementById('contact_value');
                
                if (contactSelect) {
                    contactSelect.addEventListener('change', function(e) {
                        const selectedOption = this.options[this.selectedIndex];
                        const icon = selectedOption.dataset.icon;
                        const name = selectedOption.textContent;
                        const placeholder = selectedOption.dataset.placeholder || 'أدخل القيمة';
                        
                        if (icon) {
                            iconPreview.className = icon;
                            namePreview.textContent = name;
                            
                            // تحديث placeholder
                            valueInput.placeholder = placeholder;
                            
                            // إذا كان google_maps، أضف نص مساعد
                            if (selectedOption.value === 'google_maps') {
                                valueInput.value = 'https://www.google.com/maps/place/';
                            }
                        }
                    });
                }
            }
            
            // معالجة إغلاق المودال
            const modalElement = document.getElementById('contactModal');
            modalElement.addEventListener('hidden.bs.modal', function () {
                // تنظيف الرابط
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('edit_contact')) {
                    urlParams.delete('edit_contact');
                    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                }
                
                // إزالة المودال من DOM
                setTimeout(() => {
                    if (modalElement && modalElement.parentNode) {
                        modalElement.remove();
                    }
                }, 300);
            });
        }

        // تهيئة تأثيرات عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            // إغلاق رسائل التنبيه تلقائياً
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // تحسين تجربة المستخدم على الأجهزة الصغيرة
            if (window.innerWidth < 992) {
                document.body.style.overflowX = 'hidden';
            }
            
            // فتح المودال تلقائياً إذا كان هناك تعديل
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit_social')) {
                setTimeout(() => openAddSocialModal(), 500);
            } else if (urlParams.has('edit_contact')) {
                setTimeout(() => openAddContactModal(), 500);
            }
        });

        // تحديث الإحصائيات مع تأثيرات
        function updateStats() {
            const statsNumbers = document.querySelectorAll('.stats-number');
            
            // تحديث عدد وسائل التواصل
            const socialCards = document.querySelectorAll('.social-card');
            if (statsNumbers[0]) {
                statsNumbers[0].textContent = socialCards.length;
            }
            
            // تحديث عدد معلومات الاتصال
            const contactCards = document.querySelectorAll('.contact-card');
            if (statsNumbers[1]) {
                statsNumbers[1].textContent = contactCards.length;
            }
            
            // تحديث عدد النشطة
            if (statsNumbers[2]) {
                statsNumbers[2].textContent = socialCards.length + contactCards.length;
            }
            
            // إعادة تشغيل تأثيرات الرقم
            statsNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent) || 0;
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(currentValue);
                }, 20);
            });
        }
        
        // تحديث الإحصائيات عند التحميل
        updateStats();
    </script>
</body>
</html>