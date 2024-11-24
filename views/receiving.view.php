<?php
include_once '../core/database.php';
include_once '../core/documentController.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    header("Location: ../unauthorized.php");
    exit();
}

$documentController = new documentController();
$documents = $documentController->getSubmittedDocuments($_SESSION['office_id'], $_SESSION['csrf_token']);
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
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
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

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-view,
        .btn-edit,
        .btn-delete,
        .btn-receive {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        /* .btn-receive {
            background-color: #28a745;
            color: white;
        } */

        .editor-container {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
        }

        #documentEditor {
            width: 100%;
            height: 100%;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .edit-controls {
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .edit-controls button {
            margin-right: 10px;
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

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">DOCUMENTS FOR RECEIVING</h5>

                            <div class="table-responsive">
                                <table id="receiveDocuments" class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th>From</th>
                                            <th>School/Office</th>
                                            <th>ACTIONS NEEDED</th>
                                            <th>Date & Time Posted</th>
                                            <th>Actions</th>
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
                                                    <button class="btn btn-success btn-sm btn-receive"
                                                        data-id="<?php echo htmlspecialchars($document['id']); ?>"
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
                    </div>
                </div>
            </div>
        </section>
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="acceptDocumentBtn">Accept Document</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new simpleDatatables.DataTable("#receiveDocuments", {
                searchable: true,
                fixedHeight: true,
                perPage: 10,
                perPageSelect: [10, 25, 50, 100]
            });

            const receiveModal = new bootstrap.Modal(document.getElementById('receiveDocumentModal'));

            document.querySelectorAll('.btn-receive').forEach(button => {
                button.addEventListener('click', function () {
                    const documentId = this.getAttribute('data-id');
                    const details = this.getAttribute('data-details');
                    const from = this.getAttribute('data-from');
                    const type = this.getAttribute('data-type');
                    const actions = this.getAttribute('data-actions');

                    document.getElementById('senderName').textContent = from;
                    document.getElementById('documentType').value = type;
                    document.getElementById('documentDetails').value = details;
                    document.getElementById('actionsNeeded').value = actions;
                    document.getElementById('acceptedBy').value = '<?php echo $_SESSION['fullname']; ?>';

                    document.getElementById('acceptDocumentBtn').setAttribute('data-id', documentId);

                    receiveModal.show();
                });
            });

            document.getElementById('acceptDocumentBtn').addEventListener('click', function () {
                const documentId = this.getAttribute('data-id');

                fetch('process/accept_document.php', {
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
                                text: 'Document accepted successfully!'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to accept document: ' + data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while accepting the document.'
                        });
                    });

                receiveModal.hide();
            });
        });
    </script>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>