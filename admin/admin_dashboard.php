<?php
// بدء الجلسة والتحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config.php';
$query = new Database();

// جلب الإحصائيات من قاعدة البيانات
$products_count = $query->executeQuery("SELECT COUNT(*) as count FROM products")[0]['count'] ?? 0;
$categories_count = $query->executeQuery("SELECT COUNT(*) as count FROM category")[0]['count'] ?? 0;
$messages_count = $query->executeQuery("SELECT COUNT(*) as count FROM messages WHERE status = 'no_checked'")[0]['count'] ?? 0;
$banners_count = $query->executeQuery("SELECT COUNT(*) as count FROM banners")[0]['count'] ?? 0;
$statistics_count = $query->executeQuery("SELECT COUNT(*) as count FROM statistics")[0]['count'] ?? 0;
$features_count = $query->executeQuery("SELECT COUNT(*) as count FROM features")[0]['count'] ?? 0;

// جلب آخر الرسائل
$recent_messages = $query->select('messages', '*', 'ORDER BY created_at DESC LIMIT 5');

// جلب آخر المنتجات
$recent_products = $query->executeQuery("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN category c ON p.category_id = c.id 
    ORDER BY p.id DESC 
    LIMIT 5
");

// جلب الإحصائيات الشهرية للمنتجات (نموذج بسيط)
$monthly_stats = [
    ['month' => 'يناير', 'products' => 15, 'messages' => 8],
    ['month' => 'فبراير', 'products' => 18, 'messages' => 12],
    ['month' => 'مارس', 'products' => 22, 'messages' => 15],
    ['month' => 'أبريل', 'products' => 25, 'messages' => 18],
    ['month' => 'مايو', 'products' => 30, 'messages' => 20],
    ['month' => 'يونيو', 'products' => 35, 'messages' => 22],
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - ركن الأماسي</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #e76a04;
            --primary-dark: #d45f00;
            --secondary-color: rgb(243, 212, 23);
            --dark-color: #144734ff;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding-top: 20px;
        }
        
        .sidebar {
            background-color: var(--dark-color);
            color: white;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link i {
            margin-left: 10px;
        }
        
        .main-content {
            margin-right: 250px;
            padding: 20px;
        }
        
        /* بطاقات الإحصائيات */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            border-top: 4px solid;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
        }
        
        .stat-card.products {
            border-top-color: var(--primary-color);
        }
        
        .stat-card.products::before {
            background: var(--primary-color);
        }
        
        .stat-card.categories {
            border-top-color: #28a745;
        }
        
        .stat-card.categories::before {
            background: #28a745;
        }
        
        .stat-card.messages {
            border-top-color: #17a2b8;
        }
        
        .stat-card.messages::before {
            background: #17a2b8;
        }
        
        .stat-card.banners {
            border-top-color: #ffc107;
        }
        
        .stat-card.banners::before {
            background: #ffc107;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 15px;
        }
        
        .stat-icon.products-icon {
            background: linear-gradient(135deg, var(--primary-color), #ff8c42);
        }
        
        .stat-icon.categories-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .stat-icon.messages-icon {
            background: linear-gradient(135deg, #17a2b8, #5bc0de);
        }
        
        .stat-icon.banners-icon {
            background: linear-gradient(135deg, #ffc107, #ffd65e);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--dark-color);
            line-height: 1;
        }
        
        .stat-title {
            font-weight: 600;
            color: #666;
            margin-top: 10px;
        }
        
        .stat-trend {
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .stat-trend.up {
            color: #28a745;
        }
        
        .stat-trend.down {
            color: #dc3545;
        }
        
        /* بطاقات المحتوى */
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .content-card .card-header {
            background: none;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .content-card .card-title {
            color: var(--dark-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .content-card .card-title i {
            margin-left: 10px;
            color: var(--primary-color);
        }
        
        /* جدول الرسائل */
        .message-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
            transition: background-color 0.3s;
        }
        
        .message-item:hover {
            background-color: #f9f9f9;
        }
        
        .message-item:last-child {
            border-bottom: none;
        }
        
        .message-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .message-email {
            color: #666;
            font-size: 0.9rem;
        }
        
        .message-subject {
            color: #444;
            margin-top: 5px;
        }
        
        .message-date {
            color: #999;
            font-size: 0.8rem;
        }
        
        .message-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .message-status.new {
            background-color: #ffeaa7;
            color: #e17055;
        }
        
        .message-status.read {
            background-color: #d1f7c4;
            color: #2ecc71;
        }
        
        /* جدول المنتجات */
        .product-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .product-category {
            color: #666;
            font-size: 0.9rem;
            background-color: #f8f9fa;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .product-price {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        /* أزرار سريعة */
        .quick-action-btn {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--dark-color);
            display: block;
            margin-bottom: 15px;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.1);
            color: var(--dark-color);
            text-decoration: none;
        }
        
        .quick-action-btn i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: block;
        }
        
        .quick-action-btn span {
            font-weight: 600;
            display: block;
        }
        
        /* مخطط الإحصائيات */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
                margin-bottom: 20px;
            }
            
            .main-content {
                margin-right: 0;
            }
        }
        
        /* رأس الصفحة */
        .page-header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-right: 4px solid var(--primary-color);
        }
        
        .welcome-text {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .welcome-subtext {
            color: #666;
            margin-top: 5px;
        }
        
        .admin-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        /* الـ Footer */
        .dashboard-footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9rem;
            border-top: 1px solid #f0f0f0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- الشريط الجانبي -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-sticky">
                    <div class="text-center mb-4">
                        <div class="admin-avatar mx-auto mb-2">
                            <span>أد</span>
                        </div>
                        <h5 class="mb-1">مسؤول النظام</h5>
                        <small class="text-muted">ركن الأماسي</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_dashboard.php">
                                <i class="bi bi-speedometer"></i> لوحة التحكم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_products.php">
                                <i class="bi bi-box-seam"></i> إدارة المنتجات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_categories.php">
                                <i class="bi bi-tags"></i> إدارة الأقسام
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_banners.php">
                                <i class="bi bi-images"></i> إدارة البنرات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_statistics.php">
                                <i class="bi bi-graph-up"></i> إدارة الإحصائيات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_features.php">
                                <i class="bi bi-stars"></i> إدارة الميزات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_about.php">
                                <i class="bi bi-info-circle"></i> إدارة من نحن
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_messages.php">
                                <i class="bi bi-envelope"></i> الرسائل
                                <?php if ($messages_count > 0): ?>
                                    <span class="badge bg-danger" style="font-size: 0.6rem; padding: 2px 5px;">
                                        <?php echo $messages_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-left"></i> تسجيل الخروج
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- المحتوى الرئيسي -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- رأس الصفحة -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="welcome-text">
                                <i class="bi bi-shield-check text-primary"></i>
                                مرحباً بك في لوحة تحكم ركن الأماسي
                            </div>
                            <div class="welcome-subtext">
                                إدارة موقعك بسهولة ومراقبة أداء المتجر
                            </div>
                        </div>
                        <div class="col-md-4 text-start">
                            <div class="d-flex justify-content-end">
                                <span class="badge bg-light text-dark p-2">
                                    <i class="bi bi-calendar-check"></i>
                                    <?php echo date('Y-m-d'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقات الإحصائيات السريعة -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card products">
                            <div class="stat-icon products-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="stat-number"><?php echo $products_count; ?></div>
                            <div class="stat-title">المنتجات</div>
                            <div class="stat-trend up">
                                <i class="bi bi-arrow-up"></i> 12% عن الشهر الماضي
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card categories">
                            <div class="stat-icon categories-icon">
                                <i class="bi bi-tags"></i>
                            </div>
                            <div class="stat-number"><?php echo $categories_count; ?></div>
                            <div class="stat-title">الأقسام</div>
                            <div class="stat-trend up">
                                <i class="bi bi-arrow-up"></i> 5% عن الشهر الماضي
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card messages">
                            <div class="stat-icon messages-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="stat-number"><?php echo $messages_count; ?></div>
                            <div class="stat-title">رسائل جديدة</div>
                            <div class="stat-trend up">
                                <i class="bi bi-arrow-up"></i> 8% عن الشهر الماضي
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card banners">
                            <div class="stat-icon banners-icon">
                                <i class="bi bi-images"></i>
                            </div>
                            <div class="stat-number"><?php echo $banners_count; ?></div>
                            <div class="stat-title">البنرات</div>
                            <div class="stat-trend up">
                                <i class="bi bi-arrow-up"></i> 3% عن الشهر الماضي
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الصف الثاني: الرسائل والمنتجات -->
                <div class="row mb-4">
                    <!-- الرسائل الأخيرة -->
                    <div class="col-xl-6 col-lg-12 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0">
                                <h5 class="card-title">
                                    <i class="bi bi-envelope"></i>
                                    آخر الرسائل
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($recent_messages)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-envelope-open display-4 text-muted"></i>
                                        <p class="text-muted mt-3">لا توجد رسائل جديدة</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recent_messages as $message): ?>
                                        <div class="message-item">
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                    <div class="message-name">
                                                        <?php echo htmlspecialchars($message['name']); ?>
                                                    </div>
                                                    <div class="message-email">
                                                        <?php echo htmlspecialchars($message['email']); ?>
                                                    </div>
                                                    <div class="message-subject">
                                                        <?php echo htmlspecialchars($message['subject']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-start">
                                                    <div class="message-date">
                                                        <?php echo date('Y-m-d', strtotime($message['created_at'])); ?>
                                                    </div>
                                                    <div class="message-status <?php echo ($message['status'] == 'no_checked') ? 'new' : 'read'; ?>">
                                                        <?php echo ($message['status'] == 'no_checked') ? 'جديد' : 'مقروء'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-3">
                                <a href="admin_messages.php" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> عرض جميع الرسائل
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- المنتجات الأخيرة -->
                    <div class="col-xl-6 col-lg-12 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0">
                                <h5 class="card-title">
                                    <i class="bi bi-box-seam"></i>
                                    آخر المنتجات
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($recent_products)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-box display-4 text-muted"></i>
                                        <p class="text-muted mt-3">لا توجد منتجات</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recent_products as $product): ?>
                                        <div class="product-item">
                                            <div class="row align-items-center">
                                                <div class="col-7">
                                                    <div class="product-name">
                                                        <?php echo htmlspecialchars($product['product_name']); ?>
                                                    </div>
                                                    <?php if (!empty($product['category_name'])): ?>
                                                        <div class="product-category mt-2">
                                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-5 text-start">
                                                    <div class="product-price">
                                                        <?php echo htmlspecialchars($product['price']); ?> ريال
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        ID: <?php echo $product['id']; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-3">
                                <a href="admin_products.php" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> عرض جميع المنتجات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الصف الثالث: مخطط الإحصائيات والإجراءات السريعة -->
                <div class="row">
                    <!-- مخطط الإحصائيات -->
                    <div class="col-xl-8 col-lg-12 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0 mb-3">
                                <h5 class="card-title">
                                    <i class="bi bi-bar-chart"></i>
                                    الإحصائيات الشهرية
                                </h5>
                            </div>
                            <div class="chart-container">
                                <canvas id="monthlyStatsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- الإجراءات السريعة -->
                    <div class="col-xl-4 col-lg-12 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0 mb-3">
                                <h5 class="card-title">
                                    <i class="bi bi-lightning"></i>
                                    إجراءات سريعة
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <a href="admin_products.php?action=add" class="quick-action-btn">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>إضافة منتج</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="admin_categories.php?action=add" class="quick-action-btn">
                                        <i class="bi bi-folder-plus"></i>
                                        <span>إضافة قسم</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="admin_banners.php?action=add" class="quick-action-btn">
                                        <i class="bi bi-image"></i>
                                        <span>إضافة بانر</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="admin_statistics.php?action=add" class="quick-action-btn">
                                        <i class="bi bi-graph-up-arrow"></i>
                                        <span>إضافة إحصائية</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="admin_features.php?action=add" class="quick-action-btn">
                                        <i class="bi bi-star"></i>
                                        <span>إضافة ميزة</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="admin_messages.php" class="quick-action-btn">
                                        <i class="bi bi-chat-dots"></i>
                                        <span>الرد على الرسائل</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات إضافية -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0 mb-3">
                                <h5 class="card-title">
                                    <i class="bi bi-info-circle"></i>
                                    معلومات النظام
                                </h5>
                            </div>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="stat-number"><?php echo $statistics_count; ?></div>
                                    <div class="stat-title">إحصائية</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stat-number"><?php echo $features_count; ?></div>
                                    <div class="stat-title">ميزة</div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-number">2</div>
                                    <div class="stat-title">مستخدم</div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-number">6</div>
                                    <div class="stat-title">أقسام</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="content-card">
                            <div class="card-header border-0 p-0 mb-3">
                                <h5 class="card-title">
                                    <i class="bi bi-calendar-check"></i>
                                    نشاط اليوم
                                </h5>
                            </div>
                            <div class="activity-list">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="activity-icon bg-primary text-white rounded-circle p-2 me-3">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">تسجيل الدخول الناجح</div>
                                        <small class="text-muted">قبل 5 دقائق</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="activity-icon bg-success text-white rounded-circle p-2 me-3">
                                        <i class="bi bi-box"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">إضافة منتج جديد</div>
                                        <small class="text-muted">قبل 2 ساعة</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="activity-icon bg-info text-white rounded-circle p-2 me-3">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">رسالة جديدة من عميل</div>
                                        <small class="text-muted">قبل 3 ساعات</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="dashboard-footer">
                    <p class="mb-0">
                        <i class="bi bi-c-circle"></i> <?php echo date('Y'); ?> جميع الحقوق محفوظة لـ ركن الأماسي
                        | 
                        <span class="text-primary">إصدار النظام: 2.1.0</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // مخطط الإحصائيات الشهرية
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyStatsChart').getContext('2d');
            
            // بيانات المخطط
            const monthlyStats = <?php echo json_encode($monthly_stats); ?>;
            const months = monthlyStats.map(stat => stat.month);
            const productsData = monthlyStats.map(stat => stat.products);
            const messagesData = monthlyStats.map(stat => stat.messages);
            
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'المنتجات',
                            data: productsData,
                            borderColor: '#e76a04',
                            backgroundColor: 'rgba(231, 106, 4, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'الرسائل',
                            data: messagesData,
                            borderColor: '#144734',
                            backgroundColor: 'rgba(20, 71, 52, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: {
                                font: {
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            rtl: true,
                            titleFont: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            bodyFont: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
            
            // تحديث الوقت في رأس الصفحة
            function updateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };
                const timeString = now.toLocaleTimeString('ar-SA', options);
                
                // يمكنك استخدام timeString في مكان ما إذا أردت
            }
            
            updateTime();
            setInterval(updateTime, 60000); // تحديث كل دقيقة
            
            // تأثيرات للبطاقات عند التحميل
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>