async function sendEnergyData(electricity, water, gas){
    let res = await fetch('save_energy.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({electricity, water, gas})
    });
    console.log(await res.text());
}

async function getAIRecommendations(){
    let res = await fetch('ai_recommendations.php');
    let data = await res.json();
    console.log("AI Tips:", data.tips);
}
