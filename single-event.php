<?php
session_start();
include 'config/db-connection.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$event = $conn->query("SELECT e.*, c.name as cat_name FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = $event_id")->fetch_assoc();

if (!$event) {
    header('Location: all-events.php');
    exit();
}

$isRegistered = false;
$registration_id = null;
if (isset($_SESSION['user_id'])) {
    $check = $conn->query("SELECT id FROM registrations WHERE user_id = {$_SESSION['user_id']} AND event_id = $event_id");
    if ($check->num_rows > 0) {
        $isRegistered = true;
        $registration_id = $check->fetch_assoc()['id'];
    }
}

$regCount = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE event_id = $event_id AND attendance != 'cancelled'")->fetch_assoc()['count'];
$availableSeats = $event['capacity'] - $regCount;
?>

<?php include 'includes/page-header.php'; ?>

<div style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
        <div>
            <div style="height: 400px; background: linear-gradient(135deg, #4f46e5, #ec4899); border-radius: 24px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-alt" style="font-size: 5rem; color: white;"></i>
            </div>
        </div>
        <div>
            <span style="background: #6366f1; padding: 0.3rem 1rem; border-radius: 20px; display: inline-block;"><?php echo $event['cat_name']; ?></span>
            <h1 style="font-size: 2.5rem; margin: 1rem 0;"><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <div style="margin: 1rem 0;">
                <p><i class="fas fa-calendar"></i> <?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                <p><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['venue']); ?></p>
                <p><i class="fas fa-users"></i> Capacity: <?php echo $event['capacity']; ?> | Available: <?php echo $availableSeats; ?></p>
            </div>
            
            <p style="margin: 1rem 0; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            
            <?php if($availableSeats <= 0): ?>
                <div class="alert alert-warning">Event is fully booked!</div>
            <?php elseif(!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-primary">Login to Register</a>
            <?php elseif($isRegistered): ?>
                <div class="alert alert-success">You are registered for this event!</div>
                <a href="qr-generator.php?reg_id=<?php echo $registration_id; ?>" class="btn btn-primary">Download Ticket</a>
            <?php else: ?>
                <a href="user-panel/event-signup.php?id=<?php echo $event_id; ?>" class="btn btn-primary">Register Now</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/page-footer.php'; ?>