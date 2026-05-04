<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

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
    
    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, venue, capacity, category_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $title, $description, $event_date, $event_time, $venue, $capacity, $category_id, $status);
    
    if ($stmt->execute()) {
        header('Location: event-list.php?msg=created');
        exit();
    } else {
        $error = "Failed to create event.";
    }
}
?>

<div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <h1>Create New Event</h1>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="glass" style="padding: 2rem;">
        <div class="form-group">
            <label>Event Title</label>
            <input type="text" name="title" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Event Date</label>
            <input type="date" name="event_date" required>
        </div>
        
        <div class="form-group">
            <label>Event Time</label>
            <input type="time" name="event_time" required>
        </div>
        
        <div class="form-group">
            <label>Venue</label>
            <input type="text" name="venue" required>
        </div>
        
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" required>
        </div>
        
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Event</button>
        <a href="event-list.php" class="btn btn-outline">Cancel</a>
    </form>
</div>

<?php include '../includes/page-footer.php'; ?>