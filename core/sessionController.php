<?php
include_once 'Database.php';
require 'vendor/autoload.php'; // Include Composer's autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class sessionController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login($username, $password)
    {
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['office_id'] = $user['office_id'];
                $_SESSION['otp_verified'] = $user['otp_verified'];

                // Check if the user has completed OTP verification
                if (!$user['otp_verified']) {
                    // Generate a random OTP
                    $_SESSION['otp'] = rand(100000, 999999);

                    // For testing purposes, we can log the OTP to the console
                    error_log("Generated OTP: " . $_SESSION['otp']);

                    return ['status' => 'otp_required', 'message' => 'OTP required for first-time login'];
                } else {
                    return ['status' => 'success', 'message' => 'Login successful'];
                }
            } else {
                return ['status' => 'error', 'message' => 'Invalid username or password'];
            }
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred during login'];
        }
    }

    public function verifyOtp($otp)
    {
        try {
            session_start();
            if (isset($_SESSION['otp']) && $otp == $_SESSION['otp']) {
                $_SESSION['otp_verified'] = true;

                // Update the otp_verified flag in the database
                $query = "UPDATE users SET otp_verified = TRUE WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_SESSION['user_id']);
                $stmt->execute();

                return ['status' => 'success', 'message' => 'OTP verified successfully'];
            } else {
                return ['status' => 'error', 'message' => 'Invalid OTP'];
            }
        } catch (Exception $e) {
            error_log("OTP Verification Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred during OTP verification'];
        }
    }

    public function getOtp()
    {
        try {
            session_start();
            if (isset($_SESSION['otp'])) {
                return ['status' => 'success', 'otp' => $_SESSION['otp']];
            } else {
                return ['status' => 'error', 'message' => 'OTP not generated'];
            }
        } catch (Exception $e) {
            error_log("Get OTP Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while retrieving the OTP'];
        }
    }

    public function sendPasskey($email)
    {
        try {
            // Check if the email is connected to a user
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a random passkey in the format CODE-CODE-CODE
                $part1 = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);
                $part2 = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);
                $part3 = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);
                $passkey = $part1 . '-' . $part2 . '-' . $part3;

                // Encrypt the passkey before storing it in the database
                $encryptedPasskey = password_hash($passkey, PASSWORD_DEFAULT);

                // Insert the encrypted passkey into the users table
                $query = "UPDATE users SET passkey = :passkey WHERE email = :email";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':passkey', $encryptedPasskey);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                // Send the passkey via email using PHPMailer
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'tpas052202@gmail.com';
                    $mail->Password = 'ailamnlsomhhtglb';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('tpas052202@gmail.com', 'Administrator');
                    $mail->addAddress($email);
                    $mail->addReplyTo('tpas052202@gmail.com', 'Administrator');

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Passkey';
                    $mail->Body = 'Your passkey is: ' . $passkey;

                    $mail->send();
                    return ['status' => 'success', 'message' => 'Passkey sent successfully'];
                } catch (Exception $e) {
                    error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    error_log("PHPMailer Exception: " . $e->getMessage());
                    return ['status' => 'error', 'message' => 'Failed to send email. Please try again later.'];
                }
            } else {
                return ['status' => 'error', 'message' => 'Email not found'];
            }
        } catch (Exception $e) {
            error_log("Send Passkey Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while sending the passkey'];
        }
    }

    public function loginWithPasskey($passkey)
    {
        try {
            $query = "SELECT * FROM users WHERE passkey IS NOT NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as $user) {
                if (password_verify($passkey, $user['passkey'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['office_id'] = $user['office_id'];
                    $_SESSION['otp_verified'] = $user['otp_verified'];

                    return ['status' => 'success', 'message' => 'Login successful'];
                }
            }

            return ['status' => 'error', 'message' => 'Invalid passkey'];
        } catch (Exception $e) {
            error_log("Login with Passkey Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred during login with passkey'];
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        // Clear the cookies
        if (isset($_COOKIE['logged_in'])) {
            setcookie('logged_in', '', time() - 3600, '/'); // Expire the cookie
        }
    }
}
?>