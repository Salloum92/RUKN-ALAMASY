<?php
// search.php
session_start();

// تضمين الملفات الأساسية
require_once 'config.php';
require_once 'lang.php';

// التحقق من وجود Database class وإنشاء الاتصال
if (class_exists('Database')) {
    $query = new Database();
} else {
    die("خطأ: لا يمكن تحميل قاعدة البيانات");
}

// قاعدة URL للصور
$base_image_url = 'assets/img/product/';

// معالجة البحث
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_results = [];
$total_results = 0;

if (!empty($search_query)) {
    try {
        // البحث في جدول products فقط مع منع التكرار
        $sql = "SELECT DISTINCT p.* 
                FROM products p 
                WHERE p.product_name LIKE ? OR p.description LIKE ? 
                ORDER BY 
                    CASE 
                        WHEN p.product_name LIKE ? THEN 1 
                        WHEN p.description LIKE ? THEN 2 
                        ELSE 3 
                    END,
                    p.product_name ASC";
        
        $params = [
            "%$search_query%", 
            "%$search_query%",
            "%$search_query%",
            "%$search_query%"
        ];
        
        $search_results = $query->eQuery($sql, $params);
        
        if (is_array($search_results)) {
            $total_results = count($search_results);
        } else {
            $search_results = [];
        }
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        $search_results = [];
        $total_results = 0;
    }
}

// دالة مساعدة لجلب صورة المنتج من product_images
function getProductImage($product_id, $query, $base_url) {
    try {
        $sql = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1";
        $result = $query->eQuery($sql, [$product_id]);
        
        if (is_array($result) && count($result) > 0 && !empty($result[0]['image_url'])) {
            $image_name = $result[0]['image_url'];
            
            // إذا كان الرابط يحتوي على مسار كامل، استخدمه كما هو
            if (strpos($image_name, 'http') === 0 || strpos($image_name, '/') === 0) {
                return $image_name;
            }
            
            // إذا كان اسم ملف فقط، أضف المسار الأساسي
            return $base_url . $image_name;
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}

// إضافة الصور للنتائج
if (is_array($search_results) && count($search_results) > 0) {
    foreach ($search_results as &$product) {
        $product['image_url'] = getProductImage($product['id'], $query, $base_image_url);
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($t['search_results']) ? $t['search_results'] : 'نتائج البحث'; ?> - Rukn Alamasy</title>
    
    <!-- CSS الأساسي -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    
    <style>
        /* ===== VARIABLES ===== */
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

        /* ===== GLOBAL RESET ===== */
        .search-page {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
        }

        /* ===== SEARCH SECTION ===== */
        .search-section-page {
            padding: 120px 0 60px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #34495e 100%);
            min-height: 40vh;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .search-section-page {
                padding: 100px 0 40px;
                min-height: 35vh;
            }
        }

        .search-hero {
            text-align: center;
            color: var(--white);
        }

        .search-hero h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--white) 0%, var(--primary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .search-box-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .search-form {
            position: relative;
        }

        .search-input-group {
            background: var(--white);
            border-radius: 50px;
            padding: 5px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 2px solid transparent;
            transition: var(--transition);
        }

        .search-input-group:focus-within {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .search-input {
            border: none;
            background: none;
            padding: 15px 25px;
            font-size: 1.1rem;
            width: 100%;
            outline: none;
            border-radius: 50px;
        }

        .search-input::placeholder {
            color: var(--text-light);
        }

        .search-btn-page {
            background: var(--primary-color);
            border: none;
            color: var(--white);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            white-space: nowrap;
        }

        .search-btn-page:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        /* ===== RESULTS SECTION ===== */
        .results-section {
            padding: 60px 0;
            background: var(--bg-light);
        }

        .results-header {
            background: var(--white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            border-left: 4px solid var(--primary-color);
        }

        [dir="rtl"] .results-header {
            border-left: none;
            border-right: 4px solid var(--primary-color);
        }

        .results-count {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .search-query {
            color: var(--primary-color);
            font-weight: 700;
        }

        /* ===== PRODUCT CARDS ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .product-image-container {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .no-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            flex-direction: column;
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        [dir="rtl"] .product-badge {
            left: auto;
            right: 15px;
        }

        .product-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-description {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .product-price {
            color: var(--primary-color);
            font-weight: 800;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .btn-view-details {
            background: var(--primary-color);
            border: none;
            color: var(--white);
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            text-decoration: none;
            text-align: center;
            display: block;
        }

        .btn-view-details:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 106, 4, 0.3);
            color: var(--white);
        }

        /* ===== NO RESULTS ===== */
        .no-results {
            text-align: center;
            padding: 80px 20px;
        }

        .search-icon-large {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .suggestions {
            background: var(--white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-top: 30px;
        }

        .suggestion-item {
            display: inline-block;
            background: var(--bg-light);
            padding: 8px 16px;
            margin: 5px;
            border-radius: 20px;
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .suggestion-item:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* ===== LOADING STATES ===== */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* ===== RTL SUPPORT ===== */
        [dir="rtl"] .search-input-group {
            text-align: right;
        }

        [dir="rtl"] .ms-2 {
            margin-left: 0 !important;
            margin-right: 0.5rem !important;
        }

        [dir="rtl"] .me-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        [dir="rtl"] .text-start {
            text-align: right !important;
        }

        [dir="rtl"] .text-end {
            text-align: left !important;
        }

        /* ===== MOBILE OPTIMIZATIONS ===== */
        @media (max-width: 576px) {
            .search-section-page {
                padding: 80px 0 30px;
            }

            .search-input-group {
                flex-direction: column;
                border-radius: var(--border-radius);
                padding: 0;
            }

            .search-input {
                border-radius: var(--border-radius) var(--border-radius) 0 0;
                padding: 15px 20px;
            }

            .search-btn-page {
                border-radius: 0 0 var(--border-radius) var(--border-radius);
                width: 100%;
            }

            .results-header {
                padding: 20px;
                margin-bottom: 30px;
            }

            .product-content {
                padding: 20px;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body class="search-page">
    <?php include 'includes/header.php'; ?>

    <main class="main">
        <!-- Search Hero Section -->
        <section class="search-section-page">
            <div class="container">
                <div class="search-hero">
                    <h1><?php echo isset($t['search']) ? $t['search'] : 'بحث'; ?></h1>
                    <p class="lead text-light opacity-90">
                        <?php echo ($lang == 'ar') ? 'ابحث في منتجاتنا باستخدام اسم المنتج أو الوصف' : 'Search our products using product name or description'; ?>
                    </p>
                    
                    <div class="search-box-container mt-4">
                        <form action="search.php" method="GET" class="search-form">
                            <div class="search-input-group d-flex">
                                <input type="text" 
                                       class="search-input" 
                                       name="q" 
                                       value="<?php echo htmlspecialchars($search_query); ?>" 
                                       placeholder="<?php echo ($lang == 'ar') ? 'ابحث عن منتج...' : 'Search for a product...'; ?>" 
                                       aria-label="Search"
                                       required>
                                <button class="search-btn-page" type="submit">
                                    <i class="bi bi-search"></i>
                                    <span class="ms-2"><?php echo isset($t['search']) ? $t['search'] : 'بحث'; ?></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Results Section -->
        <section class="results-section">
            <div class="container">
                <?php if (!empty($search_query)): ?>
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="h3 mb-2 fw-bold">
                                    <?php echo isset($t['search_results']) ? $t['search_results'] : 'نتائج البحث'; ?>
                                </h2>
                                <p class="results-count mb-0">
                                    "<span class="search-query"><?php echo htmlspecialchars($search_query); ?></span>"
                                    - 
                                    <?php 
                                    if ($lang == 'ar') {
                                        if ($total_results == 0) {
                                            echo "لم يتم العثور على منتجات";
                                        } elseif ($total_results == 1) {
                                            echo "تم العثور على منتج واحد";
                                        } else {
                                            echo "تم العثور على {$total_results} منتج";
                                        }
                                    } else {
                                        if ($total_results == 0) {
                                            echo "No products found";
                                        } elseif ($total_results == 1) {
                                            echo "Found 1 product";
                                        } else {
                                            echo "Found {$total_results} products";
                                        }
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="products.php" class="btn btn-outline-primary">
                                    <i class="bi bi-grid me-2"></i>
                                    <?php echo isset($t['all_products']) ? $t['all_products'] : 'جميع المنتجات'; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <?php if ($total_results > 0): ?>
                        <div class="products-grid">
                            <?php foreach ($search_results as $product): ?>
                                <div class="product-card">
                                    <!-- Product Image -->
                                    <div class="product-image-container">
                                        <?php if (isset($product['image_url']) && !empty($product['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                 class="product-image"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="no-image" style="display: none;">
                                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                <small><?php echo ($lang == 'ar') ? 'لا توجد صورة' : 'No image'; ?></small>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-image">
                                                <i class="bi bi-box" style="font-size: 2rem;"></i>
                                                <small><?php echo ($lang == 'ar') ? 'لا توجد صورة' : 'No image'; ?></small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="product-badge">
                                            <?php echo ($lang == 'ar') ? 'منتج' : 'Product'; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Product Content -->
                                    <div class="product-content">
                                        <h3 class="product-title">
                                            <?php echo htmlspecialchars($product['product_name'] ?? 'اسم المنتج'); ?>
                                        </h3>
                                        
                                        <p class="product-description">
                                            <?php
                                            $description = $product['description'] ?? '';
                                            if (!empty($description)) {
                                                $clean_description = strip_tags($description);
                                                echo substr(htmlspecialchars($clean_description), 0, 120);
                                                if (strlen($clean_description) > 120) echo '...';
                                            } else {
                                                echo ($lang == 'ar') ? 'لا يوجد وصف متاح' : 'No description available';
                                            }
                                            ?>
                                        </p>
                                        
                                        <div class="product-price">
                                            <?php echo ($lang == 'ar') ? 'ر.س' : 'SAR'; ?> 
                                            <?php echo isset($product['price']) ? number_format($product['price'], 2) : '0.00'; ?>
                                        </div>
                                        
                                        <a href="product-details.php?id=<?php echo $product['id']; ?>" 
                                           class="btn-view-details">
                                            <i class="bi bi-eye me-2"></i>
                                            <?php echo isset($t['view_details']) ? $t['view_details'] : 'عرض التفاصيل'; ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- No Results -->
                        <div class="no-results">
                            <i class="bi bi-search search-icon-large"></i>
                            <h3 class="h4 text-muted mb-3">
                                <?php echo isset($t['no_results']) ? $t['no_results'] : 'لم يتم العثور على نتائج'; ?>
                            </h3>
                            <p class="text-muted mb-4">
                                <?php echo ($lang == 'ar') 
                                    ? 'لم نعثر على أي منتج مطابق لبحثك. جرب استخدام كلمات بحث مختلفة.' 
                                    : 'No products found matching your search. Try using different keywords.'; ?>
                            </p>
                            
                            <div class="suggestions">
                                <h5 class="mb-3"><?php echo ($lang == 'ar') ? 'اقتراحات للبحث:' : 'Search suggestions:'; ?></h5>
                                <div>
                                    <a href="search.php?q=سلامة" class="suggestion-item">سلامة</a>
                                    <a href="search.php?q=أمن" class="suggestion-item">أمن</a>
                                    <a href="search.php?q=معدات" class="suggestion-item">معدات</a>
                                    <a href="search.php?q=حماية" class="suggestion-item">حماية</a>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <a href="products.php" class="btn btn-primary btn-lg me-3">
                                    <i class="bi bi-grid me-2"></i>
                                    <?php echo isset($t['browse_products']) ? $t['browse_products'] : 'تصفح المنتجات'; ?>
                                </a>
                                <a href="index.php" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-house me-2"></i>
                                    <?php echo isset($t['home']) ? $t['home'] : 'العودة للرئيسية'; ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Empty Search State -->
                    <div class="text-center py-5">
                        <i class="bi bi-search search-icon-large"></i>
                        <h2 class="h3 mt-4 mb-3"><?php echo isset($t['start_searching']) ? $t['start_searching'] : 'ابدأ البحث'; ?></h2>
                        <p class="text-muted fs-5 mb-4">
                            <?php echo ($lang == 'ar') 
                                ? 'استخدم شريط البحث أعلاه للعثور على المنتجات التي تبحث عنها' 
                                : 'Use the search bar above to find the products you\'re looking for'; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('.search-form');
            const searchInput = searchForm.querySelector('input[name="q"]');

            // التركيز على حقل البحث
            if (searchInput.value === '') {
                searchInput.focus();
            }

            // تأثيرات عند التمرير على البطاقات
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // تحسين تجربة البحث
            searchForm.addEventListener('submit', function(e) {
                const query = searchInput.value.trim();
                if (query.length < 2) {
                    e.preventDefault();
                    searchInput.focus();
                    return false;
                }
            });
        });
    </script>
</body>
</html>