<?php
include_once 'Database.php';

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