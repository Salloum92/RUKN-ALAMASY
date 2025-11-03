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
$query = new Database();

// جلب بيانات about من قاعدة البيانات
$aboutData = $query->select('about');
$statistics = $query->select('statistics');

// تحديد اللغة
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= ($lang == 'ar') ? 'rtl' : 'ltr' ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= ($lang == 'ar') ? 'من نحن - ركن السلامة' : 'About Us - Safety Corner' ?></title>
  <meta name="description" content="شركة رائدة في توفير حلول الأمن والسلامة المتكاملة - معدات إطفاء، أنظمة مراقبة، معدات حماية شخصية">
  <meta name="keywords" content="معدات أمن، سلامة، إطفاء، مراقبة، حماية شخصية">
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
    :root {
      --primary-color: #e74c3c;
      --primary-dark: #c0392b;
      --secondary-color: #2c3e50;
      --accent-color: #3498db;
      --text-dark: #2c3e50;
      --text-light: #6c757d;
      --bg-light: #f8f9fa;
      --white: #ffffff;
      --shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
      --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
      --border-radius: 15px;
      --transition: all 0.3s ease;
    }

    /* Hero Section */
    .about-hero-section {
      background: linear-gradient(135deg, var(--primary-color) 0%, #fff 100%);
      color: var(--white);
      padding: 140px 0 80px;
      position: relative;
      overflow: hidden;
    }

    .about-hero-content {
      text-align: center;
      max-width: 800px;
      margin: 0 auto;
    }

    .about-hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      color: black;
      line-height: 1.2;
    }

    .about-hero-subtitle {
      font-size: 1.3rem;
      color: black;
      margin-bottom: 2.5rem;
      line-height: 1.6;
    }

    /* Story Section */
    .about-story-section {
      padding: 100px 0;
      background: var(--white);
    }

    .about-story-content {
      padding-right: 3rem;
    }

    [dir="rtl"] .about-story-content {
      padding-right: 0;
      padding-left: 3rem;
    }

    .about-story-content h2 {
      color: var(--text-dark);
      font-weight: 700;
      margin-bottom: 1.5rem;
      font-size: 2.3rem;
    }

    .about-story-content h3 {
      color: var(--primary-color);
      font-weight: 600;
      margin: 2rem 0 1.5rem;
      font-size: 1.6rem;
    }

    .about-story-content p {
      color: var(--text-light);
      font-size: 1.1rem;
      line-height: 1.8;
      margin-bottom: 1.5rem;
    }

    .about-story-list {
      list-style: none;
      padding: 0;
      margin: 2rem 0;
    }

    .about-story-list li {
      padding: 1rem 0;
      border-bottom: 1px solid #e9ecef;
      color: var(--text-dark);
      font-size: 1.1rem;
      display: flex;
      align-items: center;
    }

    .about-story-list li:last-child {
      border-bottom: none;
    }

    .about-story-list li i {
      color: var(--primary-color);
      margin-right: 12px;
      font-size: 1.3rem;
    }

    [dir="rtl"] .about-story-list li i {
      margin-right: 0;
      margin-left: 12px;
    }

    .about-story-image {
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: var(--shadow-hover);
      position: relative;
    }

    .about-story-image img {
      width: 100%;
      height: auto;
      border-radius: var(--border-radius);
      transition: var(--transition);
    }

    .about-story-image:hover img {
      transform: scale(1.05);
    }

    /* Mission Vision Section */
    .about-mission-vision-section {
      padding: 100px 0;
      background: var(--bg-light);
    }

    .about-mission-card, .about-vision-card {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 3rem 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: none;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .about-mission-card::before, .about-vision-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .about-mission-card:hover, .about-vision-card:hover {
      transform: translateY(-10px);
      box-shadow: var(--shadow-hover);
    }

    .about-mission-icon, .about-vision-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
      color: var(--white);
      font-size: 2rem;
      transition: var(--transition);
    }

    .about-mission-card:hover .about-mission-icon, .about-vision-card:hover .about-vision-icon {
      transform: scale(1.1) rotate(5deg);
    }

    .about-mission-card h3, .about-vision-card h3 {
      color: var(--text-dark);
      font-weight: 700;
      margin-bottom: 1rem;
      font-size: 1.8rem;
    }

    .about-mission-card p, .about-vision-card p {
      color: var(--text-light);
      font-size: 1.1rem;
      line-height: 1.7;
      margin-bottom: 0;
    }

    /* Statistics Section */
    .about-stats-section {
      padding: 100px 0;
      background: linear-gradient(135deg, var(--secondary-color) 0%, #34495e 100%);
      color: var(--white);
      position: relative;
      overflow: hidden;
    }

    .about-stats-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.03"><polygon fill="white" points="0,1000 1000,0 1000,1000"/></svg>');
    }

    .about-stats-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      padding: 3rem 2rem;
      text-align: center;
      transition: var(--transition);
      border: 1px solid rgba(255, 255, 255, 0.2);
      height: 100%;
      position: relative;
      z-index: 1;
    }

    .about-stats-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 255, 255, 0.15);
    }

    .about-stats-icon {
      width: 70px;
      height: 70px;
      background: var(--primary-color);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      color: var(--white);
      font-size: 1.8rem;
    }

    .about-stats-number {
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      color: var(--white);
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .about-stats-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }

    .about-stats-description {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      line-height: 1.6;
    }

    /* Expertise Section */
    .about-expertise-section {
      padding: 100px 0;
      background: var(--white);
    }

    .about-expertise-card {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 2.5rem 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: none;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .about-expertise-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }

    .about-expertise-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      color: var(--white);
      font-size: 2rem;
      transition: var(--transition);
    }

    .about-expertise-card:hover .about-expertise-icon {
      transform: scale(1.1);
    }

    .about-expertise-card h4 {
      color: var(--text-dark);
      font-weight: 700;
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }

    .about-expertise-card p {
      color: var(--text-light);
      font-size: 1rem;
      line-height: 1.6;
      margin-bottom: 0;
    }

    /* Values Section */
    .about-values-section {
      padding: 100px 0;
      background: var(--bg-light);
    }

    .about-value-card {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 2.5rem 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: none;
      height: 100%;
      position: relative;
    }

    .about-value-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }

    .about-value-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      color: var(--white);
      font-size: 1.8rem;
      transition: var(--transition);
    }

    .about-value-card:hover .about-value-icon {
      transform: scale(1.1);
    }

    .about-value-card h4 {
      color: var(--text-dark);
      font-weight: 700;
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }

    .about-value-card p {
      color: var(--text-light);
      font-size: 1rem;
      line-height: 1.6;
      margin-bottom: 0;
    }

    /* Partners Section */
    .about-partners-section {
      padding: 80px 0;
      background: var(--white);
    }

    .about-partner-logo {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: 1px solid #e9ecef;
      height: 120px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .about-partner-logo:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }

    .about-partner-logo img {
      max-width: 100%;
      max-height: 60px;
      filter: grayscale(100%);
      transition: var(--transition);
    }

    .about-partner-logo:hover img {
      filter: grayscale(0%);
    }

    /* CTA Section */
    .about-cta-section {
      padding: 100px 0;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: var(--white);
      text-align: center;
    }

    .about-cta-content h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .about-cta-content p {
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      opacity: 0.9;
    }

    .about-btn-cta {
      background: var(--white);
      color: var(--primary-color);
      border: none;
      padding: 15px 35px;
      border-radius: 30px;
      font-weight: 600;
      transition: var(--transition);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 1.1rem;
    }

    .about-btn-cta:hover {
      background: var(--secondary-color);
      color: var(--white);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Section Titles */
    .about-section-title {
      text-align: center;
      margin-bottom: 4rem;
    }

    .about-section-title h2 {
      font-size: 2.8rem;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 1rem;
      position: relative;
      display: inline-block;
    }

    .about-section-title h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      border-radius: 2px;
    }

    .about-section-title p {
      font-size: 1.2rem;
      color: var(--text-light);
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
      .about-hero-title {
        font-size: 3rem;
      }
      
      .about-section-title h2 {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 992px) {
      .about-hero-section {
        padding: 120px 0 60px;
      }
      
      .about-hero-title {
        font-size: 2.5rem;
      }
      
      .about-story-content {
        padding-right: 0;
        padding-left: 0;
        margin-bottom: 3rem;
      }
      
      .about-section-title h2 {
        font-size: 2.2rem;
      }
    }

    @media (max-width: 768px) {
      .about-hero-title {
        font-size: 2.2rem;
      }
      
      .about-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .about-section-title h2 {
        font-size: 2rem;
      }
      
      .about-mission-card, .about-vision-card, .about-stats-card, .about-expertise-card, .about-value-card {
        padding: 2rem 1.5rem;
      }
      
      .about-stats-number {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 576px) {
      .about-hero-title {
        font-size: 1.8rem;
      }
      
      .about-section-title h2 {
        font-size: 1.8rem;
      }
      
      .about-section-title p {
        font-size: 1rem;
      }
      
      .about-cta-content h2 {
        font-size: 2rem;
      }
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

    .about-fade-in-up {
      animation: fadeInUp 0.8s ease-out;
    }
  </style>
</head>

<body class="about-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <section class="about-hero-section">
      <div class="container">
        <div class="about-hero-content about-fade-in-up">
          <h1 class="display-4 fw-bold mb-3">من نحن</h1>
          <p class="about-hero-subtitle">شركة رائدة في توفير حلول الأمن والسلامة المتكاملة</p>
        </div>
      </div>
    </section>

    <!-- Story Section -->
    <section class="about-story-section">
      <div class="container">
        <div class="row gy-5 align-items-center">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="about-story-image">
              <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); height: 400px; border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="about-story-content">
              <h2>قصتنا</h2>
              <h3>ركن السلامة - شركتك الموثوقة للأمن والسلامة</h3>
              <p>تأسست شركة ركن السلامة بهدف واحد واضح: توفير أفضل حلول الأمن والسلامة للشركات والمؤسسات في جميع أنحاء المنطقة. نحن نؤمن بأن السلامة ليست مجرد منتج، بل هي ثقافة ومسؤولية.</p>
              
              <h3>لماذا تختار ركن السلامة</h3>
              <ul class="about-story-list">
                <li><i class="bi bi-check2-all"></i> خبرة تزيد عن 10 سنوات في مجال معدات الأمن والسلامة</li>
                <li><i class="bi bi-check2-all"></i> منتجات معتمدة محلياً ودولياً من أفضل العلامات التجارية</li>
                <li><i class="bi bi-check2-all"></i> فريق دعم فني متخصص على أعلى مستوى من التدريب</li>
                <li><i class="bi bi-check2-all"></i> خدمة ما بعد البيع متكاملة ومستمرة</li>
                <li><i class="bi bi-check2-all"></i> أسعار تنافسية مع الحفاظ على أعلى معايير الجودة</li>
              </ul>
              
              <p>نحن لا نبيع منتجات فقط، بل نقدم حلولاً متكاملة تضمن سلامة منشآتك وموظفيك. نعمل معك من مرحلة التخطيط وحتى التنفيذ والصيانة الدورية.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="about-mission-vision-section">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="about-mission-card">
              <div class="about-mission-icon">
                <i class="bi bi-bullseye"></i>
              </div>
              <h3>مهمتنا</h3>
              <p>
                توفير حلول أمنية ووقائية متكاملة وعالية الجودة لحماية الأرواح والممتلكات، مع الالتزام بأعلى معايير السلامة العالمية والمساهمة في بناء مجتمع آمن.
              </p>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="about-vision-card">
              <div class="about-vision-icon">
                <i class="bi bi-eye"></i>
              </div>
              <h3>رؤيتنا</h3>
              <p>
                أن نكون الشريك الأمثل في توفير حلول الأمن والسلامة، والمساهمة في بناء مجتمع آمن من خلال تقديم منتجات مبتكرة وخدمات استشارية متخصصة.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="about-stats-section">
      <div class="container">
        <div class="about-section-title" data-aos="fade-up">
          <h2 style="color: white;">إنجازاتنا</h2>
        </div>

        <div class="row">
          <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="about-stats-card">
              <div class="about-stats-icon">
                <i class="bi bi-award"></i>
              </div>
              <div class="about-stats-number">
                <span data-purecounter-start="0" data-purecounter-end="10" data-purecounter-duration="2" class="purecounter">10</span>+
              </div>
              <div class="about-stats-title">سنوات من الخبرة</div>
              <div class="about-stats-description">في مجال معدات الأمن والسلامة</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="about-stats-card">
              <div class="about-stats-icon">
                <i class="bi bi-people"></i>
              </div>
              <div class="about-stats-number">
                <span data-purecounter-start="0" data-purecounter-end="1500" data-purecounter-duration="2" class="purecounter">1500</span>+
              </div>
              <div class="about-stats-title">عميل سعيد</div>
              <div class="about-stats-description">من القطاعات الحكومية والخاصة</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="about-stats-card">
              <div class="about-stats-icon">
                <i class="bi bi-box-seam"></i>
              </div>
              <div class="about-stats-number">
                <span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="2" class="purecounter">500</span>+
              </div>
              <div class="about-stats-title">منتج أمان</div>
              <div class="about-stats-description">من معدات الحماية والسلامة</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="about-stats-card">
              <div class="about-stats-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="about-stats-number">
                <span data-purecounter-start="0" data-purecounter-end="25" data-purecounter-duration="2" class="purecounter">25</span>+
              </div>
              <div class="about-stats-title">شهادة معتمدة</div>
              <div class="about-stats-description">محلياً ودولياً</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Expertise Section -->
    <section class="about-expertise-section">
      <div class="container">
        <div class="about-section-title" data-aos="fade-up">
          <h2>مجالات تخصصنا</h2>
          <p>نقدم مجموعة شاملة من منتجات وخدمات الأمن والسلامة</p>
        </div>

        <div class="row">
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-fire"></i>
              </div>
              <h4>معدات الإطفاء</h4>
              <p>طفايات حريق، أنظمة إنذار، أنظمة إطفاء آلي، خراطيم حريق، صنابير حريق، وكل ما يتعلق بمكافحة الحرائق.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-person-gear"></i>
              </div>
              <h4>معدات الحماية الشخصية</h4>
              <p>خوذات، نظارات، قفازات، أحذية سلامة، ملابس عاكسة، وأجهزة تنفس لحماية العاملين في مختلف المجالات.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-camera-video"></i>
              </div>
              <h4>أنظمة المراقبة والأمن</h4>
              <p>كاميرات مراقبة، أنظمة إنذار ضد السرقة، أنظمة تحكم بالدخول، أجهزة كشف المعادن، وأنظمة حماية المحيط.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-shield-exclamation"></i>
              </div>
              <h4>أنظمة الإنذار</h4>
              <p>أنظمة إنذار للدخان، الحرارة، الغازات الخطرة، التسربات، الاختراقات الأمنية، والطوارئ المختلفة.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-file-medical"></i>
              </div>
              <h4>معدات الإسعافات الأولية</h4>
              <p>حقائب إسعافات أولية، صناديق إسعاف، أجهزة إنعاش، أدوات طبية طارئة، ومعدات الإخلاء الطبي.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
            <div class="about-expertise-card">
              <div class="about-expertise-icon">
                <i class="bi bi-clipboard-check"></i>
              </div>
              <h4>الاستشارات والتدريب</h4>
              <p>تقييم مخاطر، تدريب على استخدام المعدات، خطط طوارئ، استشارات سلامة، وبرامج توعوية متخصصة.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="about-values-section">
      <div class="container">
        <div class="about-section-title" data-aos="fade-up">
          <h2>قيمنا</h2>
          <p>المبادئ التي نؤمن بها ونسير عليها في كل ما نقدمه</p>
        </div>

        <div class="row">
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="about-value-card">
              <div class="about-value-icon">
                <i class="bi bi-gem"></i>
              </div>
              <h4>الجودة</h4>
              <p>نلتزم بأعلى معايير الجودة في كل ما نقدمه من منتجات وخدمات لضمان سلامة عملائنا وممتلكاتهم.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="about-value-card">
              <div class="about-value-icon">
                <i class="bi bi-lightbulb"></i>
              </div>
              <h4>الابتكار</h4>
              <p>نسعى دائماً للتطوير والابتكار لتقديم حلول أمنية متطورة تلبي احتياجات العملاء المتغيرة.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="about-value-card">
              <div class="about-value-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <h4>الموثوقية</h4>
              <p>نحرص على بناء علاقات قائمة على الثقة والشفافية مع عملائنا وشركائنا في جميع تعاملاتنا.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta-section">
      <div class="container">
        <div class="about-cta-content" data-aos="fade-up">
          <h2>هل تبحث عن حلول أمنية متكاملة؟</h2>
          <p>فريقنا من الخبراء مستعد لمساعدتك في اختيار أفضل معدات الأمن والسلامة لمنشآتك</p>
          <a href="contact.php" class="about-btn-cta">
            <i class="bi bi-telephone"></i>تواصل معنا
          </a>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize AOS
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true,
          offset: 100
        });
      }

      // Initialize PureCounter
      if (typeof PureCounter !== 'undefined') {
        new PureCounter();
      }

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

      // Scroll to top functionality
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
  </script>

</body>

</html>