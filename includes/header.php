<?php
require_once 'config.php';

// استعلامات قاعدة البيانات
$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

// تحديد اللغة الافتراضية
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

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
        'cart' => 'Shopping Cart',
        'profile' => 'Profile',
        'logout' => 'Logout'
    ]
];

$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

$lang = $_SESSION['lang'];
$t = $translations[$lang];

$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= ($lang == 'ar') ? 'rtl' : 'ltr' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rukn Alamasy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    /* ===== VARIABLES ===== */
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
    }

    /* ===== RESET & BASE ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: var(--text-dark);
        background: var(--bg-light);
    }

    .container-fluid {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
         position: relative;
    z-index: 5; /* فوق الجسيمات */
    }

    /* ===== TOP BAR ===== */
    .top-bar {
        background: linear-gradient(135deg, var(--dark-color), var(--dark-light));
        color: var(--light-color);
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: relative; 
        overflow: hidden;
        z-index: 100;
        
    }


/* ضمان أن الروابط قابلة للتفاعل */
.top-bar a {
    position: relative;
    z-index: 10;
    pointer-events: auto; /* التأكيد على أنها تستقبل النقرات */
}
    .top-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .contact-info {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #bdc3c7;
        font-size: 0.85rem;
        transition: var(--transition-fast);
    }

    .contact-item i {
        color: var(--primary-color);
        font-size: 0.9rem;
    }

    .contact-item a {
        color: #bdc3c7;
        text-decoration: none;
        transition: var(--transition-fast);
    }

    .contact-item a:hover {
        color: var(--primary-color);
    }

    .contact-item:hover {
        color: var(--primary-color);
        transform: translateY(-1px);
    }

    .social-links {
        display: flex;
        gap: 12px;
    }

    .social-link {
        color: #bdc3c7;
        text-decoration: none;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid #4a5f7a;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .social-link:hover {
        color: var(--bg-white);
        background: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px) scale(1.1);
        box-shadow: 0 4px 12px rgba(231, 106, 4, 0.3);
    }

    /* ===== MAIN NAVIGATION ===== */


    

/* أضف هذه الأنماط لملف CSS الخاص بك */
.main-nav {
    position: relative;
    width: 100%;
    height : 100px;
    z-index: 1000;
    background: #ffffff;
    padding: 15px 0; /* مساحة واسعة في البداية */
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);

}

/* النسخة المخففة عند التمرير */
.main-nav.scrolled {
    position: fixed !important;
    top: 0;
    padding: 8px 0; /* تصغير الارتفاع لإعطاء مساحة للمحتوى */
    background: rgba(255, 255, 255, 0.9); /* شفافية بسيطة */
    backdrop-filter: blur(15px) saturate(180%); /* تأثير الزجاج الضبابي الرهيب */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); /* ظل ناعم جداً */
    border-bottom: 1px solid rgba(20, 71, 52, 0.1); /* تأثير دخول ناعم */
}
.main-nav.scrolled::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #144734, #e76a04); /* تدرج بين الأخضر والبرتقالي الخاص بك */
}


.main-nav.scrolled .nav-link {
    font-size: 0.95rem; /* تصغير الخط قليلاً */
    color: var(--dark-color);
}
@keyframes slideInDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

/* لمنع القفزة في المحتوى عند التثبيت */
body.nav-fixed-active {
    padding-top: 80px; /* نفس ارتفاع الناف بار تقريباً */
}

.main-nav.scrolled .nav-container {
    padding: 10px 0;
}

/* يمكنك إضافة تأثيرات إضافية */
.main-nav.scrolled .logo-text .brand-tagline {
    opacity: 0;
    height: 0;
    overflow: hidden;
    transition: opacity 0.3s ease;
}

.main-nav:not(.scrolled) .logo-text .brand-tagline {
    opacity: 1;
    height: auto;
    transition: opacity 0.3s ease;
}

.nav-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 0;
    gap: 30px;
    transition: padding 0.3s ease;
}


.main-nav.nav-hidden {
    transform: translateY(-100%);
    transition: transform 0.3s ease;
}

.main-nav.scrolling-up {
    transform: translateY(0);
    transition: transform 0.3s ease;
}

/* تأثيرات للعناصر الداخلية */
.main-nav.scrolled .logo-image img {
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.main-nav.scrolled .search-input {
    padding: 8px 15px;
    transition: padding 0.3s ease;
}

/* تأثيرات للقوائم المنسدلة */
.main-nav.scrolled .dropdown-menu {
    margin-top: 10px;
}

/* تحسين الظل عند التمرير */
.main-nav.scrolled {
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
}
    /* Logo Section */
    .logo-section {
        flex-shrink: 0;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        transition: var(--transition);
    }

   

    .logo-image img {
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius);
        object-fit: cover;
        border: 3px solid var(--primary-color);
        box-shadow: 0 4px 15px rgba(231, 106, 4, 0.2);
        transition: var(--transition);
    }

    .logo:hover .logo-image img {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(231, 106, 4, 0.3);
    }

    .logo-text {
        display: flex;
        flex-direction: column;
    }

    .brand-name {
        color: var(--secondary-color);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.2;
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .brand-tagline {
        color: var(--text-light);
        font-size: 0.6rem;
        font-weight: 400;
        margin-top: 1px;
        text-align: center;
    }

    /* Search Section */
    .search-section {
        flex: 1;
        max-width: 500px;
        position: relative;
        color : var(--primary-color);
    }

    .desktop-search {
        width: 100%;
    }

    .search-box {
        position: relative;
        background: white;
        border-radius: 50px;
        border: 2px solid var(--primary-color);
        transition: var(--transition);
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .mobile-search-toggle i{
        color : var(--primary-color) ;
    }

    .search-box:focus-within {
        border-color: var(--primary-color);
        background: var(--bg-white);
        box-shadow: 0 4px 20px rgba(231, 106, 4, 0.15);
        transform: translateY(-1px);
    }

    .search-input {
        width: 100%;
        border: none;
        background: none;
        padding: 14px 20px;
        font-size: 0.95rem;
        color: var(--text-dark);
        outline: none;
    }

    .search-input::placeholder {
        color: var(--text-light);
    }

    .search-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 6px;
        background: var(--primary-color);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        cursor: pointer;
    }

    .search-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-50%) scale(1.05);
    }

    .mobile-search-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--text-dark);
        font-size: 1.3rem;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        transition: var(--transition);
        cursor: pointer;
    }

    .mobile-search-toggle:hover {
        background: rgba(231, 106, 4, 0.1);
        color: var(--primary-color);
    }

    /* Navigation Actions */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 8px;
        
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        color: var(--text-dark);
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: var(--transition);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(231, 106, 4, 0.1), transparent);
        transition: var(--transition);
    }

    .nav-link:hover::before {
        left: 100%;
    }

    .nav-link:hover {
        color: var(--primary-color);
        background: rgba(231, 106, 4, 0.05);
        transform: translateY(-2px);
    }

    .nav-link.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 15px rgba(231, 106, 4, 0.3);
        transform: translateY(-2px);
    }

    .nav-link i {
        font-size: 1.1rem;
        transition: var(--transition);
    }

    .nav-link.active i {
        color: white;
    }

    /* Dropdown Menu */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--bg-white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-xl);
        padding: 8px;
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: var(--transition);
        z-index: 1000;
        border: 1px solid var(--border-color);
    }

    .nav-item:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: block;
        padding: 12px 16px;
        color: var(--text-dark);
        text-decoration: none;
        border-radius: 8px;
        transition: var(--transition-fast);
        font-weight: 500;
    }

    .dropdown-item:hover {
        background: rgba(231, 106, 4, 0.1);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    /* Language Selector */
    .language-selector {
        position: relative;
    }

    .lang-dropdown {
        position: relative;
    }

    .lang-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--primary-color);
        border: 2px solid transparent;
        border-radius: 25px;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .lang-toggle:hover {
        border-color: var(--primary-color);
        background: var(--bg-white);
        box-shadow: var(--shadow);
        transform: translateY(-2px);
    }

    .flag-icon {
        border-radius: 2px;
        border: 1px solid var(--border-color);
    }

    .lang-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--primary-color);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-xl);
        padding: 8px;
        min-width: 160px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: var(--transition);
        z-index: 1000;
    }

    .lang-dropdown:hover .lang-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(5px);
    }

    .lang-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: var(--transition-fast);
        font-weight: 500;
    }

    .lang-option:hover {
        background: rgba(231, 106, 4, 0.1);
        color: var(--dark-color);
    }

    .lang-option.active {
        background: var(--dark-color);
        color: var(--primary-color);
    }

    .lang-option i {
        margin-left: auto;
        color: var(--primary-color);
    }

    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        gap: 4px;
        background: white;
        border: none;
        padding: 8px;
        cursor: pointer;
        transition: var(--transition);
    }

    .mobile-menu-toggle span {
        color: var(--primary-color);
    }

    .menu-bar {
        width: 25px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 2px;
        transition: var(--transition);
    }

    .mobile-menu-toggle:hover .menu-bar {
        background: var(--primary-color);
    }

    .mobile-menu-toggle:hover {
        transform: scale(1.1);
    }

    /* ===== MOBILE SEARCH MODAL ===== */
    .mobile-search-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .mobile-search-modal.active {
        display: flex;
        opacity: 1;
    }

    .search-modal-content {
        background: var(--bg-white);
        border-radius: var(--border-radius-lg);
        padding: 30px;
        width: 90%;
        max-width: 500px;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .mobile-search-modal.active .search-modal-content {
        transform: scale(1);
    }

    .search-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-modal-header h3 {
        color: var(--text-dark);
        margin: 0;
        font-size: 1.5rem;
    }

    .close-search-modal {
        background: none;
        border: none;
        color: var(--text-light);
        font-size: 1.5rem;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .close-search-modal:hover {
        color: var(--primary-color);
        transform: scale(1.1);
    }

    .search-input-group {
        display: flex;
        background: var(--bg-light);
        border-radius: 50px;
        border: 2px solid transparent;
        transition: var(--transition);
        overflow: hidden;
    }

    .search-input-group:focus-within {
        border-color: var(--primary-color);
        background: var(--bg-white);
    }

    .mobile-search-input {
        flex: 1;
        border: none;
        background: none;
        padding: 15px 20px;
        font-size: 1rem;
        outline: none;
    }

    .mobile-search-btn {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 0 25px;
        cursor: pointer;
        transition: var(--transition);
    }

    .mobile-search-btn:hover {
        background: var(--primary-dark);
    }

    /* ===== MOBILE MENU ===== */
    .mobile-menu {
        position: fixed;
        top: 0;
        left: -100%;
        width: 320px;
        height: 100vh;
        background: var(--bg-white);
        box-shadow: var(--shadow-xl);
        z-index: 9998;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .mobile-menu.active {
        left: 0;
        background-color : #144734ff;
    }

    .mobile-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-light);
    }

    .mobile-logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-logo-img {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
    }

    .mobile-brand {
        color: var(--secondary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .mobile-menu-close {
        background: none;
        border: none;
        color: var(--primary-color);
        font-size: 1.5rem;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .mobile-menu-close:hover {
        color: var(--primary-color);
        transform: scale(1.1);
    }

    .mobile-menu-content {
        flex: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 25px;
        overflow-y: auto;
        margin-bottom : 50px;
    }

    /* Mobile Search in Menu */
    .mobile-search-container {
        margin-bottom: 10px;
    }

    .mobile-nav-search-box {
        display: flex;
        background: var(--bg-light);
        border-radius: 25px;
        border: 2px solid transparent;
        transition: var(--transition);
        overflow: hidden;
    }

    .mobile-nav-search-box:focus-within {
        border-color: var(--primary-color);
    }

    .mobile-nav-search-input {
        flex: 1;
        border: none;
        background: white;
        padding: 12px 16px;
        font-size: 0.9rem;
        outline: none;
        border: 1px solid var(--primary-color)
    }

    .mobile-nav-search-btn {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 0 16px;
        cursor: pointer;
        transition: var(--transition);
    }

    .mobile-nav-search-btn:hover {
        background: var(--primary-dark);
    }

    /* Mobile Navigation */
    .mobile-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .mobile-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        color: white;
        text-decoration: none;
        border-radius: 12px;
        transition: var(--transition);
        font-weight: 600;
        border: 2px solid transparent;
    }

    .mobile-nav-item:hover {
        background: rgba(231, 106, 4, 0.1);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .mobile-nav-item.active {
        background: var(--primary-color);
        color: white;
        transform: translateX(5px);
    }

    .mobile-nav-item i {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }
    .mobile-contact-item span {
        color : white ;
    }
    .mobile-contact-item a {
        color : white ;
    }

    /* Mobile Contact Section */
    .mobile-contact-section {
        background: var(--bg-light);
        padding: 20px;
        border-radius: var(--border-radius);
    }

    .mobile-contact-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .mobile-contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-dark);
        font-size: 0.85rem;
    }

    .mobile-contact-item i {
        color: var(--primary-color);
        width: 16px;
    }

    .mobile-contact-item a {
        color: white;
        text-decoration: none;
        transition: var(--transition-fast);
    }

    .mobile-contact-item a:hover {
        color: var(--primary-color);
    }

    /* Mobile Language Selector Container */
.mobile-language-selector {
    display: flex;
    gap: 10px;
    padding: 10px 0;
}

/* الحالة العادية: الزر غير مفعل (نص وأيقونة باللون الأبيض) */
.mobile-lang-option {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border: 2px solid rgba(255, 255, 255, 0.3); /* حدود بيضاء شفافة قليلاً */
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    background: transparent;
    transition: all 0.3s ease;
}

/* ضمان أن النص والأيقونة باللون الأبيض في الحالة العادية */
.mobile-lang-option span,
.mobile-lang-option i,
.mobile-lang-option svg {
    color: white !important;
    fill: white;
}

/* الحالة المفعلة: عند ضغط الزر (خلفية بيضاء، نص وأيقونة باللون الأخضر الغامق) */
.mobile-lang-option.active {
    background: white !important;
    border-color: white !important; /* لجعل الزر يبدو كقطعة واحدة بيضاء */
}

/* تغيير لون النص والأيقونة للأخضر عند التفعيل */
.mobile-lang-option.active span,
.mobile-lang-option.active i,
.mobile-lang-option.active svg {
    color: #144734ff !important;
    fill: #144734ff;
}

/* تأثير التمرير (Hover) */
.mobile-lang-option:hover {
    transform: translateY(-2px);
    border-color: white;
    background: rgba(255, 255, 255, 0.1); /* تعتيم بسيط عند التمرير */
}

/* منع تداخل ألوان الروابط الافتراضية */
.mobile-lang-option:focus, 
.mobile-lang-option:active {
    text-decoration: none;
    outline: none;
}
    /* ===== FLOATING BUTTONS ===== */
    .floating-whatsapp {
        position: fixed;
        bottom: 25px;
        left: 25px;
        z-index: 1000;
    }

    .whatsapp-float {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: #25D366;
        color: white;
        border-radius: 50%;
        text-decoration: none;
        font-size: 1.8rem;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        transition: var(--transition);
        animation: pulse 2s infinite;
    }

    .whatsapp-float:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
        animation: none;
    }

    .scroll-to-top {
        position: fixed;
        bottom: 25px;
        left : 25px;
        z-index: 1000;
    }

    .scroll-top-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.2rem;
        box-shadow: var(--shadow);
        transition: var(--transition);
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
    }

    .scroll-top-btn.show {
        opacity: 1;
        visibility: visible;
    }

    .scroll-top-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    /* ===== OVERLAY ===== */
    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        z-index: 9997;
        display: none;
    }

    .mobile-overlay.active {
        display: block;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes pulse {
        0% {
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        }
        50% {
            box-shadow: 0 4px 25px rgba(37, 211, 102, 0.7);
        }
        100% {
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 1200px) {
        .nav-container {
            gap: 20px;
        }
        
        .nav-link {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        
        .search-section {
            max-width: 400px;
        }
    }

    @media (max-width: 992px) {
        .top-bar {
            display: none;
        }
        
        .nav-menu,
        .desktop-search,
        .language-selector {
            display: none;
        }
        
        .mobile-search-toggle,
        .mobile-menu-toggle {
            display: flex;
        }
        
        .nav-container {
            padding: 12px 0;
        }
        
        .logo-image img {
            width: 45px;
            height: 45px;
        }
        
        .brand-name {
            font-size: 1.2rem;
        }
        
        .brand-tagline {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 768px) {
        .nav-container {
            gap: 15px;
        }
        
        .logo {
            gap: 10px;
        }
        
        .logo-image img {
            width: 40px;
            height: 40px;
        }
        
        .brand-name {
            font-size: 1.1rem;
        }
        
        .mobile-menu {
            width: 280px;
        }
        
        .floating-whatsapp {
            bottom: 20px;
            left: 20px;
        }
        
        .scroll-to-top {
            bottom: 20px;
            left : 20px;
        }
        
        .whatsapp-float {
            width: 55px;
            height: 55px;
            font-size: 1.6rem;
        }
        
        .scroll-top-btn {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .main-nav{
            height: 75px;
            
        }
        .nav-container {
            padding: 10px 0;
            display : flex;
            align-items : center;
        }
        
        .logo-image img {
            width: 35px;
            height: 35px;
        }
        
        .brand-name {
            font-size: 1rem;
        }
        
        .brand-tagline {
            display: none;
        }
        
        .mobile-menu {
            width: 100%;
        }
        
        .search-modal-content {
            margin: 20px;
            padding: 25px;
        }
    }

    @media (max-width: 480px) {
        
        
        .mobile-search-toggle,
        .mobile-menu-toggle {
            width: 40px;
            height: 40px;
        }
    }

    /* ===== RTL SUPPORT ===== */
    [dir="rtl"] .logo {
        flex-direction: row-reverse;
    }

    [dir="rtl"] .search-btn {
        right: auto;
        left: 6px;
    }

    [dir="rtl"] .lang-menu {
        right: auto;
        left: 0;
    }

    [dir="rtl"] .lang-option i {
        margin-left: 0;
        margin-right: auto;
    }

    [dir="rtl"] .mobile-nav-item:hover,
    [dir="rtl"] .mobile-nav-item.active {
        transform: translateX(-5px);
    }

    [dir="rtl"] .dropdown-item:hover {
        transform: translateX(-5px);
    }

    [dir="rtl"] .floating-whatsapp {
        left: auto;
        right: 25px;
    }

    [dir="rtl"] .scroll-to-top {
        right: auto;
        left: 25px;
    }

    /* ===== SCROLL BEHAVIOR ===== */
    .header.scrolled .main-nav {
        box-shadow: var(--shadow-lg);
        background: rgba(255, 255, 255, 0.98);
    }

    /* ===== ACCESSIBILITY ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Focus styles */
    .nav-link:focus,
    .search-btn:focus,
    .lang-toggle:focus,
    .mobile-search-toggle:focus,
    .mobile-menu-toggle:focus,
    .mobile-nav-item:focus,
    .whatsapp-float:focus,
    .scroll-top-btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* منع التمرير عندما تكون القائمة مفتوحة */
    body.menu-open {
        overflow: hidden;
    }
    </style>
</head>

<body>
<header id="header" class="header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container-fluid">
            <div class="top-bar-content">
                <!-- Contact Info -->
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="bi bi-clock"></i>
                        <span><?= isset($contact_boxData[3]['value']) ? $contact_boxData[3]['value'] : 'السبت - الخميس: 8ص - 10م' ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?= isset($contact_boxData[2]['value']) ? $contact_boxData[2]['value'] : 'info@ruknalamasy.com' ?>">
                            <?= isset($contact_boxData[2]['value']) ? $contact_boxData[2]['value'] : 'info@ruknalamasy.com' ?>
                        </a>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-phone"></i>
                        <a href="tel:<?= isset($contact_boxData[1]['value']) ? trim($contact_boxData[1]['value']) : '+966500000000' ?>">
                            <?= isset($contact_boxData[1]['value']) ? $contact_boxData[1]['value'] : '+966 50 000 0000' ?>
                        </a>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="social-links">
                            <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
                                <a href="https://x.com/<?= $contactData[0]['twitter'] ?>" class="twitter" target="_blank" title="Twitter">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
                                <a href="https://facebook.com/<?= $contactData[0]['facebook'] ?>" class="facebook" target="_blank" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
                                <a href="https://instagram.com/<?= $contactData[0]['instagram'] ?>" class="instagram" target="_blank" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['linkedin']) && !empty($contactData[0]['linkedin'])): ?>
                                <a href="https://linkedin.com/in/<?= $contactData[0]['linkedin'] ?>" class="linkedin" target="_blank" title="LinkedIn">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
                                <a href="https://www.youtube.com/<?= $contactData[0]['youtube'] ?>" class="youtube" target="_blank" title="YouTube">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
                                <a href="https://wa.me/<?= $contactData[0]['whatsapp'] ?>" class="whatsapp" target="_blank" title="WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                        </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <div class="container-fluid">
            <div class="nav-container">
                <!-- Logo -->
                <div class="logo-section">
                    <a href="index.php" class="logo">
                        <div class="logo-image">
                            <img src="assets/img/logo.png" alt="Rukn Alamasy" 
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiByeD0iMTAiIGZpbGw9IiNlNzZhMDQiLz4KPHN2ZyB4PSIxMiIgeT0iMTIiIHdpZHRoPSIyNiIgaGVpZ2h0PSIyNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiPgo8cGF0aCBkPSJNMTIgMkM2LjQ4IDIgMiA2LjQ4IDIgMTJzNC40OCAxMCAxMCAxMCAxMC00LjQ4IDEwLTEwUzE3LjUyIDIgMTIgMnpNMTIgMjBsLTMtMyAyLTcgNyA1LTIgN3oiLz4KPC9zdmc+Cjwvc3ZnPg=='">
                        </div>
                        <div class="logo-text">
                            <span class="brand-name">Rukn Alamasy</span>
                            <span class="brand-tagline"><?= $t['description'] ?></span>
                        </div>
                    </a>
                </div>

                <!-- Search Section -->
                <div class="search-section">
                    <!-- Desktop Search -->
                    <div class="desktop-search">
                        <form class="search-form" action="search.php" method="GET">
                            <div class="search-box">
                                <input type="text" 
                                       class="search-input" 
                                       name="q" 
                                       placeholder="<?= $t['search'] ?>" 
                                       value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                                <button class="search-btn" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Mobile Search Icon -->
                    
                </div>

                <!-- Navigation & Actions -->
                <div class="nav-actions">
                    <!-- Main Navigation -->
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link <?= ($current_page == 'index.php' || $current_page == '' || $current_page == '/') ? 'active' : '' ?>">
                                <i class="bi bi-house"></i>
                                <span><?= $t['home'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="about.php" class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>">
                                <i class="bi bi-info-circle"></i>
                                <span><?= $t['about'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="services.php" class="nav-link dropdown-toggle <?= ($current_page == 'services.php') ? 'active' : '' ?>">
                                <i class="bi bi-gear"></i>
                                <span><?= $t['services'] ?></span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="services.php#web"><?= ($lang == 'ar') ? 'تطوير الويب' : 'Web Development' ?></a>
                                <a class="dropdown-item" href="services.php#mobile"><?= ($lang == 'ar') ? 'تطبيقات الجوال' : 'Mobile Apps' ?></a>
                                <a class="dropdown-item" href="services.php#seo"><?= ($lang == 'ar') ? 'تحسين محركات البحث' : 'SEO' ?></a>
                                <a class="dropdown-item" href="services.php#marketing"><?= ($lang == 'ar') ? 'التسويق الرقمي' : 'Digital Marketing' ?></a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="products.php" class="nav-link <?= ($current_page == 'products.php') ? 'active' : '' ?>">
                                <i class="bi bi-box"></i>
                                <span><?= $t['products'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="contact.php" class="nav-link <?= ($current_page == 'contact.php') ? 'active' : '' ?>">
                                <i class="bi bi-telephone"></i>
                                <span><?= $t['contact'] ?></span>
                            </a>
                        </li>
                    </ul>

                    <!-- Language Selector -->
                    <div class="language-selector">
                        <div class="lang-dropdown">
                            <button class="lang-toggle">
                                <?php if ($lang == 'ar'): ?>
                                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                                        <rect width="24" height="18" fill="#006C35"/>
                                        <text x="12" y="12" text-anchor="middle" fill="#FFFFFF" font-size="8" font-weight="bold">ﷲ</text>
                                    </svg>
                                <?php else: ?>
                                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                                        <rect width="24" height="1.38" fill="#B22234" y="0"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="2.76"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="5.52"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="8.28"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="11.04"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="13.8"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="16.56"/>
                                        <rect width="9.6" height="9.66" fill="#3C3B6E"/>
                                    </svg>
                                <?php endif; ?>
                                <span class="lang-text"><?= ($lang == 'ar') ? $t['arabic'] : $t['english'] ?></span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="lang-menu">
                                <a href="?lang=ar" class="lang-option <?= ($lang == 'ar') ? 'active' : '' ?>">
                                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                                        <rect width="24" height="18" fill="#006C35"/>
                                        <text x="12" y="12" text-anchor="middle" fill="#FFFFFF" font-size="8" font-weight="bold">ﷲ</text>
                                    </svg>
                                    <span>العربية</span>
                                    <?php if ($lang == 'ar'): ?>
                                        <i class="bi bi-check"></i>
                                    <?php endif; ?>
                                </a>
                                <a href="?lang=en" class="lang-option <?= ($lang == 'en') ? 'active' : '' ?>">
                                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                                        <rect width="24" height="1.38" fill="#B22234" y="0"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="2.76"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="5.52"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="8.28"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="11.04"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="13.8"/>
                                        <rect width="24" height="1.38" fill="#B22234" y="16.56"/>
                                        <rect width="9.6" height="9.66" fill="#3C3B6E"/>
                                    </svg>
                                    <span>English</span>
                                    <?php if ($lang == 'en'): ?>
                                        <i class="bi bi-check"></i>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle">
                        <span class="menu-bar"></span>
                        <span class="menu-bar"></span>
                        <span class="menu-bar"></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Search Modal -->
    <div class="mobile-search-modal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <h3><?= $t['search'] ?></h3>
                <button class="close-search-modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form class="mobile-search-form" action="search.php" method="GET">
                <div class="search-input-group">
                    <input type="text" 
                           class="mobile-search-input" 
                           name="q" 
                           placeholder="<?= $t['search'] ?>..." 
                           value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                    <button class="mobile-search-btn" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <div class="mobile-logo">
                <img src="assets/img/logo.png" alt="Rukn Alamasy" class="mobile-logo-img">
                <span class="mobile-brand">Rukn Alamasy</span>
            </div>
            <button class="mobile-menu-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mobile-menu-content">
            <!-- Mobile Search -->
            <div class="mobile-search-container">
                <form class="mobile-nav-search-form" action="search.php" method="GET">
                    <div class="mobile-nav-search-box">
                        <input type="text" 
                               class="mobile-nav-search-input" 
                               name="q" 
                               placeholder="<?= $t['search'] ?>..." 
                               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                        <button class="mobile-nav-search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Navigation -->
            <nav class="mobile-nav">
                <a href="./" class="mobile-nav-item <?= ($current_page == 'index.php' || $current_page == '' || $current_page == '/') ? 'active' : '' ?>">
                    <i class="bi bi-house"></i>
                    <span><?= $t['home'] ?></span>
                </a>
                <a href="about.php" class="mobile-nav-item <?= ($current_page == 'about.php') ? 'active' : '' ?>">
                    <i class="bi bi-info-circle"></i>
                    <span><?= $t['about'] ?></span>
                </a>
                <a href="services.php" class="mobile-nav-item <?= ($current_page == 'services.php') ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    <span><?= $t['services'] ?></span>
                </a>
                <a href="products.php" class="mobile-nav-item <?= ($current_page == 'products.php') ? 'active' : '' ?>">
                    <i class="bi bi-box"></i>
                    <span><?= $t['products'] ?></span>
                </a>
                <a href="contact.php" class="mobile-nav-item <?= ($current_page == 'contact.php') ? 'active' : '' ?>">
                    <i class="bi bi-telephone"></i>
                    <span><?= $t['contact'] ?></span>
                </a>
            </nav>

            <!-- Mobile Contact Info -->
            <div class="mobile-contact-section">
                <div class="mobile-contact-info">
                    <div class="mobile-contact-item">
                        <i class="bi bi-clock"></i>
                        <span><?= isset($contact_boxData[3]['value']) ? $contact_boxData[3]['value'] : 'السبت - الخميس: 8ص - 10م' ?></span>
                    </div>
                    <div class="mobile-contact-item">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?= isset($contact_boxData[2]['value']) ? $contact_boxData[2]['value'] : 'info@ruknalamasy.com' ?>">
                            <?= isset($contact_boxData[2]['value']) ? $contact_boxData[2]['value'] : 'info@ruknalamasy.com' ?>
                        </a>
                    </div>
                    <div class="mobile-contact-item">
                        <i class="bi bi-phone"></i>
                        <a href="tel:<?= isset($contact_boxData[1]['value']) ? trim($contact_boxData[1]['value']) : '+966500000000' ?>">
                            <?= isset($contact_boxData[1]['value']) ? $contact_boxData[1]['value'] : '+966 50 000 0000' ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Language Selector -->
            <div class="mobile-language-selector">
                <a href="?lang=ar" class="mobile-lang-option <?= ($lang == 'ar') ? 'active' : '' ?>">
                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                        <rect width="24" height="18" fill="#006C35"/>
                        <text x="12" y="12" text-anchor="middle" fill="#FFFFFF" font-size="8" font-weight="bold">ﷲ</text>
                    </svg>
                    <span>العربية</span>
                </a>
                <a href="?lang=en" class="mobile-lang-option <?= ($lang == 'en') ? 'active' : '' ?>">
                    <svg class="flag-icon" width="20" height="15" viewBox="0 0 24 18">
                        <rect width="24" height="1.38" fill="#B22234" y="0"/>
                        <rect width="24" height="1.38" fill="#B22234" y="2.76"/>
                        <rect width="24" height="1.38" fill="#B22234" y="5.52"/>
                        <rect width="24" height="1.38" fill="#B22234" y="8.28"/>
                        <rect width="24" height="1.38" fill="#B22234" y="11.04"/>
                        <rect width="24" height="1.38" fill="#B22234" y="13.8"/>
                        <rect width="24" height="1.38" fill="#B22234" y="16.56"/>
                        <rect width="9.6" height="9.66" fill="#3C3B6E"/>
                    </svg>
                    <span>English</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<!-- Floating WhatsApp Button -->
<div class="floating-whatsapp">
    <a href="https://wa.me/<?= isset($contactData[0]['whatsapp']) ? $contactData[0]['whatsapp'] : '+966500000000' ?>" target="_blank" class="whatsapp-float">
        <i class="bi bi-whatsapp"></i>
    </a>
</div>

<!-- Scroll to Top Button -->


<script>
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.header');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const mobileSearchToggle = document.querySelector('.mobile-search-toggle');
    const mobileSearchModal = document.querySelector('.mobile-search-modal');
    const closeSearchModal = document.querySelector('.close-search-modal');
    const scrollTopBtn = document.querySelector('.scroll-top-btn');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    // دالة فتح/إغلاق القائمة الجانبية
    function toggleMobileMenu() {
        const isOpening = !mobileMenu.classList.contains('active');
        
        mobileMenu.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        document.body.classList.toggle('menu-open', isOpening);
        
        // Animate menu bars
        const bars = mobileMenuToggle.querySelectorAll('.menu-bar');
        if (mobileMenu.classList.contains('active')) {
            bars[0].style.transform = 'rotate(45deg) translate(6px, 6px)';
            bars[1].style.opacity = '0';
            bars[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
        } else {
            bars[0].style.transform = 'none';
            bars[1].style.opacity = '1';
            bars[2].style.transform = 'none';
        }
    }

    // دالة فتح/إغلاق نافذة البحث
    function toggleSearchModal() {
        const isOpening = !mobileSearchModal.classList.contains('active');
        
        mobileSearchModal.classList.toggle('active');
        document.body.classList.toggle('menu-open', isOpening);
        
        if (mobileSearchModal.classList.contains('active')) {
            setTimeout(() => {
                mobileSearchModal.querySelector('.mobile-search-input').focus();
            }, 300);
        }
    }

    // Event Listeners
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', toggleMobileMenu);
    }

    if (mobileSearchToggle) {
        mobileSearchToggle.addEventListener('click', toggleSearchModal);
    }

    if (closeSearchModal) {
        closeSearchModal.addEventListener('click', toggleSearchModal);
    }

    // Close modals with overlay
    mobileOverlay.addEventListener('click', function() {
        if (mobileMenu.classList.contains('active')) {
            toggleMobileMenu();
        }
        if (mobileSearchModal.classList.contains('active')) {
            toggleSearchModal();
        }
    });

    // Scroll to top functionality
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            header.classList.add('scrolled');
            scrollTopBtn.classList.add('show');
        } else {
            header.classList.remove('scrolled');
            scrollTopBtn.classList.remove('show');
        }
    });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Search form validation
    const searchForms = document.querySelectorAll('.search-form, .mobile-search-form, .mobile-nav-search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="q"]');
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
                searchInput.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    searchInput.style.animation = '';
                }, 500);
            }
        });
    });

    // Close mobile menu when clicking on links
    document.querySelectorAll('.mobile-nav-item, .mobile-lang-option').forEach(item => {
        item.addEventListener('click', function() {
            if (mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
            }
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
            }
            if (mobileSearchModal.classList.contains('active')) {
                toggleSearchModal();
            }
        }
    });

    // Touch device optimizations
    function isTouchDevice() {
        return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    }

    if (isTouchDevice()) {
        document.body.classList.add('touch-device');
        
        // Add touch-specific improvements
        const navLinks = document.querySelectorAll('.nav-link, .mobile-nav-item');
        navLinks.forEach(link => {
            link.style.cursor = 'pointer';
        });
    }

    // Add smooth animations for mobile nav items
    const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
    mobileNavItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.style.animation = 'slideIn 0.3s ease forwards';
    });
});

window.addEventListener('scroll', function() {
    const nav = document.querySelector('.main-nav');
    const scrollPosition = window.scrollY;
    
    if (scrollPosition > 80) { // يبدأ التأثير بعد 80 بكسل
        nav.classList.add('scrolled');
        // إضافة أنيميشن بسيط للعناصر الداخلية
        document.querySelectorAll('.nav-item').forEach((el, index) => {
            el.style.transitionDelay = `${index * 0.05}s`;
        });
    } else {
        nav.classList.remove('scrolled');
    }
});


</script>
</body>
</html>