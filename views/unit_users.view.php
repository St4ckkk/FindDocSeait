<?php
include_once '../core/userController.php';

session_start();

// Check if the user is logged in and has a valid CSRF token
if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['user_id'])) {
    header("Location: ../unauthorized.php");
    exit();
}

// Create an instance of userController
$userController = new userController();

// Get the user's role
$userRole = $userController->getUserRole($_SESSION['user_id']);

// Check if the user has the role of Admin or Super Admin
if ($userRole !== 'Admin' && $userRole !== 'Super Admin') {
    header("Location: ../unauthorized.php");
    exit();
}

$users = $userController->getAllUsers();
$roles = $userController->getAllRoles();
$offices = $userController->getOffices();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Manage User Permissions and Roles</title>
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
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <style>
        .table-controls-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .entries-control,
        .search-control {
            display: flex;
            align-items: center;
        }

        .entries-control select,
        .search-control input {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <?php
    include_once '../includes/header.php';
    include_once '../includes/sidebar.php';
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage User Permissions and Roles</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Manage User Permissions and Roles</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">USER PERMISSIONS AND ROLES</h5>

                            <div class="table-controls-container">
                                <div class="entries-control">
                                    Show
                                    <select class="form-select form-select-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    entries
                                </div>
                                <div class="search-control">
                                    Search:
                                    <input type="text" class="form-control form-control-sm">
                                </div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addUserModal">Add User</button>
                            </div>

                            <div class="table-responsive">
                                <table id="userPermissions" class="table datatable datatable-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fullname</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Office</th>
                                            <th>Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <form class="role-form">
                                                        <input type="hidden" name="user_id"
                                                            value="<?php echo htmlspecialchars($user['id']); ?>">
                                                        <select class="form-select form-select-sm" name="role_id">
                                                            <?php foreach ($roles as $role): ?>
                                                                <option
                                                                    value="<?php echo htmlspecialchars($role['role_id']); ?>"
                                                                    <?php echo $user['role_id'] == $role['role_id'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['office_name']); ?></td>
                                                <td>
                                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#permissionsModal"
                                                        data-fullname="<?php echo htmlspecialchars($user['fullname']); ?>"
                                                        data-user-id="<?php echo htmlspecialchars($user['id']); ?>">View
                                                        Permissions</button>
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Fullname</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                        <?php echo htmlspecialchars($role['role_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="office_id" class="form-label">Office</label>
                            <select class="form-select" id="office_id" name="office_id" required>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?php echo htmlspecialchars($office['office_id']); ?>">
                                        <?php echo htmlspecialchars($office['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveUserBtn">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionsModalLabel">User Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="permissionsForm">
                        <div class="mb-3">
                            <label for="viewDocuments" class="form-label">View Documents</label>
                            <input type="checkbox" id="viewDocuments" name="permissions[]" value="view_documents">
                        </div>
                        <div class="mb-3">
                            <label for="downloadDocuments" class="form-label">Download Documents</label>
                            <input type="checkbox" id="downloadDocuments" name="permissions[]"
                                value="download_documents">
                        </div>
                        <div class="mb-3">
                            <label for="deleteDocuments" class="form-label">Delete Documents</label>
                            <input type="checkbox" id="deleteDocuments" name="permissions[]" value="delete_documents">
                        </div>
                        <div class="mb-3">
                            <label for="shareDocuments" class="form-label">Share Documents</label>
                            <input type="checkbox" id="shareDocuments" name="permissions[]" value="share_documents">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePermissions">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new simpleDatatables.DataTable("#userPermissions", {
                searchable: true,
                fixedHeight: true,
                perPage: 10,
                perPageSelect: [10, 25, 50, 100]
            });

            $('#permissionsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var userId = button.data('user-id');
                var modal = $(this);

                // Store the userId in the modal for later use
                modal.data('user-id', userId);

                // Clear previous permissions
                modal.find('input[type="checkbox"]').prop('checked', false);

                // Fetch existing permissions
                $.ajax({
                    url: 'process/get_user_permissions.php',
                    method: 'GET',
                    data: { user_id: userId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            var permissions = response.permissions;
                            permissions.forEach(function (permission) {
                                modal.find('input[value="' + permission + '"]').prop('checked', true);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to fetch permissions'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch permissions: ' + error
                        });
                    }
                });
            });

            $('#savePermissions').on('click', function () {
                var modal = $('#permissionsModal');
                var userId = modal.data('user-id');
                var permissions = [];

                modal.find('input[type="checkbox"]:checked').each(function () {
                    permissions.push($(this).val());
                });

                console.log('Saving permissions for user ID:', userId);
                console.log('Permissions:', permissions);

                $.ajax({
                    url: 'process/save_permissions.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        user_id: userId,
                        permissions: permissions
                    }),
                    dataType: 'json',
                    success: function (response) {
                        console.log('Response:', response);
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Permissions saved successfully'
                            }).then(() => {
                                modal.modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to save permissions'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        console.log('Response Text:', xhr.responseText); // Log the response text
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save permissions: ' + error
                        });
                    }
                });
            });

            $('.role-form select').on('change', function () {
                var form = $(this).closest('.role-form');
                var userId = form.find('input[name="user_id"]').val();
                var roleId = form.find('select[name="role_id"]').val();

                $.ajax({
                    url: 'process/update_user_role.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        user_id: userId,
                        role_id: roleId
                    }),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Role updated successfully'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update role'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update role: ' + error
                        });
                    }
                });
            });

            $('#saveUserBtn').on('click', function () {
                var form = $('#addUserForm');
                var formData = form.serialize();

                $.ajax({
                    url: 'process/add_user.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'User added successfully'
                            }).then(() => {
                                $('#addUserModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to add user'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add user: ' + error
                        });
                    }
                });
            });
        });
    </script>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <script src="assets/js/main.js"></script>
</body>

</html>