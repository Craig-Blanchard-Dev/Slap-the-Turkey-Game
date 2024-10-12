<?php
// Use your Heroku DATABASE_URL here
$db_url = 'postgres://u2eo2h512h0v2v:pa2017cd9fccf9241fea590ecd928cbead4cac455db11ec3d17a7cf08fb656c82@c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com:5432/dfq54pj7m0bvn4
';

// Parse the Heroku DATABASE_URL
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
    echo "An error occurred connecting to the database.\n";
    exit;
}

// Now your connection is ready, and you can perform queries here
// Example query to verify connection:
$result = pg_query($conn, 'SELECT * FROM slaps;');
while ($row = pg_fetch_assoc($result)) {
    print_r($row);
}

// Close the connection when done
pg_close($conn);
?>