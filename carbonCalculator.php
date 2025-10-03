<?php
session_start();
$loggedIn = isset($_SESSION['username']); // true if user logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Planet365 Services</title>


</head>
<body>
<button style="align-item:left;padding:12px; background:#2e7d32; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s;"><a style="text-decoratio:none;color:white;" href="index.html">go back to home</a></button>
<!-- Carbon Footprint Calculator -->
<section id="carbon-calculator" class="section" style="background:#e8f5e9; padding:50px 20px; border-radius:12px; margin:20px auto; max-width:700px;">
  <div class="container">
    <h2 style="color:#2e7d32; text-align:center; margin-bottom:20px;">🌍 Carbon Footprint Calculator</h2>
    <p style="text-align:center; color:#1b5e20;">Estimate your daily carbon footprint and discover ways to reduce it.</p>

    <form id="footprintForm" style="display:flex; flex-direction:column; gap:15px; margin-top:20px;">
      <label for="travel" style="font-weight:bold; color:#2e7d32;">Daily Travel Distance (km):</label>
      <input type="number" id="travel" placeholder="e.g., 15" required style="padding:10px; border-radius:6px; border:1px solid #2e7d32;">

      <label for="electricity" style="font-weight:bold; color:#2e7d32;">Monthly Electricity Usage (kWh):</label>
      <input type="number" id="electricity" placeholder="e.g., 120" required style="padding:10px; border-radius:6px; border:1px solid #2e7d32;">

      <label for="water" style="font-weight:bold; color:#2e7d32;">Daily Water Usage (liters):</label>
      <input type="number" id="water" placeholder="e.g., 100" required style="padding:10px; border-radius:6px; border:1px solid #2e7d32;">

      <label for="diet" style="font-weight:bold; color:#2e7d32;">Diet Type:</label>
      <select id="diet" style="padding:10px; border-radius:6px; border:1px solid #2e7d32;">
        <option value="veg">Vegetarian</option>
        <option value="nonveg">Non-Vegetarian</option>
      </select>

      <button type="button" onclick="calculateFootprint()" style="padding:12px; background:#2e7d32; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s;">
        Calculate
      </button>
    </form>

    <div id="result" style="margin-top:25px; font-weight:bold; text-align:center; color:#1b5e20;"></div>
  </div>
</section>

<script>
const loggedIn = <?php echo $loggedIn ? 'true' : 'false'; ?>;

function calculateFootprint() {
    let travel = parseFloat(document.getElementById("travel").value) || 0;
    let electricity = parseFloat(document.getElementById("electricity").value) || 0;
    let water = parseFloat(document.getElementById("water").value) || 0;
    let diet = document.getElementById("diet").value;

    let travelEmission = travel * 0.21;
    let electricityEmission = (electricity / 30) * 0.92;
    let waterEmission = water * 0.001;
    let dietEmission = (diet === "veg") ? (0.5 / 365) : (1.5 / 365);

    let total = travelEmission + electricityEmission + waterEmission + (dietEmission * 1000);

    let resultHTML = `🌱 Your estimated daily carbon footprint is <b>${total.toFixed(2)} kg CO₂</b>.<br>
                      That’s about ${(total * 365 / 1000).toFixed(2)} tons per year.<br><br>`;

    // Suggestions based on footprint
    if(total < 5){
        resultHTML += `✅ Great! You have a low carbon footprint.<br>
                       Keep up your eco-friendly habits like walking, recycling, and saving electricity.`;
    } else if(total < 10){
        resultHTML += `⚠️ Moderate footprint. Try reducing travel and electricity use.<br>
                       Suggestions:<br>
                       - Use public transport or carpool.<br>
                       - Switch to energy-efficient appliances.<br>
                       - Reduce water usage.<br>
                       - Eat more plant-based meals.`;
    } else {
        resultHTML += `❌ High footprint. Consider taking action to reduce it.<br>
                       Suggestions:<br>
                       - Switch to renewable energy sources.<br>
                       - Minimize car usage and fly less.<br>
                       - Eat mostly vegetarian meals.<br>
                       - Reduce, reuse, recycle daily.<br>
                       - Conserve water and electricity wherever possible.`;
    }

    document.getElementById("result").innerHTML = resultHTML;

    if(loggedIn){
        // Send data to PHP to save in database
        fetch('save_footprint.php', {
            method: 'POST',
            headers: { 'Content-Type':'application/json' },
            body: JSON.stringify({ footprint: total.toFixed(2) })
        }).then(res=>res.text())
          .then(data => console.log(data));
    } else {
        // Store locally for anonymous users
        localStorage.setItem('lastFootprint', total.toFixed(2));
        alert("You are not logged in. Your footprint is saved locally in this browser.");
    }
}
</script>
</body>
</html>
