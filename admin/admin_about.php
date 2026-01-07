<?php
// بدء الجلسة والتحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config.php';
$query = new Database();

// معالجة طلبات POST
$message = '';
$messageType = '';

// معالجة تحديث محتوى "من نحن"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_about'])) {
        $id = intval($_POST['id']);
        $title = $query->validate($_POST['title']);
        $p1 = $query->validate($_POST['p1']);
        $p2 = $query->validate($_POST['p2']);
        
        // معالجة رفع الصورة
        $image = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($file_type, $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = 'about_' . time() . '_' . uniqid() . '.' . $file_ext;
                $target_dir = "../assets/img/about/";
                
                // إنشاء المجلد إذا لم يكن موجوداً
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $target_path = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image = $file_name;
                    
                    // حذف الصورة القديمة إذا كانت موجودة
                    if ($_POST['current_image'] && file_exists($target_dir . $_POST['current_image'])) {
                        unlink($target_dir . $_POST['current_image']);
                    }
                }
            }
        }
        
        $data = [
            'title' => $title,
            'p1' => $p1,
            'p2' => $p2,
            'image' => $image
        ];
        
        $result = $query->update('about', $data, "WHERE id = $id");
        
        if ($result) {
            $message = 'تم تحديث محتوى "من نحن" بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء تحديث المحتوى';
            $messageType = 'danger';
        }
    }
    
    // معالجة إضافة عنصر إلى القائمة
    elseif (isset($_POST['add_list_item'])) {
        $about_id = intval($_POST['about_id']);
        $list_item = $query->validate($_POST['list_item']);
        
        $data = [
            'about_id' => $about_id,
            'list_item' => $list_item
        ];
        
        $result = $query->insert('about_ul_items', $data);
        
        if ($result) {
            $message = 'تمت إضافة العنصر إلى القائمة بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء إضافة العنصر';
            $messageType = 'danger';
        }
    }
    
    // معالجة تحديث عنصر في القائمة
    elseif (isset($_POST['update_list_item'])) {
        $id = intval($_POST['item_id']);
        $list_item = $query->validate($_POST['list_item']);
        
        $data = [
            'list_item' => $list_item
        ];
        
        $result = $query->update('about_ul_items', $data, "WHERE id = $id");
        
        if ($result) {
            $message = 'تم تحديث العنصر بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء تحديث العنصر';
            $messageType = 'danger';
        }
    }
    
    // معالجة حذف عنصر من القائمة
    elseif (isset($_POST['delete_list_item'])) {
        $id = intval($_POST['item_id']);
        $result = $query->delete('about_ul_items', "WHERE id = $id");
        
        if ($result) {
            $message = 'تم حذف العنصر بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء حذف العنصر';
            $messageType = 'danger';
        }
    }
}

// جلب بيانات "من نحن"
$aboutData = $query->select('about');
$aboutItems = [];

if (!empty($aboutData)) {
    $about = $aboutData[0];
    $aboutItems = [
        'id' => $about['id'],
        'title' => $about['title'],
        'p1' => $about['p1'],
        'p2' => $about['p2'],
        'image' => $about['image']
    ];
    
    // جلب عناصر القائمة
    $listItems = $query->select('about_ul_items', '*', "WHERE about_id = {$about['id']} ORDER BY id ASC");
} else {
    // إنشاء سجل افتراضي إذا لم يكن موجوداً
    $defaultData = [
        'title' => 'من نحن',
        'p1' => 'وصف قصير عن الشركة',
        'p2' => 'وصف طويل عن الشركة',
        'image' => ''
    ];
    
    $result = $query->insert('about', $defaultData);
    
    if ($result) {
        $aboutData = $query->select('about');
        $about = $aboutData[0];
        $aboutItems = [
            'id' => $about['id'],
            'title' => $about['title'],
            'p1' => $about['p1'],
            'p2' => $about['p2'],
            'image' => $about['image']
        ];
    }
    
    $listItems = [];
}

// جلب الإحصائيات والميزات للعرض
$statistics = $query->select('statistics');
$features = $query->select('features');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة "من نحن" - لوحة تحكم ركن الأماسي</title>
    
    <!-- مكتبات CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        /* بطاقات المحتوى */
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-right: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .card-header-custom {
            background: none;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .card-title-custom {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            margin: 0;
        }
        
        .card-title-custom i {
            margin-left: 10px;
            color: var(--primary-color);
        }
        
        /* حقول النماذج */
        .form-label-custom {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control-custom,
        .form-textarea-custom {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
            font-family: inherit;
        }
        
        .form-control-custom:focus,
        .form-textarea-custom:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.15);
        }
        
        .form-textarea-custom {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }
        
        /* معاينة الصورة */
        .image-preview-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .image-preview {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
        }
        
        .image-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            margin-bottom: 15px;
            flex-direction: column;
        }
        
        /* قائمة العناصر */
        .list-items-container {
            margin-top: 30px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .list-item-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-right: 3px solid var(--primary-color);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .list-item-card:hover {
            background: #e9ecef;
            transform: translateX(-5px);
        }
        
        .list-item-content {
            flex: 1;
            padding-left: 10px;
        }
        
        .list-item-index {
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .list-item-actions {
            display: flex;
            gap: 8px;
        }
        
        /* الأزرار */
        .btn-custom-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-custom-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
            color: white;
        }
        
        .btn-custom-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-custom-outline:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-custom-danger {
            background: transparent;
            color: #dc3545;
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-custom-danger:hover {
            background: #dc3545;
            color: white;
        }
        
        /* الأدوات الإضافية */
        .info-box {
            background: rgba(20, 71, 52, 0.05);
            border: 1px solid rgba(20, 71, 52, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
        }
        
        .info-box i {
            color: var(--primary-color);
            margin-left: 5px;
        }
        
        /* تصميم متجاوب */
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
                margin-bottom: 20px;
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .content-card {
                padding: 20px;
            }
            
            .list-item-actions {
                flex-direction: column;
                gap: 5px;
            }
        }
        
        /* تأثيرات الرسوم المتحركة */
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* تحسينات إضافية */
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control-file {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .form-control-file:hover {
            border-color: var(--primary-color);
            background: rgba(231, 106, 4, 0.05);
        }
        
        .stats-preview {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stats-preview i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-left: 10px;
        }
        
        .features-preview {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .features-preview i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-left: 10px;
        }
        
        /* ألوان الرسائل */
        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: rgba(25, 135, 84, 0.2);
            color: #0f5132;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        /* شريط التمرير المخصص */
        .list-items-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .list-items-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .list-items-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        .list-items-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <!-- الرسائل -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert" style="margin: 0 250px 20px 20px;">
        <i class="bi <?php echo ($messageType == 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- الشريط الجانبي -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-sticky">
                    <h4 class="text-center mb-4">
                        <i class="bi bi-shield-lock"></i> لوحة التحكم
                    </h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="bi bi-speedometer"></i> لوحة التحكم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
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
                            <a class="nav-link active" href="admin_about.php">
                                <i class="bi bi-info-circle"></i> إدارة من نحن
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_messages.php">
                                <i class="bi bi-envelope"></i> الرسائل
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-info-circle text-primary"></i>
                        إدارة محتوى "من نحن"
                    </h2>
                    <div>
                        <a href="../about.php" target="_blank" class="btn-custom-outline me-2">
                            <i class="bi bi-eye"></i> معاينة الصفحة
                        </a>
                        <button type="button" class="btn-custom-primary" onclick="window.location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> تحديث
                        </button>
                    </div>
                </div>

                <!-- المحتوى الأساسي -->
                <div class="row">
                    <div class="col-lg-8">
                        <!-- نموذج تحديث المحتوى -->
                        <div class="content-card fade-in">
                            <div class="card-header-custom">
                                <h3 class="card-title-custom">
                                    <i class="bi bi-pencil"></i>
                                    المحتوى الأساسي
                                </h3>
                                <p class="text-muted mb-0 mt-2">هذا المحتوى يظهر في صفحة "من نحن" للزوار</p>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="aboutForm">
                                <input type="hidden" name="id" value="<?php echo $aboutItems['id']; ?>">
                                <input type="hidden" name="current_image" id="current_image" value="<?php echo htmlspecialchars($aboutItems['image']); ?>">
                                
                                <!-- العنوان -->
                                <div class="mb-4">
                                    <label for="title" class="form-label-custom required-field">العنوان الرئيسي</label>
                                    <input type="text" 
                                           class="form-control-custom" 
                                           id="title" 
                                           name="title" 
                                           value="<?php echo htmlspecialchars($aboutItems['title']); ?>"
                                           required
                                           maxlength="255"
                                           placeholder="أدخل العنوان الرئيسي للصفحة">
                                    <div class="form-text">هذا هو العنوان الذي يظهر في أعلى صفحة "من نحن"</div>
                                </div>
                                
                                <!-- الفقرة الأولى (وصف قصير) -->
                                <div class="mb-4">
                                    <label for="p1" class="form-label-custom required-field">الوصف القصير</label>
                                    <textarea class="form-textarea-custom" 
                                              id="p1" 
                                              name="p1" 
                                              rows="3"
                                              required
                                              placeholder="اكتب وصفاً قصيراً عن الشركة..."><?php echo htmlspecialchars($aboutItems['p1']); ?></textarea>
                                    <div class="form-text">وصف مختصر يظهر تحت العنوان الرئيسي</div>
                                </div>
                                
                                <!-- الفقرة الثانية (وصف مفصل) -->
                                <div class="mb-4">
                                    <label for="p2" class="form-label-custom required-field">الوصف المفصل</label>
                                    <textarea class="form-textarea-custom" 
                                              id="p2" 
                                              name="p2" 
                                              rows="5"
                                              required
                                              placeholder="اكتب وصفاً مفصلاً عن الشركة وتاريخها وأهدافها..."><?php echo htmlspecialchars($aboutItems['p2']); ?></textarea>
                                    <div class="form-text">وصف مفصل يظهر في القسم الرئيسي من الصفحة</div>
                                </div>
                                
                                <!-- صورة -->
                                <div class="mb-4">
                                    <label for="image" class="form-label-custom">صورة العرض</label>
                                    <div class="form-control-file" onclick="document.getElementById('image').click()">
                                        <i class="bi bi-cloud-upload display-4 text-muted mb-2"></i>
                                        <div class="text-muted">انقر لرفع صورة أو اسحب وأفلت</div>
                                        <small class="text-muted">الصيغ المسموحة: JPG, PNG, GIF, WebP (الحد الأقصى: 5MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none" 
                                           id="image" 
                                           name="image"
                                           accept="image/*">
                                </div>
                                
                                <!-- معاينة الصورة -->
                                <div class="image-preview-container">
                                    <h6 class="mb-3">معاينة الصورة</h6>
                                    <?php if (!empty($aboutItems['image']) && file_exists("../assets/img/about/" . $aboutItems['image'])): ?>
                                        <img src="../assets/img/about/<?php echo htmlspecialchars($aboutItems['image']); ?>" 
                                             alt="معاينة الصورة" 
                                             class="image-preview"
                                             id="imagePreview"
                                             onerror="this.style.display='none'; document.getElementById('imagePreviewPlaceholder').style.display='flex';">
                                        <div class="image-placeholder" id="imagePreviewPlaceholder" style="display: none;">
                                            <i class="bi bi-image display-4 text-muted"></i>
                                            <div class="mt-2">لا توجد صورة</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder" id="imagePreviewPlaceholder">
                                            <i class="bi bi-image display-4 text-muted"></i>
                                            <div class="mt-2">لا توجد صورة</div>
                                        </div>
                                        <img src="" alt="معاينة الصورة" class="image-preview d-none" id="imagePreview">
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <small class="text-muted">حجم الصورة المثالي: 800×600 بكسل</small>
                                    </div>
                                </div>
                                
                                <!-- زر الحفظ -->
                                <div class="text-start mt-4 pt-3 border-top">
                                    <button type="submit" name="update_about" class="btn-custom-primary">
                                        <i class="bi bi-save"></i> حفظ التغييرات
                                    </button>
                                    <button type="reset" class="btn-custom-outline me-2" onclick="resetForm()">
                                        <i class="bi bi-arrow-counterclockwise"></i> إعادة تعيين
                                    </button>
                                    <button type="button" class="btn-custom-outline" onclick="previewContent()">
                                        <i class="bi bi-eye"></i> معاينة المحتوى
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- قائمة "لماذا تختارنا" -->
                        <div class="content-card fade-in" style="animation-delay: 0.1s;">
                            <div class="card-header-custom">
                                <h3 class="card-title-custom">
                                    <i class="bi bi-list-check"></i>
                                    قائمة "لماذا تختارنا"
                                </h3>
                                <p class="text-muted mb-0 mt-2">هذه القائمة تظهر كنقاط مميزة للشركة</p>
                            </div>
                            
                            <!-- إضافة عنصر جديد -->
                            <form method="POST" class="mb-4" id="addListItemForm">
                                <input type="hidden" name="about_id" value="<?php echo $aboutItems['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="new_list_item" class="form-label-custom required-field">إضافة عنصر جديد</label>
                                    <textarea class="form-textarea-custom" 
                                              id="new_list_item" 
                                              name="list_item" 
                                              rows="2"
                                              placeholder="اكتب سبباً جديداً لاختيار الشركة..."
                                              required></textarea>
                                </div>
                                
                                <div class="text-start">
                                    <button type="submit" name="add_list_item" class="btn-custom-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> إضافة إلى القائمة
                                    </button>
                                    <div class="form-text d-inline-block me-3">يجب أن تكون العبارات واضحة ومختصرة</div>
                                </div>
                            </form>
                            
                            <!-- عرض العناصر الحالية -->
                            <div class="list-items-container">
                                <h6 class="mb-3">العناصر الحالية (<?php echo count($listItems); ?>)</h6>
                                
                                <?php if (empty($listItems)): ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-list display-4 mb-3"></i>
                                        <p>لا توجد عناصر في القائمة</p>
                                        <small>أضف عناصر لتعرض نقاط تميز الشركة</small>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($listItems as $index => $item): ?>
                                        <div class="list-item-card" data-id="<?php echo $item['id']; ?>">
                                            <div class="list-item-index"><?php echo $index + 1; ?></div>
                                            <div class="list-item-content">
                                                <?php echo htmlspecialchars($item['list_item']); ?>
                                            </div>
                                            <div class="list-item-actions">
                                                <button type="button" 
                                                        class="btn-custom-outline btn-sm"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editListItemModal"
                                                        onclick="editListItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['list_item']); ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟');">
                                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="delete_list_item" class="btn-custom-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- معاينة الإحصائيات -->
                        <div class="content-card slide-in">
                            <div class="card-header-custom">
                                <h3 class="card-title-custom">
                                    <i class="bi bi-graph-up"></i>
                                    معاينة الإحصائيات
                                </h3>
                                <p class="text-muted mb-0 mt-2">الإحصائيات المعروضة في صفحة "من نحن"</p>
                            </div>
                            
                            <div class="mb-3">
                                <a href="admin_statistics.php" class="btn-custom-outline btn-sm w-100 mb-2">
                                    <i class="bi bi-plus-circle"></i> إدارة الإحصائيات
                                </a>
                            </div>
                            
                            <?php if (empty($statistics)): ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-bar-chart display-4 mb-2"></i>
                                    <p>لا توجد إحصائيات</p>
                                    <small>أضف إحصائيات من صفحة إدارة الإحصائيات</small>
                                </div>
                            <?php else: ?>
                                <?php foreach ($statistics as $stat): ?>
                                    <div class="stats-preview">
                                        <i class="<?php echo htmlspecialchars($stat['icon']); ?>"></i>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($stat['title']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($stat['description']); ?></small>
                                        </div>
                                        <div class="ms-auto fw-bold text-primary">
                                            <?php echo $stat['count']; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- معاينة الميزات -->
                        <div class="content-card slide-in" style="animation-delay: 0.2s;">
                            <div class="card-header-custom">
                                <h3 class="card-title-custom">
                                    <i class="bi bi-stars"></i>
                                    معاينة الميزات
                                </h3>
                                <p class="text-muted mb-0 mt-2">الميزات المعروضة في صفحة "من نحن"</p>
                            </div>
                            
                            <div class="mb-3">
                                <a href="admin_features.php" class="btn-custom-outline btn-sm w-100 mb-2">
                                    <i class="bi bi-plus-circle"></i> إدارة الميزات
                                </a>
                            </div>
                            
                            <?php if (empty($features)): ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-stars display-4 mb-2"></i>
                                    <p>لا توجد ميزات</p>
                                    <small>أضف ميزات من صفحة إدارة الميزات</small>
                                </div>
                            <?php else: ?>
                                <?php foreach ($features as $feature): ?>
                                    <div class="features-preview">
                                        <i class="<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($feature['title']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($feature['description']); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- معلومات المساعدة -->
                        <div class="content-card slide-in" style="animation-delay: 0.3s;">
                            <div class="card-header-custom">
                                <h3 class="card-title-custom">
                                    <i class="bi bi-lightbulb"></i>
                                    نصائح للتحسين
                                </h3>
                            </div>
                            
                            <div class="info-box">
                                <h6 class="fw-bold mb-2"><i class="bi bi-check-circle"></i> نصائح للمحتوى:</h6>
                                <ul class="mb-0 ps-3">
                                    <li class="mb-2">استخدم لغة واضحة وسهلة الفهم</li>
                                    <li class="mb-2">ركز على مزايا الشركة الفريدة</li>
                                    <li class="mb-2">اذكر إنجازات وأرقام حقيقية</li>
                                    <li class="mb-2">استخدم صوراً عالية الجودة</li>
                                    <li>تأكد من صحة المعلومات ودقتها</li>
                                </ul>
                            </div>
                            
                            <div class="info-box mt-3">
                                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle"></i> معلومات سريعة:</h6>
                                <div class="row text-center">
                                    <div class="col-6 mb-2">
                                        <div class="fw-bold text-primary"><?php echo count($listItems); ?></div>
                                        <small class="text-muted">عنصر في القائمة</small>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="fw-bold text-primary"><?php echo count($statistics); ?></div>
                                        <small class="text-muted">إحصائية</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-primary"><?php echo count($features); ?></div>
                                        <small class="text-muted">ميزة</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-primary"><?php echo (!empty($aboutItems['image'])) ? '✓' : '✗'; ?></div>
                                        <small class="text-muted">صورة مرفوعة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تعديل عنصر القائمة -->
    <div class="modal fade" id="editListItemModal" tabindex="-1" aria-labelledby="editListItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editListItemModalLabel">
                        <i class="bi bi-pencil"></i>
                        تعديل العنصر
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editListItemForm">
                    <input type="hidden" name="item_id" id="edit_item_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_list_item" class="form-label-custom required-field">نص العنصر</label>
                            <textarea class="form-textarea-custom" 
                                      id="edit_list_item" 
                                      name="list_item" 
                                      rows="3"
                                      required
                                      placeholder="اكتب نص العنصر..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom-outline" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="update_list_item" class="btn-custom-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مكتبة Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // تهيئة الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // معاينة الصورة قبل الرفع
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // التحقق من حجم الملف
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        if (file.size > maxSize) {
                            alert('حجم الملف كبير جداً. الحد الأقصى 5MB');
                            this.value = '';
                            return;
                        }
                        
                        // التحقق من نوع الملف
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('نوع الملف غير مدعوم. الرجاء اختيار صورة');
                            this.value = '';
                            return;
                        }
                        
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            // إنشاء معاينة جديدة
                            let previewImg = document.getElementById('imagePreview');
                            let placeholder = document.getElementById('imagePreviewPlaceholder');
                            
                            if (previewImg) {
                                previewImg.src = e.target.result;
                                previewImg.style.display = 'block';
                            }
                            
                            if (placeholder) {
                                placeholder.style.display = 'none';
                            }
                        }
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // سحب وإفلات للصورة
            const fileDropZone = document.querySelector('.form-control-file');
            if (fileDropZone) {
                fileDropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.borderColor = 'var(--primary-color)';
                    this.style.backgroundColor = 'rgba(231, 106, 4, 0.1)';
                });
                
                fileDropZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = '';
                });
                
                fileDropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = '';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        const imageInput = document.getElementById('image');
                        if (imageInput) {
                            // إنشاء DataTransfer object لمحاكاة اختيار الملف
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(files[0]);
                            imageInput.files = dataTransfer.files;
                            
                            // تشغيل حدث change
                            const event = new Event('change', { bubbles: true });
                            imageInput.dispatchEvent(event);
                        }
                    }
                });
            }
            
            // التحقق من صحة النماذج
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = this.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.style.borderColor = '#dc3545';
                            field.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.15)';
                        } else {
                            field.style.borderColor = '#e0e0e0';
                            field.style.boxShadow = 'none';
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('الرجاء ملء جميع الحقول المطلوبة');
                    }
                });
            });
            
            // إضافة مؤشر أحرف للنصوص الطويلة
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                const maxLength = textarea.maxLength || 1000;
                const charCount = document.createElement('div');
                charCount.className = 'form-text';
                charCount.style.fontSize = '0.8rem';
                charCount.style.color = '#666';
                charCount.style.marginTop = '5px';
                charCount.style.textAlign = 'left';
                
                textarea.parentNode.insertBefore(charCount, textarea.nextSibling);
                
                function updateCharCount() {
                    const currentLength = textarea.value.length;
                    charCount.textContent = `${currentLength} / ${maxLength}`;
                    
                    if (currentLength > maxLength * 0.9) {
                        charCount.style.color = '#dc3545';
                    } else if (currentLength > maxLength * 0.7) {
                        charCount.style.color = '#ffc107';
                    } else {
                        charCount.style.color = '#666';
                    }
                }
                
                textarea.addEventListener('input', updateCharCount);
                updateCharCount(); // التهيئة الأولية
            });
            
            // إضافة فرز للعناصر (سحب وإفلات)
            initDragAndDrop();
        });
        
        // وظيفة تعديل عنصر القائمة
        function editListItem(id, text) {
            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_list_item').value = text;
            
            // إظهار Modal
            const modal = new bootstrap.Modal(document.getElementById('editListItemModal'));
            modal.show();
            
            // التركيز على حقل النص
            setTimeout(() => {
                document.getElementById('edit_list_item').focus();
            }, 500);
        }
        
        // وظيفة إعادة تعيين النموذج
        function resetForm() {
            if (confirm('هل تريد إعادة تعيين جميع الحقول؟ سيتم فقدان التغييرات غير المحفوظة.')) {
                const form = document.getElementById('aboutForm');
                if (form) {
                    form.reset();
                    
                    // إعادة تعيين معاينة الصورة
                    const currentImage = document.getElementById('current_image').value;
                    
                    if (currentImage) {
                        // إعادة تحميل الصورة الأصلية
                        let previewImg = document.getElementById('imagePreview');
                        let placeholder = document.getElementById('imagePreviewPlaceholder');
                        
                        if (previewImg) {
                            previewImg.src = '../assets/img/about/' + currentImage;
                            if (currentImage) {
                                previewImg.style.display = 'block';
                                if (placeholder) placeholder.style.display = 'none';
                            } else {
                                previewImg.style.display = 'none';
                                if (placeholder) placeholder.style.display = 'flex';
                            }
                        }
                    } else {
                        // إعادة عرض العنصر النائب
                        let previewImg = document.getElementById('imagePreview');
                        let placeholder = document.getElementById('imagePreviewPlaceholder');
                        
                        if (previewImg) {
                            previewImg.style.display = 'none';
                        }
                        
                        if (placeholder) {
                            placeholder.style.display = 'flex';
                        }
                    }
                    
                    // إعادة تعيين ألوان الحدود
                    const inputs = form.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.style.borderColor = '#e0e0e0';
                        input.style.boxShadow = 'none';
                    });
                    
                    alert('تمت إعادة تعيين النموذج');
                }
            }
        }
        
        // وظيفة بدائية للسحب والإفلات للعناصر
        function initDragAndDrop() {
            const listItems = document.querySelectorAll('.list-item-card');
            let draggedItem = null;
            
            listItems.forEach(item => {
                item.setAttribute('draggable', 'true');
                
                item.addEventListener('dragstart', function(e) {
                    draggedItem = this;
                    setTimeout(() => {
                        this.style.opacity = '0.4';
                    }, 0);
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', this.innerHTML);
                });
                
                item.addEventListener('dragend', function() {
                    setTimeout(() => {
                        this.style.opacity = '1';
                        draggedItem = null;
                    }, 0);
                });
                
                item.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    return false;
                });
                
                item.addEventListener('dragenter', function(e) {
                    e.preventDefault();
                    this.style.backgroundColor = '#e3f2fd';
                });
                
                item.addEventListener('dragleave', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                
                item.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (draggedItem !== this) {
                        const allItems = Array.from(document.querySelectorAll('.list-item-card'));
                        const draggedIndex = allItems.indexOf(draggedItem);
                        const targetIndex = allItems.indexOf(this);
                        
                        // إعادة ترتيب العناصر في الواجهة
                        const container = this.parentNode;
                        if (draggedIndex < targetIndex) {
                            container.insertBefore(draggedItem, this.nextSibling);
                        } else {
                            container.insertBefore(draggedItem, this);
                        }
                        
                        // تحديث أرقام العناصر
                        updateItemNumbers();
                        
                        // هنا يمكن إضافة كود لتحديث ترتيب العناصر في قاعدة البيانات
                        // updateItemsOrder();
                    }
                    
                    this.style.backgroundColor = '#f8f9fa';
                    return false;
                });
            });
        }
        
        // تحديث أرقام العناصر بعد السحب والإفلات
        function updateItemNumbers() {
            const items = document.querySelectorAll('.list-item-card');
            items.forEach((item, index) => {
                const numberElement = item.querySelector('.list-item-index');
                if (numberElement) {
                    numberElement.textContent = index + 1;
                }
            });
        }
        
        // وظيفة معاينة المحتوى
        function previewContent() {
            const title = document.getElementById('title').value;
            const p1 = document.getElementById('p1').value;
            const p2 = document.getElementById('p2').value;
            
            const previewWindow = window.open('', 'معاينة المحتوى', 'width=800,height=600,scrollbars=yes');
            
            const previewHTML = `
                <!DOCTYPE html>
                <html dir="rtl">
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            padding: 20px;
                            background: #f5f5f5;
                        }
                        .preview-container {
                            max-width: 800px;
                            margin: 0 auto;
                            background: white;
                            padding: 30px;
                            border-radius: 10px;
                            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                        }
                        .preview-title {
                            color: #144734;
                            font-size: 2rem;
                            margin-bottom: 20px;
                            border-bottom: 3px solid #e76a04;
                            padding-bottom: 10px;
                        }
                        .preview-subtitle {
                            color: #666;
                            font-size: 1.2rem;
                            margin-bottom: 20px;
                            line-height: 1.6;
                        }
                        .preview-content {
                            color: #333;
                            line-height: 1.8;
                            margin-bottom: 20px;
                        }
                        .preview-image {
                            max-width: 100%;
                            height: auto;
                            border-radius: 10px;
                            margin: 20px 0;
                        }
                        .preview-note {
                            background: #f8f9fa;
                            padding: 15px;
                            border-radius: 5px;
                            border-right: 3px solid #e76a04;
                            margin-top: 20px;
                            font-size: 0.9rem;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <div class="preview-container">
                        <h1 class="preview-title">${title || '(بدون عنوان)'}</h1>
                        <div class="preview-subtitle">${p1 || '(بدون وصف قصير)'}</div>
                        <div class="preview-content">${p2 || '(بدون وصف مفصل)'}</div>
                        <div class="preview-note">
                            <strong>ملاحظة:</strong> هذه معاينة للمحتوى فقط. الصور والعناصر الأخرى لن تظهر هنا.
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            previewWindow.document.write(previewHTML);
            previewWindow.document.close();
        }
        
        // اختصارات لوحة المفاتيح
        document.addEventListener('keydown', function(e) {
            // Ctrl + S للحفظ
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.querySelector('button[name="update_about"]').click();
            }
            
            // Ctrl + N لإضافة عنصر جديد
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                document.getElementById('new_list_item').focus();
            }
            
            // Esc لإغلاق Modals
            if (e.key === 'Escape') {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editListItemModal'));
                if (modal) {
                    modal.hide();
                }
            }
        });
        
        // إغلاق الرسائل تلقائياً بعد 5 ثوانٍ
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>