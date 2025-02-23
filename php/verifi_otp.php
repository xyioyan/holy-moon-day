<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);
    $new_password = $_POST['new_password'];

    try {
        $time = date("U");
        $stmt = $conn->prepare("SELECT email FROM otp_codes WHERE otp = :otp AND expires >= :time");
        $stmt->bindParam(':otp', $otp);
        $stmt->bindParam(':time', $time);
        $stmt->execute();
        $otp_entry = $stmt->fetch();

        if ($otp_entry) {
            var_dump( $otp_entry);
            $email = $otp_entry['email'];
            //upndate the password
            $stmt = $conn->prepare("UPDATE users SET password = :new_password WHERE email = :email");
            $stmt->bindParam(':new_password', $new_password);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            // check the password updated nor nnot
            if ($stmt->rowcount() > 0) {
            $stmt = $conn->prepare("DELETE FROM otp_codes WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo " <script> 
            alert ('Your password has been reset successfully.Please Login!')
        window.location.href = '../frontend/login.html';

        </script>";
       } else {
        echo 'Failed to update password. Please try again.';
       }
     } else {
            echo 'Invalid or expired OTP.';
        }
    } catch (Exception $e) {
        error_log('Error in verify_otp.php: ' . $e->getMessage());
        echo 'An error occurred. Please try again later.';
    }
}
?>
