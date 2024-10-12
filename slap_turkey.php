<?php
include 'db_connect.php'; // Ensure this contains your PostgreSQL connection

if (!$conn) {
    die("Database connection error: " . pg_last_error());
}

$username = $_POST['username'];
$slaps = $_POST['slaps'];

// Debugging - Check if POST data is being received correctly
var_dump($username);
var_dump($slaps);

// Corrected Query - Use correct column names (slap_count and total_slaps)
$query = "INSERT INTO slaps (username, slap_count, total_slaps) 
          VALUES ($1, $2, $2) 
          ON CONFLICT (username) 
          DO UPDATE SET slap_count = slaps.slap_count + $2, total_slaps = slaps.total_slaps + $2";

$result = pg_prepare($conn, "insert_slaps", $query);

if ($result) {
    if (!pg_execute($conn, "insert_slaps", array($username, $slaps))) {
        echo "Error executing query: " . pg_last_error($conn);
    } else {
        echo "Slap recorded!";
    }
} else {
    echo "Error preparing query: " . pg_last_error($conn);
}

pg_close($conn);
?>



