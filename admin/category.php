<?php
include 'check.php';

// جلب الأقسام مع عدد المنتجات
$categories = $query->eQuery('SELECT 
    c.id,
    c.category_name, 
    COUNT(p.id) AS product_count
FROM 
    category c
LEFT JOIN 
    products p ON c.id = p.category_id
GROUP BY 
    c.id, c.category_name
ORDER BY 
    c.category_name ASC');

// إضافة قسم جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        try {
            $query->eQuery('INSERT INTO category (category_name) VALUES (?)', [$category_name]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?added=true");
            exit;
        } catch (Exception $e) {
            $error_message = "خطأ في إضافة القسم: " . $e->getMessage();
        }
    }
}

// تحديث قسم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        try {
            $query->eQuery('UPDATE category SET category_name = ? WHERE id = ?', [$category_name, $id]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?updated=true");
            exit;
        } catch (Exception $e) {
            $error_message = "خطأ في تحديث القسم: " . $e->getMessage();
        }
    }
}

// حذف قسم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    try {
        // التحقق من وجود منتجات في القسم
        $productCount = $query->select('products', 'COUNT(*) as count', "WHERE category_id = $delete_id")[0]['count'];
        
        if ($productCount > 0) {
            echo json_encode(['success' => false, 'message' => 'لا يمكن حذف القسم لأنه يحتوي على منتجات. الرجاء نقل المنتجات أولاً.']);
            exit;
        }
        
        $deleteResult = $query->eQuery('DELETE FROM category WHERE id = ?', [$delete_id]);
        
        if ($deleteResult) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في حذف القسم.']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
    }
    exit;
}

// إذا كان هناك edit_id، عرض مودال التعديل
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_category = null;

if ($edit_id > 0) {
    $edit_category = $query->select('category', '*', "WHERE id = $edit_id")[0] ?? null;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>إدارة الأقسام - ركن الأماسي</title>
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
        
        /* بطاقة القسم */
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--dark-color), var(--primary-color));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }
        
        .category-card:hover::before {
            transform: scaleX(1);
        }
        
        .category-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(20, 71, 52, 0.15);
        }
        
        .category-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--dark-color), #1e5b48);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 8px 20px rgba(20, 71, 52, 0.2);
            transition: all 0.3s ease;
        }
        
        .category-card:hover .category-icon {
            transform: scale(1.1) rotate(10deg);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }
        
        .category-name {
            color: var(--dark-color);
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .category-stats {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .product-count {
            background: linear-gradient(135deg, rgba(20, 71, 52, 0.1), rgba(20, 71, 52, 0.05));
            color: var(--dark-color);
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .empty-category {
            background: linear-gradient(135deg, rgba(108, 117, 125, 0.1), rgba(108, 117, 125, 0.05));
            color: #6c757d;
        }
        
        .category-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        .btn-edit:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
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
            background: linear-gradient(135deg, var(--dark-color), #1e5b48);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(20, 71, 52, 0.2);
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
            box-shadow: 0 15px 40px rgba(20, 71, 52, 0.3);
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
        
        /* زر إضافة قسم */
        .btn-add-category {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(20, 71, 52, 0.4);
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
        
        .btn-add-category:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 15px 40px rgba(20, 71, 52, 0.6);
        }
        
        /* حالة عدم وجود أقسام */
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
            color: var(--dark-color);
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
            
            .btn-add-category {
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
            
            .category-card {
                padding: 20px;
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .category-name {
                font-size: 1.3rem;
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
            
            .category-name {
                font-size: 1.2rem;
            }
            
            .category-actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
            
            .btn-add-category {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
                left: 15px;
                bottom: 15px;
            }
        }
        
        /* زر القائمة المتنقلة */
        .mobile-menu-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            z-index: 1001;
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
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
            
            .category-card {
                background: #2c3e50;
                border-color: #34495e;
            }
            
            .category-name {
                color: #ecf0f1;
            }
            
            .product-count {
                background: rgba(255,255,255,0.1);
                color: #ecf0f1;
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
        
        /* تأثيرات إضافية */
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
                <a class="nav-link active" href="category.php">
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
                        <p class="mb-0">تم إضافة القسم بنجاح.</p>
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
                        <p class="mb-0">تم تحديث القسم بنجاح.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- رسائل الخطأ -->
        <?php if (isset($error_message)): ?>
            <div class="alert-message alert-error">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="mb-1">حدث خطأ!</h5>
                        <p class="mb-0"><?php echo $error_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-2" style="color: var(--dark-color); font-weight: 800;">
                    <i class="bi bi-tags text-primary me-3"></i>
                    إدارة الأقسام
                </h1>
                <p class="text-muted mb-0">إدارة وإضافة وتعديل أقسام المتجر</p>
            </div>
            <button type="button" class="btn btn-primary d-none d-md-flex" onclick="openAddCategoryModal()">
                <i class="bi bi-plus-circle me-2"></i>
                إضافة قسم جديد
            </button>
        </div>

        <!-- إحصائيات -->
        <div class="row stagger-animation">
            <?php
            $total_products = array_sum(array_column($categories, 'product_count'));
            $average_products = count($categories) > 0 ? round($total_products / count($categories), 1) : 0;
            ?>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element">
                    <div class="stats-number"><?php echo count($categories); ?></div>
                    <div class="stats-label">الأقسام</div>
                    <i class="bi bi-tags display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                    <div class="stats-number"><?php echo $total_products; ?></div>
                    <div class="stats-label">إجمالي المنتجات</div>
                    <i class="bi bi-box-seam display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--secondary-color), #e6b800);">
                    <div class="stats-number"><?php echo $average_products; ?></div>
                    <div class="stats-label">متوسط المنتجات</div>
                    <i class="bi bi-bar-chart display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--success-color), #1e7e34);">
                    <?php
                    $empty_categories = 0;
                    foreach ($categories as $category) {
                        if ($category['product_count'] == 0) {
                            $empty_categories++;
                        }
                    }
                    ?>
                    <div class="stats-number"><?php echo count($categories) - $empty_categories; ?></div>
                    <div class="stats-label">أقسام نشطة</div>
                    <i class="bi bi-check-circle display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
        </div>

        <!-- قائمة الأقسام -->
        <?php if (empty($categories)): ?>
            <div class="empty-state fade-in">
                <i class="bi bi-tags empty-state-icon"></i>
                <h2 class="empty-state-title">لا توجد أقسام</h2>
                <p class="empty-state-text">ابدأ بإضافة أول قسم إلى متجرك لتنظيم منتجاتك.</p>
                <button type="button" class="btn btn-primary btn-lg" onclick="openAddCategoryModal()">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة أول قسم
                </button>
            </div>
        <?php else: ?>
            <div class="row stagger-animation">
                <?php foreach ($categories as $category): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            
                            <h3 class="category-name"><?php echo htmlspecialchars($category['category_name']); ?></h3>
                            
                            <div class="category-stats">
                                <span class="product-count <?php echo $category['product_count'] == 0 ? 'empty-category' : ''; ?>">
                                    <i class="bi bi-box-seam me-1"></i>
                                    <?php echo $category['product_count']; ?> منتج
                                </span>
                            </div>
                            
                            <div class="category-actions">
                                <button type="button" class="btn-action btn-edit" onclick="editCategory(<?php echo $category['id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                    تعديل
                                </button>
                                <button type="button" class="btn-action btn-delete" onclick="deleteCategory(<?php echo $category['id']; ?>)">
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

    <!-- زر إضافة قسم عائم -->
    <button type="button" class="btn-add-category" onclick="openAddCategoryModal()">
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

        // حذف القسم
        function deleteCategory(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذا القسم!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=delete&delete_id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // إزالة القسم مع تأثير
                            const categoryCard = document.querySelector(`[onclick="deleteCategory(${id})"]`).closest('.category-card');
                            categoryCard.style.opacity = '0';
                            categoryCard.style.transform = 'translateY(-20px) scale(0.95)';
                            
                            setTimeout(() => {
                                categoryCard.remove();
                                
                                // التحقق إذا لم تعد هناك أقسام
                                if (document.querySelectorAll('.category-card').length === 0) {
                                    location.reload();
                                }
                                
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف القسم بنجاح.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }, 300);
                        } else {
                            Swal.fire('خطأ!', data.message || 'فشل في حذف القسم.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                    });
                }
            });
        }

        // تعديل القسم
        function editCategory(id) {
            window.location.href = '?edit_id=' + id;
        }

        // فتح مودال إضافة قسم جديد
        function openAddCategoryModal() {
            // تنظيف أي edit_id في الرابط
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit_id')) {
                urlParams.delete('edit_id');
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }
            
            // فتح المودال
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        }

        // تأثيرات عند التمرير
        window.addEventListener('scroll', function() {
            const categoryCards = document.querySelectorAll('.category-card');
            const windowHeight = window.innerHeight;
            
            categoryCards.forEach(card => {
                const cardPosition = card.getBoundingClientRect().top;
                
                if (cardPosition < windowHeight - 100) {
                    card.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });

        // تهيئة تأثيرات عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات للبطاقات
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach((card, index) => {
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
            
            // تحسينات للوضع الداكن
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.querySelectorAll('.category-card').forEach(card => {
                    card.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
                });
            }
            
            // تحديث الإحصائيات مع تأثيرات
            updateStats();
        });

        // تحديث الإحصائيات مع تأثيرات
        function updateStats() {
            const statsNumbers = document.querySelectorAll('.stats-number');
            statsNumbers.forEach(stat => {
                const finalValue = parseFloat(stat.textContent);
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    stat.textContent = currentValue.toFixed(currentValue % 1 === 0 ? 0 : 1);
                }, 20);
            });
        }
    </script>

    <!-- مودال إضافة/تعديل القسم -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">
                        <i class="bi <?php echo $edit_category ? 'bi-pencil' : 'bi-plus-circle'; ?> me-2"></i>
                        <?php echo $edit_category ? 'تعديل القسم' : 'إضافة قسم جديد'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">اسم القسم *</label>
                            <input type="text" class="form-control" name="category_name" 
                                id="categoryName"
                                value="<?php echo $edit_category ? htmlspecialchars($edit_category['category_name']) : ''; ?>" 
                                maxlength="255" required
                                placeholder="أدخل اسم القسم">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_category ? 'تحديث القسم' : 'إضافة القسم'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // التحكم في مودال القسم
        document.addEventListener('DOMContentLoaded', function() {
            const categoryModal = document.getElementById('categoryModal');
            const modalTitle = document.getElementById('categoryModalLabel');
            const modalForm = document.getElementById('categoryForm');
            
            // إذا كان هناك edit_id في الرابط
            const urlParams = new URLSearchParams(window.location.search);
            const editId = urlParams.get('edit_id');
            
            if (editId) {
                // فتح المودال للتعديل
                const modal = new bootstrap.Modal(categoryModal);
                modal.show();
            }
            
            // عند إغلاق المودال، تنظيف الرابط
            categoryModal.addEventListener('hidden.bs.modal', function() {
                // إزالة edit_id من الرابط إذا كان موجوداً
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('edit_id')) {
                    urlParams.delete('edit_id');
                    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                }
                
                // تنظيف الحقول
                setTimeout(() => {
                    document.getElementById('categoryName').value = '';
                    
                    // إعادة العنوان للإضافة
                    modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>إضافة قسم جديد';
                    
                    // تغيير إجراء النموذج
                    const actionInput = modalForm.querySelector('input[name="action"]');
                    if (actionInput) actionInput.value = 'add';
                    
                    // إزالة حقل id إذا كان موجوداً
                    const idInput = modalForm.querySelector('input[name="id"]');
                    if (idInput) idInput.remove();
                }, 300);
            });
        });
    </script>
</body>
</html>