<?php
header('Content-Type: application/json');
include '../config/db-connection.php';

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$start_date = "$year-$month-01";
$end_date = date('Y-m-t', strtotime($start_date));

$events = $conn->query("
    SELECT e.*, c.name as category_name 
    FROM events e 
    LEFT JOIN categories c ON e.category_id = c.id 
    WHERE e.event_date BETWEEN '$start_date' AND '$end_date'
    ORDER BY e.event_date ASC
");

$calendar_data = [];
while($event = $events->fetch_assoc()) {
    $day = (int)date('j', strtotime($event['event_date']));
    if (!isset($calendar_data[$day])) {
        $calendar_data[$day] = [];
    }
    $calendar_data[$day][] = [
        'id' => $event['id'],
        'title' => $event['title'],
        'time' => date('h:i A', strtotime($event['event_time'])),
        'venue' => $event['venue'],
        'category' => $event['category_name']
    ];
}

echo json_encode([
    'success' => true,
    'month' => $month,
    'year' => $year,
    'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
    'total_days' => date('t', strtotime($start_date)),
    'first_day_of_month' => date('N', strtotime($start_date)),
    'events' => $calendar_data
]);
?>