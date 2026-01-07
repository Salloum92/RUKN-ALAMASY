<?php
include 'check.php';

// جلب المنتجات
$products = $query->select('products', '*');
$categories = $query->eQuery('SELECT * FROM category');

// إذا كان هناك edit_id، عرض مودال التعديل
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_product = null;

if ($edit_id > 0) {
    $edit_product = $query->select('products', '*', "WHERE id = $edit_id")[0] ?? null;
    
    // إذا لم يتم العثور على المنتج، توجيه للصفحة الرئيسية
    if (!$edit_product) {
        header("Location: products.php");
        exit();
    }
}

// معالجة حذف المنتج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        
        try {
            // حذف الصور من قاعدة البيانات ونظام الملفات
            $imagesUrl = $query->select('product_images', '*', "WHERE product_id = $delete_id");
            foreach ($imagesUrl as $image) {
                $imageUrl = "../assets/img/product/" . $image['image_url'];
                if (file_exists($imageUrl) && is_file($imageUrl)) {
                    if (!unlink($imageUrl)) {
                        error_log("فشل في حذف الصورة: " . $imageUrl);
                    }
                }
            }
            
            // حذف سجلات الصور من قاعدة البيانات
            $query->eQuery('DELETE FROM product_images WHERE product_id = ?', [$delete_id]);
            
            // حذف المنتج
            $deleteResult = $query->eQuery('DELETE FROM products WHERE id = ?', [$delete_id]);
            
            if ($deleteResult) {
                echo 'success';
            } else {
                echo 'error: delete failed';
            }
            
        } catch (Exception $e) {
            echo 'error: ' . $e->getMessage();
        }
        exit;
    }
    
    // معالجة تحديث المنتج
    if ($_POST['action'] === 'update' && isset($_POST['edit_id'])) {
        $edit_id = intval($_POST['edit_id']);
        $product_name = trim($_POST['product_name']);
        $price = floatval($_POST['price']);
        $description = trim($_POST['description']);
        $category_id = intval($_POST['category_id']);

        try {
            // تحديث بيانات المنتج
            $query->eQuery('UPDATE products SET product_name = ?, price = ?, description = ?, category_id = ? WHERE id = ?', 
                          [$product_name, $price, $description, $category_id, $edit_id]);

            // التعامل مع تحديث الصور إذا تم رفع صور جديدة
            if (!empty($_FILES['image']['name'][0])) {
                // حذف الصور القديمة
                $oldImages = $query->select('product_images', '*', "WHERE product_id = $edit_id");
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = "../assets/img/product/" . $oldImage['image_url'];
                    if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // حذف سجلات الصور القديمة
                $query->eQuery('DELETE FROM product_images WHERE product_id = ?', [$edit_id]);

                // رفع الصور الجديدة
                $uploadedImages = [];
                $totalFiles = count($_FILES['image']['name']);
                
                if ($totalFiles <= 10) {
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['image']['error'][$i] == 0) {
                            $image_name = basename($_FILES['image']['name'][$i]);
                            $encrypted_name = md5(time() . $image_name . $i) . "." . pathinfo($image_name, PATHINFO_EXTENSION);
                            $target_dir = "../assets/img/product/";
                            $target_file = $target_dir . $encrypted_name;

                            if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $target_file)) {
                                $uploadedImages[] = $encrypted_name;
                            }
                        }
                    }

                    // إضافة سجلات الصور الجديدة
                    foreach ($uploadedImages as $uploadedImage) {
                        $query->eQuery('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)', [$edit_id, $uploadedImage]);
                    }
                }
            }

            header("Location: " . $_SERVER['PHP_SELF'] . "?updated=true");
            exit;
            
        } catch (Exception $e) {
            echo "خطأ في تحديث المنتج: " . $e->getMessage();
        }
    }
}

// معالجة إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);

    try {
        // عملية رفع الصور
        $uploadedImages = [];
        $totalFiles = count($_FILES['image']['name']);
        
        if ($totalFiles <= 10 && $totalFiles > 0) {
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['image']['error'][$i] == 0) {
                    $image_name = basename($_FILES['image']['name'][$i]);
                    $encrypted_name = md5(time() . $image_name . $i) . "." . pathinfo($image_name, PATHINFO_EXTENSION);
                    $target_dir = "../assets/img/product/";
                    $target_file = $target_dir . $encrypted_name;

                    if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $target_file)) {
                        $uploadedImages[] = $encrypted_name;
                    }
                }
            }

            if (!empty($uploadedImages)) {
                // إضافة المنتج
                $query->eQuery('INSERT INTO products (product_name, description, price, category_id) VALUES (?, ?, ?, ?)', 
                              [$product_name, $description, $price, $category_id]);

                $product_id = $query->lastInsertId();

                // إضافة الصور
                foreach ($uploadedImages as $uploadedImage) {
                    $query->eQuery('INSERT INTO product_images (product_id, image_url) VALUES (?, ?)', [$product_id, $uploadedImage]);
                }

                header("Location: " . $_SERVER['PHP_SELF'] . "?added=true");
                exit;
            } else {
                echo "خطأ: لم يتم رفع أي صور بنجاح.";
            }
        } else {
            echo "الرجاء رفع صورة واحدة على الأقل ولا تزيد عن 10 صور.";
        }
    } catch (Exception $e) {
        echo "خطأ في إضافة المنتج: " . $e->getMessage();
    }
}

// جلب بيانات المنتج للتعديل
$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_product = $query->select('products', '*', "WHERE id = $edit_id")[0] ?? null;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>إدارة المنتجات - ركن الأماسي</title>
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
        
        /* بطاقة المنتج */
        .product-card {
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
        
        .product-card::before {
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
        
        .product-card:hover::before {
            transform: scaleX(1);
        }
        
        .product-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(231, 106, 4, 0.15);
        }
        
        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 6px 15px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 2;
        }
        
        .product-title {
            color: var(--dark-color);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .product-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 15px;
        }
        
        .product-price::before {
            content: 'ريال ';
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }
        
        .product-actions {
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
        
        /* زر إضافة منتج */
        .btn-add-product {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(231, 106, 4, 0.4);
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
        
        .btn-add-product:hover {
            transform: scale(1.1) rotate(180deg);
            box-shadow: 0 15px 40px rgba(231, 106, 4, 0.6);
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
            
            .btn-add-product {
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
            
            .product-card {
                padding: 15px;
            }
            
            .product-image-container {
                height: 180px;
            }
            
            .product-actions {
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
            
            .product-title {
                font-size: 1.2rem;
            }
            
            .product-price {
                font-size: 1.3rem;
            }
            
            .btn-add-product {
                width: 55px;
                height: 55px;
                font-size: 1.6rem;
                left: 15px;
                bottom: 15px;
            }
        }
        
        @media (max-width: 400px) {
            .product-image-container {
                height: 150px;
            }
            
            .product-badge {
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
        
        /* حالة عدم وجود منتجات */
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
            color: var(--primary-color);
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
            
            .product-card {
                background: #2c3e50;
                border-color: #34495e;
            }
            
            .product-title {
                color: #ecf0f1;
            }
            
            .product-description {
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
                <a class="nav-link active" href="products.php">
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
                        <p class="mb-0">تم إضافة المنتج بنجاح.</p>
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
                        <p class="mb-0">تم تحديث المنتج بنجاح.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-2" style="color: var(--dark-color); font-weight: 800;">
                    <i class="bi bi-box-seam text-primary me-3"></i>
                    إدارة المنتجات
                </h1>
                <p class="text-muted mb-0">إدارة وإضافة وتعديل منتجات المتجر</p>
            </div>
            <button type="button" class="btn btn-primary d-none d-md-flex" onclick="openAddProductModal()">
    <i class="bi bi-plus-circle me-2"></i>
    إضافة منتج جديد
</button>
        </div>

        <!-- إحصائيات -->
        <div class="row stagger-animation">
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element">
                    <div class="stats-number"><?php echo count($products); ?></div>
                    <div class="stats-label">المنتجات</div>
                    <i class="bi bi-box-seam display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                    <div class="stats-number"><?php echo count($categories); ?></div>
                    <div class="stats-label">الأقسام</div>
                    <i class="bi bi-tags display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--secondary-color), #e6b800);">
                    <div class="stats-number">
                        <?php 
                        if (count($products) > 0) {
                            $total = array_sum(array_column($products, 'price'));
                            echo number_format($total / count($products), 0);
                        } else {
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class="stats-label">متوسط السعر</div>
                    <i class="bi bi-currency-exchange display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="stats-card floating-element" style="background: linear-gradient(135deg, var(--success-color), #1e7e34);">
                    <div class="stats-number"><?php echo count($products); ?></div>
                    <div class="stats-label">نشطة</div>
                    <i class="bi bi-check-circle display-4 opacity-25 position-absolute" style="bottom: 10px; left: 20px;"></i>
                </div>
            </div>
        </div>

        <!-- قائمة المنتجات -->
        <?php if (empty($products)): ?>
            <div class="empty-state fade-in">
                <i class="bi bi-box empty-state-icon"></i>
                <h2 class="empty-state-title">لا توجد منتجات</h2>
                <p class="empty-state-text">ابدأ بإضافة أول منتج إلى متجرك لتظهر هنا.</p>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    إضافة أول منتج
                </button>
            </div>
        <?php else: ?>
            <div class="row stagger-animation">
                <?php foreach ($products as $i => $product): ?>
                    <?php
                    $productid = $product['id'];
                    $product_images = $query->select('product_images', '*', "WHERE product_id = $productid");
                    $first_image = $product_images[0]['image_url'] ?? 'default-product.jpg';
                    $product_image = "../assets/img/product/" . $first_image;
                    $category = $query->select('category', '*', "WHERE id = " . $product['category_id'])[0] ?? null;
                    ?>
                    
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="product-card">
                            <div class="product-badge">
                                <?php echo $category ? htmlspecialchars($category['category_name']) : 'غير مصنف'; ?>
                            </div>
                            
                            <div class="product-image-container">
                                <img src="<?= $product_image ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     class="product-image"
                                     onerror="this.src='../assets/img/default-product.jpg'">
                            </div>
                            
                            <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            
                            <p class="product-description">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                            
                            <div class="product-price"><?php echo number_format($product['price'], 2); ?></div>
                            
                            <div class="product-actions">
                                <button type="button" class="btn-action btn-edit" onclick="editProduct(<?php echo $productid; ?>)">
                                    <i class="bi bi-pencil"></i>
                                    تعديل
                                </button>
                                <button type="button" class="btn-action btn-delete" onclick="deleteProduct(<?php echo $productid; ?>)">
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

    <!-- زر إضافة منتج عائم -->
    <button type="button" class="btn-add-product" onclick="openAddProductModal()">
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

        // حذف المنتج
        function deleteProduct(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذا المنتج!",
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
                    .then(response => response.text())
                    .then(result => {
                        if (result === 'success') {
                            // إزالة المنتج مع تأثير
                            const productCard = document.querySelector(`[onclick="deleteProduct(${id})"]`).closest('.product-card');
                            productCard.style.opacity = '0';
                            productCard.style.transform = 'translateY(-20px) scale(0.95)';
                            
                            setTimeout(() => {
                                productCard.remove();
                                
                                // التحقق إذا لم تعد هناك منتجات
                                if (document.querySelectorAll('.product-card').length === 0) {
                                    location.reload();
                                }
                                
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف المنتج بنجاح.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }, 300);
                        } else {
                            Swal.fire('خطأ!', 'فشل في حذف المنتج.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                    });
                }
            });
        }

        // تعديل المنتج
        function editProduct(id) {
            window.location.href = '?edit_id=' + id;
        }

        // تأثيرات عند التمرير
        window.addEventListener('scroll', function() {
            const productCards = document.querySelectorAll('.product-card');
            const windowHeight = window.innerHeight;
            
            productCards.forEach(card => {
                const cardPosition = card.getBoundingClientRect().top;
                
                if (cardPosition < windowHeight - 100) {
                    card.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });

        // تهيئة تأثيرات عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات للبطاقات
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach((card, index) => {
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
                document.querySelectorAll('.product-card').forEach(card => {
                    card.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
                });
            }
        });

        // إضافة تحسينات للوحة اللمس
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', e => {
            touchStartY = e.changedTouches[0].screenY;
        });

        document.addEventListener('touchend', e => {
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        });

        function handleSwipe() {
            if (touchStartY - touchEndY > 100) {
                // مسح لأعلى
                document.querySelector('.btn-add-product').style.bottom = '20px';
            }
            
            if (touchEndY - touchStartY > 100) {
                // مسح لأسفل
                document.querySelector('.btn-add-product').style.bottom = '80px';
            }
        }

        // تحسين الأداء للصور
        const images = document.querySelectorAll('.product-image');
        images.forEach(img => {
            img.loading = 'lazy';
        });

        // تحديث الإحصائيات مع تأثيرات
        function updateStats() {
            const statsNumbers = document.querySelectorAll('.stats-number');
            statsNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
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

        // استدعاء تحديث الإحصائيات
        updateStats();
    </script>

    <!-- مودال إضافة/تعديل المنتج -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">
                    <i class="bi <?php echo $edit_product ? 'bi-pencil' : 'bi-plus-circle'; ?> me-2"></i>
                    <?php echo $edit_product ? 'تعديل المنتج' : 'إضافة منتج جديد'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <input type="hidden" name="action" value="<?php echo $edit_product ? 'update' : 'add'; ?>">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">اسم المنتج *</label>
                            <input type="text" class="form-control" name="product_name"
                                id="productName"
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_name']) : ''; ?>" 
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">السعر (ريال) *</label>
                            <input type="number" class="form-control" name="price"
                                id="productPrice"
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['price']) : ''; ?>" 
                                step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف *</label>
                        <textarea class="form-control" name="description" id="productDescription" 
                                  rows="3" required><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">القسم *</label>
                        <select class="form-control" name="category_id" id="categorySelect" required>
                            <option value="">اختر القسم</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="productImages" class="form-label">صور المنتج *</label>
                        <input type="file" class="form-control" name="image[]" id="productImages"
                            accept="image/*" multiple <?php echo !$edit_product ? 'required' : ''; ?>>
                        <small class="text-muted">
                            يمكنك رفع حتى 10 صور. الصيغ المدعومة: JPG, PNG, GIF
                            <?php if ($edit_product): ?>
                                اتركه فارغاً للحفاظ على الصور الحالية.
                            <?php endif; ?>
                        </small>
                    </div>
                    
                    <?php if ($edit_product): ?>
                        <div class="mb-3">
                            <label class="form-label">الصور الحالية</label>
                            <div class="d-flex flex-wrap gap-2" id="currentImages">
                                <?php 
                                $current_images = $query->select('product_images', '*', "WHERE product_id = " . $edit_product['id']);
                                if (!empty($current_images)): 
                                ?>
                                    <?php foreach ($current_images as $img): ?>
                                        <div class="position-relative">
                                            <img src="../assets/img/product/<?php echo $img['image_url']; ?>" 
                                                 class="img-thumbnail" 
                                                 style="width: 80px; height: 80px; object-fit: cover;"
                                                 onerror="this.src='../assets/img/default-product.jpg'">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 start-0" 
                                                    style="transform: translate(-30%, -30%);"
                                                    onclick="removeCurrentImage(<?php echo $img['id']; ?>, this)">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">لا توجد صور</span>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="keep_images" value="1">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_product ? 'تحديث المنتج' : 'إضافة المنتج'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
// عند فتح المودال، تنظيف الحقول إذا كان إضافة جديدة
document.addEventListener('DOMContentLoaded', function() {
    const productModal = document.getElementById('productModal');
    const modalTitle = document.getElementById('productModalLabel');
    const modalForm = document.getElementById('productForm');
    
    // إذا لم يكن هناك edit_id في الرابط
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit_id');
    
    // إذا لم يكن هناك تعديل، تنظيف المودال عند الفتح
    if (!editId) {
        // عند فتح المودال للإضافة الجديدة
        productModal.addEventListener('show.bs.modal', function() {
            // تغيير العنوان
            const titleIcon = modalTitle.querySelector('i');
            titleIcon.className = 'bi bi-plus-circle me-2';
            modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>إضافة منتج جديد';
            
            // تنظيف الحقول
            document.getElementById('productName').value = '';
            document.getElementById('productPrice').value = '';
            document.getElementById('productDescription').value = '';
            document.getElementById('categorySelect').selectedIndex = 0;
            document.getElementById('productImages').value = '';
            
            // تغيير إجراء النموذج
            const actionInput = modalForm.querySelector('input[name="action"]');
            if (actionInput) actionInput.value = 'add';
            
            // إزالة حقل edit_id إذا كان موجوداً
            const editIdInput = modalForm.querySelector('input[name="edit_id"]');
            if (editIdInput) editIdInput.remove();
            
            // إضافة required لحقل الصور
            const imageInput = document.getElementById('productImages');
            imageInput.required = true;
        });
    } else {
        // عند فتح المودال للتعديل
        productModal.addEventListener('show.bs.modal', function() {
            // تغيير العنوان
            const titleIcon = modalTitle.querySelector('i');
            titleIcon.className = 'bi bi-pencil me-2';
            modalTitle.innerHTML = '<i class="bi bi-pencil me-2"></i>تعديل المنتج';
            
            // تغيير إجراء النموذج
            const actionInput = modalForm.querySelector('input[name="action"]');
            if (actionInput) actionInput.value = 'update';
            
            // إزالة required من حقل الصور للتعديل
            const imageInput = document.getElementById('productImages');
            imageInput.required = false;
        });
    }
    
    // عند إغلاق المودال، تنظيف الرابط
    productModal.addEventListener('hidden.bs.modal', function() {
        // إزالة edit_id من الرابط إذا كان موجوداً
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('edit_id')) {
            urlParams.delete('edit_id');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
        
        // تنظيف المودال تماماً
        setTimeout(() => {
            document.getElementById('productName').value = '';
            document.getElementById('productPrice').value = '';
            document.getElementById('productDescription').value = '';
            document.getElementById('categorySelect').selectedIndex = 0;
            document.getElementById('productImages').value = '';
            
            // إعادة العنوان للإضافة
            modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>إضافة منتج جديد';
            
            // تغيير إجراء النموذج
            const actionInput = modalForm.querySelector('input[name="action"]');
            if (actionInput) actionInput.value = 'add';
            
            // إزالة حقل edit_id إذا كان موجوداً
            const editIdInput = modalForm.querySelector('input[name="edit_id"]');
            if (editIdInput) editIdInput.remove();
            
            // إضافة required لحقل الصور
            const imageInput = document.getElementById('productImages');
            imageInput.required = true;
        }, 300);
    });
    
    // فتح المودال تلقائياً إذا كان هناك edit_id
    if (editId) {
        const modal = new bootstrap.Modal(productModal);
        modal.show();
    }
    
    // التحقق من الصور عند الإرسال
    modalForm.addEventListener('submit', function(e) {
        const isEditMode = this.querySelector('input[name="action"]').value === 'update';
        const imageInput = document.getElementById('productImages');
        
        // إذا كان وضع الإضافة ولم يتم اختيار صور
        if (!isEditMode && imageInput.files.length === 0) {
            e.preventDefault();
            Swal.fire({
                title: 'خطأ',
                text: 'يرجى اختيار صورة واحدة على الأقل للمنتج',
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
            return false;
        }
        
        // التحقق من عدد الصور
        if (imageInput.files.length > 10) {
            e.preventDefault();
            Swal.fire({
                title: 'خطأ',
                text: 'يمكنك رفع حتى 10 صور فقط',
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
            return false;
        }
        
        // التحقق من حجم الصور (5MB كحد أقصى)
        const maxSize = 5 * 1024 * 1024; // 5MB
        for (let file of imageInput.files) {
            if (file.size > maxSize) {
                e.preventDefault();
                Swal.fire({
                    title: 'خطأ',
                    text: `الصورة ${file.name} حجمها كبير جداً. الحد الأقصى 5MB`,
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
                return false;
            }
        }
        
        return true;
    });
    
    // عرض معاينة للصور المختارة
    document.getElementById('productImages').addEventListener('change', function(e) {
        const files = e.target.files;
        const previewContainer = document.getElementById('imagePreviewContainer') || createImagePreviewContainer();
        
        // تنظيف المعاينة السابقة
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            for (let i = 0; i < Math.min(files.length, 10); i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'position-relative';
                    imgContainer.style.cssText = 'width: 80px; height: 80px; margin: 5px;';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 start-0';
                    removeBtn.style.cssText = 'transform: translate(-30%, -30%); padding: 0.15rem 0.3rem;';
                    removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                    removeBtn.onclick = function() {
                        // إزالة الصورة من input files
                        const dt = new DataTransfer();
                        const input = document.getElementById('productImages');
                        
                        for (let j = 0; j < input.files.length; j++) {
                            if (j !== i) {
                                dt.items.add(input.files[j]);
                            }
                        }
                        
                        input.files = dt.files;
                        imgContainer.remove();
                    };
                    
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(removeBtn);
                    previewContainer.appendChild(imgContainer);
                };
                
                reader.readAsDataURL(file);
            }
        }
    });
    
    function createImagePreviewContainer() {
        const container = document.createElement('div');
        container.id = 'imagePreviewContainer';
        container.className = 'd-flex flex-wrap mt-2';
        document.getElementById('productImages').parentNode.appendChild(container);
        return container;
    }
});

// إزالة الصورة الحالية
function removeCurrentImage(imageId, button) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'هل تريد حذف هذه الصورة؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذفها',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إرسال طلب AJAX لحذف الصورة
            fetch('delete_product_image.php?id=' + imageId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // إزالة الصورة من الواجهة
                        button.parentElement.remove();
                        
                        // التحقق إذا لم تبقى صور
                        const imagesContainer = document.getElementById('currentImages');
                        if (imagesContainer.querySelectorAll('img').length === 0) {
                            imagesContainer.innerHTML = '<span class="text-muted">لا توجد صور</span>';
                        }
                        
                        Swal.fire('تم الحذف!', 'تم حذف الصورة بنجاح', 'success');
                    } else {
                        Swal.fire('خطأ!', 'فشل في حذف الصورة', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف', 'error');
                });
        }
    });
}

// فتح المودال للإضافة الجديدة
function openAddProductModal() {
    // تنظيف أي edit_id في الرابط
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('edit_id')) {
        urlParams.delete('edit_id');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }
    
    // فتح المودال
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}
</script>

</body>
</html>