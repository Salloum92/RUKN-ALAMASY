<?php
include 'check.php';

// فقط للمشرفين
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_database'])) {
    try {
        // إضافة الأعمدة المفقودة
        $queries = [
            "ALTER TABLE services ADD COLUMN IF NOT EXISTS display_order INT NOT NULL DEFAULT 0 AFTER status",
            "ALTER TABLE services ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "ALTER TABLE services ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ];
        
        foreach ($queries as $sql) {
            try {
                $query->eQuery($sql);
            } catch (Exception $e) {
                // تجاهل الأخطاء
            }
        }
        
        $_SESSION['success_message'] = "تم تحديث قاعدة البيانات بنجاح";
        header('Location: services.php');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "خطأ في تحديث قاعدة البيانات: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تحديث قاعدة البيانات</title>
    <?php include 'includes/css.php'; ?>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .update-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="update-card">
        <div class="text-center mb-4">
            <i class="fas fa-database fa-4x text-primary mb-3"></i>
            <h3>تحديث قاعدة البيانات</h3>
            <p class="text-muted">هذه العملية ستضيف الأعمدة المطلوبة لإدارة الخدمات</p>
        </div>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> ما الذي سيفعل:</h5>
            <ul class="mb-0">
                <li>إضافة عمود ترتيب العرض (display_order)</li>
                <li>إضافة تاريخ الإنشاء (created_at)</li>
                <li>إضافة تاريخ التحديث (updated_at)</li>
            </ul>
        </div>
        
        <form method="POST" action="">
            <div class="text-center mt-4">
                <button type="submit" name="update_database" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-sync-alt"></i> بدء التحديث
                </button>
                <a href="services.php" class="btn btn-secondary btn-lg px-5">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</body>
</html>