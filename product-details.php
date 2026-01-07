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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = new Database();
$product = $query->getById('products', $id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$category = isset($product['category_id']) ? $query->getById('category', $product['category_id']) : null;
$category_name = $category ? $category['category_name'] : ($lang == 'ar' ? 'غير مصنف' : 'Uncategorized');

$product_images = $query->executeQuery("SELECT image_url FROM product_images WHERE product_id = $id");
$product_images = !empty($product_images) ? array_column($product_images, 'image_url') : ['default-product.jpg'];

// الحصول على منتجات ذات صلة (نفس الفئة)
$related_products = [];
if ($product['category_id']) {
    $related_products = $query->executeQuery("SELECT p.* FROM products p WHERE p.category_id = {$product['category_id']} AND p.id != $id LIMIT 4");
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? htmlspecialchars($product['product_name']) . ' - ركن الأماسي' : htmlspecialchars($product['product_name']) . ' - Rukn Alamasy'; ?></title>
  <meta name="description" content="<?php echo htmlspecialchars(mb_substr($product['description'], 0, 160)); ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? htmlspecialchars($product['product_name']) . ', منتجات أمن, سلامة, معدات حماية' : htmlspecialchars($product['product_name']) . ', security products, safety equipment, protection'; ?>">
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
    /* نفس الـ CSS من صفحة About مع تعديلات لصفحة تفاصيل المنتج */
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
  text-align : center ;
}

.loader-diamond {
  width: 100%;
  height: 100%;
  background: var(--gradient-primary);
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
  animation: loader-spin 2s infinite linear;
  filter: drop-shadow(0 0 20px rgba(231, 106, 4, 0.5));
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
  line-height : 1.5;
  
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

    /* Hero Section من صفحة About */
    .about-hero-section {
      height: 40vh;
      min-height: 300px;
      position: relative;
      overflow: hidden;
      background: var(--gradient-dark);
    }

    #particles-js-about {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      pointer-events: none;
    }

    .about-hero-content {
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

    .about-hero-title {
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

    .about-hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
      line-height: 1.8;
    }

    /* قسم breadcrumb-nav كما هو في الكود القديم */
    .breadcrumb-nav {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 20px 0;
      box-shadow: 0 2px 20px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .breadcrumb {
      margin: 0;
      background: none;
      padding: 0;
    }

    .breadcrumb-item a {
      color: var(--text-light);
      text-decoration: none;
      transition: var(--transition);
      font-weight: 500;
    }

    .breadcrumb-item a:hover {
      color: var(--primary-color);
      transform: translateX(5px);
    }

    .breadcrumb-item.active {
      color: var(--primary-color);
      font-weight: 700;
    }

    /* Product Details Section بتصميم About */
    .product-details-section {
      padding: 100px 0;
      background: transparent;
      position: relative;
      overflow: hidden;
    }

    #particles-js-details {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      pointer-events: none;
    }

    .product-gallery {
      background: white;
      border-radius: 25px;
      padding: 50px 35px;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(231, 106, 4, 0.1);
      text-align: center;
    }

    .product-gallery::before {
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

    .product-gallery:hover::before {
      transform: scaleX(1);
    }

    .product-gallery:hover {
      transform: translateY(-15px);
      box-shadow: 0 40px 80px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.3);
    }

    .main-image {
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: var(--transition);
    }

    .main-image:hover {
      transform: scale(1.02);
    }

    .main-image img {
      width: 100%;
      height: 450px;
      object-fit: cover;
      border-radius: 15px;
    }

    .thumbnail-gallery {
      display: flex;
      gap: 12px;
      overflow-x: auto;
      padding: 15px 5px;
      scrollbar-width: thin;
    }

    .thumbnail-gallery::-webkit-scrollbar {
      height: 6px;
    }

    .thumbnail-gallery::-webkit-scrollbar-thumb {
      background: var(--primary-color);
      border-radius: 10px;
    }

    .thumbnail {
      width: 90px;
      height: 90px;
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      border: 3px solid transparent;
      transition: var(--transition);
      flex-shrink: 0;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .thumbnail.active {
      border-color: var(--primary-color);
      transform: scale(1.1);
    }

    .thumbnail:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(231, 106, 4, 0.3);
    }

    .thumbnail img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-info {
      background: white;
      border-radius: 25px;
      padding: 50px 40px;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(231, 106, 4, 0.1);
    }

    .product-info::before {
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

    .product-info:hover::before {
      transform: scaleX(1);
    }

    .product-info:hover {
      transform: translateY(-15px);
      box-shadow: 0 40px 80px rgba(231, 106, 4, 0.15);
      border-color: rgba(231, 106, 4, 0.3);
    }

    .product-badge {
      background: var(--gradient-primary);
      color: white;
      padding: 12px 30px;
      border-radius: 60px;
      font-weight: 700;
      font-size: 1rem;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 25px;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .product-badge::before {
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

    .product-title {
      font-size: 2.5rem;
      font-weight: 900;
      color: var(--dark-color);
      margin-bottom: 25px;
      line-height: 1.3;
    }

    .product-category {
      color: var(--primary-color);
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .product-price {
      font-size: 3rem;
      font-weight: 900;
      color: var(--primary-color);
      margin-bottom: 30px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .product-description {
      color: var(--text-light);
      font-size: 1.15rem;
      line-height: 1.8;
      margin-bottom: 30px;
      padding: 25px;
      background: rgba(248, 249, 250, 0.5);
      border-radius: 15px;
      border-left: 4px solid var(--primary-color);
    }

    .action-buttons {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    .btn-primary-custom {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 18px 35px;
      border-radius: 15px;
      font-weight: 700;
      transition: var(--transition);
      text-decoration: none;
      box-shadow: 0 8px 25px rgba(231, 106, 4, 0.3);
      font-size: 1.1rem;
      border: none;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-primary-custom::before {
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

    .btn-primary-custom:hover::before {
      width: 100%;
    }

    .btn-primary-custom:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 15px 40px rgba(231, 106, 4, 0.4);
    }

    .btn-outline-custom {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: transparent;
      color: var(--dark-color);
      border: 2px solid var(--dark-color);
      padding: 18px 35px;
      border-radius: 15px;
      font-weight: 700;
      transition: var(--transition);
      text-decoration: none;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-outline-custom::before {
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

    .btn-outline-custom:hover::before {
      width: 100%;
    }

    .btn-outline-custom:hover {
      color: var(--white);
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(20, 71, 52, 0.2);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
      padding-top: 30px;
      border-top: 1px solid rgba(231, 106, 4, 0.1);
    }

    .feature-card {
      background: linear-gradient(135deg, #ffffff, #f8f9fa);
      padding: 25px 20px;
      border-radius: 15px;
      text-align: center;
      transition: var(--transition);
      border: 1px solid rgba(231, 106, 4, 0.1);
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .feature-icon {
      width: 70px;
      height: 70px;
      background: var(--gradient-primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      color: white;
      font-size: 1.8rem;
      transition: var(--transition);
      box-shadow: 0 10px 20px rgba(231, 106, 4, 0.2);
    }

    .feature-card:hover .feature-icon {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 15px 30px rgba(231, 106, 4, 0.3);
    }

    .feature-card h5 {
      color: var(--text-dark);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .feature-card p {
      color: var(--text-light);
      font-size: 0.9rem;
      margin: 0;
    }

    .specifications {
      background: white;
      border-radius: 25px;
      padding: 50px 40px;
      margin-top: 40px;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
      transition: all 0.5s ease;
      border: 1px solid rgba(231, 106, 4, 0.1);
    }

    .specifications:hover {
      transform: translateY(-10px);
      box-shadow: 0 40px 80px rgba(0, 0, 0, 0.15);
    }

    .spec-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
      border-bottom: 1px solid rgba(231, 106, 4, 0.1);
    }

    .spec-item:last-child {
      border-bottom: none;
    }

    .spec-label {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 1.1rem;
    }

    .spec-value {
      color: var(--text-light);
      font-weight: 500;
      font-size: 1.1rem;
    }

    .related-products {
      padding: 100px 0;
      background: var(--gradient-dark);
      position: relative;
      overflow: hidden;
    }

    #particles-js-related {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 1;
      pointer-events: none;
    }

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

    .section-title {
      font-size: 2.8rem;
      font-weight: 900;
      color: white;
      margin-bottom: 25px;
      position: relative;
      display: inline-block;
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
      color: rgba(255, 255, 255, 0.9);
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.9;
    }

    .btn-view-details {
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
      width: 100%;
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

    /* Responsive Design */
    @media (max-width: 1200px) {
      .about-hero-title {
        font-size: 3.2rem;
      }
      
      .product-title {
        font-size: 2.2rem;
      }
      
      .product-price {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 992px) {
      .about-hero-title {
        font-size: 2.8rem;
      }
      
      .about-hero-subtitle {
        font-size: 1.3rem;
      }
      
      .product-gallery {
        margin-bottom: 30px;
      }
      
      .product-info {
        padding: 40px 30px;
      }
      
      .action-buttons {
        flex-direction: column;
      }
    }

    @media (max-width: 768px) {
      .about-hero-title {
        font-size: 2.2rem;
      }
      
      .about-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .product-title {
        font-size: 1.8rem;
      }
      
      .product-price {
        font-size: 2rem;
      }
      
      .main-image img {
        height: 300px;
      }
      
      .section-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 576px) {
      .about-hero-section {
        height: 30vh;
        min-height: 250px;
      }
      
      .about-hero-title {
        font-size: 1.8rem;
      }
      
      .product-title {
        font-size: 1.6rem;
      }
      
      .product-price {
        font-size: 1.8rem;
      }
      
      .thumbnail {
        width: 70px;
        height: 70px;
      }
      
      .btn-primary-custom, .btn-outline-custom {
        padding: 15px 25px;
        font-size: 1rem;
      }
      
      .section-title {
        font-size: 1.6rem;
      }
    }

    /* Canvas styles */
    #particles-js-about canvas,
    #particles-js-details canvas,
    #particles-js-related canvas {
      display: block;
      vertical-align: bottom;
      transform: translate3d(0, 0, 0);
    }
  </style>
</head>

<body class="product-details-page">
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
  <div id="particles-js-about"></div>

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Hero Section - نفس تصميم About -->
   

    <!-- Breadcrumb Navigation - كما هو في الكود القديم -->
    <div class="breadcrumb-nav">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./"><?php echo ($lang == 'ar') ? ' الرئيسية ' : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><a href="products.php"><?php echo ($lang == 'ar') ? ' / المنتجات' : 'Products'; ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Product Details Section -->
    <section class="product-details-section bg-pattern" id="details">
      <div id="particles-js-details"></div>
      
      <div class="container">
        <div class="row gy-5">
          <?php if ($product): ?>
            <!-- Product Gallery -->
            <div class="col-lg-6" data-aos="fade-up">
              <div class="product-gallery fade-in-up">
                <div class="main-image">
                  <img src="assets/img/product/<?php echo htmlspecialchars($product_images[0]); ?>" 
                       alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                       id="mainProductImage"
                       onerror="this.src='assets/img/default-product.jpg'">
                </div>
                <?php if (count($product_images) > 1): ?>
                <div class="thumbnail-gallery">
                  <?php foreach ($product_images as $index => $image): ?>
                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                         onclick="changeImage('assets/img/product/<?php echo htmlspecialchars($image); ?>', this)">
                      <img src="assets/img/product/<?php echo htmlspecialchars($image); ?>" 
                           alt="Thumbnail <?php echo $index + 1; ?>"
                           onerror="this.src='assets/img/default-product.jpg'">
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
              <div class="product-info fade-in-up">
                <div class="product-badge pulse floating-element">
                  <i class="bi bi-star-fill"></i>
                  <?php echo ($lang == 'ar') ? 'منتج مميز' : 'Premium Product'; ?>
                </div>
                
                <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                <div class="product-category">
                  <i class="bi bi-tags"></i>
                  <span><?php echo htmlspecialchars($category_name); ?></span>
                </div>

                <div class="product-price">
                  <?php echo ($lang == 'ar') ? 'ر.س ' : 'SAR '; ?><?php echo number_format($product['price'], 0, '', ' '); ?>
                </div>

                <div class="product-description">
                  <h4 style="color: var(--dark-color); margin-bottom: 15px;">
                    <i class="bi bi-info-circle"></i> <?php echo ($lang == 'ar') ? 'الوصف:' : 'Description:'; ?>
                  </h4>
                  <p style="margin: 0;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="action-buttons">
                  <a href="contact.php" class="btn-primary-custom">
                    <i class="bi bi-whatsapp"></i>
                    <?php echo ($lang == 'ar') ? 'طلب المنتج' : 'Order Product'; ?>
                  </a>
                  <a href="products.php" class="btn-outline-custom">
                    <i class="bi bi-arrow-right"></i>
                    <?php echo ($lang == 'ar') ? 'تصفح المزيد' : 'Browse More'; ?>
                  </a>
                </div>

                <!-- Features Grid -->
                <div class="features-grid">
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-shield-check"></i>
                    </div>
                    <h5><?php echo ($lang == 'ar') ? 'ضمان الجودة' : 'Quality Guarantee'; ?></h5>
                    <p><?php echo ($lang == 'ar') ? 'منتجات أصلية 100%' : '100% Original Products'; ?></p>
                  </div>
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-truck"></i>
                    </div>
                    <h5><?php echo ($lang == 'ar') ? 'توصيل سريع' : 'Fast Delivery'; ?></h5>
                    <p><?php echo ($lang == 'ar') ? 'شحن لجميع المدن' : 'Shipping to all cities'; ?></p>
                  </div>
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-headset"></i>
                    </div>
                    <h5><?php echo ($lang == 'ar') ? 'دعم فني' : 'Technical Support'; ?></h5>
                    <p><?php echo ($lang == 'ar') ? 'متواصل 24/7' : '24/7 Support'; ?></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Specifications Section -->
            

          <?php else: ?>
            <div class="col-12">
              <div class="product-not-found fade-in-up">
                <div>
                  <i class="bi bi-exclamation-triangle"></i>
                  <h3><?php echo ($lang == 'ar') ? 'المنتج غير موجود' : 'Product not found'; ?></h3>
                  <p><?php echo ($lang == 'ar') ? 'المنتج الذي تبحث عنه غير موجود.' : 'The product you are looking for does not exist.'; ?></p>
                  <a href="products.php" class="btn-primary-custom">
                    <i class="bi bi-arrow-left"></i> <?php echo ($lang == 'ar') ? 'العودة للمنتجات' : 'Back to Products'; ?>
                  </a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Related Products Section -->
    <section class="related-products">
      <div id="particles-js-related"></div>
      
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-grid-3x3-gap"></i>
            <?php echo ($lang == 'ar') ? 'منتجات ذات صلة' : 'Related Products'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'قد يعجبك أيضاً' : 'You May Also Like'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اكتشف منتجات أخرى من نفس الفئة' : 'Discover other products from the same category'; ?></p>
        </div>

        <div class="row justify-content-center">
          <div class="col-md-8 text-center">
            <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin-bottom: 30px;">
              <?php echo ($lang == 'ar') ? 'نعمل على إضافة المزيد من المنتجات المتعلقة بهذا المنتج قريباً' : 'We are working on adding more related products soon'; ?>
            </p>
            <a href="products.php" class="btn-view-details" style="max-width: 300px; margin: 0 auto; background: var(--gradient-primary); border: 2px solid white;">
              <i class="bi bi-grid"></i> <?php echo ($lang == 'ar') ? 'تصفح جميع المنتجات' : 'Browse All Products'; ?>
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

      // تأثيرات الجسيمات
      if (typeof particlesJS !== 'undefined') {
        // Hero Section Particles
        particlesJS('particles-js-about', {
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

        // Details Section Particles
        particlesJS('particles-js-details', {
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

        // Related Products Particles
        particlesJS('particles-js-related', {
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

        document.querySelectorAll('a, button, .thumbnail, .product-gallery, .product-info, .specifications, .feature-card').forEach(el => {
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

      // وظيفة تغيير الصورة الرئيسية
      window.changeImage = function(src, element) {
        const mainImage = document.getElementById('mainProductImage');
        mainImage.style.opacity = '0';
        
        setTimeout(() => {
          mainImage.src = src;
          mainImage.style.opacity = '1';
        }, 200);
        
        // إزالة النشاط من جميع الثمبنييلات
        document.querySelectorAll('.thumbnail').forEach(thumb => {
          thumb.classList.remove('active');
        });
        
        // إضافة النشاط للثمبنييل المحدد
        element.classList.add('active');
      };

      // تأثيرات hover للبطاقات
      const cards = document.querySelectorAll('.product-gallery, .product-info, .specifications, .feature-card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-15px)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
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
      document.querySelectorAll('.btn-primary-custom, .btn-outline-custom, .btn-view-details').forEach(btn => {
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