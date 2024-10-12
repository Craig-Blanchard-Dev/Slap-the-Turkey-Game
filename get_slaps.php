<?php
// Enable error reporting, but don't output it as HTML
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the header to ensure JSON is always returned
header('Content-Type: application/json');

try {
    // Database connection (replace with your actual database connection logic)
    include 'db_connect.php';  // This should be your actual database connection file

    // Fetch the total number of slaps from the database
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    $result = pg_query($conn, $totalQuery);

    if (!$result) {
        throw new Exception("Failed to fetch total slaps from the database: " . pg_last_error($conn));
    }

    $row = pg_fetch_assoc($result);
    $totalSlaps = $row['total'];

    // Fetch individual slaps for each user
    $usersQuery = "SELECT username, slap_count FROM slaps";
    $result = pg_query($conn, $usersQuery);

    if (!$result) {
        throw new Exception("Failed to fetch user slaps from the database: " . pg_last_error($conn));
    }

    $users = [];
    while ($row = pg_fetch_assoc($result)) {
        $users[] = ['username' => $row['username'], 'slap_count' => $row['slap_count']];
    }

    // Send the successful JSON response with the real data
    $response = [
        'status' => 'success',
        'total' => $totalSlaps,
        'users' => $users
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // If an error occurs, return the error as JSON
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
