<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="header">
        <div class="logo">
            <div class="logo-container">
                <img src="time.png" alt="Logo">
                <h1>Timepiece Treasures</h1>
            </div>
        </div>

        <div class="topnav">
            <a class="active" href="#home">Articles</a>
            <a href="#about">About</a>
            <a href="contact.php">Contact</a>
            <a href="home.php">Home</a>
        </div>
        <div class="user-actions">
            <div class="login-container">
                <a href="login_admin.php">Login</a>
            </div>
            <div class="signup-container">
                <a href="signup_admin.php">Sign Up</a>
            </div>
        </div>
    </div>

    <?php
    session_start();
    require_once "database.php"; // Include the database connection

    function log_admin_activity($conn, $admin_id, $action, $details) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $sql_log = "INSERT INTO admin_activity_logs (admin_id, action, details, ip_address, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param("isss", $admin_id, $action, $details, $ip_address);
        $stmt_log->execute();
    }

    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM administrators WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($password, $user["password"])) {
                // Set session and log successful login
                $_SESSION["user"] = "yes";
                $_SESSION["id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];  
                $_SESSION['last_login'] = time();

                // Log the successful login
                log_admin_activity($conn, $user["id"], "login_success", "Admin logged in successfully");

                // Redirect to the dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Log failed login due to incorrect password
                log_admin_activity($conn, $user["id"], "login_failed", "Incorrect password");
                echo "<div class='alert alert-danger'>Password does not match</div>";
            }
        } else {
            // Log failed login due to incorrect email
            log_admin_activity($conn, NULL, "login_failed", "Email not found");
            echo "<div class='alert alert-danger'>Email does not match</div>";
        }
    }
    ?>

    <div class="container">
        <div class="card">
            <div class="card2">
                <form class="form" action="login_admin.php" method="post">
                    <p id="heading">Login Admin</p>
                    <div class="field">
                        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
                            <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
                        </svg>
                        <input type="email" name="email" class="input-field" placeholder="Email..." autocomplete="off" required>
                    </div>
                    <div class="field">
                        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                        </svg>
                        <input type="password" class="input-field" name="password" placeholder="Password..." required>
                    </div>
                    <div class="btn">
                        <input type="submit" class="button1" value="Login" name="login">
                        <a href="signup_admin.php" class="button2">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
