<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}
$username = $_SESSION['username']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planet365 - Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #a8e063, #56ab2f);
      margin: 0;
      padding: 0;
      color: #fff;
      text-align: center;
    }
    .navbar {
      display: flex;
      justify-content: flex-end;
      background: #2e7d32;
      padding: 15px 20px;
    }
    .navbar a {
      color: #fff;
      text-decoration: none;
      margin-left: 20px;
      font-weight: bold;
      transition: color 0.3s ease;
    }
    .navbar a:hover {
      color: #c8e6c9;
    }
    .container {
      margin-top: 50px;
    }
    .card {
      background: rgba(255, 255, 255, 0.1);
      padding: 30px;
      border-radius: 12px;
      display: inline-block;
      max-width: 400px;
    }
    h1 {
      margin-bottom: 15px;
    }
    .tip {
      background: #388e3c;
      padding: 15px;
      border-radius: 10px;
      margin-top: 20px;
      font-style: italic;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <a href="logout.php">Logout</a>
  </div>

  <div class="container">
    <div class="card">
      <h1 id="greeting">Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
      <p>You are now logged into <strong>Planet365</strong>.</p>

      <div class="tip" id="ecoTip">
        🌱 Loading eco tip...
      </div>
    </div>
  </div>

  <script>
    // Username from PHP
    const username = "<?php echo htmlspecialchars($username); ?>";

    // Greeting based on time
    const now = new Date();
    const hour = now.getHours();
    let greet;

    if(hour >= 5 && hour < 12){
      greet = "Good Morning";
    } else if(hour >= 12 && hour < 17){
      greet = "Good Afternoon";
    } else if(hour >= 17 && hour < 21){
      greet = "Good Evening";
    } else {
      greet = "Good Night";
    }

    document.getElementById("greeting").innerHTML = greet + ", " + username + " 🌿";

    // Array of eco-friendly tips
    const ecoTips = [
      "Switch off lights when not in use to save energy.",
      "Carry a reusable water bottle instead of buying plastic.",
      "Use public transport or cycle to reduce carbon footprint.",
      "Plant a tree and help restore nature.",
      "Segregate your waste for recycling.",
      "Save water by turning off the tap while brushing."
    ];

    // Pick a random tip
    const randomTip = ecoTips[Math.floor(Math.random() * ecoTips.length)];
    document.getElementById("ecoTip").innerHTML = "🌱 <strong>Eco Tip of the Day:</strong><br>" + randomTip;
  </script>

</body>
</html>

