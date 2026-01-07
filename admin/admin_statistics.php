<?php
include 'check.php';

// جلب الإحصائيات
$statistics = $query->select('statistics', '*', 'ORDER BY id DESC');

// معالجة إضافة إحصائية
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $icon = trim($_POST['icon']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $count = intval($_POST['count']);

    try {
        $data = [
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
            'count' => $count
        ];

        $query->insert('statistics', $data);
        header("Location: {$_SERVER['PHP_SELF']}?added=true");
        exit;
        
    } catch (Exception $e) {
        $error = "حدث خطأ أثناء إضافة الإحصائية: " . $e->getMessage();
    }
}

// معالجة تعديل إحصائية
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $icon = trim($_POST['icon']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $count = intval($_POST['count']);

    try {
        $data = [
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
            'count' => $count
        ];

        $query->update('statistics', $data, "WHERE id = {$id}");
        header("Location: {$_SERVER['PHP_SELF']}?updated=true");
        exit;
        
    } catch (Exception $e) {
        $error = "حدث خطأ أثناء تحديث الإحصائية: " . $e->getMessage();
    }
}

// معالجة حذف إحصائية
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    try {
        $deleteResult = $query->eQuery('DELETE FROM statistics WHERE id = ?', [$delete_id]);
        
        if ($deleteResult) {
            echo json_encode(['success' => true, 'message' => 'تم حذف الإحصائية بنجاح']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في حذف الإحصائية']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
    }
    exit;
}

// جلب بيانات الإحصائية للتعديل
$edit_statistic = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_statistic = $query->select('statistics', '*', "WHERE id = {$edit_id}")[0] ?? null;
}

// أيقونات Bootstrap المتاحة
$available_icons = [
    'bi bi-emoji-smile',
    'bi bi-journal-richtext',
    'bi bi-headset',
    'bi bi-people',
    'bi bi-award',
    'bi bi-clock',
    'bi bi-geo-alt',
    'bi bi-star',
    'bi bi-heart',
    'bi bi-check-circle',
    'bi bi-graph-up',
    'bi bi-trophy',
    'bi bi-shield-check',
    'bi bi-lightning',
    'bi bi-briefcase',
    'bi bi-building',
    'bi bi-calculator',
    'bi bi-calendar-check',
    'bi bi-cash',
    'bi bi-chat-dots',
    'bi bi-cloud-check',
    'bi bi-code-slash',
    'bi bi-cup',
    'bi bi-display',
    'bi bi-flag',
    'bi bi-globe',
    'bi bi-hand-thumbs-up',
    'bi bi-laptop',
    'bi bi-megaphone',
    'bi bi-patch-check',
    'bi bi-person-check',
    'bi bi-phone',
    'bi bi-rocket',
    'bi bi-shield-lock',
    'bi bi-speedometer',
    'bi bi-tools',
    'bi bi-truck',
    'bi bi-wallet'
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>إدارة الإحصائيات - ركن الأماسي</title>
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
            --purple-color: #6f42c1;
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
        
        /* بطاقة الإحصائية */
        .statistic-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        
        .statistic-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--purple-color), var(--secondary-color));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }
        
        .statistic-card:hover::before {
            transform: scaleX(1);
        }
        
        .statistic-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(111, 66, 193, 0.15);
        }
        
        .statistic-icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .statistic-icon-container::after {
            content: '';
            position: absolute;
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
            border-radius: 50%;
            opacity: 0.2;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.2; }
            50% { transform: scale(1.1); opacity: 0.1; }
            100% { transform: scale(1); opacity: 0.2; }
        }
        
        .statistic-icon {
            font-size: 2rem;
            color: white;
            z-index: 2;
        }
        
        .statistic-count {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--purple-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .statistic-title {
            color: var(--dark-color);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .statistic-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .statistic-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
            color: white;
            padding: 6px 15px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .statistic-actions {
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
            background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
            color: white;
        }
        
        .btn-edit:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
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
            background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(111, 66, 193, 0.2);
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
            box-shadow: 0 15px 40px rgba(111, 66, 193, 0.3);
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
        
        /* زر إضافة إحصائية */
        .btn-add-statistic {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--purple-color), var(--secondary-color));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(111, 66, 193, 0.4);
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
        
        .btn-add-statistic:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 15px 40px rgba(111, 66, 193, 0.6);
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
            
            .btn-add-statistic {
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
            
            .statistic-card {
                padding: 15px;
            }
            
            .statistic-icon-container {
                width: 70px;
                height: 70px;
            }
            
            .statistic-icon {
                font-size: 1.8rem;
            }
            
            .statistic-count {
                font-size: 2.2rem;
            }
            
            .statistic-actions {
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
            
            .statistic-title {
                font-size: 1.2rem;
            }
            
            .statistic-count {
                font-size: 2rem;
            }
            
            .btn-add-statistic {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
                left: 15px;
                bottom: 15px;
            }
        }
        
        @media (max-width: 400px) {
            .statistic-icon-container {
                width: 60px;
                height: 60px;
            }
            
            .statistic-icon {
                font-size: 1.5rem;
            }
            
            .statistic-badge {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
            
            .stats-card {
                padding: 12px;
            }
            
            .stats-number {
                font-size: 1.8rem;
            }
        }
        
        /* حالة عدم وجود إحصائيات */
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
            color: var(--purple-color);
            margin-bottom: 20px;
            opacity: 0.7;
            animation: pulse 2s infinite;
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
        .stagger-animation > *:nth-child(6) { animation-delay: 0.6s; }
        
        /* زر القائمة المتنقلة */
        .mobile-menu-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--purple-color), var(--secondary-color));
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            z-index: 1001;
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
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
        
        /* تحسينات للوضع الداكن */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            }
            
            .statistic-card {
                background: #2c3e50;
                border-color: #34495e;
            }
            
            .statistic-title {
                color: #ecf0f1;
            }
            
            .statistic-description {
                color: #bdc3c7;
            }
            
            .empty-state {
                background: #2c3e50;
            }
            
            .empty-state-title {
                color: #ecf0f1;
            }
            
            .empty-state-text {
                color: #bdc3c7;
            }
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
                <a class="nav-link" href="admin_banners.php">
                    <i class="bi bi-images"></i>
                    إدارة البنرات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin_statistics.php">
                    <i class="bi bi-graph-up"></i>
                    إدارة الإحصائيات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">
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
        <!-- رسائل النجاح -->
        <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
            <div class="alert-message alert-success">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="mb-1">تمت العملية بنجاح!</h5>
                        <p class="mb-0">تم إضافة الإحصائية بنجاح.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true'): ?>
            <div class="alert-message alert-success">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="mb-1">تمت العملية بنجاح!</h5>
                        <p class="mb-0">تم تحديث الإحصائية بنجاح.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-2" style="color: var(--dark-color); font-weight: 800;">
                    <i class="bi bi-graph-up text-purple me-3"></i>
                    إدارة الإحصائيات
                </h1>
                <p class="text-muted mb-0">إدارة وإضافة وتعديل إحصائيات الموقع</p>
            </div>
            <button type="button" class="btn btn-purple d-none d-md-flex" onclick="openAddStatisticModal()">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة إحصائية جديدة
            </button>
        </div>

        <!-- إحصائيات -->
        <div class="row stagger-animation">
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element">
                    <div class="stats-number"><?php echo count($statistics); ?></div>
                    <div class="stats-label">الإحصائيات</div>
                    <i class="bi bi-graph-up display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                    <div class="stats-number">
                        <?php 
                        $totalCount = 0;
                        foreach ($statistics as $stat) {
                            $totalCount += $stat['count'];
                        }
                        echo number_format($totalCount);
                        ?>
                    </div>
                    <div class="stats-label">مجموع الأرقام</div>
                    <i class="bi bi-calculator display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--success-color), #1e7e34);">
                    <div class="stats-number"><?php echo count($statistics); ?></div>
                    <div class="stats-label">نشطة</div>
                    <i class="bi bi-check-circle display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--warning-color), #e0a800);">
                    <div class="stats-number">
                        <?php 
                        $uniqueIcons = [];
                        foreach ($statistics as $stat) {
                            if (!in_array($stat['icon'], $uniqueIcons)) {
                                $uniqueIcons[] = $stat['icon'];
                            }
                        }
                        echo count($uniqueIcons);
                        ?>
                    </div>
                    <div class="stats-label">أيقونات مختلفة</div>
                    <i class="bi bi-palette display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
        </div>

        <!-- قائمة الإحصائيات -->
        <?php if (empty($statistics)): ?>
            <div class="empty-state fade-in">
                <i class="bi bi-graph-up empty-state-icon"></i>
                <h2 class="empty-state-title">لا توجد إحصائيات</h2>
                <p class="empty-state-text">ابدأ بإضافة أول إحصائية إلى موقعك لتظهر هنا.</p>
                <button type="button" class="btn btn-purple btn-lg" onclick="openAddStatisticModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة أول إحصائية
                </button>
            </div>
        <?php else: ?>
            <div class="row stagger-animation">
                <?php foreach ($statistics as $i => $stat): ?>
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="statistic-card">
                            <div class="statistic-badge">
                                <?php echo $i + 1; ?>
                            </div>
                            
                            <div class="statistic-icon-container">
                                <i class="statistic-icon <?php echo htmlspecialchars($stat['icon']); ?>"></i>
                            </div>
                            
                            <div class="statistic-count"><?php echo number_format($stat['count']); ?></div>
                            
                            <h3 class="statistic-title"><?php echo htmlspecialchars($stat['title']); ?></h3>
                            
                            <p class="statistic-description">
                                <?php echo htmlspecialchars($stat['description']); ?>
                            </p>
                            
                            <div class="statistic-actions">
                                <button type="button" class="btn-action btn-edit" onclick="editStatistic(<?php echo $stat['id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                    تعديل
                                </button>
                                <button type="button" class="btn-action btn-delete" onclick="deleteStatistic(<?php echo $stat['id']; ?>)">
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

    <!-- زر إضافة إحصائية عائم -->
    <button type="button" class="btn-add-statistic" onclick="openAddStatisticModal()">
        <i class="bi bi-plus-lg"></i>
    </button>

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

        // حذف الإحصائية
        function deleteStatistic(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذه الإحصائية!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذفها!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // إرسال طلب AJAX للحذف
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('delete_id', id);
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // إزالة الإحصائية مع تأثير
                            const statisticCard = document.querySelector(`[onclick="deleteStatistic(${id})"]`).closest('.statistic-card');
                            statisticCard.style.opacity = '0';
                            statisticCard.style.transform = 'translateY(-20px) scale(0.95)';
                            
                            setTimeout(() => {
                                statisticCard.remove();
                                
                                // التحقق إذا لم تعد هناك إحصائيات
                                if (document.querySelectorAll('.statistic-card').length === 0) {
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

        // تعديل الإحصائية
        function editStatistic(id) {
            window.location.href = '?edit=' + id;
        }

        // تأثيرات عند التمرير
        window.addEventListener('scroll', function() {
            const statisticCards = document.querySelectorAll('.statistic-card');
            const windowHeight = window.innerHeight;
            
            statisticCards.forEach(card => {
                const cardPosition = card.getBoundingClientRect().top;
                
                if (cardPosition < windowHeight - 100) {
                    card.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });

        // تهيئة تأثيرات عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات للبطاقات
            const statisticCards = document.querySelectorAll('.statistic-card');
            statisticCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            // إغلاق رسائل التنبيه تلقائياً
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-message');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
            
            // إضافة تأثيرات للأزرار
            const buttons = document.querySelectorAll('.btn-action');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // تحسين تجربة المستخدم على الأجهزة الصغيرة
            if (window.innerWidth < 992) {
                document.body.style.overflowX = 'hidden';
            }
            
            // إضافة تأثيرات للوضع الداكن
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.querySelectorAll('.statistic-card').forEach(card => {
                    card.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
                });
            }
            
            // فتح المودال تلقائياً إذا كان هناك edit
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit')) {
                openStatisticModal();
            }
        });

        // تحديث الإحصائيات مع تأثيرات
        function updateStats() {
            const statsNumbers = document.querySelectorAll('.stats-number');
            
            // تحديث عدد الإحصائيات
            const statisticCount = document.querySelectorAll('.statistic-card').length;
            statsNumbers[0].textContent = statisticCount;
            
            // تحديث مجموع الأرقام
            let totalCount = 0;
            document.querySelectorAll('.statistic-count').forEach(countEl => {
                const count = parseInt(countEl.textContent.replace(/,/g, ''));
                totalCount += count;
            });
            statsNumbers[1].textContent = totalCount.toLocaleString();
            
            // تحديث عدد الإحصائيات النشطة
            statsNumbers[2].textContent = statisticCount;
            
            // تحديث عدد الأيقونات المختلفة
            const icons = new Set();
            document.querySelectorAll('.statistic-icon').forEach(iconEl => {
                const iconClass = Array.from(iconEl.classList).find(cls => cls.startsWith('bi-'));
                if (iconClass) {
                    icons.add(iconClass);
                }
            });
            statsNumbers[3].textContent = icons.size;
            
            // إعادة تشغيل تأثيرات الرقم
            statsNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(currentValue).toLocaleString();
                }, 20);
            });
        }

        // فتح مودال إضافة/تعديل الإحصائية
        function openStatisticModal() {
            // إنشاء المودال ديناميكياً إذا لم يكن موجوداً
            if (!document.getElementById('statisticModal')) {
                createStatisticModal();
            }
            
            // فتح المودال
            const modal = new bootstrap.Modal(document.getElementById('statisticModal'));
            modal.show();
        }

        // فتح مودال إضافة جديد
        function openAddStatisticModal() {
            // تنظيف أي edit في الرابط
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit')) {
                urlParams.delete('edit');
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }
            
            openStatisticModal();
        }

        // إنشاء مودال الإحصائية ديناميكياً
        function createStatisticModal() {
            const urlParams = new URLSearchParams(window.location.search);
            const isEditMode = urlParams.has('edit');
            const editId = isEditMode ? urlParams.get('edit') : null;
            
            // بيانات الأيقونات المتاحة من PHP
            const availableIcons = <?php echo json_encode($available_icons); ?>;
            
            const modalHTML = `
                <div class="modal fade" id="statisticModal" tabindex="-1" aria-labelledby="statisticModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="statisticModalLabel">
                                    <i class="bi ${isEditMode ? 'bi-pencil' : 'bi-plus-circle'} me-2"></i>
                                    ${isEditMode ? 'تعديل الإحصائية' : 'إضافة إحصائية جديدة'}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" id="statisticForm">
                                ${isEditMode ? '<input type="hidden" name="update" value="1">' : '<input type="hidden" name="add" value="1">'}
                                ${isEditMode && editId ? '<input type="hidden" name="id" value="' + editId + '">' : ''}
                                
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="icon" class="form-label">الأيقونة *</label>
                                            <select class="form-select" name="icon" id="icon" required>
                                                <option value="">اختر أيقونة</option>
                                                ${availableIcons.map(icon => 
                                                    `<option value="${icon}" ${isEditMode && <?php echo isset($edit_statistic) ? 'true' : 'false'; ?> && '<?php echo isset($edit_statistic) ? htmlspecialchars($edit_statistic["icon"]) : ""; ?>' === icon ? 'selected' : ''}>
                                                        ${icon}
                                                    </option>`
                                                ).join('')}
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="count" class="form-label">العدد *</label>
                                            <input type="number" class="form-control" name="count" id="count"
                                                value="${isEditMode && <?php echo isset($edit_statistic) ? 'true' : 'false'; ?> ? '<?php echo isset($edit_statistic) ? htmlspecialchars($edit_statistic["count"]) : ""; ?>' : ''}" 
                                                min="0" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">العنوان *</label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="${isEditMode && <?php echo isset($edit_statistic) ? 'true' : 'false'; ?> ? '<?php echo isset($edit_statistic) ? htmlspecialchars($edit_statistic["title"]) : ""; ?>' : ''}" 
                                            required maxlength="255">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">الوصف *</label>
                                        <textarea class="form-control" name="description" id="description" 
                                                  rows="3" required maxlength="500">${isEditMode && <?php echo isset($edit_statistic) ? 'true' : 'false'; ?> ? '<?php echo isset($edit_statistic) ? htmlspecialchars($edit_statistic["description"]) : ""; ?>' : ''}</textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label mb-2">معاينة الأيقونة</label>
                                        <div class="d-flex justify-content-center">
                                            <div class="statistic-icon-container-preview" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--purple-color), #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i id="iconPreview" class="statistic-icon ${isEditMode && <?php echo isset($edit_statistic) ? 'true' : 'false'; ?> ? '<?php echo isset($edit_statistic) ? htmlspecialchars($edit_statistic["icon"]) : ""; ?>' : ''}"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-purple">
                                        ${isEditMode ? 'تحديث الإحصائية' : 'إضافة الإحصائية'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // إضافة معالج الحدث لتحديث معاينة الأيقونة
            document.getElementById('icon')?.addEventListener('change', function(e) {
                const iconPreview = document.getElementById('iconPreview');
                if (iconPreview) {
                    // إزالة جميع فئات الأيقونة الحالية
                    Array.from(iconPreview.classList).forEach(cls => {
                        if (cls.startsWith('bi-')) {
                            iconPreview.classList.remove(cls);
                        }
                    });
                    // إضافة الأيقونة الجديدة
                    const iconValue = e.target.value;
                    if (iconValue) {
                        const iconClasses = iconValue.split(' ');
                        iconClasses.forEach(cls => iconPreview.classList.add(cls));
                    }
                }
            });
            
            // إضافة معالج الحدث لإغلاق المودال
            const modalElement = document.getElementById('statisticModal');
            modalElement.addEventListener('hidden.bs.modal', function () {
                // تنظيف الرابط
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('edit')) {
                    urlParams.delete('edit');
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
        
        // إضافة لون أرجواني للزر
        const style = document.createElement('style');
        style.textContent = `
            .btn-purple {
                background: linear-gradient(135deg, var(--purple-color), #8b5cf6);
                border-color: var(--purple-color);
                color: white;
            }
            
            .btn-purple:hover {
                background: linear-gradient(135deg, #5c32a8, #7c3aed);
                border-color: #5c32a8;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
            }
            
            .text-purple {
                color: var(--purple-color) !important;
            }
        `;
        document.head.appendChild(style);
        
        // تحديث الإحصائيات عند التحميل
        updateStats();
    </script>
</body>
</html>