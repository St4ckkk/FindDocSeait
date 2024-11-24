<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FindDocSEAIT - Document Tracking System</title>

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="views/node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="views/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="views/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


</head>
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
        animation: spinY 5s linear infinite;
    }

    @keyframes spinY {
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

    .g-recaptcha {
        transform: scale(0.9);
        transform-origin: 0 0;
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

    .btn-submit:hover {
        background: rgb(226, 123, 24);
    }

    .btn-passkey {
        background-color: #f6f8fa;
        border: 1px solid rgba(27, 31, 35, 0.15);
        color: #24292e;
    }

    .btn-passkey:hover {
        background-color: #e1e4e8;
    }

    .register-link {
        text-align: center;
        margin-top: 10px;
    }

    .register-link a {
        color: rgb(226, 123, 24);
        text-decoration: none;
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 20px 0;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid gray;
    }

    .divider:not(:empty)::before {
        margin-right: 1em;
    }

    .divider:not(:empty)::after {
        margin-left: 1em;
    }

    .swal2-container {
        z-index: 9999;
    }
</style>

<body>

    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo-text">FindDocSEAIT</div>
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
            <div class="form-title">Log In</div>
            <form id="loginForm" action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LdjcIMqAAAAANy9mqljGZhmNpJjfvBYM7s7N6jP"></div>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-submit w-100">Sign in</button>
                </div>
                <div class="register-link">
                    <p>Don't have an account? <a href="registration_page.php" class="btn btn-register">Register</a></p>
                </div>
            </form>
            <div class="divider">or</div>
            <div class="button-group mt-3">
                <button id="passkeyLoginBtn" class="btn btn-passkey w-100">Login with Passkey</button>
            </div>
            <div class="button-group mt-3">
                <button id="generateGoogleAuthBtn" class="btn btn-info w-100">Login using Google Authenticator</button>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpForm">
                        <div class="form-group">
                            <label class="form-label">OTP</label>
                            <input type="text" class="form-control" name="otp" id="otpInput" placeholder="Enter OTP"
                                required>
                        </div>
                        <div class="button-group mt-3">
                            <button type="submit" class="btn btn-submit w-100">Verify OTP</button>
                        </div>
                    </form>
                    <button id="getOtpBtn" class="btn btn-info mt-3 w-100">Get OTP</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Passkey Modal -->
    <div class="modal fade" id="passkeyModal" tabindex="-1" aria-labelledby="passkeyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passkeyModalLabel">Enter Passkey</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="passkeyForm">
                        <div class="form-group">
                            <label class="form-label">Passkey</label>
                            <input type="text" class="form-control" name="passkey" id="passkeyInput"
                                placeholder="Enter Passkey" required>
                        </div>
                        <div class="button-group mt-3">
                            <button type="submit" class="btn btn-submit w-100">Verify Passkey</button>
                        </div>
                    </form>
                    <button id="getPasskeyBtn" class="btn btn-info mt-3 w-100">Email Passkey</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Enter Your Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="emailForm">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="emailInput"
                                placeholder="Enter your email" required>
                        </div>
                        <div class="button-group mt-3">
                            <button type="submit" class="btn btn-submit w-100">Submit</button>
                        </div>
                    </form>
                    <div id="loader" class="loader" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Authenticator Modal -->
    <div class="modal fade" id="googleAuthModal" tabindex="-1" aria-labelledby="googleAuthModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="googleAuthModalLabel">Google Authenticator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="qrCodeContainer" style="text-align: center; margin-bottom: 20px;"></div>
                    <form id="googleAuthForm">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Authenticator Code</label>
                            <input type="text" class="form-control" name="authCode"
                                placeholder="Enter Authenticator Code" required>
                        </div>
                        <div class="button-group mt-3">
                            <button type="submit" class="btn btn-submit w-100">Verify Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function resetRecaptcha() {
                grecaptcha.reset();
            }

            $('#loginForm').on('submit', function (e) {
                e.preventDefault();

                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please complete the reCAPTCHA verification.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                const formData = $(this).serialize() + '&g-recaptcha-response=' + recaptchaResponse;

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'otp_required') {
                            $('#otpModal').modal('show');
                        } else if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Login successful!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'views/dashboard.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Invalid credentials',
                                confirmButtonColor: '#d33'
                            });
                        }
                        resetRecaptcha();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                        resetRecaptcha();
                    }
                });
            });

            $('#otpForm').on('submit', function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'OTP verified successfully!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'views/dashboard.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Invalid OTP',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            $('#getOtpBtn').on('click', function () {
                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: { action: 'getOtp' },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Your OTP',
                                text: 'Your OTP is: ' + response.otp,
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                $('#otpInput').val(response.otp);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to get OTP',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            $('#passkeyLoginBtn').on('click', function () {
                $('#passkeyModal').modal('show');
            });

            $('#passkeyForm').on('submit', function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: 'login_passkey.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Passkey verified successfully!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'views/dashboard.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Invalid Passkey',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            $('#getPasskeyBtn').on('click', function () {
                $('#emailModal').modal('show');
            });

            $('#emailForm').on('submit', function (e) {
                e.preventDefault();

                const email = $('#emailInput').val();
                $('#loader').show(); // Show loader

                $.ajax({
                    url: 'send_passkey.php',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        $('#loader').hide(); // Hide loader
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Passkey',
                                text: 'Your passkey has been emailed to you.',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to email passkey',
                                confirmButtonColor: '#d33'
                            });
                        }
                        $('#emailModal').modal('hide');
                        $('.btn-close').focus(); // Move focus to another element
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        $('#loader').hide(); // Hide loader
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                        $('#emailModal').modal('hide');
                        $('.btn-close').focus(); // Move focus to another element
                    }
                });
            });

            $('#googleAuthForm').on('submit', function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: 'verify_google_auth_login.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Google Authenticator verified successfully!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'views/dashboard.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Invalid Authenticator Code',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            $('#generateGoogleAuthBtn').on('click', function () {
                $('#googleAuthModal').modal('show');
                generateGoogleAuthSecret();
            });

            function generateGoogleAuthSecret() {
                const email = $('input[name="email"]').val();

                $.ajax({
                    url: 'generate_google_auth_secret.php',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#qrCodeContainer').html('<img src="' + response.qrCodeUrl + '" alt="QR Code">');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to generate Google Authenticator secret',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    </script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>


</html>