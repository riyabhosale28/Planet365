





<?php
session_start();
include 'db.php';

// Redirect if user not logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.html");
    exit();
}

$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$msg = "";

// Fetch user info
$sql_user = "SELECT username, email, address, dob FROM users WHERE userid='$userid'";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();

// Fetch latest footprint for this user
$sql_fp = "SELECT * FROM user_footprints WHERE username='$username' ORDER BY created_at DESC LIMIT 1";
$res_fp = $conn->query($sql_fp);
$footprintData = $res_fp->num_rows > 0 ? $res_fp->fetch_assoc() : null;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['update'])) {
    $username_new = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $dob = $conn->real_escape_string($_POST['dob']);

    $update_sql = "UPDATE users SET username='$username_new', email='$email', address='$address', dob='$dob' WHERE userid='$userid'";
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['username'] = $username_new;
        $msg = "Profile updated successfully!";
        $user['username'] = $username_new;
        $user['email'] = $email;
        $user['address'] = $address;
        $user['dob'] = $dob;
        $username = $username_new; // update for footprint fetch
    } else {
        $msg = "Error updating profile: " . $conn->error;
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    $sql_pass = "SELECT password FROM users WHERE userid='$userid'";
    $res_pass = $conn->query($sql_pass);
    $row_pass = $res_pass->fetch_assoc();

    if (password_verify($current, $row_pass['password'])) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hashed' WHERE userid='$userid'");
        $msg = "Password changed successfully!";
    } else {
        $msg = "Current password is incorrect!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🌱Profile - Planet365</title>
<style>
    body { font-family: 'Arial', sans-serif; background:#e8f5e9; margin:0; padding:20px; }
    .container { max-width:900px; margin:20px auto; display:flex; flex-wrap:wrap; gap:20px; }
    .card { background:#fff; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); flex:1 1 400px; }
    h2 { color:#2e7d32; margin-bottom:20px; }
    label { display:block; margin:10px 0 5px; font-weight:bold; }
    input, select { width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; margin-bottom:10px; }
    button { padding:12px 20px; border:none; border-radius:8px; background:#2e7d32; color:white; font-size:16px; cursor:pointer; margin-top:10px; }
    button:hover { background:#1b5e20; }
    .msg { padding:10px; margin-bottom:15px; border-radius:6px; background:#c8e6c9; color:#2e7d32; font-weight:bold; }
    nav { text-align:center; margin-top:20px; }
    nav a { text-decoration:none; color:#2e7d32; margin:0 10px; font-weight:bold; }
    nav a:hover { text-decoration:underline; }
    table { width:100%; border-collapse:collapse; margin-top:15px; }
    th, td { padding:10px; border:1px solid #a5d6a7; }
    th { background:#2e7d32; color:white; text-align:left; }
    tfoot tr { font-weight:bold; background:#c8e6c9; }
    @media(max-width:600px) { .container { flex-direction:column; } }
</style>
</head>
<body>

<section id="carbon-report" class="card">
    <h2>🌱 Your Carbon Footprint Report</h2>
    <p>Track your daily activities and see their impact on the environment.</p>

    <table>
        <thead>
            <tr>
                <th>Activity</th>
                <th>Daily Usage</th>
                <th>CO₂ Emission (kg)</th>
            </tr>
        </thead>
        <tbody id="carbonTableBody">
         
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total Daily CO₂</td>
                <td id="totalCO2">0</td>
            </tr>
            <tr>
                <td colspan="2">Estimated Yearly CO₂ (tons)</td>
                <td id="yearlyCO2">0</td>
            </tr>
        </tfoot>
    </table>
</section>

<div class="container">
    <div class="card">
        <h2>Profile Information</h2>
        <?php if(!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <label>Address</label>
            <input type="text" name="address" value="<?php echo $user['address']; ?>">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo $user['dob']; ?>">
            <button type="submit" name="update">Update Profile</button>
        </form>
    </div>

    <div class="card">
        <h2>Change Password</h2>
        <form method="POST">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
            <label>New Password</label>
            <input type="password" name="new_password" required>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>
</div>

<nav>
    <a href="dashboard.php">⬅ Back to Dashboard</a> | 
    <a href="logout.php">🚪 Logout</a>
</nav>

<script>
// Function to show carbon report dynamically
function showCarbonReport(userData) {
    if(!userData) {
        document.getElementById("carbonTableBody").innerHTML = `<tr><td colspan="3" style="text-align:center;">You haven't calculated your carbon footprint yet.</td></tr>`;
        return;
    }

    let travelEmission = userData.travel * 0.21;
    let electricityEmission = (userData.electricity / 30) * 0.92;
    let waterEmission = userData.water * 0.001;
    let dietEmission = (userData.diet === "veg" ? 0.5/365 : 1.5/365) * 1000;

    let dailyTotal = travelEmission + electricityEmission + waterEmission + dietEmission;
    let yearlyTotal = (dailyTotal * 365 / 1000).toFixed(2);

    document.getElementById("carbonTableBody").innerHTML = `
        <tr><td>Daily Travel (km)</td><td>${userData.travel}</td><td>${travelEmission.toFixed(2)}</td></tr>
        <tr><td>Monthly Electricity (kWh)</td><td>${userData.electricity}</td><td>${electricityEmission.toFixed(2)}</td></tr>
        <tr><td>Daily Water Usage (liters)</td><td>${userData.water}</td><td>${waterEmission.toFixed(3)}</td></tr>
        <tr><td>Diet Type</td><td>${userData.diet === "veg" ? "Vegetarian" : "Non-Vegetarian"}</td><td>${dietEmission.toFixed(3)}</td></tr>
    `;
    document.getElementById("totalCO2").innerText = dailyTotal.toFixed(2);
    document.getElementById("yearlyCO2").innerText = yearlyTotal;
}

// Pass PHP data to JS
const footprintData = <?php echo json_encode($footprintData); ?>;
showCarbonReport(footprintData);
</script>

</body>
</html>
