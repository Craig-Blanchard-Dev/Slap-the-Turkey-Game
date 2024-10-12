<?php
// Enable error reporting, but don't output it as HTML
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the header to ensure JSON is always returned
header('Content-Type: application/json');

try {
    // Your code to fetch slaps data from the database
    // For example:
    $response = ['status' => 'error', 'message' => 'An error occurred'];

    // Simulate data fetching from a database or other source
    // Replace this part with your actual database logic
    $username = 'test_user';  // This should come from the database
    $slap_count = 50;         // Example slap count

    // Example successful response
    $response = [
        'status' => 'success',
        'total' => 1000,  // Example total slaps
        'users' => [
            ['username' => $username, 'slap_count' => $slap_count]
        ]
    ];

    // Send the JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // If an error occurs, return the error as JSON
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
