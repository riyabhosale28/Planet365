
<?php
 
session_start();

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}
$username = $_SESSION['username']; 
$defaultCity = "Mumbai"; 
$city = isset($_GET['city']) ?urlencode($_GET['city']) :urlencode( $defaultCity);
$apiKey = "298e46cc1d3f208bc3a54e0abbb152dd"; 
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&appid=$apiKey";
$response = @file_get_contents($apiUrl);
if($response!==false){
$weatherData = json_decode($response, true);
$temperature = $weatherData['main']['temp'] ?? 'N/A';
$description = $weatherData['weather'][0]['description'] ?? '';
$icon = $weatherData['weather'][0]['icon'] ?? '';
}else{
  $temperature = 'N/A';
    $description = 'Weather data unavailable';
    $icon = '';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🌱Planet365 - Dashboard</title>
  
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #a8e063, #56ab2f);
      margin-bottom: 30px;
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
    .nav-links {
  list-style: none;
  margin: 0;
  margin-top: 10px;
  display: flex;
  gap: 20px;
  padding: 10px;
  justify-content: center;
}
.nav-actions {
    list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  gap: 10px;
  margin-left: auto;
}

.nav-links li a{
  color: white;
  text-decoration: none;
  font-weight: bold;
  font-size: 18px;
  padding: 8px 14px;
  transition: 0.3s;
}

.nav-links li a:hover {
  background-color: white;
  color: #4CAF50;
  border-radius: 5px;
}

.weather {
        margin-top: 30px;
        padding: 15px;
        background: #a5d6a7;
        border-radius: 8px;
        color: #1b5e20;
        display: inline-block;
    }
    .weather img { vertical-align: middle; }
     .city-form {
        margin-top: 20px;
    }
    .city-form input {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #2e7d32;
    }
    .city-form button {
        padding: 8px 12px;
        background: #2e7d32;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .city-form button:hover {
        background: #1b5e20;
    }
  </style>
</head>
<body>
 
  <div class="navbar">
   
     <a href="profile.php">👤 Profile</a> | 
      <a href="logout.php">🚪 Logout</a>

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
  
    const username = "<?php echo htmlspecialchars($username); ?>";

   
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
  <div class="weather">
        <h3>Weather in <?php echo $city; ?></h3>
        <?php if($temperature !== 'N/A'): ?>
            <img src="http://openweathermap.org/img/wn/<?php echo $icon; ?>@2x.png" alt="Weather Icon">
            <span><?php echo $temperature; ?>°C, <?php echo ucfirst($description); ?></span>
        <?php else: ?>
            <span>Weather data not available.</span>
        <?php endif; ?>
    </div>
    <div class="city-form">
        <form method="GET" action="dashboard.php">
            <input type="text" name="city" placeholder="Enter city" required>
            <button type="submit">Check Weather</button>
        </form>
    </div>
    <script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }
}

function showPosition(position) {
    let lat = position.coords.latitude;
    let lon = position.coords.longitude;

    // Fetch city name using OpenWeatherMap Geocoding API
    fetch(`https://api.openweathermap.org/geo/1.0/reverse?lat=${lat}&lon=${lon}&limit=1&appid=298e46cc1d3f208bc3a54e0abbb152dd`)
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            let city = data[0].name;
            window.location.href = "dashboard.php?city=" + city;
        }
    })
    .catch(error => console.log(error));
}

function showError(error) {
    console.log("Location error:", error.message);
}

// Run on first load only if no city chosen manually
<?php if (!isset($_GET['city'])): ?>
    window.onload = getLocation;
<?php endif; ?>
</script>

<section id="quiz" class="section">
  <div class="container">
    <h2>Test Your Environmental Knowledge 🌱</h2>
    <div id="quiz-container">
      <p id="question"></p>
      <div id="options"></div>
      <button id="next-btn">Next</button>
      <p id="score" style="font-weight:bold;"></p>
    </div>
  </div>
</section>

<script>
const quizData = [
  {
    question: "Which material is fully recyclable?",
    options: ["Plastic bags", "Glass bottles", "Styrofoam", "Food waste"],
    answer: "Glass bottles"
  },
  {
    question: "What is e-waste?",
    options: ["Electronic waste", "Energy waste", "Environmental waste", "Everyday waste"],
    answer: "Electronic waste"
  },
  {
    question: "Which practice reduces environmental impact the most?",
    options: ["Reuse items", "Throw items in trash", "Buy more plastic", "Use disposable cups"],
    answer: "Reuse items"
  },
  {
    question: "Which of these can be composted?",
    options: ["Paper towels", "Plastic bottles", "Aluminum cans", "Glass jars"],
    answer: "Paper towels"
  }
];

let currentQuestion = 0;
let score = 0;

const questionEl = document.getElementById("question");
const optionsEl = document.getElementById("options");
const nextBtn = document.getElementById("next-btn");
const scoreEl = document.getElementById("score");

function loadQuestion() {
  optionsEl.innerHTML = "";
  let q = quizData[currentQuestion];
  questionEl.textContent = q.question;

  q.options.forEach(option => {
    let btn = document.createElement("button");
    btn.textContent = option;
    btn.style.margin = "5px";
    btn.onclick = () => checkAnswer(option);
    optionsEl.appendChild(btn);
  });
}

function checkAnswer(selected) {
  if (selected === quizData[currentQuestion].answer) score++;
  currentQuestion++;
  if (currentQuestion < quizData.length) {
    loadQuestion();
  } else {
    questionEl.textContent = "Quiz Completed!";
    optionsEl.innerHTML = "";
    nextBtn.style.display = "none";
    scoreEl.textContent = `Your Score: ${score} / ${quizData.length}`;
  }
}

nextBtn.onclick = () => loadQuestion();

// Initialize
loadQuestion();
</script>

<style>
#quiz-container { background:lightblue; padding: 20px; border-radius: 12px; max-width: 600px; margin: 20px auto; }
#options button { background: #2e7d32; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; }
#options button:hover { background: #1b5e20; }
</style>

  <ul class="nav-links">
<li style="text-align:center"><a href="index.html">go to home</a></li>
  </ul>
</body>
</html>





