<?php
// يجب أن تكون الجلسة في أعلى الصفحة قبل أي output
session_start();
include 'config.php';
$query = new Database();
$services = $query->select('services');
$bioservices = $query->select('services');
$ourservices = $query->select('ourservices');
$aboutData = $query->select('about');
$serviceItems = $query->select('about_ul_items');
$statistics = $query->select('statistics');

// Prepare the about items array - الطريقة الصحيحة
$aboutItems = [];
if (!empty($aboutData)) {
    // نأخذ أول سجل من about (افترضنا أن هناك سجل واحد فقط)
    $about = $aboutData[0];
    $aboutItems = [
        'title' => isset($about['title']) ? $about['title'] : '',
        'p1' => isset($about['p1']) ? $about['p1'] : '',
        'p2' => isset($about['p2']) ? $about['p2'] : '',
        'image' => isset($about['image']) ? $about['image'] : '',
        'list_items' => []
    ];
    
    // إضافة جميع عناصر القائمة (بدون about_id)
    foreach ($serviceItems as $item) {
        if (isset($item['list_item']) && !empty($item['list_item'])) {
            $aboutItems['list_items'][] = $item['list_item'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Our Services - Rukn Alamasy</title>
  <meta name="description" content="Discover our premium services and solutions for your business">
  <meta name="keywords" content="services, solutions, business, professional services">
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
    
  </style>
</head>

<body class="services-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <section class="services-hero" data-aos="fade-down">
      <div class="container">
        <h1 class="display-4 fw-bold mb-3">Our Premium Services</h1>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <p class="mb-0">Explore our comprehensive range of services designed to drive your success</p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <div class="container">
        <div class="section-title" data-aos="fade-up">
          <h2>Why Choose Us</h2>
          <p>We deliver excellence through innovation and dedication</p>
        </div>

        <div class="row gy-5 align-items-center">
          <?php if (!empty($aboutItems)): ?>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
              <div class="about-image">
                <?php if (!empty($aboutItems['image'])): ?>
                  <img src="<?= htmlspecialchars($aboutItems['image']) ?>" class="img-fluid" alt="About Us">
                <?php else: ?>
                  <div style="background: #e76a04; height: 400px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                    Service Image
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
              <div class="about-content">
                <?php if (!empty($aboutItems['title'])): ?>
                  <h3><?= htmlspecialchars($aboutItems['title']) ?></h3>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['p1'])): ?>
                  <p class="fst-italic"><?= htmlspecialchars($aboutItems['p1']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['list_items'])): ?>
                  <h4>Our Key Features</h4>
                  <ul class="about-list">
                    <?php foreach ($aboutItems['list_items'] as $item): ?>
                      <?php if (!empty($item)): ?>
                        <li>
                          <i class="bi bi-check2-all"></i> 
                          <?= htmlspecialchars($item) ?>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                <?php if (!empty($aboutItems['p2'])): ?>
                  <p><?= htmlspecialchars($aboutItems['p2']) ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="col-12 text-center">
              <div class="about-card">
                <h3>No Content Available</h3>
                <p>Please add content from the admin panel to display here.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
      <div class="container">
        <div class="section-title" data-aos="fade-up">
          <h2>Our Achievements</h2>
          <p>Numbers that speak about our success and dedication</p>
        </div>

        <div class="row">
          <?php if (!empty($statistics)): ?>
            <?php foreach ($statistics as $stat) : ?>
              <div class="col-xl-3 col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                  <div class="stats-icon">
                    <i class="<?= isset($stat['icon']) ? $stat['icon'] : 'bi bi-graph-up' ?>"></i>
                  </div>
                  <div class="stats-number">
                    <span data-purecounter-start="0" 
                          data-purecounter-end="<?= isset($stat['count']) ? $stat['count'] : '0' ?>" 
                          data-purecounter-duration="2" 
                          class="purecounter">
                      <?= isset($stat['count']) ? $stat['count'] : '0' ?>
                    </span>
                  </div>
                  <div class="stats-title">
                    <?= isset($stat['title']) ? htmlspecialchars($stat['title']) : 'Achievement' ?>
                  </div>
                  <div class="stats-description">
                    <?= isset($stat['description']) ? htmlspecialchars($stat['description']) : 'Our success story' ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p class="text-muted">No statistics available.</p>
            </div>
          <?php endif; ?>
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
          once: true
        });
      }

      // Initialize PureCounter
      if (typeof PureCounter !== 'undefined') {
        new PureCounter();
      }
    });
  </script>

</body>

</html>