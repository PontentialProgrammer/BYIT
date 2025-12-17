<?php 
    require_once "../includes/database.php";
    require_once "../includes/functions.php";

    
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/font.css">
    <link rel="stylesheet" href="../assets/redirect_style.css">
    <title>signinredirect</title>
</head>
<body>
    
    <h1 class="redirect_h1" style="color: white;">User has successfully been created.</h1>
    <p class="redirect_p" style="color: white;">They will be redirected to the login page.</p>


    <div class="timer-container">
        <svg class="timer-ring" viewBox="0 0 100 100">
            <circle class="timer-background" cx="50" cy="50" r="45"></circle>
            <circle class="timer-progress" cx="50" cy="50" r="45"></circle>
            <!-- <text x="50", y="50"></text> -->
        </svg>
        <p id="countdown-display">3</p>
    </div>
    <!-- <p id="timer">3</p> -->
</body>

<script>
    // const timer = document.getElementById("countdown-display");
    const countdownDisplay = document.getElementById('countdown-display');
    const timerProgress = document.querySelector('.timer-progress');
    const radius = timerProgress.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;

    timerProgress.style.strokeDasharray = circumference;

    let timeRemaining = 3; // Example: 60 seconds

    function updateTimer() {
        // const minutes = Math.floor(timeRemaining / 60);

        if(timeRemaining >= 0){
            // const seconds = timeRemaining % 60;

            countdownDisplay.textContent = `${timeRemaining}`;

            const progress = timeRemaining / 3; // Assuming 60 seconds total
            const offset = circumference * (1 - progress);
            timerProgress.style.strokeDashoffset = offset;
            console.log(timeRemaining);
            timeRemaining--;
        }
        

        if (timeRemaining < 0) {
            setTimeout(1500);
            clearInterval(timerInterval);
            // countdownDisplay.textContent = "TIME UP!";
            window.location.href = "./login.php";
            // Optional: Add a sound or visual alert
        }
    }

    // Initial call to set up the display
    updateTimer();

    // Start the interval
    const timerInterval = setInterval(updateTimer, 1000);


</script>
</html>

