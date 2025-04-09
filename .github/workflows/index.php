<?php
// Start session
session_start();

// Database connection (replace with your own database credentials)
$servername = "localhost";
$username = "users";
$password = "";
$dbname = "student_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === "signup" && isset($_POST['name'], $_POST['email'], $_POST['password'])) {
        // Handle Sign-Up
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            // Set session variables
            $_SESSION['user_id'] = $stmt->insert_id; // Get the last inserted ID
            $_SESSION['name'] = $name;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === "login" && isset($_POST['email'], $_POST['password'])) {
        // Handle Login
        $loginEmail = $conn->real_escape_string($_POST['email']);
        $loginPassword = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $loginEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($loginPassword, $row['password'])) {
                // Password is correct, start a session
                $_SESSION['user'] = $row;

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "No user found with that email.";
        }
        $stmt->close();
    } else {
        echo "Invalid action or missing fields.";
    }
}

// Close the database connection
$conn->close();
?>