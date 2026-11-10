<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!$_SESSION["LoginAdmin"]) {
    header('location:../login/login.php');
    exit;
}
require_once "../connection/connection.php";

// Handle Add Pupil
if (isset($_POST['submit'])) {
    $pupil_id = date("Y") . rand(1000, 9999);
    $first_name   = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name    = mysqli_real_escape_string($con, $_POST['last_name']);
    $other_name   = mysqli_real_escape_string($con, $_POST['other_name']);
    $DOB          = $_POST['DOB'];
    $gender       = $_POST['gender'];
    $address      = mysqli_real_escape_string($con, $_POST['address']);
    $year         = $_POST['year'];
    $class_id     = $_POST['class_id'];
    $password     = $_POST['password'];
    $role         = $_POST['role'];
    $RegDate      = date("Y-m-d");

    // Profile image upload
    $profile_image = "";
    if (!empty($_FILES['profile_image']['name'])) {
        $profile_image = $_FILES['profile_image']['name'];
        $tmp_name      = $_FILES['profile_image']['tmp_name'];
        $upload_dir    = "../uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        move_uploaded_file($tmp_name, $upload_dir . basename($profile_image));
    }

    $stmt = $con->prepare("INSERT INTO Pupils_tb 
        (pupil_id, first_name, other_name, last_name, DOB, gender, RegDate, address, year, class_id, profile_image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssssss",
        $pupil_id, $first_name, $other_name, $last_name, $DOB, $gender, $RegDate,
        $address, $year, $class_id, $profile_image
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Pupil added successfully! ID: $pupil_id";
    } else {
        $_SESSION['error_message'] = "Error inserting pupil: " . $stmt->error;
    }
    $stmt->close();
    header("Location: pupils.php");
    exit;
}

// Handle Delete Pupil
if (isset($_GET['delete'])) {
    $pupil_id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM Pupils_tb WHERE pupil_id=?");
    $stmt->bind_param("s", $pupil_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Pupil deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting pupil: " . $stmt->error;
    }
    $stmt->close();
    header("Location: pupils.php");
    exit;
}

 $query = "SELECT 
    p.pupil_id,
    p.first_name,
    p.last_name,
    p.other_name,
    p.DOB,
    p.gender,
    p.RegDate,
    p.address,
    p.year,
    p.profile_image,
    c.class_name,
    s.stream_name
FROM pupils_tb p
LEFT JOIN classes c ON p.class_id = c.class_id
LEFT JOIN streams s ON c.stream_id = s.stream_id
ORDER BY p.pupil_id ASC";

$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Pupils</title>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Custom CSS -->
<style>
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

/* Improved Table Styles for Better Visibility */
.table-responsive {
    border-radius: 8px;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    margin-bottom: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table th {
    background-color: #f8f9fa;
    color: var(--dark);
    font-weight: 600;
    padding: 12px 8px;
    border-bottom: 2px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    padding: 10px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: rgba(74, 111, 220, 0.05);
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

/* Action buttons */
.action-buttons .btn {
    padding: 4px 8px;
    margin: 2px;
}

.action-buttons .btn-sm {
    font-size: 0.85rem;
}

/* Sortable headers */
th.sortable {
    cursor: pointer;
    position: relative;
    padding-right: 25px !important;
}

th.sortable::after {
    font-family: "Font Awesome 6 Free";
    content: "\f0dc";
    font-weight: 900;
    font-size: 12px;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    opacity: 0.7;
}

th.sortable:hover::after {
    opacity: 1;
    color: var(--primary);
}

th.sortable.asc::after {
    content: "\f0de";
    color: var(--primary);
    opacity: 1;
}

th.sortable.desc::after {
    content: "\f0dd";
    color: var(--primary);
    opacity: 1;
}

/* Improved table cell visibility */
.table td b {
    font-weight: 600;
    color: #495057;
}

.table td .text-muted {
    font-size: 0.85rem;
}

/* Column widths for better visibility */
.table colgroup col:nth-child(1) { width: 5%; }    /* # */
.table colgroup col:nth-child(2) { width: 10%; }   /* PUPIL ID */
.table colgroup col:nth-child(3) { width: 25%; }   /* NAME */
.table colgroup col:nth-child(4) { width: 10%; }   /* CLASS */
.table colgroup col:nth-child(5) { width: 8%; }    /* GENDER */
.table colgroup col:nth-child(6) { width: 15%; }   /* ADDRESS */
.table colgroup col:nth-child(7) { width: 7%; }    /* YEAR */
.table colgroup col:nth-child(8) { width: 10%; }   /* DATE OF BIRTH */
.table colgroup col:nth-child(9) { width: 10%; }   /* ACTIONS */

/* Pagination styling */
.pagination-controls {
    background: white;
    padding: 15px;
    border-top: 1px solid #eee;
    border-radius: 0 0 10px 10px;
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
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
}

.page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
}

/* Custom modal styling */
.modal-header {
    background-color: var(--primary);
    color: white;
}

/* Search box styling */
.search-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.search-box {
    position: relative;
    flex: 1;
}

.search-box input {
    padding-left: 40px;
    border-radius: 25px;
    height: 40px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.clear-search {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    display: none;
}

.clear-search:hover {
    color: var(--secondary);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .table th, .table td {
        padding: 8px 6px;
        font-size: 0.9rem;
    }
    
    .action-buttons .btn {
        padding: 3px 6px;
        font-size: 0.8rem;
    }
    
    .card-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .card-tools {
        width: 100%;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px;
    }
    
    .search-container {
        flex-direction: column;
        width: 100%;
    }
    
    .search-box {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 15px;
    }
    
    .table-responsive {
        margin: 0 -10px;
        width: calc(100% + 20px);
    }
    
    .table th, .table td {
        padding: 6px 4px;
        font-size: 0.85rem;
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
    }
    
    .action-buttons .btn {
        flex: 1;
        min-width: 60px;
    }
}

/* Zebra striping for better readability */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Fixed height for better scrolling */
.table-container {
    max-height: 600px;
    overflow-y: auto;
}

/* Ensure text doesn't overflow */
.table td, .table th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

/* Make NAME column wrap on mobile */
@media (max-width: 768px) {
    .table td:nth-child(3), /* NAME column */
    .table td:nth-child(6) { /* ADDRESS column */
        white-space: normal;
        word-break: break-word;
    }
}
</style>
</head>
<body>

<?php include('../Main/Main-sidebar.php') ?>

<main role="main" class="col-xl-10 col-lg-9 col-md-8 ml-sm-auto px-md-4 mb-2 w-100">
    <br><br>
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1 class="page-title"><i class="fas fa-user-graduate me-2"></i>Manage Pupils</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPupilModal">
            <i class="fas fa-plus me-2"></i>Add Pupil
        </button>
    </div>

    <div class="col-lg-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>All Pupils Records</span>
                <div class="card-tools d-flex align-items-center gap-2">
                    <select class="form-control form-control-sm" style="width: auto;" id="classFilter">
                        <option value="">All Classes</option>
                        <?php
                        $classes = $con->query("SELECT * FROM classes");
                        while($c = $classes->fetch_assoc()){
                            echo "<option value='{$c['class_name']}'>{$c['class_name']}</option>";
                        }
                        ?>
                    </select>
                    <select class="form-control form-control-sm" style="width: auto;" id="genderFilter">
                        <option value="">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Search Box -->
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID, class, address...">
                        <button class="clear-search" id="clearSearch" title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="mr-2 mb-0">Show:</label>
                        <select class="form-control form-control-sm" style="width: auto;" id="pageSize">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                
                <!-- Pagination Controls Top -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span id="pageInfo" class="text-muted">entries per page</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary mr-2" id="firstPage" disabled>
                            <i class="fas fa-fast-backward"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary mr-2" id="prevPage" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="currentPageInfo" class="mr-2">Page 1 of 1</span>
                        <button class="btn btn-sm btn-outline-primary mr-2" id="nextPage" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" id="lastPage" disabled>
                            <i class="fas fa-fast-forward"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="list">
                            <colgroup>
                                <col width="5%">
                                <col width="10%">
                                <col width="15%">
                                <col width="10%">
                                <col width="8%">
                                <col width="15%">
                                <col width="7%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="sortable">PUPIL ID</th>
                                    <th class="sortable">NAME</th>
                                    <th class="sortable">CLASS</th>
                                    <th class="sortable">GENDER</th>
                                    <th class="sortable">ADDRESS</th>
                                    <th class="sortable">YEAR</th>
                                    <th class="sortable">DATE OF BIRTH</th>
                                    <th class="text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php 
                                $i = 1;
                                if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): 
                                        // Concatenate full name
                                        $full_name = trim(ucwords($row['first_name']) . ' ' . 
                                                        ucwords($row['other_name']) . ' ' . 
                                                        ucwords($row['last_name']));
                                        $full_name = preg_replace('/\s+/', ' ', $full_name); // Remove extra spaces
                                    ?>
                                    <tr class="data-row" 
                                        data-pupil-id="<?= $row['pupil_id']; ?>"
                                        data-name="<?= htmlspecialchars($full_name); ?>"
                                        data-class="<?= htmlspecialchars($row['class_name'] . ' - ' . $row['stream_name']); ?>"
                                        data-gender="<?= htmlspecialchars($row['gender']); ?>"
                                        data-address="<?= htmlspecialchars($row['address']); ?>"
                                        data-year="<?= $row['year']; ?>"
                                        data-dob="<?= !empty($row['DOB']) ? date('Y-m-d', strtotime($row['DOB'])) : ''; ?>"
                                        data-display-dob="<?= !empty($row['DOB']) ? date('d-m-Y', strtotime($row['DOB'])) : 'N/A'; ?>">
                                        <td class="text-center"><b><?= $i++; ?></b></td>
                                        <td class="text-center"><b><?= $row['pupil_id']; ?></b></td>
                                        <td><b><?= $full_name; ?></b></td>
                                       <td class="text-center">
                                            <b><?= ucwords(htmlspecialchars($row['class_name'] . ' - ' . $row['stream_name'])); ?></b>
                                        </td>

                                        <td class="text-center"><b><?= ucwords($row['gender']); ?></b></td>
                                        <td><b><?= ucwords($row['address']); ?></b></td>
                                        <td class="text-center"><b><?= $row['year']; ?></b></td>
                                        <td class="text-center"><b><?= !empty($row['DOB']) ? date('d-m-Y', strtotime($row['DOB'])) : 'N/A'; ?></b></td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <a class="btn btn-info btn-sm" href="pupils_profile.php?pupil_id=<?= $row['pupil_id']; ?>" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-success btn-sm" href="edit_pupils.php?pupil_id=<?= $row['pupil_id']; ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['pupil_id']; ?>" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr id="noRecordsRow">
                                        <td colspan="9" class="text-center py-4">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Controls Bottom -->
                <div class="pagination-controls">
                    <div class="d-flex justify-content-between align-items-center">
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
    </div>
</main>

<!-- Add Pupil Modal -->
<div class="modal fade" id="addPupilModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Pupil</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="pupils.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name:</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Last Name:</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Other Name:</label>
                            <input type="text" name="other_name" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>DOB:</label>
                            <input type="date" name="DOB" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender:</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Address:</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Year:</label>
                            <input type="number" name="year" class="form-control" value="<?= date('Y'); ?>" min="2000" max="<?= date('Y') + 5; ?>" required>
                        </div>
                       <div class="col-md-6 mb-3">
    <label>Class:</label>
    <select name="class_id" class="form-control" required>
        <option value="">Select Class</option>
        <?php
        $class_query = $con->query("
            SELECT 
                c.class_id,
                c.class_name,
                s.stream_name
            FROM classes c
            INNER JOIN streams s ON s.stream_id = c.stream_id
            ORDER BY c.class_name
        ");

        while ($class = $class_query->fetch_assoc()) {
            echo "<option value='{$class['class_id']}'>
                    {$class['class_name']} - {$class['stream_name']}
                </option>";
        }
        ?>
    </select>
</div>
                                                        <div class="col-md-6 mb-3">
                            <label>Profile Image:</label>
                            <input type="file" name="profile_image" class="form-control-file">
                        </div>
                    </div>
                    <input type="hidden" name="password" value="student123*">
                    <input type="hidden" name="role" value="Student">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Pagination variables
    let currentPage = 1;
    let pageSize = parseInt(document.getElementById('pageSize').value) || 10;
    let totalPages = 1;
    let filteredRows = [];
    let currentSortColumn = null;
    let currentSortDirection = 'asc';
    let searchTerm = '';
    
    // DOM Elements
    const pageSizeSelect = document.getElementById('pageSize');
    const pageInfo = document.getElementById('pageInfo');
    const currentPageInfo = document.getElementById('currentPageInfo');
    const totalInfo = document.getElementById('totalInfo');
    const pageNumbers = document.getElementById('pageNumbers');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    
    // Pagination buttons
    const firstPageBtn = document.getElementById('firstPage');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const lastPageBtn = document.getElementById('lastPage');
    const firstPageBottomBtn = document.getElementById('firstPageBottom');
    const prevPageBottomBtn = document.getElementById('prevPageBottom');
    const nextPageBottomBtn = document.getElementById('nextPageBottom');
    const lastPageBottomBtn = document.getElementById('lastPageBottom');
    
    // Filter elements
    const classFilter = document.getElementById('classFilter');
    const genderFilter = document.getElementById('genderFilter');
    
    // Initialize
    initializeTable();
    
    function initializeTable() {
        // Get all data rows
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        filteredRows = allRows;
        
        // Set initial page size
        pageSize = parseInt(pageSizeSelect.value);
        
        // Apply filters and pagination
        applyFilters();
        
        // Update page info
        updatePaginationInfo();
        renderPageNumbers();
    }
    
    // Event Listeners for Filters
    if (classFilter) {
        classFilter.addEventListener('change', applyFilters);
    }
    
    if (genderFilter) {
        genderFilter.addEventListener('change', applyFilters);
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
    
    // Search functionality
    if (searchInput) {
        // Real-time search with debouncing
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchTerm = this.value.trim().toLowerCase();
                currentPage = 1;
                applyFilters();
                updatePaginationInfo();
                renderPageNumbers();
                updateClearButton();
            }, 300);
        });
        
        // Clear search button
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchTerm = '';
                currentPage = 1;
                applyFilters();
                updatePaginationInfo();
                renderPageNumbers();
                updateClearButton();
                searchInput.focus();
            });
        }
    }
    
    function updateClearButton() {
        if (clearSearchBtn) {
            if (searchTerm.length > 0) {
                clearSearchBtn.style.display = 'block';
            } else {
                clearSearchBtn.style.display = 'none';
            }
        }
    }
    
    // Pagination button events
    firstPageBtn.addEventListener('click', () => goToPage(1));
    prevPageBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextPageBtn.addEventListener('click', () => goToPage(currentPage + 1));
    lastPageBtn.addEventListener('click', () => goToPage(totalPages));
    
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
        // Scroll to top of table
        document.querySelector('.table-container').scrollTop = 0;
    }
    
    // Apply filters
    function applyFilters() {
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        const selectedClass = classFilter ? classFilter.value : '';
        const selectedGender = genderFilter ? genderFilter.value : '';
        
        // Apply filters
        filteredRows = allRows.filter(row => {
            const rowClass = row.getAttribute('data-class');
            const rowGender = row.getAttribute('data-gender');
            
            // Class filter
            if (selectedClass && rowClass !== selectedClass) {
                return false;
            }
            
            // Gender filter
            if (selectedGender && rowGender !== selectedGender) {
                return false;
            }
            
            // Search filter
            if (searchTerm.length > 0) {
                const rowData = {
                    pupilId: row.getAttribute('data-pupil-id').toLowerCase(),
                    name: row.getAttribute('data-name').toLowerCase(),
                    class: row.getAttribute('data-class').toLowerCase(),
                    address: row.getAttribute('data-address').toLowerCase(),
                    year: row.getAttribute('data-year').toString(),
                    dob: row.getAttribute('data-display-dob').toLowerCase()
                };
                
                // Check if search term matches any field
                const matchesSearch = Object.values(rowData).some(value => 
                    value.includes(searchTerm)
                );
                
                if (!matchesSearch) {
                    return false;
                }
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
        });
        
        // Show rows for current page
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, filteredRows.length);
        
        for (let i = startIndex; i < endIndex; i++) {
            if (filteredRows[i]) {
                filteredRows[i].style.display = '';
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
        currentPageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        totalInfo.textContent = `Showing ${filteredRows.length > 0 ? startIndex : 0} to ${endIndex} of ${filteredRows.length} entries`;
        
        // Enable/disable pagination buttons
        const isFirstPage = currentPage === 1;
        const isLastPage = currentPage === totalPages;
        
        [firstPageBtn, prevPageBtn, firstPageBottomBtn, prevPageBottomBtn].forEach(btn => {
            btn.disabled = isFirstPage;
        });
        
        [nextPageBtn, lastPageBtn, nextPageBottomBtn, lastPageBottomBtn].forEach(btn => {
            btn.disabled = isLastPage;
        });
    }
    
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
            case 1: // PUPIL ID
                return row.getAttribute('data-pupil-id');
            case 2: // NAME
                return row.getAttribute('data-name').toLowerCase();
            case 3: // CLASS
                return row.getAttribute('data-class').toLowerCase();
            case 4: // GENDER
                return row.getAttribute('data-gender');
            case 5: // ADDRESS
                return row.getAttribute('data-address').toLowerCase();
            case 6: // YEAR
                return parseInt(row.getAttribute('data-year')) || 0;
            case 7: // DATE OF BIRTH
                const dob = row.getAttribute('data-dob');
                return dob ? new Date(dob) : null;
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
        
        if (a instanceof Date && b instanceof Date) {
            return a - b;
        }
        
        return a.toString().localeCompare(b.toString(), undefined, {numeric: true});
    }
    
    // Delete Confirmation
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const pupilId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pupils.php?delete=' + pupilId;
                }
            });
        });
    });
    
    // Show SweetAlert messages
    <?php if(isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $_SESSION['success_message']; ?>',
            timer: 3000
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= $_SESSION['error_message']; ?>'
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    // Focus search input on Ctrl+F
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Clear search on Escape key
        if (e.key === 'Escape' && searchInput && searchInput.value.length > 0) {
            searchInput.value = '';
            searchTerm = '';
            currentPage = 1;
            applyFilters();
            updatePaginationInfo();
            renderPageNumbers();
            updateClearButton();
        }
    });
    
    // Initial update of clear button
    updateClearButton();
});
</script>

</body>
</html>