<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>School Portal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background:#f5f7fb; overflow-x:hidden; }

/* ===== MOBILE HEADER ===== */
.mobile-header { display:none; position:fixed; top:0; left:0; right:0; height:60px; background:#1a365d; color:white; z-index:1001; padding:0 20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.mobile-header-content { display:flex; justify-content:space-between; align-items:center; height:100%; }
.mobile-logo { display:flex; align-items:center; font-weight:600; font-size:1.2rem; }
.mobile-logo i { margin-right:10px; font-size:1.5rem; color:#4895ef; }
.mobile-menu-btn { background:transparent; border:none; color:white; font-size:1.5rem; cursor:pointer; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:5px; transition:0.3s; }
.mobile-menu-btn:hover { background: rgba(255,255,255,0.1); }

/* ===== SIDEBAR ===== */
.sidebar { position:fixed; top:0; left:0; width:250px; height:100vh; background:#1a365d; color:white; z-index:1000; transition:transform 0.3s ease; overflow-y:auto; }
.sidebar-content { padding:20px 15px; }
.sidebar-logo { padding:0 10px 20px; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.1); }
.logo-container { display:flex; align-items:center; }
.logo-icon { font-size:28px; margin-right:10px; color:#4895ef; }
.logo-main { font-size:18px; font-weight:bold; }
.logo-sub { font-size:11px; color:#4895ef; margin-top:2px; }
.sidebar a { display:flex; align-items:center; color:rgba(255,255,255,0.85); text-decoration:none; padding:12px 15px; margin-bottom:5px; border-radius:5px; transition:all 0.3s; }
.sidebar a:hover { background:rgba(255,255,255,0.1); color:white; }
.sidebar a.active { background: rgba(72,149,239,0.2); color:white; border-left:4px solid #4895ef; }
.sidebar a i { width:20px; margin-right:10px; font-size:16px; }

.dropdown-toggle { cursor:pointer; position:relative; }
.dropdown-toggle::after { content:'\f078'; font-family:'Font Awesome 6 Free'; font-weight:900; position:absolute; right:15px; font-size:12px; transition:0.3s; }
.dropdown-toggle.active::after { transform: rotate(180deg); }

.dropdown-container { background: rgba(40,55,71,0.6); margin-left:10px; border-left:2px solid #4895ef; max-height:0; overflow:hidden; transition:max-height 0.3s ease; }
.dropdown-container.show { max-height:500px; }
.dropdown-container a { padding:10px 15px 10px 25px; font-size:14px; }

.sidebar-footer { padding:20px 15px; border-top:1px solid rgba(255,255,255,0.1); }
.sidebar-footer a:hover { background: rgba(231,76,60,0.2); }
.sidebar-footer i { color:#e74c3c; }

.main-content { margin-left:250px; padding:20px; min-height:100vh; transition: margin-left 0.3s ease; }

/* Overlay */
.overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:999; }
.overlay.active { display:block; }

/* Responsive */
@media (max-width:992px) {
    .mobile-header { display:block; }
    .sidebar { transform:translateX(-100%); }
    .sidebar.active { transform:translateX(0); }
    .main-content { margin-left:0; padding-top:80px; }
}

/* Scrollbar */
.sidebar::-webkit-scrollbar { width:5px; }
.sidebar::-webkit-scrollbar-track { background:rgba(255,255,255,0.1); }
.sidebar::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.3); border-radius:3px; }
</style>
</head>
<body>

<!-- Mobile Header -->
<div class="mobile-header">
    <div class="mobile-header-content">
        <div class="mobile-logo">
            <i class="fas fa-graduation-cap"></i> SCHOOL
        </div>
        <button id="mobileMenuBtn" class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</div>

<!-- Overlay -->
<div id="overlay" class="overlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="logo-container">
                <i class="fas fa-graduation-cap logo-icon"></i>
                <div>
                    <div class="logo-main">SCHOOL</div>
                    <div class="logo-sub">TEACHER PORTAL</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <a href="admin-index.php" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a>

        <!-- Teachers Dropdown -->
        <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-chalkboard-teacher"></i>Teachers</a>
        <div class="dropdown-container">
            <a href="../Admin/instructor.php"><i class="fas fa-user-plus"></i>Add Teacher</a>
         
        </div>

        <!-- Pupils Dropdown -->
        <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-user-graduate"></i>Pupils</a>
        <div class="dropdown-container">
            <a href="../Admin/pupils.php"><i class="fas fa-user-plus"></i>Add Student</a>
    
           <a href="../Admin/generate_qrcode.php"><i class="fas fa-user-plus"></i>	QR code</a>
            <a href="../Admin/attendance.php"><i class="fas fa-calendar-check"></i>Attendance</a>
        </div>

        <!-- Academics Dropdown -->
        <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-graduation-cap"></i>Academics</a>
        <div class="dropdown-container">
            <a href="../Admin/class.php"><i class="fas fa-school"></i>Classes</a>
            <a href="../Admin/add_stream.php"><i class="fas fa-book"></i>Stream</a>
            <a href="../Admin/test.php"><i class="fas fa-file-alt"></i>Tests</a>
        </div>

        <!-- Subjects Dropdown -->
        <a href="javascript:void(0)" class="dropdown-toggle"><i class="fas fa-book"></i>Subjects</a>
        <div class="dropdown-container">
            <a href="../Admin/add_subjects.php"><i class="fas fa-book-open"></i>Subjects</a>
            <a href="../Admin/forms.php"><i class="fas fa-file-alt"></i>Forms</a>
            <a href="../Admin/assign_teacher.php"><i class="fas fa-chalkboard-teacher"></i>Assign Teacher</a>
        </div>
 
<a href="javascript:void(0)" class="dropdown-toggle">
    <i class="fas fa-chart-line"></i>
    <span class="nav-text">Reports & Results</span>
</a>
<div class="dropdown-container">
    
    <a href="../Admin/report_form.php">
        <i class="fas fa-file-pdf"></i>
        Report Book
    </a>
    <a href="../Admin/pupils_list.php">
        <i class="fas fa-user-circle"></i>
        Individual Results
    </a>
</div>

<a href="javascript:void(0)" class="dropdown-toggle">
    <i class="fas fa-chart-bar"></i>
    <span class="nav-text">Result Analytics</span>
</a>
<div class="dropdown-container">
    <a href="../Admin/summary.php">
        <i class="fas fa-chart-pie"></i>
        Summary Report
    </a>
    <a href="../Admin/generate_to_pdf.php">
        <i class="fas fa-download"></i>
        Export Analytics
    </a>
</div>

<a href="javascript:void(0)" class="dropdown-toggle">
    <i class="fas fa-users-cog"></i>
    <span class="nav-text">Manage Accounts</span>
</a>
<div class="dropdown-container">
   
    <a href="../Admin/insert_admin.php">
        <i class="fas fa-user-plus"></i>
        Add Admin
    </a>
    
    <a href="../Admin/manage_accounts.php">
        <i class="fas fa-user-cog"></i>
        User Accounts
    </a>
    <a href="../Admin/reset_password.php">
        <i class="fas fa-key"></i>
        Password Reset
    </a>
    <a href="../Admin/account_status.php">
        <i class="fas fa-user-check"></i>
        User Account Status
    </a>
</div>
    </div> <!-- end sidebar-content -->

    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="../Login/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</div>



<script>
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

// Toggle sidebar
function toggleSidebar() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
}

// Close sidebar
function closeSidebar() {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

mobileMenuBtn.addEventListener('click', e => { e.stopPropagation(); toggleSidebar(); });
overlay.addEventListener('click', closeSidebar);

// Dropdown toggle
dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', e => {
        e.preventDefault();
        e.stopPropagation();
        toggle.classList.toggle('active');
        const dropdown = toggle.nextElementSibling;
        if(dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        } else {
            document.querySelectorAll('.dropdown-container.show').forEach(d => d.classList.remove('show'));
            document.querySelectorAll('.dropdown-toggle.active').forEach(t => { if(t!==toggle) t.classList.remove('active'); });
            dropdown.classList.add('show');
        }
    });
});

// Close sidebar on link click (mobile)


// Close dropdowns when clicking outside
document.addEventListener('click', e => {
    if(!e.target.closest('.dropdown-toggle')) {
        dropdownToggles.forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.dropdown-container.show').forEach(d => d.classList.remove('show'));
    }
});

// Close on Escape
document.addEventListener('keydown', e => { if(e.key==='Escape') closeSidebar(); });

// Auto-close on desktop resize
window.addEventListener('resize', () => { if(window.innerWidth>992 && sidebar.classList.contains('active')) closeSidebar(); });
</script>

</body>
</html>