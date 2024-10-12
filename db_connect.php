<?php
// Set the Heroku DATABASE_URL (this should be stored in an environment variable in production)
$db_url = 'postgres://u2eo2h512h0v2v:pa2017cd9fccf9241fea590ecd928cbead4cac455db11ec3d17a7cf08fb656c82@c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com:5432/dfq54pj7m0bvn4';

// Parse the database URL to extract connection details
$db_opts = parse_url($db_url);

// Establish a connection to the PostgreSQL database
$conn = pg_connect(
    "host={$db_opts['host']} " .     // Database host
    "port={$db_opts['port']} " .     // Database port
    "user={$db_opts['user']} " .     // Database username
    "password={$db_opts['pass']} " . // Database password
    "dbname=" . ltrim($db_opts['path'], '/') // Database name
);

// Check if the connection was successful
if (!$conn) {
    // If the connection fails, output an error and exit the script
    die("An error occurred while connecting to the database.");
}

// At this point, the connection is established, and you can perform queries
// The connection will remain open and should be closed in the main script when necessary.
?>
