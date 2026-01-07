<?php
session_start();
require_once '../config.php';
$db = new Database();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login/");
    exit;
}

// جلب بيانات من نحن
$about_data = $db->select('about', '*', 'LIMIT 1');
$about = $about_data[0] ?? null;

// جلب عناصر القائمة
$list_items = $db->select('about_ul_items', '*', 'WHERE about_id = 1 ORDER BY id ASC');

// معالجة طلبات الحفظ
$success_msg = $error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_about'])) {
        // تحديث المحتوى الرئيسي
        $data = [
            'title' => $_POST['title'],
            'p1' => $_POST['p1'],
            'p2' => $_POST['p2']
        ];
        
        // معالجة صورة جديدة إذا تم رفعها
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = "../../assets/img/";
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // التحقق من نوع الملف
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $data['image'] = $file_name;
                    // حذف الصورة القديمة إذا كانت موجودة
                    if ($about && isset($about['image']) && $about['image']) {
                        $old_file = $upload_dir . $about['image'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                }
            }
        }
        
        if ($about) {
            $result = $db->update('about', $data, "WHERE id = 1");
        } else {
            $result = $db->insert('about', $data);
        }
        
        if ($result) {
            $success_msg = "تم تحديث محتوى 'من نحن' بنجاح!";
            // إعادة تحميل البيانات بعد التحديث
            $about_data = $db->select('about', '*', 'LIMIT 1');
            $about = $about_data[0] ?? null;
        } else {
            $error_msg = "حدث خطأ أثناء التحديث!";
        }
        
    } elseif (isset($_POST['add_item'])) {
        // إضافة عنصر جديد للقائمة
        if (!empty($_POST['new_item'])) {
            $result = $db->insert('about_ul_items', [
                'about_id' => 1,
                'list_item' => $_POST['new_item']
            ]);
            
            if ($result) {
                $success_msg = "تم إضافة العنصر بنجاح!";
                $list_items = $db->select('about_ul_items', '*', 'WHERE about_id = 1 ORDER BY id ASC');
            } else {
                $error_msg = "حدث خطأ أثناء إضافة العنصر!";
            }
        }
        
    } elseif (isset($_POST['update_item'])) {
        // تحديث عنصر في القائمة
        $item_id = $_POST['item_id'];
        $list_item = $_POST['list_item'];
        
        $result = $db->update('about_ul_items', 
            ['list_item' => $list_item], 
            "WHERE id = $item_id"
        );
        
        if ($result) {
            $success_msg = "تم تحديث العنصر بنجاح!";
            $list_items = $db->select('about_ul_items', '*', 'WHERE about_id = 1 ORDER BY id ASC');
        } else {
            $error_msg = "حدث خطأ أثناء التحديث!";
        }
        
    } elseif (isset($_POST['delete_item'])) {
        // حذف عنصر من القائمة
        $item_id = $_POST['item_id'];
        
        $result = $db->delete('about_ul_items', "WHERE id = $item_id");
        
        if ($result) {
            $success_msg = "تم حذف العنصر بنجاح!";
            $list_items = $db->select('about_ul_items', '*', 'WHERE about_id = 1 ORDER BY id ASC');
        } else {
            $error_msg = "حدث خطأ أثناء الحذف!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحكم - من نحن</title>
    <!-- نفس مكتبات التصميم -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #e76a04;
            --primary-dark: #d45f00;
            --secondary-color: rgb(243, 212, 23);
            --secondary-dark: rgb(223, 192, 3);
            --dark-color: #144734ff;
            --dark-light: rgb(30, 91, 72);
            --light-color: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #e76a04, #f3d417);
            --gradient-dark: linear-gradient(135deg, #144734, #1e5b48);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            background: var(--gradient-dark);
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            padding-top: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-nav {
            padding: 0 15px;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .nav-link i {
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-right: 250px;
            padding: 20px;
        }

        /* Header */
        .page-header {
            background: var(--gradient-primary);
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(231, 106, 4, 0.2);
        }

        .page-header h1 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .page-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        /* Cards */
        .dashboard-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-primary);
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 0 0 20px 0;
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(20, 71, 52, 0.1);
        }

        .card-header h3 {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 i {
            color: var(--primary-color);
        }

        /* Form Styling */
        .form-control, .form-select {
            border: 2px solid rgba(20, 71, 52, 0.1);
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(231, 106, 4, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        /* Buttons */
        .btn {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 10px 20px rgba(231, 106, 4, 0.2);
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(231, 106, 4, 0.3);
        }

        .btn-success {
            background: var(--gradient-dark);
            color: white;
            box-shadow: 0 10px 20px rgba(20, 71, 52, 0.2);
        }

        .btn-success:hover {
            background: var(--gradient-dark);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(20, 71, 52, 0.3);
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        /* Image Preview */
        .image-preview {
            border: 3px dashed rgba(20, 71, 52, 0.2);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            background: rgba(20, 71, 52, 0.05);
            margin-top: 10px;
        }

        .current-image {
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        /* List Items */
        .list-item {
            background: rgba(20, 71, 52, 0.05);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .list-item:hover {
            border-color: var(--primary-color);
            background: rgba(231, 106, 4, 0.05);
        }

        .list-item .form-control {
            border: none;
            background: transparent;
            padding: 0;
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        .item-actions .btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 15px;
            padding: 20px 25px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 5px solid #198754;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 5px solid #dc3545;
        }

        /* Preview Box */
        .preview-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 2px solid rgba(20, 71, 52, 0.1);
        }

        .preview-box h4 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding-top: 20px;
            }
            
            .sidebar-header h3,
            .nav-link span {
                display: none;
            }
            
            .main-content {
                margin-right: 70px;
            }
            
            .nav-link {
                justify-content: center;
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .dashboard-card {
                padding: 20px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .row {
                flex-direction: column;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(20, 71, 52, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="bi bi-shield-check"></i> لوحة التحكم</h3>
            <small>ركن الأماسي</small>
        </div>
        
        <div class="sidebar-nav">
            <a href="../dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>الرئيسية</span>
            </a>
            <a href="about.php" class="nav-link active">
                <i class="bi bi-people"></i>
                <span>من نحن</span>
            </a>
            <a href="../banners/" class="nav-link">
                <i class="bi bi-images"></i>
                <span>البنرات</span>
            </a>
            <a href="../products/" class="nav-link">
                <i class="bi bi-box"></i>
                <span>المنتجات</span>
            </a>
            <a href="../services/" class="nav-link">
                <i class="bi bi-tools"></i>
                <span>الخدمات</span>
            </a>
            <a href="../contact/" class="nav-link">
                <i class="bi bi-envelope"></i>
                <span>التواصل</span>
            </a>
            <a href="../messages/" class="nav-link">
                <i class="bi bi-chat-dots"></i>
                <span>الرسائل</span>
            </a>
            <a href="../logout.php" class="nav-link" style="margin-top: 50px;">
                <i class="bi bi-box-arrow-right"></i>
                <span>تسجيل الخروج</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="page-header fade-in" data-aos="fade-down">
            <h1><i class="bi bi-people"></i> إدارة قسم "من نحن"</h1>
            <p>قم بتعديل محتوى صفحة "من نحن" وإدارة نقاط القوة والعروض</p>
        </div>

        <!-- Alerts -->
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show fade-in" role="alert" data-aos="fade-up">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert" data-aos="fade-up">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- المحتوى الرئيسي -->
            <div class="col-lg-8">
                <div class="dashboard-card fade-in" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <h3><i class="bi bi-pencil-square"></i> المحتوى الرئيسي</h3>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label">العنوان الرئيسي</label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?php echo htmlspecialchars($about['title'] ?? 'من نحن'); ?>" 
                                   placeholder="أدخل العنوان الرئيسي للصفحة" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">الفقرة الأولى</label>
                            <textarea class="form-control" name="p1" rows="6" 
                                      placeholder="أدخل النص الأول لقسم 'من نحن'" required><?php echo htmlspecialchars($about['p1'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">الفقرة الثانية</label>
                            <textarea class="form-control" name="p2" rows="6" 
                                      placeholder="أدخل النص الثاني لقسم 'من نحن'" required><?php echo htmlspecialchars($about['p2'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">صورة العرض</label>
                            <?php if ($about && isset($about['image']) && $about['image']): ?>
                                <div class="image-preview">
                                    <img src="../../assets/img/<?php echo htmlspecialchars($about['image']); ?>" 
                                         class="current-image img-fluid">
                                    <p class="text-muted mb-0">الصورة الحالية: <?php echo htmlspecialchars($about['image']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <input type="file" class="form-control" name="image" accept="image/*" id="imageUpload">
                                <small class="text-muted">اختياري - يفضل صورة بحجم 600×400 بكسل (JPG, PNG, GIF, WebP)</small>
                            </div>
                            
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-width: 300px;">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="submit" name="update_about" class="btn btn-primary">
                                <i class="bi bi-save"></i> حفظ التغييرات
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> إعادة تعيين
                            </button>
                        </div>
                    </form>
                </div>

                <!-- نقاط القوة -->
                <div class="dashboard-card fade-in" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header">
                        <h3><i class="bi bi-list-check"></i> نقاط القوة والعروض</h3>
                    </div>
                    
                    <form method="POST" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="new_item" 
                                   placeholder="أضف نقطة قوة جديدة أو عرض مميز..." 
                                   aria-label="أضف عنصر جديد" required>
                            <button type="submit" name="add_item" class="btn btn-success">
                                <i class="bi bi-plus-lg"></i> إضافة
                            </button>
                        </div>
                    </form>
                    
                    <div class="list-items-container">
                        <?php if ($list_items): ?>
                            <?php foreach ($list_items as $item): ?>
                                <div class="list-item d-flex justify-content-between align-items-center fade-in">
                                    <form method="POST" class="w-100 d-flex align-items-center gap-3">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" name="list_item" 
                                                   value="<?php echo htmlspecialchars($item['list_item']); ?>" 
                                                   placeholder="أدخل نص العنصر">
                                        </div>
                                        <div class="item-actions">
                                            <button type="submit" name="update_item" class="btn btn-sm btn-outline-primary" 
                                                    title="تحديث">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا العنصر؟')" 
                                                    title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-list-check display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">لم يتم إضافة أي عناصر بعد</h5>
                                <p class="text-muted">قم بإضافة نقاط القوة والعروض الخاصة بشركتك</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- المعاينة -->
            <div class="col-lg-4">
                <div class="dashboard-card fade-in" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-header">
                        <h3><i class="bi bi-eye"></i> معاينة مباشرة</h3>
                    </div>
                    
                    <div class="preview-box">
                        <h4 class="mb-3"><?php echo htmlspecialchars($about['title'] ?? 'من نحن'); ?></h4>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">الفقرة الأولى:</h6>
                            <p class="text-dark"><?php echo nl2br(htmlspecialchars($about['p1'] ?? 'محتوى الفقرة الأولى...')); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">نقاط القوة:</h6>
                            <ul class="list-unstyled">
                                <?php if ($list_items): ?>
                                    <?php foreach ($list_items as $item): ?>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <?php echo htmlspecialchars($item['list_item']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="text-muted">
                                        <i class="bi bi-dash-circle me-2"></i>
                                        عنصر توضيحي 1
                                    </li>
                                    <li class="text-muted">
                                        <i class="bi bi-dash-circle me-2"></i>
                                        عنصر توضيحي 2
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">الفقرة الثانية:</h6>
                            <p class="text-dark"><?php echo nl2br(htmlspecialchars($about['p2'] ?? 'محتوى الفقرة الثانية...')); ?></p>
                        </div>
                        
                        <?php if ($about && isset($about['image']) && $about['image']): ?>
                            <div class="mt-4">
                                <h6 class="text-muted mb-2">الصورة:</h6>
                                <img src="../../assets/img/<?php echo htmlspecialchars($about['image']); ?>" 
                                     class="img-fluid rounded" style="max-width: 100%;" 
                                     onerror="this.src='https://via.placeholder.com/600x400?text=صورة+غير+متوفرة'">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- معلومات سريعة -->
                <div class="dashboard-card fade-in" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header">
                        <h3><i class="bi bi-info-circle"></i> معلومات سريعة</h3>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="bi bi-file-text display-4 text-primary mb-2"></i>
                                <h4 class="mb-0"><?php echo count($list_items); ?></h4>
                                <small class="text-muted">نقطة قوة</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="bi bi-image display-4 text-success mb-2"></i>
                                <h4 class="mb-0"><?php echo ($about && isset($about['image'])) ? '1' : '0'; ?></h4>
                                <small class="text-muted">صورة</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>نصيحة:</strong> تأكد من أن جميع النصوص واضحة وموجزة، وأن الصور عالية الجودة وذات صلة.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
                easing: 'ease-out-cubic'
            });
        }

        // Image preview
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            if (e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = `<img src="${event.target.result}" alt="Preview" class="img-fluid rounded" style="max-width: 300px;">`;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(e.target.files[0]);
            } else {
                preview.style.display = 'none';
            }
        });

        // Auto-save indicator
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الحفظ...';
                    submitBtn.disabled = true;
                }
            });
        });

        // Real-time preview update
        const titleInput = document.querySelector('input[name="title"]');
        const p1Input = document.querySelector('textarea[name="p1"]');
        const p2Input = document.querySelector('textarea[name="p2"]');

        function updatePreview() {
            const previewTitle = document.querySelector('.preview-box h4');
            const previewP1 = document.querySelector('.preview-box .text-dark:nth-child(2)');
            const previewP2 = document.querySelector('.preview-box .text-dark:nth-child(4)');
            
            if (previewTitle) previewTitle.textContent = titleInput.value || 'من نحن';
            if (previewP1) previewP1.textContent = p1Input.value || 'محتوى الفقرة الأولى...';
            if (previewP2) previewP2.textContent = p2Input.value || 'محتوى الفقرة الثانية...';
        }

        if (titleInput) titleInput.addEventListener('input', updatePreview);
        if (p1Input) p1Input.addEventListener('input', updatePreview);
        if (p2Input) p2Input.addEventListener('input', updatePreview);

        // Character counter for textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            const counter = document.createElement('div');
            counter.className = 'text-muted text-end small mt-1';
            textarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const length = textarea.value.length;
                counter.textContent = `${length} حرف`;
                
                if (length > 500) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-muted');
                } else {
                    counter.classList.remove('text-danger');
                    counter.classList.add('text-muted');
                }
            }
            
            textarea.addEventListener('input', updateCounter);
            updateCounter(); // Initialize
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('button[name="update_about"]').click();
            }
            
            // Escape to reset
            if (e.key === 'Escape') {
                document.querySelector('button[type="reset"]').click();
            }
        });

        // Auto-focus first field
        window.addEventListener('load', function() {
            const firstInput = document.querySelector('input, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Smooth scroll to element
        function scrollToElement(element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Show success message with animation
        <?php if ($success_msg): ?>
            setTimeout(() => {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.style.animation = 'fadeIn 0.5s ease-in';
                }
            }, 100);
        <?php endif; ?>
    </script>
</body>
</html>