<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FindDocSEAIT - Unauthorized Access</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="views/node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="views/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="views/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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

        .error-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .error-icon {
            font-size: 64px;
            color: rgb(226, 123, 24);
            margin-bottom: 20px;
        }

        .error-title {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-message {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 4px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: rgb(226, 123, 24);
            color: white;
        }

        .btn-primary:hover {
            background: rgb(200, 100, 0);
        }

        .animation-shake {
            animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo-text me-2">FindDocSEAIT</div>
        </div>
    </header>
    <div class="error-container animation-shake">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Unauthorized Access</h1>
        <p class="error-message">
            Your session has expired or you don't have permission to access this page.
            Please log in again to continue.
        </p>
        <a href="index.php" class="btn btn-primary">Return to Login</a>
    </div>
</body>

</html>