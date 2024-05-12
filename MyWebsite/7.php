<?php
// Start session
session_start();



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
$book_id = 7; // Change this to match the corresponding book ID

// Prepare and execute SQL query
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch book data
if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    // Round the rating to the nearest integer
    $rating = round($book['rating']);
} else {
    // Handle case where book is not found
    echo "Book not found!";
    exit;
}

// Close statement
$stmt->close();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $book['title'] ?> - Book Review Website</title>
    <link rel="stylesheet" type="text/css" href="Css/book.css">
    <script src="js/book.js"></script> <!-- Include your JavaScript file here -->
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="reviews.html">Book Reviews</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <div class="book-container">
            <div class="book-image-container">
                <img class="book-image" src="<?= $book['imageurl'] ?>" alt="<?= $book['title'] ?>">
                <!-- Star rating system -->
                <div class="star-rating">
                    <?php
                    // Display star rating based on rounded rating
                    if (isset($rating)) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo "★";
                            } else {
                                echo "☆";
                            }
                        }
                    } else {
                        echo "Rating not available";
                    }
                    ?>
                </div>
                <?php if (isset($_SESSION["auth_token"])): ?>
                    <button class="rate-button" onclick="rateBook(<?= $book_id ?>)">Rate this book</button>
                <?php else: ?>
                    <button class="rate-button" onclick="window.location.href='login.php'">Login to Rate</button>
                <?php endif; ?>
            </div>
            <div class="book-info">
                <h2 class="book-title"><?= $book['title'] ?></h2>
                <p class="book-author"><?= $book['authorname'] ?></p>
                <p class="book-rating">Rating: <?= isset($rating) ? $rating . "/5" : "N/A" ?></p>
                <p class="book-description"><?= $book['description'] ?></p>
                <p class="book-genres">Genres</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>© 2024 Book Review Website. All rights reserved.</p>
    </footer>
    <script>
    function rateBook(bookId) {
    var rating = prompt("Please enter your rating (0.0 to 5.0):");

    // Validate the rating
    if (rating !== null && rating !== "" && !isNaN(parseFloat(rating)) && isFinite(rating)) {
        // Send the rating to the server using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "rate_book.php", true); 
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Check the server response 
                if (xhr.responseText === "Rating submitted successfully!") {
                    alert("Rating submitted successfully!");
                    location.reload(); // Reload the page to update the rating 
                } else if (xhr.responseText === "You have already rated this book.") {
                    alert("You have already rated this book.");
                } else { 
                    // Handle other potential errors from the server 
                    alert("An error occurred while submitting your rating."); 
                }
            }
        };
        xhr.send("book_id=" + bookId + "&rating=" + rating); 
    } else {
        alert("Please enter a valid rating.");
    }
}
</script>
</body>
</html>
