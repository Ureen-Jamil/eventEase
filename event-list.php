<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$events = $conn->query("SELECT e.*, c.name as cat_name, COUNT(r.id) as reg_count FROM events e LEFT JOIN categories c ON e.category_id = c.id LEFT JOIN registrations r ON e.id = r.event_id GROUP BY e.id ORDER BY e.event_date ASC");
?>

<div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Manage Events</h1>
        <a href="event-create.php" class="btn btn-primary">+ Add New Event</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Title</th><th>Date</th><th>Venue</th><th>Category</th><th>Registrations</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while($event = $events->fetch_assoc()): ?>
            <tr>
                <td><?php echo $event['id']; ?></td>
                <td><?php echo $event['title']; ?></td>
                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                <td><?php echo substr($event['venue'], 0, 30); ?></td>
                <td><?php echo $event['cat_name']; ?></td>
                <td><?php echo $event['reg_count']; ?></td>
                <td>
                    <span style="background: <?php echo $event['status'] == 'upcoming' ? '#10b981' : ($event['status'] == 'ongoing' ? '#f59e0b' : '#64748b'); ?>; padding: 0.2rem 0.5rem; border-radius: 20px;">
                        <?php echo $event['status']; ?>
                    </span>
                 </td>
                <td>
                    <a href="event-update.php?id=<?php echo $event['id']; ?>" style="color: #6366f1; margin-right: 0.5rem;">Edit</a>
                    <a href="event-remove.php?id=<?php echo $event['id']; ?>" style="color: #ef4444;" onclick="return confirm('Delete this event?')">Delete</a>
                 </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
     </table>
</div>

<?php include '../includes/page-footer.php'; ?>