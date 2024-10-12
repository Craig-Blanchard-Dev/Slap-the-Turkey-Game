<?php
// Include the database connection (assuming db_connect.php contains your pg_connect setup)
include 'db_connect.php'; // Ensure this file has the PostgreSQL connection

if (!$conn) {
    die("Database connection error: " . pg_last_error());
}

// Fetch total slaps (sum the total_slaps column)
$totalQuery = "SELECT SUM(total_slaps) AS total FROM slaps";
$result = pg_query($conn, $totalQuery);
if (!$result) {
    echo "An error occurred when fetching total slaps.\n";
    exit;
}
$row = pg_fetch_assoc($result);
$totalSlaps = $row['total'];

// Fetch individual slap counts for each user
$usersQuery = "SELECT username, slap_count FROM slaps";
$result = pg_query($conn, $usersQuery);
if (!$result) {
    echo "An error occurred when fetching individual slaps.\n";
    exit;
}

$users = [];
while ($row = pg_fetch_assoc($result)) {
    $users[] = $row;
}

// Send data back as JSON
echo json_encode([
    'total' => $totalSlaps,
    'users' => $users
]);

// Close the connection
pg_close($conn);
?>
