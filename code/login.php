<?php
session_start();
require_once "database.php";
require_once "VirtualIPManager.php";

$virtualIPManager = new VirtualIPManager($conn);

// Security Headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:;");

// Configuration
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_TIME = 900; // 15 minutes in seconds
const TOKEN_EXPIRY = 1800; // 30 minutes in seconds

// Function to check if user has any blocked IPs
function hasBlockedIPs($conn, $email) {
    $query = "SELECT DISTINCT ip.ip_address 
              FROM users u 
              JOIN login_logs ll ON u.id = ll.user_id 
              JOIN ip_blocklist ip ON ll.ip_address = ip.ip_address 
              WHERE u.email = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// Check if the user's current IP is blocked
function isCurrentIPBlocked($conn, $ipAddress) {
    $stmt = $conn->prepare("SELECT 1 FROM ip_blocklist WHERE ip_address = ?");
    $stmt->bind_param("s", $ipAddress);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Get the user's IP address
$currentIPAddress = $_SERVER['REMOTE_ADDR'];
if (isCurrentIPBlocked($conn, $currentIPAddress)) {
    header("Location: haha.php");
    exit();
}

// Initialize session variables if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || 
        $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        die('CSRF token validation failed');
    }
}
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Rate Limiting Check
function checkRateLimit() {
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $time_passed = time() - $_SESSION['last_attempt_time'];
        if ($time_passed < LOCKOUT_TIME) {
            $wait_time = LOCKOUT_TIME - $time_passed;
            die("Too many login attempts. Please try again in " . ceil($wait_time/60) . " minutes.");
        } else {
            $_SESSION['login_attempts'] = 0;
        }
    }
}

// Login Process
if (isset($_POST["login"])) {
    checkRateLimit();

    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    
    // Check if user has any blocked IPs
    if (hasBlockedIPs($conn, $email)) {
        // Get or assign virtual IP for logging
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $virtual_ip = $virtualIPManager->assignVirtualIP($user_data['id']);
        
        // Log the blocked attempt with virtual IP
        $log_stmt = $conn->prepare("INSERT INTO login_logs (ip_address, status, risk_level) VALUES (?, 'blocked', 'high')");
        $log_stmt->bind_param("s", $virtual_ip);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: haha.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, password, full_name, account_status, last_password_change FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Account lock check
        if ($user['account_status'] === 'locked') {
            die("Account is locked. Please contact support.");
        }

        if (md5($password) === $user["password"]) {
            $_SESSION['login_attempts'] = 0;
            session_regenerate_id(true);

            // Assign or retrieve virtual IP
            $virtual_ip = $virtualIPManager->assignVirtualIP($user["id"]);
            
            // Check if the virtual IP is blocked
            if ($virtualIPManager->isIPBlocked($virtual_ip)) {
                // Log the blocked attempt
                $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, status, risk_level) 
                                          VALUES (?, ?, 'blocked', 'high')");
                $log_stmt->bind_param("is", $user["id"], $virtual_ip);
                $log_stmt->execute();
                $log_stmt->close();

                header("Location: haha.php");
                exit();
            }

            $_SESSION["user"] = "yes";
            $_SESSION["id"] = $user["id"];
            $_SESSION["name"] = $user["full_name"];
            $_SESSION['last_activity'] = time();
            $_SESSION['virtual_ip'] = $virtual_ip;

            // Log successful login with virtual IP
            $user_agent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
            $device_type = 'desktop';
            $os_type = 'Unknown';
            $browser_type = 'Unknown';

            $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, status, risk_level, device_type, os_type, browser_type) 
                                      VALUES (?, ?, ?, 'success', 'low', ?, ?, ?)");
            $log_stmt->bind_param("isssss", $user["id"], $virtual_ip, $user_agent, $device_type, $os_type, $browser_type);
            $log_stmt->execute();
            $log_stmt->close();

            header("Location: home.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();

            // Get virtual IP for failed attempt logging
            $virtual_ip = $virtualIPManager->getVirtualIP($user["id"]);
            if (!$virtual_ip) {
                $virtual_ip = $virtualIPManager->assignVirtualIP($user["id"]);
            }

            // Check if we should block the virtual IP
            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                $virtualIPManager->blockVirtualIP($virtual_ip, "Multiple failed login attempts");
                
                // Add to IP blocklist
                $block_stmt = $conn->prepare("INSERT INTO ip_blocklist (ip_address, blocked_by) VALUES (?, ?)");
                $blocked_by = $user["id"];
                $block_stmt->bind_param("si", $virtual_ip, $blocked_by);
                $block_stmt->execute();
                $block_stmt->close();
            }

            // Log failed attempt with virtual IP
            $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, status, risk_level) 
                                      VALUES (?, ?, 'failed', 'medium')");
            $log_stmt->bind_param("is", $user["id"], $virtual_ip);
            $log_stmt->execute();
            $log_stmt->close();

            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                header("Location: haha.php");
                exit();
            }

            echo "<div class='alert alert-danger'>Invalid credentials</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid credentials</div>";
    }
    
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-container">
                <img src="timepiece.jpg" alt="Logo">
                <div class="title">
                    <h1 style="font-size: 22px; letter-spacing: 2px;">Timepiece Treasures</h1>
                </div>
            </div>
        </div>

        <div class="topnav">
            <a class="active" href="#home">Articles</a>
            <a href="#about">About</a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Home</a>
        </div>
        <div class="user-actions">
            <div class="login-container">
                <a href="login.php">Login</a>
            </div>
            <div class="signup-container">
                <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card2">
                <form class="form" action="login.php" method="post">
                    <p id="heading">Login</p>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="field">
                        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
                            <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
                        </svg>
                        <input type="email" name="email" class="input-field" placeholder="Email..." autocomplete="off" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    </div>
                    <div class="field">
                        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                        </svg>
                        <input type="password" class="input-field" name="password" placeholder="Password..." required minlength="8">
                    </div>
                    <div class="btn">
                        <input type="submit" class="button1" value="Login" name="login">
                        <a href="signup.php" class="button2">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>