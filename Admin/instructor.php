<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["LoginAdmin"])) {
    header('location:../login/login.php');
    exit;
}

require_once "../connection/connection.php";

// -----------------------------
// Handle Add Teacher
// -----------------------------
if (isset($_POST['btn_save'])) {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $tcz_number  = trim($_POST['tcz_number']);
    $gender      = $_POST['gender'];
    $role        = $_POST['role'];

    // ✅ Hash the password with bcrypt
    $password    = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $teacher_id  = date("Y") . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format!";
        header("Location: instructor.php");
        exit;
    }

    // Check if email already exists
    $check = $con->prepare("SELECT 1 FROM teachers_tb WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['error_message'] = "Email already exists!";
        $check->close();
        header("Location: instructor.php");
        exit;
    }
    $check->close();

    // Insert into teachers table
    $stmt = $con->prepare("INSERT INTO teachers_tb (teacher_id, first_name, middle_name, last_name, email, Teaching_number, gender, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $teacher_id, $first_name, $middle_name, $last_name, $email, $tcz_number, $gender, $role);
    $stmt->execute();
    $stmt->close();

    // Insert into login table with hashed password (bcrypt ~60 chars)
    $stmt2 = $con->prepare("INSERT INTO login (user_id, Password, Role, account, failed_attempts) 
                            VALUES (?, ?, ?, 'Activate', 0)");
    $stmt2->bind_param("sss", $email, $password, $role);
    $stmt2->execute();
    $stmt2->close();

    $_SESSION['success_message'] = "Teacher added successfully!";
    header("Location: instructor.php");
    exit;
}

// -----------------------------
// Handle Delete Teacher
// -----------------------------
if (isset($_GET['delete'])) {
    $teacher_id = $_GET['delete'];

    // Get teacher email first
    $stmt = $con->prepare("SELECT email FROM teachers_tb WHERE teacher_id=?");
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $stmt->bind_result($teacher_email);
    $stmt->fetch();
    $stmt->close();

    if ($teacher_email) {
        // Delete teacher record
        $stmt = $con->prepare("DELETE FROM teachers_tb WHERE teacher_id=?");
        $stmt->bind_param("s", $teacher_id);
        if ($stmt->execute()) {
            $stmt->close();

            // Delete login by email (hashed password stays secure)
            $stmt2 = $con->prepare("DELETE FROM login WHERE user_id=?");
            $stmt2->bind_param("s", $teacher_email);
            $stmt2->execute();
            $stmt2->close();

            $_SESSION['success_message'] = "Teacher deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting teacher: " . $stmt->error;
        }
    } else {
        $_SESSION['error_message'] = "Teacher not found!";
    }

    header("Location: instructor.php");
    exit;
}
 
$teachers = $con->query("SELECT * FROM teachers_tb WHERE role != 'Admin' ORDER BY teacher_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Teachers</title> <!-- Changed title -->

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Custom CSS -->
<style>
    th.sortable {
        cursor: pointer;
    }

    th.sortable::after {
        font-family: "Font Awesome 6 Free";
        content: "\f0dc"; /* sort */
        font-weight: 900;
        font-size: 12px;
        margin-left: 6px;
        color: #6c757d;
    }

    :root {
        --primary: #4A6FDC;
        --secondary: #FF6B6B;
        --accent: #4CAF50;
        --light: #F5F7FF;
        --dark: #2C3E50;
        --text: #333;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        color: var(--text);
    }

    .page-header {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: var(--shadow);
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    .card-header {
        background: white;
        border-bottom: 1px solid #eee;
        font-weight: 600;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background-color: #3a5fc9;
        border-color: #3a5fc9;
    }

    .table th {
        background-color: var(--light);
        color: var(--dark);
        font-weight: 600;
    }

    .action-buttons .btn {
        padding: 4px 8px;
        margin-right: 5px;
    }

    .badge-success {
        background-color: var(--accent);
    }

    .badge-warning {
        background-color: #ffc107;
    }

    .badge-danger {
        background-color: var(--secondary);
    }

    /* Custom modal styling */
    .modal-header {
        background-color: var(--primary);
        color: white;
    }

    .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eee;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
    }

    /* Sortable header styling */
    th.sortable {
        position: relative;
        cursor: pointer;
        padding-right: 20px !important;
    }

    th.sortable::after {
        content: "↕";
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #6c757d;
        opacity: 0.7;
        transition: all 0.2s;
    }

    th.sortable:hover::after {
        opacity: 1;
        color: #495057;
    }

    th.sortable.asc::after {
        content: "↑";
        color: #007bff;
        opacity: 1;
        font-weight: bold;
    }

    th.sortable.desc::after {
        content: "↓";
        color: #007bff;
        opacity: 1;
        font-weight: bold;
    }

    /* Table styling */
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    /* Button group spacing */
    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    /* Advanced Search Panel */
    #advancedSearchPanel {
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 0;
    }

    #advancedSearchPanel .form-group {
        margin-bottom: 0;
    }

    #advancedSearchPanel label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    /* Pagination styling */
    .page-link {
        cursor: pointer;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .page-numbers {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-numbers .page-item {
        margin: 0 2px;
    }

    .page-numbers .page-link {
        padding: 4px 10px;
        font-size: 14px;
    }

    /* Hidden rows */
    .hidden-row {
        display: none;
    }
</style>
</head>
<body>
 
<?php include('../Main/Main-sidebar.php') ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1 class="page-title"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Teachers</h1> <!-- Changed icon and text -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addTeacherModal"> <!-- Fixed modal target -->
            <i class="fas fa-plus me-2"></i>Add Teacher
        </button>
    </div>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Teachers Records</span> <!-- Changed text -->
            <div class="card-tools d-flex align-items-center gap-2">
                <!-- Removed class filter since teachers don't have classes -->
                <select class="form-control form-control-sm" style="width: auto;" id="genderFilter">
                    <option value="">All Genders</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <select class="form-control form-control-sm" style="width: auto;" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="Teacher">Teacher</option>
                     
                </select>
                <button class="btn btn-sm btn-default btn-flat border-secondary" id="advancedSearchBtn">
                    <i class="fa fa-search"></i> Advanced Search
                </button>
            </div>
        </div>
        
        <!-- Advanced Search Panel (Hidden by Default) -->
        <div class="card-body border-bottom" id="advancedSearchPanel" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchName">Search by Name</label>
                        <input type="text" class="form-control form-control-sm" id="searchName" placeholder="Enter name...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchEmail">Email</label>
                        <input type="email" class="form-control form-control-sm" id="searchEmail" placeholder="Search by email...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchTCZ">TCZ Number</label>
                        <input type="text" class="form-control form-control-sm" id="searchTCZ" placeholder="Search by TCZ number...">
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <button class="btn btn-sm btn-primary" id="applySearch">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn btn-sm btn-secondary" id="resetSearch">
                        <i class="fa fa-times"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Pagination Controls Top -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <label class="mr-2 mb-0">Show:</label>
                    <select class="form-control form-control-sm" style="width: auto;" id="pageSize">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ml-2 mb-0" id="pageInfo">entries</span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="25%">
                        <col width="10%">
                        <col width="8%">
                        <col width="15%">
                        <col width="7%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="sortable">ID</th>
                            <th class="sortable">First Name</th>
                            <th class="sortable">Middle Name</th>
                            <th class="sortable">Last Name</th>
                            <th class="sortable">Gender</th>
                            <th class="sortable">Email</th>
                            <th class="sortable">TCZ Number</th>
                            <th class="sortable">Role</th>
                            <th class="text-center sortable">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php 
                        if ($teachers && $teachers->num_rows > 0): 
                            $i = 1;
                            while ($row = $teachers->fetch_assoc()): 
                                $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                                $full_name = preg_replace('/\s+/', ' ', $full_name);
                        ?>
                        <tr class="data-row" 
                            data-id="<?= $row['teacher_id']; ?>"
                            data-firstname="<?= htmlspecialchars($row['first_name']); ?>"
                            data-middlename="<?= htmlspecialchars($row['middle_name']); ?>"
                            data-lastname="<?= htmlspecialchars($row['last_name']); ?>"
                            data-fullname="<?= htmlspecialchars($full_name); ?>"
                            data-gender="<?= htmlspecialchars($row['gender']); ?>"
                            data-email="<?= htmlspecialchars($row['email']); ?>"
                            data-tcz="<?= htmlspecialchars($row['Teaching_number']); ?>"
                            data-role="<?= htmlspecialchars($row['role']); ?>">
                            <td><?= $row['teacher_id']; ?></td>
                            <td><?= $row['first_name']; ?></td>
                            <td><?= $row['middle_name']; ?></td>
                            <td><?= $row['last_name']; ?></td>
                            <td><?= $row['gender']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['Teaching_number']; ?></td>
                            <td><?= $row['role']; ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                     <a class="btn btn-info btn-sm" href="teachers_profile.php?teacher_id=<?= $row['teacher_id']; ?>" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                    <a class="btn btn-success btn-sm mr-1" href="edit_teacher.php?teacher_id=<?= $row['teacher_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['teacher_id']; ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            $i++;
                            endwhile; 
                        else: ?>
                            <tr id="noRecordsRow">
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-2"></i> <!-- Changed icon -->
                                    <p class="text-muted">No teachers found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls Bottom -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span id="totalInfo">Showing 0 to 0 of 0 entries</span>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-primary mr-2" id="firstPageBottom" disabled>
                        <i class="fas fa-fast-backward"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary mr-2" id="prevPageBottom" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div id="pageNumbers" class="d-flex mr-2"></div>
                    <button class="btn btn-sm btn-outline-primary mr-2" id="nextPageBottom" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" id="lastPageBottom" disabled>
                        <i class="fas fa-fast-forward"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript for Pagination and Filtering
document.addEventListener('DOMContentLoaded', function() {
    // Pagination variables
    let currentPage = 1;
    let pageSize = 10;
    let totalPages = 1;
    let filteredRows = [];
    let currentSortColumn = null;
    let currentSortDirection = 'asc';
    
    // DOM Elements
    const pageSizeSelect = document.getElementById('pageSize');
    const pageInfo = document.getElementById('pageInfo');
    const totalInfo = document.getElementById('totalInfo');
    const pageNumbers = document.getElementById('pageNumbers');
    
    // Pagination buttons
    const firstPageBottomBtn = document.getElementById('firstPageBottom');
    const prevPageBottomBtn = document.getElementById('prevPageBottom');
    const nextPageBottomBtn = document.getElementById('nextPageBottom');
    const lastPageBottomBtn = document.getElementById('lastPageBottom');
    
    // Filter elements
    const genderFilter = document.getElementById('genderFilter');
    const roleFilter = document.getElementById('roleFilter');
    const advancedSearchBtn = document.getElementById('advancedSearchBtn');
    const advancedSearchPanel = document.getElementById('advancedSearchPanel');
    const applySearchBtn = document.getElementById('applySearch');
    const resetSearchBtn = document.getElementById('resetSearch');
    const searchName = document.getElementById('searchName');
    const searchEmail = document.getElementById('searchEmail');
    const searchTCZ = document.getElementById('searchTCZ');
    
    // Delete button handlers
    const deleteButtons = document.querySelectorAll('.deleteBtn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const teacherId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this teacher?')) {
                window.location.href = 'instructor.php?delete=' + teacherId; // Fixed URL
            }
        });
    });
    
    // Initialize
    initializeTable();
    
    function initializeTable() {
        // Get all data rows
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        filteredRows = allRows;
        
        // Apply filters and pagination
        applyFilters();
        
        // Set initial page size
        pageSize = parseInt(pageSizeSelect.value);
        
        // Update page info
        updatePaginationInfo();
        renderPageNumbers();
    }
    
    // Event Listeners for Filters
    if (genderFilter) {
        genderFilter.addEventListener('change', applyFilters);
    }
    
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }
    
    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', function() {
            pageSize = parseInt(this.value);
            currentPage = 1;
            applyFilters();
            updatePaginationInfo();
            renderPageNumbers();
        });
    }
    
    // Pagination button events
    firstPageBottomBtn.addEventListener('click', () => goToPage(1));
    prevPageBottomBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextPageBottomBtn.addEventListener('click', () => goToPage(currentPage + 1));
    lastPageBottomBtn.addEventListener('click', () => goToPage(totalPages));
    
    function goToPage(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        applyFilters();
        updatePaginationInfo();
        renderPageNumbers();
    }
    
    // Advanced Search Toggle
    if (advancedSearchBtn) {
        advancedSearchBtn.addEventListener('click', function() {
            if (advancedSearchPanel.style.display === 'none') {
                advancedSearchPanel.style.display = 'block';
                this.innerHTML = '<i class="fa fa-search-minus"></i> Hide Search';
                this.classList.remove('border-secondary');
                this.classList.add('border-primary');
            } else {
                advancedSearchPanel.style.display = 'none';
                this.innerHTML = '<i class="fa fa-search"></i> Advanced Search';
                this.classList.remove('border-primary');
                this.classList.add('border-secondary');
            }
        });
    }
    
    // Apply Advanced Search
    if (applySearchBtn) {
        applySearchBtn.addEventListener('click', function() {
            currentPage = 1;
            applyFilters();
            updatePaginationInfo();
            renderPageNumbers();
        });
    }
    
    // Reset Search
    if (resetSearchBtn) {
        resetSearchBtn.addEventListener('click', function() {
            searchName.value = '';
            searchEmail.value = '';
            searchTCZ.value = '';
            currentPage = 1;
            applyFilters();
            updatePaginationInfo();
            renderPageNumbers();
        });
    }
    
    // Apply all filters
    function applyFilters() {
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        const selectedGender = genderFilter ? genderFilter.value : '';
        const selectedRole = roleFilter ? roleFilter.value : '';
        
        // Apply filters
        filteredRows = allRows.filter(row => {
            const rowGender = row.getAttribute('data-gender');
            const rowRole = row.getAttribute('data-role');
            const rowFullName = row.getAttribute('data-fullname').toLowerCase();
            const rowEmail = row.getAttribute('data-email').toLowerCase();
            const rowTCZ = row.getAttribute('data-tcz').toLowerCase();
            
            // Gender filter
            if (selectedGender && rowGender !== selectedGender) {
                return false;
            }
            
            // Role filter
            if (selectedRole && rowRole !== selectedRole) {
                return false;
            }
            
            // Advanced search filters
            const nameSearch = searchName ? searchName.value.toLowerCase() : '';
            const emailSearch = searchEmail ? searchEmail.value.toLowerCase() : '';
            const tczSearch = searchTCZ ? searchTCZ.value.toLowerCase() : '';
            
            // Name filter
            if (nameSearch && !rowFullName.includes(nameSearch)) {
                return false;
            }
            
            // Email filter
            if (emailSearch && !rowEmail.includes(emailSearch)) {
                return false;
            }
            
            // TCZ filter
            if (tczSearch && !rowTCZ.includes(tczSearch)) {
                return false;
            }
            
            return true;
        });
        
        // Apply sorting if any
        if (currentSortColumn !== null) {
            sortFilteredRows();
        }
        
        // Calculate total pages
        totalPages = Math.ceil(filteredRows.length / pageSize);
        
        // Hide all rows first
        allRows.forEach(row => {
            row.style.display = 'none';
            row.classList.add('hidden-row');
        });
        
        // Show rows for current page
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, filteredRows.length);
        
        for (let i = startIndex; i < endIndex; i++) {
            if (filteredRows[i]) {
                filteredRows[i].style.display = '';
                filteredRows[i].classList.remove('hidden-row');
            }
        }
        
        // Update "No records" message
        const noRecordsRow = document.getElementById('noRecordsRow');
        if (noRecordsRow) {
            if (filteredRows.length === 0) {
                noRecordsRow.style.display = '';
            } else {
                noRecordsRow.style.display = 'none';
            }
        }
    }
    
    // Update pagination information
    function updatePaginationInfo() {
        const startIndex = (currentPage - 1) * pageSize + 1;
        const endIndex = Math.min(startIndex + pageSize - 1, filteredRows.length);
        
        // Update page info text
        totalInfo.textContent = `Showing ${filteredRows.length > 0 ? startIndex : 0} to ${endIndex} of ${filteredRows.length} entries`;
        
        // Enable/disable pagination buttons
        firstPageBottomBtn.disabled = currentPage === 1;
        prevPageBottomBtn.disabled = currentPage === 1;
        nextPageBottomBtn.disabled = currentPage === totalPages;
        lastPageBottomBtn.disabled = currentPage === totalPages;
        
        // Update button styles
        updateButtonStyle(firstPageBottomBtn, currentPage === 1);
        updateButtonStyle(prevPageBottomBtn, currentPage === 1);
        updateButtonStyle(nextPageBottomBtn, currentPage === totalPages);
        updateButtonStyle(lastPageBottomBtn, currentPage === totalPages);
    }
    
    function updateButtonStyle(button, disabled) {
        if (disabled) {
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-outline-secondary');
        } else {
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-outline-primary');
        }
    }
    
    // Render page number buttons
    function renderPageNumbers() {
        pageNumbers.innerHTML = '';
        
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        // Create page number buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('div');
            pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
            
            const pageLink = document.createElement('button');
            pageLink.className = 'page-link btn btn-sm btn-outline-primary';
            pageLink.textContent = i;
            pageLink.addEventListener('click', () => goToPage(i));
            
            pageItem.appendChild(pageLink);
            pageNumbers.appendChild(pageItem);
        }
    }
    
    // Sorting functionality
    const sortableHeaders = document.querySelectorAll('th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = Array.from(this.parentNode.children).indexOf(this);
            
            // Update sort direction
            if (currentSortColumn === columnIndex) {
                currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = columnIndex;
                currentSortDirection = 'asc';
            }
            
            // Update visual indicators
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            this.classList.add(currentSortDirection);
            
            // Sort and update display
            sortFilteredRows();
            applyFilters();
            updatePaginationInfo();
        });
    });
    
    function sortFilteredRows() {
        if (currentSortColumn === null) return;
        
        filteredRows.sort((a, b) => {
            const aValue = getSortValue(a, currentSortColumn);
            const bValue = getSortValue(b, currentSortColumn);
            
            if (currentSortDirection === 'asc') {
                return compareValues(aValue, bValue);
            } else {
                return compareValues(bValue, aValue);
            }
        });
    }
    
    function getSortValue(row, columnIndex) {
        switch(columnIndex) {
            case 0: // Teacher ID
                return row.getAttribute('data-id');
            case 1: // First Name
                return row.getAttribute('data-firstname').toLowerCase();
            case 2: // Middle Name
                return row.getAttribute('data-middlename').toLowerCase();
            case 3: // Last Name
                return row.getAttribute('data-lastname').toLowerCase();
            case 4: // Gender
                return row.getAttribute('data-gender');
            case 5: // Email
                return row.getAttribute('data-email').toLowerCase();
            case 6: // TCZ Number
                return row.getAttribute('data-tcz');
            case 7: // Role
                return row.getAttribute('data-role');
            default:
                return '';
        }
    }
    
    function compareValues(a, b) {
        if (a === null || a === '') return 1;
        if (b === null || b === '') return -1;
        
        if (typeof a === 'number' && typeof b === 'number') {
            return a - b;
        }
        
        return a.toString().localeCompare(b.toString(), undefined, {numeric: true});
    }
});
</script>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog"> <!-- This modal now matches the button -->
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Teacher</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addTeacherForm" action="instructor.php"> <!-- Added action -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required 
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    title="Please enter a valid email address">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>TCZ Number</label>
                                <input type="text" name="tcz_number" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="Teacher">Teacher</option>
                                 
                                </select>
                            </div>
                            <input type="hidden" name="password" value="teacher123*">
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="btn_save" class="btn btn-primary">Save Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function(){
        // Show SweetAlert messages
        <?php if(isset($_SESSION['success_message'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= addslashes($_SESSION['success_message']); ?>',
                timer: 3000
            });
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if(isset($_SESSION['error_message'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= addslashes($_SESSION['error_message']); ?>'
            });
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    });
    </script>

</main>
</body>
</html>