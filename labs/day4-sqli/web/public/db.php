<?php
// Database connection helper — reads from environment variables
// set in docker-compose.yml.

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_USER = getenv('DB_USER') ?: 'q8user';
$DB_PASS = getenv('DB_PASS') ?: 'q8password';
$DB_NAME = getenv('DB_NAME') ?: 'q8portal';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
