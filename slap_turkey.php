<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Use your Heroku DATABASE_URL
    $db_url = getenv('DATABASE_URL');
    if (!$db_url) {
        throw new Exception("No DATABASE_URL found.");
    }

    $db_opts = parse_url($db_url);

    // Establish connection
    $conn = pg_connect(
        "host={$db_opts['host']} " .
        "port={$db_opts['port']} " .
        "user={$db_opts['user']} " .
        "password={$db_opts['pass']} " .
        "dbname=" . ltrim($db_opts['path'], '/')
    );

    if (!$conn) {
        throw new Exception("Database connection error: " . pg_last_error());
    }

    // Get the POST data
    $username = $_POST['username'] ?? null;
    $slap_count = (int) ($_POST['slap_count'] ?? 0);

    if (!$username || $slap_count <= 0) {
        throw new Exception("Invalid data provided.");
    }

    // Check if the user already exists
    $userQuery = "SELECT slap_count FROM slaps WHERE username = $1";
    $result = pg_query_params($conn, $userQuery, [$username]);

    if ($result && pg_num_rows($result) > 0) {
        // If the user exists, update their slap count
        $row = pg_fetch_assoc($result);
        $newSlapCount = $row['slap_count'] + $slap_count;

        $updateQuery = "UPDATE slaps SET slap_count = $1 WHERE username = $2";
        pg_query_params($conn, $updateQuery, [$newSlapCount, $username]);
    } else {
        // If the user doesn't exist, insert a new record
        $insertQuery = "INSERT INTO slaps (username, slap_count) VALUES ($1, $2)";
        pg_query_params($conn, $insertQuery, [$username, $slap_count]);
    }

    // Fetch the updated total slaps
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    $result = pg_query($conn, $totalQuery);
    
    if (!$result) {
        throw new Exception("Failed to fetch total slaps: " . pg_last_error($conn));
    }

    $row = pg_fetch_assoc($result);
    $totalSlaps = $row['total'];

    // Return the updated total slaps and user's slap count
    echo json_encode([
        'status' => 'success',
        'total' => $totalSlaps,
        'username' => $username,
        'user_slaps' => $newSlapCount ?? $slap_count
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

pg_close($conn);
?>
