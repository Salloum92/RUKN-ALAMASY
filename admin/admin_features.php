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

// معالجة إضافة/تحديث ميزة
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_feature'])) {
        // إضافة ميزة جديدة
        $icon = $query->validate($_POST['icon']);
        $title = $query->validate($_POST['title']);
        $description = $query->validate($_POST['description']);
        
        $data = [
            'icon' => $icon,
            'title' => $title,
            'description' => $description
        ];
        
        $result = $query->insert('features', $data);
        
        if ($result) {
            $message = 'تمت إضافة الميزة بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء إضافة الميزة';
            $messageType = 'danger';
        }
    }
    elseif (isset($_POST['update_feature'])) {
        // تحديث ميزة موجودة
        $id = intval($_POST['id']);
        $icon = $query->validate($_POST['icon']);
        $title = $query->validate($_POST['title']);
        $description = $query->validate($_POST['description']);
        
        $data = [
            'icon' => $icon,
            'title' => $title,
            'description' => $description
        ];
        
        $result = $query->update('features', $data, "WHERE id = $id");
        
        if ($result) {
            $message = 'تم تحديث الميزة بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء تحديث الميزة';
            $messageType = 'danger';
        }
    }
    elseif (isset($_POST['delete_feature'])) {
        // حذف ميزة
        $id = intval($_POST['id']);
        $result = $query->delete('features', "WHERE id = $id");
        
        if ($result) {
            $message = 'تم حذف الميزة بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء حذف الميزة';
            $messageType = 'danger';
        }
    }
}

// جلب جميع الميزات
$features = $query->select('features', '*', 'ORDER BY id DESC');

// أيقونات Bootstrap المتاحة
$available_icons = [
    'bi bi-bounding-box-circles',
    'bi bi-calendar4-week',
    'bi bi-broadcast',
    'bi bi-shield-check',
    'bi bi-lightning',
    'bi bi-clock',
    'bi bi-star',
    'bi bi-award',
    'bi bi-check-circle',
    'bi bi-people',
    'bi bi-gear',
    'bi bi-graph-up',
    'bi bi-headset',
    'bi bi-laptop',
    'bi bi-phone',
    'bi bi-truck',
    'bi bi-wallet',
    'bi bi-cash',
    'bi bi-shield-lock',
    'bi bi-key',
    'bi bi-eye',
    'bi bi-camera',
    'bi bi-bell',
    'bi bi-chat',
    'bi bi-cloud',
    'bi bi-database',
    'bi bi-display',
    'bi bi-download',
    'bi bi-emoji-smile',
    'bi bi-flag',
    'bi bi-globe',
    'bi bi-heart',
    'bi bi-house',
    'bi bi-link',
    'bi bi-lock',
    'bi bi-megaphone',
    'bi bi-patch-check',
    'bi bi-pencil',
    'bi bi-printer',
    'bi bi-rocket',
    'bi bi-search',
    'bi bi-shield',
    'bi bi-speedometer',
    'bi bi-tools',
    'bi bi-unlock',
    'bi bi-upload',
    'bi bi-wifi'
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الميزات - ركن الأماسي</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-top: 4px solid var(--primary-color);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: white;
        }
        
        .feature-title {
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.6;
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
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(231, 106, 4, 0.25);
        }
        
        .icon-preview {
            font-size: 1.5rem;
            color: var(--primary-color);
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
    </style>
</head>
<body>
    <!-- الرسائل -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
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
                            <a class="nav-link active" href="admin_features.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-stars text-primary"></i>
                        إدارة الميزات
                    </h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                        <i class="bi bi-plus-circle"></i> إضافة ميزة جديدة
                    </button>
                </div>

                <!-- عرض الميزات -->
                <div class="row">
                    <?php if (empty($features)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                لا توجد ميزات لعرضها. أضف ميزة جديدة.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($features as $feature): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="feature-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="feature-icon">
                                                <i class="<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                                            </div>
                                            <div class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></div>
                                            <div class="feature-description"><?php echo htmlspecialchars($feature['description']); ?></div>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editFeatureModal"
                                                    onclick="editFeature(
                                                        <?php echo $feature['id']; ?>,
                                                        '<?php echo addslashes($feature['icon']); ?>',
                                                        '<?php echo addslashes($feature['title']); ?>',
                                                        '<?php echo addslashes($feature['description']); ?>'
                                                    )">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الميزة؟');">
                                                <input type="hidden" name="id" value="<?php echo $feature['id']; ?>">
                                                <button type="submit" name="delete_feature" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal إضافة ميزة -->
    <div class="modal fade" id="addFeatureModal" tabindex="-1" aria-labelledby="addFeatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFeatureModalLabel">
                            <i class="bi bi-plus-circle"></i> إضافة ميزة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="feature_icon" class="form-label">الأيقونة</label>
                                <select class="form-select" id="feature_icon" name="icon" required onchange="updateFeatureIconPreview(this.value)">
                                    <option value="">اختر أيقونة</option>
                                    <?php foreach ($available_icons as $icon): ?>
                                        <option value="<?php echo $icon; ?>"><?php echo $icon; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <div id="featureIconPreview" class="icon-preview d-flex align-items-center">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                                            <i class=""></i>
                                        </div>
                                        <small class="text-muted">معاينة الأيقونة</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="feature_title" class="form-label">عنوان الميزة</label>
                                <input type="text" class="form-control" id="feature_title" name="title" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="feature_description" class="form-label">وصف الميزة</label>
                                <textarea class="form-control" id="feature_description" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="add_feature" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal تعديل ميزة -->
    <div class="modal fade" id="editFeatureModal" tabindex="-1" aria-labelledby="editFeatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_feature_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFeatureModalLabel">
                            <i class="bi bi-pencil"></i> تعديل الميزة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_feature_icon" class="form-label">الأيقونة</label>
                                <select class="form-select" id="edit_feature_icon" name="icon" required onchange="updateEditFeatureIconPreview(this.value)">
                                    <option value="">اختر أيقونة</option>
                                    <?php foreach ($available_icons as $icon): ?>
                                        <option value="<?php echo $icon; ?>"><?php echo $icon; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <div id="editFeatureIconPreview" class="icon-preview d-flex align-items-center">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                                            <i class=""></i>
                                        </div>
                                        <small class="text-muted">معاينة الأيقونة</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="edit_feature_title" class="form-label">عنوان الميزة</label>
                                <input type="text" class="form-control" id="edit_feature_title" name="title" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="edit_feature_description" class="form-label">وصف الميزة</label>
                                <textarea class="form-control" id="edit_feature_description" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="update_feature" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // معاينة أيقونة الميزة عند الإضافة
        function updateFeatureIconPreview(icon) {
            var previewDiv = document.getElementById('featureIconPreview');
            var iconDiv = previewDiv.querySelector('div');
            var iconElement = iconDiv.querySelector('i');
            iconElement.className = icon;
        }
        
        // معاينة أيقونة الميزة عند التعديل
        function updateEditFeatureIconPreview(icon) {
            var previewDiv = document.getElementById('editFeatureIconPreview');
            var iconDiv = previewDiv.querySelector('div');
            var iconElement = iconDiv.querySelector('i');
            iconElement.className = icon;
        }
        
        // تعبئة بيانات التعديل للميزة
        function editFeature(id, icon, title, description) {
            document.getElementById('edit_feature_id').value = id;
            document.getElementById('edit_feature_icon').value = icon;
            document.getElementById('edit_feature_title').value = title;
            document.getElementById('edit_feature_description').value = description;
            
            // تحديث معاينة الأيقونة
            updateEditFeatureIconPreview(icon);
            
            // إظهار Modal التعديل
            var editModal = new bootstrap.Modal(document.getElementById('editFeatureModal'));
            editModal.show();
        }
        
        // إغلاق الرسائل تلقائياً بعد 5 ثوانٍ
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>