<?php
// Error reporting settings for production (display off, log errors)
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Turn off error display for security
ini_set('log_errors', 1);      // Log errors to the error log file

// Set the response type to JSON
header('Content-Type: application/json');

try {
    // Get the Heroku DATABASE_URL environment variable
    $db_url = getenv('DATABASE_URL');
    if (!$db_url) {
        throw new Exception("No DATABASE_URL found.");
    }

    // Parse the database URL into its components
    $db_opts = parse_url($db_url);

    // Establish connection to PostgreSQL database
    $conn = pg_connect(
        "host={$db_opts['host']} " .
        "port={$db_opts['port']} " .
        "user={$db_opts['user']} " .
        "password={$db_opts['pass']} " .
        "dbname=" . ltrim($db_opts['path'], '/')
    );

    // Check if the connection was successful
    if (!$conn) {
        throw new Exception("Database connection error: " . pg_last_error());
    }

    // Retrieve POST data
    $username = $_POST['username'] ?? null;  // Username provided by the user
    $slap_count = (int) ($_POST['slap_count'] ?? 0);  // Slap count from POST, default to 0

    // Validate input data
    if (!$username || $slap_count <= 0) {
        throw new Exception("Invalid data provided.");
    }

    // Check if the user already exists in the database
    $userQuery = "SELECT slap_count FROM slaps WHERE username = $1";
    $result = pg_query_params($conn, $userQuery, [$username]);

    if ($result && pg_num_rows($result) > 0) {
        // User exists, update their slap count
        $row = pg_fetch_assoc($result);
        $newSlapCount = $row['slap_count'] + $slap_count;

        // Update the existing record with the new slap count
        $updateQuery = "UPDATE slaps SET slap_count = $1 WHERE username = $2";
        pg_query_params($conn, $updateQuery, [$newSlapCount, $username]);
    } else {
        // User doesn't exist, insert a new record
        $insertQuery = "INSERT INTO slaps (username, slap_count) VALUES ($1, $2)";
        pg_query_params($conn, $insertQuery, [$username, $slap_count]);
        $newSlapCount = $slap_count;  // New user's slap count
    }

    // Fetch the updated total number of slaps across all users
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    $result = pg_query($conn, $totalQuery);
    
    // Check if the query was successful
    if (!$result) {
        throw new Exception("Failed to fetch total slaps: " . pg_last_error($conn));
    }

    // Get the total slaps from the result
    $row = pg_fetch_assoc($result);
    $totalSlaps = $row['total'];

    // Return the updated total slaps and the user's current slap count
    echo json_encode([
        'status' => 'success',         // Status of the operation
        'total' => $totalSlaps,        // Total number of slaps
        'username' => $username,       // Current user's username
        'user_slaps' => $newSlapCount  // Updated slap count for the user
    ]);

} catch (Exception $e) {
    // Handle any errors and return a JSON error response
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Close the database connection when finished
    pg_close($conn);
}
?>
