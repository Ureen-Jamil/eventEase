<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireUser();

$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel'])) {
    $reg_id = (int)$_GET['cancel'];
    $conn->query("UPDATE registrations SET attendance = 'cancelled' WHERE id = $reg_id AND user_id = $user_id");
    header('Location: my-registrations.php');
    exit();
}

$registrations = $conn->query("
    SELECT r.*, e.title, e.event_date, e.event_time, e.venue 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.user_id = $user_id 
    ORDER BY e.event_date DESC
");
?>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <h1>My Registrations</h1>
    
    <?php if($registrations->num_rows == 0): ?>
        <div class="alert alert-warning">You haven't registered for any events. <a href="../all-events.php">Browse Events</a></div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>Event</th><th>Date</th><th>Venue</th><th>Ticket</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while($reg = $registrations->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $reg['title']; ?></strong></td>
                    <td><?php echo date('M d, Y', strtotime($reg['event_date'])); ?></td>
                    <td><?php echo substr($reg['venue'], 0, 30); ?></td>
                    <td><?php echo $reg['ticket_type']; ?></td>
                    <td>
                        <?php if($reg['attendance'] == 'registered'): ?>
                            <span style="background: #f59e0b; padding: 0.2rem 0.5rem; border-radius: 20px;">Registered</span>
                        <?php elseif($reg['attendance'] == 'checked_in'): ?>
                            <span style="background: #10b981; padding: 0.2rem 0.5rem; border-radius: 20px;">Checked In</span>
                        <?php else: ?>
                            <span style="background: #ef4444; padding: 0.2rem 0.5rem; border-radius: 20px;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../qr-generator.php?reg_id=<?php echo $reg['id']; ?>" class="btn btn-primary" style="padding: 0.3rem 0.8rem;">Ticket</a>
                        <?php if($reg['attendance'] == 'registered'): ?>
                            <a href="?cancel=<?php echo $reg['id']; ?>" onclick="return confirm('Cancel registration?')" class="btn btn-outline" style="padding: 0.3rem 0.8rem;">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/page-footer.php'; ?>