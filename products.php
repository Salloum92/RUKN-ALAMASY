<?php
// يجب أن تكون الجلسة في أعلى الصفحة قبل أي output
session_start();
include 'config.php';

// Create a new Database instance
$query = new Database();

// Fetch categories, products, and product images from the database
$categories = $query->select('category');
$products = $query->select('products');
$product_images = $query->select('product_images');

// Get category names for filtering
$category_names = [];
foreach ($categories as $category) {
  $category_names[$category['id']] = $category['category_name'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Our Products | Premium Collection</title>
  <meta name="description" content="Discover our premium collection of high-quality products">
  <meta name="keywords" content="products, premium, collection, shopping, ecommerce">
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
    .product-item {
    display: none;
    animation: fadeIn 0.5s ease-in-out;
}

.product-item.show {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* تحسينات التصميم */
.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-bottom: 30px;
}

.filter-btn {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    
}

.filter-btn.active {
    background: #e76a04;
    color: white;
    border-color: #e76a04;
}

.filter-btn:hover {
    background: #e76a04;
    color: white;
    border-color: #e76a04;
}
.filter-btn:hover .products-count{
  color : white ; 
}
.products-count {
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    color : #e76a04 ;
}

.filter-btn.active .products-count {
    background: rgba(255,255,255,0.3);
}

/* تحسينات الكروت */
.product-card-pro {
    border: 1px solid #e9ecef;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.product-card-pro:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card-pro:hover .product-image img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e76a04;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.product-content-pro {
    padding: 20px;
}

.product-category {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.product-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #2c3e50;
}

.product-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 15px;
    line-height: 1.5;
}

.btn-view {
    background: #e76a04;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    width: 100%;
    justify-content: center;
}

.btn-view:hover {
    background: #e76a04;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-buttons {
        gap: 5px;
    }
    
    .filter-btn {
        padding: 8px 15px;
        font-size: 0.9rem;
    }
    
    .product-image {
        height: 200px;
    }
}
  </style>
  
</head>

<body class="products-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <section class="products-hero" data-aos="fade-down">
      <div class="container">
        <h1 class="display-4 fw-bold mb-3">Our Premium Collection</h1>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <p class="mb-0">Explore our carefully curated selection of products designed to exceed your expectations</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Category Filters -->
    <section class="category-filters">
      <div class="container">
        <div class="text-center">
          <h3 class="h4 mb-4" data-aos="fade-up">Browse by Category</h3>
          <div class="filter-buttons" data-aos="fade-up" data-aos-delay="100">
            <button class="filter-btn active" data-filter="all">
              All Products 
              <span class="products-count"><?= count($products) ?></span>
            </button>
            <?php foreach ($categories as $category): 
              $category_count = 0;
              foreach ($products as $product) {
                if ($product['category_id'] == $category['id']) {
                  $category_count++;
                }
              }
            ?>
              <button class="filter-btn" data-filter="category-<?= $category['id'] ?>">
                <?= htmlspecialchars($category['category_name']) ?>
                <?php if ($category_count > 0): ?>
                  <span class="products-count"><?= $category_count ?></span>
                <?php endif; ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

   <!-- Products Grid -->
<section class="section products-grid">
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
            $category_names[$product['category_id']] : 'Uncategorized';
          ?>
          
          <div class="col-xl-3 col-lg-4 col-md-6 mb-4 product-item all category-<?= $product['category_id'] ?>" 
               data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
            <div class="product-card-pro">
              <div class="product-image">
                <img src="assets/img/product/<?= htmlspecialchars($image_url) ?>" 
                     alt="<?= htmlspecialchars($product['product_name']) ?>"
                     onerror="this.src='assets/img/default-product.jpg'">
                <div class="product-badge">New</div>
              </div>
              <div class="product-content-pro">
                <div class="product-category"><?= htmlspecialchars($category_name) ?></div>
                <h3 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h3>
                <p class="product-description">
                  <?= htmlspecialchars(mb_substr($product['description'], 0, 80)) . (strlen($product['description']) > 80 ? '...' : '') ?>
                </p>
                <p class="text-center mb-3">
                  <strong style="color: #e74c3c; font-size: 1.2rem;">
                    ر.س <?= number_format($product['price'], 0, '', ' ') ?>
                  </strong>
                </p>
                <div class="product-actions">
                  <a href="product-details.php?id=<?= $product['id'] ?>" class="btn-view">
                    <i class="bi bi-eye me-2"></i> عرض التفاصيل
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
            <h3 class="text-muted">لا توجد منتجات متاحة</h3>
            <p class="text-muted">سنضيف منتجات جديدة قريباً</p>
          </div>
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
            duration: 800,
            once: true,
            offset: 50
        });
    }

    // Fast Filter Functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productItems = document.querySelectorAll('.product-item');
    const productsContainer = document.getElementById('products-container');
    
    // Show all products initially with fast animation
    setTimeout(() => {
        productItems.forEach(item => {
            item.style.display = 'block';
            item.classList.add('show');
        });
    }, 100);

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.transform = 'scale(1)';
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            this.style.transform = 'scale(1.05)';
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Fast filter with minimal delay
            let visibleCount = 0;
            
            productItems.forEach(item => {
                item.style.opacity = '0.5';
                item.style.transition = 'opacity 0.2s ease';
                
                setTimeout(() => {
                    if (filterValue === 'all') {
                        item.style.display = 'block';
                        item.classList.add('show');
                        visibleCount++;
                    } else {
                        if (item.classList.contains(filterValue)) {
                            item.style.display = 'block';
                            item.classList.add('show');
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('show');
                        }
                    }
                    
                    item.style.opacity = '1';
                }, 50);
            });

            // Add smooth scroll to products
            setTimeout(() => {
                productsContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }, 100);
        });
    });

    // Add hover effects
    productItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Keyboard navigation for filters
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            const activeBtn = document.querySelector('.filter-btn.active');
            const buttons = Array.from(filterButtons);
            const currentIndex = buttons.indexOf(activeBtn);
            
            let nextIndex;
            if (e.key === 'ArrowRight') {
                nextIndex = (currentIndex + 1) % buttons.length;
            } else {
                nextIndex = (currentIndex - 1 + buttons.length) % buttons.length;
            }
            
            buttons[nextIndex].click();
        }
    });
});
</script>

</body>

</html>