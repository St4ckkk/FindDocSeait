<?php
include_once '../core/userController.php';
include_once '../core/VirtualIPManager.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    header("Location: unauthorized.php");
    exit();
}

$userController = new userController();
$loginLogs = $userController->getUserLoginLogs();
$virtualIPManager = new VirtualIPManager();

function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'success':
            return 'badge bg-success';
        case 'failed':
            return 'badge bg-danger';
        case 'blocked':
            return 'badge bg-warning';
        default:
            return 'badge bg-secondary';
    }
}

function getRiskLevelBadgeClass($riskLevel)
{
    switch ($riskLevel) {
        case 'low':
            return 'badge bg-success';
        case 'medium':
            return 'badge bg-warning';
        case 'high':
            return 'badge bg-danger';
        default:
            return 'badge bg-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>User Login Logs</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <link href="node_modules/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
</head>

<body>

    <?php
    include_once '../includes/header.php';
    include_once '../includes/sidebar.php';
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>User Login Logs</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">User Login Logs</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Login Logs</h5>

                            <div class="table-responsive">
                                <table id="loginLogs" class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Status</th>
                                            <th>Risk Level</th>
                                            <th>Device Type</th>
                                            <th>OS Type</th>
                                            <th>Browser Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($loginLogs as $log): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                                <td><?php echo htmlspecialchars($log['user_agent']); ?></td>
                                                <td><span
                                                        class="<?php echo getStatusBadgeClass($log['status']); ?>"><?php echo htmlspecialchars($log['status']); ?></span>
                                                </td>
                                                <td><span
                                                        class="<?php echo getRiskLevelBadgeClass($log['risk_level']); ?>"><?php echo htmlspecialchars($log['risk_level']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($log['device_type']); ?></td>
                                                <td><?php echo htmlspecialchars($log['os_type']); ?></td>
                                                <td><?php echo htmlspecialchars($log['browser_type']); ?></td>
                                                <td>
                                                    <?php if ($log['status'] !== 'blocked'): ?>
                                                        <button class="btn btn-danger btn-sm btn-block"
                                                            data-ip="<?php echo htmlspecialchars($log['ip_address']); ?>"
                                                            data-user-id="<?php echo htmlspecialchars($log['user_id']); ?>">Block</button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-success btn-sm btn-unblock"
                                                        data-ip="<?php echo htmlspecialchars($log['ip_address']); ?>"
                                                        data-user-id="<?php echo htmlspecialchars($log['user_id']); ?>">Unblock</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable with pagination
            new simpleDatatables.DataTable("#loginLogs", {
                searchable: true,
                fixedHeight: true,
                perPage: 10,
                perPageSelect: [10, 25, 50, 100]
            });

            // Handle block button click
            document.querySelectorAll('.btn-block').forEach(button => {
                button.addEventListener('click', function () {
                    const ipAddress = this.getAttribute('data-ip');
                    const userId = this.getAttribute('data-user-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, block it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('process/block_user.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `ip_address=${encodeURIComponent(ipAddress)}&user_id=${encodeURIComponent(userId)}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire(
                                            'Blocked!',
                                            'The user has been blocked.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to block the user: ' + data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while blocking the user.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });

            // Handle unblock button click
            document.querySelectorAll('.btn-unblock').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-user-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to unblock this user!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, unblock it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('process/unblock_user.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `user_id=${encodeURIComponent(userId)}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Response:', data);
                                    if (data.status === 'success') {
                                        Swal.fire(
                                            'Unblocked!',
                                            'The user has been unblocked.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to unblock the user: ' + data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while unblocking the user.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>