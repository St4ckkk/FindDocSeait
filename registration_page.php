<?php
include_once './core/userController.php';
$userController = new userController();
$offices = $userController->getOffices();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FindDocSEAIT - Register</title>

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="views/node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="views/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="views/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <style>
        body {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            background: none;
            color: #444444;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.5), rgba(0, 0, 0, 0.3)),
                url('assets/img/seait-logo.png') no-repeat center center fixed;
            background-size: cover;
            filter: blur(5px);
            z-index: -1;
            opacity: 0.9;
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
            animation: spin 5s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotateY(0deg);
            }

            100% {
                transform: rotateY(360deg);
            }
        }

        .header {
            background: rgb(226, 123, 24);
            padding: 15px 20px;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1;
        }

        .logo-text {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .search-bar {
            display: flex;
            gap: 5px;
        }

        .search-bar input {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
        }

        .search-bar button {
            padding: 5px 15px;
            background: white;
            border: none;
            border-radius: 3px;
            color: #333;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            padding-top: 60px;
            /* Adjust for header height */
        }

        .login-panel {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .form-title {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgb(226, 123, 24);
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            color: #333;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            padding: 6px 20px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-cancel {
            background: #f1f1f1;
            color: #333;
        }

        .btn-submit {
            background: rgb(226, 123, 24);
            color: white;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
        }

        .register-link a {
            color: rgb(226, 123, 24);
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo-text">
                FindDocSEAIT
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Tracking Number">
                <button>Search</button>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="login-panel">
            <div class="logo">
                <img src="assets/img/seait-logo.png" alt="SEAIT Logo">
            </div>
            <div class="form-title">Register</div>
            <form id="registerForm" action="register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">ID Number</label>
                    <input type="text" class="form-control" name="id_number" placeholder="ID Number" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-submit w-100">Register</button>
                </div>

                <div class="register-link">
                    <p>Already have an account? <a href="index.php" class="btn btn-register">Log In</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#registerForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Registration successful!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'login.php'; // Redirect to login
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Registration failed',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>