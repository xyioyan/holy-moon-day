<?php
// Database connection details
$host = 'localhost';
$db = 'ids_db';
$user = 'root';
$pass = '';

// Establish database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username='$username' AND password=('$password')";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Login successful!";
        // Redirect to dashboard
        header('Location: dashboard.html');
    } else {
        echo "Invalid credentials.";
    }
}

$conn->close();
?>
