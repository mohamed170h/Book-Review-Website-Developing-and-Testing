<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="CSS/login.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>

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

            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get form data and sanitize
                $username = htmlspecialchars($_POST["username"]);
                $password = htmlspecialchars($_POST["password"]);

                // Prepare and execute SQL statement to retrieve user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if user exists
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $hashed_password = $row['password'];

                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Generate a random token
                        $token = bin2hex(random_bytes(16));

                        // Store the token in the database for the user
                        $stmt = $conn->prepare("UPDATE users SET token = ? WHERE username = ?");
                        $stmt->bind_param("ss", $token, $username);
                        $stmt->execute();

                        // Set a cookie with the token
                        setcookie("auth_token", $token, time() + (86400 * 30), "/"); // 30 days expiration

                        // Redirect to the reviews page
                        header("Location: reviews.html");
                        exit; // Stop further execution
                    } else {
                        echo "Incorrect username or password.";
                    }
                } else {
                    echo "Incorrect username or password.";
                }

                $stmt->close();
            }

            $conn->close();
            ?>

            <form method="post">
                <input class="form-input" type="text" name="username" placeholder="Username" required>
                <input class="form-input" type="password" name="password" placeholder="Password" required>
                <input class="form-submit" type="submit" value="Login">
            </form>
        </div>
    </div>
</body>
</html>
