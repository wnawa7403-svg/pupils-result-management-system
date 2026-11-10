<?php
session_start();
require_once "../connection/connection.php";
 
// PHPMailer
require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';
require '../libs/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if (isset($_POST['submit'])) {

    // 🔹 Step 1: Get user input
    $email = trim($_POST['email']);

    // 🔹 Step 2: Check in database
    $stmt = $con->prepare("SELECT email FROM login WHERE LOWER(email)=LOWER(?) LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        $dbEmail = $user['email'];

        // 🔹 Step 3: Generate token
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // 🔹 Step 4: Save token
        $stmt2 = $con->prepare("UPDATE login SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt2->bind_param("sss", $token, $expiry, $dbEmail);
        $stmt2->execute();
        $stmt2->close();

        // 🔹 Step 5: Send email
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Fix localhost SSL
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // 🔹 Sender (system)
            $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);

            // 🔹 Recipient (user)
            $mail->addAddress($dbEmail);

            // 🔹 Optional: reply goes to user
            $mail->addReplyTo($dbEmail);

            // Reset link
            $resetLink = "http://localhost/yourproject/reset_password.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h3>Password Reset</h3>
                <p>You requested a password reset.</p>
                <p>Click below (valid 30 minutes):</p>
                <a href='$resetLink'>$resetLink</a>
            ";

            $mail->send();
            $message = "Check your email to reset your password!";

        } catch (Exception $e) {
            $message = "Mailer Error: " . $mail->ErrorInfo;
        }

    } else {
        // Security-safe response
        $message = "If the email exists, a reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
</head>
<body>

<h2>Forgot Password</h2>

<?php if (!empty($message)) echo "<p>$message</p>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required><br><br>
    <button type="submit" name="submit">Send Reset Link</button>
</form>

</body>
</html>