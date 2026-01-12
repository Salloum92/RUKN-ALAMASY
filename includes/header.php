<?php
// تضمين ملفات النظام الأساسية
require_once 'config.php';

// نظام الترجمة
$translations = [
    'ar' => [
        'home' => 'الرئيسية',
        'about' => 'من نحن',
        'services' => 'خدماتنا',
        'products' => 'منتجاتنا',
        'contact' => 'اتصل بنا',
        'arabic' => 'العربية',
        'english' => 'الإنجليزية',
        'description' => 'أفضل منتجات الأمن و السلام',
        'search_placeholder' => 'ابحث هنا...'
    ],
    'en' => [
        'home' => 'Home',
        'about' => 'About Us',
        'services' => 'Services',
        'products' => 'Products',
        'contact' => 'Contact',
        'arabic' => 'Arabic',
        'english' => 'English',
        'description' => 'Premium Products & Services',
        'search_placeholder' => 'Search here...'
    ]
];

// تحديد اللغة
$lang = $_SESSION['lang'] ?? 'ar';
$t = $translations[$lang];

// استعلامات قاعدة البيانات - بنفس طريقة الفوتر
$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

// البحث عن بيانات الاتصال بنفس طريقة الفوتر
$footer_contact_data = [
    'location' => ['value' => '', 'icon' => 'bi bi-geo-alt', 'type' => 'location'],
    'phone' => ['value' => '', 'icon' => 'bi bi-telephone', 'type' => 'phone'],
    'email' => ['value' => '', 'icon' => 'bi bi-envelope', 'type' => 'email'],
    'working_hours' => ['value' => '', 'icon' => 'bi bi-clock', 'type' => 'working_hours']
];

foreach ($contact_boxData as $item) {
    if (isset($item['type'])) {
        $type = $item['type'];
        if (isset($footer_contact_data[$type]) && !empty($item['value'])) {
            $footer_contact_data[$type]['value'] = $item['value'];
            if (!empty($item['icon'])) {
                $footer_contact_data[$type]['icon'] = $item['icon'];
            }
        }
    }
    
    if (isset($item['title']) && isset($item['value']) && !empty($item['value'])) {
        $title = strtolower($item['title']);
        
        if (strpos($title, 'موقع') !== false || strpos($title, 'location') !== false) {
            $footer_contact_data['location']['value'] = $item['value'];
        } elseif (strpos($title, 'هاتف') !== false || strpos($title, 'phone') !== false || strpos($title, 'tel') !== false) {
            $footer_contact_data['phone']['value'] = $item['value'];
        } elseif (strpos($title, 'بريد') !== false || strpos($title, 'email') !== false || strpos($title, 'mail') !== false) {
            $footer_contact_data['email']['value'] = $item['value'];
        } elseif (strpos($title, 'ساعات') !== false || strpos($title, 'working') !== false || strpos($title, 'hours') !== false) {
            $footer_contact_data['working_hours']['value'] = $item['value'];
        }
    }
}

// استخدام بيانات الاتصال من جدول contact إذا لم توجد في contact_box
if (empty($footer_contact_data['phone']['value']) && isset($contactData[0]['phone'])) {
    $footer_contact_data['phone']['value'] = $contactData[0]['phone'];
}

if (empty($footer_contact_data['email']['value']) && isset($contactData[0]['email'])) {
    $footer_contact_data['email']['value'] = $contactData[0]['email'];
}

// استرجاع بيانات الموقع والرابط - بنفس طريقة الفوتر
$google_maps_url = '';
$location_address = '';

$google_maps_item = $query->select('contact_box', '*', "WHERE id = 1")[0] ?? null;

if ($google_maps_item && !empty($google_maps_item['value'])) {
    $google_maps_url = $google_maps_item['value'];
    $location_address = $google_maps_item['label'] ?? ($lang == 'ar' ? 'احصل على الاتجاهات' : 'Get Directions');
} else {
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'google_maps' && !empty($item['value'])) {
            $google_maps_url = $item['value'];
            $location_address = $item['label'] ?? ($lang == 'ar' ? 'احصل على الاتجاهات' : 'Get Directions');
            break;
        }
    }
}

if (empty($google_maps_url)) {
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'location' && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
        if (isset($item['title']) && stripos($item['title'], 'موقع') !== false && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
    }
    
    if (empty($location_address) && isset($contactData[0]['location'])) {
        $location_address = $contactData[0]['location'];
    }
    
    if (!empty($location_address)) {
        $encoded_address = urlencode($location_address);
        $google_maps_url = "https://www.google.com/maps/search/?api=1&query=" . $encoded_address;
    }
}

if (!empty($google_maps_url) && empty($location_address)) {
    $location_address = $lang == 'ar' ? 'احصل على الاتجاهات' : 'Get Directions';
}

// تعيين المتغيرات للاستخدام في HTML
$phone_number = $footer_contact_data['phone']['value'];
$email_address = $footer_contact_data['email']['value'];
$display_location = !empty($location_address) ? $location_address : ($lang == 'ar' ? 'الرياض' : 'Riyadh');

// تحديد الصفحة الحالية
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= ($lang == 'ar') ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rukn Alamasy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #e76a04;
        --primary-dark: #cc5f03;
        --secondary-color: #e76a04;
        --secondary-dark: #e76a04;
        --dark-color: #144734ff;
        --dark-light: rgb(30, 91, 72);
        --light-color: #f8f9fa;
        --text-dark: #2c3e50;
        --text-light: #6c757d;
        --white: #ffffff;
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        --transition-fast: all 0.3s ease;
        --border-radius: 8px;
        --border-radius-lg: 15px;
        --border-color: #e9ecef;
        --bg-light: #f8f9fa;
        --bg-white: #ffffff;
        --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

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
        z-index: 5;
    }

    .top-bar {
        background: linear-gradient(135deg, var(--dark-color), var(--dark-light));
        color: var(--light-color);
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
        z-index: 100;
        display: flex;
        align-items: center;
        height: 60px;
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
        display: flex !important;
        align-items: center;
        gap: 8px;
        color: #bdc3c7;
        font-size: 0.85rem;
        transition: var(--transition-fast);
        cursor: pointer;
    }

    .contact-item i {
        color: var(--primary-color);
        font-size: 0.9rem;
        transition: var(--transition-fast);
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

    .contact-item:hover i {
        transform: scale(1.1);
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

    .main-nav {
        position: relative;
        width: 100%;
        height: 80px;
        min-height: 80px;
        z-index: 1000;
        background: #ffffff;
        padding: 15px 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
    }

    .main-nav.scrolled {
        position: fixed !important;
        top: 0;
        padding: 8px 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px) saturate(180%);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border-bottom: 1px solid rgba(20, 71, 52, 0.1);
    }

    .main-nav.scrolled::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #144734, #e76a04);
    }

    body.nav-fixed-active {
        padding-top: 80px;
    }

    .nav-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
        gap: 30px;
        transition: padding 0.3s ease;
    }

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
        width: 70px !important;
        height: 70px !important;
        object-fit: cover;
        box-shadow: none !important;
        transition: var(--transition);
        border: none !important;
    }

    .logo:hover .logo-image img {
        transform: scale(1.05);
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
        color: #144734ff;
        font-size: 0.6rem;
        font-weight: 400;
        margin-top: 1px;
        text-align: center;
    }

    .search-section {
        flex: 1;
        max-width: 500px;
        position: relative;
        color: var(--primary-color);
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

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: space-between;
        width: 75%;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 5px;
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
        background: white;
        border: 2px solid var(--primary-color);
        border-radius: 25px;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .lang-toggle:hover {
        background: var(--primary-color);
        color: white;
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
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-xl);
        padding: 8px;
        min-width: 160px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: var(--transition);
        z-index: 1000;
        border: 1px solid var(--border-color);
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
        color: var(--text-dark);
        text-decoration: none;
        border-radius: 8px;
        transition: var(--transition-fast);
        font-weight: 500;
    }

    .lang-option:hover {
        background: rgba(231, 106, 4, 0.1);
        color: var(--primary-color);
    }

    .lang-option.active {
        background: var(--dark-color);
        color: var(--primary-color);
    }

    .lang-option i {
        margin-left: auto;
        color: var(--primary-color);
    }

    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        gap: 4px;
        background: white;
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        padding: 10px;
        cursor: pointer;
        transition: var(--transition);
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

    .mobile-menu {
        position: fixed;
        top: 0;
        left: -100%;
        width: 320px;
        height: 100vh;
        background: var(--dark-color);
        box-shadow: var(--shadow-xl);
        z-index: 9998;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .mobile-menu.active {
        left: 0;
    }

    .mobile-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
    }

    .mobile-logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-logo-img {
        width: 80px !important;
        height: 80px !important;
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
        color: white;
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
        margin-bottom: 50px;
    }

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

    .mobile-contact-section {
        background: rgba(255, 255, 255, 0.1);
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
        color: white;
        font-size: 0.85rem;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .mobile-contact-item i {
        color: var(--primary-color);
        width: 16px;
        transition: var(--transition-fast);
    }

    .mobile-contact-item a {
        color: white;
        text-decoration: none;
        transition: var(--transition-fast);
    }

    .mobile-contact-item a:hover {
        color: var(--primary-color);
    }

    .mobile-contact-item:hover {
        color: var(--primary-color);
    }

    .mobile-contact-item:hover i {
        transform: scale(1.1);
    }

    .mobile-language-selector {
        display: flex;
        gap: 10px;
        padding: 10px 0;
    }

    .mobile-lang-option {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        background: transparent;
        transition: all 0.3s ease;
    }

    .mobile-lang-option span,
    .mobile-lang-option i,
    .mobile-lang-option svg {
        color: white !important;
        fill: white;
    }

    .mobile-lang-option.active {
        background: white !important;
        border-color: white !important;
    }

    .mobile-lang-option.active span,
    .mobile-lang-option.active i,
    .mobile-lang-option.active svg {
        color: #144734ff !important;
        fill: #144734ff;
    }

    .mobile-lang-option:hover {
        transform: translateY(-2px);
        border-color: white;
        background: rgba(255, 255, 255, 0.1);
    }

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

    .scroll-top {
        position: fixed;
        bottom: 15px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 15px 40px rgba(231, 106, 4, 0.4);
        z-index: 999;
        transition: all 0.4s ease;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .scroll-top:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 25px 50px rgba(231, 106, 4, 0.6);
    }

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

    @media (max-width: 992px) {
        .top-bar {
            display: none;
        }

        .nav-menu,
        .desktop-search,
        .language-selector {
            display: none;
        }

        .mobile-menu-toggle {
            display: flex;
        }

        .nav-container {
            padding: 12px 0;
        }

        .logo-image img {
            width: 80px;
            height: 80px;
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

        .logo-image img {
            width: 80px;
            height: 80px;
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

        .whatsapp-float {
            width: 55px;
            height: 55px;
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .main-nav {
            min-height: 75px;
        }

        .nav-container {
            padding: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .nav-actions {
            width: auto;
        }
        
        .mobile-nav {
            gap: 0;
        }

        .logo-image img {
            width: 75px !important;
        }

        .brand-name {
            font-size: 1rem;
        }

        .brand-tagline {
            font-size: 10px !important;
            color: #144734ff;
        }

        .mobile-menu {
            width: 80%;
        }
        
        .mobile-menu.active .brand-tagline {
            display: none;
        }
    }

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
       
         right: auto;
        left: 20px;
    }

    [dir="rtl"] .scroll-top {
        left: auto;
        right: 25px;
    }

    /* إضافة قسم لـ LTR (اللغة الإنجليزية) */
    [dir="ltr"] .floating-whatsapp {
        
        right: 20px;
        left: auto;
    }
    
    [dir="ltr"] .scroll-top {
        left: 25px;
        right: auto;
    }
   
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
                    <?php if (!empty($google_maps_url)): ?>
                    <div class="contact-item google-maps-icon" onclick="window.open('<?= htmlspecialchars($google_maps_url) ?>', '_blank')" title="<?= htmlspecialchars($location_address) ?>">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= htmlspecialchars($display_location) ?></span>
                    </div>
                    <?php elseif (!empty($location_address)): ?>
                    <div class="contact-item google-maps-icon" onclick="window.open('https://www.google.com/maps/search/?api=1&query=<?= urlencode($location_address) ?>', '_blank')" title="<?= htmlspecialchars($location_address) ?>">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= htmlspecialchars($display_location) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= $lang == 'ar' ? 'الرياض' : 'Riyadh' ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($email_address)): ?>
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?= htmlspecialchars($email_address) ?>">
                            <?= htmlspecialchars($email_address) ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <span><?= $lang == 'ar' ? 'البريد غير متوفر' : 'Email not available' ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($phone_number)): ?>
                    <div class="contact-item">
                        <i class="bi bi-phone"></i>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $phone_number)) ?>">
                            <?= htmlspecialchars($phone_number) ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="contact-item">
                        <i class="bi bi-phone"></i>
                        <span><?= $lang == 'ar' ? 'الهاتف غير متوفر' : 'Phone not available' ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Social Links -->
                <div class="social-links">
                    <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
                        <a href="<?= $contactData[0]['twitter'] ?>" class="social-link twitter" target="_blank" title="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
                        <a href="<?= $contactData[0]['facebook'] ?>" class="social-link facebook" target="_blank" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
                        <a href="<?= $contactData[0]['instagram'] ?>" class="social-link instagram" target="_blank" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['linkedin']) && !empty($contactData[0]['linkedin'])): ?>
                        <a href="<?= $contactData[0]['linkedin'] ?>" class="social-link linkedin" target="_blank" title="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
                        <a href="<?= $contactData[0]['youtube'] ?>" class="social-link youtube" target="_blank" title="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
                        <a href="<?= $contactData[0]['whatsapp'] ?>" class="social-link whatsapp" target="_blank" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($contactData[0]['telegram']) && !empty($contactData[0]['telegram'])): ?>
                        <a href="<?= $contactData[0]['telegram'] ?>" class="social-link telegram" target="_blank" title="Telegram">
                            <i class="bi bi-telegram"></i>
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
                        <li class="nav-item">
                            <a href="services.php" class="nav-link <?= ($current_page == 'services.php') ? 'active' : '' ?>">
                                <i class="bi bi-gear"></i>
                                <span><?= $t['services'] ?></span>
                            </a>
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
                                    <span class="lang-text">العربية</span>
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
                                    <span class="lang-text">English</span>
                                <?php endif; ?>
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

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <div class="mobile-logo">
                <img src="assets/img/logo.png" alt="Rukn Alamasy" class="mobile-logo-img">
                <span class="mobile-brand">Rukn Alamasy</span>
                <span class="brand-tagline"><?= $t['description'] ?></span>
            </div>
            <button class="mobile-menu-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mobile-menu-content">
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
                    <?php if (!empty($google_maps_url)): ?>
                    <div class="mobile-contact-item google-maps-icon" onclick="window.open('<?= htmlspecialchars($google_maps_url) ?>', '_blank')" title="<?= htmlspecialchars($location_address) ?>">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= htmlspecialchars($display_location) ?></span>
                    </div>
                    <?php elseif (!empty($location_address)): ?>
                    <div class="mobile-contact-item google-maps-icon" onclick="window.open('https://www.google.com/maps/search/?api=1&query=<?= urlencode($location_address) ?>', '_blank')" title="<?= htmlspecialchars($location_address) ?>">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= htmlspecialchars($display_location) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="mobile-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= $lang == 'ar' ? 'الرياض' : 'Riyadh' ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($email_address)): ?>
                    <div class="mobile-contact-item">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:<?= htmlspecialchars($email_address) ?>">
                            <?= htmlspecialchars($email_address) ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="mobile-contact-item">
                        <i class="bi bi-envelope"></i>
                        <span><?= $lang == 'ar' ? 'البريد غير متوفر' : 'Email not available' ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($phone_number)): ?>
                    <div class="mobile-contact-item">
                        <i class="bi bi-phone"></i>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $phone_number)) ?>">
                            <?= htmlspecialchars($phone_number) ?>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="mobile-contact-item">
                        <i class="bi bi-phone"></i>
                        <span><?= $lang == 'ar' ? 'الهاتف غير متوفر' : 'Phone not available' ?></span>
                    </div>
                    <?php endif; ?>
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
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Overlay -->
<div class="mobile-overlay"></div>

<!-- Floating WhatsApp Button -->
<?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
<div class="floating-whatsapp">
    <a href="<?= $contactData[0]['whatsapp'] ?>" class="whatsapp-float" target="_blank" title="WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
</div>
<?php endif; ?>

<!-- Scroll to Top Button -->
<a href="#" class="scroll-top" id="scroll-top">
    <i class="bi bi-arrow-up"></i>
</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.header');
    const mainNav = document.querySelector('.main-nav');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const scrollTopBtn = document.getElementById('scroll-top');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const googleMapsIcons = document.querySelectorAll('.google-maps-icon');
    const contactItems = document.querySelectorAll('.contact-item, .mobile-contact-item');

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

    // Close mobile menu when clicking on links
    document.querySelectorAll('.mobile-nav-item, .mobile-lang-option').forEach(item => {
        item.addEventListener('click', function() {
            if (mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
            }
        });
    });

    // Event Listeners
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', toggleMobileMenu);
    }

    // Close modals with overlay
    mobileOverlay.addEventListener('click', function() {
        if (mobileMenu.classList.contains('active')) {
            toggleMobileMenu();
        }
    });

    // إضافة تأثير hover لعناصر الاتصال
    contactItems.forEach(item => {
        const icon = item.querySelector('i');
        if (icon) {
            item.addEventListener('mouseenter', function() {
                icon.style.transform = 'scale(1.1)';
            });
            item.addEventListener('mouseleave', function() {
                icon.style.transform = 'scale(1)';
            });
        }
    });

    // Fixed navigation on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            mainNav.classList.add('scrolled');
            if (scrollTopBtn) {
                scrollTopBtn.style.display = 'flex';
            }
        } else {
            mainNav.classList.remove('scrolled');
            if (scrollTopBtn) {
                scrollTopBtn.style.display = 'none';
            }
        }
    });

    // Scroll to top functionality
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // إضافة تأثير النقر على أيقونات خرائط جوجل
    googleMapsIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
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
</script>
</body>
</html>