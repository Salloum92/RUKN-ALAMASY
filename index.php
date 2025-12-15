<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ركن الأمسي - قريباً</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            width: 100%;
            padding: 30px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: float 20s linear infinite;
            z-index: -1;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-30px, -30px) rotate(360deg); }
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 3.5rem;
            color: #ffd166;
            text-shadow: 0 0 15px rgba(255, 209, 102, 0.5);
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .logo .subtitle {
            font-size: 1.2rem;
            color: #a9d6e5;
            font-weight: 300;
            letter-spacing: 2px;
        }
        
        .main-content {
            margin: 40px 0;
        }
        
        .main-content h2 {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: #ffd166;
        }
        
        .main-content p {
            font-size: 1.3rem;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 30px;
            color: #e0e0e0;
        }
        
        .countdown {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin: 50px 0;
        }
        
        .countdown-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px 20px;
            min-width: 140px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        
        .countdown-item:hover {
            transform: translateY(-10px);
            background: rgba(255, 209, 102, 0.15);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .countdown-value {
            font-size: 3.5rem;
            font-weight: 700;
            color: #ffd166;
            display: block;
            line-height: 1;
            text-shadow: 0 0 10px rgba(255, 209, 102, 0.3);
        }
        
        .countdown-label {
            font-size: 1.2rem;
            color: #a9d6e5;
            margin-top: 10px;
            display: block;
        }
        
        .subscribe-form {
            max-width: 600px;
            margin: 50px auto;
        }
        
        .subscribe-form h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #ffd166;
        }
        
        .form-group {
            display: flex;
            flex-direction: row;
            gap: 15px;
            margin-top: 20px;
        }
        
        input[type="email"] {
            flex: 1;
            padding: 18px 25px;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1.1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        input[type="email"]:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: #ffd166;
            box-shadow: 0 0 15px rgba(255, 209, 102, 0.2);
        }
        
        input[type="email"]::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .subscribe-btn {
            padding: 18px 40px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(45deg, #ffd166, #ff9e00);
            color: #16213e;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .subscribe-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 209, 102, 0.3);
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 50px;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-link:hover {
            background: #ffd166;
            color: #16213e;
            transform: translateY(-5px);
        }
        
        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .contact-info p {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #a9d6e5;
        }
        
        .contact-info i {
            color: #ffd166;
            margin-left: 10px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .logo h1 {
                font-size: 2.8rem;
            }
            
            .main-content h2 {
                font-size: 2.2rem;
            }
            
            .main-content p {
                font-size: 1.1rem;
            }
            
            .countdown-item {
                min-width: 120px;
                padding: 20px 15px;
            }
            
            .countdown-value {
                font-size: 2.8rem;
            }
            
            .form-group {
                flex-direction: column;
            }
            
            .subscribe-btn {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 20px;
            }
            
            .logo h1 {
                font-size: 2.2rem;
            }
            
            .main-content h2 {
                font-size: 1.8rem;
            }
            
            .countdown {
                gap: 15px;
            }
            
            .countdown-item {
                min-width: 100px;
                padding: 15px 10px;
            }
            
            .countdown-value {
                font-size: 2.2rem;
            }
            
            .countdown-label {
                font-size: 1rem;
            }
        }
        
        /* Animation for the coming soon text */
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>ركن الأماسي</h1>
            <div class="subtitle">Rukn Al-Amasy</div>
        </div>
        
        <div class="main-content">
            <h2 class="pulse">نحن قادمون قريباً!</h2>
            <p>نعمل حالياً على تطوير موقع ركن الأماسي لتقديم أفضل تجربة لمستخدمينا. نحن نعمل بجد لإطلاق الموقع قريباً جداً.</p>
        </div>
        
       
        
        
        
       
    </div>

    <script>
        // Set the launch date (3 months from now)
        const launchDate = new Date();
        launchDate.setMonth(launchDate.getMonth() + 3);
        
        // Countdown function
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = launchDate.getTime() - now;
            
            // Calculate days, hours, minutes, seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update the countdown display
            document.getElementById("days").textContent = days.toString().padStart(2, '0');
            document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
            document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');
            
            // If the countdown is over
            if (distance < 0) {
                clearInterval(countdownInterval);
                document.querySelector(".countdown").innerHTML = "<h2>لقد تم الإطلاق!</h2>";
            }
        }
        
        // Initialize countdown
        updateCountdown();
        const countdownInterval = setInterval(updateCountdown, 1000);
        
        // Email subscription
        document.getElementById("subscribeBtn").addEventListener("click", function() {
            const email = document.getElementById("email").value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                alert("الرجاء إدخال بريد إلكتروني صحيح");
                return;
            }
            
            // In a real application, you would send this to a server
            
        });
        
        // Allow pressing Enter to submit the form
        document.getElementById("email").addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                document.getElementById("subscribeBtn").click();
            }
        });
        
        // Add some visual effects to countdown items
        const countdownItems = document.querySelectorAll('.countdown-item');
        countdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>