const slapButton = document.getElementById("slapButton");
const turkeyImg = document.getElementById("turkey");
const progress = document.getElementById("progress");
const status = document.getElementById("status");
const totalSlapsElement = document.getElementById("totalSlaps"); // Use the existing #totalSlaps element
let username = prompt("Enter your username");

if (!username) {
    username = "Anonymous"; // fallback username if prompt is canceled
}

const thanksgivingDate = new Date('November 28, 2024 00:00:00').getTime();

let slaps = 0;
const totalSlapsToCook = 1000000; // Adjust this number as necessary for your game

// Slap the turkey button functionality
slapButton.addEventListener("click", () => {
    slaps++;

    // Update local slap status
    progress.textContent = `Your Slaps: ${slaps}`;
    updateTurkeyStatus();

    // Send slap to the server to update the database
    fetch('slap_turkey.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `username=${encodeURIComponent(username)}&slap_count=${slaps}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        console.log(data);  // Log success message
        // After slapping, update total slaps and individual contributions
        updateSlapInfo();
    })
    .catch(error => console.error('Error:', error));
});

// Function to update turkey status based on slaps
function updateTurkeyStatus() {
    if (slaps < totalSlapsToCook / 3) {
        status.textContent = "Status: As raw ass it gets...";
        turkeyImg.src = "Raw_Turkey.webp"; // Make sure your image paths are correct
    } else if (slaps < (totalSlapsToCook / 3) * 2) {
        status.textContent = "Status: It's starting to steam!";
        turkeyImg.src = "Raw_Turkey.webp";
    } else if (slaps < totalSlapsToCook) {
        status.textContent = "Status: So close you can smell something!";
        turkeyImg.src = "Raw_Turkey.webp";
    } else {
        status.textContent = "Status: Fully cooked, but so is your hand!";
        turkeyImg.src = "Raw_Turkey.webp";
        slapButton.disabled = true;  // Disable button after turkey is fully cooked
    }
}

// Function to update total slaps and user contributions from the server
function updateSlapInfo() {
    fetch('get_slaps.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text(); // Log raw text instead of JSON
    })
    .then(rawData => {
        console.log("Raw response: ", rawData); // Log the raw response to the console
        return JSON.parse(rawData);  // Then parse the JSON
    })
        .then(data => {
            // Update total slaps (using total_slaps)
            totalSlapsElement.textContent = `Total Slaps: ${data.total}`;
            
            // Find and display the current user's slap count (using slap_count)
            const user = data.users.find(u => u.username === username);
            progress.textContent = `Your Slaps: ${user ? user.slap_count : 0}`;

            // Update the scoreboard
            const playerList = document.getElementById("playerList");
            playerList.innerHTML = ''; // Clear existing list

            data.users.forEach(user => {
                const li = document.createElement('li');
                li.textContent = `${user.username}: ${user.slap_count} slaps`;
                playerList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching slap info:', error));
}

// Countdown to Thanksgiving
const countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const timeUntilThanksgiving = thanksgivingDate - now;

    // Time calculations for days, hours, minutes, and seconds
    const days = Math.floor(timeUntilThanksgiving / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeUntilThanksgiving % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeUntilThanksgiving % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeUntilThanksgiving % (1000 * 60)) / 1000);

    // Display the result
    document.getElementById("countdown").innerHTML = `
        ${days}d ${hours}h ${minutes}m ${seconds}s
    `;

    // If the countdown is over, display a message
    if (timeUntilThanksgiving < 0) {
        clearInterval(countdownInterval);
        document.getElementById("countdown").innerHTML = "Happy Thanksgiving!";
    }
}, 1000);

// Initial call to load slap data
updateSlapInfo();
