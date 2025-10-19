<?php
session_start();
include('db.php'); // go up two folders

if(!isset($_SESSION['userid'])){
    header("Location:login.html");
    exit();
}
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🌱 Planet365 Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body { font-family: Arial, sans-serif; background:#e8f5e9; margin-bottom:30px; padding:0; }
    .header { background:#2e7d32; color:white; padding:20px; text-align:center; }
    .container { max-width:1200px; margin:20px auto; padding:0 20px; display:grid; grid-template-columns: 2fr 1fr; gap:20px; }
    .card { background:white; border-radius:12px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { color:#2e7d32; }
    ul { padding-left:20px; }
    li { margin-bottom:10px; }
    .btn{
      background-color: #2e8b57;       /* soft eco green */
  color: #fff;
  border: none;
  border-radius: 30px;             /* smooth rounded look */
  padding:12px 28px;
  font-size: 16px;
  font-weight: 100;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(46, 139, 87, 0.3);
  letter-spacing: 0.5px;
    }
    .btn: hover{
    background-color: #3cb371;       /* brighter green on hover */
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(46, 139, 87, 0.4);
    }
</style>
</head>
<body>

<div class="header">
    <h1>🌱 Welcome, <?php echo $username; ?>!</h1>
    
</div>

<div class="container">
    <!-- Left Column: Charts & AI Tips -->
    <div>
        <div class="card">
            <h2>Your Daily CO₂ Footprint</h2>
            <canvas id="co2Chart" height="200"></canvas>
        </div>

        <div class="card" style="margin-top:20px;">
            <h2>AI Eco Tips</h2>
            <ul id="aiTips">
                <li>Loading tips...</li>
            </ul>
        </div>
    </div>

    <!-- Right Column: Weekly Summary & Challenges -->
    <div>
        <div class="card">
            <h2>Weekly CO₂ Summary</h2>
            <p><b>Total CO₂ this week:</b> <span id="weeklyCO2">0</span> kg</p>
            <p><b>Average daily CO₂:</b> <span id="avgCO2">0</span> kg</p>
        </div>

        <div class="card" style="margin-top:20px;">
            <h2>Challenges & Leaderboard</h2>
            <ul id="challengeList">
                <li>Loading challenges...</li>
            </ul>
        </div>
    </div>
    
    <button class="btn" ><a href="index.html">Go back to Home</a></button>
  
</div>

<script>
// Fetch AI recommendations
async function fetchAITips(){
    try {
    let res = await fetch('ai_recommendations.php');
    let data = await res.json();
    console.log('AI Tips:', data);
    const aiTips = document.getElementById('aiTips');
    aiTips.innerHTML = '';
    data.tips.forEach(tip => {
        let li = document.createElement('li');
        li.textContent = tip;
        aiTips.appendChild(li);
    });
}catch(err) {
    console.error('Error fetching AI tips:', err);
}
}

fetchAITips();

// Example Chart.js CO2 data
const ctx = document.getElementById('co2Chart').getContext('2d');
const co2Chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Travel','Electricity','Water','Diet'],
        datasets: [{
            label: 'Daily CO₂ (kg)',
            data: [3.1, 4.2, 0.1, 2.5], // fetch from DB in real scenario
            backgroundColor: ['#66bb6a','#2e7d32','#a5d6a7','#c8e6c9']
        }]
    },
    options: {
        responsive:true,
        plugins: { legend:{display:false} },
        scales:{ y:{ beginAtZero:true } }
    }
});

// Weekly summary example
document.getElementById('weeklyCO2').textContent = 45.2; // fetch from DB
document.getElementById('avgCO2').textContent = (45.2/7).toFixed(2);

// Example challenges / leaderboard
const challenges = [
    {title:"Reduce plastic usage", points:50},
    {title:"Walk to work", points:40},
    {title:"Save 10% electricity", points:35}
];
const challengeList = document.getElementById('challengeList');
challengeList.innerHTML = '';
challenges.forEach(c=>{
    let li = document.createElement('li');
    li.textContent = `${c.title} - ${c.points} pts`;
    challengeList.appendChild(li);
});
</script>

</body>
</html>
