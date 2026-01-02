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
        'not_specified' => 'غير محدد'
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
        'not_specified' => 'Not specified'
    ]
];
$t = $translations[$lang];
?>

<footer id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-container">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <div class="footer-company">
                        <a href="./" class="footer-logo d-flex align-items-center">
                            <div class="logo-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="logo-text">
                                <h3>Rukn Alamasy</h3>
                            </div>
                        </a>
                        <div class="footer-contact mt-4">
                            <?php
                    // البحث عن البيانات بشكل آمن
                    $location = '';
                    $phone = '';
                    $email = '';
                    
                    foreach ($contact_boxData as $item) {
                        if (isset($item['title']) && isset($item['value'])) {
                            $title = strtolower($item['title']);
                            if (strpos($title, 'location') !== false || strpos($title, 'address') !== false) {
                                $location = $item['value'];
                            } elseif (strpos($title, 'phone') !== false || strpos($title, 'tel') !== false) {
                                $phone = $item['value'];
                            } elseif (strpos($title, 'email') !== false || strpos($title, 'mail') !== false) {
                                $email = $item['value'];
                            }
                        }
                    }
                    ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['location'] ?>:</strong>
                                    <span><?= !empty($location) ? htmlspecialchars($location) : $t['not_specified'] ?></span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="contact-info">
                                    <strong><?= $t['phone'] ?>:</strong>
                                    <a href="tel:<?= !empty($phone) ? trim($phone) : '' ?>" class="contact-link">
                                        <?= !empty($phone) ? htmlspecialchars($phone) : $t['not_specified'] ?>
                                    </a>
                                </div>
                            </div>
                         
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

                        <!-- Newsletter Subscription -->
                        
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
                        © <?= date('Y') ?> <strong> <a href="">Ahmad Salloum</a></strong>. <?= $t['all_rights_reserved'] ?>
                    </p>
                </div>
                
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Variables */
:root {
   
      --primary-color: #e76a04;
      --primary-dark: #d45f00;
      --secondary-color: rgb(243, 212, 23);
      --secondary-dark: rgb(223, 192, 3);
      --dark-color: rgb(20, 71, 52);
      --dark-light: rgb(30, 91, 72);
      --light-color: #f8f9fa;
      --text-dark: #2c3e50;
      --text-light: #6c757d;
      --white: #ffffff;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    
}

/* Footer Main */
.footer {
    background: linear-gradient(135deg, var(--dark-color), var(--dark-light));
    color: var(--light-color);
    margin-top: auto;
}

.footer-main {
    padding: 80px 0 40px;
    background: linear-gradient(135deg, var(--footer-bg) 0%, #2d2d2d 100%);
}
.footer-container {
    display : flex;
    justify-content : space-between;
}
/* Footer Logo */
.footer-logo {
    text-decoration: none;
    color: var(--footer-heading);
    margin-bottom: 1.5rem;
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: var(--footer-accent);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 1.5rem;
}

[dir="rtl"] .logo-icon {
    margin-right: 0;
    margin-left: 15px;
}



/* Contact Info */
.footer-contact {
    margin-top: 1.5rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--footer-border);
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
}

.contact-info {
    flex: 1;
}

.contact-info strong {
    color: var(--footer-heading);
    font-weight: 600;
    display: block;
    margin-bottom: 2px;
}

.contact-link {
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
}

.contact-link:hover {
    color: var(--footer-accent);
}

/* Footer Links */
.footer-links h4 {
    color: var(--primary-color);
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
.footer-links li:hover{
    color: var(--primary-color); 
}

.footer-links i {
    color: var(--footer-accent);
    font-size: 0.8rem;
    margin-right: 8px;
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

/* Social Links */
.footer-social h4 {
    color: var(--primary-color);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-social p {
    color: var(--footer-text);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.social-links {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.social-links a {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--footer-border);
}

.social-links a:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    border-color: var(--footer-accent);
}



/* Copyright */
.footer-copyright {
    background: var(--footer-copyright-bg);
    padding: 20px 0;
    text-align: center;
    border-top: 1px solid var(--footer-border);
}
.footer-copyright a{
    color :var(--primary-color);
    text-decoration : none ;
}
.footer-copyright p {
    margin: 0;
    color: var(--footer-text);
    font-size: 0.9rem;
}

.footer-copyright strong {
    color: var(--footer-accent);
}

.footer-extra-links {
    display: flex;
    gap: 10px;
    justify-content: center;
}

@media (min-width: 768px) {
    .footer-extra-links {
        justify-content: flex-end;
    }
}

.footer-extra-links a {
    color: var(--footer-text);
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.footer-extra-links a:hover {
    color: var(--footer-accent);
}

.separator {
    color: var(--footer-text);
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-container {
        padding: 20px 30px;
        flex-direction : column;
    }
    
    .footer-links,
    .footer-social {
        margin-top: 2rem;
    }
    
    .footer-links h4,
    .footer-social h4 {
        font-size: 1.1rem;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .footer-copyright .row > div {
        text-align: center !important;
        margin-bottom: 10px;
    }
    
    .footer-extra-links {
        justify-content: center;
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
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate form submission
            const submitBtn = this.querySelector('button');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-check2"></i>';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                this.reset();
                
                // Show success message (you can replace this with actual form submission)
                alert('شكراً لك! تم الاشتراك بنجاح.');
            }, 1500);
        });
    }
    
    // Smooth scrolling for footer links
    document.querySelectorAll('.footer-links a[href^="#"]').forEach(anchor => {
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
});
</script>