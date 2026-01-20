<?php
// تضمين نظام اللغة في الفوتر
$translations = [
    'ar' => [
        'location' => 'الموقع',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',
        'useful_links' => 'روابط سريعة',
        'home' => 'الرئيسية',
        'about' => 'من نحن',
        'products' => 'المنتجات',
        'services' => 'الخدمات',
        'contact' => 'اتصل بنا',
        'follow_us' => 'تابعنا',
        'follow_desc' => 'تابع أحدث منتجاتنا وعروضنا الخاصة على وسائل التواصل الاجتماعي',
        'copyright' => 'حقوق النشر',
        'all_rights_reserved' => 'جميع الحقوق محفوظة',
        'not_specified' => 'غير محدد',
        'working_hours' => 'ساعات العمل',
        'get_directions' => 'احصل على الاتجاهات',
        'your_email' => 'بريدك الإلكتروني',
        'subscribe' => 'اشتراك',
        'company_description' => 'نقدم أفضل الحلول والمنتجات ذات الجودة العالية لجميع احتياجاتك'
    ],
    'en' => [
        'location' => 'Location',
        'phone' => 'Phone',
        'email' => 'Email',
        'useful_links' => 'Useful Links',
        'home' => 'Home',
        'about' => 'About Us',
        'products' => 'Products',
        'services' => 'Services',
        'contact' => 'Contact',
        'follow_us' => 'Follow Us',
        'follow_desc' => 'Follow our latest products and special offers on social media',
        'copyright' => 'Copyright',
        'all_rights_reserved' => 'All Rights Reserved',
        'not_specified' => 'Not specified',
        'working_hours' => 'Working Hours',
        'get_directions' => 'Get Directions',
        'your_email' => 'Your Email',
        'subscribe' => 'Subscribe',
        'company_description' => 'We provide the best solutions and high-quality products for all your needs'
    ]
];

// استخدام اللغة من الجلسة
$lang = $_SESSION['lang'] ?? 'ar';
$t = $translations[$lang];

// استعلامات قاعدة البيانات
$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

// البحث عن بيانات الاتصال
$footer_contact_data = [
    'location' => ['value' => '', 'icon' => 'bi bi-geo-alt', 'type' => 'location'],
    'phone' => ['value' => '', 'icon' => 'bi bi-telephone', 'type' => 'phone'],
    'email' => ['value' => '', 'icon' => 'bi bi-envelope', 'type' => 'email'],
    'working_hours' => ['value' => '', 'icon' => 'bi bi-clock', 'type' => 'working_hours']
];

foreach ($contact_boxData as $item) {
    if (isset($item['type'])) {
        $type = $item['type'];
        if (isset($footer_contact_data[$type]) && !empty($item['value'])) {
            $footer_contact_data[$type]['value'] = $item['value'];
            if (!empty($item['icon'])) {
                $footer_contact_data[$type]['icon'] = $item['icon'];
            }
        }
    }
    
    if (isset($item['title']) && isset($item['value']) && !empty($item['value'])) {
        $title = strtolower($item['title']);
        
        if (strpos($title, 'موقع') !== false || strpos($title, 'location') !== false) {
            $footer_contact_data['location']['value'] = $item['value'];
        } elseif (strpos($title, 'هاتف') !== false || strpos($title, 'phone') !== false || strpos($title, 'tel') !== false) {
            $footer_contact_data['phone']['value'] = $item['value'];
        } elseif (strpos($title, 'بريد') !== false || strpos($title, 'email') !== false || strpos($title, 'mail') !== false) {
            $footer_contact_data['email']['value'] = $item['value'];
        } elseif (strpos($title, 'ساعات') !== false || strpos($title, 'working') !== false || strpos($title, 'hours') !== false) {
            $footer_contact_data['working_hours']['value'] = $item['value'];
        }
    }
}

if (empty($footer_contact_data['phone']['value']) && isset($contactData[0]['phone'])) {
    $footer_contact_data['phone']['value'] = $contactData[0]['phone'];
}

if (empty($footer_contact_data['email']['value']) && isset($contactData[0]['email'])) {
    $footer_contact_data['email']['value'] = $contactData[0]['email'];
}

// 🔴 استرجاع بيانات الموقع والرابط
$google_maps_url = '';
$location_address = '';

$google_maps_item = $query->select('contact_box', '*', "WHERE id = 1")[0] ?? null;

if ($google_maps_item && !empty($google_maps_item['value'])) {
    $google_maps_url = $google_maps_item['value'];
    $location_address = $google_maps_item['label'] ?? $t['get_directions'];
} else {
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'google_maps' && !empty($item['value'])) {
            $google_maps_url = $item['value'];
            $location_address = $item['label'] ?? $t['get_directions'];
            break;
        }
    }
}

if (empty($google_maps_url)) {
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'location' && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
        if (isset($item['title']) && stripos($item['title'], 'موقع') !== false && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
    }
    
    if (empty($location_address) && isset($contactData[0]['location'])) {
        $location_address = $contactData[0]['location'];
    }
    
    if (!empty($location_address)) {
        $encoded_address = urlencode($location_address);
        $google_maps_url = "https://www.google.com/maps/search/?api=1&query=" . $encoded_address;
    }
}

if (!empty($google_maps_url) && empty($location_address)) {
    $location_address = $t['get_directions'];
}
?>

<footer class="alx-footer">
    <!-- Wave Decoration -->
    <div class="alx-footer-wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
        </svg>
    </div>

    <div class="alx-footer-main">
        <div class="alx-container">
            <div class="alx-footer-grid">
                <!-- Brand Section -->
                <div class="alx-footer-brand">
                    <a href="index.php" class="alx-footer-logo">
                        <div class="alx-logo-icon">
                            <img src="assets/img/logo.png" alt="Rukn Alamasy" class="alx-logo-img"
                                >
                        </div>
                        <div class="alx-logo-text">
                            <span class="alx-brand-name">Rukn Alamasy</span>
                            <p class="alx-brand-desc"><?= $t['company_description'] ?></p>
                        </div>
                    </a>
                    
                    <div class="alx-contact-info">
                        <!-- Location -->
                        <div class="alx-contact-card">
                            <div class="alx-contact-icon">
                                <i class="<?= $footer_contact_data['location']['icon'] ?>"></i>
                            </div>
                            <div class="alx-contact-content">
                                <h4><?= $t['location'] ?></h4>
                                <?php if (!empty($google_maps_url) && !empty($location_address)): ?>
                                    <a href="<?= htmlspecialchars($google_maps_url) ?>" target="_blank" class="alx-contact-link alx-location-link">
                                        <?= htmlspecialchars($location_address) ?>
                                        <i class="bi bi-arrow-up-right alx-link-arrow"></i>
                                    </a>
                                <?php elseif (!empty($footer_contact_data['location']['value'])): ?>
                                    <p class="alx-contact-text"><?= htmlspecialchars($footer_contact_data['location']['value']) ?></p>
                                <?php else: ?>
                                    <p class="alx-contact-text"><?= $t['not_specified'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div class="alx-contact-card">
                            <div class="alx-contact-icon">
                                <i class="<?= $footer_contact_data['phone']['icon'] ?>"></i>
                            </div>
                            <div class="alx-contact-content">
                                <h4><?= $t['phone'] ?></h4>
                                <?php if (!empty($footer_contact_data['phone']['value'])): ?>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $footer_contact_data['phone']['value']) ?>" class="alx-contact-link">
                                        <?= htmlspecialchars($footer_contact_data['phone']['value']) ?>
                                    </a>
                                <?php else: ?>
                                    <p class="alx-contact-text"><?= $t['not_specified'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="alx-contact-card">
                            <div class="alx-contact-icon">
                                <i class="<?= $footer_contact_data['email']['icon'] ?>"></i>
                            </div>
                            <div class="alx-contact-content">
                                <h4><?= $t['email'] ?></h4>
                                <?php if (!empty($footer_contact_data['email']['value'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($footer_contact_data['email']['value']) ?>" class="alx-contact-link">
                                        <?= htmlspecialchars($footer_contact_data['email']['value']) ?>
                                    </a>
                                <?php else: ?>
                                    <p class="alx-contact-text"><?= $t['not_specified'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Working Hours -->
                        <?php if (!empty($footer_contact_data['working_hours']['value'])): ?>
                        <div class="alx-contact-card">
                            <div class="alx-contact-icon">
                                <i class="<?= $footer_contact_data['working_hours']['icon'] ?>"></i>
                            </div>
                            <div class="alx-contact-content">
                                <h4><?= $t['working_hours'] ?></h4>
                                <p class="alx-contact-text"><?= htmlspecialchars($footer_contact_data['working_hours']['value']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="alx-footer-links">
                    <h3 class="alx-section-title"><?= $t['useful_links'] ?></h3>
                    <div class="alx-links-grid">
                        <div class="alx-link-group">
                            <a href="./" class="alx-nav-link">
                                <span class="alx-link-icon"><i class="bi bi-house"></i></span>
                                <span class="alx-link-text"><?= $t['home'] ?></span>
                                <span class="alx-link-arrow"><i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i></span>
                            </a>
                            <a href="about.php" class="alx-nav-link">
                                <span class="alx-link-icon"><i class="bi bi-info-circle"></i></span>
                                <span class="alx-link-text"><?= $t['about'] ?></span>
                                <span class="alx-link-arrow"><i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i></span>
                            </a>
                            <a href="products.php" class="alx-nav-link">
                                <span class="alx-link-icon"><i class="bi bi-box-seam"></i></span>
                                <span class="alx-link-text"><?= $t['products'] ?></span>
                                <span class="alx-link-arrow"><i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i></span>
                            </a>
                             <a href="services.php" class="alx-nav-link">
                                <span class="alx-link-icon"><i class="bi bi-gear"></i></span>
                                <span class="alx-link-text"><?= $t['services'] ?></span>
                                <span class="alx-link-arrow"><i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i></span>
                            </a>
                            <a href="contact.php" class="alx-nav-link">
                                <span class="alx-link-icon"><i class="bi bi-envelope"></i></span>
                                <span class="alx-link-text"><?= $t['contact'] ?></span>
                                <span class="alx-link-arrow"><i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i></span>
                            </a>
                            
                        </div>
                       
                    </div>
                </div>

                <!-- Social Media -->
                <div class="alx-footer-social">
                    <h3 class="alx-section-title"><?= $t['follow_us'] ?></h3>
                    <p class="alx-social-desc"><?= $t['follow_desc'] ?></p>
                    
                    <div class="alx-social-grid">
                        <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
                            <a href="<?= $contactData[0]['twitter'] ?>" class="alx-social-btn alx-twitter" target="_blank" title="Twitter">
                                <i class="bi bi-twitter-x"></i>
                                <span>Twitter</span>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
                            <a href="<?= $contactData[0]['facebook'] ?>" class="alx-social-btn alx-facebook" target="_blank" title="Facebook">
                                <i class="bi bi-facebook"></i>
                                <span>Facebook</span>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
                            <a href="<?= $contactData[0]['instagram'] ?>" class="alx-social-btn alx-instagram" target="_blank" title="Instagram">
                                <i class="bi bi-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($contactData[0]['linkedin']) && !empty($contactData[0]['linkedin'])): ?>
                            <a href="<?= $contactData[0]['linkedin'] ?>" class="alx-social-btn alx-linkedin" target="_blank" title="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                                <span>LinkedIn</span>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
                            <a href="<?= $contactData[0]['youtube'] ?>" class="alx-social-btn alx-youtube" target="_blank" title="YouTube">
                                <i class="bi bi-youtube"></i>
                                <span>YouTube</span>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
                            <a href="<?= $contactData[0]['whatsapp'] ?>" class="alx-social-btn alx-whatsapp" target="_blank" title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Newsletter -->
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="alx-footer-bottom">
        <div class="alx-container">
            <div class="alx-bottom-content">
                <div class="alx-copyright">
                    <p>&copy; <?= date('Y') ?> <span class="alx-copyright-brand">Rukn Alamasy</span>. <?= $t['all_rights_reserved'] ?></p>
                </div>
                <div class="alx-developer">
                    <p>
                        <?= $lang == 'ar' ? 'تصميم وتطوير' : 'Designed & Developed by' ?>
                        <a href="https://salloum92.github.io/Ahmad-Portfolio/" target="_blank" class="alx-dev-link">
                            <i class="bi bi-code-slash"></i> Ahmad Salloum
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* === CSS Variables === */
:root {
    /* Primary Colors */
    --alx-primary: #e76a04;
    --alx-primary-light: #ff8b2d;
    --alx-primary-dark: #cc5f03;
    
    /* Secondary Colors */
    --alx-secondary: #144734;
    --alx-secondary-light: #1a5943;
    --alx-secondary-dark: #0d3528;
    
    /* Neutral Colors */
    --alx-light: #f8f9fa;
    --alx-light-2: #e9ecef;
    --alx-dark: #212529;
    --alx-dark-2: #343a40;
    
    /* Text Colors */
    --alx-text-primary: #ffffff;
    --alx-text-secondary: #bdc3c7;
    --alx-text-muted: #95a5a6;
    
    /* Social Colors */
    --alx-twitter: #1DA1F2;
    --alx-facebook: #1877F2;
    --alx-instagram: #E4405F;
    --alx-linkedin: #0A66C2;
    --alx-youtube: #FF0000;
    --alx-whatsapp: #25D366;
    
    /* Gradients */
    --alx-gradient-primary: linear-gradient(135deg, var(--alx-primary) 0%, var(--alx-primary-light) 100%);
    --alx-gradient-secondary: linear-gradient(135deg, var(--alx-secondary) 0%, var(--alx-secondary-light) 100%);
    --alx-gradient-dark: linear-gradient(135deg, var(--alx-secondary-dark) 0%, var(--alx-secondary) 100%);
    
    /* Shadows */
    --alx-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
    --alx-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.15);
    --alx-shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.2);
    
    /* Transitions */
    --alx-transition-fast: 0.2s ease;
    --alx-transition-normal: 0.3s ease;
    --alx-transition-slow: 0.5s ease;
    
    /* Border Radius */
    --alx-radius-sm: 6px;
    --alx-radius-md: 12px;
    --alx-radius-lg: 20px;
    --alx-radius-xl: 30px;
    --alx-radius-full: 50px;
    
    /* Spacing */
    --alx-spacing-xs: 0.5rem;
    --alx-spacing-sm: 1rem;
    --alx-spacing-md: 1.5rem;
    --alx-spacing-lg: 2rem;
    --alx-spacing-xl: 3rem;
}

/* === Base Styles === */
.alx-footer {
    position: relative;
    background: var(--alx-gradient-dark);
    color: var(--alx-text-primary);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow: hidden;
}

.alx-footer-wave {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 120px;
    transform: translateY(-100%);
    z-index: 1;
}

.alx-footer-wave svg {
    width: 100%;
    height: 100%;
    fill: var(--alx-secondary);
}

.alx-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--alx-spacing-md);
}

/* === Main Footer === */
.alx-footer-main {
    margin: 30px 0 0 10px;
    position: relative;
    z-index: 2;
}

.alx-footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--alx-spacing-xl);
    margin-bottom: var(--alx-spacing-xl);
}

/* === Brand Section === */
.alx-footer-brand {
    display: flex;
    flex-direction: column;
    gap: var(--alx-spacing-lg);
}

.alx-footer-logo {
    display: flex;
    align-items: center;
    gap: var(--alx-spacing-md);
    text-decoration: none;
    transition: var(--alx-transition-normal);
}

.alx-footer-logo:hover {
    transform: translateY(-3px);
}

.alx-logo-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--alx-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: var(--alx-shadow-md);
}

.alx-logo-img {
    width: 70%;
    height: 70%;
    object-fit: contain;
}

.alx-logo-text {
    flex: 1;
}

.alx-brand-name {
    font-size: 1.8rem;
    font-weight: 800;
    background: #e76a04;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
    display: block;
    margin-bottom: var(--alx-spacing-xs);
}

.alx-brand-desc {
    color: var(--alx-text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
}

/* === Contact Info === */
.alx-contact-info {
    display: flex;
    flex-direction: column;
    gap: var(--alx-spacing-md);
}

.alx-contact-card {
    display: flex;
    align-items: flex-start;
    gap: var(--alx-spacing-sm);
    padding: var(--alx-spacing-sm);
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--alx-radius-md);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--alx-transition-normal);
    position: relative;
    overflow: hidden;
}

.alx-contact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
    transform: translateX(-100%);
    transition: var(--alx-transition-slow);
}

.alx-contact-card:hover::before {
    transform: translateX(100%);
}

.alx-contact-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--alx-primary);
    transform: translateY(-2px);
    box-shadow: var(--alx-shadow-md);
}

.alx-contact-icon {
    width: 40px;
    height: 40px;
    background: var(--alx-gradient-primary);
    border-radius: var(--alx-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: white;
    font-size: 1.2rem;
    transition: var(--alx-transition-normal);
}

.alx-contact-card:hover .alx-contact-icon {
    transform: rotate(15deg) scale(1.1);
}

.alx-contact-content {
    flex: 1;
}

.alx-contact-content h4 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--alx-text-secondary);
    margin: 0 0 4px 0;
}

.alx-contact-link {
    color: var(--alx-text-primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: var(--alx-transition-fast);
    position: relative;
}

.alx-contact-link:hover {
    color: var(--alx-primary-light);
}

.alx-contact-link:hover .alx-link-arrow {
    transform: translate(3px, -3px);
}

.alx-location-link:hover {
    color: var(--alx-whatsapp);
}

.alx-contact-text {
    color: var(--alx-text-primary);
    font-weight: 500;
    font-size: 1rem;
    margin: 0;
}

/* === Links Section === */
.alx-footer-links {
    padding: var(--alx-spacing-md) 0;
}

.alx-section-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: var(--alx-spacing-lg);
    position: relative;
    padding-bottom: var(--alx-spacing-sm);
    color: var(--alx-text-primary);
}

.alx-section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: var(--alx-gradient-primary);
    border-radius: var(--alx-radius-full);
}

.alx-links-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--alx-spacing-md);
}

.alx-link-group {
    display: flex;
    flex-direction: column;
    gap: var(--alx-spacing-sm);
}

.alx-nav-link {
    display: flex;
    align-items: center;
    gap: var(--alx-spacing-sm);
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--alx-radius-md);
    text-decoration: none;
    color: var(--alx-text-primary);
    transition: var(--alx-transition-normal);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
}

.alx-nav-link::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(231, 106, 4, 0.2);
    transform: translate(-50%, -50%);
    transition: var(--alx-transition-normal);
}

.alx-nav-link:hover::before {
    width: 300px;
    height: 300px;
}

.alx-nav-link:hover {
    border-color: var(--alx-primary);
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.alx-link-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--alx-primary);
    font-size: 1rem;
    transition: var(--alx-transition-normal);
}

.alx-nav-link:hover .alx-link-icon {
    color: white;
    transform: scale(1.2);
}

.alx-link-text {
    flex: 1;
    font-weight: 500;
    transition: var(--alx-transition-normal);
}

.alx-link-arrow {
    color: var(--alx-text-secondary);
    font-size: 0.8rem;
    transition: var(--alx-transition-fast);
}

.alx-nav-link:hover .alx-link-arrow {
    color: var(--alx-primary);
    transform: translateX(3px);
}

/* === Social Section === */
.alx-footer-social {
    padding: var(--alx-spacing-md) 0;
}

.alx-social-desc {
    color: var(--alx-text-secondary);
    line-height: 1.6;
    margin-bottom: var(--alx-spacing-lg);
    font-size: 0.95rem;
}

.alx-social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: var(--alx-spacing-sm);
    margin-bottom: var(--alx-spacing-xl);
}

.alx-social-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--alx-radius-md);
    text-decoration: none;
    color: var(--alx-text-primary);
    transition: var(--alx-transition-normal);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.alx-social-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: var(--alx-transition-normal);
}

.alx-social-btn:hover::before {
    opacity: 0.1;
}

.alx-social-btn i {
    font-size: 1.2rem;
    transition: var(--alx-transition-normal);
}

.alx-social-btn span {
    font-weight: 500;
    font-size: 0.9rem;
}

.alx-social-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--alx-shadow-md);
    border-color: transparent;
}

.alx-social-btn:hover i {
    transform: scale(1.2);
}

/* Social Button Colors */
.alx-twitter { background: rgba(29, 161, 242, 0.1); }
.alx-twitter::before { background: var(--alx-twitter); }
.alx-twitter:hover { background: var(--alx-twitter); }

.alx-facebook { background: rgba(24, 119, 242, 0.1); }
.alx-facebook::before { background: var(--alx-facebook); }
.alx-facebook:hover { background: var(--alx-facebook); }

.alx-instagram { background: rgba(228, 64, 95, 0.1); }
.alx-instagram::before { background: var(--alx-instagram); }
.alx-instagram:hover { background: var(--alx-instagram); }

.alx-linkedin { background: rgba(10, 102, 194, 0.1); }
.alx-linkedin::before { background: var(--alx-linkedin); }
.alx-linkedin:hover { background: var(--alx-linkedin); }

.alx-youtube { background: rgba(255, 0, 0, 0.1); }
.alx-youtube::before { background: var(--alx-youtube); }
.alx-youtube:hover { background: var(--alx-youtube); }

.alx-whatsapp { background: rgba(37, 211, 102, 0.1); }
.alx-whatsapp::before { background: var(--alx-whatsapp); }
.alx-whatsapp:hover { background: var(--alx-whatsapp); }

/* Newsletter */
.alx-newsletter {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--alx-radius-lg);
    padding: var(--alx-spacing-lg);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.alx-newsletter h4 {
    font-size: 1.1rem;
    margin-bottom: var(--alx-spacing-md);
    color: var(--alx-text-primary);
}

.alx-newsletter-form {
    margin-top: var(--alx-spacing-md);
}

.alx-input-group {
    display: flex;
    gap: var(--alx-spacing-sm);
    position: relative;
}

.alx-input-field {
    flex: 1;
    padding: 14px 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid transparent;
    border-radius: var(--alx-radius-full);
    color: var(--alx-text-primary);
    font-size: 0.95rem;
    transition: var(--alx-transition-normal);
}

.alx-input-field:focus {
    outline: none;
    border-color: var(--alx-primary);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.2);
}

.alx-input-field::placeholder {
    color: var(--alx-text-muted);
}

.alx-submit-btn {
    padding: 14px 28px;
    background: var(--alx-gradient-primary);
    border: none;
    border-radius: var(--alx-radius-full);
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: var(--alx-transition-normal);
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.alx-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--alx-shadow-md);
}

.alx-submit-btn:active {
    transform: translateY(0);
}

/* === Footer Bottom === */
.alx-footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: var(--alx-spacing-md) 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.alx-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--alx-spacing-md);
}

.alx-copyright p {
    color: var(--alx-text-secondary);
    margin: 0;
    font-size: 0.9rem;
}

.alx-copyright-brand {
    color: var(--alx-primary-light);
    font-weight: 600;
}

.alx-developer p {
    color: var(--alx-text-secondary);
    margin: 0;
    font-size: 0.9rem;
}

.alx-dev-link {
    color: var(--alx-primary-light);
    text-decoration: none;
    font-weight: 500;
    transition: var(--alx-transition-normal);
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.alx-dev-link:hover {
    color: white;
    text-decoration: underline;
}

/* === Responsive Design === */
@media (max-width: 992px) {
    .alx-footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--alx-spacing-lg);
    }
    
    .alx-links-grid {
        grid-template-columns: 1fr;
    }
    
    .alx-social-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}

@media (max-width: 768px) {
    .alx-footer-grid {
        grid-template-columns: 1fr;
        gap: var(--alx-spacing-xl);
    }
    
    .alx-footer-main {
        padding: var(--alx-spacing-lg) 0;
    }
    
    .alx-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: var(--alx-spacing-sm);
    }
    
    .alx-input-group {
        flex-direction: column;
    }
    
    .alx-submit-btn {
        width: 100%;
        justify-content: center;
    }
    
    .alx-footer-wave {
        height: 80px;
    }
}

@media (max-width: 576px) {
    .alx-container {
        padding: 0 var(--alx-spacing-sm);
    }
    
    .alx-brand-name {
        font-size: 1.5rem;
    }
    
    .alx-logo-icon {
        width: 50px;
        height: 50px;
    }
    
    .alx-social-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .alx-contact-card {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    
    .alx-contact-content {
        text-align: center;
    }
}

/* === RTL Support === */
[dir="rtl"] .alx-section-title::after {
    left: auto;
    right: 0;
}

[dir="rtl"] .alx-nav-link:hover {
    transform: translateX(-5px);
}

[dir="rtl"] .alx-contact-link:hover .alx-link-arrow {
    transform: translate(-3px, -3px);
}

[dir="rtl"] .alx-nav-link:hover .alx-link-arrow {
    transform: translateX(-3px);
}

/* === Animations === */
@keyframes alx-fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes alx-pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(231, 106, 4, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(231, 106, 4, 0);
    }
}

.alx-footer-brand,
.alx-footer-links,
.alx-footer-social {
    animation: alx-fadeInUp 0.6s ease-out forwards;
}

.alx-contact-card:hover .alx-contact-icon {
    animation: alx-pulse 1.5s infinite;
}

/* === Scroll Animations === */
.alx-footer * {
    transition: var(--alx-transition-normal);
}

/* === Custom Scrollbar === */
.alx-footer ::-webkit-scrollbar {
    width: 6px;
}

.alx-footer ::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--alx-radius-full);
}

.alx-footer ::-webkit-scrollbar-thumb {
    background: var(--alx-gradient-primary);
    border-radius: var(--alx-radius-full);
}

.alx-footer ::-webkit-scrollbar-thumb:hover {
    background: var(--alx-primary-light);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize GSAP if available
    if (typeof gsap !== 'undefined') {
        gsap.from('.alx-footer-brand', {
            duration: 0.8,
            y: 30,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.2
        });
        
        gsap.from('.alx-footer-links', {
            duration: 0.8,
            y: 30,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.4
        });
        
        gsap.from('.alx-footer-social', {
            duration: 0.8,
            y: 30,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.6
        });
    }
    
    // Contact cards hover effect
    const contactCards = document.querySelectorAll('.alx-contact-card');
    contactCards.forEach(card => {
        card.addEventListener('mouseenter', (e) => {
            const icon = card.querySelector('.alx-contact-icon');
            if (icon) {
                icon.style.transform = 'rotate(15deg) scale(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', (e) => {
            const icon = card.querySelector('.alx-contact-icon');
            if (icon) {
                icon.style.transform = 'rotate(0) scale(1)';
            }
        });
    });
    
    // Navigation links hover effect
    const navLinks = document.querySelectorAll('.alx-nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', (e) => {
            const icon = link.querySelector('.alx-link-icon');
            const arrow = link.querySelector('.alx-link-arrow');
            
            if (icon) {
                icon.style.transform = 'scale(1.2)';
                icon.style.color = 'white';
            }
            
            if (arrow) {
                arrow.style.transform = 'translateX(5px)';
                arrow.style.color = '#e76a04';
            }
        });
        
        link.addEventListener('mouseleave', (e) => {
            const icon = link.querySelector('.alx-link-icon');
            const arrow = link.querySelector('.alx-link-arrow');
            
            if (icon) {
                icon.style.transform = 'scale(1)';
                icon.style.color = '';
            }
            
            if (arrow) {
                arrow.style.transform = 'translateX(0)';
                arrow.style.color = '';
            }
        });
    });
    
    // Social buttons hover effect
    const socialBtns = document.querySelectorAll('.alx-social-btn');
    socialBtns.forEach(btn => {
        btn.addEventListener('mouseenter', (e) => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(5deg)';
            }
        });
        
        btn.addEventListener('mouseleave', (e) => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0)';
            }
        });
    });
    
    // Newsletter form submission
    const newsletterForm = document.querySelector('.alx-newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('.alx-input-field');
            const submitBtn = this.querySelector('.alx-submit-btn');
            
            if (emailInput && emailInput.value) {
                // Change button text and add animation
                const originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-check"></i><span>تم الاشتراك!</span>';
                submitBtn.style.background = 'linear-gradient(135deg, #25D366 0%, #25D366 100%)';
                
                // Reset after 3 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.style.background = '';
                    emailInput.value = '';
                }, 3000);
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Wave animation
    const wave = document.querySelector('.alx-footer-wave');
    if (wave) {
        const paths = wave.querySelectorAll('path');
        paths.forEach((path, index) => {
            const length = path.getTotalLength();
            path.style.strokeDasharray = length;
            path.style.strokeDashoffset = length;
            
            // Animate stroke drawing
            setTimeout(() => {
                path.style.transition = 'stroke-dashoffset 1.5s ease-out';
                path.style.strokeDashoffset = '0';
            }, index * 300);
        });
    }
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('alx-animated');
            }
        });
    }, observerOptions);
    
    // Observe footer sections
    document.querySelectorAll('.alx-footer-brand, .alx-footer-links, .alx-footer-social').forEach(el => {
        observer.observe(el);
    });
});
</script>