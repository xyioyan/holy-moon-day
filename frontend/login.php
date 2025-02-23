<?php
// Database connection details
$host = 'localhost';
$db = 'ids_system';
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
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $count = 0;
    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
    // Query to check user credentials
    $query1 = "SELECT * FROM users WHERE `password`='$password'";
    $result1 = $conn->query($query1);

        if ($result1->num_rows > 0){

        
        // The login details are stored in logs
        $stmt = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('User loged in as $username', '$ip_address','LOGIN SUCCESS', 'Low')");
       $stmt->execute();

        
        // Redirect to dashboard
        header('Location: dashboard1.html');
    } else {
        echo "<script> alert('You entered wrong username');</script>";
        // The login details are stored in logs
        $stmt = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('User log in Failed while log in as `$username` for wrong password', '$ip_address','LOGIN FAILD', 'Medium')");
       $stmt->execute();
       
       // Redirect to login
       header('Location: login.html');
    }
}
else{
    echo "<script> alert('You entered wrong username');</script>";
    echo " <script> 
window.location.href = '../frontend/login.html';
</script>";
}
}

$conn->close();
?>
