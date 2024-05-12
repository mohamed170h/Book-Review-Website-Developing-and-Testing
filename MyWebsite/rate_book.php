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

// Check if book_id and rating are set in the POST request
if (isset($_POST['book_id']) && isset($_POST['rating']) && isset($_COOKIE["auth_token"])) {
    // Sanitize and validate the input data
    $book_id = intval($_POST['book_id']);
    $rating = floatval($_POST['rating']);
    $token = $_COOKIE["auth_token"];

    if ($rating < 0 || $rating > 5) {
        // Rating out of range (0.0 to 5.0)
        echo "Invalid rating value.";
        exit;
    }

    // Get user ID from the token
    $stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Check if the user has already rated this book
        $stmt = $conn->prepare("SELECT id FROM user_ratings WHERE user_id = ? AND book_id = ?");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User has already rated this book
            echo "You have already rated this book.";
        } else {
            // Insert the rating into user_ratings
            $stmt = $conn->prepare("INSERT INTO user_ratings (user_id, book_id, rating) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $user_id, $book_id, $rating);
            $stmt->execute();

            // *** INSERT THE CODE SNIPPET HERE *** 
            // Fetch current rating from books table
            $stmt = $conn->prepare("SELECT rating FROM books WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $book = $result->fetch_assoc();
                $current_rating = $book['rating'];

                // Calculate the new average rating 
                $new_rating = ($current_rating + $rating) / 2;

                // Update the book's rating in the books table
                $stmt = $conn->prepare("UPDATE books SET rating = ? WHERE id = ?");
                $stmt->bind_param("di", $new_rating, $book_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "Rating submitted successfully!";
                } else {
                    echo "Failed to submit rating.";
                }
            } else {
                echo "Book not found!";
            }
            // *** END OF CODE SNIPPET ***

        } 
    } else {
        echo "User not authenticated."; 
    }   
} else {
    echo "Missing data."; 
}

// Close connection
$conn->close();
?>