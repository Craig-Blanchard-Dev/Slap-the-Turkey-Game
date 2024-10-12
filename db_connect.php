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

// Example query to verify the connection and fetch data from the 'slaps' table
$result = pg_query($conn, 'SELECT * FROM slaps');

// Loop through each row in the result and print it
while ($row = pg_fetch_assoc($result)) {
    print_r($row); // Output each row as an associative array
}

// Close the database connection when done
pg_close($conn);
?>
