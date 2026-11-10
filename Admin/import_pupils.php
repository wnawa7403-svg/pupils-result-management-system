<?php
session_start();
require_once "../connection/connection.php";

if (isset($_POST['import'])) {
    if (isset($_FILES['csv_file']['tmp_name'])) {
        $file = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($file, "r")) !== FALSE) {
            $firstRow = true;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($firstRow) { $firstRow = false; continue; } // skip header

                // Read CSV data
                $first_name = mysqli_real_escape_string($con, $data[0]);
                $last_name  = mysqli_real_escape_string($con, $data[1]);
                $other_name = mysqli_real_escape_string($con, $data[2]);
                $DOB        = $data[3];
                $gender     = $data[4];
                $address    = mysqli_real_escape_string($con, $data[5]);

                // Set Year and Class ID
                $year = 2025;
                $class_id = 25;

                // Generate a unique pupil_id
                do {
                    $pupil_id = date("Y") . rand(1000, 9999);
                    $check = $con->query("SELECT pupil_id FROM pupils_tb WHERE pupil_id='$pupil_id'");
                } while($check->num_rows > 0);

                $RegDate  = date("Y-m-d");

                // Insert into database
                $stmt = $con->prepare("INSERT INTO pupils_tb 
                    (pupil_id, first_name, other_name, last_name, DOB, gender, RegDate, address, year, class_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssss", 
                    $pupil_id, $first_name, $other_name, $last_name, $DOB, $gender, $RegDate, $address, $year, $class_id);
                $stmt->execute();
            }

            fclose($handle);
            echo "<p style='color:green;'>50 Grade 8 pupils imported successfully! Year=2025, Class ID=25</p>";

        } else {
            echo "<p style='color:red;'>Failed to open CSV file.</p>";
        }
    } else {
        echo "<p style='color:red;'>Please upload a CSV file.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .form-container {
    max-width: 500px;
    margin: 2rem auto;
    padding: 2rem;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', system-ui, sans-serif;
}

.form-container label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #2d3748;
    font-size: 1.1rem;
}

.file-input-wrapper {
    position: relative;
    margin-bottom: 1.5rem;
}

.file-input-wrapper input[type="file"] {
    width: 100%;
    padding: 1rem;
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    background: #f7fafc;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 1rem;
}

.file-input-wrapper input[type="file"]:hover {
    border-color: #4299e1;
    background: #edf2f7;
}

.file-input-wrapper input[type="file"]::file-selector-button {
    padding: 0.75rem 1.5rem;
    margin-right: 1rem;
    border: none;
    border-radius: 6px;
    background: #4299e1;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}

.file-input-wrapper input[type="file"]::file-selector-button:hover {
    background: #3182ce;
}

.import-button {
    width: 100%;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.import-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.import-button:active {
    transform: translateY(0);
}

.import-button:disabled {
    background: #a0aec0;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* File name display */
.file-name {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #718096;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 600px) {
    .form-container {
        margin: 1rem;
        padding: 1.5rem;
    }
    
    .file-input-wrapper input[type="file"] {
        padding: 0.75rem;
    }
    
    .import-button {
        padding: 0.875rem 1.5rem;
    }
}

/* Loading state */
.import-button.loading {
    position: relative;
    color: transparent;
}

.import-button.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
    </style>
</head>
<body>
    <div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <label>Select CSV file:</label>
        <div class="file-input-wrapper">
            <input type="file" name="csv_file" accept=".csv" required id="csvFile">
            <div class="file-name" id="fileName">No file chosen</div>
        </div>
        <button type="submit" name="import" class="import-button">Import Pupils</button>
    </form>
</div>

<script>
// Optional JavaScript to show selected file name
document.getElementById('csvFile').addEventListener('change', function(e) {
    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
    document.getElementById('fileName').textContent = fileName;
});
</script>
</body>
</html>
 