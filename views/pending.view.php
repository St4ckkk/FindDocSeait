<?php

include_once '../core/documentController.php';
include_once '../core/userController.php';

session_start();
$documentController = new documentController();
$documents = $documentController->getPendingDocuments($_SESSION['office_id']);

$userController = new userController();
$offices = $userController->getOffices();
$userPermissions = $userController->getUserPermissions($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Pending Document</title>
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
            <h1>Pending Document</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Pending Document</li>
                </ol>
            </nav>
        </div>

        <div class="main-section">
            <div class="documents-table">
                <div class="documents-header">
                    DOCUMENT PENDING FOR ACTION
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

                <table id="pendingDocuments" class="table datatable datatable-table">
                    <thead class="table-light">
                        <tr>
                            <th>Tracking</th>
                            <th>DOC TYPE & DETAIL</th>
                            <th>FWD FROM</th>
                            <th>ACCEPTED</th>
                            <th>ACTIONS NEEDED</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $document): ?>
                            <tr>
                                <td><a href="#"><?php echo htmlspecialchars($document['tracking_number']); ?></a></td>
                                <td>
                                    <?php echo htmlspecialchars($document['document_type']); ?><br>
                                    <?php echo htmlspecialchars($document['details']); ?><br>
                                    <small>From: <?php echo htmlspecialchars($document['submitted_by_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($document['submitted_by_name']); ?></td>
                                <td><?php echo htmlspecialchars($document['created_at']); ?><br><small>by:
                                        <?php echo htmlspecialchars($_SESSION['fullname']); ?></small></td>
                                <td><?php echo htmlspecialchars($document['purpose']); ?></td>
                                <td>
                                    <?php if (in_array('view_documents', $userPermissions)): ?>
                                        <button class="btn btn-info btn-sm btn-view"
                                            data-id="<?php echo htmlspecialchars($document['id']); ?>"><i
                                                class="bi bi-eye"></i></button>
                                    <?php endif; ?>
                                    <?php if (in_array('share_documents', $userPermissions)): ?>
                                        <button class="btn btn-info btn-sm btn-share"
                                            data-id="<?php echo htmlspecialchars($document['id']); ?>"><i
                                                class="bi bi-send"></i></button>
                                    <?php endif; ?>
                                    <?php if (in_array('download_documents', $userPermissions)): ?>
                                        <button class="btn btn-primary btn-sm btn-download"
                                            data-path="<?php echo htmlspecialchars($document['document_path']); ?>"><i
                                                class="bi bi-download"></i></button>
                                    <?php endif; ?>
                                    <?php if (in_array('delete_documents', $userPermissions)): ?>
                                        <button class="btn btn-danger btn-sm btn-delete"
                                            data-id="<?php echo htmlspecialchars($document['id']); ?>"><i
                                                class="bi bi-trash"></i></button>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDocumentModalLabel">Delete Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this document?</p>
                    <input type="hidden" id="deleteDocumentId" name="deleteDocumentId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteDocumentBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Share Document Modal -->
    <div class="modal fade" id="shareDocumentModal" tabindex="-1" aria-labelledby="shareDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareDocumentModalLabel">Share Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="shareDocumentForm">
                        <div class="mb-3">
                            <label for="shareOffice" class="form-label">Select Office</label>
                            <select class="form-select" id="shareOffice" name="shareOffice">
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?php echo htmlspecialchars($office['office_id']); ?>">
                                        <?php echo htmlspecialchars($office['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" id="shareDocumentId" name="shareDocumentId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="shareDocumentBtn">Share</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add new script for modal functionality -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable
            new simpleDatatables.DataTable("#pendingDocuments");

            // Handle share button click
            document.querySelectorAll('.btn-share').forEach(button => {
                button.addEventListener('click', function () {
                    const documentId = this.getAttribute('data-id');
                    document.getElementById('shareDocumentId').value = documentId;
                    console.log(`Share button clicked for document ID: ${documentId}`);
                    new bootstrap.Modal(document.getElementById('shareDocumentModal')).show();
                });
            });

            // Handle share document
            document.getElementById('shareDocumentBtn').addEventListener('click', function () {
                const form = document.getElementById('shareDocumentForm');
                const formData = new FormData(form);
                console.log('Sharing document with data:', Object.fromEntries(formData.entries()));
                fetch('process/share_document.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response from share_document.php:', data);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Document shared successfully!'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to share document: ' + data.message
                            });
                        }
                        new bootstrap.Modal(document.getElementById('shareDocumentModal')).hide();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while sharing the document.'
                        });
                    });
            });

            // Handle delete button click
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function () {
                    const documentId = this.getAttribute('data-id');
                    document.getElementById('deleteDocumentId').value = documentId;
                    console.log(`Delete button clicked for document ID: ${documentId}`);
                    new bootstrap.Modal(document.getElementById('deleteDocumentModal')).show();
                });
            });

            // Handle delete document
            document.getElementById('deleteDocumentBtn').addEventListener('click', function () {
                const documentId = document.getElementById('deleteDocumentId').value;
                console.log(`Deleting document ID: ${documentId}`);
                fetch('process/delete_document.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `document_id=${documentId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response from delete_document.php:', data);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Document deleted successfully!'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete document: ' + data.message
                            });
                        }
                        new bootstrap.Modal(document.getElementById('deleteDocumentModal')).hide();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the document.'
                        });
                    });
            });

            // Handle download button click
            document.querySelectorAll('.btn-download').forEach(button => {
                button.addEventListener('click', function () {
                    const documentPath = this.getAttribute('data-path');
                    console.log(`Downloading document from path: ${documentPath}`);
                    window.location.href = 'process/download_document.php?path=' + encodeURIComponent(documentPath);
                });
            });
        });
    </script>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>

</html>