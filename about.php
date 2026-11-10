<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Gradexel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/home-page.css">
     
    <style>
        /* ========================================
           GLOBAL STYLES & VARIABLES
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --background-color: #ffffff;
            --default-color: #212529;
            --heading-color: #001973;
            --accent-color: #0d42ff;
            --surface-color: #ffffff;
            --contrast-color: #ffffff;
            --primary: #1a365d;
            --secondary: #e53e3e;
            --accent: #2d3748;
            --light: #f7fafc;
            --white: #ffffff;
            --gray: #a0aec0;
            --gold: goldenrod;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --dark: #1a365d;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--default-color);
            line-height: 1.6;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ========================================
           BUTTONS
        ======================================== */
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #3a5fc9;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background-color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #ff5252;
        }

        .btn-accent {
            background-color: var(--accent);
        }

        .btn-accent:hover {
            background-color: #3e8e41;
        }

        /* ========================================
           SECTIONS
        ======================================== */
        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: clamp(2rem, 5vw, 2.5rem);
            color: var(--dark);
            line-height: 1.2;
        }

        .section-subtitle {
            text-align: center;
            margin-bottom: 50px;
            font-size: clamp(1rem, 3vw, 1.2rem);
            color: #666;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* ========================================
           HEADER / NAVBAR
        ======================================== */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: var(--primary);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: clamp(1.5rem, 4vw, 1.8rem);
            font-weight: 700;
            color: var(--light);
            text-decoration: none;
        }

        .logo i {
            margin-right: 10px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-menu li {
            margin: 0;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--light);
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 1rem;
            padding: 5px 0;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--gold);
        }

        .mobile-menu-btn {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--light);
            background: none;
            border: none;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .mobile-menu-btn:hover {
            color: var(--gold);
        }

        /* ========================================
           HERO SECTION
        ======================================== */
        .hero {
            background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8ZWR1Y2F0aW9ufGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            text-align: center;
            padding: clamp(100px, 15vw, 150px) 0 clamp(60px, 10vw, 100px);
            margin-top: 60px;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-content {
            color: white;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .hero p {
            font-size: clamp(1.1rem, 3vw, 1.3rem);
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.95;
        }

        /* ========================================
           FEATURES SECTION
        ======================================== */
        .features {
            background-color: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
            background: white;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
            background: var(--light);
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px;
        }

        .feature-card h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: var(--dark);
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* ========================================
           HOW IT WORKS
        ======================================== */
        .how-it-works {
            background-color: var(--light);
        }

        .steps {
            display: flex;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto;
            gap: 20px;
        }

        .step {
            text-align: center;
            flex: 1;
            padding: 0 20px;
            position: relative;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .step h3 {
            margin-bottom: 10px;
            color: var(--dark);
        }

        .step p {
            color: #666;
        }

        .step:not(:last-child):after {
            content: "";
            position: absolute;
            top: 25px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: var(--primary);
            z-index: 1;
        }

        /* ========================================
           TESTIMONIALS
        ======================================== */
        .testimonials {
            background-color: white;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            background-color: var(--light);
            text-align: left;
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            position: relative;
            padding-left: 30px;
            color: #555;
            line-height: 1.7;
        }

        .testimonial-text:before {
            content: '"';
            font-size: 3rem;
            position: absolute;
            left: 0;
            top: -10px;
            color: var(--primary);
            opacity: 0.5;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .author-info h4 {
            margin-bottom: 5px;
            color: var(--dark);
        }

        .author-info p {
            color: #888;
            font-size: 0.9rem;
        }

        /* ========================================
           CTA SECTION
        ======================================== */
        .cta {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            text-align: center;
            padding: 80px 0;
        }

        .cta h2 {
            font-size: clamp(2rem, 5vw, 2.5rem);
            margin-bottom: 20px;
        }

        .cta p {
            max-width: 600px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* ========================================
           FOOTER
        ======================================== */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background-color: var(--gold);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #ccc;
        }

        /* ========================================
           RESPONSIVE DESIGN
        ======================================== */
        @media (max-width: 992px) {
            .steps {
                flex-direction: column;
                gap: 40px;
            }
            
            .step:not(:last-child):after {
                display: none;
            }
            
            section {
                padding: 60px 0;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--primary);
                flex-direction: column;
                padding: 20px;
                gap: 15px;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }
            
            .nav-menu.active {
                display: flex;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .features-grid,
            .testimonial-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .hero {
                margin-top: 50px;
                min-height: 400px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 15px;
            }
            
            .hero {
                margin-top: 40px;
                min-height: 350px;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        /* ========================================
           ANIMATIONS
        ======================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-card, .step, .testimonial-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.4s; }
        .step:nth-child(2) { animation-delay: 0.2s; }
        .step:nth-child(3) { animation-delay: 0.4s; }

        /* ========================================
           PRELOADER STYLES
        ======================================== */
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

        @keyframes spin-outer {
            to { transform: rotate(360deg); }
        }

        @keyframes spin-inner {
            to { transform: rotate(-360deg); }
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
    </style>
</head>

<body>
    <!-- ===== PRELOADER ===== -->
    <div id="preloader" class="preloader-overlay">
        <div>
            <div class="dual-ring-spinner" role="status" aria-label="Loading Gradexel">
                <div class="ring-outer"></div>
                <div class="ring-inner"></div>
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
            <a href="{{ url('/') }}" class="logo">
                <i class="fas fa-graduation-cap"></i>
                Gradexel
            </a>
            
            <button class="mobile-menu-btn" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <ul class="nav-menu">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
                <li><a href="{{ route('services') }}">Services</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    
                @endauth
            </ul>
        </div>
    </header>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-white">
            <h1 style="font-size: 48px; color: white; font-weight: bold;">About Gradexel</h1>
            <p style="font-size: 24px; color: white;">Empowering Educators, Transforming Education</p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Our Mission</h3>
                        <p>To revolutionize educational management by providing innovative, user-friendly solutions that streamline administrative tasks and enhance the learning experience for students and educators alike.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Our Vision</h3>
                        <p>To become the leading educational technology platform that transforms how schools manage academic data, making education more accessible, efficient, and data-driven across Africa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">Our Story</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p style="font-size: 18px; line-height: 1.8;">
                        Gradexel was born from a simple observation: educators were spending more time on administrative tasks than on teaching. Founded in 2023, our platform was developed in collaboration with teachers, administrators, and educational experts to create a solution that truly meets the needs of modern educational institutions.
                    </p>
                    <p style="font-size: 18px; line-height: 1.8;">
                        We understand the unique challenges faced by schools in managing student data, generating reports, and tracking academic progress. Our team is committed to continuous improvement and innovation in the educational technology space.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                         alt="Our Team" class="dashboard-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Our Values</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>Constantly evolving to provide cutting-edge solutions that address the changing needs of education.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Collaboration</h3>
                    <p>Working closely with educators to develop features that truly enhance teaching and learning experiences.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Reliability</h3>
                    <p>Providing a stable, secure platform that educators can depend on for their critical academic data.</p>
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
                            <li><a href=" ./index.php" class="active">Home</a></li>
                        <li><a href="./about.php" class="active">about Us</a></li>
                        <li><a href="./contact.php" class="active">contact</a></li>
                        <li><a href="./service.php" class="active">services</a></li>
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