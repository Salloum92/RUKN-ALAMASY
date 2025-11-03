<?php
// lang.php

// ملفات الترجمة
$translations = [
    'ar' => [
        'home' => 'الرئيسية',
        'about' => 'من نحن',
        'services' => 'خدماتنا',
        'products' => 'منتجاتنا',
        'contact' => 'اتصل بنا',
        'arabic' => 'العربية',
        'english' => 'الإنجليزية',
        'welcome' => 'مرحباً بكم',
        'description' => 'أفضل المنتجات والخدمات',
        'call_us' => 'اتصل بنا',
        'email_us' => 'راسلنا',
        'open_hours' => 'أوقات العمل',
        'get_quote' => 'اطلب عرض سعر',
        'login' => 'تسجيل الدخول',
        'register' => 'إنشاء حساب',
        'search' => 'ابحث هنا...',
        // إضافة الترجمات المفقودة للبحث
        'search_results' => 'نتائج البحث',
        'search_empty' => 'لم يتم العثور على نتائج',
        'view_details' => 'عرض التفاصيل',
        'view_service' => 'عرض الخدمة',
        'all_categories' => 'جميع الفئات',
        'web_development' => 'تطوير الويب',
        'mobile_apps' => 'تطبيقات الجوال',
        'seo' => 'تحسين محركات البحث',
        'digital_marketing' => 'التسويق الرقمي',
        'cart' => 'عربة التسوق',
        'profile' => 'الملف الشخصي',
        'logout' => 'تسجيل الخروج'
    ],
    'en' => [
        'home' => 'Home',
        'about' => 'About Us',
        'services' => 'Services',
        'products' => 'Products',
        'contact' => 'Contact',
        'arabic' => 'Arabic',
        'english' => 'English',
        'welcome' => 'Welcome',
        'description' => 'Premium Products & Services',
        'call_us' => 'Call Us',
        'email_us' => 'Email Us',
        'open_hours' => 'Open Hours',
        'get_quote' => 'Get Quote',
        'login' => 'Login',
        'register' => 'Register',
        'search' => 'Search here...',
        // إضافة الترجمات المفقودة للبحث
        'search_results' => 'Search Results',
        'search_empty' => 'No results found',
        'view_details' => 'View Details',
        'view_service' => 'View Service',
        'all_categories' => 'All Categories',
        'web_development' => 'Web Development',
        'mobile_apps' => 'Mobile Apps',
        'seo' => 'SEO',
        'digital_marketing' => 'Digital Marketing',
        'cart' => 'Shopping Cart',
        'profile' => 'Profile',
        'logout' => 'Logout'
    ]
];

// تحديد اللغة الافتراضية
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

// تغيير اللغة إذا تم الضغط على زر التغيير
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'ar';
    // الرجوع للصفحة السابقة
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: ' . $referer);
    exit();
}

$lang = $_SESSION['lang'];
$t = $translations[$lang];
?>