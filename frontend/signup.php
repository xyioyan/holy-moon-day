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
    $name = mysqli_real_escape_string($conn, string: $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo " <script> if(confirm ('Username allready exist! Please choose another name')){
        window.location.href = 'signup.html';
        }
    else{window.location.href = 'signup.html';}
        </script>";

    }
    // Query to check user credentials
    $query = "SELECT * FROM users WHERE email='$name'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo " <script> if(confirm ('Email id allready exist! Please Login')){
        window.location.href = 'login.html';
        }
    else{window.location.href = 'signup.html';}
        </script>";

    }
    
    else {


    $insert_query = "INSERT INTO users (email,username, password) VALUES ('$name','$username','$password')";
    $result = $conn->query($query);

    if ($result = $conn->query($insert_query)===TRUE) {
        echo " <script> if(confirm('Success! Please Login again'))
    {
        window.location.href = 'login.html';
    }
        else{
        window.location.href = 'login.html';
    }
        </script>";
        // Redirect to dashboard
    } 
 }
}
$conn->close();
?>
