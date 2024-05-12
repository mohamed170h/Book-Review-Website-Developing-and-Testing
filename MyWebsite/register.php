<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="Css/login.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Register</h2>

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

            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get form data and sanitize
                $username = htmlspecialchars($_POST["username"]);
                $email = htmlspecialchars($_POST["email"]);
                $password = htmlspecialchars($_POST["password"]);

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare and execute SQL statement
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    echo "Registration successful!";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }

            $conn->close();
            ?>

            <form method="post">
                <input class="form-input" type="text" name="username" placeholder="Username" required>
                <input class="form-input" type="email" name="email" placeholder="Email" required>
                <input class="form-input" type="password" name="password" placeholder="Password" required>
                <input class="form-submit" type="submit" value="Register">
            </form>
        </div>
    </div>
</body>
</html>