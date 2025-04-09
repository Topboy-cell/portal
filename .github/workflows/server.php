<?php
// Start session
session_start();
// Database connection (replace with your own database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_portal";
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handle Sign Up
if ($_SERVER == "POST" && isset($_POST )) {
    $name = $_POST ;
    $email = $_POST ;
    $password = password_hash($_POST , PASSWORD_DEFAULT); // Hash the password
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        // Set session variables
        $_SESSION = $conn->insert_id; // Get the last inserted ID
        $_SESSION = $name;
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
// Handle Login
if ($_SERVER == "POST" && isset($_POST )) {
    $loginEmail = $_POST ;
    $loginPassword = $_POST ;
    $sql = "SELECT * FROM users WHERE email='$loginEmail'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($loginPassword, $row )) {
            // Password is correct, start a session
            $_SESSION = $row ;
            $_SESSION = $row ;
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "No user found with that email.";
    }
}
// Close the database connection
$conn->close();
?>


    
    