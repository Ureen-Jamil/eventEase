<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

if (isset($_POST['check_in'])) {
    $reg_id = (int)$_POST['reg_id'];
    $conn->query("UPDATE registrations SET attendance = 'checked_in', check_in_time = NOW() WHERE id = $reg_id");
    header('Location: registration-list.php');
    exit();
}

$registrations = $conn->query("
    SELECT r.*, u.name as user_name, u.email, e.title as event_title, e.event_date, e.event_time
    FROM registrations r 
    JOIN users u ON r.user_id = u.id 
    JOIN events e ON r.event_id = e.id 
    ORDER BY r.registered_at DESC
");
?>

<div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Registrations Management</h1>
        <a href="../api-handlers/export-data-api.php?type=registrations" class="btn btn-primary">Export to Excel</a>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>User</th><th>Email</th><th>Event</th><th>Date</th><th>Ticket</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php while($reg = $registrations->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $reg['id']; ?></td>
                    <td><?php echo $reg['user_name']; ?></td>
                    <td><?php echo $reg['email']; ?></td>
                    <td><?php echo $reg['event_title']; ?></td>
                    <td><?php echo date('M d', strtotime($reg['event_date'])); ?></td>
                    <td><?php echo $reg['ticket_type']; ?></td>
                    <td>
                        <?php if($reg['attendance'] == 'registered'): ?>
                            <span style="background: #f59e0b; padding: 0.2rem 0.5rem; border-radius: 20px;">Pending</span>
                        <?php elseif($reg['attendance'] == 'checked_in'): ?>
                            <span style="background: #10b981; padding: 0.2rem 0.5rem; border-radius: 20px;">Checked In</span>
                        <?php else: ?>
                            <span style="background: #ef4444; padding: 0.2rem 0.5rem; border-radius: 20px;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($reg['attendance'] == 'registered'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reg_id" value="<?php echo $reg['id']; ?>">
                            <button type="submit" name="check_in" class="btn btn-primary" style="padding: 0.3rem 0.8rem;">Check In</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/page-footer.php'; ?>