<?php
// بدء الجلسة في البداية
session_start();

include '../config.php';
$query = new Database();

// إذا كان هناك خروج ناجح، عرض رسالة
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    $logout_message = 'تم تسجيل الخروج بنجاح';
}

// إذا انتهت الجلسة
if (isset($_GET['expired']) && $_GET['expired'] == 'true') {
    $error = 'انتهت الجلسة، الرجاء تسجيل الدخول مرة أخرى';
}

// إذا كان المستخدم مسجلاً بالفعل، توجيهه للوحة التحكم
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // التحقق من وجود ملف products.php
    if (file_exists('products.php')) {
        header('Location: products.php');
    } elseif (file_exists('admin_dashboard.php')) {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

// رسائل الخطأ
$error = '';
$username = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $query->validate($_POST['username']);
    $password = $_POST['password'];
    
    // التحقق من عدم وجود حقول فارغة
    if (empty($username) || empty($password)) {
        $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور';
    } else {
        // البحث عن المستخدم في قاعدة البيانات
        $result = $query->login($username, $password, 'users');
        
        if (!empty($result)) {
            // تسجيل الدخول ناجح
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $result[0]['id'];
            $_SESSION['admin_name'] = $result[0]['name'];
            $_SESSION['admin_username'] = $result[0]['username'];
            $_SESSION['login_time'] = time();
            
            // إعادة التوجيه إلى لوحة التحكم
            if (file_exists('admin_dashboard.php')) {
                header('Location: admin_dashboard.php');
            } elseif (file_exists('products.php')) {
                header('Location: products.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | لوحة تحكم ركن الأماسي</title>
    
    <!-- مكتبات CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: linear-gradient(135deg, var(--dark-color) 0%, #1e5b48 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .brand-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .brand-subtitle {
            color: #666;
            font-size: 0.95rem;
        }
        
        .alert-danger-custom {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #721c24;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success-custom {
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.2);
            color: #155724;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 15px 50px 15px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.15);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
        }
        
        .toggle-password {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-login:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(231, 106, 4, 0.3);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .login-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .link-custom {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }
        
        .link-custom:hover {
            color: var(--dark-color);
            text-decoration: underline;
        }
        
        .demo-info {
            background-color: rgba(20, 71, 52, 0.05);
            border: 1px solid rgba(20, 71, 52, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        
        .demo-info strong {
            color: var(--dark-color);
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 25px;
            }
            
            .logo-icon {
                width: 70px;
                height: 70px;
            }
            
            .brand-name {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- الشعار -->
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1 class="brand-name">ركن الأماسي</h1>
                <p class="brand-subtitle">لوحة تحكم النظام</p>
            </div>
            
            <!-- رسالة تسجيل الخروج الناجح -->
            <?php if (isset($logout_message)): ?>
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo htmlspecialchars($logout_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- رسالة الخطأ -->
            <?php if (!empty($error)): ?>
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- نموذج تسجيل الدخول -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="loginForm">
                <!-- اسم المستخدم -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>
                        اسم المستخدم
                    </label>
                    <div class="input-with-icon">
                        <input type="text" 
                               class="form-control-custom" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($username); ?>"
                               placeholder="أدخل اسم المستخدم"
                               required
                               autocomplete="username"
                               autofocus>
                        <div class="input-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                </div>
                
                <!-- كلمة المرور -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-key me-1"></i>
                        كلمة المرور
                    </label>
                    <div class="input-with-icon">
                        <input type="password" 
                               class="form-control-custom" 
                               id="password" 
                               name="password" 
                               placeholder="أدخل كلمة المرور"
                               required
                               autocomplete="current-password">
                        <div class="input-icon">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- معلومات الدخول للتجربة -->
                
                
                <!-- زر تسجيل الدخول -->
                <button type="submit" 
                        name="login" 
                        class="btn-login" 
                        id="loginButton">
                    <span id="loginText">تسجيل الدخول</span>
                    <div class="spinner" id="loadingSpinner" style="display: none;"></div>
                </button>
            </form>
            
            <!-- الروابط -->
            <div class="login-links">
                <a href="../index.php" class="link-custom me-3">
                    <i class="bi bi-house-door me-1"></i>
                    العودة للموقع
                </a>
            </div>
            
            <!-- حقوق النشر -->
            <div class="text-center mt-4 pt-3 border-top" style="color: #666; font-size: 0.85rem;">
                <i class="bi bi-c-circle me-1"></i>
                <?php echo date('Y'); ?> جميع الحقوق محفوظة
            </div>
        </div>
    </div>
    
    <!-- مكتبة Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // كود JavaScript يبقى كما هو...
    </script>
</body>
</html>