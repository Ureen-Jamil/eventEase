<?php
include '../config/db-connection.php';
session_start();

// Only admin can export
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied");
}

$type = isset($_GET['type']) ? $_GET['type'] : '';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="eventease-' . $type . '-' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($type == 'events') {
    fputcsv($output, ['ID', 'Title', 'Description', 'Date', 'Time', 'Venue', 'Capacity', 'Status']);
    $data = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
    while($row = $data->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['title'], strip_tags($row['description']), $row['event_date'], $row['event_time'], $row['venue'], $row['capacity'], $row['status']]);
    }
}
elseif ($type == 'users') {
    fputcsv($output, ['ID', 'Name', 'Email', 'Role', 'Registered On']);
    $data = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    while($row = $data->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['name'], $row['email'], $row['role'], $row['created_at']]);
    }
}
elseif ($type == 'registrations') {
    fputcsv($output, ['ID', 'User', 'Event', 'Ticket Type', 'Attendance', 'Registered At', 'Check In Time']);
    $data = $conn->query("SELECT r.*, u.name as user_name, e.title as event_title FROM registrations r JOIN users u ON r.user_id = u.id JOIN events e ON r.event_id = e.id ORDER BY r.registered_at DESC");
    while($row = $data->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['user_name'], $row['event_title'], $row['ticket_type'], $row['attendance'], $row['registered_at'], $row['check_in_time']]);
    }
}

fclose($output);
exit();
?>