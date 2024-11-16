<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Manage Users</title>
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
</head>

<body>
    <?php
    include_once '../includes/header.php';
    include_once '../includes/sidebar.php';
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Users</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Manage Users</li>
                </ol>
            </nav>
        </div>

        <div class="main-section">
            <div class="documents-table">
                <div class="documents-header">
                    USERS IN <span id="currentOffice">OFFICE</span>
                </div>
                <div class="table-controls-container d-flex justify-content-between align-items-center">
                    <div class="entries-control">
                        Show
                        <select class="form-select form-select-sm d-inline-block w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </div>
                    <div class="d-flex align-items-center">
                        <button id="addUserBtn" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">Add User</button>
                        <div class="search-control ">
                            Search:
                            <input type="text" class="form-control form-control-sm d-inline-block w-auto">
                        </div>

                    </div>
                </div>
                <table id="usersTable" class="table datatable datatable-table mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- User rows will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role">
                                <option value="1">Admin</option>
                                <option value="2">Editor</option>
                                <option value="3">Viewer</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const office = urlParams.get('office');
            const currentOfficeSpan = document.getElementById('currentOffice');
            const usersTableBody = document.querySelector('#usersTable tbody');

            if (office) {
                currentOfficeSpan.textContent = office;
                fetchUsersForOffice(office);
            }

            document.getElementById('saveUserBtn').addEventListener('click', function () {
                const fullName = document.getElementById('fullName').value;
                const username = document.getElementById('username').value;
                const email = document.getElementById('email').value;
                const role = document.getElementById('role').value;

                if (fullName && username && email && role) {
                    // Add user to the database (this is just a simulation, you need to implement the actual database logic)
                    const newUser = {
                        fullName,
                        username,
                        email,
                        role,
                        createdAt: new Date().toISOString().split('T')[0]
                    };

                    // Add the new user to the table
                    addUserToTable(newUser);

                    // Clear the form fields
                    document.getElementById('fullName').value = '';
                    document.getElementById('username').value = '';
                    document.getElementById('email').value = '';
                    document.getElementById('role').value = '1';

                    const addUserModal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    addUserModal.hide();
                }
            });

            function fetchUsersForOffice(office) {
                // Fetch users for the selected office from the database (this is just a simulation)
                const users = [
                    { fullName: 'John Doe', username: 'johndoe', email: 'john@example.com', role: 'Admin', createdAt: '2023-01-01' },
                    { fullName: 'Jane Smith', username: 'janesmith', email: 'jane@example.com', role: 'Editor', createdAt: '2023-02-01' }
                ];

                // Clear the table body
                usersTableBody.innerHTML = '';

                // Add users to the table
                users.forEach(user => {
                    addUserToTable(user);
                });
            }

            function addUserToTable(user) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.fullName}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.createdAt}</td>
                    <td>
                        <button class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i></button>
                        <button class="btn btn-secondary btn-sm"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </td>
                `;
                usersTableBody.appendChild(row);
            }

            // Initialize DataTable
            new simpleDatatables.DataTable("#usersTable");
        });
    </script>
</body>

</html>