<?php
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'ar';
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header('Location: ' . $current_url);
    exit();
}

// استيراد الملفات المشتركة
include 'config.php';
if (!function_exists('getContactInfo')) {
    include_once 'contact_functions.php';
}

require_once 'lang.php';

// جلب البيانات باستخدام الدوال المشتركة
$contact_boxData = getContactInfo('box');
$contactData = getContactInfo('social');

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';

// بيانات الخريطة
$map_embed_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3623.676815482492!2d46.691563!3d24.766563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2efd000462b7cd%3A0x9d295b4e23aa8425!2z2LHYp9mGINin2YTYp9mE2K7Yp9mEINmE2YTZh9mE2K8g2KfZhNmF2YjZhNmE2Kkg2YTZh9mE2K8!5e0!3m2!1sar!2ssa!4v1700000000000";
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? 'اتصل بنا - ركن الأماسي' : 'Contact Us - Rukn Alamasy'; ?></title>
  <!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://rukn-alamasy.com.sa/contact.php">
<meta property="og:title" content="<?php echo ($lang == 'ar') ? 'ركن الأماسي - منتجات وخدمات متميزة' : 'Rukn Alamasy - Premium Products & Services'; ?>">
<meta property="og:description" content="<?php echo ($lang == 'ar') ? 'اكتشف منتجات وخدمات استثنائية مع ركن الأماسي' : 'Discover exceptional products and services with Rukn Alamasy'; ?>">
<meta property="og:image" content="https://drive.google.com/uc?export=view&id=1Hy5LOgYkjmZc7VJUkHDZO5InrXN52VVl">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo ($lang == 'ar') ? 'ركن الأماسي - منتجات وخدمات متميزة' : 'Rukn Alamasy - Premium Products & Services'; ?>">
<meta name="twitter:description" content="<?php echo ($lang == 'ar') ? 'اكتشف منتجات وخدمات استثنائية مع ركن الأماسي' : 'Discover exceptional products and services with Rukn Alamasy'; ?>">
<meta name="twitter:image" content="https://drive.google.com/uc?export=view&id=1Hy5LOgYkjmZc7VJUkHDZO5InrXN52VVl">

  <meta name="description" content="<?php echo ($lang == 'ar') ? 'تواصل مع ركن الأماسي للحصول على أفضل منتجات وخدمات الأمن والسلامة' : 'Contact Rukn Alamasy for the best security and safety products and services'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'اتصل بنا، تواصل، دعم، خدمة عملاء' : 'contact us, support, inquiry, customer service'; ?>">
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
  <!-- مكتبات CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  
  <!-- مكتبة particles.js للإبهار -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.css">
  
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #e76a04;
      --primary-dark: #d45f00;
      --secondary-color: #e76a04;
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
      --gradient-light: linear-gradient(135deg, #ffffff, #f8f9fa);
      --gradient-card: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.98));
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      background: #fefefe;
    }

    /* Hero Section */
    .contact-hero-section {
      height: 40vh;
      min-height: 300px;
      position: relative;
      overflow: hidden;
      background: var(--gradient-dark);
    }

    #particles-js-contact {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      pointer-events: none;
    }

    .contact-hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      color: var(--white);
      padding: 0 20px;
      margin-top: 3.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }

    .contact-hero-title {
      font-size: 4rem;
      font-weight: 900;
      margin-bottom: 25px;
      text-transform: uppercase;
      letter-spacing: 3px;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.3));
      animation: titleGlow 3s infinite alternate;
    }

    @keyframes titleGlow {
      0% { filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.3)); }
      100% { filter: drop-shadow(0 0 20px rgba(231, 106, 4, 0.5)); }
    }

    .contact-hero-subtitle {
      font-size: 1.3rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
      line-height: 1.8;
    }

    /* Contact Info Section - Redesigned */
    .contact-info-section {
      padding: 80px 0;
      background: white;
      position: relative;
      overflow: hidden;
    }

    .contact-info-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-primary);
      z-index: 1;
    }

    #particles-js-contact-info {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 0;
      opacity: 0.3;
      pointer-events: none;
    }

    .contact-grid-container {
      position: relative;
      z-index: 2;
      padding: 0 15px;
    }

    /* Section Header */
    .section-header {
      text-align: center;
      margin-bottom: 60px;
      position: relative;
    }

    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--gradient-primary);
      color: white;
      padding: 12px 28px;
      border-radius: 50px;
      font-size: 0.95rem;
      font-weight: 700;
      margin-bottom: 20px;
      box-shadow: 0 10px 25px rgba(231, 106, 4, 0.3);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      transition: all 0.4s ease;
    }

    .section-badge:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.4);
    }

    .section-title {
      font-size: 2.8rem;
      font-weight: 900;
      color: var(--dark-color);
      margin-bottom: 20px;
      position: relative;
      display: inline-block;
      text-transform: capitalize;
      letter-spacing: 1px;
      line-height: 1.2;
    }
    .section-title-social{
       font-size: 2.8rem;
      font-weight: 900;
      color: #e76a04;
      margin-bottom: 20px;
      position: relative;
      display: inline-block;
      text-transform: capitalize;
      letter-spacing: 1px;
      line-height: 1.2;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: var(--gradient-primary);
      border-radius: 5px;
      animation: lineWidth 3s infinite alternate;
    }
    .section-title-social::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: var(--gradient-primary);
      border-radius: 5px;
      animation: lineWidth 3s infinite alternate;
    }

    @keyframes lineWidth {
      0% { width: 80px; }
      100% { width: 150px; }
    }

    .section-subtitle {
      font-size: 1.2rem;
      color: var(--text-light);
      max-width: 800px;
      margin: 25px auto 0;
      line-height: 1.7;
      font-weight: 400;
    }

    /* Advanced Grid System */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }

    /* Enhanced Contact Card */
    .contact-card {
      background: white;
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(231, 106, 4, 0.1);
      backdrop-filter: blur(10px);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      min-height: 380px;
    }

    .contact-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--gradient-primary);
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 0;
    }

    .contact-card:hover::before {
      opacity: 0.05;
    }

    .contact-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: var(--gradient-primary);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.5s ease;
    }

    .contact-card:hover::after {
      transform: scaleX(1);
    }

    .contact-card:hover {
      transform: translateY(-15px) scale(1.02);
      box-shadow: 0 30px 60px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.3);
    }

    .contact-card-content {
      position: relative;
      z-index: 1;
      width: 100%;
    }

    .contact-icon-wrapper {
      width: 90px;
      height: 90px;
      margin: 0 auto 25px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: var(--gradient-primary);
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.25);
      transition: all 0.6s ease;
    }

    .contact-card:hover .contact-icon-wrapper {
      transform: translateY(-10px) rotateY(360deg);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .contact-icon {
      font-size: 2.8rem;
      color: white;
      transition: all 0.6s ease;
    }

    .contact-card:hover .contact-icon {
      transform: scale(1.1);
    }

    .contact-card-title {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 15px;
      transition: color 0.3s ease;
      position: relative;
      padding-bottom: 15px;
    }

    .contact-card-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 50px;
      height: 3px;
      background: var(--gradient-primary);
      border-radius: 2px;
      transition: width 0.3s ease;
    }

    .contact-card:hover .contact-card-title::after {
      width: 80px;
    }

    .contact-card-value {
      color: var(--text-dark);
      font-size: 1.15rem;
      line-height: 1.8;
      margin-bottom: 25px;
      transition: color 0.3s ease;
      min-height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 500;
      padding: 0 10px;
    }

    .contact-card-value a {
      color: inherit;
      text-decoration: none;
      transition: all 0.3s ease;
      word-break: break-word;
    }

    .contact-card-value a:hover {
      color: var(--primary-color);
      text-decoration: underline;
    }

    .contact-action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 14px 30px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 2px solid transparent;
      font-size: 1rem;
      position: relative;
      overflow: hidden;
      z-index: 1;
      min-width: 180px;
      margin-top: auto;
    }

    .contact-action-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      background: var(--gradient-dark);
      transition: width 0.4s ease;
      z-index: -1;
    }

    .contact-action-btn:hover::before {
      width: 100%;
    }

    .contact-action-btn:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 20px 40px rgba(231, 106, 4, 0.3);
      border-color: rgba(255, 255, 255, 0.3);
    }

    .contact-action-btn i {
      font-size: 1.2rem;
      transition: transform 0.3s ease;
    }

    .contact-action-btn:hover i {
      transform: translateX(5px);
    }

    /* Social Media Section - معدل */
    .social-media-section {
      padding: 80px 0;
      background: var(--gradient-dark);
      position: relative;
      overflow: hidden;
    }

    #particles-js-social {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

    .social-container {
      position: relative;
      z-index: 2;
    }

    .social-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 25px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .social-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 35px 20px;
      text-align: center;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      display: block;
      min-height: 200px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .social-card:hover {
      transform: translateY(-15px) scale(1.05);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
      text-decoration: none;
      color: inherit;
      background: rgba(255, 255, 255, 0.15);
    }

    .social-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--gradient-primary);
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 0;
    }

    .social-card:hover::before {
      opacity: 0.1;
    }

    .social-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      background: var(--gradient-primary);
      color: white;
      font-size: 2.5rem;
      transition: all 0.5s ease;
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
      position: relative;
      z-index: 1;
    }

    .social-card:hover .social-icon {
      transform: rotateY(360deg) scale(1.1);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .social-card h4 {
      color: white;
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 10px;
      transition: color 0.3s ease;
      position: relative;
      z-index: 1;
    }

    .social-card p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 0.95rem;
      margin: 0;
      position: relative;
      z-index: 1;
    }

    .social-card .social-username {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
      margin-top: 8px;
      font-weight: 500;
    }

    /* Map Section - باستخدام OpenStreetMap */
    .map-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      position: relative;
      
      
    }
    .row-map {
      display : flex ; 
      justify-content : center ;
      margin: 0 auto;
      text-align : center;
    }
    .row-map-show {
      width : 100%;
    }
    .map-container-pro {
      border-radius: 20px;
      box-shadow: 0 40px 80px rgba(0, 0, 0, 0.15);
      transition: all 0.6s ease;
      height: 100%;
      border: 3px solid transparent;
      position: relative;
    }

    .map-container-pro:hover {
      transform: translateY(-10px);
      box-shadow: 0 60px 120px rgba(0, 0, 0, 0.2);
    }

    .map-container-pro iframe {
      width: 100%;
      height: 400px;
      border: none;
      display: block;
    }

    /* Working Hours Section */
    .hours-section {
      padding: 80px 0;
      background: var(--gradient-dark);
      position: relative;
      overflow: hidden;
    }

    .hours-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 25px;
      padding: 40px 30px;
      text-align: center;
      transition: all 0.5s ease;
      border: 2px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      max-width: 800px;
      margin: 0 auto;
    }

    .hours-card:hover {
      transform: translateY(-10px);
      border-color: rgba(231, 106, 4, 0.3);
      box-shadow: 0 40px 80px rgba(0, 0, 0, 0.3);
    }

    .hours-list {
      list-style: none;
      padding: 0;
      margin: 30px 0 0 0;
    }

    .hours-list li {
      padding: 15px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 1.1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .hours-list li:last-child {
      border-bottom: none;
    }

    .hours-list li span:first-child {
      font-weight: 600;
    }

    .hours-list li span:last-child {
      color: var(--primary-color);
      font-weight: 700;
    }

    /* No Data State */
    .no-data {
      text-align: center;
      padding: 60px 20px;
      background: var(--gradient-card);
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
      margin-top: 30px;
    }

    .no-data-icon {
      font-size: 4rem;
      color: var(--primary-color);
      margin-bottom: 20px;
      display: block;
    }

    .no-data p {
      color: var(--text-light);
      font-size: 1.2rem;
      margin-bottom: 20px;
    }

    .no-data-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--gradient-primary);
      color: white;
      padding: 12px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .no-data-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(231, 106, 4, 0.3);
    }

    .section-title-white {
      color: white !important;
    }

    .section-subtitle-white {
      color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .contact-hero-title {
        font-size: 3rem;
      }
      
      .section-title {
        font-size: 2.4rem;
      }
      .section-title-social
      {
        font-size: 2.4rem;
      }
      .contact-grid {
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
      }
      
      .social-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      }
    }

    @media (max-width: 992px) {
      .contact-hero-section {
        height: 40vh;
        min-height: 350px;
      }
      
      .contact-hero-title {
        font-size: 2.5rem;
      }
      
      .contact-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section-title {
        font-size: 2.2rem;
      }
      .section-title-social{
        font-size: 2.2rem;
      }
      .contact-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .contact-card {
        min-height: 350px;
        padding: 35px 25px;
      }
      
      .social-grid {
        grid-template-columns: repeat(3, 1fr);
      }
      
      .map-container-pro iframe {
        height: 350px;
      }
      
      .social-card {
        padding: 30px 15px;
        min-height: 180px;
      }
    }

    @media (max-width: 768px) {
      .contact-hero-section {
        height: 35vh;
        min-height: 300px;
      }
      
      .contact-hero-title {
        font-size: 2rem;
      }
      
      .contact-hero-content {
        margin-top: 6rem;
      }
      
      .section-title {
        font-size: 1.8rem;
      }
      .section-title-social{
        font-size: 1.8rem;
      }
      .section-subtitle {
        font-size: 1rem;
      }
      
      .contact-info-section {
        padding: 60px 0;
      }
      
      .contact-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .contact-card {
        min-height: 320px;
        padding: 30px 20px;
      }
      
      .contact-icon-wrapper {
        width: 80px;
        height: 80px;
      }
      
      .contact-icon {
        font-size: 2.5rem;
      }
      
      .contact-card-title {
        font-size: 1.4rem;
      }
      
      .contact-card-value {
        font-size: 1.1rem;
      }
      
      .social-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
      
      .social-card {
        padding: 25px 15px;
        min-height: 170px;
      }
      
      .social-icon {
        width: 70px;
        height: 70px;
        font-size: 2rem;
      }
      
      .map-container-pro iframe {
        height: 300px;
      }
      
      .hours-card {
        padding: 30px 20px;
      }
      
      .hours-list li {
        font-size: 1rem;
      }
    }

    @media (max-width: 576px) {
      .contact-hero-section {
        height: 30vh;
        min-height: 250px;
      }
      
      .contact-hero-title {
        font-size: 1.6rem;
      }
      
      .contact-hero-subtitle {
        font-size: 0.9rem;
      }
      
      .section-title {
        font-size: 1.5rem;
      }
      .section-title-social{
        font-size: 1.5rem;
      }
      .section-badge {
        padding: 10px 20px;
        font-size: 0.85rem;
      }
      
      .contact-info-section {
        padding: 50px 0;
      }
      
      .contact-card {
        min-height: 300px;
      }
      
      .contact-action-btn {
        padding: 12px 25px;
        min-width: 160px;
      }
      
      .social-grid {
        grid-template-columns: 1fr;
        max-width: 300px;
        margin: 0 auto;
      }
      
      .map-container-pro iframe {
        height: 250px;
      }
      
      .hours-list li {
        flex-direction: column;
        text-align: center;
        gap: 5px;
      }
    }

    /* Animation Classes */
    .fade-in-up {
      animation: fadeInUp 0.8s ease forwards;
      opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }
    .delay-6 { animation-delay: 0.6s; }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .floating {
      animation: float 3s ease-in-out infinite;
    }

    /* Custom Grid for Different Layouts */
    @media (min-width: 1400px) {
      .contact-grid {
        grid-template-columns: repeat(4, 1fr);
      }
      
      .social-grid {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    /* Hover Effects */
    .contact-card .hover-effect {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: var(--gradient-primary);
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 0;
      border-radius: 20px;
    }

    .contact-card:hover .hover-effect {
      opacity: 0.05;
    }

    /* Text Selection */
    ::selection {
      background: var(--primary-color);
      color: white;
    }

    /* Focus States */
    .contact-action-btn:focus,
    .contact-card:focus {
      outline: 2px solid var(--primary-color);
      outline-offset: 2px;
    }

    /* Loading Animation */
    .loading-shimmer {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
      
      
      .contact-card {
        background: #144734ff;
        border-color: rgba(231, 106, 4, 0.2);
      }
      
      .contact-card-title {
        color: #ffffff;
      }
      
      .contact-card-value {
        color: #e0e0e0;
      }
      

      
      .section-subtitle {
        color: #b0b0b0;
      }
    }
  </style>
</head>

<body class="contact-page">
  
  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Hero Section -->
    <section class="contact-hero-section">
      <div id="particles-js-contact"></div>
      <div class="container">
        <div class="contact-hero-content">
          <h1 class="contact-hero-title animate__animated animate__fadeInDown"><?php echo ($lang == 'ar') ? 'اتصل بنا' : 'Contact Us'; ?></h1>
          <p class="contact-hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            <?php echo ($lang == 'ar') ? 'تواصل مع فريق ركن الأماسي المتخصص للحصول على أفضل حلول الأمن والسلامة' : 'Contact the specialized Rukn Alamasy team for the best security and safety solutions'; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Info Section - Redesigned -->
    <section class="contact-info-section">
      <div id="particles-js-contact-info"></div>
      
      <div class="container contact-grid-container">
        <div class="section-header" data-aos="fade-up" data-aos-delay="100">
          <div class="section-badge">
            <i class="bi bi-telephone-plus"></i>
            <?php echo ($lang == 'ar') ? 'معلومات الاتصال' : 'Contact Information'; ?>
          </div>
          <h2 class="section-title" data-aos="fade-up" data-aos-delay="200">
            <?php echo ($lang == 'ar') ? 'تواصل معنا بكل سهولة' : 'Connect With Us Effortlessly'; ?>
          </h2>
          <p class="section-subtitle" data-aos="fade-up" data-aos-delay="300">
            <?php echo ($lang == 'ar') ? 'اختر الوسيلة المناسبة للتواصل مع فريق ركن الأماسي المتخصص، نحن هنا لخدمتك على مدار الساعة' : 'Choose the appropriate way to communicate with the specialized Rukn Alamasy team, we are here to serve you 24/7'; ?>
          </p>
        </div>

        <?php if (!empty($contact_boxData)): 
          // تصنيف عناصر الاتصال
          $contact_types = [
            'phone' => ['icon' => 'bi bi-telephone-fill', 'action' => 'tel', 'class' => 'phone'],
            'whatsapp_business' => ['icon' => 'bi bi-whatsapp', 'action' => 'whatsapp', 'class' => 'whatsapp'],
            'email' => ['icon' => 'bi bi-envelope-fill', 'action' => 'mailto', 'class' => 'email'],
            'support_email' => ['icon' => 'bi bi-headset', 'action' => 'mailto', 'class' => 'support'],
            'location' => ['icon' => 'bi bi-geo-alt-fill', 'action' => 'map', 'class' => 'location'],
            'google_maps' => ['icon' => 'bi bi-map-fill', 'action' => 'link', 'class' => 'maps'],
            'working_hours' => ['icon' => 'bi bi-clock-fill', 'action' => 'none', 'class' => 'hours'],
            'fax' => ['icon' => 'bi bi-printer-fill', 'action' => 'none', 'class' => 'fax']
          ];
          
          $labels = [
            'phone' => ($lang == 'ar') ? 'الهاتف' : 'Phone',
            'whatsapp_business' => ($lang == 'ar') ? 'واتساب' : 'WhatsApp',
            'email' => ($lang == 'ar') ? 'البريد الإلكتروني' : 'Email',
            'support_email' => ($lang == 'ar') ? 'الدعم الفني' : 'Technical Support',
            'location' => ($lang == 'ar') ? 'العنوان' : 'Address',
            'google_maps' => ($lang == 'ar') ? 'الموقع على الخريطة' : 'Map Location',
            'working_hours' => ($lang == 'ar') ? 'ساعات العمل' : 'Working Hours',
            'fax' => ($lang == 'ar') ? 'الفاكس' : 'Fax'
          ];
        ?>
        
        <div class="contact-grid">
          <?php foreach ($contact_boxData as $index => $contact_item): 
            $type = $contact_item['type'] ?? '';
            $type_info = $contact_types[$type] ?? ['icon' => 'bi bi-info-circle-fill', 'action' => 'none', 'class' => 'default'];
            $label = $contact_item['label'] ?? ($labels[$type] ?? ($contact_item['title'] ?? ($lang == 'ar') ? 'معلومات الاتصال' : 'Contact Info'));
            $value = $contact_item['value'] ?? '';
            $delay = ($index % 4) * 100 + 100;
          ?>
          <div class="contact-card-wrapper" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
            <div class="contact-card fade-in-up delay-<?php echo ($index % 6) + 1; ?>">
              <div class="hover-effect"></div>
              <div class="contact-card-content">
                <div class="contact-icon-wrapper floating">
                  <i class="contact-icon <?php echo htmlspecialchars($type_info['icon']); ?>"></i>
                </div>
                
                <h3 class="contact-card-title"><?php echo htmlspecialchars($label); ?></h3>
                
                <div class="contact-card-value">
                  <?php 
                  if ($type === 'google_maps' && !empty($value)): 
                    echo '<a href="' . htmlspecialchars($value) . '" target="_blank" class="map-link">' . 
                         (($lang == 'ar') ? 'انقر لفتح الخريطة' : 'Click to open map') . 
                         '</a>';
                  elseif ($type === 'location' && !empty($value)): 
                    echo '<span class="location-text">' . htmlspecialchars($value) . '</span>';
                  else:
                    echo '<span>' . htmlspecialchars($value) . '</span>';
                  endif;
                  ?>
                </div>
                
                <?php 
                $action = $type_info['action'];
                $btn_text = '';
                
                if ($action === 'tel' && !empty($value)): 
                  $phone_number = preg_replace('/[^0-9+]/', '', $value);
                  $btn_text = ($lang == 'ar') ? 'اتصل الآن' : 'Call Now';
                ?>
                  <a href="tel:<?php echo $phone_number; ?>" class="contact-action-btn">
                    <i class="bi bi-telephone-outbound"></i>
                    <?php echo $btn_text; ?>
                  </a>
                <?php elseif ($action === 'mailto' && !empty($value)): 
                  $btn_text = ($lang == 'ar') ? 'أرسل بريد' : 'Send Email';
                ?>
                  <a href="mailto:<?php echo htmlspecialchars($value); ?>" class="contact-action-btn">
                    <i class="bi bi-envelope-paper"></i>
                    <?php echo $btn_text; ?>
                  </a>
                <?php elseif ($action === 'map' && !empty($value)): 
                  $btn_text = ($lang == 'ar') ? 'افتح الخريطة' : 'Open Map';
                ?>
                  <a href="https://maps.google.com/?q=<?php echo urlencode($value); ?>" target="_blank" class="contact-action-btn">
                    <i class="bi bi-geo-alt"></i>
                    <?php echo $btn_text; ?>
                  </a>
                <?php elseif ($action === 'whatsapp' && !empty($value)): 
                  $whatsapp_number = preg_replace('/[^0-9+]/', '', $value);
                  $btn_text = ($lang == 'ar') ? 'تواصل عبر واتساب' : 'Chat on WhatsApp';
                ?>
                  <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="contact-action-btn">
                    <i class="bi bi-whatsapp"></i>
                    <?php echo $btn_text; ?>
                  </a>
                <?php elseif ($action === 'link' && !empty($value)): 
                  $btn_text = ($lang == 'ar') ? 'فتح الرابط' : 'Open Link';
                ?>
                  <a href="<?php echo htmlspecialchars($value); ?>" target="_blank" class="contact-action-btn">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <?php echo $btn_text; ?>
                  </a>
                <?php elseif ($action === 'none' && !empty($value)): 
                  $btn_text = ($lang == 'ar') ? 'عرض التفاصيل' : 'View Details';
                ?>
                  <button class="contact-action-btn" onclick="showDetails('<?php echo htmlspecialchars($label); ?>', '<?php echo htmlspecialchars($value); ?>')">
                    <i class="bi bi-eye"></i>
                    <?php echo $btn_text; ?>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <div class="no-data" data-aos="fade-up">
          <i class="bi bi-info-circle no-data-icon"></i>
          <p><?php echo ($lang == 'ar') ? 'لا توجد معلومات اتصال متاحة حالياً. سيتم تحديث هذه الصفحة قريباً.' : 'No contact information available at the moment. This page will be updated soon.'; ?></p>
          <a href="javascript:history.back()" class="no-data-btn">
            <i class="bi bi-arrow-left"></i>
            <?php echo ($lang == 'ar') ? 'العودة' : 'Go Back'; ?>
          </a>
        </div>
        <?php endif; ?>

        <!-- Quick Contact Stats -->
        <div class="quick-stats" data-aos="fade-up" data-aos-delay="500" style="margin-top: 60px; text-align: center;">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="stats-card" style="background: var(--gradient-card); padding: 30px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);">
                <h4 style="color: var(--dark-color); margin-bottom: 20px; font-weight: 700;">
                  <i class="bi bi-speedometer2 me-2"></i>
                  <?php echo ($lang == 'ar') ? 'مؤشرات التواصل السريع' : 'Quick Contact Stats'; ?>
                </h4>
                <div class="row text-center">
                  <div class="col-6 col-md-3 mb-4">
                    <div class="stat-item">
                      <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">24/7</div>
                      <div class="stat-label" style="color: var(--text-light); font-size: 0.9rem; margin-top: 5px;">
                        <?php echo ($lang == 'ar') ? 'دعم فني' : 'Tech Support'; ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3 mb-4">
                    <div class="stat-item">
                      <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);"><?php echo count($contact_boxData); ?></div>
                      <div class="stat-label" style="color: var(--text-light); font-size: 0.9rem; margin-top: 5px;">
                        <?php echo ($lang == 'ar') ? 'قنوات اتصال' : 'Contact Channels'; ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3 mb-4">
                    <div class="stat-item">
                      <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">&lt; 15</div>
                      <div class="stat-label" style="color: var(--text-light); font-size: 0.9rem; margin-top: 5px;">
                        <?php echo ($lang == 'ar') ? 'دقائق رد' : 'Minutes Response'; ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3 mb-4">
                    <div class="stat-item">
                      <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">100%</div>
                      <div class="stat-label" style="color: var(--text-light); font-size: 0.9rem; margin-top: 5px;">
                        <?php echo ($lang == 'ar') ? 'رضا عملاء' : 'Client Satisfaction'; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- Map Section - باستخدام OpenStreetMap -->
    <section class="map-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-geo-alt"></i>
            <?php echo ($lang == 'ar') ? 'موقعنا على الخريطة' : 'Our Location'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'زورنا في مقرنا الرئيسي' : 'Visit Us at Our Main Office'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'تعرف على موقعنا الجغرافي وكيفية الوصول إلينا بسهولة' : 'Learn about our geographical location and how to easily reach us'; ?></p>
        </div>

        <div class="row-map">
          <div class="row-map-show" data-aos="fade-up" data-aos-delay="100">
            <div class="map-container-pro">
              <!-- خريطة Google Maps -->
              <iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3623.676815482492!2d46.691563!3d24.766563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2efd000462b7cd%3A0x9d295b4e23aa8425!2z2LHYp9mGINin2YTYp9mE2K7Yp9mEINmE2YTZh9mE2K8g2KfZhNmF2YjZhNmE2Kkg2YTZh9mE2K8!5e0!3m2!1sar!2ssa!4v1700000000000"
  width="100%"
  height="400"
  style="border:0"
  loading="lazy"
  allowfullscreen
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Working Hours Section -->
    <section class="hours-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-clock"></i>
            <?php echo ($lang == 'ar') ? 'ساعات العمل' : 'Working Hours'; ?>
          </div>
          <h2 class="section-title-social section-title-white"><?php echo ($lang == 'ar') ? 'مواعيد استقبال العملاء' : 'Customer Service Hours'; ?></h2>
          <p class="section-subtitle section-subtitle-white"><?php echo ($lang == 'ar') ? 'نحن متاحون لخدمتكم خلال الأوقات التالية' : 'We are available to serve you during the following times'; ?></p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
            <div class="hours-card">
              <h3 style="color: white; font-size: 2rem; margin-bottom: 25px;">
                <i class="bi bi-calendar-week"></i> <?php echo ($lang == 'ar') ? 'أوقات الدوام الرسمي' : 'Official Working Hours'; ?>
              </h3>
              
              <ul class="hours-list">
                <li>
                  <span><?php echo ($lang == 'ar') ? 'السبت - الخميس' : 'Saturday - Thursday'; ?></span>
                  <span>8:00 <?php echo ($lang == 'ar') ? 'ص' : 'AM'; ?> - 9:00 <?php echo ($lang == 'ar') ? 'م' : 'PM'; ?></span>
                </li>
                <li>
                  <span><?php echo ($lang == 'ar') ? 'الجمعة' : 'Friday'; ?></span>
                  <span style="color: var(--primary-color);"><?php echo ($lang == 'ar') ? 'إجازة رسمية' : 'Official Holiday'; ?></span>
                </li>
                <li>
                  <span><?php echo ($lang == 'ar') ? 'الدعم الفني الطارئ' : 'Emergency Technical Support'; ?></span>
                  <span style="color: var(--primary-color); font-weight: 800;">24/7</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>
  

  
  <!-- مكتبات JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // تهيئة AOS
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true,
          offset: 100,
          easing: 'ease-out-cubic'
        });
      }

      // تأثيرات الجسيمات
      if (typeof particlesJS !== 'undefined') {
        // Contact Hero Particles
        particlesJS('particles-js-contact', {
          particles: {
            number: { value: 60, density: { enable: true, value_area: 800 } },
            color: { value: ["#e76a04"] },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, color: "#e76a04", opacity: 0.2, width: 1 },
            move: { enable: true, speed: 1, direction: "none", random: true }
          },
          interactivity: {
            detect_on: "canvas",
            events: { onhover: { enable: true, mode: "grab" } }
          },
          retina_detect: true
        });

        // Contact Info Particles
        particlesJS('particles-js-contact-info', {
          particles: {
            number: { value: 30, density: { enable: true, value_area: 800 } },
            color: { value: "#e76a04" },
            shape: { type: "circle" },
            opacity: { value: 0.1, random: true },
            size: { value: 4, random: true },
            line_linked: { enable: true, distance: 150, color: "#e76a04", opacity: 0.1, width: 1 },
            move: { enable: true, speed: 0.5, direction: "none", random: true }
          },
          interactivity: { detect_on: "canvas", events: { onhover: { enable: false } } },
          retina_detect: true
        });

        // Social Media Particles
        particlesJS('particles-js-social', {
          particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: "#e76a04" },
            shape: { type: "circle" },
            opacity: { value: 0.2, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, color: "#e76a04", opacity: 0.1, width: 1 },
            move: { enable: true, speed: 1, direction: "none", random: false }
          },
          interactivity: {
            detect_on: "canvas",
            events: { onhover: { enable: true, mode: "bubble" } },
            modes: { bubble: { distance: 200, size: 6, duration: 2, opacity: 0.8 } }
          },
          retina_detect: true
        });
      }

      // زر العودة للأعلى
      const scrollTop = document.getElementById('scroll-top');
      if (scrollTop) {
        window.addEventListener('scroll', function() {
          if (window.pageYOffset > 300) {
            scrollTop.style.display = 'flex';
          } else {
            scrollTop.style.display = 'none';
          }
        });

        scrollTop.addEventListener('click', function(e) {
          e.preventDefault();
          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        });
      }

      // تأثيرات بطاقات الاتصال
      const contactCards = document.querySelectorAll('.contact-card');
      contactCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-15px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // تأثيرات بطاقات التواصل الاجتماعي
      const socialCards = document.querySelectorAll('.social-card');
      socialCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-15px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // تأثيرات الأزرار
      const actionButtons = document.querySelectorAll('.contact-action-btn');
      actionButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // تأثيرات fade-in
      const fadeElements = document.querySelectorAll('.fade-in-up');
      fadeElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
      });

      // تأثيرات اللمس
      if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        const clickableElements = document.querySelectorAll('a, button, .contact-card, .social-card');
        clickableElements.forEach(el => {
          el.style.cursor = 'pointer';
        });
      }

      // عدد بطاقات الاتصال
      const cardCount = document.querySelectorAll('.contact-card').length;
      const statsCount = document.querySelector('.stat-item:nth-child(2) .stat-number');
      if (statsCount && cardCount > 0) {
        statsCount.textContent = cardCount;
      }
      
      // عدد بطاقات التواصل الاجتماعي
      const socialCardCount = document.querySelectorAll('.social-card').length;
      const socialStats = document.querySelectorAll('.social-stats .stat-item:nth-child(2) .stat-number');
      if (socialStats.length > 0 && socialCardCount > 0) {
        socialStats.forEach(stat => {
          stat.textContent = socialCardCount + '+';
        });
      }
    });

    // وظيفة عرض التفاصيل
    function showDetails(title, content) {
      const modalHTML = `
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--gradient-dark); color: white; border-radius: 20px;">
              <div class="modal-header border-0">
                <h5 class="modal-title">${title}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p style="font-size: 1.1rem; line-height: 1.6;">${content}</p>
              </div>
              <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background: var(--gradient-primary); border: none;">
                  ${document.documentElement.lang === 'ar' ? 'إغلاق' : 'Close'}
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // إضافة المودال إلى الصفحة
      const modalContainer = document.createElement('div');
      modalContainer.innerHTML = modalHTML;
      document.body.appendChild(modalContainer.firstElementChild);
      
      // عرض المودال
      const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
      modal.show();
      
      // تنظيف المودال بعد الإغلاق
      document.getElementById('detailsModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
      });
    }

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // تأثيرات عند تحميل الصفحة
    window.addEventListener('load', function() {
      document.body.classList.add('loaded');
      
      // تأثير تدرجي للبطاقات
      const cards = document.querySelectorAll('.contact-card');
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
      
      // تأثير تدرجي لبطاقات التواصل الاجتماعي
      const socialCards = document.querySelectorAll('.social-card');
      socialCards.forEach((card, index) => {
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 80);
      });
    });
    
    // إضافة تأثير الاهتزاز لبطاقات التواصل الاجتماعي
    function addSocialCardEffects() {
      const socialCards = document.querySelectorAll('.social-card');
      socialCards.forEach((card, index) => {
        // تأثير اهتزاز عشوائي خفيف
        setInterval(() => {
          if (Math.random() > 0.7) {
            card.style.transform = 'translateY(-5px)';
            setTimeout(() => {
              card.style.transform = 'translateY(0)';
            }, 300);
          }
        }, 3000 + (index * 500));
      });
    }
    
    // تشغيل تأثيرات إضافية بعد تحميل الصفحة
    setTimeout(addSocialCardEffects, 2000);
  </script>
</body>
</html>