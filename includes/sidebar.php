<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
?>
<style>
    .main-sidebar {
        background: linear-gradient(-45deg, #2C6B3F, #388E3C, #66BB6A);
        background-size: 400% 400%;
        animation: sidebarGradient 20s ease infinite;
        color: #ffffff; /* ensure text is visible */
    }

    .main-sidebar .sidebar-brand,
    .main-sidebar .sidebar-brand-sm,
    .main-sidebar .sidebar-menu {
        background: transparent; /* inherit animated background */
    }

    .main-sidebar a,
    .main-sidebar .menu-header,
    .main-sidebar i {
        color: #ffffff !important;
    }

    .main-sidebar .sidebar-menu li a:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    @keyframes sidebarGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>


<!-- Start main left sidebar menu -->
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a>Project Hiraya</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a>Hiraya</a>

        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    <?php if ($role == 1): ?>
                        <li><a class="nav-link" href="admin.php">Product Confirmation</a></li>
                        <li><a class="nav-link" href="user_confirmation.php">Innovator Confirmation</a></li>
                        <li><a class="nav-link" href="analytics.php">Analytics</a></li>
                        <li><a class="nav-link" href="manage_user.php">Manage Users</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </aside>
</div>
