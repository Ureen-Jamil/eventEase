<?php
include '../includes/page-header.php';
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$categoryStats = $conn->query("SELECT c.name, COUNT(e.id) as count FROM categories c LEFT JOIN events e ON c.id = e.category_id GROUP BY c.id");
$monthlyRegistrations = $conn->query("SELECT DATE_FORMAT(registered_at, '%M') as month, COUNT(*) as count FROM registrations WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY MONTH(registered_at)");
$popularEvents = $conn->query("SELECT e.title, COUNT(r.id) as registrations FROM events e LEFT JOIN registrations r ON e.id = r.event_id GROUP BY e.id ORDER BY registrations DESC LIMIT 5");
?>

<div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <h1 style="margin-bottom: 2rem;">Analytics & Reports</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        <div class="glass" style="padding: 1.5rem;">
            <h3>Events by Category</h3>
            <canvas id="categoryChart" style="max-height: 300px;"></canvas>
        </div>
        
        <div class="glass" style="padding: 1.5rem;">
            <h3>Monthly Registrations</h3>
            <canvas id="registrationsChart" style="max-height: 300px;"></canvas>
        </div>
        
        <div class="glass" style="padding: 1.5rem;">
            <h3>Most Popular Events</h3>
            <canvas id="popularityChart" style="max-height: 300px;"></canvas>
        </div>
        
        <div class="glass" style="padding: 1.5rem;">
            <h3>Export Reports</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="../api-handlers/export-data-api.php?type=events" class="btn btn-primary">Export Events</a>
                <a href="../api-handlers/export-data-api.php?type=users" class="btn btn-primary">Export Users</a>
                <a href="../api-handlers/export-data-api.php?type=registrations" class="btn btn-primary">Export Registrations</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Category Chart
const catData = <?php 
    $labels = []; $counts = [];
    while($row = $categoryStats->fetch_assoc()) {
        $labels[] = $row['name'];
        $counts[] = $row['count'];
    }
    echo json_encode(['labels' => $labels, 'data' => $counts]);
?>;
new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: { labels: catData.labels, datasets: [{ data: catData.data, backgroundColor: ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] }] }
});

// Registrations Chart
const regData = <?php 
    $months = []; $counts = [];
    while($row = $monthlyRegistrations->fetch_assoc()) {
        $months[] = $row['month'];
        $counts[] = $row['count'];
    }
    echo json_encode(['labels' => $months, 'data' => $counts]);
?>;
new Chart(document.getElementById('registrationsChart'), {
    type: 'line',
    data: { labels: regData.labels, datasets: [{ label: 'Registrations', data: regData.data, borderColor: '#6366f1', fill: true }] }
});

// Popularity Chart
const popData = <?php 
    $titles = []; $counts = [];
    while($row = $popularEvents->fetch_assoc()) {
        $titles[] = substr($row['title'], 0, 20);
        $counts[] = $row['registrations'];
    }
    echo json_encode(['labels' => $titles, 'data' => $counts]);
?>;
new Chart(document.getElementById('popularityChart'), {
    type: 'bar',
    data: { labels: popData.labels, datasets: [{ label: 'Registrations', data: popData.data, backgroundColor: '#6366f1' }] }
});
</script>

<?php include '../includes/page-footer.php'; ?>