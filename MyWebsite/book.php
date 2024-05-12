<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reviews";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get book ID from URL parameter
$book_id = $_GET["id"];

// Construct the filename for inclusion (vulnerable to directory traversal)
$filename_with_php = $book_id . ".php";

// Check if the file exists with .php extension
if (file_exists($filename_with_php)) {
    include($filename_with_php);
} else {
    // Remove the .php extension
    $filename_without_php = str_replace('.php', '', $filename_with_php);

    // Directly include the file without proper validation (vulnerable to directory traversal)
    include($filename_without_php);
}

// Close connection


?>
