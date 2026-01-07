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
        'follow_desc' => 'سنبقيك على اطلاع بأحدث المنتجات والعروض. تابعنا على وسائل التواصل الاجتماعي!',
        'copyright' => 'حقوق النشر',
        'all_rights_reserved' => 'جميع الحقوق محفوظة',
        'not_specified' => 'غير محدد',
        'working_hours' => 'ساعات العمل',
        'get_directions' => 'احصل على الاتجاهات',
        'subscribe_newsletter' => 'اشترك في النشرة البريدية',
        'your_email' => 'بريدك الإلكتروني',
        'subscribe' => 'اشتراك'
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
        'follow_desc' => 'We will keep you updated with the latest products and offers. Follow us on social media!',
        'copyright' => 'Copyright',
        'all_rights_reserved' => 'All Rights Reserved',
        'not_specified' => 'Not specified',
        'working_hours' => 'Working Hours',
        'get_directions' => 'Get Directions',
        'subscribe_newsletter' => 'Subscribe to Newsletter',
        'your_email' => 'Your Email',
        'subscribe' => 'Subscribe'
    ]
];

// استخدام اللغة من الجلسة
$lang = $_SESSION['lang'] ?? 'ar';
$t = $translations[$lang];

// استعلامات قاعدة البيانات
$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');

// البحث عن بيانات الاتصال من جدول contact_box
$footer_contact_data = [
    'location' => ['value' => '', 'icon' => 'bi bi-geo-alt', 'type' => 'location'],
    'phone' => ['value' => '', 'icon' => 'bi bi-telephone', 'type' => 'phone'],
    'email' => ['value' => '', 'icon' => 'bi bi-envelope', 'type' => 'email'],
    'working_hours' => ['value' => '', 'icon' => 'bi bi-clock', 'type' => 'working_hours']
];

// البحث في جدول contact_box أولاً (النظام الجديد)
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
    
    // دعم الهياكل القديمة باستخدام title
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

// إذا لم نجد البيانات في contact_box، ابحث في contact (النظام القديم)
if (empty($footer_contact_data['phone']['value']) && isset($contactData[0]['phone'])) {
    $footer_contact_data['phone']['value'] = $contactData[0]['phone'];
}

if (empty($footer_contact_data['email']['value']) && isset($contactData[0]['email'])) {
    $footer_contact_data['email']['value'] = $contactData[0]['email'];
}

// 🔴 استرجاع بيانات الموقع والرابط من نفس منطق الهيدر
$google_maps_url = '';
$location_address = '';

// أولاً: البحث عن رابط خرائط جوجل مباشرة (مثل الهيدر)
$google_maps_item = $query->select('contact_box', '*', "WHERE id = 1")[0] ?? null;

if ($google_maps_item && !empty($google_maps_item['value'])) {
    $google_maps_url = $google_maps_item['value'];
    $location_address = $google_maps_item['label'] ?? $t['get_directions'];
} else {
    // البحث عن أي سجل يحتوي على google_maps في النوع
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'google_maps' && !empty($item['value'])) {
            $google_maps_url = $item['value'];
            $location_address = $item['label'] ?? $t['get_directions'];
            break;
        }
    }
}

// إذا لم نجد رابط خرائط جوجل، استخدم العنوان النصي (مثل الهيدر)
if (empty($google_maps_url)) {
    // البحث عن العنوان النصي
    foreach ($contact_boxData as $item) {
        if (isset($item['type']) && $item['type'] === 'location' && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
        // دعم الهياكل القديمة
        if (isset($item['title']) && stripos($item['title'], 'موقع') !== false && !empty($item['value'])) {
            $location_address = $item['value'];
            break;
        }
    }
    
    // إذا لم نجد في contact_box، ابحث في جدول contact
    if (empty($location_address) && isset($contactData[0]['location'])) {
        $location_address = $contactData[0]['location'];
    }
    
    // إنشاء رابط خرائط من العنوان إذا كان موجوداً
    if (!empty($location_address)) {
        $encoded_address = urlencode($location_address);
        $google_maps_url = "https://www.google.com/maps/search/?api=1&query=" . $encoded_address;
    }
}

// إذا كان لدينا رابط خرائط جوجل ولم يكن لدينا عنوان نصي، استخدم التسمية
if (!empty($google_maps_url) && empty($location_address)) {
    $location_address = $t['get_directions'];
}
?>

<footer id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-container">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                   
                       <div class="logo-section">
                    <a href="index.php" class="logo">
                         <div class="logo-text">
                            <span class="brand-name">Rukn Alamasy</span>
                        </div>
                        <div class="logo-image">
                            <img src="assets/img/logo.png" alt="Rukn Alamasy"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiByeD0iMTAiIGZpbGw9IiNlNzZhMDQiLz4KPHN2ZyB4PSIxMiIgeT0iMTIiIHdpZHRoPSIyNiIgaGVpZ2h0PSIyNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiPgo8cGF0aCBkPSJNMTIgMkM2LjQ4IDIgMiA2LjQ4IDIgMTJzNC40OCAxMCAxMCAxMCAxMC00LjQ4IDEwLTEwUzE3LjUyIDIgMTIgMnpNMTIgMjBsLTMtMyAyLTcgNyA1LTIgN3oiLz4KPC9zdmc+Cjwvc3ZnPg=='">
                        </div>
                       
                        
                    </a>
              
                        <div class="footer-contact mt-4">
                            <!-- العنوان مع رابط خرائط جوجل -->
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="<?= $footer_contact_data['location']['icon'] ?>"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['location'] ?>:</strong>
                                    <?php if (!empty($google_maps_url) && !empty($location_address)): ?>
                                        <a href="<?= htmlspecialchars($google_maps_url) ?>" target="_blank" class="contact-link location-link" title="<?= $t['get_directions'] ?>">
                                            <?= htmlspecialchars($location_address) ?>
                                            <i class="bi bi-arrow-up-right ms-1"></i>
                                        </a>
                                    <?php elseif (!empty($footer_contact_data['location']['value'])): ?>
                                        <span><?= htmlspecialchars($footer_contact_data['location']['value']) ?></span>
                                    <?php else: ?>
                                        <span><?= $t['not_specified'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- الهاتف -->
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="<?= $footer_contact_data['phone']['icon'] ?>"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['phone'] ?>:</strong>
                                    <?php if (!empty($footer_contact_data['phone']['value'])): ?>
                                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $footer_contact_data['phone']['value']) ?>" class="contact-link">
                                            <?= htmlspecialchars($footer_contact_data['phone']['value']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span><?= $t['not_specified'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="<?= $footer_contact_data['email']['icon'] ?>"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['email'] ?>:</strong>
                                    <?php if (!empty($footer_contact_data['email']['value'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($footer_contact_data['email']['value']) ?>" class="contact-link">
                                            <?= htmlspecialchars($footer_contact_data['email']['value']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span><?= $t['not_specified'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- ساعات العمل -->
                            <?php if (!empty($footer_contact_data['working_hours']['value'])): ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="<?= $footer_contact_data['working_hours']['icon'] ?>"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['working_hours'] ?>:</strong>
                                    <span><?= htmlspecialchars($footer_contact_data['working_hours']['value']) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="footer-links">
                        <h4><?= $t['useful_links'] ?></h4>
                        <ul>
                            <li>
                                <i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i>
                                <a href="./"><?= $t['home'] ?></a>
                            </li>
                            <li>
                                <i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i>
                                <a href="about.php"><?= $t['about'] ?></a>
                            </li>
                            <li>
                                <i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i>
                                <a href="products.php"><?= $t['products'] ?></a>
                            </li>
                            <li>
                                <i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i>
                                <a href="services.php"><?= $t['services'] ?></a>
                            </li>
                            <li>
                                <i class="bi bi-chevron-<?= ($lang == 'ar') ? 'left' : 'right' ?>"></i>
                                <a href="contact.php"><?= $t['contact'] ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter Subscription -->
               
                <!-- Social Media -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="footer-social">
                        <h4><?= $t['follow_us'] ?></h4>
                        <p><?= $t['follow_desc'] ?></p>
                        
                        <div class="social-links">
                            <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
                                <a href="https://x.com/<?= $contactData[0]['twitter'] ?>" class="twitter" target="_blank" title="Twitter">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
                                <a href="https://facebook.com/<?= $contactData[0]['facebook'] ?>" class="facebook" target="_blank" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
                                <a href="https://instagram.com/<?= $contactData[0]['instagram'] ?>" class="instagram" target="_blank" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['linkedin']) && !empty($contactData[0]['linkedin'])): ?>
                                <a href="https://linkedin.com/in/<?= $contactData[0]['linkedin'] ?>" class="linkedin" target="_blank" title="LinkedIn">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
                                <a href="https://www.youtube.com/<?= $contactData[0]['youtube'] ?>" class="youtube" target="_blank" title="YouTube">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
                                <a href="https://wa.me/<?= $contactData[0]['whatsapp'] ?>" class="whatsapp" target="_blank" title="WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-copyright">
        <div class="container">
            <div class="row align-items-center">
                <div class="text-center">
                    <p class="mb-0">
                        © <?= date('Y') ?> <strong><a href="./">Rukn Alamasy</a></strong>. <?= $t['all_rights_reserved'] ?>
                    </p>
                    <p class="mb-0 mt-1" style="font-size: 0.8rem; opacity: 0.8;">
                        <?= $lang == 'ar' ? 'تم التطوير بواسطة' : 'Developed by' ?> <a href="https://github.com/ahmadsalloum" target="_blank">Ahmad Salloum</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Variables */
:root {
    --footer-bg: #144734ff;
    --footer-dark: #0d3528;
    --footer-accent: #e76a04;
    --footer-text: #bdc3c7;
    --footer-heading: #ffffff;
    --footer-border: rgba(255, 255, 255, 0.1);
    --footer-copyright-bg: rgba(0, 0, 0, 0.2);
}

/* Footer Main */
.footer {
    background: linear-gradient(135deg, var(--dark-color), var(--dark-light));
    color: var(--light-color);
    margin-top: auto;
}

.footer-main {
    padding: 60px 0 30px;
    background: linear-gradient(135deg, var(--dark-color) 0%, var(--dark-light) 100%);
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
}

/* Footer Logo */
.footer-logo {
    text-decoration: none;
    color: var(--footer-heading);
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.footer-logo:hover {
    transform: translateY(-3px);
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: var(--footer-accent);
    border-radius: 10px;
    display: flex;
    align-items: right;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 1.5rem;
    transition: var(--transition);
}
.logo {
          display: flex;
    align-items: center;
    justify-content: right ;
    
        gap: 12px;
        text-decoration: none;
        transition: var(--transition);
    }

.logo-image img {
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius);
        object-fit: cover;
        border: 3px solid var(--primary-color);
        box-shadow: 0 4px 15px rgba(231, 106, 4, 0.2);
        transition: var(--transition);
        text-align : right ;
}

    .logo:hover .logo-image img {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(231, 106, 4, 0.3);
    }

.footer-logo:hover .logo-icon {
    background: var(--primary-dark);
    transform: rotate(10deg);
}

[dir="rtl"] .logo-icon {
    margin-right: 0;
    margin-left: 15px;
}

.logo-text h3 {
    color: var(--secondary-color);
    font-weight: 700;
    margin-bottom: 5px;
    font-size: 1.5rem;
}

.logo-text p {
    color: var(--footer-text);
    font-size: 0.9rem;
    margin: 0;
    opacity: 0.8;
}
.brand-name {
        color: var(--secondary-color);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.2;
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
/* Contact Info */
.footer-contact {
    margin-top: 1.5rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--footer-border);
    transition: var(--transition);
}

.contact-item:hover {
    transform: translateX(5px);
}

[dir="rtl"] .contact-item:hover {
    transform: translateX(-5px);
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-icon {
    width: 36px;
    height: 36px;
    background: rgba(231, 106, 4, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--footer-accent);
    font-size: 1rem;
    flex-shrink: 0;
    transition: var(--transition);
}

.contact-item:hover .contact-icon {
    background: rgba(231, 106, 4, 0.2);
    transform: scale(1.1);
}

.contact-info {
    flex: 1;
}

.contact-info strong {
    color: var(--footer-heading);
    font-weight: 600;
    display: block;
    margin-bottom: 2px;
    font-size: 0.95rem;
}

.contact-link {
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.contact-link:hover {
    color: var(--footer-accent);
    transform: translateX(3px);
}

.location-link:hover {
    color: #25D366;
}

.contact-info span {
    color: var(--footer-text);
    font-size: 0.9rem;
}

/* Footer Links */
.footer-links {
    padding: 10px 0;
}

.footer-links h4 {
    color: var(--footer-heading);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 10px;
}

.footer-links h4::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: var(--footer-accent);
    border-radius: 2px;
}

[dir="rtl"] .footer-links h4::after {
    left: auto;
    right: 0;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.footer-links li:hover {
    color: var(--footer-accent);
}

.footer-links i {
    color: var(--footer-accent);
    font-size: 0.8rem;
    margin-right: 8px;
    transition: var(--transition);
}

.footer-links li:hover i {
    transform: scale(1.2);
}

[dir="rtl"] .footer-links i {
    margin-right: 0;
    margin-left: 8px;
}

.footer-links a {
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.footer-links a:hover {
    color: var(--footer-accent);
    transform: translateX(5px);
}

[dir="rtl"] .footer-links a:hover {
    transform: translateX(-5px);
}

/* Newsletter */
.footer-newsletter {
    padding: 10px 0;
}

.footer-newsletter h4 {
    color: var(--footer-heading);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-newsletter p {
    color: var(--footer-text);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.newsletter-form .input-group {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 30px;
    overflow: hidden;
    border: 1px solid var(--footer-border);
    transition: var(--transition);
}

.newsletter-form .input-group:focus-within {
    border-color: var(--footer-accent);
    box-shadow: 0 0 0 3px rgba(231, 106, 4, 0.1);
    transform: translateY(-2px);
}

.newsletter-form .form-control {
    background: transparent;
    border: none;
    color: var(--footer-heading);
    padding: 12px 20px;
}

.newsletter-form .form-control::placeholder {
    color: var(--footer-text);
    opacity: 0.7;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    background: transparent;
    color: var(--footer-heading);
}

.newsletter-form .btn {
    background: var(--footer-accent);
    border: none;
    color: white;
    padding: 12px 20px;
    transition: var(--transition);
}

.newsletter-form .btn:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}

/* Social Links */
.footer-social {
    padding: 10px 0;
}

.footer-social h4 {
    color: var(--footer-heading);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-social p {
    color: var(--footer-text);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.social-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.social-links a {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--footer-border);
    position: relative;
    overflow: hidden;
}

.social-links a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
}

.social-links a:hover::before {
    left: 100%;
}

.social-links a:hover {
    transform: translateY(-3px);
    border-color: var(--footer-accent);
}

.social-links a.twitter:hover {
    background: #1DA1F2;
    color: white;
}

.social-links a.facebook:hover {
    background: #1877F2;
    color: white;
}

.social-links a.instagram:hover {
    background: #E4405F;
    color: white;
}

.social-links a.linkedin:hover {
    background: #0A66C2;
    color: white;
}

.social-links a.youtube:hover {
    background: #FF0000;
    color: white;
}

.social-links a.whatsapp:hover {
    background: #25D366;
    color: white;
}

/* Copyright */
.footer-copyright {
    background: var(--footer-copyright-bg);
    padding: 20px 0;
    text-align: center;
    border-top: 1px solid var(--footer-border);
    margin-top: 30px;
}

.footer-copyright a {
    color: var(--footer-accent);
    text-decoration: none;
    transition: var(--transition);
}

.footer-copyright a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

.footer-copyright p {
    margin: 0;
    color: var(--footer-text);
    font-size: 0.9rem;
}

.footer-copyright strong {
    color: var(--footer-accent);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .footer-container {
        justify-content: space-around;
    }
    
    .col-lg-4, .col-lg-2 {
        flex: 0 0 calc(50% - 30px);
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    .footer-main {
        padding: 40px 0 20px;
    }
    
    .footer-container {
        flex-direction: column;
        gap: 30px;
    }
    
    .col-lg-4, .col-lg-2 {
        flex: 0 0 100%;
        width: 100%;
        margin-bottom: 30px;
    }
    
    .footer-links h4,
    .footer-newsletter h4,
    .footer-social h4 {
        font-size: 1.1rem;
    }
    
    .social-links {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .footer-logo {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .logo-icon {
        margin-right: 0;
        margin-left: 0;
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .contact-icon {
        margin: 0 auto;
    }
    
    .contact-link:hover {
        transform: none;
    }
}

/* Animations */
.footer * {
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            // التحقق من صحة البريد الإلكتروني
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailInput.focus();
                emailInput.style.border = '2px solid #dc3545';
                setTimeout(() => {
                    emailInput.style.border = '';
                }, 2000);
                return;
            }
            
            // Simulate form submission
            const submitBtn = this.querySelector('button');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-check2"></i>';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                this.reset();
                
                // إظهار رسالة نجاح
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle-fill me-2"></i>
                    ${document.documentElement.lang === 'ar' ? 
                      'شكراً لك! تم الاشتراك بنجاح.' : 
                      'Thank you! Subscription successful.'}
                `;
                this.parentNode.insertBefore(alertDiv, this.nextSibling);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }, 1500);
        });
    }
    
    // تأثير hover على عناصر الاتصال
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.contact-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });
    
    // تأثير على روابط الخريطة
    const locationLinks = document.querySelectorAll('.location-link');
    locationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(45deg)';
                setTimeout(() => {
                    icon.style.transform = '';
                }, 300);
            }
        });
    });
});
</script>