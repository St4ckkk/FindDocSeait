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

    <style>
        body {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            background: none;
            color: #444444;
            position: relative;
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

        .header {
            background: rgb(226, 123, 24);
            padding: 15px 20px;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .page-title {
            color: #666;
            font-size: 14px;
            margin: 20px 0;
            padding-left: 15px;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }

        .form-panel,
        .login-panel {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgb(226, 123, 24);
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .form-label {
            width: 150px;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            flex: 1;
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
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

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .background-logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: url('assets/img/school-logo.png') no-repeat left center;
            background-size: cover;
            opacity: 0.1;
            filter: blur(5px);
            z-index: 0;
        }
    </style>
</head>

<body>
    <!-- Background Logo -->
    <div class="background-logo"></div>

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

    <div class="page-title">
        <!-- Document Tracking System -->
    </div>

    <div class="main-content">
        <div class="row">
            <!-- Document Request Form -->
            <div class="col-lg-8">
                <div class="form-panel">
                    <div class="form-title">Request a Document (Guest)</div>
                    <form class="row g-3">
                        <div class="col-12">
                            <label for="from" class="form-label">From:</label>
                            <input type="text" class="form-control" id="from" placeholder="Full Name">
                        </div>

                        <div class="col-12">
                            <label for="office" class="form-label">Office:</label>
                            <input type="text" class="form-control" id="office" placeholder="School / Office">
                        </div>

                        <div class="col-12">
                            <label for="documentType" class="form-label">Document Type:</label>
                            <select class="form-select" id="documentType">
                                <option selected disabled>--Select--</option>
                                <option>Transcript of Records</option>
                                <option>Certificate of Enrollment</option>
                                <option>Good Moral Certificate</option>
                                <option>Diploma</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="details" class="form-label">Details:</label>
                            <textarea class="form-control" id="details" rows="4"
                                placeholder="Description, Date, Destination"></textarea>
                        </div>

                        <div class="col-12">
                            <label for="purpose" class="form-label">Purpose of Submission:</label>
                            <textarea class="form-control" id="purpose" rows="4"
                                placeholder="Purposes or Actions to be taken..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="receivingUnit" class="form-label">Receiving Unit:</label>
                            <select class="form-select" id="receivingUnit">
                                <option selected disabled>--Select--</option>
                                <option>Administration Office</option>
                                <option>Registrar's Office</option>
                                <option>Dean's Office</option>
                                <option>Department Head Office</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Login Form -->
            <div class="col-lg-4">
                <div class="login-panel">
                    <div class="form-title">Log In</div>
                    <form id="loginForm" action="login.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password">
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-cancel">Cancel</button>
                            <button type="submit" class="btn btn-submit">Sign in</button>
                        </div>

                        <div class="register-link text-center mt-3">
                            <p>Don't have an account? <a href="#" class="btn btn-register">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
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
                                text: 'Login successful!',
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'views/dashboard.php'; // Redirect to dashboard
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