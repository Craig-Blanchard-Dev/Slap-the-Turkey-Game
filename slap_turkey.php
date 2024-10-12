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

    // Proceed with your logic
    $totalQuery = "SELECT SUM(slap_count) AS total FROM slaps";
    $result = pg_query($conn, $totalQuery);
    if (!$result) {
        throw new Exception("Query error: " . pg_last_error());
    }

    $row = pg_fetch_assoc($result);
    $totalSlaps = $row['total'];

    echo json_encode([
        'status' => 'success',
        'total' => $totalSlaps
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

pg_close($conn);
?>
