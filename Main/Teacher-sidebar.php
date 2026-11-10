<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link  rel="stylesheet" href="../css/sidebar.css"
   
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
            
          <a href="../Teacher/Teacher-Index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-chalkboard-teacher"></i>
                <span class="nav-text">Teachers</span>
               
            </a>
            <div class="dropdown-container">
                <a href="../Teacher/instructor.php">Teacher information</a>
            </div>
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-users"></i>
                <span class="nav-text">Class</span>
                 
            </a>
            <div class="dropdown-container">
                <a href="../Teacher/class.php">Class</a>
            </div>
            
            <a href="javascript:void(0)" class="dropdown-toggle">
                <i class="fas fa-book"></i>
                <span class="nav-text">Exams</span>
                
            </a>
            <div class="dropdown-container">
                <a href="../Teacher/exam.php">Enter Marks</a>
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
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        let currentlyOpenDropdown = null;
        
        // Toggle sidebar on mobile
        function toggleMobileSidebar() {
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        // Dropdown functionality with auto-close
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                
                // Close previously open dropdown
                if (currentlyOpenDropdown && currentlyOpenDropdown !== dropdown) {
                    currentlyOpenDropdown.classList.remove('show');
                }
                
                // Toggle current dropdown
                dropdown.classList.toggle('show');
                
                // Update currently open dropdown
                if (dropdown.classList.contains('show')) {
                    currentlyOpenDropdown = dropdown;
                } else {
                    currentlyOpenDropdown = null;
                }
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-toggle') && !event.target.closest('.dropdown-container')) {
                if (currentlyOpenDropdown) {
                    currentlyOpenDropdown.classList.remove('show');
                    currentlyOpenDropdown = null;
                }
            }
        });
        
        // Event listeners
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileSidebar);
        }
        
        if (overlay) {
            overlay.addEventListener('click', toggleMobileSidebar);
        }
        
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