<?php

// adjust credentials if needed
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cms');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    // stop script when DB unavailable
    die('Database connection error: ' . $mysqli->connect_error);
}
?>