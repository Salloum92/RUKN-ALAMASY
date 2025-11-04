<?php
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'ar';
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header('Location: ' . $current_url);
    exit();
}
include 'testimonials.php';
include 'config.php';
require_once 'lang.php';

$query = new Database();
$banners = $query->select('banners');
$features = $query->select('features');
$aboutData = $query->select('about');
$serviceItems = $query->select('about_ul_items');
$services = $query->select('services');
$statistics = $query->select('statistics');
$categories = $query->select('category');
$products = $query->select('products');
$product_images = $query->select('product_images');

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
  <title><?php echo ($lang == 'ar') ? 'ركن الأماسي - منتجات وخدمات متميزة' : 'Rukn Alamasy - Premium Products & Services'; ?></title>
  <meta name="description" content="<?php echo ($lang == 'ar') ? 'اكتشف منتجات وخدمات استثنائية مع ركن الأماسي' : 'Discover exceptional products and services with Rukn Alamasy'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'منتجات متميزة، خدمات عالية الجودة، حلول أعمال' : 'premium products, quality services, business solutions'; ?>">
  <link href="favicon.ico" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
    .section-header {
      text-align: center;
      margin-bottom: 60px;
      position: relative;
    }
    
    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #e76a04, #c0392b);
      color: white;
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 15px;
      box-shadow: 0 4px 15px rgba(231, 106, 4, 0.3);
    }
    
    .section-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: #2c3e50;
      margin-bottom: 15px;
      position: relative;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(135deg, #e76a04, #c0392b);
      border-radius: 2px;
    }
    
    .section-subtitle {
      font-size: 1.1rem;
      color: #6c757d;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }
    
    .btn-view-all {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #e76a04, #c0392b);
      color: white;
      padding: 12px 30px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      box-shadow: 0 4px 15px rgba(231, 106, 4, 0.3);
    }
    
    .btn-view-all:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(231, 106, 4, 0.4);
      color: white;
    }
    
    .btn-view-details {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: transparent;
      color: #e76a04;
      padding: 10px 20px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      border: 2px solid #e76a04;
      transition: all 0.3s ease;
    }
    
    .btn-view-details:hover {
      background: #e76a04;
      color: white;
      transform: translateY(-2px);
    }
  </style>
</head>

<body class="index-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-slider">
        <?php if (!empty($banners)): ?>
          <?php foreach ($banners as $index => $banner): ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('assets/img/banners/<?php echo htmlspecialchars($banner['image']); ?>')">
              <div class="container">
                <div class="hero-content">
                  <div class="hero-text fade-in-up">
                    <h1 class="hero-title"><?php echo htmlspecialchars($banner['title']); ?></h1>
                    <p class="hero-subtitle"><?php echo htmlspecialchars($banner['description']); ?></p>
                    <div class="hero-buttons">
                      <a href="<?php echo htmlspecialchars($banner['button_link']); ?>" class="btn-hero btn-primary-hero">
                        <?php echo htmlspecialchars($banner['button_text']); ?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="hero-slide active" style="background-image: url('assets/img/hero-bg.jpg')">
            <div class="container">
              <div class="hero-content">
                <div class="hero-text fade-in-up">
                  <h1 class="hero-title"><?php echo ($lang == 'ar') ? 'مرحباً بكم في ركن الأماسي' : 'Welcome to Rukn Alamasy'; ?></h1>
                  <p class="hero-subtitle"><?php echo ($lang == 'ar') ? 'اكتشف منتجات وخدمات استثنائية' : 'Discover Exceptional Products & Services'; ?></p>
                  <div class="hero-buttons">
                    <a href="products.php" class="btn-hero btn-primary-hero">
                      <i class="bi bi-cart3"></i><?php echo ($lang == 'ar') ? 'ابدأ الآن' : 'Get Started'; ?>
                    </a>
                    <a href="about.php" class="btn-hero btn-outline-hero">
                      <i class="bi bi-info-circle"></i><?php echo ($lang == 'ar') ? 'اعرف المزيد' : 'Learn More'; ?>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <button class="hero-nav hero-prev">
          <i class="bi bi-chevron-<?php echo ($lang == 'ar') ? 'right' : 'left'; ?>"></i>
        </button>
        <button class="hero-nav hero-next">
          <i class="bi bi-chevron-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?>"></i>
        </button>

        <div class="hero-controls">
          <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $index => $banner): ?>
              <div class="hero-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="hero-dot active" data-slide="0"></div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    ئتمنتيمبنتسمنيتبمسنتيبمنستيمبنتسمينتبمسنتيبم

    <!-- Features Section -->
    <section class="features-section" style="padding: 80px 0;">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-stars"></i>
            <?php echo ($lang == 'ar') ? 'لماذا تختارنا' : 'Why Choose Us'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'مميزاتنا الاستثنائية' : 'Our Exceptional Features'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اكتشف ما يجعلنا مختلفين وأفضل' : 'Discover what makes us different and better'; ?></p>
        </div>

        <div class="row">
          <?php if (!empty($features)): ?>
            <?php foreach ($features as $index => $feature): ?>
              <div class="col-xl-4 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="feature-card">
                  <div class="feature-icon">
                    <i class="<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                  </div>
                  <h4><?php echo htmlspecialchars($feature['title']); ?></h4>
                  <p><?php echo htmlspecialchars($feature['description']); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p class="text-muted"><?php echo ($lang == 'ar') ? 'لا توجد مميزات متاحة' : 'No features available'; ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>



    فثسفكمينبتلكيمنتبكلمنيكبمنلكي بليكمبنل يكمبنلك يبلكمن
    <!-- About Section -->
    <section class="simple-about-section" style="padding: 80px 0; background: #f8f9fa;">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-building"></i>
            <?php echo ($lang == 'ar') ? 'من نحن' : 'About Us'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'قصتنا ومسيرتنا' : 'Our Story & Journey'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'الريادة في الجودة والابتكار منذ البداية' : 'Leading the way in quality and innovation from the start'; ?></p>
        </div>

        <div class="row align-items-center">
          <div class="col-lg-6">
            <div style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);" data-aos="fade-right">
              <div style="height: 400px; background: linear-gradient(135deg, #e76a04, #c0392b); display: flex; align-items: center; justify-content: center; color: white;">
                <i class="bi bi-shield-check" style="font-size: 4rem;"></i>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6" style="padding-<?php echo ($lang == 'ar') ? 'right' : 'left'; ?>: 40px;">
            <div data-aos="fade-left" data-aos-delay="100">
              <h2 style="font-size: 2.2rem; font-weight: 800; color: #2c3e50; line-height: 1.2; margin-bottom: 20px;">
                <?php echo ($lang == 'ar') ? 'ركن السلامة<br>شركتك الموثوقة للأمن والسلامة' : 'Rukn Al-Amasy<br>Your Trusted Safety & Security Partner'; ?>
              </h2>
              
              <p style="font-size: 1.1rem; color: #6c757d; line-height: 1.8; margin-bottom: 30px;">
                <?php echo ($lang == 'ar') ? 'نقدم حلولاً متكاملة في مجال معدات الأمن والسلامة بمهنية واحترافية عالية. نحن نضمن لك بيئة عمل آمنة ومنتجات عالية الجودة مع خدمة دعم فني متكاملة.' : 'We provide integrated solutions in safety and security equipment with high professionalism. We ensure you a safe work environment with high-quality products and comprehensive technical support services.'; ?>
              </p>
              
              <div style="display: flex; gap: 20px; margin: 30px 0;">
                <div style="text-align: center;" data-aos="fade-up" data-aos-delay="200">
                  <div style="font-size: 1.8rem; font-weight: 800; color: #e76a04;">10+</div>
                  <div style="font-size: 0.9rem; color: #6c757d;"><?php echo ($lang == 'ar') ? 'سنوات خبرة' : 'Years Experience'; ?></div>
                </div>
                <div style="text-align: center;" data-aos="fade-up" data-aos-delay="300">
                  <div style="font-size: 1.8rem; font-weight: 800; color: #e76a04;">1500+</div>
                  <div style="font-size: 0.9rem; color: #6c757d;"><?php echo ($lang == 'ar') ? 'عميل راضي' : 'Satisfied Clients'; ?></div>
                </div>
                <div style="text-align: center;" data-aos="fade-up" data-aos-delay="400">
                  <div style="font-size: 1.8rem; font-weight: 800; color: #e76a04;">500+</div>
                  <div style="font-size: 0.9rem; color: #6c757d;"><?php echo ($lang == 'ar') ? 'منتج أمان' : 'Safety Products'; ?></div>
                </div>
              </div>
              
              <div style="display: flex; gap: 15px; margin-top: 30px;">
                <a href="about.php" class="btn-view-all" data-aos="fade-up" data-aos-delay="500">
                  <i class="bi bi-info-circle"></i>
                  <?php echo ($lang == 'ar') ? 'اعرف المزيد' : 'Learn More'; ?>
                </a>
                <a href="contact.php" class="btn-view-all" style="background: #2c3e50;" data-aos="fade-up" data-aos-delay="600">
                  <i class="bi bi-telephone"></i>
                  <?php echo ($lang == 'ar') ? 'اتصل بنا' : 'Contact Us'; ?>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="about-section" style="padding: 80px 0;">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-gear"></i>
            <?php echo ($lang == 'ar') ? 'خدماتنا' : 'Our Services'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'خدماتنا المتكاملة' : 'Our Integrated Services'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'حلول شاملة تلبي جميع احتياجات الأمن والسلامة' : 'Comprehensive solutions for all your safety and security needs'; ?></p>
        </div>

        <div class="row gy-5 align-items-center">
          <?php if (!empty($aboutItems)): ?>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
              <div class="about-image">
                <?php if (!empty($aboutItems['image'])): ?>
                  <img src="<?php echo htmlspecialchars($aboutItems['image']); ?>" class="img-fluid" alt="<?php echo ($lang == 'ar') ? 'من نحن' : 'About Us'; ?>" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <?php else: ?>
                  <div style="background: linear-gradient(135deg, #e76a04, #c0392b); height: 400px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <i class="bi bi-building" style="font-size: 3rem;"></i>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
              <div class="about-content">
                <?php if (!empty($aboutItems['title'])): ?>
                  <h3 style="font-size: 2rem; font-weight: 700; color: #2c3e50; margin-bottom: 20px;"><?php echo htmlspecialchars($aboutItems['title']); ?></h3>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['p1'])): ?>
                  <p class="fst-italic" style="font-size: 1.1rem; color: #6c757d; line-height: 1.8; margin-bottom: 25px;"><?php echo htmlspecialchars($aboutItems['p1']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['list_items'])): ?>
                  <ul class="about-list" style="list-style: none; padding: 0; margin-bottom: 30px;">
                    <?php foreach ($aboutItems['list_items'] as $item): ?>
                      <?php if (!empty($item)): ?>
                        <li style="padding: 8px 0; font-size: 1rem; color: #495057;">
                          <i class="bi bi-check2-all" style="color: #e76a04; margin-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?>: 10px;"></i> 
                          <?php echo htmlspecialchars($item); ?>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['p2'])): ?>
                  <p style="font-size: 1rem; color: #6c757d; line-height: 1.8; margin-bottom: 30px;"><?php echo htmlspecialchars($aboutItems['p2']); ?></p>
                <?php endif; ?>
                
                <a href="services.php" class="btn-view-all">
                  <i class="bi bi-arrow-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?> me-2"></i> 
                  <?php echo ($lang == 'ar') ? 'استكشف خدماتنا' : 'Explore Our Services'; ?>
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="col-12 text-center">
              <div class="feature-card">
                <h3><?php echo ($lang == 'ar') ? 'مرحباً بكم في ركن الأماسي' : 'Welcome to Rukn Alamasy'; ?></h3>
                <p><?php echo ($lang == 'ar') ? 'نحن ملتزمون بتقديم أفضل المنتجات والخدمات لعملائنا الكرام' : 'We are committed to providing the best products and services to our valued customers'; ?></p>
                <a href="about.php" class="btn-view-all mt-3">
                  <i class="bi bi-arrow-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?> me-2"></i>
                  <?php echo ($lang == 'ar') ? 'اعرف المزيد' : 'Learn More'; ?>
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" style="padding: 80px 0; background: #f8f9fa;">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-box-seam"></i>
            <?php echo ($lang == 'ar') ? 'منتجاتنا' : 'Our Products'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'منتجاتنا المميزة' : 'Our Featured Products'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'اكتشف مجموعتنا المختارة من المنتجات المميزة' : 'Discover our handpicked selection of premium products'; ?></p>
        </div>

        <div class="row">
          <?php if (!empty($products)): ?>
            <?php 
            $featuredProducts = array_slice($products, 0, 6);
            foreach ($featuredProducts as $index => $product): 
              $image_url = 'default-product.jpg';
              foreach ($product_images as $image) {
                if ($image['product_id'] == $product['id']) {
                  $image_url = $image['image_url'];
                  break;
                }
              }
            ?>
              <div class="col-xl-4 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="product-card-home">
                  <div class="product-image-home">
                    <img src="assets/img/product/<?php echo htmlspecialchars($image_url); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         onerror="this.src='assets/img/default-product.jpg'">
                    <div class="product-badge"><?php echo ($lang == 'ar') ? 'جديد' : 'New'; ?></div>
                  </div>
                  <div class="product-content-home">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="product-description">
                      <?php echo htmlspecialchars(mb_substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?>
                    </p>
                    <div class="product-price">
                      <?php echo ($lang == 'ar') ? 'ر.س' : 'SAR'; ?> <?php echo number_format($product['price'], 0, '', ' '); ?>
                    </div>
                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view-details">
                      <i class="bi bi-eye"></i> <?php echo ($lang == 'ar') ? 'عرض التفاصيل' : 'View Details'; ?>
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            
            <div class="col-12 text-center" data-aos="fade-up" data-aos-delay="200">
              <a href="products.php" class="btn-view-all">
                <i class="bi bi-grid"></i> <?php echo ($lang == 'ar') ? 'عرض جميع المنتجات' : 'View All Products'; ?>
              </a>
            </div>
          <?php else: ?>
            <div class="col-12 text-center">
              <p class="text-muted"><?php echo ($lang == 'ar') ? 'لا توجد منتجات متاحة حالياً' : 'No products available at the moment'; ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section" style="padding: 80px 0; background: linear-gradient(135deg, #2c3e50, #34495e);">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-graph-up-arrow"></i>
            <?php echo ($lang == 'ar') ? 'إنجازاتنا' : 'Our Achievements'; ?>
          </div>
          <h2 class="section-title" style="color: white;"><?php echo ($lang == 'ar') ? 'إنجازاتنا بالأرقام' : 'Our Achievements in Numbers'; ?></h2>
          <p class="section-subtitle" style="color: rgba(255,255,255,0.8);"><?php echo ($lang == 'ar') ? 'أرقام تتحدث عن نجاحنا وتفانينا' : 'Numbers that speak about our success and dedication'; ?></p>
        </div>

        <div class="row">
          <?php if (!empty($statistics)): ?>
            <?php foreach ($statistics as $index => $stat): ?>
              <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="stats-card">
                  <div class="stats-icon">
                    <i class="<?php echo isset($stat['icon']) ? $stat['icon'] : 'bi bi-graph-up'; ?>"></i>
                  </div>
                  <div class="stats-number">
                    <span data-purecounter-start="0" 
                          data-purecounter-end="<?php echo isset($stat['count']) ? $stat['count'] : '0'; ?>" 
                          data-purecounter-duration="2" 
                          class="purecounter">
                      <?php echo isset($stat['count']) ? $stat['count'] : '0'; ?>
                    </span>
                  </div>
                  <div class="stats-title">
                    <?php echo isset($stat['title']) ? htmlspecialchars($stat['title']) : (($lang == 'ar') ? 'إنجاز' : 'Achievement'); ?>
                  </div>
                  <div class="stats-description">
                    <?php echo isset($stat['description']) ? htmlspecialchars($stat['description']) : (($lang == 'ar') ? 'قصة نجاحنا' : 'Our success story'); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p style="color: rgba(255,255,255,0.8);"><?php echo ($lang == 'ar') ? 'لا توجد إحصائيات متاحة' : 'No statistics available'; ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Client Reviews Section -->
    <section class="reviews-section" style="padding: 80px 0; background: #f8f9fa;">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge">
            <i class="bi bi-chat-heart"></i>
            <?php echo ($lang == 'ar') ? 'آراء العملاء' : 'Client Reviews'; ?>
          </div>
          <h2 class="section-title"><?php echo ($lang == 'ar') ? 'ماذا يقول عملاؤنا' : 'What Our Clients Say'; ?></h2>
          <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'ثقة عملائنا هي شهادتنا الحقيقية' : 'Our clients trust is our real certificate'; ?></p>
        </div>

        <div class="reviews-slider-container">
          <button class="slider-arrow prev-arrow">
            <i class="bi bi-chevron-<?php echo ($lang == 'ar') ? 'right' : 'left'; ?>"></i>
          </button>

          <div class="swiper reviews-swiper">
            <div class="swiper-wrapper">
              <!-- Review 1 -->
              <div class="swiper-slide">
                <div class="review-card" data-aos="fade-up" data-aos-delay="100">
                  <div class="review-content">
                    <div class="stars">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="review-text">
                      "<?php echo ($lang == 'ar') ? 'جودة المنتجات وخدمة العملاء ممتازة. أنصح بالتعامل معهم' : 'Product quality and customer service are excellent. I recommend dealing with them'; ?>"
                    </p>
                  </div>
                  <div class="review-author">
                    <div class="author-avatar">
                      <img src="assets/img/clients/client1.jpg" alt="<?php echo ($lang == 'ar') ? 'أحمد محمد' : 'Ahmed Mohammed'; ?>">
                    </div>
                    <div class="author-info">
                      <h4><?php echo ($lang == 'ar') ? 'أحمد محمد' : 'Ahmed Mohammed'; ?></h4>
                      <p><?php echo ($lang == 'ar') ? 'مدير مشاريع' : 'Project Manager'; ?></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Review 2 -->
              <div class="swiper-slide">
                <div class="review-card" data-aos="fade-up" data-aos-delay="200">
                  <div class="review-content">
                    <div class="stars">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <p class="review-text">
                      "<?php echo ($lang == 'ar') ? 'خدمة محترفة وسريعة. فريق العمل متعاون جداً' : 'Professional and fast service. The team is very cooperative'; ?>"
                    </p>
                  </div>
                  <div class="review-author">
                    <div class="author-avatar">
                      <img src="assets/img/clients/client2.jpg" alt="<?php echo ($lang == 'ar') ? 'فاطمة عبدالله' : 'Fatima Abdullah'; ?>">
                    </div>
                    <div class="author-info">
                      <h4><?php echo ($lang == 'ar') ? 'فاطمة عبدالله' : 'Fatima Abdullah'; ?></h4>
                      <p><?php echo ($lang == 'ar') ? 'مسؤولة سلامة' : 'Safety Officer'; ?></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Review 3 -->
              <div class="swiper-slide">
                <div class="review-card" data-aos="fade-up" data-aos-delay="300">
                  <div class="review-content">
                    <div class="stars">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="review-text">
                      "<?php echo ($lang == 'ar') ? 'التزام بالمواعيد وجودة في التنفيذ. شكراً لكم' : 'Commitment to deadlines and quality in execution. Thank you'; ?>"
                    </p>
                  </div>
                  <div class="review-author">
                    <div class="author-avatar">
                      <img src="assets/img/clients/client3.jpg" alt="<?php echo ($lang == 'ar') ? 'خالد إبراهيم' : 'Khaled Ibrahim'; ?>">
                    </div>
                    <div class="author-info">
                      <h4><?php echo ($lang == 'ar') ? 'خالد إبراهيم' : 'Khaled Ibrahim'; ?></h4>
                      <p><?php echo ($lang == 'ar') ? 'مهندس معماري' : 'Architect'; ?></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Review 4 -->
              <div class="swiper-slide">
                <div class="review-card" data-aos="fade-up" data-aos-delay="400">
                  <div class="review-content">
                    <div class="stars">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="review-text">
                      "<?php echo ($lang == 'ar') ? 'الدعم الفني ممتاز والمنتجات عالية الجودة' : 'Technical support is excellent and products are high quality'; ?>"
                    </p>
                  </div>
                  <div class="review-author">
                    <div class="author-avatar">
                      <img src="assets/img/clients/client4.jpg" alt="<?php echo ($lang == 'ar') ? 'سارة القحطاني' : 'Sara Al-Qahtani'; ?>">
                    </div>
                    <div class="author-info">
                      <h4><?php echo ($lang == 'ar') ? 'سارة القحطاني' : 'Sara Al-Qahtani'; ?></h4>
                      <p><?php echo ($lang == 'ar') ? 'مديرة تشغيل' : 'Operations Manager'; ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button class="slider-arrow next-arrow">
            <i class="bi bi-chevron-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?>"></i>
          </button>
        </div>

        <div class="slider-pagination"></div>

        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="500">
          <a href="contact.php" class="btn-view-all">
            <i class="bi bi-chat-left-text"></i>
            <?php echo ($lang == 'ar') ? 'شاركنا رأيك' : 'Share Your Opinion'; ?>
          </a>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true,
          offset: 100
        });
      }

      if (typeof PureCounter !== 'undefined') {
        new PureCounter();
      }

      class HeroSlider {
        constructor() {
          this.slides = document.querySelectorAll('.hero-slide');
          this.dots = document.querySelectorAll('.hero-dot');
          this.prevBtn = document.querySelector('.hero-prev');
          this.nextBtn = document.querySelector('.hero-next');
          this.currentSlide = 0;
          this.slideInterval = null;
          this.slideDuration = 3000;

          this.init();
        }

        init() {
          this.prevBtn.addEventListener('click', () => this.prevSlide());
          this.nextBtn.addEventListener('click', () => this.nextSlide());
          
          this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
          });

          this.startAutoSlide();

          const slider = document.querySelector('.hero-slider');
          slider.addEventListener('mouseenter', () => this.stopAutoSlide());
          slider.addEventListener('mouseleave', () => this.startAutoSlide());

          this.addTouchSupport();
        }

        showSlide(index) {
          this.slides.forEach(slide => slide.classList.remove('active'));
          this.dots.forEach(dot => dot.classList.remove('active'));

          this.slides[index].classList.add('active');
          this.dots[index].classList.add('active');
          this.currentSlide = index;
        }

        nextSlide() {
          let next = this.currentSlide + 1;
          if (next >= this.slides.length) next = 0;
          this.showSlide(next);
        }

        prevSlide() {
          let prev = this.currentSlide - 1;
          if (prev < 0) prev = this.slides.length - 1;
          this.showSlide(prev);
        }

        goToSlide(index) {
          this.showSlide(index);
        }

        startAutoSlide() {
          this.stopAutoSlide();
          this.slideInterval = setInterval(() => this.nextSlide(), this.slideDuration);
        }

        stopAutoSlide() {
          if (this.slideInterval) {
            clearInterval(this.slideInterval);
            this.slideInterval = null;
          }
        }

        addTouchSupport() {
          const slider = document.querySelector('.hero-slider');
          let startX = 0;
          let endX = 0;

          slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
          });

          slider.addEventListener('touchmove', (e) => {
            endX = e.touches[0].clientX;
          });

          slider.addEventListener('touchend', () => {
            const diff = startX - endX;
            const threshold = 50;

            if (Math.abs(diff) > threshold) {
              if (diff > 0) {
                this.nextSlide();
              } else {
                this.prevSlide();
              }
            }
          });
        }
      }

      if (document.querySelector('.hero-slider')) {
        new HeroSlider();
      }

      

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
    });
         // Initialize Reviews Slider
      const reviewsSwiper = new Swiper('.reviews-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.next-arrow',
          prevEl: '.prev-arrow',
        },
        pagination: {
          el: '.slider-pagination',
          clickable: true,
          renderBullet: function (index, className) {
            return '<span class="swiper-pagination-bullet ' + className + '"></span>';
          },
        },
        breakpoints: {
          768: {
            slidesPerView: 2,
          },
          1200: {
            slidesPerView: 3,
          }
        }
      });

      // Add hover effects to review cards
      const reviewCards = document.querySelectorAll('.review-card');
      reviewCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
        });
      });
  </script>

</body>
</html>