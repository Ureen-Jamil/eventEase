<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireUser();

$user_id = $_SESSION['user_id'];

$tickets = $conn->query("
    SELECT r.*, e.title, e.event_date, e.event_time, e.venue 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.user_id = $user_id AND r.attendance != 'cancelled' 
    ORDER BY e.event_date ASC
");
?>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <h1>My Digital Tickets</h1>
    
    <?php if($tickets->num_rows == 0): ?>
        <div class="alert alert-warning">You don't have any tickets yet. <a href="../all-events.php">Browse Events</a></div>
    <?php else: ?>
        <div class="events-grid">
            <?php while($ticket = $tickets->fetch_assoc()): 
                $qrData = "Ticket ID: " . $ticket['id'] . " | Event: " . $ticket['title'] . " | Name: " . $_SESSION['user_name'];
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
            ?>
            <div class="event-card" style="text-align: center;">
                <div class="event-image" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-ticket-alt" style="font-size: 3rem; color: white;"></i>
                </div>
                <div class="event-content">
                    <h3><?php echo $ticket['title']; ?></h3>
                    <p><?php echo date('M d, Y', strtotime($ticket['event_date'])); ?> at <?php echo date('h:i A', strtotime($ticket['event_time'])); ?></p>
                    <p><strong>Ticket Type:</strong> <?php echo $ticket['ticket_type']; ?></p>
                    <div style="margin: 1rem 0;">
                        <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 120px;">
                    </div>
                    <a href="../qr-generator.php?reg_id=<?php echo $ticket['id']; ?>" class="btn btn-primary">Download Ticket</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/page-footer.php'; ?>