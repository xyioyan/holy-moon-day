<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if ($user) {
                $otp = rand(100000, 999999);
                $expires = date('U',time() + 300); // OTP valid for 5 minutes

                $stmt = $conn->prepare("INSERT INTO otp_codes (email, otp, expires) VALUES (:email, :otp, :expires)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':otp', $otp);
                $stmt->bindParam(':expires', $expires);
                $stmt->execute();
                

                mail($email, 'Your OTP Code', "Your OTP code is: $otp");

                echo " <script> alert('Otp send successfully')
                        window.location.href = '../frontend/verifi_otp.html';
                        </script>";
            } else {
                echo 'Email not found.';
            }
        } catch (Exception $e) {
            error_log('Error in send_otp.php: ' . $e->getMessage());
            echo 'An error occurred. Please try again later.';
        }
    } else {
        echo 'Invalid email address.';
    }
}
?>
