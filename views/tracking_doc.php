<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    header("Location: ../unauthorized.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FindDocSEAIT - Track Document</title>

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <style>
        /* Keep existing styles */
        body {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            background: none;
            color: #444444;
            position: relative;
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
        }

        .logo-text {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .search-bar {
            display: flex;
            gap: 5px;
        }

        .search-bar input {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
        }

        .search-bar button {
            padding: 5px 15px;
            background: white;
            border: none;
            border-radius: 3px;
            color: #333;
        }

        .page-title {
            color: #666;
            font-size: 14px;
            margin: 20px 0;
            padding-left: 15px;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }

        .form-panel,
        .login-panel {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgb(226, 123, 24);
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .form-label {
            width: 150px;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            flex: 1;
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            padding: 6px 20px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-cancel {
            background: #f1f1f1;
            color: #333;
        }

        .btn-submit {
            background: rgb(226, 123, 24);
            color: white;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .background-logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: url('assets/img/school-logo.png') no-repeat left center;
            background-size: cover;
            opacity: 0.1;
            filter: blur(5px);
            z-index: 0;
        }

        /* New styles for tracking page */
        .tracking-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .tracking-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tracking-number {
            font-size: 24px;
            color: rgb(226, 123, 24);
            font-weight: bold;
        }

        .tracking-status {
            margin-top: 10px;
            font-size: 18px;
            color: #28a745;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background: #ddd;
        }

        .timeline-item {
            margin-bottom: 30px;
            position: relative;
            width: 100%;
        }

        .timeline-content {
            position: relative;
            width: calc(50% - 30px);
            padding: 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .timeline-item:nth-child(odd) .timeline-content {
            margin-left: auto;
        }

        .timeline-content::before {
            content: '';
            position: absolute;
            width: 40px;
            /* Adjust the size as needed */
            height: 40px;
            /* Adjust the size as needed */
            background: url('assets/img/seait-logo.png') no-repeat center center;
            background-size: cover;
            top: 50%;
            transform: translateY(-50%);
        }

        .timeline-item:nth-child(odd) .timeline-content::before {
            left: -50px;
            /* Adjust the position as needed */
        }

        .timeline-item:nth-child(even) .timeline-content::before {
            right: -50px;
            /* Adjust the position as needed */
        }

        .timeline-date {
            color: rgb(226, 123, 24);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .timeline-desc {
            font-size: 14px;
            color: #666;
        }

        .document-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
            color: #666;
        }

        .info-value {
            flex: 1;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo-text">
                FindDocSEAIT
            </div>
            <div class="search-bar">
                <input type="text" id="trackingNumberInput" placeholder="Enter Tracking Number">
                <button id="searchButton">Track</button>
            </div>
        </div>
    </header>

    <div class="tracking-container" id="trackingContainer" style="display: none;">
        <div class="tracking-header">
            <div class="tracking-number" id="trackingNumber"></div>
            <div class="tracking-status" id="trackingStatus"></div>
        </div>

        <div class="document-info">
            <div class="info-row">
                <div class="info-label">Document Type:</div>
                <div class="info-value" id="documentType"></div>
            </div>
            <div class="info-row">
                <div class="info-label">Requestor:</div>
                <div class="info-value" id="requestor"></div>
            </div>
            <div class="info-row">
                <div class="info-label">Submission Date:</div>
                <div class="info-value" id="submissionDate"></div>
            </div>
            <div class="info-row">
                <div class="info-label">Expected Completion:</div>
                <div class="info-value" id="expectedCompletion"></div>
            </div>
        </div>

        <div class="timeline" id="timeline">
            <!-- Timeline items will be appended here dynamically -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Function to search document by tracking number
            function searchDocument(trackingNumber) {
                if (trackingNumber) {
                    const csrfToken = $('#csrfToken').val();
                    $.ajax({
                        url: 'process/search_doc.php', // Ensure this path is correct
                        type: 'POST',
                        data: {
                            tracking_number: trackingNumber,
                            csrf_token: csrfToken
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                const document = response.document;
                                const trackingLogs = response.tracking_logs;
                                $('#trackingNumber').text(`Tracking #: ${document.tracking_number}`);
                                $('#trackingStatus').text(`Status: ${document.status}`);
                                $('#documentType').text(document.document_type);
                                $('#requestor').text(document.submitted_by_name);
                                $('#submissionDate').text(document.created_at);
                                $('#expectedCompletion').text(document.expected_completion || 'N/A');

                                // Clear previous timeline items
                                $('#timeline').empty();

                                // Check if trackingLogs is an array
                                if (Array.isArray(trackingLogs)) {
                                    // Append new timeline items
                                    trackingLogs.forEach(log => {
                                        const timelineItem = `
                                        <div class="timeline-item">
                                            <div class="timeline-content">
                                                <div class="timeline-date">${log.created_at}</div>
                                                <div class="timeline-title">${log.title}</div>
                                                <div class="timeline-desc">${log.message}</div>
                                            </div>
                                        </div>
                                    `;
                                        $('#timeline').append(timelineItem);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Tracking logs data is not in the expected format.',
                                        confirmButtonColor: '#d33'
                                    });
                                }

                                $('#trackingContainer').show();
                                localStorage.setItem('trackingNumber', trackingNumber);
                            } else {
                                console.error(response.message || 'Document not found');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Document not found',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Connection Error:', error);
                            console.error('Response:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Connection Error',
                                text: 'Failed to connect to the server. Please try again.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            }

            // Search functionality
            $('#searchButton').click(function () {
                const trackingNumber = $('#trackingNumberInput').val();
                searchDocument(trackingNumber);
            });
        });
    </script>
</body>

</html>