<?php
// Set error reporting (can be adjusted for production, e.g., turn off display_errors)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize $data
$data = array();

// Set the content type to JSON for the response
header('Content-Type: application/json');
echo json_encode($data);

try {
    // Include the database connection file
    include 'db_connect.php';

    // Query to fetch the total number of slaps from the database
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    $result = pg_query($conn, $totalQuery);

    // Check if the query was successful
    if (!$result) {
        throw new Exception("Error fetching total slaps: " . pg_last_error($conn));
    }

    // Get the total slaps count from the query result
    $row = pg_fetch_assoc($result);
    $totalSlaps = (int) $row['total'];  // Ensure total slaps is an integer

    // Query to fetch individual slap counts per user
    $usersQuery = "SELECT username, slap_count FROM slaps";
    $result = pg_query($conn, $usersQuery);

    // Check if the query was successful
    if (!$result) {
        throw new Exception("Error fetching user slaps: " . pg_last_error($conn));
    }

    // Collect all user data (username and slap count)
    $users = [];
    while ($row = pg_fetch_assoc($result)) {
        $users[] = [
            'username' => $row['username'],       // Username of the player
            'slap_count' => (int) $row['slap_count'] // Ensure slap count is an integer
        ];
    }

    // Create the response array
    $response = [
        'status' => 'success',   // Indicate the operation was successful
        'total' => $totalSlaps,  // Total number of slaps
        'users' => $users        // List of users and their slap counts
    ];

    // Send the JSON response
    echo json_encode($response);

} catch (Exception $e) {
    // If an error occurs, send a 500 response and return the error message as JSON
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Always close the database connection after use
    pg_close($conn);
}
?>
