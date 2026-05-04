<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireUser();

$user_id = $_SESSION['user_id'];

$myRegistrations = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE user_id = $user_id")->fetch_assoc()['count'];
$upcomingEvents = $conn->query("SELECT COUNT(*) as count FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.user_id = $user_id AND e.event_date >= CURDATE()")->fetch_assoc()['count'];
$checkedIn = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE user_id = $user_id AND attendance = 'checked_in'")->fetch_assoc()['count'];

$myEvents = $conn->query("SELECT e.*, r.id as reg_id, r.ticket_type, r.attendance FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.user_id = $user_id AND e.event_date >= CURDATE() ORDER BY e.event_date ASC LIMIT 5");
?>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-ticket-alt"></i>
            <div class="stat-value"><?php echo $myRegistrations; ?></div>
            <div class="stat-label">Total Registrations</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-day"></i>
            <div class="stat-value"><?php echo $upcomingEvents; ?></div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="stat-value"><?php echo $checkedIn; ?></div>
            <div class="stat-label">Checked In</div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <a href="my-registrations.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-list"></i>
            <h3>My Registrations</h3>
        </a>
        <a href="my-qrcodes.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-qrcode"></i>
            <h3>My Tickets</h3>
        </a>
        <a href="../all-events.php" class="stat-card" style="text-decoration: none;">
            <i class="fas fa-search"></i>
            <h3>Discover Events</h3>
        </a>
    </div>
    
    <?php if($myEvents->num_rows > 0): ?>
    <h2>Your Upcoming Events</h2>
    <div class="events-grid">
        <?php while($event = $myEvents->fetch_assoc()): ?>
        <div class="event-card">
            <div class="event-image" style="height: 150px;"></div>
            <div class="event-content">
                <h3><?php echo $event['title']; ?></h3>
                <p><?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                <a href="../qr-generator.php?reg_id=<?php echo $event['reg_id']; ?>" class="btn btn-primary">Get Ticket</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/page-footer.php'; ?>