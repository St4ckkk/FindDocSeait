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
    <link rel="stylesheet" href="index.css">
</head>
<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
        </div>
    </div>

    <div class="modal fade" id="confirmEmailModal" tabindex="-1" aria-labelledby="confirmEmailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmEmailModalLabel">Send OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you want to send OTP to this email: <span id="userEmail"></span>?</p>
                    <div id="otpLoader" class="loader" style="display: none;"></div> <!-- Loader element -->
                    <div class="button-group mt-3">
                        <button id="confirmSendOtpBtn" class="btn btn-submit w-100">Send OTP</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- OTP Input Modal -->
    <div class="modal fade" id="otpInputModal" tabindex="-1" aria-labelledby="otpInputModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpInputModalLabel">Enter OTP</h5>
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
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                // First, fetch the email based on the username
                $.ajax({
                    url: 'get_email.php', // Endpoint to get email by username
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#userEmail').text(response.email);

                            // Proceed with the existing login logic
                            $.ajax({
                                url: $('#loginForm').attr('action'),
                                type: 'POST',
                                data: formData,
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status === 'otp_required') {
                                        $('#confirmEmailModal').modal('show'); // Corrected ID
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
                                    } else if (response.status === 'error' && response.message === 'Account locked due to too many failed login attempts') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Account Locked',
                                            text: response.message,
                                            confirmButtonColor: '#d33'
                                        });
                                    } else if (response.status === 'error' && response.message === 'Your IP address is blocked.') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Blocked',
                                            text: response.message,
                                            confirmButtonColor: '#d33'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message || 'Invalid credentials',
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to fetch email',
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

            $('#confirmSendOtpBtn').on('click', function () {
                $('#confirmEmailModal').modal('hide');
                $('#otpLoader').show(); // Show loader
                $('#otpForm').hide(); // Hide OTP form

                const email = $('#userEmail').text();

                $.ajax({
                    url: 'send_otp.php',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        $('#otpLoader').hide(); // Hide loader
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Sent',
                                text: 'The OTP has been sent to your email.',
                                confirmButtonColor: '#3085d6'
                            });
                            $('#otpForm').show(); // Show OTP form
                            $('#otpInputModal').modal('show'); // Show OTP input modal
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to send OTP',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        $('#otpLoader').hide(); // Hide loader
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to the server. Please try again.',
                            confirmButtonColor: '#d33'
                        });
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