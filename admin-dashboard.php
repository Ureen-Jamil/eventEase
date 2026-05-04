<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$totalEvents = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$totalRegistrations = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];
$checkedIn = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE attendance = 'checked_in'")->fetch_assoc()['count'];
?>

<div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <h1 style="margin-bottom: 2rem;">Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-calendar"></i>
            <div class="stat-value"><?php echo $totalEvents; ?></div>
            <div class="stat-label">Total Events</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-label">Registered Users</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-ticket-alt"></i>
            <div class="stat-value"><?php echo $totalRegistrations; ?></div>
            <div class="stat-label">Total Registrations</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="stat-value"><?php echo $checkedIn; ?></div>
            <div class="stat-label">Checked In</div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <a href="event-list.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-list"></i>
            <h3>Manage Events</h3>
        </a>
        <a href="registration-list.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-users"></i>
            <h3>Manage Registrations</h3>
        </a>
        <a href="analytics-report.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-chart-line"></i>
            <h3>Analytics Report</h3>
        </a>
    </div>
</div>

<?php include '../includes/page-footer.php'; ?>