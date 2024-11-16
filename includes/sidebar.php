<aside id="sidebar" class="sidebar">
    <div class="sidebar-logo">
        <a href="#" class="logo">
            <img src="assets/img/logo.png" alt="Logo">
        </a>
        <div class="welcome-message">
            <p>Welcome, User</p>
        </div>
    </div>

    <!-- Search Box with Icon -->
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search Tracking">
    </div>

    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link active" href="../views/dashboard.php">
                <i class="bi bi-house"></i>
                <span>HOME</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../views/receiving.view.php">
                <i class="bi bi-upc-scan"></i>
                <span>Receiving New</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../views/pending.view.php">
                <i class="bi bi-arrow-left-right"></i>
                <span>Routed/Incoming</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../views/pending.view.php">
                <i class="bi bi-clock"></i>
                <span>Pending</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../views/forwarded.view.php">
                <i class="bi bi-forward"></i>
                <span>Forwarded</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../views/defferred.view.php">
                <i class="bi bi-exclamation-circle"></i>
                <span>Deferred</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#requests-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-envelope"></i>
                <span>Requests</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="requests-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../views/requested.view.php">
                        <i class="bi bi-envelope"></i>
                        <span>New Request</span>
                    </a>
                </li>
                <li>
                    <a href="../views/pending_request.view.php">
                        <i class="bi bi-envelope-open"></i>
                        <span>Pending Requests</span>
                    </a>
                </li>
                <li>
                    <a href="../views/completed_request.view.php">
                        <i class="bi bi-envelope-check"></i>
                        <span>Completed Requests</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-plus-square"></i>
                <span>Submit New</span>
            </a>
        </li>


    </ul>
</aside><!-- End Sidebar-->

<!-- Include Bootstrap Icons CSS -->