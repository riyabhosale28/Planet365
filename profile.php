<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])){
    header("Location:login.html");
    exit();
}

$userid=$_SESSION['userid'];
$msg="";
$sql="select username,email,address,dob from users where userid='$userid'";
$result=$conn->query($sql);
$user=$result->fetch_assoc();

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['update'])){
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
     $address = $conn->real_escape_string($_POST['address']);
    $dob = $conn->real_escape_string($_POST['dob']);

    $update = "UPDATE users SET username='$username', email='$email',address='$address', dob='$dob'  WHERE userid='$userid'";
    if ($conn->query($update) === TRUE) {
        $_SESSION['username'] = $username;
        $msg = "Profile updated successfully!";
        $msg = "Profile updated successfully!";
        $user['username'] = $username;
        $user['email'] = $email;
        $user['address'] = $address;
        $user['dob'] = $dob;
    } else {
         $msg = "Error updating profile: " . $conn->error;
}
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
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
<title>Profile - Planet365</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #e8f5e9;
        margin-bottom: 30px;
        padding: 0;
    }
    .container {
        max-width: 900px;
        margin: 20px auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
    }
    .card {
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        flex: 1 1 400px;
    }
    h2 { color: #2e7d32; margin-bottom: 20px; }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 10px; }
    button {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        background: #2e7d32;
        color: white;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
    }
    button:hover { background: #1b5e20; }
    .msg {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 6px;
        background: #c8e6c9;
        color: #2e7d32;
        font-weight: bold;
    }
    nav { text-align: center; margin-top: 20px; }
    nav a { text-decoration: none; color: #2e7d32; margin: 0 10px; font-weight: bold; }
    nav a:hover { text-decoration: underline; }
    @media(max-width: 600px) {
        .container { flex-direction: column; }
    }
</style>
</head>
<body>
    <section id="carbon-report" style="padding:20px; background:#e8f5e9; border-radius:12px; margin-top:20px;">
  <div class="container">
    <h2 style="color:#2e7d32;">🌱 Your Carbon Footprint Report</h2>
    <p>Track your daily activities and see their impact on the environment.</p>

    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
      <thead>
        <tr style="background:#2e7d32; color:white; text-align:left;">
          <th style="padding:10px;">Activity</th>
          <th style="padding:10px;">Daily Usage</th>
          <th style="padding:10px;">CO₂ Emission (kg)</th>
        </tr>
      </thead>
      <tbody id="carbonTableBody">
        <!-- Data rows will be inserted here dynamically -->
      </tbody>
      <tfoot>
        <tr style="background:#a5d6a7; font-weight:bold;">
          <td colspan="2" style="padding:10px;">Total Daily CO₂</td>
          <td id="totalCO2" style="padding:10px;">0</td>
        </tr>
        <tr style="background:#c8e6c9; font-weight:bold;">
          <td colspan="2" style="padding:10px;">Estimated Yearly CO₂ (tons)</td>
          <td id="yearlyCO2" style="padding:10px;">0</td>
        </tr>
      </tfoot>
    </table>
  </div>
</section>

<script>
function showCarbonReport(userData) {
    // userData: {travel, electricity, water, diet}
    let travelEmission = userData.travel * 0.21;
    let electricityEmission = (userData.electricity / 30) * 0.92;
    let waterEmission = userData.water * 0.001;
    let dietEmission = (userData.diet === "veg") ? (0.5 / 365) * 1000 : (1.5 / 365) * 1000;

    let dailyTotal = travelEmission + electricityEmission + waterEmission + dietEmission;
    let yearlyTotal = (dailyTotal * 365 / 1000).toFixed(2);

    const tbody = document.getElementById("carbonTableBody");
    tbody.innerHTML = `
      <tr>
        <td>Daily Travel (km)</td>
        <td>${userData.travel}</td>
        <td>${travelEmission.toFixed(2)}</td>
      </tr>
      <tr>
        <td>Monthly Electricity (kWh)</td>
        <td>${userData.electricity}</td>
        <td>${electricityEmission.toFixed(2)}</td>
      </tr>
      <tr>
        <td>Daily Water Usage (liters)</td>
        <td>${userData.water}</td>
        <td>${waterEmission.toFixed(3)}</td>
      </tr>
      <tr>
        <td>Diet Type</td>
        <td>${userData.diet === "veg" ? "Vegetarian" : "Non-Vegetarian"}</td>
        <td>${dietEmission.toFixed(3)}</td>
      </tr>
    `;

    document.getElementById("totalCO2").innerText = dailyTotal.toFixed(2);
    document.getElementById("yearlyCO2").innerText = yearlyTotal;
}

// Example usage:
showCarbonReport({travel:15, electricity:120, water:100, diet:"nonveg"});
</script>

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
</body>
</html>