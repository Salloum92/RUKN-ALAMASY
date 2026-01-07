<?php
// بدء الجلسة
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'ar';
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header('Location: ' . $current_url);
    exit();
}

include 'config.php';
require_once 'lang.php';

$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

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
  
  <!-- مكتبات لإضافة الجاذبية -->
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
    /* نفس الـ CSS من صفحة About مع تعديلات لصفحة الاتصال */
     :root {
      --primary-color: #e76a04;
      --primary-dark: #d45f00;
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
      --gradient-primary: #e76a04;
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

    /* Loading Animation */
    .loading-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: var(--dark-color);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 99999;
      transition: opacity 0.5s, visibility 0.5s;
    }

    .loader {
      width: 120px;
      height: 120px;
      position: relative;
    }

    .loader-diamond {
      width: 100%;
      height: 100%;
      background: var(--gradient-primary);
      clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
      animation: loader-spin 2s infinite linear;
      filter: drop-shadow(0 0 20px rgba(231, 106, 4, 0.5));
    }

    @keyframes loader-spin {
      0% { transform: rotate(0deg) scale(1); }
      50% { transform: rotate(180deg) scale(1.2); }
      100% { transform: rotate(360deg) scale(1); }
    }

    /* Floating Particles Background */
    .floating-particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      pointer-events: none;
    }

    .particle {
      position: absolute;
      border-radius: 50%;
      background: var(--gradient-primary);
      animation: float 6s infinite ease-in-out;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0) rotate(0deg);
        filter: blur(0);
      }
      50% {
        transform: translateY(-40px) rotate(180deg);
        filter: blur(2px);
      }
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
      margin-top : 3.5rem;
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
      0% {
        filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.3));
      }
      100% {
        filter: drop-shadow(0 0 20px rgba(231, 106, 4, 0.5));
      }
    }

    .contact-hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
      line-height: 1.8;
    }

    /* Contact Info Section - تصميم احترافي */
    .contact-info-section {
      padding: 120px 0;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      position: relative;
      overflow: hidden;
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
      padding: 60px 40px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12);
      transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
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

    .contact-card-pro::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: var(--gradient-primary);
      opacity: 0;
      transition: opacity 0.6s ease;
      z-index: -1;
    }

    .contact-card-pro:hover::before {
      transform: scaleX(1);
    }

    .contact-card-pro:hover::after {
      opacity: 0.05;
    }

    .contact-card-pro:hover {
      transform: translateY(-30px) scale(1.05);
      box-shadow: 0 50px 100px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.2);
    }

    .contact-icon-pro {
      width: 120px;
      height: 120px;
      margin: 0 auto 40px;
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
      transform: rotateY(360deg) scale(1.15);
      box-shadow: 0 30px 70px rgba(231, 106, 4, 0.4);
    }

    .contact-icon-pro i {
      font-size: 3.2rem;
      color: white;
      transition: all 0.6s ease;
    }

    .contact-card-pro:hover .contact-icon-pro i {
      transform: scale(1.2);
    }

    .contact-card-pro h3 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 25px;
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

    .contact-card-pro:hover h3 {
      color: var(--primary-color);
    }

    .contact-card-pro .contact-value {
      color: var(--text-dark);
      font-size: 1.3rem;
      line-height: 1.8;
      text-align: center;
      font-weight: 500;
      margin-bottom: 30px;
      transition: color 0.3s ease;
    }

    .contact-card-pro:hover .contact-value {
      color: var(--text-light);
    }

    .contact-action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 15px 35px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: 2px solid transparent;
      font-size: 1.1rem;
      margin-top: 20px;
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
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
      border-color: var(--white);
    }

    /* Social Media Section - تصميم احترافي */
    .social-media-section {
      padding: 100px 0;
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
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 25px;
      max-width: 900px;
      margin: 0 auto;
    }

    .social-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 40px 20px;
      text-align: center;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 2px solid transparent;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .social-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: var(--gradient-primary);
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: -1;
    }

    .social-card:hover::before {
      opacity: 0.1;
    }

    .social-card:hover {
      transform: translateY(-20px) scale(1.1);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .social-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      background: var(--gradient-primary);
      color: white;
      font-size: 2.2rem;
      transition: all 0.5s ease;
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
    }

    .social-card:hover .social-icon {
      transform: rotateY(360deg) scale(1.2);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .social-card h4 {
      color: white;
      font-size: 1.3rem;
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
      padding: 100px 0;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      position: relative;
    }

    .map-container-pro {
      border-radius: 30px;
      overflow: hidden;
      box-shadow: 0 40px 80px rgba(0, 0, 0, 0.15);
      transition: all 0.6s ease;
      height: 100%;
      border: 3px solid transparent;
      position: relative;
    }

    .map-container-pro::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border: 3px solid transparent;
      border-radius: 30px;
      background: var(--gradient-primary);
      background-clip: padding-box;
      -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .map-container-pro:hover {
      transform: translateY(-20px);
      box-shadow: 0 60px 120px rgba(0, 0, 0, 0.2);
    }

    .map-container-pro iframe {
      width: 100%;
      height: 500px;
      border: none;
      display: block;
    }

    /* Working Hours Section */
    .hours-section {
      padding: 100px 0;
      background: var(--gradient-dark);
      position: relative;
      overflow: hidden;
    }

    .hours-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 25px;
      padding: 60px 50px;
      text-align: center;
      transition: all 0.5s ease;
      border: 2px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      max-width: 800px;
      margin: 0 auto;
    }

    .hours-card:hover {
      transform: translateY(-20px);
      border-color: rgba(231, 106, 4, 0.3);
      box-shadow: 0 40px 80px rgba(0, 0, 0, 0.3);
    }

    .hours-list {
      list-style: none;
      padding: 0;
      margin: 40px 0 0 0;
    }

    .hours-list li {
      padding: 20px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 1.2rem;
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
      color: var(--secondary-color);
      font-weight: 700;
    }

    /* Section Header */
    .section-header {
      text-align: center;
      margin-bottom: 80px;
      position: relative;
    }

    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--gradient-primary);
      color: white;
      padding: 12px 30px;
      border-radius: 60px;
      font-size: 1rem;
      font-weight: 700;
      margin-bottom: 25px;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .section-badge::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transform: rotate(45deg);
      animation: shine 3s infinite linear;
    }

    @keyframes shine {
      0% { transform: translateX(-100%) rotate(45deg); }
      100% { transform: translateX(100%) rotate(45deg); }
    }

    .section-title {
      font-size: 3.5rem;
      font-weight: 900;
      color: var(--dark-color);
      margin-bottom: 25px;
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
      bottom: -20px;
      left: 50%;
      transform: translateX(-50%);
      width: 150px;
      height: 6px;
      background: var(--gradient-primary);
      border-radius: 5px;
      animation: lineWidth 3s infinite alternate;
    }

    @keyframes lineWidth {
      0% { width: 100px; }
      100% { width: 200px; }
    }

    .section-subtitle {
      font-size: 1.3rem;
      color: var(--text-light);
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.9;
    }

    .section-subtitle-white {
      color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Floating Elements */
    .floating-element {
      animation: floatAnimation 3s ease-in-out infinite;
    }

    @keyframes floatAnimation {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-25px) rotate(5deg); }
    }

    /* Scroll to Top */
    .scroll-top {
      position: fixed;
      bottom: 40px;
      right: 40px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--gradient-primary);
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

    /* Animations */
    .fade-in-up {
      animation: fadeInUp 1.2s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Cursor Effect */
    .cursor-effect {
      position: fixed;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: radial-gradient(circle, var(--primary-color), transparent);
      pointer-events: none;
      z-index: 9999;
      mix-blend-mode: screen;
      transition: transform 0.2s ease;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .contact-hero-title {
        font-size: 3.2rem;
      }
      
      .section-title {
        font-size: 3rem;
      }
      
      .contact-card-pro {
        padding: 50px 30px;
      }
      
      .contact-icon-pro {
        width: 100px;
        height: 100px;
      }
    }

    @media (max-width: 992px) {
      .contact-hero-title {
        font-size: 2.8rem;
      }
      
      .contact-hero-subtitle {
        font-size: 1.3rem;
      }
      
      .section-title {
        font-size: 2.5rem;
      }
      
      .contact-card-pro {
        margin-bottom: 30px;
      }
      
      .social-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .contact-hero-title {
        font-size: 2.2rem;
      }
      
      .contact-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .section-subtitle {
        font-size: 1.1rem;
      }
      
      .contact-card-pro {
        padding: 40px 25px;
      }
      
      .contact-card-pro h3 {
        font-size: 1.7rem;
      }
      
      .contact-card-pro .contact-value {
        font-size: 1.1rem;
      }
      
      .map-container-pro iframe {
        height: 400px;
      }
    }

    @media (max-width: 576px) {
      .contact-hero-section {
        height: 30vh;
        min-height: 250px;
      }
      
      .contact-hero-title {
        font-size: 1.8rem;
      }
      
      .section-title {
        font-size: 1.8rem;
      }
      
      .section-badge {
        padding: 10px 20px;
        font-size: 0.9rem;
      }
      
      .contact-icon-pro {
        width: 80px;
        height: 80px;
      }
      
      .contact-icon-pro i {
        font-size: 2.5rem;
      }
      
      .social-grid {
        grid-template-columns: 1fr;
      }
      
      .map-container-pro iframe {
        height: 350px;
      }
      
      .hours-card {
        padding: 40px 30px;
      }
      
      .hours-list li {
        font-size: 1rem;
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    /* Canvas styles */
    #particles-js-contact canvas,
    #particles-js-contact-info canvas,
    #particles-js-social canvas {
      display: block;
      vertical-align: bottom;
      transform: translate3d(0, 0, 0);
    }
  </style>
</head>

<body class="contact-page">
  <!-- Loading Screen -->
  <div class="loading-screen">
    <div class="loader">
      <div class="loader-diamond"></div>
    </div>
  </div>
  
  <!-- Floating Particles Background -->
  <div class="floating-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <!-- تأثيرات المؤشر -->
  <div class="cursor-effect"></div>

  <!-- تأثيرات الجسيمات للهيرو -->
  <div id="particles-js-contact"></div>

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Hero Section -->
    <section class="contact-hero-section">
      <div class="container">
        <div class="contact-hero-content fade-in-up">
          <h1 class="contact-hero-title animate__animated animate__fadeInDown"><?php echo ($lang == 'ar') ? 'اتصل بنا' : 'Contact Us'; ?></h1>
          <p class="contact-hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            <?php echo ($lang == 'ar') ? 'تواصل مع فريق ركن الأماسي المتخصص للحصول على أفضل حلول الأمن والسلامة' : 'Contact the specialized Rukn Alamasy team for the best security and safety solutions'; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Info Section - تصميم احترافي -->
    <section class="contact-info-section">
      <div id="particles-js-contact-info"></div>
      
      <div class="container contact-grid-container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-telephone"></i>
            <?php echo ($lang == 'ar') ? 'معلومات الاتصال' : 'Contact Information'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'تواصل معنا بسهولة' : 'Connect With Us Easily'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اختر الوسيلة المناسبة للتواصل مع فريق ركن الأماسي المتخصص' : 'Choose the appropriate way to communicate with the specialized Rukn Alamasy team'; ?></p>
        </div>

        <div class="row">
          <?php foreach ($contact_boxData as $index => $contact): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
              <div class="contact-card-pro fade-in-up">
                <div class="contact-icon-pro floating-element">
                  <i class="<?php echo htmlspecialchars($contact['icon']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($contact['title']); ?></h3>
                <p class="contact-value"><?php echo htmlspecialchars($contact['value']); ?></p>
                
                <?php 
                // إضافة زر للاتصال حسب نوع الوسيلة
                $contact_type = strtolower($contact['title']);
                if (strpos($contact_type, 'هاتف') !== false || strpos($contact_type, 'phone') !== false) {
                  $phone_number = preg_replace('/[^0-9+]/', '', $contact['value']);
                  echo '<a href="tel:' . $phone_number . '" class="contact-action-btn">
                          <i class="bi bi-telephone"></i>
                          ' . ($lang == 'ar' ? 'اتصل الآن' : 'Call Now') . '
                        </a>';
                } elseif (strpos($contact_type, 'بريد') !== false || strpos($contact_type, 'email') !== false) {
                  echo '<a href="mailto:' . $contact['value'] . '" class="contact-action-btn">
                          <i class="bi bi-envelope"></i>
                          ' . ($lang == 'ar' ? 'أرسل بريد' : 'Send Email') . '
                        </a>';
                } elseif (strpos($contact_type, 'موقع') !== false || strpos($contact_type, 'location') !== false) {
                  echo '<a href="https://maps.google.com/?q=' . urlencode($contact['value']) . '" target="_blank" class="contact-action-btn">
                          <i class="bi bi-geo-alt"></i>
                          ' . ($lang == 'ar' ? 'افتح الخريطة' : 'Open Map') . '
                        </a>';
                }
                ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Social Media Section -->
    <section class="social-media-section">
      <div id="particles-js-social"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-people"></i>
            <?php echo ($lang == 'ar') ? 'وسائل التواصل' : 'Social Media'; ?>
          </div>
          <h2 class="section-title section-title-white"><?php echo ($lang == 'ar') ? 'تابعنا على وسائل التواصل' : 'Follow Us on Social Media'; ?></h2>
          <p class="section-subtitle section-subtitle-white"><?php echo ($lang == 'ar') ? 'ابق على اطلاع بآخر أخبارنا وعروضنا الخاصة ومنتجاتنا الجديدة' : 'Stay updated with our latest news, special offers and new products'; ?></p>
        </div>

        <div class="social-grid">
          <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="100">
            <div class="social-icon">
              <i class="bi bi-whatsapp"></i>
            </div>
            <h4>WhatsApp</h4>
            <p><?php echo ($lang == 'ar') ? 'اتصال مباشر' : 'Direct Contact'; ?></p>
          </div>
          <?php endif; ?>

          <?php if (isset($contactData[0]['telegram']) && !empty($contactData[0]['telegram'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="200">
            <div class="social-icon">
              <i class="bi bi-telegram"></i>
            </div>
            <h4>Telegram</h4>
            <p><?php echo ($lang == 'ar') ? 'قناتنا الرسمية' : 'Our Official Channel'; ?></p>
          </div>
          <?php endif; ?>

          <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="300">
            <div class="social-icon">
              <i class="bi bi-twitter-x"></i>
            </div>
            <h4>Twitter</h4>
            <p><?php echo ($lang == 'ar') ? 'أحدث التغريدات' : 'Latest Tweets'; ?></p>
          </div>
          <?php endif; ?>

          <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="400">
            <div class="social-icon">
              <i class="bi bi-facebook"></i>
            </div>
            <h4>Facebook</h4>
            <p><?php echo ($lang == 'ar') ? 'صفحتنا الرسمية' : 'Our Official Page'; ?></p>
          </div>
          <?php endif; ?>

          <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="500">
            <div class="social-icon">
              <i class="bi bi-instagram"></i>
            </div>
            <h4>Instagram</h4>
            <p><?php echo ($lang == 'ar') ? 'معرض أعمالنا' : 'Our Work Gallery'; ?></p>
          </div>
          <?php endif; ?>

          <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
          <div class="social-card" data-aos="fade-up" data-aos-delay="600">
            <div class="social-icon">
              <i class="bi bi-youtube"></i>
            </div>
            <h4>YouTube</h4>
            <p><?php echo ($lang == 'ar') ? 'فيديوهات توضيحية' : 'Explanatory Videos'; ?></p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-geo-alt"></i>
            <?php echo ($lang == 'ar') ? 'موقعنا على الخريطة' : 'Our Location'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'زورنا في مقرنا الرئيسي' : 'Visit Us at Our Main Office'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'تعرف على موقعنا الجغرافي وكيفية الوصول إلينا بسهولة' : 'Learn about our geographical location and how to easily reach us'; ?></p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
            <div class="map-container-pro fade-in-up">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2974.8813426551865!2d67.01298087569626!3d39.58263960598262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d21d3f20f2e7d%3A0x65da282d59cb1b22!2sUy!5e1!3m2!1sen!2s!4v1738728573422!5m2!1sen!2s"
                width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" 
                title="<?php echo ($lang == 'ar') ? 'موقع ركن الأماسي على الخريطة' : 'Rukn Alamasy Location on Map'; ?>"></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Working Hours Section -->
    <section class="hours-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-clock"></i>
            <?php echo ($lang == 'ar') ? 'ساعات العمل' : 'Working Hours'; ?>
          </div>
          <h2 class="section-title section-title-white"><?php echo ($lang == 'ar') ? 'مواعيد استقبال العملاء' : 'Customer Service Hours'; ?></h2>
          <p class="section-subtitle section-subtitle-white"><?php echo ($lang == 'ar') ? 'نحن متاحون لخدمتكم خلال الأوقات التالية' : 'We are available to serve you during the following times'; ?></p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
            <div class="hours-card fade-in-up">
              <h3 style="color: white; font-size: 2.5rem; margin-bottom: 30px;">
                <i class="bi bi-calendar-week"></i> <?php echo ($lang == 'ar') ? 'أوقات الدوام الرسمي' : 'Official Working Hours'; ?>
              </h3>
              
              <ul class="hours-list">
                <li>
                  <span><?php echo ($lang == 'ar') ? 'السبت - الأربعاء' : 'Saturday - Wednesday'; ?></span>
                  <span>8:00 <?php echo ($lang == 'ar') ? 'ص' : 'AM'; ?> - 5:00 <?php echo ($lang == 'ar') ? 'م' : 'PM'; ?></span>
                </li>
                <li>
                  <span><?php echo ($lang == 'ar') ? 'الخميس' : 'Thursday'; ?></span>
                  <span>8:00 <?php echo ($lang == 'ar') ? 'ص' : 'AM'; ?> - 3:00 <?php echo ($lang == 'ar') ? 'م' : 'PM'; ?></span>
                </li>
                <li>
                  <span><?php echo ($lang == 'ar') ? 'الجمعة' : 'Friday'; ?></span>
                  <span style="color: var(--secondary-color);"><?php echo ($lang == 'ar') ? 'إجازة رسمية' : 'Official Holiday'; ?></span>
                </li>
                <li>
                  <span><?php echo ($lang == 'ar') ? 'الدعم الفني الطارئ' : 'Emergency Technical Support'; ?></span>
                  <span style="color: var(--primary-color);">24/7</span>
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
    // Initialize when page loads
    window.addEventListener('DOMContentLoaded', (event) => {
      // Remove loading screen
      setTimeout(() => {
        document.querySelector('.loading-screen').style.opacity = '0';
        document.querySelector('.loading-screen').style.visibility = 'hidden';
      }, 1500);
    }); 
    
    document.addEventListener('DOMContentLoaded', function() {
      // تهيئة AOS
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1200,
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
            number: {
              value: 80,
              density: {
                enable: true,
                value_area: 800
              }
            },
            color: {
              value: ["#e76a04", "#f3d417", "#ffffff"]
            },
            shape: {
              type: ["circle", "triangle"],
              stroke: {
                width: 0,
                color: "#000000"
              }
            },
            opacity: {
              value: 0.5,
              random: true,
              anim: {
                enable: true,
                speed: 1,
                opacity_min: 0.1,
                sync: false
              }
            },
            size: {
              value: 3,
              random: true,
              anim: {
                enable: true,
                speed: 2,
                size_min: 0.1,
                sync: false
              }
            },
            line_linked: {
              enable: false,
              distance: 150,
              color: "#e76a04",
              opacity: 0.4,
              width: 1
            },
            move: {
              enable: true,
              speed: 1,
              direction: "none",
              random: true,
              straight: false,
              out_mode: "out",
              bounce: false,
              attract: {
                enable: false,
                rotateX: 600,
                rotateY: 1200
              }
            }
          },
          interactivity: {
            detect_on: "canvas",
            events: {
              onhover: {
                enable: true,
                mode: "grab"
              },
              onclick: {
                enable: true,
                mode: "push"
              },
              resize: true
            },
            modes: {
              grab: {
                distance: 140,
                line_linked: {
                  opacity: 1
                }
              },
              push: {
                particles_nb: 4
              }
            }
          },
          retina_detect: true
        });

        // Contact Info Particles
        particlesJS('particles-js-contact-info', {
          particles: {
            number: {
              value: 60,
              density: {
                enable: true,
                value_area: 800
              }
            },
            color: {
              value: "#144734"
            },
            shape: {
              type: "circle",
              stroke: {
                width: 0,
                color: "#144734"
              }
            },
            opacity: {
              value: 0.2,
              random: true,
              anim: {
                enable: false
              }
            },
            size: {
              value: 4,
              random: true,
              anim: {
                enable: false
              }
            },
            line_linked: {
              enable: false,
              distance: 150,
              color: "#144734",
              opacity: 0.2,
              width: 1
            },
            move: {
              enable: true,
              speed: 1,
              direction: "none",
              random: true,
              straight: false,
              out_mode: "out",
              bounce: false
            }
          },
          interactivity: {
            detect_on: "canvas",
            events: {
              onhover: {
                enable: true,
                mode: "grab"
              },
              onclick: {
                enable: false
              }
            }
          },
          retina_detect: true
        });

        // Social Media Particles
        particlesJS('particles-js-social', {
          particles: {
            number: {
              value: 80,
              density: {
                enable: true,
                value_area: 800
              }
            },
            color: {
              value: "#ffffff"
            },
            shape: {
              type: "circle"
            },
            opacity: {
              value: 0.3,
              random: true
            },
            size: {
              value: 3,
              random: true
            },
            line_linked: {
              enable: true,
              distance: 150,
              color: "#ffffff",
              opacity: 0.2,
              width: 1
            },
            move: {
              enable: true,
              speed: 2,
              direction: "none",
              random: false,
              straight: false,
              out_mode: "out",
              bounce: false
            }
          },
          interactivity: {
            detect_on: "canvas",
            events: {
              onhover: {
                enable: true,
                mode: "bubble"
              },
              onclick: {
                enable: false
              }
            },
            modes: {
              bubble: {
                distance: 200,
                size: 6,
                duration: 2,
                opacity: 0.8
              }
            }
          },
          retina_detect: true
        });
      }

      // تأثيرات المؤشر
      const cursor = document.querySelector('.cursor-effect');
      if (cursor) {
        document.addEventListener('mousemove', (e) => {
          cursor.style.left = e.clientX + 'px';
          cursor.style.top = e.clientY + 'px';
        });

        document.querySelectorAll('a, button, .contact-card-pro, .social-card, .map-container-pro, .hours-card').forEach(el => {
          el.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2)';
            cursor.style.background = 'radial-gradient(circle, rgba(231,106,4,0.5), transparent)';
          });
          el.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.background = 'radial-gradient(circle, var(--primary-color), transparent)';
          });
        });
      }

      // تأثيرات hover للبطاقات
      const contactCards = document.querySelectorAll('.contact-card-pro');
      contactCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-30px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      const socialCards = document.querySelectorAll('.social-card');
      socialCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-20px) scale(1.1)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

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

      // تأثيرات النقر على الأزرار
      document.querySelectorAll('.contact-action-btn, .social-card').forEach(btn => {
        btn.addEventListener('click', function(e) {
          if (this.tagName === 'A') {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
              position: absolute;
              border-radius: 50%;
              background: rgba(255, 255, 255, 0.7);
              transform: scale(0);
              animation: ripple-animation 0.8s linear;
              width: ${size}px;
              height: ${size}px;
              top: ${y}px;
              left: ${x}px;
              pointer-events: none;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
              ripple.remove();
            }, 800);
          }
        });
      });

      // إضافة CSS للـ ripple effect
      const style = document.createElement('style');
      style.textContent = `
        @keyframes ripple-animation {
          to {
            transform: scale(4);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);

      // Floating Particles Animation
      function animateParticles() {
        const particles = document.querySelectorAll('.particle');
        particles.forEach((particle, index) => {
          particle.style.animationDelay = `${index * 0.5}s`;
        });
      }

      animateParticles();
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