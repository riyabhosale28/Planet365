

<?php
/*  
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password

    // Check if user already exists
    $check_sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Email already registered
        echo "<p style='color:red;'>This email is already registered. Please <a href='login.html'>login</a>.</p>";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password) VALUES ('$name', '$email', '$pass')";

        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Registration successful! <a href='login.html'>Login here</a></p>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}
    */

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password

    // Check if user already exists
    $check_sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $message = "This email is already registered. Please login.";
        $type = "error"; // for styling
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password) VALUES ('$name', '$email', '$pass')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful! You can now login.";
            $type = "success"; // for styling
        } else {
            $message = "Error: " . $conn->error;
            $type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <h2>Register for Planet365</h2>
    <?php if(isset($message)): ?>
        <div class="message <?php echo $type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
    <a class="link" href="login.html">Already have an account? Login</a>
</div>
</body>
</html>


?>

