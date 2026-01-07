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
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? 'اتصل بنا - ركن الأماسي' : 'Contact Us - Rukn Alamasy'; ?></title>
  <meta name="description" content="<?php echo ($lang == 'ar') ? 'تواصل مع ركن الأماسي للحصول على أفضل منتجات وخدمات الأمن والسلامة' : 'Contact Rukn Alamasy for the best security and safety products and services'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'اتصل بنا، تواصل، دعم، خدمة عملاء' : 'contact us, support, inquiry, customer service'; ?>">
  <link href="favicon.ico" rel="icon">
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
      height: 50vh;
      min-height: 400px;
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
      margin-top: 8rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }

    .contact-hero-title {
      font-size: 3.5rem;
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

    /* Contact Info Section */
    .contact-info-section {
      padding: 100px 0;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      position: relative;
      overflow: hidden;
    }
    .row-info{
    
    display: grid;
    flex-wrap: wrap;
    margin-top: calc(-1 * var(--bs-gutter-y));
    margin-right: calc(-.5 * var(--bs-gutter-x));
    margin-left: calc(-.5 * var(--bs-gutter-x));
    }
    #particles-js-contact-info {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 0;
      pointer-events: none;
    }

    .contact-grid-container {
      position: relative;
      z-index: 2;
    }

    .contact-card-pro {
      background: var(--white);
      border-radius: 25px;
      padding: 40px 30px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12);
      transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
      text-align : center ;
    }

    .contact-card-pro::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-primary);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.6s ease;
    }

    .contact-card-pro:hover::before {
      transform: scaleX(1);
    }

    .contact-card-pro:hover {
      transform: translateY(-15px) scale(1.03);
      box-shadow: 0 50px 100px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.2);
    }

    .contact-icon-pro {
      width: 80px;
      height: 80px;
      margin: 0 auto 30px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: var(--gradient-primary);
      box-shadow: 0 20px 50px rgba(231, 106, 4, 0.3);
      transition: all 0.6s ease;
    }

    .contact-card-pro:hover .contact-icon-pro {
      transform: rotateY(360deg) scale(1.1);
      box-shadow: 0 30px 70px rgba(231, 106, 4, 0.4);
    }

    .contact-icon-pro i {
      font-size: 2.5rem;
      color: white;
      transition: all 0.6s ease;
    }

    .contact-card-pro h3 {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 20px;
      text-align: center;
      transition: color 0.3s ease;
      position: relative;
      padding-bottom: 15px;
    }

    .contact-card-pro h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: var(--gradient-primary);
      border-radius: 2px;
      transition: width 0.3s ease;
    }

    .contact-card-pro:hover h3::after {
      width: 100px;
    }

    .contact-card-pro .contact-value {
      color: var(--text-dark);
      font-size: 1.1rem;
      line-height: 1.8;
      text-align: center;
      font-weight: 500;
      margin-bottom: 25px;
      transition: color 0.3s ease;
      min-height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .contact-action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 12px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: 2px solid transparent;
      font-size: 1rem;
      margin-top: 10px;
      position: relative;
      overflow: hidden;
      z-index: 1;
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
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
      border-color: var(--white);
    }

    /* Social Media Section */
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

    .social-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
      max-width: 900px;
      margin: 0 auto;
    }

    .social-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 30px 15px;
      text-align: center;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .social-card:hover {
      transform: translateY(-10px) scale(1.05);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
      text-decoration: none;
      color: inherit;
    }

    .social-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      background: var(--gradient-primary);
      color: white;
      font-size: 2rem;
      transition: all 0.5s ease;
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
    }

    .social-card:hover .social-icon {
      transform: rotateY(360deg) scale(1.1);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .social-card h4 {
      color: white;
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 10px;
      transition: color 0.3s ease;
    }

    .social-card p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      margin: 0;
    }

    /* Map Section */
    .map-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      position: relative;
    }

    .map-container-pro {
      border-radius: 20px;
      overflow: hidden;
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
      padding: 10px 25px;
      border-radius: 60px;
      font-size: 0.9rem;
      font-weight: 700;
      margin-bottom: 20px;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: 900;
      color: var(--dark-color);
      margin-bottom: 20px;
      position: relative;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .section-title-white {
      color: white !important;
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

    @keyframes lineWidth {
      0% { width: 80px; }
      100% { width: 150px; }
    }

    .section-subtitle {
      font-size: 1.1rem;
      color: var(--text-light);
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.7;
    }

    .section-subtitle-white {
      color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Scroll to Top */
    .scroll-top {
      position: fixed;
      bottom: 20px;
      left: 30px;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: var(--gradient-primary);
      color: white;
      text-decoration: none;
      display: none;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      box-shadow: 0 10px 30px rgba(231, 106, 4, 0.4);
      z-index: 999;
      transition: all 0.4s ease;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }

    .scroll-top:hover {
      transform: translateY(-3px) scale(1.1);
      box-shadow: 0 20px 40px rgba(231, 106, 4, 0.6);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .contact-hero-title {
        font-size: 3rem;
      }
      
      .section-title {
        font-size: 2.2rem;
      }
      
      .contact-card-pro {
        padding: 35px 25px;
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
        font-size: 2rem;
      }
      
      .social-grid {
        grid-template-columns: repeat(3, 1fr);
      }
      
      .map-container-pro iframe {
        height: 350px;
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
      
      .section-subtitle {
        font-size: 1rem;
      }
      
      .contact-card-pro {
        padding: 30px 20px;
      }
      
      .contact-icon-pro {
        width: 70px;
        height: 70px;
      }
      
      .contact-icon-pro i {
        font-size: 2rem;
      }
      
      .social-grid {
        grid-template-columns: repeat(2, 1fr);
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
      
      .section-badge {
        padding: 8px 20px;
        font-size: 0.8rem;
      }
      
      .social-grid {
        grid-template-columns: 1fr;
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

    /* إصلاحات إضافية */
    .no-data {
      text-align: center;
      padding: 50px 20px;
      color: var(--text-light);
      font-size: 1.1rem;
    }

    .contact-value a {
      color: inherit;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .contact-value a:hover {
      color: var(--primary-color);
      text-decoration: underline;
    }

    /* Animations */
    .fade-in-up {
      animation: fadeInUp 1s ease forwards;
      opacity: 0;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
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

    <!-- Contact Info Section -->
    <section class="contact-info-section">
      <div id="particles-js-contact-info"></div>
      
      <div class="container contact-grid-container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-telephone"></i>
            <?php echo ($lang == 'ar') ? 'معلومات الاتصال' : 'Contact Information'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'تواصل معنا بسهولة' : 'Connect With Us Easily'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اختر الوسيلة المناسبة للتواصل مع فريق ركن الأماسي المتخصص' : 'Choose the appropriate way to communicate with the specialized Rukn Alamasy team'; ?></p>
        </div>

        <div class="row">
          <?php 
          if (!empty($contact_boxData)): 
            $contact_types = [
              'phone' => ['icon' => 'bi bi-telephone', 'action' => 'tel'],
              'email' => ['icon' => 'bi bi-envelope', 'action' => 'mailto'],
              'location' => ['icon' => 'bi bi-geo-alt', 'action' => 'map'],
              'working_hours' => ['icon' => 'bi bi-clock', 'action' => 'none'],
              'support_email' => ['icon' => 'bi bi-headset', 'action' => 'mailto'],
              'fax' => ['icon' => 'bi bi-printer', 'action' => 'none'],
              'whatsapp_business' => ['icon' => 'bi bi-whatsapp', 'action' => 'whatsapp'],
              'google_maps' => ['icon' => 'bi bi-geo-alt-fill', 'action' => 'link']
            ];
            
            $labels = [
              'phone' => ($lang == 'ar') ? 'رقم الهاتف' : 'Phone Number',
              'email' => ($lang == 'ar') ? 'البريد الإلكتروني' : 'Email',
              'location' => ($lang == 'ar') ? 'العنوان' : 'Address',
              'working_hours' => ($lang == 'ar') ? 'ساعات العمل' : 'Working Hours',
              'support_email' => ($lang == 'ar') ? 'البريد للدعم' : 'Support Email',
              'fax' => ($lang == 'ar') ? 'فاكس' : 'Fax',
              'whatsapp_business' => ($lang == 'ar') ? 'واتساب الأعمال' : 'Business WhatsApp',
              'google_maps' => ($lang == 'ar') ? 'خرائط جوجل' : 'Google Maps'
            ];
            
            foreach ($contact_boxData as $index => $contact_item): 
              $type = $contact_item['type'] ?? '';
              $type_info = $contact_types[$type] ?? ['icon' => 'bi bi-info-circle', 'action' => 'none'];
              $label = $contact_item['label'] ?? ($labels[$type] ?? ($contact_item['title'] ?? ($lang == 'ar') ? 'معلومات الاتصال' : 'Contact Info'));
              $value = $contact_item['value'] ?? '';
          ?>
          <div class="col-xl-3 col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($index % 4) * 100; ?>">
            <div class="contact-card-pro fade-in-up">
              <div class="contact-icon-pro">
                <i class="<?php echo htmlspecialchars($type_info['icon']); ?>"></i>
              </div>
              <h3><?php echo htmlspecialchars($label); ?></h3>
              <p class="contact-value">
                <?php 
                if ($type === 'google_maps' && !empty($value)): 
                  echo '<a href="' . htmlspecialchars($value) . '" target="_blank">' . 
                       (($lang == 'ar') ? 'انقر لفتح الخريطة' : 'Click to open map') . 
                       '</a>';
                elseif ($type === 'location' && !empty($value)): 
                  echo htmlspecialchars($value);
                else:
                  echo htmlspecialchars($value);
                endif;
                ?>
              </p>
              
              <?php 
              $action = $type_info['action'];
              
              if ($action === 'tel' && !empty($value)): 
                $phone_number = preg_replace('/[^0-9+]/', '', $value);
                ?>
                <a href="tel:<?php echo $phone_number; ?>" class="contact-action-btn">
                  <i class="bi bi-telephone"></i>
                  <?php echo ($lang == 'ar') ? 'اتصل الآن' : 'Call Now'; ?>
                </a>
              <?php elseif ($action === 'mailto' && !empty($value)): ?>
                <a href="mailto:<?php echo htmlspecialchars($value); ?>" class="contact-action-btn">
                  <i class="bi bi-envelope"></i>
                  <?php echo ($lang == 'ar') ? 'أرسل بريد' : 'Send Email'; ?>
                </a>
              <?php elseif ($action === 'map' && !empty($value)): ?>
                <a href="https://maps.google.com/?q=<?php echo urlencode($value); ?>" target="_blank" class="contact-action-btn">
                  <i class="bi bi-geo-alt"></i>
                  <?php echo ($lang == 'ar') ? 'افتح الخريطة' : 'Open Map'; ?>
                </a>
              <?php elseif ($action === 'whatsapp' && !empty($value)): 
                $whatsapp_number = preg_replace('/[^0-9+]/', '', $value);
                ?>
                <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="contact-action-btn">
                  <i class="bi bi-whatsapp"></i>
                  <?php echo ($lang == 'ar') ? 'تواصل عبر واتساب' : 'Chat on WhatsApp'; ?>
                </a>
              <?php elseif ($action === 'link' && !empty($value)): ?>
                <a href="<?php echo htmlspecialchars($value); ?>" target="_blank" class="contact-action-btn">
                  <i class="bi bi-link"></i>
                  <?php echo ($lang == 'ar') ? 'فتح الرابط' : 'Open Link'; ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php else: ?>
          <div class="col-12">
            <div class="no-data">
              <i class="bi bi-info-circle" style="font-size: 3rem; margin-bottom: 20px; color: var(--primary-color);"></i>
              <p><?php echo ($lang == 'ar') ? 'لا توجد معلومات اتصال متاحة حالياً' : 'No contact information available at the moment'; ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Social Media Section -->
    <section class="social-media-section">
      <div id="particles-js-social"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-people"></i>
            <?php echo ($lang == 'ar') ? 'وسائل التواصل' : 'Social Media'; ?>
          </div>
          <h2 class="section-title section-title-white"><?php echo ($lang == 'ar') ? 'تابعنا على وسائل التواصل' : 'Follow Us on Social Media'; ?></h2>
          <p class="section-subtitle section-subtitle-white"><?php echo ($lang == 'ar') ? 'ابق على اطلاع بآخر أخبارنا وعروضنا الخاصة ومنتجاتنا الجديدة' : 'Stay updated with our latest news, special offers and new products'; ?></p>
        </div>

        <div class="social-grid">
          <?php 
          $social_platforms = [
            'whatsapp' => ['icon' => 'bi bi-whatsapp', 'label_ar' => 'واتساب', 'label_en' => 'WhatsApp'],
            'telegram' => ['icon' => 'bi bi-telegram', 'label_ar' => 'تيليجرام', 'label_en' => 'Telegram'],
            'twitter' => ['icon' => 'bi bi-twitter-x', 'label_ar' => 'تويتر', 'label_en' => 'Twitter'],
            'facebook' => ['icon' => 'bi bi-facebook', 'label_ar' => 'فيسبوك', 'label_en' => 'Facebook'],
            'instagram' => ['icon' => 'bi bi-instagram', 'label_ar' => 'انستغرام', 'label_en' => 'Instagram'],
            'youtube' => ['icon' => 'bi bi-youtube', 'label_ar' => 'يوتيوب', 'label_en' => 'YouTube'],
            'tiktok' => ['icon' => 'bi bi-tiktok', 'label_ar' => 'تيك توك', 'label_en' => 'TikTok'],
            'snapchat' => ['icon' => 'bi bi-snapchat', 'label_ar' => 'سناب شات', 'label_en' => 'Snapchat'],
            'linkedin' => ['icon' => 'bi bi-linkedin', 'label_ar' => 'لينكد إن', 'label_en' => 'LinkedIn'],
            'pinterest' => ['icon' => 'bi bi-pinterest', 'label_ar' => 'بنترست', 'label_en' => 'Pinterest']
          ];
          
          $has_social = false;
          $delay = 100;
          
          foreach ($social_platforms as $platform => $info): 
            if (!empty($contactData[$platform])): 
              $has_social = true;
              $link = $contactData[$platform];
              $label = ($lang == 'ar') ? $info['label_ar'] : $info['label_en'];
          ?>
          <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="social-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
            <div class="social-icon">
              <i class="<?php echo $info['icon']; ?>"></i>
            </div>
            <h4><?php echo $label; ?></h4>
            <p><?php echo ($lang == 'ar') ? 'انقر للمتابعة' : 'Click to follow'; ?></p>
          </a>
          <?php 
              $delay += 50;
            endif;
          endforeach; 
          
          if (!$has_social):
          ?>
          <div class="col-12">
            <div class="no-data" style="color: white;">
              <i class="bi bi-share" style="font-size: 3rem; margin-bottom: 20px;"></i>
              <p><?php echo ($lang == 'ar') ? 'لا توجد وسائل تواصل اجتماعي متاحة حالياً' : 'No social media links available at the moment'; ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Map Section -->
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

        <div class="row-info">
          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
            <div class="map-container-pro">
              <iframe
                src="https://maps.app.goo.gl/RgU88JLEDwqH3XFWA"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" 
                title="<?php echo ($lang == 'ar') ? 'موقع ركن الأماسي على الخريطة' : 'Rukn Alamasy Location on Map'; ?>">
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
          <h2 class="section-title section-title-white"><?php echo ($lang == 'ar') ? 'مواعيد استقبال العملاء' : 'Customer Service Hours'; ?></h2>
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
                  <span><?php echo ($lang == 'ar') ? 'السبت - الخميس' : 'Saturday - Thersday'; ?></span>
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
 <a href="#" class="scroll-top" id="scroll-top">
    <i class="bi bi-arrow-up"></i>
  </a>
  
 
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
          duration: 800,
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
            color: { value: ["#e76a04", "#f3d417", "#ffffff"] },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: false },
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
            number: { value: 40, density: { enable: true, value_area: 800 } },
            color: { value: "#144734" },
            shape: { type: "circle" },
            opacity: { value: 0.1, random: true },
            size: { value: 4, random: true },
            line_linked: { enable: false },
            move: { enable: true, speed: 0.5, direction: "none", random: true }
          },
          interactivity: { detect_on: "canvas", events: { onhover: { enable: false } } },
          retina_detect: true
        });

        // Social Media Particles
        particlesJS('particles-js-social', {
          particles: {
            number: { value: 60, density: { enable: true, value_area: 800 } },
            color: { value: "#ffffff" },
            shape: { type: "circle" },
            opacity: { value: 0.2, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.1, width: 1 },
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

      // تأثيرات hover للبطاقات
      const contactCards = document.querySelectorAll('.contact-card-pro');
      contactCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-15px) scale(1.03)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      const socialCards = document.querySelectorAll('.social-card');
      socialCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-10px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // تأثيرات fade-in
      const fadeElements = document.querySelectorAll('.fade-in-up');
      fadeElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
      });

      // إضافة تأثيرات للمس
      if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        // تحسين تجربة اللمس
        const clickableElements = document.querySelectorAll('a, button, .contact-card-pro, .social-card');
        clickableElements.forEach(el => {
          el.style.cursor = 'pointer';
        });
      }
    });

    // Smooth scrolling for anchor links
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
  </script>
</body>
</html>