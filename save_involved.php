


<!DOCTYPE html>
<html>
    <head>
        <style>
            .box{
                margin-top: 10%;
                 background: #ffffff;
  border-radius: 10px;
  padding: 30px 35px;
  width: 90%;
  max-width: 480px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  text-align: center;
            }
 .btn {
  display: inline-block;
  text-decoration: none;
  width: 25%;
  padding: 14px;
  background: #2e7d32;
  color: white;
  font-size: 18px;
  border-radius: 6px;
  text-align: center;
  transition: 0.3s;
}
.btn:hover {
  background: #1b5e20;
}

        </style>
    </head>
</html>
<?php
require 'db.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitization
    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        die("Please fill in all required fields.");
    }

    // Save to DB securely
    $stmt = $conn->prepare("INSERT INTO get_involved (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {
        // --- PHPMailer Setup ---
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bhosaleriya71@gmail.com'; // ✅ Remove spaces
            $mail->Password = 'tktxuaqtgwwtvtub'; // ✅ Replace this with App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email to admin
            $mail->setFrom('bhosaleriya71@gmail.com', 'Planet365');
            $mail->addAddress($email,$name);
            $mail->isHTML(true);
            $mail->Subject = "Thankyou for getting involved with Planet365!";
            $mail->Body = "<p>Dear $name,</p>
            <p>Thank you for joining Planet365! 🌿</p>
            <p>We have received your message:</p>
            <p><strong>$message</strong></p>
            <p>Our team will reach out to you soon.</p>
            <p>-Planet365 Team</p>
            ";


          $mail->AltBody="Dear $name,\n\nThankyou for joining planet365!\n\nMessage:$message\n\nOur team will reach out to you soon.\n\n— Planet365 Team";
          $mail->send();
          echo "<div class='box'>";
echo "<p>Thank you! A confirmation email has been sent to $email.</p>";
echo "<a href='index.html' class='btn'>Go back</a>";
echo "</div>";


    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
}

?>


          
      


