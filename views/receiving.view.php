<?php
include_once '../core/database.php';
include_once '../core/documentController.php';

session_start();
$documentController = new documentController();
$documents = $documentController->getSubmittedDocuments($_SESSION['office_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Receive Document</title>
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
    <style>
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .iframe-container {
            display: flex;
            justify-content: center;
        }

        .iframe-container iframe {
            width: 100%;
            height: 500px;
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
            <h1>Receive Document</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Receive Document</li>
                </ol>
            </nav>
        </div>

        <div class="main-section">
            <div class="documents-table">
                <div class="documents-header">
                    FOR RECEIVING
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

                <table id="example" class="table datatable datatable-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Details</th>
                            <th>From</th>
                            <th>School/Office</th>
                            <th>ACTIONS NEEDED</th>
                            <th>Date & Time Posted</th>
                            <th>Receive</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $document): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($document['document_type']); ?></td>
                                <td><?php echo htmlspecialchars($document['details']); ?></td>
                                <td><?php echo htmlspecialchars($document['submitted_by_name']); ?></td>
                                <td><?php echo htmlspecialchars($document['office_id']); ?></td>
                                <td><?php echo htmlspecialchars($document['purpose']); ?></td>
                                <td><?php echo htmlspecialchars($document['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-receive"
                                        data-details="<?php echo htmlspecialchars($document['details']); ?>"
                                        data-from="<?php echo htmlspecialchars($document['submitted_by_name']); ?>"
                                        data-type="<?php echo htmlspecialchars($document['document_type']); ?>"
                                        data-actions="<?php echo htmlspecialchars($document['purpose']); ?>"
                                        data-path="<?php echo htmlspecialchars($document['document_path']); ?>"
                                        data-filename="<?php echo htmlspecialchars(basename($document['document_path'])); ?>">
                                        Receive
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Receive Document Modal -->
    <div class="modal fade" id="receiveDocumentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receive & Accept Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="receiveDocumentForm">
                        <p class="mb-3">Are you sure you want to accept this document from <span class="text-primary"
                                id="senderName">Phebelyn Bastillada</span>?</p>

                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Document Type</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="documentType" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Details / Subject Matter</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" id="documentDetails" readonly></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Actions Needed</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="2" id="actionsNeeded"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Accepted by</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="acceptedBy" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 iframe-container">
                            <iframe id="documentPreview" frameborder="0"></iframe>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="acceptDocumentBtn">Accept Document</button>
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
            new simpleDatatables.DataTable("#example");

            // Initialize modal
            const receiveModal = new bootstrap.Modal(document.getElementById('receiveDocumentModal'));

            // Add click event listeners to all receive buttons
            document.querySelectorAll('.btn-receive').forEach(button => {
                button.addEventListener('click', function () {
                    // Get data from button attributes
                    const details = this.getAttribute('data-details');
                    const from = this.getAttribute('data-from');
                    const type = this.getAttribute('data-type');
                    const actions = this.getAttribute('data-actions');
                    const path = this.getAttribute('data-path');
                    const filename = this.getAttribute('data-filename');

                    // Populate modal fields
                    document.getElementById('senderName').textContent = from;
                    document.getElementById('documentType').value = type;
                    document.getElementById('documentDetails').value = details;
                    document.getElementById('actionsNeeded').value = actions;
                    document.getElementById('acceptedBy').value = '<?php echo $_SESSION['fullname']; ?>'; // Dynamic based on logged-in user
                    document.getElementById('documentPreview').src = 'process/preview_document.php?path=' + encodeURIComponent(path);

                    // Show modal
                    receiveModal.show();
                });
            });

            // Handle accept document button
            document.getElementById('acceptDocumentBtn').addEventListener('click', function () {
                // Add your document acceptance logic here
                alert('Document accepted successfully!');
                receiveModal.hide();
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