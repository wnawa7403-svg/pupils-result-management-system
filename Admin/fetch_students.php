<?php
require_once "../connection/connection.php";

$class_id = intval($_GET['class_id'] ?? 0);
if(!$class_id) exit('<option value="">-- Select Student --</option>');

$students = $con->query("SELECT pupil_id, first_name, last_name FROM pupils_tb WHERE class_id=$class_id ORDER BY last_name ASC");

echo '<option value="">-- Select Student --</option>';
while($s = $students->fetch_assoc()){
    echo "<option value='{$s['pupil_id']}'>{$s['first_name']} {$s['last_name']}</option>";
}
?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
