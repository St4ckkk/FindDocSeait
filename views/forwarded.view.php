<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Forwarded Document</title>
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
            <h1>Forwarded Document</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Forward Document</li>
                </ol>
            </nav>
        </div>

        <div class="main-section">
            <div class="documents-table">
                <div class="documents-header">
                    DOCUMENT FORWARDED FOR ACTION
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
                <table id="forwardedDocuments" class="table datatable datatable-table">
                    <thead class="table-light">
                        <tr>
                            <th>Tracking</th>
                            <th>DOC TYPE & DETAIL</th>
                            <th>FWD TO</th>
                            <th>FORWARDED</th>
                            <th>ACTIONS NEEDED</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="#">19-1299</a></td>
                            <td>
                                Leave (Form 6)<br>
                                Form 6 of Ms. Bastillada Attn: Ms. Saquin<br>
                                <small>From: Phebelyn Bastillada</small>
                            </td>
                            <td>School Personnel Group<br>Phebelyn Bastillada</td>
                            <td>02/06/19 @ 1:15 PM<br><small>by: Louie Moreno</small></td>
                            <td>for approval</td>
                            <td>
                                <button class="btn btn-info btn-sm"><i class="bi bi-forward"></i></button>
                                <button class="btn btn-primary btn-sm"><i class="bi bi-folder"></i></button>
                                <button class="btn btn-warning btn-sm"><i class="bi bi-send"></i></button>
                                <button class="btn btn-danger btn-sm"><i class="bi bi-bookmark"></i></button>
                            </td>
                        </tr>

                        <!-- Additional rows based on other entries in the table -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add new script for modal functionality -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>

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