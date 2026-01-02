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
$services = $query->select('services');
$bioservices = $query->select('services');
$ourservices = $query->select('ourservices');
$aboutData = $query->select('about');
$serviceItems = $query->select('about_ul_items');
$statistics = $query->select('statistics');

// تحضير مصفوفة العناصر - الطريقة الصحيحة
$aboutItems = [];
if (!empty($aboutData)) {
    $about = $aboutData[0];
    $aboutItems = [
        'title' => isset($about['title']) ? $about['title'] : '',
        'p1' => isset($about['p1']) ? $about['p1'] : '',
        'p2' => isset($about['p2']) ? $about['p2'] : '',
        'image' => isset($about['image']) ? $about['image'] : '',
        'list_items' => []
    ];
    
    foreach ($serviceItems as $item) {
        if (isset($item['list_item']) && !empty($item['list_item'])) {
            $aboutItems['list_items'][] = $item['list_item'];
        }
    }
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? 'خدماتنا - ركن الأماسي' : 'Our Services - Rukn Alamasy'; ?></title>
  <meta name="description" content="<?php echo ($lang == 'ar') ? 'اكتشف خدماتنا المتميزة وحلولنا الشاملة لأمن وسلامة منشآتك' : 'Discover our premium services and comprehensive solutions for your facility security and safety'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'خدمات، حلول، أمن، سلامة، معدات حماية' : 'services, solutions, security, safety, protection equipment'; ?>">
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
    /* نفس الـ CSS من صفحة About مع تعديلات بسيطة */
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
    .services-hero-section {
      height: 40vh;
      min-height: 300px;
      position: relative;
      overflow: hidden;
      background: var(--gradient-dark);
    }

    #particles-js-services {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      pointer-events: none;
    }

    .services-hero-content {
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

    .services-hero-title {
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

    .services-hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
      line-height: 1.8;
    }

    /* Services Section */
    .services-section {
      padding: 120px 0;
      background: transparent;
      position: relative;
      overflow: hidden;
    }

    #particles-js-services-content {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      pointer-events: none;
    }

    .service-card {
      background: white;
      border-radius: 25px;
      padding: 50px 35px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(231, 106, 4, 0.1);
      text-align: center;
      margin-bottom: 30px;
    }

    .service-card::before {
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

    .service-card:hover::before {
      transform: scaleX(1);
    }

    .service-card:hover {
      transform: translateY(-25px) scale(1.03);
      box-shadow: 0 40px 80px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.3);
    }

    .service-icon {
      width: 100px;
      height: 100px;
      margin: 0 auto 35px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: var(--gradient-primary);
      box-shadow: 0 15px 35px rgba(231, 106, 4, 0.3);
      transition: all 0.5s ease;
    }

    .service-card:hover .service-icon {
      transform: rotateY(360deg) scale(1.1);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .service-icon i {
      font-size: 2.8rem;
      color: white;
      transition: transform 0.5s ease;
    }

    .service-card:hover .service-icon i {
      transform: scale(1.2);
    }

    .service-card h3 {
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 20px;
      transition: color 0.3s ease;
    }

    .service-card:hover h3 {
      color: var(--primary-color);
    }

    .service-card p {
      color: var(--text-light);
      line-height: 1.8;
      margin-bottom: 30px;
      font-size: 1.05rem;
    }

    .service-features {
      list-style: none;
      padding: 0;
      margin: 25px 0;
      text-align: left;
    }

    .service-features li {
      padding: 10px 0;
      border-bottom: 1px solid rgba(231, 106, 4, 0.1);
      color: var(--text-dark);
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .service-features li:last-child {
      border-bottom: none;
    }

    .service-features li i {
      color: var(--primary-color);
      font-size: 1.2rem;
      min-width: 25px;
    }

    /* About Section */
    .about-services-section {
      padding: 120px 0;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      position: relative;
    }

    .about-content {
      padding: 0 20px;
    }

    .about-content h2 {
      font-size: 3rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 30px;
      position: relative;
      display: inline-block;
    }

    .about-content h2::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 0;
      width: 100px;
      height: 6px;
      background: var(--gradient-primary);
      border-radius: 5px;
    }

    [dir="rtl"] .about-content h2::after {
      left: auto;
      right: 0;
    }

    .about-content h3 {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary-color);
      margin: 40px 0 20px;
    }

    .about-content p {
      font-size: 1.1rem;
      color: var(--text-light);
      line-height: 1.9;
      margin-bottom: 25px;
    }

    .about-list {
      list-style: none;
      padding: 0;
      margin: 30px 0;
    }

    .about-list li {
      padding: 15px 0;
      border-bottom: 1px solid rgba(231, 106, 4, 0.1);
      color: var(--text-dark);
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 15px;
      transition: transform 0.3s ease;
    }

    .about-list li:hover {
      transform: translateX(10px);
    }

    [dir="rtl"] .about-list li:hover {
      transform: translateX(-10px);
    }

    .about-list li:last-child {
      border-bottom: none;
    }

    .about-list li i {
      color: var(--primary-color);
      font-size: 1.3rem;
      min-width: 30px;
    }

    .about-image-container {
      border-radius: 25px;
      overflow: hidden;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
      position: relative;
      height: 500px;
    }

    .about-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.8s ease;
    }

    .about-image-container:hover .about-image {
      transform: scale(1.1);
    }

    .about-image-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(20, 71, 52, 0.9), transparent);
      color: white;
      padding: 30px;
    }

    /* Statistics Section */
    .stats-section-services {
      padding: 120px 0;
      background: linear-gradient(135deg, #0d3b28 0%, var(--dark-color) 100%);
      position: relative;
      overflow: hidden;
    }

    #particles-js-stats-services {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

    .stats-card-services {
      text-align: center;
      padding: 50px 30px;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 25px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.5s ease;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .stats-card-services::before {
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

    .stats-card-services:hover::before {
      opacity: 0.1;
    }

    .stats-card-services:hover {
      transform: translateY(-15px);
      border-color: rgba(231, 106, 4, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .stats-icon-services {
      font-size: 3rem;
      color: var(--light-color);
      margin-bottom: 25px;
      filter: drop-shadow(0 5px 15px rgba(231, 106, 4, 0.3));
    }

    .stats-number-services {
      font-size: 4rem;
      font-weight: 900;
      color: white;
      margin-bottom: 15px;
      text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
    }

    .stats-title-services {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .stats-description-services {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.05rem;
      line-height: 1.7;
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

    /* Buttons */
    .btn-service {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 18px 45px;
      border-radius: 60px;
      font-weight: 700;
      text-decoration: none;
      transition: var(--transition);
      border: none;
      font-size: 1.2rem;
      margin: 0 15px;
      position: relative;
      overflow: hidden;
      z-index: 1;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-primary-service {
      background: var(--gradient-primary);
      color: var(--white);
      box-shadow: 0 10px 30px rgba(231, 106, 4, 0.4);
    }

    .btn-outline-service {
      background: transparent;
      color: var(--white);
      border: 3px solid var(--primary-color);
      backdrop-filter: blur(10px);
    }

    .btn-service::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: 0.8s;
      z-index: -1;
    }

    .btn-service:hover::before {
      left: 100%;
    }

    .btn-service:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 20px 40px rgba(231, 106, 4, 0.6);
    }

    .btn-view-all {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--gradient-primary);
      color: white;
      padding: 18px 45px;
      border-radius: 60px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: none;
      box-shadow: 0 15px 40px rgba(231, 106, 4, 0.3);
      position: relative;
      overflow: hidden;
      z-index: 1;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-view-all::before {
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

    .btn-view-all:hover::before {
      width: 100%;
    }

    .btn-view-all:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    /* CTA Section */
    .cta-section-services {
      padding: 120px 0;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      position: relative;
      overflow: hidden;
    }

    #particles-js-cta-services {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

    .cta-content-services {
      position: relative;
      z-index: 2;
      text-align: center;
      color: white;
    }

    .cta-title-services {
      font-size: 3rem;
      font-weight: 900;
      margin-bottom: 30px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .cta-description-services {
      font-size: 1.3rem;
      margin-bottom: 50px;
      opacity: 0.9;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
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
      left: 40px;
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

    /* Background Patterns */
    .bg-pattern {
      position: relative;
    }

    .bg-pattern::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(circle at 20% 20%, rgba(231, 106, 4, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(243, 212, 23, 0.1) 0%, transparent 50%);
      z-index: -1;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .services-hero-title {
        font-size: 3.2rem;
      }
      
      .section-title {
        font-size: 3rem;
      }
      
      .about-content h2 {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 992px) {
      .services-hero-title {
        font-size: 2.8rem;
      }
      
      .services-hero-subtitle {
        font-size: 1.3rem;
      }
      
      .section-title {
        font-size: 2.5rem;
      }
      
      .about-content {
        margin-bottom: 50px;
      }
      
      .service-card {
        margin-bottom: 30px;
      }
    }

    @media (max-width: 768px) {
      .services-hero-title {
        font-size: 2.2rem;
      }
      
      .services-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .section-subtitle {
        font-size: 1.1rem;
      }
      
      .btn-service {
        padding: 15px 30px;
        font-size: 1rem;
        margin: 10px 0;
        display: block;
      }
      
      .about-content h2 {
        font-size: 2rem;
      }
      
      .about-content h3 {
        font-size: 1.5rem;
      }
      
      .cta-title-services {
        font-size: 2.2rem;
      }
    }

    @media (max-width: 576px) {
      .services-hero-section {
        height: 30vh;
        min-height: 300px;
      }
      
      .services-hero-title {
        font-size: 1.8rem;
      }
      
      .section-title {
        font-size: 1.8rem;
      }
      
      .section-badge {
        padding: 10px 20px;
        font-size: 0.9rem;
      }
      
      .about-list li {
        font-size: 1rem;
      }
      
      .cta-title-services {
        font-size: 1.8rem;
      }
    }

    /* Canvas styles */
    #particles-js-services canvas,
    #particles-js-services-content canvas,
    #particles-js-stats-services canvas,
    #particles-js-cta-services canvas {
      display: block;
      vertical-align: bottom;
      transform: translate3d(0, 0, 0);
    }
  </style>
</head>

<body class="services-page">
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
  <div id="particles-js-services"></div>

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Hero Section -->
    <section class="services-hero-section">
      <div class="container">
        <div class="services-hero-content fade-in-up">
          <h1 class="services-hero-title animate__animated animate__fadeInDown"><?php echo ($lang == 'ar') ? 'خدماتنا' : 'Our Services'; ?></h1>
          <p class="services-hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            <?php echo ($lang == 'ar') ? 'اكتشف مجموعة خدماتنا المتكاملة والمبتكرة المصممة خصيصاً لاحتياجات أمن وسلامة منشآتك' : 'Discover our integrated and innovative services specifically designed for your facility security and safety needs'; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services-section bg-pattern" id="services">
      <div id="particles-js-services-content"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-gear"></i>
            <?php echo ($lang == 'ar') ? 'خدماتنا المتميزة' : 'Our Premium Services'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'مجموعة خدماتنا الشاملة' : 'Our Comprehensive Service Portfolio'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'نقدم مجموعة واسعة من خدمات الأمن والسلامة التي تلبي جميع متطلبات المنشآت بأنواعها' : 'We offer a wide range of safety and security services that meet all the requirements of various types of facilities'; ?></p>
        </div>

        <div class="row gy-5">
          <?php if (!empty($services)): ?>
            <?php foreach ($services as $index => $service): ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                <div class="service-card">
                  <div class="service-icon">
                    <i class="<?php echo isset($service['icon']) ? $service['icon'] : 'bi bi-shield-check'; ?>"></i>
                  </div>
                  <h3><?php echo isset($service['title']) ? htmlspecialchars($service['title']) : 'Service'; ?></h3>
                  <p><?php echo isset($service['description']) ? htmlspecialchars($service['description']) : 'Premium service description'; ?></p>
                  
                  <?php if (!empty($service['features'])): ?>
                    <ul class="service-features">
                      <?php 
                      $features = explode(';', $service['features']);
                      foreach ($features as $feature):
                        if (!empty(trim($feature))): ?>
                          <li><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                        <?php endif;
                      endforeach; ?>
                    </ul>
                  <?php endif; ?>
                  
                  <?php if (!empty($service['link'])): ?>
                    <a href="<?php echo htmlspecialchars($service['link']); ?>" class="btn-service btn-primary-service">
                      <i class="bi bi-arrow-right"></i>
                      <?php echo ($lang == 'ar') ? 'المزيد من التفاصيل' : 'Learn More'; ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Services Default Cards -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-shield-check"></i>
                </div>
                <h3><?php echo ($lang == 'ar') ? 'استشارات الأمن والسلامة' : 'Security & Safety Consultations'; ?></h3>
                <p><?php echo ($lang == 'ar') ? 'تقييم احتياجات الأمن والسلامة وتقديم الحلول المثلى لمنشآتك' : 'Assessment of security and safety needs and providing optimal solutions for your facilities'; ?></p>
                <ul class="service-features">
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'تحليل المخاطر' : 'Risk Analysis'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'تخطيط أنظمة السلامة' : 'Safety Systems Planning'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'تقييم الامتثال' : 'Compliance Assessment'; ?></li>
                </ul>
                <a href="contact.php" class="btn-service btn-primary-service">
                  <i class="bi bi-arrow-right"></i>
                  <?php echo ($lang == 'ar') ? 'المزيد من التفاصيل' : 'Learn More'; ?>
                </a>
              </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-tools"></i>
                </div>
                <h3><?php echo ($lang == 'ar') ? 'تركيب وصيانة الأنظمة' : 'System Installation & Maintenance'; ?></h3>
                <p><?php echo ($lang == 'ar') ? 'تركيب وصيانة جميع أنظمة الأمن والسلامة بأعلى معايير الجودة' : 'Installation and maintenance of all security and safety systems with the highest quality standards'; ?></p>
                <ul class="service-features">
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'تركيب الأنظمة' : 'System Installation'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'الصيانة الدورية' : 'Periodic Maintenance'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'الإصلاح العاجل' : 'Emergency Repair'; ?></li>
                </ul>
                <a href="contact.php" class="btn-service btn-primary-service">
                  <i class="bi bi-arrow-right"></i>
                  <?php echo ($lang == 'ar') ? 'المزيد من التفاصيل' : 'Learn More'; ?>
                </a>
              </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-people"></i>
                </div>
                <h3><?php echo ($lang == 'ar') ? 'التدريب والتأهيل' : 'Training & Qualification'; ?></h3>
                <p><?php echo ($lang == 'ar') ? 'برامج تدريبية متخصصة للعاملين في مجال الأمن والسلامة المهنية' : 'Specialized training programs for workers in the field of occupational safety and security'; ?></p>
                <ul class="service-features">
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'تدريب فرق الطوارئ' : 'Emergency Teams Training'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'برامج السلامة المهنية' : 'Occupational Safety Programs'; ?></li>
                  <li><i class="bi bi-check-circle"></i> <?php echo ($lang == 'ar') ? 'ورش العمل التخصصية' : 'Specialized Workshops'; ?></li>
                </ul>
                <a href="contact.php" class="btn-service btn-primary-service">
                  <i class="bi bi-arrow-right"></i>
                  <?php echo ($lang == 'ar') ? 'المزيد من التفاصيل' : 'Learn More'; ?>
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>


    <!-- Statistics Section -->
    <section class="stats-section-services">
      <div id="particles-js-stats-services"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-graph-up-arrow"></i>
            <?php echo ($lang == 'ar') ? 'إنجازاتنا في الخدمات' : 'Our Service Achievements'; ?>
          </div>
          <h2 class="section-title section-title-white"><?php echo ($lang == 'ar') ? 'خدماتنا بالأرقام' : 'Our Services in Numbers'; ?></h2>
          <p class="section-subtitle section-subtitle-white"><?php echo ($lang == 'ar') ? 'أرقام تعكس جودة وتنوع خدماتنا التي نقدمها' : 'Numbers that reflect the quality and diversity of the services we provide'; ?></p>
        </div>

        <div class="row">
          <?php if (!empty($statistics)): ?>
            <?php foreach ($statistics as $index => $stat): ?>
              <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="stats-card-services">
                  <div class="stats-icon-services">
                    <i class="<?php echo isset($stat['icon']) ? $stat['icon'] : 'bi bi-graph-up'; ?>"></i>
                  </div>
                  <div class="stats-number-services">
                    <span data-purecounter-start="0" 
                          data-purecounter-end="<?php echo isset($stat['count']) ? $stat['count'] : '0'; ?>" 
                          data-purecounter-duration="2" 
                          class="purecounter">
                      <?php echo isset($stat['count']) ? $stat['count'] : '0'; ?>
                    </span>
                  </div>
                  <div class="stats-title-services">
                    <?php echo isset($stat['title']) ? htmlspecialchars($stat['title']) : (($lang == 'ar') ? 'إنجاز' : 'Achievement'); ?>
                  </div>
                  <div class="stats-description-services">
                    <?php echo isset($stat['description']) ? htmlspecialchars($stat['description']) : (($lang == 'ar') ? 'قصة نجاحنا' : 'Our success story'); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Default Statistics -->
            <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
              <div class="stats-card-services">
                <div class="stats-icon-services">
                  <i class="bi bi-award"></i>
                </div>
                <div class="stats-number-services">
                  <span data-purecounter-start="0" data-purecounter-end="1500" data-purecounter-duration="2" class="purecounter">1500</span>+
                </div>
                <div class="stats-title-services"><?php echo ($lang == 'ar') ? 'مشروع خدمة' : 'Service Project'; ?></div>
                <div class="stats-description-services"><?php echo ($lang == 'ar') ? 'نفذت بنجاح' : 'Successfully Implemented'; ?></div>
              </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
              <div class="stats-card-services">
                <div class="stats-icon-services">
                  <i class="bi bi-people"></i>
                </div>
                <div class="stats-number-services">
                  <span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="2" class="purecounter">500</span>+
                </div>
                <div class="stats-title-services"><?php echo ($lang == 'ar') ? 'عميل راضي' : 'Satisfied Client'; ?></div>
                <div class="stats-description-services"><?php echo ($lang == 'ar') ? 'لخدماتنا المتميزة' : 'For Our Premium Services'; ?></div>
              </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="300">
              <div class="stats-card-services">
                <div class="stats-icon-services">
                  <i class="bi bi-clock-history"></i>
                </div>
                <div class="stats-number-services">
                  <span data-purecounter-start="0" data-purecounter-end="99" data-purecounter-duration="2" class="purecounter">99</span>%
                </div>
                <div class="stats-title-services"><?php echo ($lang == 'ar') ? 'نسبة الرضا' : 'Satisfaction Rate'; ?></div>
                <div class="stats-description-services"><?php echo ($lang == 'ar') ? 'من عملائنا' : 'From Our Clients'; ?></div>
              </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="400">
              <div class="stats-card-services">
                <div class="stats-icon-services">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="stats-number-services">
                  <span data-purecounter-start="0" data-purecounter-end="50" data-purecounter-duration="2" class="purecounter">50</span>+
                </div>
                <div class="stats-title-services"><?php echo ($lang == 'ar') ? 'نوع خدمة' : 'Service Type'; ?></div>
                <div class="stats-description-services"><?php echo ($lang == 'ar') ? 'متخصصة ومتنوعة' : 'Specialized & Diverse'; ?></div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section-services">
      <div id="particles-js-cta-services"></div>
      
      <div class="container">
        <div class="cta-content-services" data-aos="fade-up">
          <h2 class="cta-title-services"><?php echo ($lang == 'ar') ? 'هل تحتاج إلى خدمات متخصصة؟' : 'Need Specialized Services?'; ?></h2>
          <p class="cta-description-services">
            <?php echo ($lang == 'ar') ? 'فريقنا من الخبراء مستعد لمساعدتك في اختيار وتنفيذ أفضل الحلول والخدمات لمنشآتك. تواصل معنا اليوم للحصول على استشارة مجانية.' : 'Our team of experts is ready to help you choose and implement the best solutions and services for your facilities. Contact us today for a free consultation.'; ?>
          </p>
          <div class="cta-buttons">
            <a href="contact.php" class="btn-view-all pulse">
              <i class="bi bi-telephone"></i>
              <?php echo ($lang == 'ar') ? 'تواصل معنا الآن' : 'Contact Us Now'; ?>
            </a>
            <a href="about.php" class="btn-service btn-outline-service" style="margin-left: 20px;">
              <i class="bi bi-info-circle"></i>
              <?php echo ($lang == 'ar') ? 'تعرف علينا أكثر' : 'Learn More About Us'; ?>
            </a>
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

      // تهيئة PureCounter
      if (typeof PureCounter !== 'undefined') {
        new PureCounter();
      }

      // تأثيرات الجسيمات
      if (typeof particlesJS !== 'undefined') {
        // Hero Section Particles
        particlesJS('particles-js-services', {
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

        // Services Content Section Particles
        particlesJS('particles-js-services-content', {
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

        // Stats Section Particles
        particlesJS('particles-js-stats-services', {
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
              enable: false,
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

        // CTA Section Particles
        particlesJS('particles-js-cta-services', {
          particles: {
            number: {
              value: 100,
              density: {
                enable: true,
                value_area: 800
              }
            },
            color: {
              value: ["#e76a04", "#f3d417", "#ffffff"]
            },
            shape: {
              type: ["circle", "triangle", "polygon"],
              stroke: {
                width: 0,
                color: "#000000"
              }
            },
            opacity: {
              value: 0.6,
              random: true,
              anim: {
                enable: true,
                speed: 1,
                opacity_min: 0.1,
                sync: false
              }
            },
            size: {
              value: 4,
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
              speed: 2,
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
      }

      // تأثيرات المؤشر
      const cursor = document.querySelector('.cursor-effect');
      if (cursor) {
        document.addEventListener('mousemove', (e) => {
          cursor.style.left = e.clientX + 'px';
          cursor.style.top = e.clientY + 'px';
        });

        document.querySelectorAll('a, button, .btn, .service-card, .stats-card-services').forEach(el => {
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
      const cards = document.querySelectorAll('.service-card, .stats-card-services');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-25px) scale(1.03)';
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
      document.querySelectorAll('.btn, .btn-service, .btn-view-all').forEach(btn => {
        btn.addEventListener('click', function(e) {
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