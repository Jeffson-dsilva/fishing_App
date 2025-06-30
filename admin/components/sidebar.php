<div class="sidebar">
    <div class="sidebar-header">
        <span class="material-icons logo-icon">anchor</span>
        <h1 class="logo-text" style="font-size:24px;padding-top:10px;">FishersNet</h1>
    </div>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fa fa-tachometer-alt"></i><span class="menu-text">Dashboard</span></a></li>
        <li><a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>"><i class="fa fa-users"></i><span class="menu-text">Manage Users</span></a></li>
        <li><a href="manage_fishers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_fishers.php' ? 'active' : '' ?>"><i class="fa fa-fish"></i><span class="menu-text">Manage Fishers</span></a></li>
        <li><a href="manage_magazines.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_magazines.php' ? 'active' : '' ?>"><i class="fa fa-book"></i><span class="menu-text">Manage Magazines</span></a></li>
        <li><a href="manage_rescues.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_rescues.php' ? 'active' : '' ?>"><i class="fa fa-life-ring"></i><span class="menu-text">Manage Rescues</span></a></li>
        <li><a href="manage_feedback.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_feedback.php' ? 'active' : '' ?>"><i class="fa fa-comments"></i><span class="menu-text">Feedback</span></a></li>
        <li><a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></li>
    </ul>
</div>
