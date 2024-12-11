<?php
session_start();
include_once '../includes/header.php';
include_once '../includes/sidebar.php';
include_once '../core/userController.php';

if (!isset($_SESSION['csrf_token'])) {
  header("Location: ../unauthorized.php");
  exit();
}

$userController = new userController();
$offices = $userController->getOffices();
$office_name = '';
foreach ($offices as $office) {
  if ($office['office_id'] == $_SESSION['office_id']) {
    $office_name = $office['name'];
    break;
  }
}

$userRole = $userController->getUserRole($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Submit Document</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/global.css" rel="stylesheet">
  <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

  <style>
    .submit-document {
      background: #fff;
      padding: 20px;
      border-radius: 4px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .document-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-top: 20px;
    }

    .document-item {
      background: #e27b18;
      padding: 15px;
      border-radius: 5px;
      text-align: center;
      transition: all 0.3s;
    }

    .document-item:hover {
      background: #B16218FF;
      cursor: pointer;
    }

    .document-item i {
      font-size: 24px;
      color: white;
      margin-bottom: 8px;
    }

    .document-item span {
      display: block;
      font-size: 13px;
      color: white;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      font-weight: 500;
      color: #444;
      margin-bottom: 0.5rem;
    }

    .form-control {
      border: 1px solid #ddd;
      padding: 0.5rem;
    }

    .form-control:disabled {
      background-color: #f8f9fa;
    }

    .btn-submit {
      background: #0d6efd;
      color: white;
      padding: 8px 20px;
    }

    .btn-cancel {
      background: #6c757d;
      color: white;
      padding: 8px 20px;
    }

    .main-section {
      max-width: 1200px;
    }

    .documents-section {
      background: white;
      padding: 20px;
      border-radius: 4px;
    }

    .section-title {
      font-size: 18px;
      color: #333;
      margin-bottom: 15px;
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

    .submit-document {
      transition: opacity 0.5s ease-in-out;
    }

    .submit-document.hidden {
      opacity: 0;
      pointer-events: none;
    }

    .btn-cancel {
      background-color: #f44336;
      color: white;
      padding: 5px 10px;
      font-size: 14px;
    }

    .btn-submit {
      background-color: #4CAF50;
      color: white;
      padding: 5px 10px;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Submit or Request Document</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Submit or Request Document</li>
        </ol>
      </nav>
    </div>

    <div class="main-section">
      <div class="row">
        <div class="document-choice mb-4">
          <button id="submitBtn" class="btn btn-primary me-2">Submit</button>
          <button id="requestBtn" class="btn btn-secondary">Request</button>
        </div>
        <div class="col-md-8">
          <div id="submitForm" class="submit-document">
            <form id="submitDocumentForm" action="process/submit_document.php" method="POST"
              enctype="multipart/form-data">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">From (South East Asian Institute Of Technology):</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" value="<?php echo $office_name; ?>" readonly>
                  <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">By:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" value="<?php echo $_SESSION['fullname']; ?>" readonly>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Document Type:</label>
                <div class="col-sm-8">
                  <select class="form-control" name="document_type">
                    <option>Leave (Form 6)</option>
                    <option>Report Card</option>
                    <option>Transcript of Records</option>
                    <option>Certificate of Enrollment</option>
                    <option>Good Moral Certificate</option>
                    <option>Diploma</option>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Details:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" name="details" rows="3"></textarea>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Purpose of Submission:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" name="purpose" rows="3"
                    placeholder="Purposes or Actions to be taken..."></textarea>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">To:</label>
                <div class="col-sm-8">
                  <select class="form-control" name="office">
                    <option selected disabled>Select Office</option>
                    <?php foreach ($offices as $office): ?>
                      <option value="<?php echo $office['office_id']; ?>">
                        <?php echo htmlspecialchars($office['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Upload Document <span class="text-danger">(PDF
                    only)</span>:</label>
                <div class="col-sm-8">
                  <input type="file" class="form-control" name="document" accept="application/pdf">
                </div>
              </div>

              <div class="form-group row">
                <div class="col-sm-12 text-end">
                  <button type="button" class="btn btn-cancel me-2">Cancel</button>
                  <button type="submit" class="btn btn-submit">Submit</button>
                </div>
              </div>
            </form>
          </div>

          <div id="requestForm" class="submit-document" style="display: none;">
            <form id="documentRequestForm" class="row g-3" action="process/request_document.php" method="POST">
              <div class="col-12">
                <label for="from" class="form-label">From:</label>
                <input type="text" class="form-control" id="from" name="requestor_name"
                  value="<?php echo $_SESSION['fullname']; ?>" readonly>
              </div>

              <div class="col-12">
                <label for="documentType" class="form-label">Document Type:</label>
                <select class="form-select" id="documentType" name="document_type" required>
                  <option selected disabled>--Select--</option>
                  <option>Transcript of Records</option>
                  <option>Certificate of Enrollment</option>
                  <option>Good Moral Certificate</option>
                  <option>Diploma</option>
                </select>
              </div>

              <div class="col-12">
                <label for="details" class="form-label">Details:</label>
                <textarea class="form-control" id="details" name="details" rows="4"
                  placeholder="Description, Date, Destination" required></textarea>
              </div>

              <div class="col-12">
                <label for="purpose" class="form-label">Purpose of Request:</label>
                <textarea class="form-control" id="purpose" name="purpose" rows="4"
                  placeholder="Purposes or Actions to be taken..." required></textarea>
              </div>

              <div class="col-12">
                <label for="receivingUnit" class="form-label">Office / School Office</label>
                <select name="office" id="receivingUnit" class="form-select" required>
                  <option selected disabled>Select Office</option>
                  <?php foreach ($offices as $office): ?>
                    <option value="<?php echo $office['office_id']; ?>">
                      <?php echo htmlspecialchars($office['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="text-end">
                <button type="button" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>

        <div class="col-md-4">
          <div class="documents-section">
            <h5 class="section-title">Documents</h5>
            <div class="document-grid">
              <div class="document-item">
                <i class="bi bi-upc-scan"></i>
                <span>Receiving New</span>
              </div>
              <div class="document-item">
                <i class="bi bi-clock-history"></i>
                <span>Pending</span>
              </div>
              <div class="document-item">
                <i class="bi bi-folder"></i>
                <span>All Documents</span>
              </div>
            </div>

            <?php if ($userRole === 'Admin' || $userRole === 'Super Admin'): ?>
              <h5 class="section-title mt-4">Others</h5>
              <div class="document-grid">
                <a href="unit_users.view.php">
                  <div class="document-item">
                    <i class="bi bi-people"></i>
                    <span>Unit Users</span>
                  </div>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.getElementById('submitBtn').addEventListener('click', function () {
      document.getElementById('submitForm').style.display = 'block';
      document.getElementById('requestForm').style.display = 'none';
    });

    document.getElementById('requestBtn').addEventListener('click', function () {
      document.getElementById('submitForm').style.display = 'none';
      document.getElementById('requestForm').style.display = 'block';
    });

    $(document).ready(function () {
      $('#submitDocumentForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission
        const formData = new FormData(this);
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
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
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Document submitted successfully!'
              });
              $('#submitDocumentForm')[0].reset();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message || 'Error submitting document'
              });
            }
          },
          error: function (xhr, status, error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'AJAX request failed: ' + error
            });
          }
        });
      });

      $('#documentRequestForm').on('submit', function (e) {
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
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Document requested successfully!'
            });
            $('#documentRequestForm')[0].reset();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message || 'Error requesting document'
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
    });
  </script>

  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="assets/vendors/chart.js/Chart.min.js"></script>
  <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="assets/js/dashboard.js"></script>
  <script src="assets/js/Chart.roundedBarCharts.js"></script>
</body>

</html>