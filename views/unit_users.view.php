<?php
include_once '../core/userController.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    header("Location: ../unauthorized.php");
    exit();
}

$userController = new userController();
$users = $userController->getAllUsers();
$roles = $userController->getAllRoles();
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

        <div class="main-section">
            <div class="users-table">
                <div class="users-header">
                    USER PERMISSIONS AND ROLES
                </div>
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
                </div>

                <table id="userPermissions" class="table datatable datatable-table">
                    <thead class="table-light">
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <select class="form-select form-select-sm">
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo htmlspecialchars($role['role_id']); ?>" <?php echo $user['role_id'] == $role['role_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($role['role_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#permissionsModal"
                                        data-fullname="<?php echo htmlspecialchars($user['fullname']); ?>"
                                        data-user-id="<?php echo htmlspecialchars($user['id']); ?>">View
                                        Permissions</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm save-btn"
                                        data-id="<?php echo htmlspecialchars($user['id']); ?>">Save</button>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        data-id="<?php echo htmlspecialchars($user['id']); ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save permissions: ' + error
                    });
                }
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