<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
<body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "planet365";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderName  = trim($_POST['name']);
    $senderEmail = trim($_POST['email']);
    $messageBody = trim($_POST['message']);

   
 $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $senderName, $senderEmail, $messageBody);
        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
 
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bhosaleriya71@gmail.com';           // Your Gmail
        $mail->Password   = 'tktxuaqtgwwtvtub';      // Your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('bhosaleriya71@gmail.com','Planet365'); // Your email & name
        $mail->addAddress($senderEmail, $senderName); // Where messages go

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for contacting Planet365';
        $mail->Body    = "Hello {$senderEmail},<br><br>
                          Thank you for reaching out to Planet365. We have received your message:<br><br>
                          <strong>Message:</strong> {$messageBody}<br><br>
                          We will get back to you shortly!<br><br>
                          Regards,<br>Planet365 Team";
        $mail->AltBody = "Hello {$senderName},\n\nThank you for reaching out to Planet365.\n\nMessage: {$messageBody}\n\nWe will get back to you shortly!\n\nRegards, Planet365 Team";
        $mail->send();
        echo "<div class='box'>";
        echo '<p>Thank you! Your message has been sent.</p>';
        echo "<a href='index.html' class='btn'>Go back</a>";
        echo "</div>";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
}
?>
 


 