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

// جلب البيانات من قاعدة البيانات
$categories = $query->select('category');
$products = $query->select('products');
$product_images = $query->select('product_images');
$statistics = $query->select('statistics');

// الحصول على أسماء الفئات للتصفية
$category_names = [];
foreach ($categories as $category) {
    $category_names[$category['id']] = $category['category_name'];
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? 'منتجاتنا - ركن الأماسي' : 'Our Products - Rukn Alamasy'; ?></title>
  <meta name="description" content="<?php echo ($lang == 'ar') ? 'اكتشف مجموعتنا المتميزة من منتجات الأمن والسلامة عالية الجودة' : 'Discover our premium collection of high-quality safety and security products'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'منتجات، أمن، سلامة، معدات حماية، تجهيزات' : 'products, security, safety, protection equipment, supplies'; ?>">
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
    /* نفس الـ CSS من صفحة About مع تعديلات لصفحة المنتجات */
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
      text-align: center;
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
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .loader h3{
      font-size: 1.5rem;
      margin-top: 1rem;
      text-transform: uppercase;
      background: linear-gradient(135deg, #ffffff 0%, #e76a04 50%, #e76a04 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      animation: title-glow 3s infinite alternate;
      position: relative;
      line-height: 1.5;
    }
    
    @keyframes title-glow {
      0% { filter: brightness(1); }
      100% { filter: brightness(1.3); }
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
    .products-hero-section {
      height: 40vh;
      min-height: 300px;
      position: relative;
      overflow: hidden;
      background: var(--gradient-dark);
    }

    #particles-js-products {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      pointer-events: none;
    }

    .products-hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      color: var(--white);
      padding: 0 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }

    .products-hero-title {
      font-size: 4rem;
      font-weight: 900;
      margin-bottom: 25px;
      margin-top : 50px ;
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

    .products-hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
      line-height: 1.8;
      color: rgba(255, 255, 255, 0.9);
    }

    /* Filters Section */
    .filters-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      position: relative;
    }

    #particles-js-filters {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      pointer-events: none;
    }

    .filter-buttons-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 30px;
    }

    .filter-btn-product {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--white);
      border: 2px solid rgba(231, 106, 4, 0.2);
      padding: 15px 30px;
      border-radius: 50px;
      font-weight: 600;
      color: var(--text-dark);
      cursor: pointer;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
      border: none;
      outline: none;
    }

    .filter-btn-product::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      background: var(--gradient-primary);
      transition: width 0.4s ease;
      z-index: -1;
    }

    .filter-btn-product:hover::before,
    .filter-btn-product.active::before {
      width: 100%;
    }

    .filter-btn-product:hover,
    .filter-btn-product.active {
      color: var(--white);
      border-color: var(--primary-color);
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(231, 106, 4, 0.2);
    }

    .products-count {
      background: rgba(231, 106, 4, 0.1);
      color: var(--primary-color);
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 700;
      transition: all 0.3s ease;
    }

    .filter-btn-product:hover .products-count,
    .filter-btn-product.active .products-count {
      background: rgba(255, 255, 255, 0.3);
      color: var(--white);
    }

    /* Products Grid Section */
    .products-grid-section {
      padding: 100px 0;
      background: transparent;
      position: relative;
      overflow: hidden;
    }

    #particles-js-products-grid {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      pointer-events: none;
    }

    .product-card {
      background: var(--white);
      border-radius: 25px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      margin-bottom: 30px;
      border: 1px solid rgba(231, 106, 4, 0.1);
    }

    .product-card::before {
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

    .product-card:hover::before {
      transform: scaleX(1);
    }

    .product-card:hover {
      transform: translateY(-25px) scale(1.03);
      box-shadow: 0 40px 80px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.3);
    }

    .product-image-container {
      position: relative;
      height: 280px;
      overflow: hidden;
    }

    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.8s ease;
    }

    .product-card:hover .product-image {
      transform: scale(1.1);
    }

    .product-badge {
      position: absolute;
      top: 20px;
      right: 20px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
      z-index: 2;
    }

    .product-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(20, 71, 52, 0.9), transparent);
      padding: 30px;
      color: var(--white);
      transform: translateY(100%);
      transition: transform 0.5s ease;
    }

    .product-card:hover .product-overlay {
      transform: translateY(0);
    }

    .product-content {
      padding: 30px;
    }

    .product-category {
      display: inline-block;
      background: rgba(231, 106, 4, 0.1);
      color: var(--primary-color);
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .product-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 15px;
      line-height: 1.4;
    }

    .product-description {
      color: var(--text-light);
      font-size: 1rem;
      line-height: 1.7;
      margin-bottom: 20px;
      min-height: 80px;
    }

    .product-price {
      font-size: 1.8rem;
      font-weight: 900;
      color: var(--primary-color);
      margin-bottom: 25px;
    }

    .product-actions {
      display: flex;
      gap: 15px;
    }

    .btn-view-details {
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 15px 25px;
      border-radius: 15px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: none;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-view-details::before {
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

    .btn-view-details:hover::before {
      width: 100%;
    }

    .btn-view-details:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(231, 106, 4, 0.3);
    }

    /* Statistics Section */
    .stats-section-products {
      padding: 120px 0;
      background: linear-gradient(135deg, #0d3b28 0%, var(--dark-color) 100%);
      position: relative;
      overflow: hidden;
    }

    #particles-js-stats-products {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

    .stats-card-products {
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

    .stats-card-products::before {
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

    .stats-card-products:hover::before {
      opacity: 0.1;
    }

    .stats-card-products:hover {
      transform: translateY(-15px);
      border-color: rgba(231, 106, 4, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .stats-icon-products {
      font-size: 3rem;
      color: var(--light-color);
      margin-bottom: 25px;
      filter: drop-shadow(0 5px 15px rgba(231, 106, 4, 0.3));
    }

    .stats-number-products {
      font-size: 4rem;
      font-weight: 900;
      color: white;
      margin-bottom: 15px;
      text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
    }

    .stats-title-products {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .stats-description-products {
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

    /* CTA Section */
    .cta-section-products {
      padding: 120px 0;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      position: relative;
      overflow: hidden;
    }

    #particles-js-cta-products {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

    .cta-content-products {
      position: relative;
      z-index: 2;
      text-align: center;
      color: white;
    }

    .cta-title-products {
      font-size: 3rem;
      font-weight: 900;
      margin-bottom: 30px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .cta-description-products {
      font-size: 1.3rem;
      margin-bottom: 50px;
      opacity: 0.9;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Buttons */
    .btn-view-all-products {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--gradient-dark);
      color: white;
      padding: 18px 45px;
      border-radius: 60px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: none;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
      z-index: 1;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-view-all-products::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      background: var(--gradient-primary);
      transition: width 0.4s ease;
      z-index: -1;
    }

    .btn-view-all-products:hover::before {
      width: 100%;
    }

    .btn-view-all-products:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 25px 50px rgba(231, 106, 4, 0.4);
    }

    .btn-primary-products {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--white);
      color: var(--primary-color);
      padding: 18px 45px;
      border-radius: 60px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      border: 3px solid var(--white);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
      z-index: 1;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-primary-products::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      background: var(--gradient-primary);
      transition: width 0.4s ease;
      z-index: -1;
    }

    .btn-primary-products:hover::before {
      width: 100%;
    }

    .btn-primary-products:hover {
      color: var(--white);
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 25px 50px rgba(255, 255, 255, 0.2);
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

    /* No Products Message */
    .no-products {
      text-align: center;
      padding: 100px 0;
    }

    .no-products i {
      font-size: 5rem;
      color: var(--text-light);
      margin-bottom: 30px;
    }

    .no-products h3 {
      color: var(--text-dark);
      margin-bottom: 20px;
    }

    .no-products p {
      color: var(--text-light);
      font-size: 1.1rem;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .products-hero-title {
        font-size: 3.2rem;
      }
      
      .section-title {
        font-size: 3rem;
      }
      
      .product-title {
        font-size: 1.3rem;
      }
    }

    @media (max-width: 992px) {
      .products-hero-title {
        font-size: 2.8rem;
      }
      
      .products-hero-subtitle {
        font-size: 1.3rem;
      }
      
      .section-title {
        font-size: 2.5rem;
      }
      
      .filter-buttons-container {
        gap: 10px;
      }
      
      .filter-btn-product {
        padding: 12px 25px;
        font-size: 0.9rem;
      }
      
      .product-image-container {
        height: 220px;
      }
    }

    @media (max-width: 768px) {
      .products-hero-title {
        font-size: 2.2rem;
      }
      
      .products-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .section-subtitle {
        font-size: 1.1rem;
      }
      
      .filter-buttons-container {
        flex-direction: column;
        align-items: center;
      }
      
      .filter-btn-product {
        width: 80%;
        justify-content: center;
      }
      
      .product-actions {
        flex-direction: column;
      }
      
      .cta-title-products {
        font-size: 2.2rem;
      }
      
      .products-hero-section {
        margin-top: 70px;
        height: 35vh;
        min-height: 250px;
      }
    }

    @media (max-width: 576px) {
      .products-hero-section {
        height: 30vh;
        min-height: 220px;
      }
      
      .products-hero-title {
        font-size: 1.8rem;
      }
      
      .section-title {
        font-size: 1.8rem;
      }
      
      .section-badge {
        padding: 10px 20px;
        font-size: 0.9rem;
      }
      
      .product-content {
        padding: 20px;
      }
      
      .cta-title-products {
        font-size: 1.8rem;
      }
      
      .scroll-top {
        left: 20px;
        bottom: 20px;
        width: 50px;
        height: 50px;
      }
    }

    /* Canvas styles */
    #particles-js-products canvas,
    #particles-js-filters canvas,
    #particles-js-products-grid canvas,
    #particles-js-stats-products canvas,
    #particles-js-cta-products canvas {
      display: block;
      vertical-align: bottom;
      transform: translate3d(0, 0, 0);
    }

    /* Animation for product items */
    .product-item {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.4s ease;
    }

    .product-item.show {
      opacity: 1;
      transform: translateY(0);
    }

    /* CTA Buttons Container */
    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    
    .cta-buttons a {
      margin: 5px 0;
    }

    /* تحسينات للفلترة */
    .product-filtered {
      display: block !important;
    }

    .product-hidden {
      display: none !important;
    }

    /* تنسيق لغة RTL */
    html[dir="rtl"] .scroll-top {
      left: auto;
      right: 40px;
    }
    
    html[dir="rtl"] .product-badge {
      right: auto;
      left: 20px;
    }
    
    html[dir="rtl"] .section-title::after {
      left: auto;
      right: 50%;
      transform: translateX(50%);
    }

    @media (max-width: 576px) {
      html[dir="rtl"] .scroll-top {
        right: 20px;
      }
    }
  </style>
</head>

<body class="products-page">
   <!-- Loading Screen -->
  <div class="loading-screen">
    <div class="loader">
      <div class="loader-diamond"></div>
      <h3>ركن الأماسي</h3>
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
  <div id="particles-js-products"></div>

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Hero Section -->
    <section class="products-hero-section">
      <div class="container">
        <div class="products-hero-content fade-in-up">
          <h1 class="products-hero-title animate__animated animate__fadeInDown"><?php echo ($lang == 'ar') ? 'منتجاتنا' : 'Our Products'; ?></h1>
          <p class="products-hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            <?php echo ($lang == 'ar') ? 'اكتشف مجموعتنا المتميزة من منتجات الأمن والسلامة عالية الجودة التي تواكب أحدث المعايير العالمية' : 'Discover our premium collection of high-quality safety and security products that keep up with the latest international standards'; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
      <div id="particles-js-filters"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-filter"></i>
            <?php echo ($lang == 'ar') ? 'تصفية المنتجات' : 'Filter Products'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'تصفح منتجاتنا حسب الفئة' : 'Browse Our Products by Category'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اختر الفئة التي تناسب احتياجاتك واستعرض أفضل المنتجات' : 'Choose the category that suits your needs and browse the best products'; ?></p>
        </div>

        <div class="filter-buttons-container" data-aos="fade-up" data-aos-delay="100">
          <button class="filter-btn-product active" data-filter="all">
            <i class="bi bi-grid-3x3-gap"></i>
            <?php echo ($lang == 'ar') ? 'جميع المنتجات' : 'All Products'; ?>
            <span class="products-count"><?php echo count($products); ?></span>
          </button>
          
          <?php foreach ($categories as $category): 
            $category_count = 0;
            foreach ($products as $product) {
              if ($product['category_id'] == $category['id']) {
                $category_count++;
              }
            }
          ?>
            <button class="filter-btn-product" data-filter="category-<?php echo $category['id']; ?>">
              <?php if (!empty($category['icon'])): ?>
                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
              <?php else: ?>
                <i class="bi bi-box"></i>
              <?php endif; ?>
              <?php echo htmlspecialchars($category['category_name']); ?>
              <?php if ($category_count > 0): ?>
                <span class="products-count"><?php echo $category_count; ?></span>
              <?php endif; ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Products Grid Section -->
    <section class="products-grid-section bg-pattern">
      <div id="particles-js-products-grid"></div>
      
      <div class="container">
        <div class="row" id="products-container">
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
              <?php
              $image_url = 'default-product.jpg';
              foreach ($product_images as $image) {
                if ($image['product_id'] == $product['id']) {
                  $image_url = $image['image_url'];
                  break;
                }
              }
              
              $category_name = isset($category_names[$product['category_id']]) ? 
                $category_names[$product['category_id']] : ($lang == 'ar' ? 'غير مصنف' : 'Uncategorized');
              ?>
              
              <div class="col-xl-3 col-lg-4 col-md-6 mb-4 product-item" 
                   data-category="category-<?php echo $product['category_id']; ?>"
                   data-aos="fade-up" data-aos-delay="<?php echo ($index % 4) * 100; ?>">
                <div class="product-card">
                  <div class="product-image-container">
                    <img src="assets/img/product/<?php echo htmlspecialchars($image_url); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="product-image"
                         onerror="this.src='assets/img/default-product.jpg'">
                    <div class="product-badge"><?php echo ($lang == 'ar') ? 'جديد' : 'New'; ?></div>
                    <div class="product-overlay">
                      <p><?php echo ($lang == 'ar') ? 'منتج عالي الجودة' : 'High Quality Product'; ?></p>
                    </div>
                  </div>
                  
                  <div class="product-content">
                    <div class="product-category"><?php echo htmlspecialchars($category_name); ?></div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="product-description">
                      <?php echo htmlspecialchars(mb_substr($product['description'], 0, 80)) . (strlen($product['description']) > 80 ? '...' : ''); ?>
                    </p>
                    <div class="product-price">
                      <?php echo ($lang == 'ar') ? 'ر.س ' : 'SAR '; ?><?php echo number_format($product['price'], 0, '', ' '); ?>
                    </div>
                    <div class="product-actions">
                      <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view-details">
                        <i class="bi bi-eye"></i>
                        <?php echo ($lang == 'ar') ? 'عرض التفاصيل' : 'View Details'; ?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12">
              <div class="no-products text-center py-5" data-aos="fade-up">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h3 class="text-muted"><?php echo ($lang == 'ar') ? 'لا توجد منتجات متاحة' : 'No Products Available'; ?></h3>
                <p class="text-muted"><?php echo ($lang == 'ar') ? 'سنضيف منتجات جديدة قريباً' : 'We will add new products soon'; ?></p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section-products">
      <div id="particles-js-cta-products"></div>
      
      <div class="container">
        <div class="cta-content-products" data-aos="fade-up">
          <h2 class="cta-title-products"><?php echo ($lang == 'ar') ? 'هل تبحث عن منتجات أمنية متخصصة؟' : 'Looking for Specialized Security Products?'; ?></h2>
          <p class="cta-description-products">
            <?php echo ($lang == 'ar') ? 'فريقنا من الخبراء مستعد لمساعدتك في اختيار أفضل منتجات الأمن والسلامة لمنشآتك. تواصل معنا اليوم للحصول على استشارة مجانية.' : 'Our team of experts is ready to help you choose the best safety and security products for your facilities. Contact us today for a free consultation.'; ?>
          </p>
          <div class="cta-buttons">
            <a href="contact.php" class="btn-view-all-products pulse">
              <i class="bi bi-telephone"></i>
              <?php echo ($lang == 'ar') ? 'تواصل معنا الآن' : 'Contact Us Now'; ?>
            </a>
            <a href="services.php" class="btn-primary-products">
              <i class="bi bi-gear"></i>
              <?php echo ($lang == 'ar') ? 'استكشف خدماتنا' : 'Explore Our Services'; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
      console.log('صفحة المنتجات جاهزة!');
      
      // Remove loading screen
      setTimeout(() => {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
          loadingScreen.style.opacity = '0';
          loadingScreen.style.visibility = 'hidden';
        }
      }, 1500);
      
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
        particlesJS('particles-js-products', {
          particles: {
            number: { value: 80 },
            color: { value: ["#e76a04", "#f3d417", "#ffffff"] },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: true },
            size: { value: 3, random: true },
            move: { enable: true, speed: 1 }
          }
        });
        
        particlesJS('particles-js-filters', {
          particles: {
            number: { value: 60 },
            color: { value: "#144734" },
            opacity: { value: 0.2 },
            size: { value: 4 },
            move: { enable: true, speed: 1 }
          }
        });
        
        particlesJS('particles-js-products-grid', {
          particles: {
            number: { value: 80 },
            color: { value: "#e76a04" },
            opacity: { value: 0.3 },
            size: { value: 3 },
            line_linked: { enable: true, distance: 150, opacity: 0.2 },
            move: { enable: true, speed: 2 }
          }
        });
      }

      // تأثيرات المؤشر
      const cursor = document.querySelector('.cursor-effect');
      if (cursor) {
        document.addEventListener('mousemove', (e) => {
          cursor.style.left = e.clientX + 'px';
          cursor.style.top = e.clientY + 'px';
        });
      }

      // فلترة المنتجات - الكود المصحح بشكل نهائي
      const filterButtons = document.querySelectorAll('.filter-btn-product');
      const productItems = document.querySelectorAll('.product-item');
      
      console.log('عدد أزرار الفلترة:', filterButtons.length);
      console.log('عدد المنتجات:', productItems.length);
      
      // إضافة show class لجميع المنتجات في البداية
      productItems.forEach(item => {
        item.classList.add('show');
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
      });

      // دالة الفلترة
      function filterProducts(filterValue) {
        console.log('التصفية حسب:', filterValue);
        
        productItems.forEach(item => {
          const itemCategory = item.getAttribute('data-category');
          
          if (filterValue === 'all' || filterValue === itemCategory) {
            // إظهار المنتج
            item.style.display = 'block';
            setTimeout(() => {
              item.classList.add('show');
              item.classList.remove('product-hidden');
              item.classList.add('product-filtered');
              item.style.opacity = '1';
              item.style.transform = 'translateY(0)';
            }, 10);
          } else {
            // إخفاء المنتج
            item.classList.remove('show');
            item.classList.add('product-hidden');
            item.classList.remove('product-filtered');
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
              item.style.display = 'none';
            }, 400);
          }
        });
        
        // إعادة تنشيط AOS بعد الفلترة
        setTimeout(() => {
          if (typeof AOS !== 'undefined') {
            AOS.refresh();
          }
        }, 500);
      }

      // إضافة event listeners لأزرار الفلترة
      filterButtons.forEach(button => {
        button.addEventListener('click', function() {
          console.log('تم النقر على زر الفلترة');
          
          // إزالة active من جميع الأزرار
          filterButtons.forEach(btn => {
            btn.classList.remove('active');
          });
          
          // إضافة active للزر المحدد
          this.classList.add('active');
          
          // الحصول على قيمة التصفية
          const filterValue = this.getAttribute('data-filter');
          
          // تطبيق الفلترة
          filterProducts(filterValue);
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
      document.querySelectorAll('.btn-view-details, .btn-view-all-products, .btn-primary-products, .filter-btn-product').forEach(btn => {
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
          
          this.style.position = 'relative';
          this.style.overflow = 'hidden';
          this.appendChild(ripple);
          
          setTimeout(() => {
            if (ripple.parentNode === this) {
              this.removeChild(ripple);
            }
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

      // تأثيرات hover للمنتجات
      const productCards = document.querySelectorAll('.product-card');
      productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-25px) scale(1.03)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // Floating Particles Animation
      function animateParticles() {
        const particles = document.querySelectorAll('.floating-particles .particle');
        particles.forEach((particle, index) => {
          const size = Math.random() * 20 + 5;
          const left = Math.random() * 100;
          const top = Math.random() * 100;
          const delay = index * 0.5;
          
          particle.style.width = `${size}px`;
          particle.style.height = `${size}px`;
          particle.style.left = `${left}%`;
          particle.style.top = `${top}%`;
          particle.style.animationDelay = `${delay}s`;
          particle.style.opacity = Math.random() * 0.3 + 0.1;
        });
      }

      animateParticles();
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        if (this.getAttribute('href') !== '#') {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        }
      });
    });
    
    // إضافة event listener لتحميل الصفحة بالكامل
    window.addEventListener('load', function() {
      console.log('تم تحميل الصفحة بالكامل');
      
      // إعادة تهيئة AOS بعد تحميل الصفحة
      if (typeof AOS !== 'undefined') {
        AOS.refresh();
      }
    });
  </script>
</body>
</html>