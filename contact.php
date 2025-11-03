<?php
// يجب أن تكون الجلسة في أعلى الصفحة قبل أي output
session_start();
include 'config.php';
$query = new Database();
$contact_boxData = $query->select('contact_box');
$contactData = $query->select('contact');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Contact Us - Rukn Alamasy</title>
  <meta name="description" content="Get in touch with Rukn Alamasy for premium products and services">
  <meta name="keywords" content="contact, support, inquiry, customer service">
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

<body class="contact-page">

  <?php include 'includes/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <section class="contact-hero" data-aos="fade-down">
      <div class="container">
        <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
        <p class="lead mb-4">We're here to help and answer any questions you might have</p>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <p class="mb-0">Reach out to us and we'll get back to you as soon as possible</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info-section">
      <div class="container">
        

        <div class="row">
          <?php foreach ($contact_boxData as $contact): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
              <div class="contact-card">
                <div class="contact-icon">
                  <i class="<?php echo $contact['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($contact['title']); ?></h3>
                <p><?php echo htmlspecialchars($contact['value']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Social Media Links -->
        <div class="row">
          <div class="col-12 text-center" data-aos="fade-up" data-aos-delay="200">
            <h3 class="mb-4">Follow Us</h3>
            <div class="social-links-contact">
              <?php if (isset($contactData[0]['twitter']) && !empty($contactData[0]['twitter'])): ?>
                    <a href="https://x.com/<?php echo $contactData[0]['twitter']; ?>" class="twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
                <?php endif; ?>
                <?php if (isset($contactData[0]['facebook']) && !empty($contactData[0]['facebook'])): ?>
                    <a href="https://facebook.com/<?php echo $contactData[0]['facebook']; ?>" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                <?php endif; ?>
                <?php if (isset($contactData[0]['instagram']) && !empty($contactData[0]['instagram'])): ?>
                    <a href="https://instagram.com/<?php echo $contactData[0]['instagram']; ?>" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
                <?php endif; ?>
                <?php if (isset($contactData[0]['linkedin']) && !empty($contactData[0]['linkedin'])): ?>
                    <a href="https://linkedin.com/in/<?php echo $contactData[0]['linkedin']; ?>" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
                <?php endif; ?>
                 <?php if (isset($contactData[0]['youtube']) && !empty($contactData[0]['youtube'])): ?>
                    <a href="https://www.youtube.com/<?php echo $contactData[0]['youtube']; ?>" class="twitter" target="_blank"><i class="bi bi-youtube"></i></a>
                <?php endif; ?>
                <?php if (isset($contactData[0]['telegram']) && !empty($contactData[0]['telegram'])): ?>
                    <a href="https://t.me/<?php echo $contactData[0]['telegram']; ?>" class="twitter" target="_blank"><i class="bi bi-telegram"></i></a>
                <?php endif; ?>
                <?php if (isset($contactData[0]['whatsapp']) && !empty($contactData[0]['whatsapp'])): ?>
                    <a href="https://wa.me/<?php echo $contactData[0]['whatsapp']; ?>" class="twitter" target="_blank"><i class="bi bi-whatsapp"></i></a>
                <?php endif; ?>
             
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Map Section -->
    <section class="contact-form-section">
      <div class="container">
        <div class="row gy-5">
          <!-- Contact Form -->
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="form-card">
              <h2 class="text-center mb-4">Send Us a Message</h2>
              <form action="send_message.php" method="post" class="php-email-form" id="contactForm">
                <div class="row">
                  <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required maxlength="255">
                  </div>
                  <div class="col-md-6">
                    <input type="email" class="form-control" name="email" placeholder="Your Email" required maxlength="255">
                  </div>
                </div>
                <input type="text" class="form-control" name="subject" placeholder="Subject" required maxlength="255">
                <textarea class="form-control" name="message" rows="6" placeholder="Your Message" required></textarea>
                
                <button type="submit" class="btn-submit">
                  <i class="bi bi-send me-2"></i>Send Message
                </button>
                
                <div class="alert alert-success mt-3" style="display: none;">
                  <i class="bi bi-check-circle me-2"></i>Your message has been sent successfully!
                </div>
                <div class="alert alert-danger mt-3" style="display: none;">
                  <i class="bi bi-exclamation-circle me-2"></i><span class="error-message"></span>
                </div>
              </form>
            </div>
          </div>

          <!-- Google Map -->
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="map-container">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2974.8813426551865!2d67.01298087569626!3d39.58263960598262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d21d3f20f2e7d%3A0x65da282d59cb1b22!2sUy!5e1!3m2!1sen!2s!4v1738728573422!5m2!1sen!2s"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
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

      // Contact Form Handling
      document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const submitButton = e.target.querySelector('button[type="submit"]');
        const successAlert = e.target.querySelector('.alert-success');
        const errorAlert = e.target.querySelector('.alert-danger');
        const errorMessage = e.target.querySelector('.error-message');

        // Reset alerts
        successAlert.style.display = 'none';
        errorAlert.style.display = 'none';
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';

        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_message.php', true);
        xhr.onload = function () {
          if (this.status === 200) {
            try {
              const response = JSON.parse(this.responseText);
              if (response.status === 'success') {
                successAlert.style.display = 'block';
                errorAlert.style.display = 'none';
                document.getElementById('contactForm').reset();
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                  successAlert.style.display = 'none';
                }, 5000);
              } else {
                errorMessage.textContent = response.message || 'An error occurred. Please try again.';
                errorAlert.style.display = 'block';
              }
            } catch (e) {
              errorMessage.textContent = 'An error occurred. Please try again.';
              errorAlert.style.display = 'block';
            }
          } else {
            errorMessage.textContent = 'Network error. Please check your connection.';
            errorAlert.style.display = 'block';
          }
          
          submitButton.disabled = false;
          submitButton.innerHTML = '<i class="bi bi-send me-2"></i>Send Message';
        };
        
        xhr.onerror = function () {
          errorMessage.textContent = 'Network error. Please check your connection.';
          errorAlert.style.display = 'block';
          submitButton.disabled = false;
          submitButton.innerHTML = '<i class="bi bi-send me-2"></i>Send Message';
        };
        
        xhr.send(formData);
      });
    });
  </script>

</body>

</html>