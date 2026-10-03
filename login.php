<?php
/*  
session_start();
include 'db.php';
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email=$conn->real_escape_string($_POST['email']);
    $pass=$_POST['password'];
    $sql="SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['userid'];
            $_SESSION['user_name'] = $row['username'];
            header("Location: welcome.php");
            exit();
        } else {
            echo "<p style='color:red;'>Invalid password.";
        }
    } else {
        echo "<p style='color:red;'>User not found.";
    }
}else{
    header("Location: login.html");
    exit();
}
*/
?>


/*
session_start();
include 'db.php';
include 'lib/rewards.php'; 
$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);
 $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close(); 
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
             $_SESSION['userid'] = $row['userid'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
            $type = "error";
        }
    } else {
        $message = "User not found.";
        $type = "error";
    }

    $today = date("Y-m-d");
$lastLogin = $user['last_login']; 

if ($lastLogin == null) {
    $streak = 1;
} else if ($lastLogin == date("Y-m-d", strtotime("-1 day"))) {
    $streak = $user['streak_count'] + 1;
} else if ($lastLogin == $today) {
    $streak = $user['streak_count']; // same day
} else {
    $streak = 1; // reset
}

$stmt2 = $conn->prepare("UPDATE users SET last_login=?, streak_count=? WHERE userid=?");
$stmt2->bind_param("sii", $today, $streak, $userid);
$stmt2->execute();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🌱Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <h2>Login to Planet365</h2>
    <?php if($message != ""): ?>
        <div class="message <?php echo $type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a class="link" href="register.html">Don't have an account? Register</a>
</div>
</body>
</html>
*/

<?php
session_start();
include 'db.php';
include 'lib/rewards.php'; // <-- IMPORTANT

$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $pass  = $_POST['password'];

    // SAFE SQL (prepared statement)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close(); 

    if ($row) {
        if (password_verify($pass, $row['password'])) {

            // LOGIN SUCCESS
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];

            // ================================================
            //      ⭐ DAILY LOGIN STREAK SYSTEM ⭐
            // ================================================
            $today = date("Y-m-d");
            $lastLogin = $row['last_login'];
            $streak = $row['streak_count'];

            if ($lastLogin == null) {
                $streak = 1;
            }
            else if ($lastLogin == date("Y-m-d", strtotime("-1 day"))) {
                $streak = $streak + 1;
            }
            else if ($lastLogin == $today) {
                // no change
            }
            else {
                $streak = 1;
            }

            // update streak
            $stmt2 = $conn->prepare("UPDATE users SET last_login=?, streak_count=? WHERE userid=?");
            $stmt2->bind_param("sii", $today, $streak, $row['userid']);
            $stmt2->execute();
            $stmt2->close();

            // ================================================
            //      ⭐ AWARD XP + BADGES ⭐
            // ================================================
            // Daily login XP
            addPoints($conn, $row['userid'], 10, "daily_login");

            // Streak XP
            if ($streak > 1) {
                addPoints($conn, $row['userid'], 5, "login_streak", ["streak" => $streak]);
            }

            // Streak Badge
            if ($streak == 7) {
                awardBadge($conn, $row['userid'], "streak_master");
            }

            // redirect to dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Invalid password.";
            $type = "error";
        }
    } else {
        $message = "User not found.";
        $type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🌱 Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Login to Planet365</h2>
    <?php if($message != ""): ?>
        <div class="message <?php echo $type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a class="link" href="register.html">Don't have an account? Register</a>
</div>

</body>
</html>
