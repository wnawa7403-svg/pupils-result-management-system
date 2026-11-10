<?php
session_start();
require_once "../connection/connection.php";

/* =========================
   CSRF TOKEN GENERATION
========================= */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* =========================
   LOGIN PROCESS
========================= */
if (isset($_POST['login'])) {

    // ✅ CSRF VALIDATION
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("❌ Invalid request");
    }

    // Regenerate token after use
    unset($_SESSION['csrf_token']);

    // Small delay (anti-brute force)
    sleep(1);

    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user
    $stmt = $con->prepare("SELECT email, Password, Role, account, failed_attempts FROM login WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // 🚫 Check deactivated account
        if ($row['account'] === 'Deactivate') {
            $_SESSION['error_message'] = 'Your account is deactivated. Contact admin.';
            header('Location: login.php');
            exit;
        }

        // 🔐 Verify password
        if (password_verify($password, $row['Password'])) {

            // Reset failed attempts
            $stmt = $con->prepare("UPDATE login SET failed_attempts=0 WHERE email=?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();

            // Secure session
            session_regenerate_id(true);

            $_SESSION['Role'] = $row["Role"];

            // Redirect by role
            switch ($row["Role"]) {

                case "Admin":
                    $_SESSION['LoginAdmin'] = $row["email"];
                    header('Location: ../Admin/admin-index.php');
                    break;

                case "Teacher":
                    $_SESSION['LoginTeacher'] = $row["email"];
                    header('Location: ../Teacher/Teacher-Index.php');
                    break;

                case "Class Teacher":
                    $_SESSION['LoginClassTeacher'] = $row["email"];
                    header('Location: ../ClassTeacher/class_teacher-index.php');
                    break;

                case "Hod":
                    $_SESSION['LoginHod'] = $row["email"];
                    header('Location: ../HOD/hod-index.php');
                    break;

                default:
                    $_SESSION['error_message'] = "Unknown user role!";
                    header("Location: login.php");
                    break;
            }
            exit;

        } else {
            // ❌ Wrong password
            $failed = $row['failed_attempts'] + 1;

            if ($failed >= 3) {
                $stmt = $con->prepare("UPDATE login SET failed_attempts=?, account='Deactivate' WHERE email=?");
                $stmt->bind_param("is", $failed, $username);
                $stmt->execute();
                $stmt->close();

                $_SESSION['error_message'] = 'Account deactivated after 3 failed attempts. Contact admin.';
            } else {
                $stmt = $con->prepare("UPDATE login SET failed_attempts=? WHERE email=?");
                $stmt->bind_param("is", $failed, $username);
                $stmt->execute();
                $stmt->close();

                $_SESSION['error_message'] = "Invalid username or password. Attempt $failed of 3.";
            }

            header('Location: login.php');
            exit;
        }

    } else {
        // ❌ Prevent username enumeration
        $_SESSION['error_message'] = 'Invalid username or password.';
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradexel - Student Result Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/lo.css">
     <link rel="stylesheet" href="../css/login.css">
    
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

<header>
    <div class="container header-container">
        <a href="#" class="logo">
            <i class="fas fa-graduation-cap"></i> Gradexel
        </a>
        <div class="mobile-menu-btn"><i class="fas fa-bars"></i></div>
        <ul class="nav-menu">
            <li><a href="/index.php">Home</a></li>
            <li><a href="/about.php">About Us</a></li>
            <li><a href="/contact.php">Contact</a></li>
            <li><a href="/service.php">Services</a></li>

        </ul>
    </div>
</header>
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
<div class="login-container">
    <div class="login-card">
        <div class="login-header text-center">
          
            <p>Login</p>
        </div>
        <div class="login-body">
        <form action="login.php" method="POST" autocomplete="off">
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger text-center">'.$_SESSION['error_message'].'</div>';
        unset($_SESSION['error_message']);
    }

    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success text-center">'.$_SESSION['success_message'].'</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <!-- Username/Email -->
    <div class="form-group mb-3">
        <label for="email">Username/Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" id="email" name="email" class="form-control" placeholder="Enter your username or email" required>
        </div>
    </div>

    <!-- Password -->
    <div class="form-group mb-3">
        <label for="password">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <span class="password-toggle" onclick="togglePassword()">
                <i class="fas fa-eye" id="eye-icon"></i>
            </span>
        </div>
    </div>

    <!-- Forgot Password -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <a href="forgot_password.php">Forgot password?</a>
        </div>
    </div>

    <!-- Login Button -->
    <button type="submit" name="login" class="btn btn-login mb-3">
        <i class="fas fa-sign-in-alt me-2"></i>LOGIN
    </button>
</form>
        </div>
    </div>
</div>

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
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Mobile menu toggle
document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
    document.querySelector('.nav-menu').classList.toggle('active');
});

// Focus on email field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
    
    
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
