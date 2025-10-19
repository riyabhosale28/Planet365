

<?php
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
        header("Location: dashboard.php");
            exit();
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password) VALUES ('$name', '$email', '$pass')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful! You can now login.";
            $type = "success"; // for styling
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $type = "error";
        }
    }
}
?>
