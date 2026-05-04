<?php 
include 'includes/page-header.php'; 
include 'config/db-connection.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to EventEase</h1>
        <p>Smart Event Management System with QR Code Ticketing</p>
        <a href="all-events.php" class="btn btn-primary">Explore Events</a>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <h2 class="section-title">Browse by Category</h2>
    <div class="categories-grid">
        <?php
        $cats = $conn->query("SELECT * FROM categories LIMIT 8");
        while($cat = $cats->fetch_assoc()):
        ?>
        <div class="category-card" onclick="window.location.href='all-events.php?cat=<?php echo $cat['id']; ?>'">
            <i class="<?php echo $cat['icon']; ?>"></i>
            <span><?php echo $cat['name']; ?></span>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Events -->
<h2 class="section-title">Featured Events</h2>
<div class="events-grid">
    <?php
    $result = $conn->query("SELECT e.*, c.name as cat_name FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.status = 'upcoming' ORDER BY e.event_date ASC LIMIT 6");
    if($result->num_rows > 0):
    while($event = $result->fetch_assoc()):
    ?>
    <div class="event-card">
        <div class="event-image">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="event-content">
            <span class="event-category-badge"><?php echo $event['cat_name']; ?></span>
            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
            <div class="event-meta">
                <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?php echo substr($event['venue'], 0, 20); ?></span>
            </div>
            <p class="event-desc"><?php echo substr($event['description'], 0, 100); ?>...</p>
            <a href="single-event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">View Details</a>
        </div>
    </div>
    <?php endwhile; else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>No Events Found</h3>
        <p>Check back later for upcoming events!</p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/page-footer.php'; ?>