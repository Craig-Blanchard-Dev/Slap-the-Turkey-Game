<?php
// Set error reporting (can be adjusted for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Include the database connection file
    include 'db_connect.php';

    // Check if the connection was successful
    if (!$conn) {
        throw new Exception("Failed to connect to the database.");
    }

    // Log that connection is open
    error_log("PostgreSQL connection opened.");

    // Query to fetch the total number of slaps from the database
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    
    if (!$conn) throw new Exception("Connection closed unexpectedly.");
    $totalResult = pg_query($conn, $totalQuery);

    // Check if the query was successful
    if (!$totalResult) {
        throw new Exception("Error fetching total slaps: " . pg_last_error($conn));
    }

    // Get the total slaps count from the query result
    $row = pg_fetch_assoc($totalResult);
    $totalSlaps = isset($row['total']) ? (int) $row['total'] : 0;

    // Query to fetch individual slap counts per user
    $usersQuery = "SELECT username, slap_count FROM slaps";
    
    if (!$conn) throw new Exception("Connection closed unexpectedly.");
    $userResult = pg_query($conn, $usersQuery);

    // Check if the query was successful
    if (!$userResult) {
        throw new Exception("Error fetching user slaps: " . pg_last_error($conn));
    }

    // Collect all user data (username and slap count)
    $users = [];
    while ($row = pg_fetch_assoc($userResult)) {
        $users[] = [
            'username' => $row['username'],
            'slap_count' => (int) $row['slap_count']
        ];
    }

    // Create the response array
    $response = [
        'status' => 'success',
        'total' => $totalSlaps,
        'users' => $users
    ];

    // Send the JSON response
    echo json_encode($response);

} catch (Exception $e) {
    // If an error occurs, send a 500 response and return the error message as JSON
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

} finally {
    // Always close the database connection after all queries
    if (isset($conn)) {
        error_log('Closing PostgreSQL connection.');  // Log when the connection is closed
        pg_close($conn);
    }
}
