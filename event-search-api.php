<?php
header('Content-Type: application/json');
include '../config/db-connection.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'upcoming';

$sql = "SELECT e.*, c.name as cat_name FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.status = '$status'";

if (!empty($search)) {
    $sql .= " AND (e.title LIKE '%$search%' OR e.description LIKE '%$search%' OR e.venue LIKE '%$search%')";
}

if ($category > 0) {
    $sql .= " AND e.category_id = $category";
}

$sql .= " ORDER BY e.event_date ASC";

$result = $conn->query($sql);
$count = $result->num_rows;

$html = '';
if ($count > 0) {
    while($event = $result->fetch_assoc()) {
        $html .= '
        <div class="event-card">
            <div class="event-image">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="event-content">
                <span class="event-category-badge">'.$event['cat_name'].'</span>
                <h3 class="event-title">'.htmlspecialchars($event['title']).'</h3>
                <div class="event-meta">
                    <span><i class="fas fa-calendar"></i> '.date('M d, Y', strtotime($event['event_date'])).'</span>
                    <span><i class="fas fa-clock"></i> '.date('h:i A', strtotime($event['event_time'])).'</span>
                    <span><i class="fas fa-map-marker-alt"></i> '.substr($event['venue'], 0, 20).'</span>
                </div>
                <p class="event-desc">'.substr($event['description'], 0, 100).'...</p>
                <a href="single-event.php?id='.$event['id'].'" class="btn btn-primary">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>';
    }
} else {
    $html = '<div class="empty-state" style="grid-column: 1/-1;">
        <i class="fas fa-calendar-times"></i>
        <h3>No events found</h3>
        <p>Try adjusting your search or filters</p>
    </div>';
}

echo json_encode(['html' => $html, 'count' => $count]);
?>