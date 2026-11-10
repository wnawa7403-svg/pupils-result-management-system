<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            transition: margin-left 0.3s ease;
        }
        
        /* Mobile Header (shown on mobile) */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-logo {
            display: flex;
            align-items: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .mobile-logo i {
            margin-right: 10px;
            font-size: 1.5rem;
        }
        
        .mobile-menu-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        /* Sidebar - Visible on both desktop and mobile */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(165deg, var(--dark-color) 0%, #34495e 100%);
            color: white;
            z-index: 1001;
            overflow-y: auto;
            padding-top: 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-content {
            padding: 20px;
        }
        
        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .dropdown-container {
            background: rgba(40, 55, 71, 0.6);
            border-left: 3px solid var(--accent-color);
            padding-left: 20px;
            margin-left: 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .sidebar .dropdown-container.show {
            max-height: 500px;
        }
        
        .sidebar .dropdown-container a {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
        
        .sidebar-footer {
            padding: 20px;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        	.sidebar-footer i.fa-sign-out-alt {
        color: red;
    }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }
        
        /* Main content */
        .main-content {
            margin-left: 280px; /* Same as sidebar width */
            padding: 30px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        /* When sidebar is collapsed */
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .dropdown-container,
        .sidebar.collapsed .fa-chevron-down {
            display: none;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Demo content */
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Toggle button for desktop */
        .sidebar-toggle {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--accent-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1002;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                left: -280px;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            }
            
            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
            
            .mobile-header {
                display: block;
            }
            
            .sidebar-toggle {
                display: none;
            }
            
            .sidebar.collapsed {
                width: 280px;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .sidebar.collapsed .nav-text,
            .sidebar.collapsed .dropdown-container,
            .sidebar.collapsed .fa-chevron-down {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Header (shown on mobile only) -->
    <div class="mobile-header">
        <div class="mobile-header-content">
            <div class="mobile-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>School Portal</span>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuButton">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    
    <!-- Sidebar - Visible on both desktop and mobile -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <div class="logo" style="padding: 0 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px;">
                <div style="display: flex; align-items: center; color: white;">
                    <i class="fas fa-graduation-cap" style="font-size: 28px; margin-right: 10px; color: var(--accent-color);"></i>
                    <div>
                        <div class="logo-text" style="font-size: 20px; font-weight: bold;">SCHOOL</div>
                        <div class="logo-subtext" style="font-size: 12px; color: var(--accent-color);">TEACHER PORTAL</div>
                    </div>
                </div>
            </div>
            <a href="/HOD/hod-index.php">
    <i class="fas fa-tachometer-alt"></i>
    <span class="nav-text">Dashboard</span>
</a>
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-chalkboard-teacher"></i>
                <span class="nav-text">Teachers Info</span>
               
            </a>
            <div class="dropdown-container">
                  <a href="../HOD/teacher.php">Teacher Info</a>
            </div>
              <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-chalkboard-teacher"></i>
              <span class="nav-text">Departmental info</span>
               
            </a>
            <div class="dropdown-container">
                  <a href="../HOD/departmental_info.php">dept info</a>
            </div>
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-users"></i>
                <span class="nav-text">Class</span>
                 
            </a>
            <div class="dropdown-container">
                      <a href="../HOD/class.php">View Class</a>
            </div>
             
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-book"></i>
                <span class="nav-text">Exams</span>
                
            </a>
            <div class="dropdown-container">
                <a href="../HOD/exam.php">Enter Marks</a>
            </div>
             
             
             
             
             
        </div>
        
        <div class="sidebar-footer">
            <a href="../Login/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </div>
    
    </div>

    <script>
        // Elements
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('overlay');
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        // Toggle sidebar on mobile
        function toggleMobileSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        // Toggle sidebar collapse on desktop
        function toggleDesktopSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Change icon
            const icon = sidebarToggle.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-ellipsis-h');
            } else {
                icon.classList.remove('fa-ellipsis-h');
                icon.classList.add('fa-bars');
            }
        }
        
        // Dropdown functionality
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                dropdown.classList.toggle('show');
                
                // Rotate arrow icon
                const icon = this.querySelector('.fa-chevron-down');
                icon.classList.toggle('fa-rotate-180');
            });
        });
        
        // Event listeners
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileSidebar);
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleDesktopSidebar);
        }
        
        overlay.addEventListener('click', toggleMobileSidebar);
        
        // Swipe to close (for touch devices)
        let touchStartX = 0;
        
        sidebar.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        sidebar.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) { // Swipe left to close
                if (window.innerWidth <= 992) { // Only on mobile
                    toggleMobileSidebar();
                }
            }
        });
        
        // Auto-close sidebar on mobile when clicking a link
        const sidebarLinks = sidebar.querySelectorAll('a:not(.dropdown-toggle)');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    toggleMobileSidebar();
                }
            });
        });
    </script>
</body>
</html>