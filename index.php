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
  <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">

  <title><?php echo ($lang == 'ar') ? 'ركن الأماسي - منتجات وخدمات متميزة' : 'Rukn Alamasy - Premium Products & Services'; ?></title>
  <meta name="description" content="<?php echo ($lang == 'ar') ? 'اكتشف منتجات وخدمات استثنائية مع ركن الأماسي' : 'Discover exceptional products and services with Rukn Alamasy'; ?>">
  <meta name="keywords" content="<?php echo ($lang == 'ar') ? 'منتجات متميزة، خدمات عالية الجودة، حلول أعمال' : 'premium products, quality services, business solutions'; ?>">
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
  <!-- مكتبات جديدة لإضافة الجاذبية -->
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
  
  <link href="assets/css/index.css" rel="stylesheet">
  
  <style>
    
  </style>
</head>

<body class="index-page">
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

  <!-- تأثيرات الجسيمات -->
  <div id="particles-js"></div>

  <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- ========================== -->
    <!-- Hero Section - السينمائي -->
    <!-- ========================== -->
    <section class="hero-section">
      <div class="hero-slider">
        <?php if (!empty($banners)): ?>
          <?php foreach ($banners as $index => $banner): ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('assets/img/banners/<?php echo htmlspecialchars($banner['image']); ?>')">
              <div class="container">
                <div class="hero-content">
                  <div class="hero-text fade-in-up">
                    <div class="hero-badge floating-element">
                      <?php echo ($lang == 'ar') ? 'متميزون في الجودة' : 'Excellence in Quality'; ?>
                    </div>
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                      <?php echo htmlspecialchars($banner['title']); ?>
                    </h1>
                    <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                      <?php echo htmlspecialchars($banner['description']); ?>
                    </p>
                    <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-2s">
                      <a href="<?php echo htmlspecialchars($banner['button_link']); ?>" 
                         class="btn-hero btn-primary-hero floating-element">
                        <i class="bi bi-arrow-<?php echo ($lang == 'ar') ? 'left' : 'right'; ?> me-2"></i>
                        <?php echo htmlspecialchars($banner['button_text']); ?>
                      </a>
                      <a href="about.php" class="btn-hero btn-outline-hero floating-element" 
                         style="animation-delay: 0.2s">
                        <i class="bi bi-info-circle me-2"></i>
                        <?php echo ($lang == 'ar') ? 'اعرف المزيد' : 'Learn More'; ?>
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
                  <div class="hero-badge floating-element">
                    <?php echo ($lang == 'ar') ? 'الريادة في الأمن والسلامة' : 'Leadership in Safety & Security'; ?>
                  </div>
                  <h1 class="hero-title animate__animated animate__fadeInDown">
                    <?php echo ($lang == 'ar') ? 'ركن الأماسي<br>حماية استثنائية' : 'Rukn Alamasy<br>Exceptional Protection'; ?>
                  </h1>
                  <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                    <?php echo ($lang == 'ar') ? 'نقدم حلولاً متكاملة في مجال معدات الأمن والسلامة بمهنية واحترافية عالية.<br>نحن نضمن لك بيئة عمل آمنة ومنتجات عالية الجودة.' : 'We provide integrated solutions in safety and security equipment with high professionalism.<br>We ensure you a safe work environment with high-quality products.'; ?>
                  </p>
                  <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="products.php" class="btn-hero btn-primary-hero floating-element">
                      <i class="bi bi-shield-check me-2"></i>
                      <?php echo ($lang == 'ar') ? 'اكتشف منتجاتنا' : 'Discover Our Products'; ?>
                    </a>
                    <a href="contact.php" class="btn-hero btn-outline-hero floating-element" 
                       style="animation-delay: 0.2s">
                      <i class="bi bi-chat-dots me-2"></i>
                      <?php echo ($lang == 'ar') ? 'احصل على استشارة' : 'Get a Consultation'; ?>
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
              <div class="hero-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                   data-slide="<?php echo $index; ?>"></div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="hero-dot active" data-slide="0"></div>
            <div class="hero-dot" data-slide="1"></div>
            <div class="hero-dot" data-slide="2"></div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ========================== -->
    <!-- باقي الأقسام كما هي -->
    <!-- ========================== -->

    <!-- Services Section with Cards -->
    <section class="services-section bg-pattern" id="services">
    <div id="particles-js-2"></div>

    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge floating-element">
                <i class="bi bi-gear-fill"></i>
                <?php echo ($lang == 'ar') ? 'خدماتنا المتكاملة' : 'Our Integrated Services'; ?>
            </div>
            <h2 class="section-title"><?php echo ($lang == 'ar') ? 'حلول متكاملة لجميع احتياجاتك' : 'Integrated Solutions for All Your Needs'; ?></h2>
            <p class="section-subtitle"><?php echo ($lang == 'ar') ? 'نقدم مجموعة شاملة من الخدمات المصممة خصيصاً لتلبية احتياجاتكم باحترافية عالية' : 'We offer a comprehensive range of services specifically designed to meet your needs with high professionalism'; ?></p>
        </div>

        <div class="row gy-5">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $index => $service): ?>
                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="service-card">
                            <div class="service-number"><?php echo $index + 1; ?></div>
                            <div class="service-icon">
                                <i class="<?php echo isset($service['icon']) ? htmlspecialchars($service['icon']) : 'bi bi-gear'; ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p><?php echo htmlspecialchars($service['description']); ?></p>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

    <!-- Products Section -->
    <section class="products-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
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
                      <i class="bi bi-tag"></i>
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

    <!-- Features Section -->
    <section class="features-section">
        <div id="particles-js-3"></div>

      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
            <i class="bi bi-stars"></i>
            <?php echo ($lang == 'ar') ? 'لماذا تختارنا' : 'Why Choose Us'; ?>
          </div>
          <h2 class="section-title" style="color: white;"><?php echo ($lang == 'ar') ? 'مميزاتنا الاستثنائية' : 'Our Exceptional Features'; ?></h2>
          <p class="section-subtitle" style="color: rgba(255,255,255,0.9);"><?php echo ($lang == 'ar') ? 'اكتشف ما يجعلنا مختلفين وأفضل' : 'Discover what makes us different and better'; ?></p>
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

    <!-- About Section -->
    <section class="simple-about-section" style="padding: 80px 0; background: #f8f9fa;">
          <div id="particles-js-5"></div>

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
                <?php echo ($lang == 'ar') ? 'ركن الأماسي<br>شركتك الموثوقة للأمن والسلامة' : 'Rukn Al-Amasy<br>Your Trusted Safety & Security Partner'; ?>
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

    <!-- Statistics Section -->
    <section class="stats-section">
              <div id="particles-js-4"></div>

      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element" style="background: rgba(255,255,255,0.2);">
            <i class="bi bi-graph-up-arrow"></i>
            <?php echo ($lang == 'ar') ? 'إنجازاتنا' : 'Our Achievements'; ?>
          </div>
          <h2 class="section-title" style="color: white;"><?php echo ($lang == 'ar') ? 'إنجازاتنا بالأرقام' : 'Our Achievements in Numbers'; ?></h2>
          <p class="section-subtitle" style="color: rgba(255,255,255,0.9);"><?php echo ($lang == 'ar') ? 'أرقام تتحدث عن نجاحنا وتفانينا' : 'Numbers that speak about our success and dedication'; ?></p>
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
    <section class="reviews-section">
      <div class="container">
        <div class="section-header" data-aos="fade-up">
          <div class="section-badge floating-element">
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
                      <img src="assets/img/6952312cc0531-1766994220.jpg" alt="<?php echo ($lang == 'ar') ? 'أحمد محمد' : 'Ahmed Mohammed'; ?>">
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
                      <img src="assets/img/6952312cc0531-1766994220.jpg" alt="<?php echo ($lang == 'ar') ? 'فاطمة عبدالله' : 'Fatima Abdullah'; ?>">
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
                      <img src="assets/img/6952312cc0531-1766994220.jpg" alt="<?php echo ($lang == 'ar') ? 'خالد إبراهيم' : 'Khaled Ibrahim'; ?>">
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
                      <img src="assets/img/6952312cc0531-1766994220.jpg" alt="<?php echo ($lang == 'ar') ? 'سارة القحطاني' : 'Sara Al-Qahtani'; ?>">
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

       
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <a href="#" class="scroll-top" id="scroll-top">
    <i class="bi bi-arrow-up"></i>
  </a>
  
  <!-- مكتبات جديدة مضافة -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    // تحميل المكتبات الثقيلة بعد تحميل الصفحة
window.addEventListener('load', function() {
    // مكتبات التأثيرات
    const scripts = [
        'https://unpkg.com/aos@2.3.4/dist/aos.js',
        'https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js',
        'assets/vendor/swiper/swiper-bundle.min.js'
    ];
    
    scripts.forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        document.body.appendChild(script);
    });
});
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

      // تأثيرات الجسيمات للهيرو سكشن
      if (typeof particlesJS !== 'undefined') {
        particlesJS('particles-js', {
          particles: {
            number: {
              value: 40,
              density: {
                enable: true,
                value_area: 1000
              }
            },
            color: {
              value: ["#e76a04", "#f3d417", "#ffffff", "#144734"]
            },
            shape: {
              type: ["circle", "triangle", "star"],
              stroke: {
                width: 0,
                color: "#000000"
              }
            },
            opacity: {
              value: 0.8,
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
                speed: 3,
                size_min: 0.1,
                sync: false
              }
            },
            line_linked: {
              enable: true,
              distance: 150,
              color: "#f3d417",
              opacity: 0.3,
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
                mode: "repulse"
              },
              onclick: {
                enable: true,
                mode: "push"
              },
              resize: true
            },
            modes: {
              repulse: {
                distance: 100,
                duration: 0.4
              },
              push: {
                particles_nb: 6
              }
            }
          },
          retina_detect: true
        });
      }

      // Hero Slider with Cinematic Transitions
      class CinematicHeroSlider {
        constructor() {
          this.slides = document.querySelectorAll('.hero-slide');
          this.dots = document.querySelectorAll('.hero-dot');
          this.prevBtn = document.querySelector('.hero-prev');
          this.nextBtn = document.querySelector('.hero-next');
          this.currentSlide = 0;
          this.slideInterval = null;
          this.slideDuration = 7000;
          this.isTransitioning = false;
          
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
          this.addKeyboardSupport();
        }
        
        showSlide(index) {
          if (this.isTransitioning) return;
          this.isTransitioning = true;
          
          // Hide current slide with cinematic effect
          this.slides[this.currentSlide].style.opacity = '0';
          this.slides[this.currentSlide].style.transform = 'scale(1.1)';
          this.dots[this.currentSlide].classList.remove('active');
          
          setTimeout(() => {
            // Show new slide with cinematic effect
            this.slides[index].style.opacity = '1';
            this.slides[index].style.transform = 'scale(1)';
            this.dots[index].classList.add('active');
            this.currentSlide = index;
            
            // Add entry animation to content
            const content = this.slides[index].querySelector('.hero-content');
            content.style.animation = 'none';
            setTimeout(() => {
              content.style.animation = 'fadeInUp 1.5s ease forwards';
            }, 50);
            
            this.isTransitioning = false;
          }, 600);
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
          if (index !== this.currentSlide) {
            this.showSlide(index);
          }
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
          let startY = 0;
          let endX = 0;
          let endY = 0;
          
          slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            this.stopAutoSlide();
          });
          
          slider.addEventListener('touchmove', (e) => {
            endX = e.touches[0].clientX;
            endY = e.touches[0].clientY;
          });
          
          slider.addEventListener('touchend', () => {
            const diffX = startX - endX;
            const diffY = startY - endY;
            
            // Only horizontal swipe with minimal vertical movement
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
              if (diffX > 0) {
                this.nextSlide();
              } else {
                this.prevSlide();
              }
            }
            
            this.startAutoSlide();
          });
        }
        
        addKeyboardSupport() {
          document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
              this.prevSlide();
            } else if (e.key === 'ArrowRight') {
              this.nextSlide();
            }
          });
        }
      }
      
      if (document.querySelector('.hero-slider')) {
        new CinematicHeroSlider();
      }

      // Reviews Slider
      const reviewsSwiper = new Swiper('.reviews-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
          delay: 5000,
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

      // تأثيرات hover للبطاقات
      const cards = document.querySelectorAll('.service-card, .review-card, .feature-card, .product-card-home, .stats-card');
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
      document.querySelectorAll('.btn, .btn-service, .btn-view-details, .btn-hero').forEach(btn => {
        btn.addEventListener('click', function(e) {
          // تأثير النقرة
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

    // Animate elements on scroll
    window.addEventListener('scroll', function() {
      const serviceCards = document.querySelectorAll('.service-card');
      serviceCards.forEach(card => {
        const cardPosition = card.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.2;
        
        if (cardPosition < screenPosition) {
          card.classList.add('animate__animated', 'animate__fadeInUp');
        }
      });
    });

// تأثيرات الجسيمات للأقسام الأخرى
particlesJS('particles-js-2', {
    "particles": {
      "number": {
        "value": 50,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": {
        "value": "#144734"
      },
      "shape": {
        "type": "circle",
        "stroke": { "width": 0, "color": "#144734" }
      },
      "opacity": {
        "value": 0.8,
        "random": true,
        "anim": { "enable": false }
      },
      "size": {
        "value": 4,
        "random": true,
        "anim": { "enable": false }
      },
      "line_linked": {
        "enable": false,
        "distance": 150,
        "color": "#144734",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": true,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "grab" },
        "onclick": { "enable": false }
      }
    },
    "retina_detect": true
  });
  particlesJS('particles-js-3', {
    "particles": {
      "number": {
        "value": 50,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": {
        "value": "#ffffff"
      },
      "shape": { "type": "circle" },
      "opacity": {
        "value": 0.3,
        "random": true
      },
      "size": {
        "value": 3,
        "random": true
      },
      "line_linked": {
        "enable": true,
        "distance": 150,
        "color": "#ffffff",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": false,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "bubble" },
        "onclick": { "enable": false }
      },
      "modes": {
        "bubble": { "distance": 200, "size": 6, "duration": 2, "opacity": 0.8 }
      }
    },
    "retina_detect": true
  });

  particlesJS('particles-js-4', {
    "particles": {
      "number": {
        "value": 60,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": {
        "value": "#ffffff"
      },
      "shape": { "type": "circle" },
      "opacity": {
        "value": 0.3,
        "random": true
      },
      "size": {
        "value": 3,
        "random": true
      },
      "line_linked": {
        "enable": false,
        "distance": 150,
        "color": "#ffffff",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": false,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "bubble" },
        "onclick": { "enable": false }
      },
      "modes": {
        "bubble": { "distance": 200, "size": 6, "duration": 2, "opacity": 0.8 }
      }
    },
    "retina_detect": true
  });
  particlesJS('particles-js-5', {
    "particles": {
      "number": {
        "value": 50,
        "density": { "enable": true, "value_area": 800 }
      },
      "color": {
        "value": "#144734"
      },
      "shape": {
        "type": "circle",
        "stroke": { "width": 0, "color": "#144734" }
      },
      "opacity": {
        "value": 0.8,
        "random": true,
        "anim": { "enable": false }
      },
      "size": {
        "value": 4,
        "random": true,
        "anim": { "enable": false }
      },
      "line_linked": {
        "enable": false,
        "distance": 150,
        "color": "#144734",
        "opacity": 0.2,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 2,
        "direction": "none",
        "random": true,
        "straight": false,
        "out_mode": "out",
        "bounce": false
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "grab" },
        "onclick": { "enable": false }
      }
    },
    "retina_detect": true
  });
  </script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</body>
</html>