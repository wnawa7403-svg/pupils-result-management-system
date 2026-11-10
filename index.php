<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradexel - Pupil Result Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="./css/home-page.css">
    
    <!-- Preloader CSS -->
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
            <div class="loading-text">
                <span>loading Gradexel</span>
                <div class="loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="#" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradexel
            </a>
            
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
            
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="service.php">Services</a></li>
                <li><a href="./Login/login.php">Login</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-white">
            <p style="font-size: 36px; color: white; font-weight: bold;">Excellence in Every Grade</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Features</h2>
            <p class="section-subtitle">Designed specifically for educational institutions to simplify result management</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Performance Analytics</h3>
                    <p>Track student progress with visual analytics and identify areas for improvement.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3>Report Generation</h3>
                    <p>Generate comprehensive reports and transcripts with just a few clicks.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Automated Alerts</h3>
                    <p>Receive notifications for missing grades or below-average performances.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Simple steps to manage your student results effectively</p>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Add Students</h3>
                    <p>Create classes and add students to your system</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Input Results</h3>
                    <p>Enter grades and assessment results</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Generate Reports</h3>
                    <p>Create transcripts and performance reports</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Educators Say</h2>
            <p class="section-subtitle">Hear from teachers and administrators using our system</p>
            
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">Gradexel has saved me countless hours of paperwork. I can now focus more on teaching and less on administrative tasks.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MJ</div>
                        <div class="author-info">
                            <h4>Maria Johnson</h4>
                            <p>Science Teacher</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-text">The analytics features have helped us identify struggling students early and provide timely interventions.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">RS</div>
                        <div class="author-info">
                            <h4>Robert Smith</h4>
                            <p>School Principal</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-text">Generating report cards used to take weeks. Now with Gradexel, we can do it in just a few days.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">NW</div>
                        <div class="author-info">
                            <h4>Nawa William</h4>
                            <p>Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Transform Your Result Management?</h2>
            <p>Join hundreds of schools already using Gradexel to streamline their academic processes</p>
            <a href="index.php" class="btn btn-accent">Get Started Today</a>
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
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="service.php">Services</a></li>
                        <li><a href="./Login/login.php">Login</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Resources</h3>
                    <ul class="footer-links">
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Support Center</a></li>
                        <li><a href="#">Webinars</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Education Street, Academic City</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> info@Gradexel.com</li>
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
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.querySelector('.nav-menu');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (menu && menuBtn && !menu.contains(event.target) && !menuBtn.contains(event.target)) {
                menu.classList.remove('active');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navMenu = document.querySelector('.nav-menu');
                    if (navMenu) {
                        navMenu.classList.remove('active');
                    }
                }
            });
        });

        // Disable right-click
        document.addEventListener('contextmenu', event => event.preventDefault());
        
        // Disable print screen
        document.addEventListener('keydown', event => {
            if (event.key === "PrintScreen") {
                alert("Screenshots are disabled on this page.");
                event.preventDefault();
            }
        });
        
        // Disable text selection
        document.addEventListener('selectstart', event => event.preventDefault());
    </script>
</body>
</html>