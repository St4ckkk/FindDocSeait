<?php
include_once 'Database.php';
include_once 'VirtualIPManager.php';
require 'vendor/autoload.php'; // Include Composer's autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use Mobile_Detect;
// use Jenssegers\Agent\Agent;

class sessionController
{
    private $db;
    private $ga;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ga = new PHPGangsta_GoogleAuthenticator();
    }

    // Generate CSRF token
    private function generateCsrfToken()
    {
        return bin2hex(random_bytes(32)); // Generate a 64-character token
    }

    // Store CSRF token in both session and database
    private function storeCsrfToken($userId, $token)
    {
        try {
            // Store in session
            $_SESSION['csrf_token'] = $token;

            // Store in database
            $query = "UPDATE users SET csrf_token = :token, token_timestamp = NOW() WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':token' => $token,
                ':user_id' => $userId
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error storing CSRF token: " . $e->getMessage());
            return false;
        }
    }

    // Verify CSRF token
    public function verifyCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private function logLoginAttempt($userId, $status, $riskLevel)
    {
        try {
            $ipManager = new VirtualIPManager();
            $virtualIp = $ipManager->assignVirtualIP($userId);
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceType = 'unknown'; // You can use a library to detect the device type
            $osType = 'unknown'; // You can use a library to detect the OS type
            $browserType = 'unknown'; // You can use a library to detect the browser type

            $query = "INSERT INTO login_logs (user_id, ip_address, user_agent, status, risk_level, device_type, os_type, browser_type) 
                  VALUES (:user_id, :ip_address, :user_agent, :status, :risk_level, :device_type, :os_type, :browser_type)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':ip_address' => $virtualIp,
                ':user_agent' => $userAgent,
                ':status' => $status,
                ':risk_level' => $riskLevel,
                ':device_type' => $deviceType,
                ':os_type' => $osType,
                ':browser_type' => $browserType
            ]);
        } catch (Exception $e) {
            error_log("Log Login Attempt Error: " . $e->getMessage());
        }
    }

    // Modified login method to include CSRF token generation
    public function login($username, $password)
    {
        try {
            $query = "SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.role_id WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Generate new CSRF token
                $csrfToken = $this->generateCsrfToken();

                // Store token in session and database
                if (!$this->storeCsrfToken($user['id'], $csrfToken)) {
                    throw new Exception("Failed to store CSRF token");
                }

                session_start();
                // Set other session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['authorized'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name']; // Add role_name to session
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['office_id'] = $user['office_id'];
                $_SESSION['otp_verified'] = $user['otp_verified'];
                $_SESSION['csrf_token'] = $csrfToken; // Use the newly generated token

                // Check if OTP verification is required
                if (!$user['otp_verified']) {
                    $_SESSION['otp'] = rand(100000, 999999);
                    error_log("Generated OTP: " . $_SESSION['otp']);
                    return [
                        'status' => 'otp_required',
                        'message' => 'OTP required for first-time login',
                        'csrf_token' => $csrfToken
                    ];
                }

                // Log the login attempt
                $this->logLoginAttempt($user['id'], 'success', 'low');

                return [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'csrf_token' => $csrfToken
                ];
            }

            // Log the failed login attempt
            $this->logLoginAttempt(null, 'error', 'high');

            return ['status' => 'error', 'message' => 'Invalid username or password'];

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
            $query = "SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.role_id WHERE passkey IS NOT NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as $user) {
                if (password_verify($passkey, $user['passkey'])) {
                    $csrfToken = $this->generateCsrfToken();

                    // Store token in session and database
                    if (!$this->storeCsrfToken($user['id'], $csrfToken)) {
                        throw new Exception("Failed to store CSRF token");
                    }
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['role_name'] = $user['role_name']; // Add role_name to session
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['office_id'] = $user['office_id'];
                    $_SESSION['otp_verified'] = $user['otp_verified'];
                    $_SESSION['csrf_token'] = $csrfToken;

                    // Generate new CSRF token
                    $csrfToken = $this->generateCsrfToken();
                    $this->storeCsrfToken($user['id'], $csrfToken);

                    return ['status' => 'success', 'message' => 'Login successful', 'csrf_token' => $csrfToken];
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
        $userId = $_SESSION['user_id'] ?? null;

        // Unset session variables
        session_unset();
        session_destroy();

        // Clear the cookies
        if (isset($_COOKIE['logged_in'])) {
            setcookie('logged_in', '', time() - 3600, '/'); // Expire the cookie
        }

        // Remove CSRF token from the database
        if ($userId) {
            try {
                $query = "UPDATE users SET csrf_token = NULL, token_timestamp = NULL WHERE id = :user_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute([':user_id' => $userId]);
            } catch (Exception $e) {
                error_log("Error removing CSRF token: " . $e->getMessage());
            }
        }
    }

    public function enableGoogleAuthenticator($email)
    {
        try {
            $secret = $this->ga->createSecret();
            $qrCodeUrl = $this->ga->getQRCodeGoogleUrl('FindDocSEAIT', $secret);

            // Store the secret in the database
            $query = "UPDATE users SET google_auth_secret = :secret WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':secret', $secret);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return ['status' => 'success', 'secret' => $secret, 'qrCodeUrl' => $qrCodeUrl];
        } catch (Exception $e) {
            error_log("Enable Google Authenticator Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while enabling Google Authenticator'];
        }
    }

    public function generateGoogleAuthSecret($email)
    {
        try {
            $secret = $this->ga->createSecret();
            $qrCodeUrl = $this->ga->getQRCodeGoogleUrl('FindDocSEAIT', $secret);

            // Store the secret in the database
            $query = "UPDATE users SET google_auth_secret = :secret WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':secret', $secret);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return ['status' => 'success', 'secret' => $secret, 'qrCodeUrl' => $qrCodeUrl];
        } catch (Exception $e) {
            error_log("Generate Google Authenticator Secret Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while generating Google Authenticator secret'];
        }
    }

    public function verifyGoogleAuthenticator($userId, $code)
    {
        try {
            // Retrieve the secret from the database
            $query = "SELECT google_auth_secret FROM users WHERE google_auth_secret IS NOT NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as $user) {
                if ($this->ga->verifyCode($user['google_auth_secret'], $code, 2)) {
                    return ['status' => 'success', 'message' => 'Google Authenticator verified successfully'];
                }
            }
            return ['status' => 'error', 'message' => 'Invalid Google Authenticator code'];
        } catch (Exception $e) {
            error_log("Verify Google Authenticator Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while verifying Google Authenticator'];
        }
    }

    public function loginWithGoogleAuthenticator($email, $code)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email AND google_auth_secret IS NOT NULL";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $this->ga->verifyCode($user['google_auth_secret'], $code, 2)) {
                $csrfToken = $this->generateCsrfToken();

                // Store token in session and database
                if (!$this->storeCsrfToken($user['id'], $csrfToken)) {
                    throw new Exception("Failed to store CSRF token");
                }
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['office_id'] = $user['office_id'];
                $_SESSION['otp_verified'] = $user['otp_verified'];
                $_SESSION['csrf_token'] = $csrfToken;

                // Generate new CSRF token
                $csrfToken = $this->generateCsrfToken();
                $this->storeCsrfToken($user['id'], $csrfToken);

                return ['status' => 'success', 'message' => 'Login successful', 'csrf_token' => $csrfToken];
            } else {
                return ['status' => 'error', 'message' => 'Invalid email or Google Authenticator code'];
            }
        } catch (Exception $e) {
            error_log("Login with Google Authenticator Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred during login with Google Authenticator'];
        }
    }

    public function verifyGoogleAuthenticatorLogin($email, $code)
    {
        try {
            // Retrieve the user by email
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['google_auth_secret']) {
                    $secret = $user['google_auth_secret'];
                    error_log("Secret: " . $secret);
                    error_log("Code: " . $code);
                    $isValid = $this->ga->verifyCode($secret, $code, 2); // 2 = 2*30sec clock tolerance

                    if ($isValid) {
                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['username'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $_SESSION['fullname'] = $user['fullname'];
                        $_SESSION['office_id'] = $user['office_id'];
                        $_SESSION['otp_verified'] = $user['otp_verified'];
                        $_SESSION['csrf_token'] = $user['csrf_token'];

                        // Generate new CSRF token
                        $csrfToken = $this->generateCsrfToken();
                        $this->storeCsrfToken($user['id'], $csrfToken);

                        return ['status' => 'success', 'message' => 'Google Authenticator verified successfully', 'csrf_token' => $csrfToken];
                    } else {
                        return ['status' => 'error', 'message' => 'Invalid Google Authenticator code'];
                    }
                } else {
                    return ['status' => 'error', 'message' => 'Google Authenticator not enabled for this user'];
                }
            } else {
                return ['status' => 'error', 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            error_log("Login with Google Authenticator Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred during login with Google Authenticator'];
        }
    }
}
?>