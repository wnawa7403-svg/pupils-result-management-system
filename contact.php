<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Gradexel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/home-page.css">
     <style>
        /* ===== PRELOADER STYLES ===== */
        .preloader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #02114e 0%, #080431 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .preloader-overlay.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .dual-ring-spinner {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }

        .dual-ring-spinner .ring-outer {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-top-color: #fff;
            border-right-color: #fbbf24;
            animation: spin-outer 0.85s cubic-bezier(0.4, 0.2, 0.3, 0.9) infinite;
        }

        .dual-ring-spinner .ring-inner {
            position: absolute;
            width: 58%;
            height: 58%;
            top: 21%;
            left: 21%;
            border-radius: 50%;
            border: 2.5px solid rgba(255, 255, 255, 0.25);
            border-bottom-color: #f59e0b;
            border-left-color: #fffbeb;
            animation: spin-inner 0.65s linear infinite;
        }

        .dual-ring-spinner .breathing-scale {
            position: absolute;
            width: 100%;
            height: 100%;
            animation: breathe 2.2s ease-in-out infinite;
        }

        .dual-ring-spinner .ripple {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.6);
            opacity: 0;
            animation: rippleWave 2s cubic-bezier(0.2, 0.9, 0.4, 1) infinite;
        }

        .dual-ring-spinner .ripple-delay {
            animation: rippleWave 2s cubic-bezier(0.2, 0.9, 0.4, 1) 0.66s infinite;
            border-color: #fbbf24;
        }

        @keyframes spin-outer {
            to { transform: rotate(360deg); }
        }

        @keyframes spin-inner {
            to { transform: rotate(-360deg); }
        }

        @keyframes breathe {
            0%, 100% {
                transform: scale(0.88);
                opacity: 0.92;
            }
            50% {
                transform: scale(1.08);
                opacity: 1;
            }
        }

        @keyframes rippleWave {
            0% {
                transform: scale(0.7);
                opacity: 0.7;
                border-width: 2px;
            }
            100% {
                transform: scale(1.6);
                opacity: 0;
                border-width: 1px;
            }
        }

        .loading-text {
            margin-top: 28px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            color: white;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .loading-dots {
            display: inline-flex;
            gap: 4px;
        }

        .loading-dots span {
            width: 5px;
            height: 5px;
            background: white;
            border-radius: 50%;
            animation: dotPulse 1.2s ease infinite;
        }

        .loading-dots span:nth-child(2) {
            animation-delay: 0.2s;
            background: #fbbf24;
        }
        .loading-dots span:nth-child(3) {
            animation-delay: 0.4s;
            background: #f59e0b;
        }

        @keyframes dotPulse {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        @media (prefers-reduced-motion: reduce) {
            .dual-ring-spinner .ring-outer,
            .dual-ring-spinner .ring-inner,
            .dual-ring-spinner .breathing-scale,
            .dual-ring-spinner .ripple,
            .dual-ring-spinner .ripple-delay {
                animation-duration: 1.5s;
            }
        }
    </style>
</head>

<body>
   <body>
    <!-- ===== PRELOADER ===== -->
    <div id="preloader" class="preloader-overlay">
        <div>
            <div class="dual-ring-spinner" role="status" aria-label="Loading Gradexel">
                <div class="breathing-scale">
                    <div class="ring-outer"></div>
                    <div class="ring-inner"></div>
                </div>
                <div class="ripple"></div>
                <div class="ripple ripple-delay"></div>
            </div>
           
        </div>
    </div>

    <header>
        <div class="container header-container">
            <a href="index.html" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradexel
            </a>
            
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
            
            <ul class="nav-menu">
                <li><a href="./index.php" class="active">Home</a></li>
                   <li><a href="./about.php" class="active">about Us</a></li>
                <li><a href="./contact.php" class="active">contact</a></li>
                <li><a href="./service.php" class="active">services</a></li>
                <li><a href="./Login/login.php">Login</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-white">
            <h1 style="font-size: 48px; color: white; font-weight: bold;">Contact Us</h1>
            <p style="font-size: 24px; color: white;">Get in Touch - We're Here to Help</p>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Get In Touch</h2>
            <p class="section-subtitle">We'd love to hear from you. Choose the most convenient way to reach us.</p>
            
            <div class="features-grid">
                <div class="feature-card text-center">
                    <div class="feature-icon">
              <i class="fa-brands fa-whatsapp"></i>

                    </div>
                    <h3>Call Us</h3>
                    <p>chat with our support team</p>
                    <a href="https://wa.me/260972189510?text=Hello%20I%20would%20like%20to%20know%20more" target="_blank" class="btn btn-success">
    			<i class="fa-brands fa-whatsapp"></i> +260 972189510
</a>

                </div>
                
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>Send us your questions and feedback</p>
                    <a href="mailto:wnawa7403@gmail.com" class="btn btn-primary mt-3">wnawa7403@gmail.com</a>
                </div>
                
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Support Hours</h3>
                    <p>We're here to help when you need us</p>
                    <p class="mt-3"><strong>Mon - Fri:</strong> 8:00 AM - 6:00 PM<br>
                    <strong>Sat:</strong> 9:00 AM - 1:00 PM<br>
                    <strong>Sun:</strong> Closed</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="how-it-works">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="feature-card">
                        <h3 class="text-center mb-4">Send Us a Message</h3>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <input type="text" class="form-control" id="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="institution" class="form-label">Institution/School</label>
                                <input type="text" class="form-control" id="institution">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="feature-card">
                        <h4>How do I get started with Gradexel?</h4>
                        <p>Simply create an account on our platform, and you can start adding students and managing results immediately. Our basic plan is free for up to 50 students.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h4>Is my data secure?</h4>
                        <p>Yes, we use enterprise-level security measures including data encryption and secure servers to protect all your academic information.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h4>Can I import existing student data?</h4>
                        <p>Absolutely! We support CSV imports for easy migration of your existing student records and academic data.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <h4>Do you offer training?</h4>
                        <p>Yes, we provide comprehensive training materials and live webinars to help you get the most out of Gradexel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Gradexel</h3>
                    <p>Comprehensive pupil result management system designed for modern educational institutions.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                         
                <li><a href="./about.php" class="active">About Us</a></li>
                <li><a href="./services.php">Services</a></li>
                <li><a href="./contact.php">Contact</a></li>
                <li><a href="./Login/login.php">Login</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone"></i> 09721819510</li>
                        <li><i class="fas fa-envelope"></i> wnawa7403@gmail.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> Zambia</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 Gradexel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
<script>
        // ===== PRELOADER SCRIPT =====
        (function() {
            const preloader = document.getElementById('preloader');
            const startTime = Date.now();
            const minimumLoadTime = 1200; // Show spinner for at least 1.2 seconds

            function hidePreloader() {
                if (preloader) {
                    preloader.classList.add('hidden');
                    setTimeout(() => {
                        if (preloader.parentNode) {
                            preloader.remove();
                        }
                    }, 600);
                }
            }

            // Wait for page to fully load
            window.addEventListener('load', function() {
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumLoadTime - elapsed);
                setTimeout(hidePreloader, remainingTime);
            });

            // Fallback: hide after 5 seconds max
            setTimeout(hidePreloader, 5000);
        })();

 
        
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Contact form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });
        
        document.addEventListener('contextmenu', event => event.preventDefault());
  document.addEventListener('keydown', event => {
    if (event.key === "PrintScreen") {
      alert("Screenshots are disabled on this page.");
      event.preventDefault();
    }
  });
  document.addEventListener('selectstart', event => event.preventDefault());
    </script>
</body>
</html>