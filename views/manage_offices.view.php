<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Manage Offices and Users</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <style>
        .manage-offices,
        .manage-users {
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .office-grid,
        .user-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .office-item,
        .user-item {
            background: #007bff;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            transition: all 0.3s;
        }

        .office-item:hover,
        .user-item:hover {
            background: #0056b3;
            cursor: pointer;
        }

        .office-item i,
        .user-item i {
            font-size: 24px;
            color: white;
            margin-bottom: 8px;
        }

        .office-item span,
        .user-item span {
            display: block;
            font-size: 13px;
            color: white;
        }

        .main-section {
            max-width: 1200px;
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .admin-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .admin-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php
    include_once '../includes/header.php';
    include_once '../includes/sidebar.php';
    include_once '../core/userController.php';

    $userController = new userController();
    $admins = $userController->getUserAdmin();
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Offices and Users</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Manage Offices and Users</li>
                </ol>
            </nav>
        </div>

        <div class="main-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="offices-section manage-offices">
                        <h5 class="section-title">Offices</h5>
                        <div class="office-grid">
                            <div class="office-item">
                                <i class="bi bi-building"></i>
                                <span>Scholarship</span>
                            </div>
                            <div class="office-item">
                                <i class="bi bi-building"></i>
                                <span>Registrar</span>
                            </div>
                            <div class="office-item">
                                <i class="bi bi-building"></i>
                                <span>President Office</span>
                            </div>
                            <div class="office-item">
                                <i class="bi bi-building"></i>
                                <span>Accounting</span>
                            </div>
                        </div>
                        <button id="addOfficeBtn" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#addOfficeModal">Add Office</button>
                    </div>

                    <div class="admin-container">
                        <h5 class="admin-title">Admins</h5>
                        <div class="user-grid">
                            <?php foreach ($admins as $admin): ?>
                                <div class="user-item">
                                    <i class="bi bi-person-badge"></i>
                                    <span><?php echo htmlspecialchars($admin['fullname']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button id="addAdminBtn" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#addAdminModal">Add Admin</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Office Modal -->
    <div class="modal fade" id="addOfficeModal" tabindex="-1" inert>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addOfficeForm" action="process/add_office.php" method="POST">
                        <div class="mb-3">
                            <label for="officeName" class="form-label">Office Name</label>
                            <input type="text" class="form-control" id="officeName" name="name"
                                placeholder="Enter office name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveOfficeBtn">Save Office</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" inert>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm" action="process/add_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="adminName" class="form-label">Admin Name</label>
                            <input type="text" class="form-control" id="adminName" name="fullname"
                                placeholder="Enter admin name">
                        </div>
                        <div class="mb-3">
                            <label for="adminUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="adminUsername" name="username"
                                placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="adminPassword" name="password"
                                placeholder="Enter password">
                        </div>
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="adminEmail" name="email"
                                placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="adminRole" class="form-label">Role</label>
                            <select class="form-control" id="adminRole" name="role_id">
                                <option value="1">Admin</option>
                                <option value="2">Super Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveAdminBtn">Save Admin</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#saveAdminBtn').on('click', function () {
                const form = $('#addAdminForm')[0];
                if (form.checkValidity()) {
                    $('#addAdminForm').submit();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please fill in all the fields'
                    });
                }
            });

            $('#addAdminForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission
                const form = $(this);
                $.post(form.attr('action'), form.serialize(), function (response) {
                    console.log('Server response:', response); // Debugging line

                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid server response: ' + response
                            });
                            return;
                        }
                    }

                    if (response.status === 'success') {
                        const adminName = $('#adminName').val();
                        const newAdmin = $('<div>').addClass('user-item').html(`<i class="bi bi-person-badge"></i><span>${adminName}</span>`);
                        $('.admin-container .user-grid').append(newAdmin);

                        form[0].reset();

                        const addAdminModal = bootstrap.Modal.getInstance($('#addAdminModal')[0]);
                        addAdminModal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Admin added successfully!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error adding admin'
                        });
                    }
                }).fail(function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'AJAX request failed: ' + error
                    });
                });
            });

            $('#saveOfficeBtn').on('click', function () {
                const form = $('#addOfficeForm')[0];
                if (form.checkValidity()) {
                    $('#addOfficeForm').submit();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please fill in all the fields'
                    });
                }
            });

            $('#addOfficeForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission
                const form = $(this);
                $.post('process/add_office.php', form.serialize(), function (response) {
                    console.log('Server response:', response); // Debugging line

                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid server response: ' + response
                            });
                            return;
                        }
                    }

                    if (response.status === 'success') {
                        const officeName = $('#officeName').val();
                        const newOffice = $('<div>').addClass('office-item').html(`<i class="bi bi-building"></i><span>${officeName}</span>`);
                        $('.office-grid').append(newOffice);

                        form[0].reset();

                        const addOfficeModal = bootstrap.Modal.getInstance($('#addOfficeModal')[0]);
                        addOfficeModal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Office added successfully!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error adding office'
                        });
                    }
                }).fail(function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'AJAX request failed: ' + error
                    });
                });
            });

            const addAdminModal = document.getElementById('addAdminModal');
            addAdminModal.addEventListener('show.bs.modal', function () {
                addAdminModal.removeAttribute('inert');
            });

            addAdminModal.addEventListener('hidden.bs.modal', function () {
                addAdminModal.setAttribute('inert', '');
            });

            const addOfficeModal = document.getElementById('addOfficeModal');
            addOfficeModal.addEventListener('show.bs.modal', function () {
                addOfficeModal.removeAttribute('inert');
            });

            addOfficeModal.addEventListener('hidden.bs.modal', function () {
                addOfficeModal.setAttribute('inert', '');
            });
        });
    </script>
</body>

</html>