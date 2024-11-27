<?php
// Start of login_logs.php
session_start();
require_once "database.php";

// Security headers remain the same
$nonce = base64_encode(random_bytes(16));
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'nonce-{$nonce}'; style-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:;");

// Check if user is logged in and has admin privileges
if (!isset($_SESSION["email"]) || !isset($_SESSION["id"]) || !isset($_SESSION["role"])) {
    header("Location: login_admin.php");
    exit();
}

// Function to get device details
function getDeviceInfo($userAgent) {
    $device = array(
        'type' => 'Unknown',
        'os' => 'Unknown',
        'browser' => 'Unknown',
        'risk_level' => 'low'
    );
    
    if ($userAgent && is_string($userAgent)) {
        // OS Detection
        if (stripos($userAgent, 'Windows') !== false) {
            $device['os'] = 'Windows';
        } elseif (stripos($userAgent, 'Mac') !== false) {
            $device['os'] = 'MacOS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $device['os'] = 'Linux';
        }
        
        // Browser Detection
        if (stripos($userAgent, 'Chrome') !== false) {
            $device['browser'] = 'Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $device['browser'] = 'Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            $device['browser'] = 'Safari';
        }
        
        // Device Type
        if (stripos($userAgent, 'Mobile') !== false) {
            $device['type'] = 'Mobile';
        } else {
            $device['type'] = 'Desktop';
        }
    }
    
    return $device;
}

// Function to assess risk level
function assessRiskLevel($log) {
    $risk = 'low';
    
    // Check for failed attempts
    if ($log['status'] === 'failed') {
        $risk = 'high';
    }
    
    // Check for unusual IP
    if (!isset($log['ip_address']) || $log['ip_address'] === '*NULL*' || empty($log['ip_address'])) {
        $risk = 'high';
    }
    
    // Check for missing user agent
    if (!isset($log['user_agent']) || $log['user_agent'] === '*NULL*' || empty($log['user_agent'])) {
        $risk = 'medium';
    }
    
    return $risk;
}


$page_users = filter_input(INPUT_GET, 'page_users', FILTER_VALIDATE_INT) ?: 1;
$page_admin = filter_input(INPUT_GET, 'page_admin', FILTER_VALIDATE_INT) ?: 1;
$records_per_page = 10;

// Validate and sanitize page parameter
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = ($page !== false && $page > 0) ? $page : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

$offset_users = ($page_users - 1) * $records_per_page;
$user_query = "SELECT l.*, u.email, u.full_name 
               FROM login_logs l 
               LEFT JOIN users u ON l.user_id = u.id 
               ORDER BY l.timestamp DESC 
               LIMIT ? OFFSET ?";

$user_logs = [];
if ($stmt = $conn->prepare($user_query)) {
    $stmt->bind_param("ii", $records_per_page, $offset_users);
    $stmt->execute();
    $user_logs = $stmt->get_result();
}

// Fetch admin logs
$offset_admin = ($page_admin - 1) * $records_per_page;
$admin_query = "SELECT * FROM admin_activity_logs 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

$admin_logs = [];
if ($stmt = $conn->prepare($admin_query)) {
    $stmt->bind_param("ii", $records_per_page, $offset_admin);
    $stmt->execute();
    $admin_logs = $stmt->get_result();
}

// Get total pages for both tables
$total_users = $conn->query("SELECT COUNT(*) as count FROM login_logs")->fetch_assoc()['count'];
$total_admin = $conn->query("SELECT COUNT(*) as count FROM admin_activity_logs")->fetch_assoc()['count'];
$total_pages_users = ceil($total_users / $records_per_page);
$total_pages_admin = ceil($total_admin / $records_per_page);

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Login Logs - Security Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .risk-high { background-color: #ffebee; }
        .risk-medium { background-color: #fff3e0; }
        .risk-low { background-color: #f1f8e9; }
        .device-info { font-size: 0.9em; color: #666; }
        .section-divider { margin: 40px 0; border-top: 2px solid #eee; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Admin Logs Section -->
        <h2>Administrator Login Logs</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Admin ID</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($admin_logs && $admin_logs->num_rows > 0):
                        while ($log = $admin_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['admin_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['action'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['details'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['ip_address'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['created_at'] ?? ''); ?></td>
                            <td>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button type="button" class="btn btn-sm btn-danger btn-block-ip"
                                        data-ip="<?php echo htmlspecialchars($log['ip_address'] ?? ''); ?>">
                                    Block IP
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Admin Logs Pagination -->
        <nav aria-label="Admin logs pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages_admin; $i++): ?>
                    <li class="page-item <?php echo $page_admin == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page_admin=<?php echo $i; ?>&page_users=<?php echo $page_users; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <div class="section-divider"></div>

        <!-- User Logs Section -->
        <h2>User Login Logs</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Device Info</th>
                        <th>Status</th>
                        <th>Risk Level</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($user_logs && $user_logs->num_rows > 0):
                        while ($log = $user_logs->fetch_assoc()): 
                            $device = getDeviceInfo($log['user_agent'] ?? '');
                            $risk_level = assessRiskLevel($log);
                    ?>
                        <tr class="risk-<?php echo htmlspecialchars($risk_level); ?>">
                            <td><?php echo htmlspecialchars($log['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['email'] ?? 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($log['ip_address'] ?? ''); ?></td>
                            <td>
                                <div class="device-info">
                                    <strong>OS:</strong> <?php echo htmlspecialchars($device['os']); ?><br>
                                    <strong>Browser:</strong> <?php echo htmlspecialchars($device['browser']); ?><br>
                                    <strong>Type:</strong> <?php echo htmlspecialchars($device['type']); ?>
                                </div>
                            </td>
                            <td>
                                <?php if (($log['status'] ?? '') === 'success'): ?>
                                    <span class="badge bg-success">Success</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $risk_level === 'high' ? 'danger' : ($risk_level === 'medium' ? 'warning' : 'success'); ?>">
                                    <?php echo ucfirst($risk_level); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($log['timestamp'] ?? ''); ?></td>
                            <td>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button type="button" class="btn btn-sm btn-danger btn-block-ip"
                                        data-ip="<?php echo htmlspecialchars($log['ip_address'] ?? ''); ?>">
                                    Block IP
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- User Logs Pagination -->
        <nav aria-label="User logs pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages_users; $i++): ?>
                    <li class="page-item <?php echo $page_users == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page_users=<?php echo $i; ?>&page_admin=<?php echo $page_admin; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="security_logs.js" nonce="<?php echo $nonce; ?>"></script>
</body>
</html>