<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Gradexel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/home-page.css">
    <style> 
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
    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradexel
            </a>
            
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
            
            <ul class="nav-menu">
                <li><a href="./index.php">Home</a></li>
                <li><a href="./about.php" class="active">About Us</a></li>
                <li><a href="./service.php">Services</a></li>
                <li><a href="./contact.php">Contact</a></li>
                <li><a href="./Login/login.php">Login</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-white">
            <h1 style="font-size: 48px; color: white; font-weight: bold;">Our Services</h1>
            <p style="font-size: 24px; color: white;">Comprehensive Solutions for Educational Institutions</p>
        </div>
    </section>

    <!-- Main Services -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Core Services</h2>
            <p class="section-subtitle">Everything you need to manage student results efficiently</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Student Data Management</h3>
                    <p>Comprehensive student profiles with academic history, personal information, and performance tracking in one secure platform.</p>
                    <ul style="text-align: left; margin-top: 15px;">
                        <li>Student profile management</li>
                        <li>Academic records storage</li>
                        <li>Attendance tracking</li>
                        <li>Progress monitoring</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Result Processing</h3>
                    <p>Automated grade calculation, result compilation, and transcript generation with customizable grading systems.</p>
                    <ul style="text-align: left; margin-top: 15px;">
                        <li>Automated grade calculation</li>
                        <li>Custom grading systems</li>
                        <li>Transcript generation</li>
                        <li>Report card creation</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-analytics"></i>
                    </div>
                    <h3>Performance Analytics</h3>
                    <p>Advanced analytics and visual reports to identify trends, track progress, and make data-driven decisions.</p>
                    <ul style="text-align: left; margin-top: 15px;">
                        <li>Performance dashboards</li>
                        <li>Trend analysis</li>
                        <li>Comparative reports</li>
                        <li>Predictive analytics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Services -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">Additional Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Automated Notifications</h3>
                    <p>Real-time alerts for missing grades, below-average performances, and important academic deadlines.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Access</h3>
                    <p>Access student data and generate reports on-the-go with our responsive mobile-friendly platform.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Data Security</h3>
                    <p>Enterprise-level security with encrypted data storage and role-based access control to protect sensitive information.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Service Packages</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <h3>Basic</h3>
                        <div class="price" style="font-size: 2rem; color: var(--primary); margin: 20px 0;">Free</div>
                        <ul style="text-align: left;">
                            <li>Up to 50 students</li>
                            <li>Basic result processing</li>
                            <li>Standard reports</li>
                            <li>Email support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center" style="border: 2px solid var(--primary);">
                        <h3>Professional</h3>
                        <div class="price" style="font-size: 2rem; color: var(--primary); margin: 20px 0;">$99/year</div>
                        <ul style="text-align: left;">
                            <li>Up to 500 students</li>
                            <li>Advanced analytics</li>
                            <li>Custom reports</li>
                            <li>Priority support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <h3>Enterprise</h3>
                        <div class="price" style="font-size: 2rem; color: var(--primary); margin: 20px 0;">Custom</div>
                        <ul style="text-align: left;">
                            <li>Unlimited students</li>
                            <li>Full feature access</li>
                            <li>API integration</li>
                            <li>Dedicated support</li>
                        </ul>
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
                                    <li><a href="./index.php">Home</a></li>
                            <li><a href="./about.php" class="active">About Us</a></li>
                            <li><a href="./service.php">Services</a></li>
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