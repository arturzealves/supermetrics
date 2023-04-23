<?php

// if ($_REQUEST['email']) {
//     $masterEmail = $_REQUEST['email'];
// }
// $masterEmail = isset($masterEmail) && $masterEmail
//     ? $masterEmail
//     : array_key_exists('masterEmail', $_REQUEST) && $_REQUEST["masterEmail"]
//     ? $_REQUEST['masterEmail'] : 'unknown';

// The previous block of code can just be replaced with the following line
$masterEmail = $_REQUEST['email'] ?? ($_REQUEST['masterEmail'] ?? 'unknown');

echo 'The master email is ' . $masterEmail . '\n';

// These database credentials must be stored in a configuration file or on environment variables
// $conn = mysqli_connect('localhost', 'root', 'sldjfpoweifns', 'my_database');

// Here for instance, I'm retrieving the database credentials from environment variables
$conn = mysqli_connect(
    $_ENV['DB_HOSTNAME'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_NAME'],
);

// The following query is vulnerable to SQL injection
// $res = mysqli_query($conn, "SELECT * FROM users WHERE email='" . $masterEmail . "'");

// Using mysqli_real_escape_string to prevent SQL injection
$res = mysqli_query($conn, "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($conn, $masterEmail) . "'");

$row = mysqli_fetch_assoc($res);
// Checking if there was any record in the database
if ($row) {
    echo $row['username'] . "\n";
}

/**
 * Further refactor iterations can/should be made to the code above.
 * Here are some examples:
 * 1. Replacing mysqli functions with a PDO connection
 * 2. Using prepared statements in the SQL statement
 * 3. Using an ORM library such as Doctrine to abstract the database code
 * 4. Moving the database related code to a Repository class
 */
