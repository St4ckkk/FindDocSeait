<?php

include_once '../core/documentController.php';
include_once '../core/userController.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    header("Location: ../unauthorized.php");
    exit();
}

$documentController = new documentController();
$documents = $documentController->getPendingDocuments($_SESSION['office_id'], $_SESSION['csrf_token']);

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>
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

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">DOCUMENT PENDING FOR ACTION</h5>

                            <div class="table-responsive">
                                <table id="pendingDocuments" class="table datatable">
                                    <thead>
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
                                                <td><a
                                                        href="#"><?php echo htmlspecialchars($document['tracking_number']); ?></a>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($document['document_type']); ?><br>
                                                    <?php echo htmlspecialchars($document['details']); ?><br>
                                                    <small>From:
                                                        <?php echo htmlspecialchars($document['submitted_by_name']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($document['submitted_by_name']); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($document['created_at']); ?><br>
                                                    <small>by:
                                                        <?php echo htmlspecialchars($document['accepted_by_name']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($document['purpose']); ?></td>
                                                <td>
                                                    <?php if (in_array('view_documents', $userPermissions)): ?>
                                                        <button class="btn btn-info btn-sm btn-view"
                                                            data-path="<?php echo htmlspecialchars($document['document_path']); ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (in_array('share_documents', $userPermissions)): ?>
                                                        <button class="btn btn-info btn-sm btn-share"
                                                            data-id="<?php echo htmlspecialchars($document['id']); ?>">
                                                            <i class="bi bi-send"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (in_array('download_documents', $userPermissions)): ?>
                                                        <button class="btn btn-primary btn-sm btn-download"
                                                            data-path="<?php echo htmlspecialchars($document['document_path']); ?>">
                                                            <i class="bi bi-download"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (in_array('delete_documents', $userPermissions)): ?>
                                                        <button class="btn btn-danger btn-sm btn-delete"
                                                            data-id="<?php echo htmlspecialchars($document['id']); ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
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

    <!-- View Document Modal -->
    <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDocumentModalLabel">View Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="pdfViewer"></div>
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
                            <select class="form-select" id="shareOffice" name="shareOffice" required>
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

    <!-- Delete Document Modal -->
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

    <!-- Required Scripts -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="assets/vendor/pdfjs/pdf.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable
            new simpleDatatables.DataTable("#pendingDocuments", {
                searchable: true,
                fixedHeight: true,
                perPage: 10,
                perPageSelect: [10, 25, 50, 100]
            });

            // Set up PDF.js worker
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'assets/vendor/pdfjs/pdf.worker.js';

            // Enhanced PDF rendering function
            function renderPDF(url) {
                const pdfViewer = document.getElementById('pdfViewer');
                pdfViewer.innerHTML = ''; // Clear previous content

                // Create container for PDF pages
                const pdfContainer = document.createElement('div');
                pdfContainer.style.cssText = `
            width: 100%;
            height: 100%;
            overflow: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: #f5f5f5;
        `;
                pdfViewer.appendChild(pdfContainer);

                // Loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.textContent = 'Loading PDF...';
                loadingDiv.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            color: #666;
        `;
                pdfContainer.appendChild(loadingDiv);

                pdfjsLib.getDocument(url).promise.then(pdf => {
                    loadingDiv.remove();
                    const totalPages = pdf.numPages;

                    // Function to determine optimal scale
                    function getOptimalScale(page) {
                        const viewport = page.getViewport({ scale: 1.0 });
                        const containerWidth = pdfContainer.clientWidth - 40; // Account for padding
                        return (containerWidth / viewport.width);
                    }

                    // Render all pages
                    for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                        pdf.getPage(pageNum).then(page => {
                            const scale = getOptimalScale(page);
                            const viewport = page.getViewport({ scale });

                            const pageContainer = document.createElement('div');
                            pageContainer.style.cssText = `
                        margin-bottom: 20px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        background: white;
                    `;

                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            pageContainer.appendChild(canvas);
                            pdfContainer.appendChild(pageContainer);

                            page.render({
                                canvasContext: context,
                                viewport: viewport
                            });
                        });
                    }
                }).catch(error => {
                    console.error('Error rendering PDF:', error);
                    pdfContainer.innerHTML = `
                <div style="color: red; padding: 20px;">
                    Error loading PDF. Please try again later.
                </div>
            `;
                });
            }

            // Handle view button click
            document.querySelectorAll('.btn-view').forEach(button => {
                button.addEventListener('click', function () {
                    const documentPath = this.getAttribute('data-path');
                    new bootstrap.Modal(document.getElementById('viewDocumentModal')).show();
                    renderPDF('process/preview_document.php?path=' + encodeURIComponent(documentPath));
                });
            });

            // Handle share button click
            document.querySelectorAll('.btn-share').forEach(button => {
                button.addEventListener('click', function () {
                    const documentId = this.getAttribute('data-id');
                    document.getElementById('shareDocumentId').value = documentId;
                    new bootstrap.Modal(document.getElementById('shareDocumentModal')).show();
                });
            });

            // Handle share document submission
            document.getElementById('shareDocumentBtn').addEventListener('click', function () {
                const form = document.getElementById('shareDocumentForm');
                const formData = new FormData(form);

                fetch('process/share_document.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Document shared successfully!'
                            }).then(() => {
                                bootstrap.Modal.getInstance(document.getElementById('shareDocumentModal')).hide();
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to share document: ' + data.message
                            });
                        }
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
                    new bootstrap.Modal(document.getElementById('deleteDocumentModal')).show();
                });
            });

            // Handle delete document
            document.getElementById('deleteDocumentBtn').addEventListener('click', function () {
                const documentId = document.getElementById('deleteDocumentId').value;

                fetch('process/delete_document.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `document_id=${documentId}`
                })
                    .then(response => response.json())
                    .then(data => {
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
                        bootstrap.Modal.getInstance(document.getElementById('deleteDocumentModal')).hide();
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
                    window.location.href = 'process/download_document.php?path=' + encodeURIComponent(documentPath);
                });
            });
        });
    </script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>

</html>