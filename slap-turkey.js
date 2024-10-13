// Grab essential HTML elements for interactivity
const slapButton = document.getElementById("slapButton");
const turkeyImg = document.getElementById("turkey");
const progress = document.getElementById("progress");
const status = document.getElementById("status");
const totalSlapsElement = document.getElementById("totalSlaps");
let username = prompt("Enter your username");

const form = document.getElementById('nameForm');

form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevents the page from refreshing
    const playerName = document.getElementById('playerName').value;

if (playerName) {
        // Play the turkey sound after the user enters their name
        var turkeyAudio = document.getElementById('turkeySound');
        turkeyAudio.play();

        // You can also hide the form or show other content here
        form.style.display = 'none'; // Hides the form
        // Display a welcome message or start the game, etc.
        document.body.insertAdjacentHTML('beforeend', `<p>Welcome, ${playerName}! Get ready to spank the turkey!</p>`);
}

// Fallback to 'Anonymous' if no username is provided
if (!username) {
    username = "Anonymous";
}

// Date of Thanksgiving for countdown
const thanksgivingDate = new Date('November 28, 2024 00:00:00').getTime();

// Initial slap count and target to fully cook the turkey
let slaps = 0;
const totalSlapsToCook = 66000; // Adjust as necessary

// Event listener for when the 'Slap the Turkey' button is clicked
slapButton.addEventListener("click", () => {
    slaps += 1; // Always add 1
    console.log('Slap count:', slaps);
    progress.textContent = `Your Slaps: ${slaps}`; // Update user's slap count
    updateTurkeyStatus(); // Update turkey image and status

    // Send the slap count to the server to update the database
    fetch('slap_turkey.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `username=${encodeURIComponent(username)}&slap_count=${slaps}`
    })
    .then(response => response.text())
    .then(() => {
        // Refresh total slaps and player information after slapping
        updateSlapInfo();
    })
    .catch(error => console.error('Error sending slap data:', error));
});

// Function to update the turkey's image and status based on slap count
function updateTurkeyStatus() {
    if (slaps < totalSlapsToCook / 3) {
        status.textContent = "Status: As raw as it gets...";
        turkeyImg.src = "Raw_Turkey.webp"; // Adjust image path as needed
    } else if (slaps < (totalSlapsToCook / 3) * 2) {
        status.textContent = "Status: It's starting to steam!";
        turkeyImg.src = "Steaming_Turkey.webp"; // Adjust image path as needed
    } else if (slaps < totalSlapsToCook) {
        status.textContent = "Status: So close you can smell something!";
        turkeyImg.src = "Almost_Done_Turkey.webp"; // Adjust image path as needed
    } else {
        status.textContent = "Status: Fully cooked, but so is your hand!";
        turkeyImg.src = "Done_Turkey.webp"; // Change to cooked turkey image
        slapButton.disabled = true; // Disable button when fully cooked
    }
}

// Function to fetch and update total slaps and player contributions
function updateSlapInfo() {
    fetch('get_slaps.php')
    .then(response => response.json()) // Parse response as JSON
    .then(data => {
        // Update the total slaps display
        totalSlapsElement.textContent = `Total Slaps: ${data.total}`;
        
        // Find the current user's slap count and update progress
        const user = data.users.find(u => u.username === username);
        progress.textContent = `Your Slaps: ${user ? user.slap_count : 0}`;

        // Sort users by slap count in descending order
        const sortedUsers = data.users.sort((a, b) => b.slap_count - a.slap_count);

        // Update the player list
        const playerList = document.getElementById("playerList");
        playerList.innerHTML = ''; // Clear the list before updating

        // Populate the list with each user's slap count
        sortedUsers.forEach(user => {
            const li = document.createElement('li');
            li.textContent = `${user.username}: ${user.slap_count} slaps`;
            playerList.appendChild(li);
        });
    })
    .catch(error => {
        console.error('Error fetching slap info:', error);
    });
}


// Countdown timer for Thanksgiving
const countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const timeUntilThanksgiving = thanksgivingDate - now;

    // Calculate days, hours, minutes, and seconds
    const days = Math.floor(timeUntilThanksgiving / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeUntilThanksgiving % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeUntilThanksgiving % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeUntilThanksgiving % (1000 * 60)) / 1000);

    // Update the countdown display
    document.getElementById("countdown").innerHTML = `
        ${days}d ${hours}h ${minutes}m ${seconds}s
    `;

    // Stop countdown and display a message if Thanksgiving has passed
    if (timeUntilThanksgiving < 0) {
        clearInterval(countdownInterval);
        document.getElementById("countdown").innerHTML = "Happy Thanksgiving!";
    }
}, 1000);

// Initial call to load slap data from the server
updateSlapInfo();
