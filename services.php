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

    /* قسم "لماذا تختارنا" الاحترافي */
    .why-choose-professional {
      padding: 100px 0;
      background: #ffffff;
      position: relative;
      overflow: hidden;
    }

    .why-choose-professional .professional-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    .why-choose-professional .bg-gradient {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 40%;
      background: linear-gradient(135deg, rgba(20, 71, 52, 0.05) 0%, rgba(231, 106, 4, 0.05) 100%);
    }

    .why-choose-professional .bg-pattern {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(231, 106, 4, 0.03) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(20, 71, 52, 0.03) 0%, transparent 20%);
    }

    /* العنوان الرئيسي */
    .section-header-professional {
      text-align: center;
      margin-bottom: 60px;
      position: relative;
      z-index: 1;
    }

    .professional-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, #144734 0%, #1e6b4e 100%);
      color: white;
      padding: 8px 20px;
      border-radius: 30px;
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 20px;
    }

    .badge-icon {
      font-size: 1rem;
      font-weight: bold;
    }

    .professional-title {
      font-size: 2.8rem;
      font-weight: 800;
      color: #144734;
      margin-bottom: 20px;
      line-height: 1.2;
    }

    .professional-title em {
      font-style: normal;
      background: linear-gradient(90deg, #e76a04, #ff9a00);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .professional-subtitle {
      font-size: 1.1rem;
      color: #666;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* الشبكة */
    .features-professional {
      display: flex;
      flex-direction: column;
      gap: 30px;
      margin-bottom: 60px;
      position: relative;
      z-index: 1;
    }

    .feature-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    /* البطاقات */
    .feature-card-pro {
      perspective: 1000px;
      height: 100%;
    }

    .feature-card-inner {
      position: relative;
      background: white;
      border-radius: 16px;
      padding: 40px 30px;
      height: 100%;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(20, 71, 52, 0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
    }

    .feature-card-inner:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(20, 71, 52, 0.15);
      border-color: rgba(231, 106, 4, 0.2);
    }

    .feature-card-inner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #e76a04, #ff9a00);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
    }

    .feature-card-inner:hover::before {
      transform: scaleX(1);
    }

    .feature-hover-effect {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(20, 71, 52, 0.03) 0%, rgba(231, 106, 4, 0.03) 100%);
      opacity: 0;
      transition: opacity 0.4s ease;
      z-index: 0;
    }

    .feature-card-inner:hover .feature-hover-effect {
      opacity: 1;
    }

    /* الأيقونة */
    .feature-icon {
      margin-bottom: 25px;
      position: relative;
      z-index: 1;
      text-align: center;
    }

    /* المحتوى */
    .feature-content {
      position: relative;
      z-index: 1;
    }

    .feature-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: #144734;
      margin-bottom: 15px;
      line-height: 1.3;
      text-align: center;
    }

    .feature-desc {
      font-size: 0.95rem;
      color: #666;
      line-height: 1.6;
      margin-bottom: 25px;
      text-align: center;
    }

    /* الإحصائيات */
    .feature-stat {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: linear-gradient(135deg, rgba(20, 71, 52, 0.05) 0%, rgba(231, 106, 4, 0.05) 100%);
      border-radius: 30px;
      border: 1px solid rgba(20, 71, 52, 0.1);
      margin: 0 auto;
      display: block;
      text-align: center;
      width: fit-content;
    }

    .stat-value {
      font-size: 1.4rem;
      font-weight: 800;
      background: linear-gradient(90deg, #e76a04, #ff9a00);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: inline-block;
    }

    .stat-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #144734;
      display: inline-block;
      margin-left: 5px;
    }

    /* شهادة الجودة */
    .quality-certificate {
      background: linear-gradient(135deg, #144734 0%, #1e6b4e 100%);
      border-radius: 20px;
      padding: 50px;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .quality-certificate::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
      z-index: 0;
    }

    .certificate-content {
      display: flex;
      align-items: center;
      gap: 30px;
      position: relative;
      z-index: 1;
    }

    .certificate-icon {
      text-align: center;
    }

    .certificate-text h3 {
      font-size: 1.8rem;
      font-weight: 700;
      color: white;
      margin-bottom: 10px;
    }

    .certificate-text p {
      font-size: 1.1rem;
      color: rgba(255, 255, 255, 0.9);
      line-height: 1.6;
      margin: 0;
    }

    /* SVG أيقونات */
    .feature-icon svg {
      stroke-width: 2;
    }

    /* تصميم متجاوب */
    @media (max-width: 1200px) {
      .professional-title {
        font-size: 2.4rem;
      }
    }

    @media (max-width: 992px) {
      .feature-row {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .professional-title {
        font-size: 2.2rem;
      }
      
      .quality-certificate {
        padding: 40px 30px;
      }
    }

    @media (max-width: 768px) {
      .why-choose-professional {
        padding: 70px 0;
      }
      
      .feature-row {
        grid-template-columns: 1fr;
      }
      
      .professional-title {
        font-size: 2rem;
      }
      
      .certificate-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
      }
      
      .feature-card-inner {
        padding: 30px 20px;
      }
      
      .feature-title {
        font-size: 1.3rem;
      }
    }

    @media (max-width: 480px) {
      .professional-title {
        font-size: 1.8rem;
      }
      
      .professional-subtitle {
        font-size: 1rem;
      }
      
      .certificate-text h3 {
        font-size: 1.5rem;
      }
      
      .certificate-text p {
        font-size: 1rem;
      }
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
          <p class="section-subtitle">
            <?php echo ($lang == 'ar') ? 'نلتزم بالجودة، وسرعة التوريد، وتوفير حلول متكاملة تناسب متطلبات كل مشروع' : 'We are committed to quality, fast delivery, and providing integrated solutions that meet the requirements of each project'; ?>
          </p>
        </div>

        <div class="row gy-5">
            <!-- كرت الخدمة 1 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'توريد معدات الوقاية الشخصية (PPE)' : 'Personal Protective Equipment (PPE) Supply'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'توريد جميع أنواع معدات الوقاية الشخصية لحماية العاملين من المخاطر المهنية' : 'Supply of all types of personal protective equipment to protect workers from occupational hazards'; ?></p>
                </div>
            </div>

            <!-- كرت الخدمة 2 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'توريد أنظمة وأدوات مكافحة الحريق' : 'Fire Fighting Systems & Tools Supply'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'أنظمة الإطفاء الحديثة ومعدات مكافحة الحرائق بمختلف أنواعها' : 'Modern fire extinguishing systems and firefighting equipment of various types'; ?></p>
                </div>
            </div>

            <!-- كرت الخدمة 3 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-signpost"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'توريد لوحات وإشارات السلامة' : 'Safety Signs & Boards Supply'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'لوحات تحذيرية وإرشادية معتمدة تعزز الوعي بالسلامة في المنشآت' : 'Approved warning and guidance boards that enhance safety awareness in facilities'; ?></p>
                </div>
            </div>

            <!-- كرت الخدمة 4 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'توريد معدات الطوارئ والإخلاء' : 'Emergency & Evacuation Equipment Supply'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'معدات متكاملة لخطط الطوارئ والإخلاء في حالات الخطر' : 'Integrated equipment for emergency and evacuation plans in hazardous situations'; ?></p>
                </div>
            </div>

            <!-- كرت الخدمة 5 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-building-gear"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'توريد أدوات السلامة المهنية' : 'Professional Safety Tools Supply'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'أدوات ومعدات السلامة المهنية للمصانع والمشاريع الإنشائية' : 'Professional safety tools and equipment for factories and construction projects'; ?></p>
                </div>
            </div>

            <!-- كرت الخدمة 6 -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="600">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3><?php echo ($lang == 'ar') ? 'مستلزمات أمن وسلامة معتمدة' : 'Certified Safety & Security Supplies'; ?></h3>
                    <p><?php echo ($lang == 'ar') ? 'مستلزمات معتمدة محلياً ودولياً تلبي أعلى معايير الجودة' : 'Locally and internationally certified supplies meeting the highest quality standards'; ?></p>
                </div>
            </div>
        </div>
      </div>
    </section>

    <!-- قسم "لماذا تختارنا" -->
    <section class="why-choose-professional" id="why-choose">
        <!-- خلفية متدرجة أنيقة -->
        <div class="professional-bg">
            <div class="bg-gradient"></div>
            <div class="bg-pattern"></div>
        </div>

        <div class="container">
            <!-- العنوان الرئيسي -->
            <div class="section-header-professional" data-aos="fade-up">
                <div class="professional-badge">
                    <span class="badge-icon">✓</span>
                    <span class="badge-text"><?php echo ($lang == 'ar') ? 'التميز في كل تفصيل' : 'Excellence in Every Detail'; ?></span>
                </div>
                <h2 class="professional-title">
                    <span class="title-main"><?php echo ($lang == 'ar') ? 'لماذا نحن <em>الخيار الأول</em> للأمن والسلامة؟' : 'Why We Are The <em>First Choice</em> for Safety & Security?'; ?></span>
                </h2>
                <p class="professional-subtitle">
                    <?php echo ($lang == 'ar') ? 'نحن نؤمن بأن الجودة ليست مجرد شعار، بل التزام نلتزم به في كل مشروع وكل منتج' : 'We believe that quality is not just a slogan, but a commitment we uphold in every project and product'; ?>
                </p>
            </div>

            <!-- الميزات الرئيسية -->
            <div class="features-professional">
                <!-- الصف العلوي -->
                <div class="feature-row top-row">
                    <!-- بطاقة 1 -->
                    <div class="feature-card-pro" data-aos="fade-right" data-aos-delay="100">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 17L12 22L22 17" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 12L12 17L22 12" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'خبرة عقد من الزمان' : 'A Decade of Expertise'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'أكثر من 10 سنوات من الخبرة المتخصصة في مجال الأمن والسلامة' : 'Over 10 years of specialized experience in security and safety'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">10+</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'سنوات نجاح' : 'Years of Success'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>

                    <!-- بطاقة 2 -->
                    <div class="feature-card-pro" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#144734" stroke-width="2"/>
                                    <path d="M19.4 15C19.2663 15.3031 19.133 15.6063 19 15.9094C18.4 17.2094 17.8 18.5094 17.2 19.8094C16.9895 20.294 16.495 20.6 16 20.6C15.505 20.6 15.0105 20.294 14.8 19.8094C14.2 18.5094 13.6 17.2094 13 15.9094C12.4 17.2094 11.8 18.5094 11.2 19.8094C10.9895 20.294 10.495 20.6 10 20.6C9.505 20.6 9.0105 20.294 8.8 19.8094C8.2 18.5094 7.6 17.2094 7 15.9094C6.86699 15.6063 6.73366 15.3031 6.6 15" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 2V4" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 20V22" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 12H2" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M22 12H20" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'جودة عالمية' : 'Global Quality Standards'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'منتجات معتمدة محلياً ودولياً بمواصفات تضمن أعلى درجات الأمان' : 'Locally and internationally certified products with specifications ensuring highest safety levels'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">100%</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'جودة مضمونة' : 'Quality Guaranteed'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>

                    <!-- بطاقة 3 -->
                    <div class="feature-card-pro" data-aos="fade-left" data-aos-delay="300">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'فريق احترافي' : 'Professional Team'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'كادر فني متخصص مدرب على أعلى المستويات للتعامل مع جميع التحديات' : 'Specialized technical staff trained to the highest levels to handle all challenges'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">50+</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'متخصص معتمد' : 'Certified Specialists'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>
                </div>

                <!-- الصف السفلي -->
                <div class="feature-row bottom-row">
                    <!-- بطاقة 4 -->
                    <div class="feature-card-pro" data-aos="fade-right" data-aos-delay="400">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#144734" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 6V12L16 14" stroke="#144734" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'دعم مستمر 24/7' : '24/7 Continuous Support'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'خدمات ما بعد البيع متاحة على مدار الساعة لضمان استمرارية العمل' : 'After-sales services available around the clock to ensure business continuity'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">24/7</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'دعم فني' : 'Technical Support'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>

                    <!-- بطاقة 5 -->
                    <div class="feature-card-pro" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 16V8C20.9996 7.64927 20.9071 7.30481 20.7315 7.00116C20.556 6.69751 20.3037 6.44536 20 6.27L13 2.27C12.696 2.09446 12.3511 2.00205 12 2.00205C11.6489 2.00205 11.304 2.09446 11 2.27L4 6.27C3.69626 6.44536 3.44398 6.69751 3.26846 7.00116C3.09294 7.30481 3.00036 7.64927 3 8V16C3.00036 16.3507 3.09294 16.6952 3.26846 16.9988C3.44398 17.3025 3.69626 17.5546 4 17.73L11 21.73C11.304 21.9055 11.6489 21.9979 12 21.9979C12.3511 21.9979 12.696 21.9055 13 21.73L20 17.73C20.3037 17.5546 20.556 17.3025 20.7315 16.9988C20.9071 16.6952 20.9996 16.3507 21 16Z" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3.27 6.96L12 12.01L20.73 6.96" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22.08V12" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'حلول متكاملة' : 'Integrated Solutions'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'خدمات شاملة من التوريد حتى الصيانة من مصدر واحد' : 'Comprehensive services from supply to maintenance from a single source'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">6</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'خدمات رئيسية' : 'Core Services'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>

                    <!-- بطاقة 6 -->
                    <div class="feature-card-pro" data-aos="fade-left" data-aos-delay="600">
                        <div class="feature-card-inner">
                            <div class="feature-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 1V23" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6" stroke="#144734" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo ($lang == 'ar') ? 'أسعار تنافسية' : 'Competitive Pricing'; ?></h3>
                                <p class="feature-desc"><?php echo ($lang == 'ar') ? 'عروض متميزة تناسب جميع الميزانيات مع الحفاظ على أعلى معايير الجودة' : 'Premium offers suitable for all budgets while maintaining highest quality standards'; ?></p>
                                <div class="feature-stat">
                                    <span class="stat-value">⭐</span>
                                    <span class="stat-label"><?php echo ($lang == 'ar') ? 'أفضل قيمة' : 'Best Value'; ?></span>
                                </div>
                            </div>
                            <div class="feature-hover-effect"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شهادة الجودة -->
            <div class="quality-certificate" data-aos="fade-up" data-aos-delay="700">
                <div class="certificate-content">
                    <div class="certificate-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none">
                            <path d="M9 12L11 14L15 10M12 3C13.1819 3 14.3522 3.23279 15.4442 3.68508C16.5361 4.13738 17.5282 4.80031 18.364 5.63604C19.1997 6.47177 19.8626 7.46392 20.3149 8.55585C20.7672 9.64778 21 10.8181 21 12C21 13.1819 20.7672 14.3522 20.3149 15.4442C19.8626 16.5361 19.1997 17.5282 18.364 18.364C17.5282 19.1997 16.5361 19.8626 15.4442 20.3149C14.3522 20.7672 13.1819 21 12 21C10.8181 21 9.64778 20.7672 8.55585 20.3149C7.46392 19.8626 6.47177 19.1997 5.63604 18.364C4.80031 17.5282 4.13738 16.5361 3.68508 15.4442C3.23279 14.3522 3 13.1819 3 12C3 9.61305 3.94821 7.32387 5.63604 5.63604C7.32387 3.94821 9.61305 3 12 3Z" stroke="#e76a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="certificate-text">
                        <h3><?php echo ($lang == 'ar') ? 'شريكك الموثوق في الأمن والسلامة' : 'Your Trusted Safety & Security Partner'; ?></h3>
                        <p><?php echo ($lang == 'ar') ? 'نحن لا نبيع منتجات، نقدم حلولاً واستثماراً في أمان عملك' : 'We don\'t sell products, we provide solutions and invest in the security of your business'; ?></p>
                    </div>
                </div>
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

        document.querySelectorAll('a, button, .btn, .service-card, .stats-card-services, .feature-card-pro').forEach(el => {
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
      const cards = document.querySelectorAll('.service-card, .stats-card-services, .feature-card-pro');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          if (this.classList.contains('feature-card-pro')) {
            this.style.transform = 'scale(1.02)';
          } else {
            this.style.transform = 'translateY(-25px) scale(1.03)';
          }
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // تأثيرات hover للبطاقات الاحترافية
      document.querySelectorAll('.feature-card-pro').forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'scale(1)';
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

      // تأثيرات الظهور التدريجي للبطاقات الاحترافية
      const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      document.querySelectorAll('.feature-card-pro').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
      });
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