<?php
require_once "../connection/connection.php";

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];

// Check token
$stmt = $con->prepare("SELECT email, token_expiry FROM login WHERE reset_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    die("Invalid or expired token");
}

// Check expiry
if (strtotime($user['token_expiry']) < time()) {
    die("Token expired");
}

$email = $user['email'];

// Handle password update
if (isset($_POST['reset'])) {

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt2 = $con->prepare("UPDATE login SET password=?, reset_token=NULL, token_expiry=NULL WHERE email=?");
        $stmt2->bind_param("ss", $hashedPassword, $email);
        $stmt2->execute();

        $success = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
</head>
<body>

<h2>Reset Password</h2>

<?php
if (isset($error)) echo "<p style='color:red;'>$error</p>";
if (isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required><br><br>
    <input type="password" name="confirm" placeholder="Confirm Password" required><br><br>
    <button type="submit" name="reset">Reset Password</button>
</form>

</body>
</html>