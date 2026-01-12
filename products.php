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

// AJAX request للفلترة
if (isset($_POST['ajax_filter'])) {
    $search_query = isset($_POST['search']) ? trim($_POST['search']) : '';
    $category_filter = isset($_POST['category']) ? $_POST['category'] : 'all';
    $sort_by = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
    
    $filtered_products = $products;
    
    // فلترة حسب البحث
    if (!empty($search_query)) {
        $search_lower = strtolower($search_query);
        $filtered_products = array_filter($filtered_products, function($product) use ($search_lower) {
            $product_name = strtolower($product['product_name']);
            $description = strtolower($product['description']);
            return strpos($product_name, $search_lower) !== false || 
                   strpos($description, $search_lower) !== false;
        });
    }
    
    // فلترة حسب الفئة
    if ($category_filter !== 'all') {
        $filtered_products = array_filter($filtered_products, function($product) use ($category_filter) {
            return $product['category_id'] == $category_filter;
        });
    }
    
    // إعادة ترتيب المفاتيح
    $filtered_products = array_values($filtered_products);
    
    // ترتيب المنتجات
    if ($sort_by === 'name_asc') {
        usort($filtered_products, function($a, $b) {
            return strcmp($a['product_name'], $b['product_name']);
        });
    } elseif ($sort_by === 'name_desc') {
        usort($filtered_products, function($a, $b) {
            return strcmp($b['product_name'], $a['product_name']);
        });
    }
    
    // إرجاع النتائج كـ HTML
    $product_images_by_id = [];
    foreach ($product_images as $image) {
        $product_images_by_id[$image['product_id']] = $image['image_url'];
    }
    
    ob_start();
    
    if (empty($filtered_products)): ?>
        <div class="col-12">
            <div class="no-products text-center py-5">
                <i class="bi bi-search display-1 text-muted mb-3"></i>
                <h3 class="text-muted"><?php echo ($lang == 'ar') ? 'لا توجد نتائج' : 'No Results Found'; ?></h3>
                <p class="text-muted"><?php echo ($lang == 'ar') ? 'جرب مصطلحات بحث مختلفة' : 'Try different search terms'; ?></p>
            </div>
        </div>
    <?php else: 
        foreach ($filtered_products as $index => $product): 
            $image_url = isset($product_images_by_id[$product['id']]) ? $product_images_by_id[$product['id']] : 'default-product.jpg';
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
                        <div class="product-actions">
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view-details">
                                <i class="bi bi-eye"></i>
                                <?php echo ($lang == 'ar') ? 'عرض التفاصيل' : 'View Details'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;
    endif;
    
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'count' => count($filtered_products),
        'html' => $html
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo ($lang == 'ar') ? 'منتجاتنا - ركن الأماسي' : 'Our Products - Rukn Alamasy'; ?></title>
  <!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://rukn-alamasy.com.sa/products.php">
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

  <meta name="description" content="<?php echo ($lang == 'ar') ? 'اكتشف مجموعتنا المتميزة من منتجات الأمن والسلامة عالية الجودة' : 'Discover our premium collection of high-quality safety and security products'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'منتجات، أمن، سلامة، معدات حماية، تجهيزات' : 'products, security, safety, protection equipment, supplies'; ?>">
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.css">
  
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
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

    .advanced-filters {
      background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
      padding: 30px 0;
      border-bottom: 1px solid rgba(231, 106, 4, 0.1);
    }

    .filters-container {
      background: white;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(231, 106, 4, 0.1);
    }

    .filter-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: center;
    }

    .filter-group {
      flex: 1;
      min-width: 200px;
    }

    .filter-label {
      display: block;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 8px;
      font-size: 0.95rem;
    }

    .search-box {
      width: 100%;
      padding: 12px 20px;
      border: 2px solid rgba(231, 106, 4, 0.2);
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .search-box:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.1);
    }

    .filter-select {
      width: 100%;
      padding: 12px 20px;
      border: 2px solid rgba(231, 106, 4, 0.2);
      border-radius: 12px;
      font-size: 1rem;
      background: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .filter-select:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.1);
    }

    .filter-actions {
      display: flex;
      gap: 10px;
      align-items: flex-end;
    }

    .btn-filter {
      padding: 12px 25px;
      background: var(--gradient-primary);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-filter:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(231, 106, 4, 0.2);
    }

    .btn-reset {
      padding: 12px 20px;
      background: var(--light-color);
      color: var(--text-dark);
      border: 2px solid rgba(231, 106, 4, 0.2);
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-reset:hover {
      background: rgba(231, 106, 4, 0.05);
      border-color: var(--primary-color);
    }

    .results-info {
      background: rgba(231, 106, 4, 0.05);
      padding: 15px 25px;
      border-radius: 12px;
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .results-count {
      font-weight: 700;
      color: var(--dark-color);
    }

    .active-filters {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .filter-tag {
      background: var(--primary-color);
      color: white;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .remove-filter {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 0;
      font-size: 1rem;
    }

    .filter-loading {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }
    
    .filter-loading.active {
      display: flex;
    }
    
    .filter-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid rgba(231, 106, 4, 0.2);
      border-radius: 50%;
      border-top-color: var(--primary-color);
      animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .filters-section {
      padding: 60px 0 40px;
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

    .products-grid-section {
      padding: 80px 0;
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

    .product-actions {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    .btn-view-details {
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: var(--gradient-primary);
      color: var(--white);
      padding: 15px 10px;
      border-radius: 15px;
      text-decoration: none;
      
      font-weight: 600;
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

    .floating-element {
      animation: floatAnimation 3s ease-in-out infinite;
    }

    @keyframes floatAnimation {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-25px) rotate(5deg); }
    }

   
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
      
      .filter-row {
        flex-direction: column;
      }
      
      .filter-group {
        width: 100%;
      }
      
      .filter-actions {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 576px) {
      .products-hero-section {
        height: 30vh;
        min-height: 220px;
        margin-top: 0;
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
      
      
    }

    #particles-js-products canvas,
    #particles-js-filters canvas,
    #particles-js-products-grid canvas,
    #particles-js-stats-products canvas,
    #particles-js-cta-products canvas {
      display: block;
      vertical-align: bottom;
      transform: translate3d(0, 0, 0);
    }

    .product-item {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.4s ease;
    }

    .product-item.show {
      opacity: 1;
      transform: translateY(0);
    }

    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    
    .cta-buttons a {
      margin: 5px 0;
    }

    .product-filtered {
      display: block !important;
    }

    .product-hidden {
      display: none !important;
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

   
    
    #products-container {
      transition: opacity 0.3s ease;
    }
    
    #products-container.updating {
      opacity: 0.5;
    }
    
    .product-item {
      animation: fadeIn 0.5s ease forwards;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
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
  
  <!-- Filter Loading Overlay -->
  <div class="filter-loading" id="filterLoading">
    <div class="filter-spinner"></div>
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

    <!-- Advanced Filters -->
    <section class="advanced-filters">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-filter"></i>
            <?php echo ($lang == 'ar') ? 'تصفية المنتجات' : 'Filter Products'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'تصفح منتجاتنا حسب الفئة' : 'Browse Our Products by Category'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اختر الفئة التي تناسب احتياجاتك واستعرض أفضل المنتجات' : 'Choose the category that suits your needs and browse the best products'; ?></p>
        </div>
        <div class="filters-container" data-aos="fade-up">
          <div class="filter-row">
            <!-- Search -->
            <div class="filter-group">
              <label class="filter-label">
                <i class="bi bi-search"></i>
                <?php echo ($lang == 'ar') ? 'بحث' : 'Search'; ?>
              </label>
              <input type="text" 
                     class="search-box" 
                     id="searchInput" 
                     placeholder="<?php echo ($lang == 'ar') ? 'ابحث عن منتج...' : 'Search for a product...'; ?>">
            </div>
            
            <!-- Category -->
            <div class="filter-group">
              <label class="filter-label">
                <i class="bi bi-grid-3x3-gap"></i>
                <?php echo ($lang == 'ar') ? 'الفئة' : 'Category'; ?>
              </label>
              <select class="filter-select" id="categorySelect">
                <option value="all"><?php echo ($lang == 'ar') ? 'جميع الفئات' : 'All Categories'; ?></option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['category_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- Sort -->
            <div class="filter-group">
              <label class="filter-label">
                <i class="bi bi-sort-down"></i>
                <?php echo ($lang == 'ar') ? 'ترتيب حسب' : 'Sort By'; ?>
              </label>
              <select class="filter-select" id="sortSelect">
                <option value="name_asc"><?php echo ($lang == 'ar') ? 'الاسم: أ-ي' : 'Name: A-Z'; ?></option>
                <option value="name_desc"><?php echo ($lang == 'ar') ? 'الاسم: ي-أ' : 'Name: Z-A'; ?></option>
              </select>
            </div>
            
            <!-- Actions -->
            <div class="filter-actions">
              <button type="button" class="btn-filter" onclick="applyFilters()">
                <i class="bi bi-funnel"></i>
                <?php echo ($lang == 'ar') ? 'تصفية' : 'Filter'; ?>
              </button>
              <button type="button" class="btn-reset" onclick="resetFilters()">
                <i class="bi bi-arrow-counterclockwise"></i>
                <?php echo ($lang == 'ar') ? 'إعادة تعيين' : 'Reset'; ?>
              </button>
            </div>
          </div>
          
          <!-- Results Info -->
          <div class="results-info" id="resultsInfo" style="display: none;">
            <div class="results-count">
              <span id="resultsCount">0</span> 
              <?php echo ($lang == 'ar') ? 'منتج' : 'products'; ?>
            </div>
            
            <div class="active-filters" id="activeFilters">
              <!-- Active filters will be added here dynamically -->
            </div>
          </div>
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
                    <div class="product-actions">
                      <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view-details">
                        <i class="bi bi-eye"></i>
                        <?php echo ($lang == 'ar') ? 'عرض تفاصيل المنتج' : 'View Details'; ?>
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
            color: {
              value: ["#e76a04"]
            },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, opacity: 0.5 ,color: "#e76a04" },
            move: { enable: true, speed: 1 }
          }
        });
        
        particlesJS('particles-js-filters', {
          particles: {
            number: { value: 60 },
           color: {
              value: ["#e76a04"]
            },
            opacity: { value: 0.2 },
            size: { value: 4 },
            line_linked: { enable: true, distance: 150, opacity: 0.1 ,color: "#e76a04" },
            move: { enable: true, speed: 1 }
          }
        });
        
        particlesJS('particles-js-products-grid', {
          particles: {
            number: { value: 80 },
            color: {
              value: ["#e76a04"]
            },
            opacity: { value: 0.3 },
            size: { value: 3 },
            line_linked: { enable: true, distance: 150, opacity: 0.1 ,color: "#e76a04" },
            move: { enable: true, speed: 2 }
          }
        });
        
        particlesJS('particles-js-stats-products', {
          particles: {
            number: { value: 50 },
            color: {
              value: ["#e76a04"]
            },
            opacity: { value: 0.1 },
            size: { value: 4 },
            line_linked: { enable: true, distance: 150, opacity: 0.1 ,color: "#e76a04" },
            move: { enable: true, speed: 1 }
          }
        });
        
        particlesJS('particles-js-cta-products', {
          particles: {
            number: { value: 70 },
            color: {
              value: ["#e76a04"]
            },
            opacity: { value: 0.3 },
            size: { value: 4 },
            line_linked: { enable: true, distance: 150, opacity: 0.1 ,color: "#e76a04" },
            move: { enable: true, speed: 1.5 }
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

    // AJAX Filter System
    let currentCategory = 'all';
    let currentSearch = '';
    let currentSort = 'newest';
    let searchTimeout;
    
    // دالة لتعيين الفئة
    function setCategory(categoryId) {
      currentCategory = categoryId;
      document.getElementById('categorySelect').value = categoryId;
      
      // تحديث الأزرار النشطة
      document.querySelectorAll('.filter-btn-product').forEach(btn => {
        btn.classList.remove('active');
      });
      
      if (categoryId === 'all') {
        document.querySelector('.filter-btn-product[onclick="setCategory(\'all\')"]').classList.add('active');
      } else {
        document.querySelector(`.filter-btn-product[onclick="setCategory(${categoryId})"]`).classList.add('active');
      }
      
      applyFilters();
    }
    
    // دالة لتطبيق الفلاتر
    function applyFilters() {
      currentSearch = document.getElementById('searchInput').value;
      currentCategory = document.getElementById('categorySelect').value;
      currentSort = document.getElementById('sortSelect').value;
      
      // إظهار loading
      const filterLoading = document.getElementById('filterLoading');
      const productsContainer = document.getElementById('products-container');
      
      filterLoading.classList.add('active');
      productsContainer.classList.add('updating');
      
      // إرسال طلب AJAX
      const formData = new FormData();
      formData.append('ajax_filter', '1');
      formData.append('search', currentSearch);
      formData.append('category', currentCategory);
      formData.append('sort', currentSort);
      
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // تحديث المنتجات
          productsContainer.innerHTML = data.html;
          productsContainer.style.display = 'flex';
          
          // تحديث معلومات النتائج
          updateResultsInfo(data.count);
          
          // إعادة تهيئة AOS للمنتجات الجديدة
          if (typeof AOS !== 'undefined') {
            AOS.refresh();
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
      })
      .finally(() => {
        filterLoading.classList.remove('active');
        productsContainer.classList.remove('updating');
      });
    }
    
    // دالة لتحديث معلومات النتائج
    function updateResultsInfo(count) {
      const resultsInfo = document.getElementById('resultsInfo');
      const resultsCount = document.getElementById('resultsCount');
      const activeFilters = document.getElementById('activeFilters');
      
      resultsCount.textContent = count;
      
      // بناء الفلاتر النشطة
      let activeFiltersHtml = '';
      const categories = <?php echo json_encode($category_names); ?>;
      const lang = document.documentElement.lang;
      
      if (currentSearch) {
        activeFiltersHtml += `
          <div class="filter-tag">
            ${lang === 'ar' ? 'بحث:' : 'Search:'} "${escapeHtml(currentSearch)}"
            <button type="button" class="remove-filter" onclick="removeFilter('search')">
              <i class="bi bi-x"></i>
            </button>
          </div>
        `;
      }
      
      if (currentCategory !== 'all') {
        const categoryName = categories[currentCategory] || (lang === 'ar' ? 'غير مصنف' : 'Uncategorized');
        activeFiltersHtml += `
          <div class="filter-tag">
            ${lang === 'ar' ? 'الفئة:' : 'Category:'} ${escapeHtml(categoryName)}
            <button type="button" class="remove-filter" onclick="removeFilter('category')">
              <i class="bi bi-x"></i>
            </button>
          </div>
        `;
      }
      
      if (currentSort !== 'newest') {
        const sortTexts = {
          'newest': lang === 'ar' ? 'الأحدث' : 'Newest',
          'name_asc': lang === 'ar' ? 'الاسم: أ-ي' : 'Name: A-Z',
          'name_desc': lang === 'ar' ? 'الاسم: ي-أ' : 'Name: Z-A'
        };
        
        activeFiltersHtml += `
          <div class="filter-tag">
            ${lang === 'ar' ? 'الترتيب:' : 'Sort:'} ${sortTexts[currentSort] || ''}
            <button type="button" class="remove-filter" onclick="removeFilter('sort')">
              <i class="bi bi-x"></i>
            </button>
          </div>
        `;
      }
      
      activeFilters.innerHTML = activeFiltersHtml;
      
      // إظهار أو إخفاء قسم النتائج
      if (currentSearch || currentCategory !== 'all' || currentSort !== 'newest') {
        resultsInfo.style.display = 'flex';
      } else {
        resultsInfo.style.display = 'none';
      }
    }
    
    // دالة لإزالة فلتر محدد
    function removeFilter(filterType) {
      if (filterType === 'search') {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
      } else if (filterType === 'category') {
        document.getElementById('categorySelect').value = 'all';
        currentCategory = 'all';
        
        // تحديث أزرار الفئات
        document.querySelectorAll('.filter-btn-product').forEach(btn => {
          btn.classList.remove('active');
        });
        document.querySelector('.filter-btn-product[onclick="setCategory(\'all\')"]').classList.add('active');
      } else if (filterType === 'sort') {
        document.getElementById('sortSelect').value = 'newest';
        currentSort = 'newest';
      }
      
      applyFilters();
    }
    
    // دالة لإعادة تعيين جميع الفلاتر
    function resetFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categorySelect').value = 'all';
      document.getElementById('sortSelect').value = 'newest';
      
      // تحديث أزرار الفئات
      document.querySelectorAll('.filter-btn-product').forEach(btn => {
        btn.classList.remove('active');
      });
      document.querySelector('.filter-btn-product[onclick="setCategory(\'all\')"]').classList.add('active');
      
      currentSearch = '';
      currentCategory = 'all';
      currentSort = 'newest';
      
      applyFilters();
    }
    
    // دالة لتهريب النصوص HTML
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    // Auto-apply filters when search input changes (with delay)
    document.getElementById('searchInput').addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilters();
      }, 500);
    });
    
    // Auto-apply filters when select changes
    document.getElementById('categorySelect').addEventListener('change', applyFilters);
    document.getElementById('sortSelect').addEventListener('change', applyFilters);
    
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