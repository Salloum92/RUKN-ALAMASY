<?php
// يجب أن تكون الجلسة في أعلى الصفحة قبل أي output
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'ar';
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header('Location: ' . $current_url);
    exit();
}

include 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = new Database();
$product = $query->getById('products', $id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$category = isset($product['category_id']) ? $query->getById('category', $product['category_id']) : null;
$category_name = $category ? $category['category_name'] : 'غير مصنف';

$product_images = $query->executeQuery("SELECT image_url FROM product_images WHERE product_id = $id");
$product_images = !empty($product_images) ? array_column($product_images, 'image_url') : ['default-product.jpg'];

// تحديد اللغة
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
$translations = [
    'ar' => [
        'title' => $product['product_name'] . ' - ركن السلامة',
        'home' => 'الرئيسية',
        'products' => 'المنتجات',
        'product_details' => 'تفاصيل المنتج',
        'product_information' => 'معلومات المنتج',
        'product_name' => 'اسم المنتج',
        'category' => 'الفئة',
        'price' => 'السعر',
        'description' => 'الوصف',
        'add_to_cart' => 'إضافة إلى السلة',
        'contact_us' => 'اتصل بنا',
        'related_products' => 'منتجات ذات صلة',
        'product_not_found' => 'المنتج غير موجود',
        'product_not_found_desc' => 'المنتج الذي تبحث عنه غير موجود.',
        'features' => 'المميزات',
        'specifications' => 'المواصفات',
        'warranty' => 'الضمان',
        'delivery' => 'التوصيل',
        'free_delivery' => 'توصيل مجاني',
        'secure_payment' => 'دفع آمن',
        'quality_guarantee' => 'ضمان الجودة',
        'technical_support' => 'دعم فني'
    ],
    'en' => [
        'title' => $product['product_name'] . ' - Safety Corner',
        'home' => 'Home',
        'products' => 'Products',
        'product_details' => 'Product Details',
        'product_information' => 'Product Information',
        'product_name' => 'Product Name',
        'category' => 'Category',
        'price' => 'Price',
        'description' => 'Description',
        'add_to_cart' => 'Add to Cart',
        'contact_us' => 'Contact Us',
        'related_products' => 'Related Products',
        'product_not_found' => 'Product not found',
        'product_not_found_desc' => 'The product you are looking for does not exist.',
        'features' => 'Features',
        'specifications' => 'Specifications',
        'warranty' => 'Warranty',
        'delivery' => 'Delivery',
        'free_delivery' => 'Free Delivery',
        'secure_payment' => 'Secure Payment',
        'quality_guarantee' => 'Quality Guarantee',
        'technical_support' => 'Technical Support'
    ]
];
$t = $translations[$lang];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= ($lang == 'ar') ? 'rtl' : 'ltr' ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $t['title'] ?></title>
  <meta name="description" content="<?= htmlspecialchars($product['description']) ?>">
  <link href="favicon.ico" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #e76a04;
      --primary-dark: #fff;
      --secondary-color: #2c3e50;
      --accent-color: #e76a04;
      --success-color: #e76a04;
      --text-dark: #2c3e50;
      --text-light: #e76a04;
      --bg-light: #f8f9fa;
      --white: #ffffff;
      --border-radius: 20px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --shadow: 0 10px 40px rgba(0,0,0,0.1);
      --shadow-hover: 0 20px 60px rgba(0,0,0,0.15);
    }

    * {
      font-family: 'Cairo', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
      min-height: 100vh;
    }

    .product-details-section {
      padding: 40px 0 80px;
    }

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

    .product-gallery {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 30px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .product-gallery::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary-color);
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
      box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
    }

    .thumbnail img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-info {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      position: relative;
    }

    .product-info::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary-color);
    }

    .product-badge {
      background: var(--primary-color);
      color: white;
      padding: 8px 20px;
      border-radius: 25px;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    }

    .product-title {
      color: var(--primary-color);
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 15px;
      line-height: 1.2;
      background: linear-gradient(135deg, var(--text-dark), #34495e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .product-category {
      color: var(--accent-color);
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .product-price {
      font-size: 2.8rem;
      font-weight: 800;
      color: var(--primary-color);
      margin-bottom: 30px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .product-description {
      color: var(--primary-color);
      font-size: 1.15rem;
      line-height: 1.8;
      margin-bottom: 30px;
      padding: 25px;
      background: rgba(248, 249, 250, 0.5);
      border-radius: 15px;
      border-left: 4px solid var(--accent-color);
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .btn-primary-custom {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: var(--white);
      border: none;
      padding: 18px 35px;
      border-radius: 15px;
      font-weight: 700;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
      font-size: 1.1rem;
    }

    .btn-primary-custom:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 15px 40px rgba(231, 76, 60, 0.4);
      color: var(--white);
    }

    .btn-outline-custom {
      background: transparent;
      color: var(--text-dark);
      border: 2px solid var(--text-dark);
      padding: 18px 35px;
      border-radius: 15px;
      font-weight: 700;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .btn-outline-custom:hover {
      background: var(--text-dark);
      color: var(--white);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
      padding-top: 30px;
      border-top: 1px solid rgba(0,0,0,0.1);
    }

    .feature-card {
      background: linear-gradient(135deg, #ffffff, #f8f9fa);
      padding: 25px 20px;
      border-radius: 15px;
      text-align: center;
      transition: var(--transition);
      border: 1px solid rgba(255, 255, 255, 0.5);
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .feature-icon {
      width: 70px;
      height: 70px;
      background: #e76a04;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      color: white;
      font-size: 1.8rem;
      transition: var(--transition);
    }

    .feature-card:hover .feature-icon {
      transform: scale(1.1) rotate(5deg);
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
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 40px;
      margin-top: 40px;
      box-shadow: var(--shadow);
    }

    .spec-item {
      display: flex;
      justify-content: between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #e9ecef;
    }

    .spec-item:last-child {
      border-bottom: none;
    }

    .spec-label {
      font-weight: 600;
      color: var(--text-dark);
      min-width: 150px;
    }

    .spec-value {
      color: var(--text-light);
    }

    .related-products {
      padding: 80px 0;
      background: linear-gradient(135deg, #e76a04 0%, #fff 100%);
      position: relative;
      overflow: hidden;
    }

    .related-products::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.05"><polygon fill="white" points="0,1000 1000,0 1000,1000"/></svg>');
    }

    .section-title {
      text-align: center;
      margin-bottom: 50px;
      position: relative;
      z-index: 2;
      color: black ;
    }

    .section-title h2 {
      font-size: 3rem;
      font-weight: 800;
      color: black;
      margin-bottom: 15px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .section-title p {
      color: black;
      font-size: 1.2rem;
    }

    .product-not-found {
      min-height: 60vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 60px;
      box-shadow: var(--shadow);
    }

    .product-not-found i {
      font-size: 5rem;
      color: #e76a04;
      margin-bottom: 20px;
    }

    .product-not-found h3 {
      font-size: 2.5rem;
      color: black;
      margin-bottom: 15px;
    }

    .product-not-found p {
      font-size: 1.3rem;
      color: black;
      margin-bottom: 30px;
    }

    /* Animations */
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

    .fade-in-up {
      animation: fadeInUp 0.8s ease-out;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .product-details-section {
        padding: 20px 0 40px;
      }
      
      .product-title {
        font-size: 2rem;
      }
      
      .product-price {
        font-size: 2.2rem;
      }
      
      .product-info,
      .product-gallery {
        padding: 25px;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn-primary-custom,
      .btn-outline-custom {
        width: 100%;
        justify-content: center;
      }
      
      .features-grid {
        grid-template-columns: 1fr;
      }
      
      .section-title h2 {
        font-size: 2.2rem;
      }
    }

    @media (max-width: 576px) {
      .product-title {
        font-size: 1.7rem;
      }
      
      .product-price {
        font-size: 1.8rem;
      }
      
      .main-image img {
        height: 300px;
      }
      
      .thumbnail {
        width: 70px;
        height: 70px;
      }
    }
  </style>
</head>

<body class="product-details-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./"><?= $t['home'] ?></a></li>
            <li class="breadcrumb-item"><a href="products.php"><?= $t['products'] ?></a></li>
            <li class="breadcrumb-item active"><?= $product['product_name'] ?></li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Product Details Section -->
    <section class="product-details-section">
      <div class="container">
        <div class="row gy-5">
          <?php if ($product): ?>
            <!-- Product Gallery -->
            <div class="col-lg-6" data-aos="fade-up">
              <div class="product-gallery fade-in-up">
                <div class="main-image">
                  <img src="assets/img/product/<?= $product_images[0] ?>" 
                       alt="<?= htmlspecialchars($product['product_name']) ?>"
                       id="mainProductImage"
                       onerror="this.src='assets/img/default-product.jpg'">
                </div>
                <?php if (count($product_images) > 1): ?>
                <div class="thumbnail-gallery">
                  <?php foreach ($product_images as $index => $image): ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                         onclick="changeImage('assets/img/product/<?= $image ?>', this)">
                      <img src="assets/img/product/<?= $image ?>" 
                           alt="Thumbnail <?= $index + 1 ?>"
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
                <div class="product-badge pulse">
                  <i class="bi bi-star-fill"></i>
                  منتج مميز
                </div>
                
                <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h1>
                
                <div class="product-category">
                  <i class="bi bi-tags"></i>
                  <span><?= htmlspecialchars($category_name) ?></span>
                </div>

                <div class="product-price">
                  ر.س <?= number_format($product['price'], 0, '', ' ') ?>
                </div>

                <div class="product-description">
                  <h4 style="color: var(--text-dark); margin-bottom: 15px;">
                    <i class="bi bi-info-circle"></i> الوصف:
                  </h4>
                  <p style="margin: 0;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <div class="action-buttons">
                  <a href="contact.php" class="btn-primary-custom">
                    <i class="bi bi-whatsapp"></i>
                    طلب المنتج
                  </a>
                  <a href="products.php" class="btn-outline-custom">
                    <i class="bi bi-arrow-right"></i>
                    تصفح المزيد
                  </a>
                </div>

                <!-- Features Grid -->
                <div class="features-grid">
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>ضمان الجودة</h5>
                    <p>منتجات أصلية 100%</p>
                  </div>
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-truck"></i>
                    </div>
                    <h5>توصيل سريع</h5>
                    <p>شحن لجميع المدن</p>
                  </div>
                  <div class="feature-card">
                    <div class="feature-icon">
                      <i class="bi bi-headset"></i>
                    </div>
                    <h5>دعم فني</h5>
                    <p>متواصل 24/7</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Specifications Section -->
            <div class="col-12" data-aos="fade-up" data-aos-delay="400">
              <div class="specifications fade-in-up">
                <h3 style="color: var(--text-dark); margin-bottom: 25px; font-weight: 700;">
                  <i class="bi bi-list-check"></i> مواصفات المنتج
                </h3>
                <div class="spec-item">
                  <span class="spec-label">اسم المنتج:</span>
                  <span class="spec-value"><?= htmlspecialchars($product['product_name']) ?></span>
                </div>
                <div class="spec-item">
                  <span class="spec-label">الفئة:</span>
                  <span class="spec-value"><?= htmlspecialchars($category_name) ?></span>
                </div>
                <div class="spec-item">
                  <span class="spec-label">السعر:</span>
                  <span class="spec-value" style="color: var(--primary-color); font-weight: 700;">
                    ر.س <?= number_format($product['price'], 0, '', ' ') ?>
                  </span>
                </div>
                <div class="spec-item">
                  <span class="spec-label">الحالة:</span>
                  <span class="spec-value" style="color: var(--success-color); font-weight: 600;">
                    <i class="bi bi-check-circle"></i> متوفر
                  </span>
                </div>
              </div>
            </div>

          <?php else: ?>
            <div class="col-12">
              <div class="product-not-found fade-in-up">
                <div>
                  <i class="bi bi-exclamation-triangle"></i>
                  <h3><?= $t['product_not_found'] ?></h3>
                  <p><?= $t['product_not_found_desc'] ?></p>
                  <a href="products.php" class="btn-primary-custom">
                    <i class="bi bi-arrow-left"></i> العودة للمنتجات
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
      <div class="container">
        
        <div class="row justify-content-center">
          <div class="col-md-8 text-center">
            <p style="color: black; font-size: 1.2rem; margin-bottom: 30px;">
              نعمل على إضافة المزيد من المنتجات المتعلقة بهذا المنتج قريباً
            </p>
            <a href="products.php" class="btn-view-details" style="background: #e76a04; color : white ; backdrop-filter: blur(10px); border: 2px solid white;">
              <i class="bi bi-grid"></i> تصفح جميع المنتجات
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>

  <!-- Scroll to top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // تغيير الصورة الرئيسية عند النقر على الثمبنييل
    function changeImage(src, element) {
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
    }

    // تهيئة AOS
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true,
          offset: 50
        });
      }

      // تأثيرات التفاعل
      const buttons = document.querySelectorAll('.btn-primary-custom, .btn-outline-custom');
      buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
        });
      });

      // تأثير التحميل للأزرار
      document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
          if (!this.getAttribute('href').startsWith('#')) {
            this.classList.add('loading');
          }
        });
      });
    });
  </script>
</body>
</html>