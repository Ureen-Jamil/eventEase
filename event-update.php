<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = $conn->query("SELECT * FROM events WHERE id = $id")->fetch_assoc();

if (!$event) {
    header('Location: event-list.php');
    exit();
}

$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = $_POST['venue'];
    $capacity = $_POST['capacity'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, venue=?, capacity=?, category_id=?, status=? WHERE id=?");
    $stmt->bind_param("sssssiisi", $title, $description, $event_date, $event_time, $venue, $capacity, $category_id, $status, $id);
    
    if ($stmt->execute()) {
        header('Location: event-list.php?msg=updated');
        exit();
    }
}
?>

<div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <h1>Edit Event</h1>
    
    <form method="POST" class="glass" style="padding: 2rem;">
        <div class="form-group">
            <label>Event Title</label>
            <input type="text" name="title" value="<?php echo $event['title']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?php echo $event['description']; ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Event Date</label>
            <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Event Time</label>
            <input type="time" name="event_time" value="<?php echo $event['event_time']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Venue</label>
            <input type="text" name="venue" value="<?php echo $event['venue']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" value="<?php echo $event['capacity']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Category</label>
            <select name="category_id">
                <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $event['category_id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="upcoming" <?php echo $event['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                <option value="ongoing" <?php echo $event['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                <option value="completed" <?php echo $event['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="event-list.php" class="btn btn-outline">Cancel</a>
    </form>
</div>

<?php include '../includes/page-footer.php'; ?>