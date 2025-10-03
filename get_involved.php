<?php


session_start();
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
$success="";

if($_SERVER['REQUEST_METHOD']=="POST"){
    $name=$conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql="INSERT INTO get_involved (name, email, message) VALUES ('$name', '$email', '$message')";
    if($conn->query($sql) === TRUE) {
     
$mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bhosaleriya28@gmail.com';    // your email
            $mail->Password = 'Riya28292004@';          // your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@planet365.com', 'Planet365');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Thank you for getting involved with Planet365!';
            $mail->Body = "
                <h3>Hi $name,</h3>
                <p>Thank you for signing up to get involved with Planet365. We'll contact you soon with ways you can help make the planet greener! 🌱</p>
                <p><strong>Your message:</strong> $message</p>
                <p>🌍 Best regards,<br>Planet365 Team</p>
            ";
            $mail->send();

            // Send notification to admin
            $adminMail = new PHPMailer(true);
            $adminMail->isSMTP();
            $adminMail->Host = 'smtp.gmail.com';
            $adminMail->SMTPAuth = true;
            $adminMail->Username = ''; // your email
            $adminMail->Password = 'yourpassword';
            $adminMail->SMTPSecure = 'tls';
            $adminMail->Port = 587;
            $adminMail->setFrom('no-reply@planet365.com', 'Planet365');
            $adminMail->addAddress('admin@example.com', 'Admin');
            $adminMail->Subject = 'New Get Involved Submission';
            $adminMail->Body = "New user signed up to get involved: <br>Name: $name<br>Email: $email<br>Message: $message";
            $adminMail->isHTML(true);
            $adminMail->send();

            $success = "Thank you, $name! A confirmation email has been sent to $email 🌱";

        } catch (Exception $e) {
            $success = "Error sending email: {$mail->ErrorInfo}";
        }

    } else {
        $success = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Get Involved - Planet365</title>
<style>
    body { font-family: Arial, sans-serif; background: #e8f5e9; text-align: center; padding: 50px; }
    input, textarea { padding: 10px; width: 300px; margin: 10px 0; border-radius: 6px; border: 1px solid #2e7d32; }
    button { padding: 10px 20px; background: #2e7d32; color: white; border: none; border-radius: 6px; cursor: pointer; }
    button:hover { background: #1b5e20; }
    .success { background: #c8e6c9; color: #1b5e20; padding: 10px; border-radius: 6px; margin-bottom: 20px; display: inline-block; }
</style>
</head>
<body>

<h2>Get Involved with Planet365</h2>

<?php if($success != ""): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" action="get_involved.php">
    <input type="text" name="name" placeholder="Your Name" value="<?php echo $_SESSION['username'] ?? ''; ?>" required><br>
    <input type="email" name="email" placeholder="Your Email" value="<?php echo $_SESSION['email'] ?? ''; ?>" required><br>
    <textarea name="message" placeholder="How do you want to get involved?" required></textarea><br>
    <button type="submit">Submit</button>
</form>

</body>
</html>

       
        