<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSS Links -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/Style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <title>MILEMU SECONDARY SCHOOL</title>
    
    <style>
        /* Header Styles */
        .header-back {
            font-family: "Open Sans", sans-serif;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('../images/courses-header.jpg') center/cover;
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        /* Branding */
        .site-title a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .site-title span {
            font-weight: 300;
            font-size: 0.8em;
            display: block;
        }

        /* Navigation Links */
        .navbar-nav .nav-link {
            color: white !important;
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 5px;
            left: 15px;
            background-color: white;
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: calc(100% - 30px);
        }

        /* Mobile Menu */
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: rgba(0,0,0,0.9);
                padding: 15px;
                margin-top: 10px;
                border-radius: 5px;
            }
            
            .navbar-nav {
                gap: 5px;
            }
        }

        /* Active State */
        .nav-item.active .nav-link {
            font-weight: 600;
            color: #fff !important;
        }

        .nav-item.active .nav-link::after {
            width: calc(100% - 30px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark header-back">
        <div class="container-fluid">
            <!-- Brand/Logo -->
            <div class="col-8 col-md-3">
                <div class="site-branding">
                    <h1 class="site-title m-0">
                        <a href="index.php" rel="home">
                            PUPILS<br>
                            <span>MANAGEMENT</span>
                        </a>
                    </h1>
                </div>
            </div>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="academics.php">Announcements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="modal" data-target=".bd-example-modal-lg1">Academics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Login/logout.php">
                            Logout <i class="fa fa-sign-in"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="../bootstrap/js/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    
    <script>
        // Add scroll effect
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.header-back').css('background-color', 'rgba(0,0,0,0.9)');
            } else {
                $('.header-back').css('background-color', 'rgba(0,0,0,0.7)');
            }
        });
    </script>
    
</body>

</html>