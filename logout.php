<?php
session_start();

// Destroy all session variables
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logged Out - Planet365</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #e8f5e9;
      color: #2e7d32;
      text-align: center;
      padding: 50px;
    }
    .logout-box {
      background: #ffffff;
      border: 2px solid #2e7d32;
      border-radius: 12px;
      padding: 30px;
      width: 400px;
      margin: auto;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    h1 {
      color: #1b5e20;
    }
    p {
      font-size: 18px;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      text-decoration: none;
      background: #2e7d32;
      color: white;
      border-radius: 6px;
      transition: background 0.3s;
    }
    a:hover {
      background: #1b5e20;
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h1>🌿 You’ve been logged out</h1>
    <p>Thank you for visiting <b>Planet365</b>.  
       Stay eco-friendly and see you again soon! 🌍</p>
    <a href="login.html">🔑 Login Again</a>
    <a href="index.html">🏠 Go to Home</a>
  </div>
</body>
</html>
