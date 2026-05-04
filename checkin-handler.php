<?php
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$ticket_id = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;

$ticket = $conn->query("SELECT r.*, e.title, u.name FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = $ticket_id")->fetch_assoc();

if (!$ticket) {
    header('Location: registration-list.php?error=invalid');
    exit();
}

if ($ticket['attendance'] == 'checked_in') {
    header('Location: registration-list.php?error=already&user=' . urlencode($ticket['name']) . '&event=' . urlencode($ticket['title']));
    exit();
}

$conn->query("UPDATE registrations SET attendance = 'checked_in', check_in_time = NOW() WHERE id = $ticket_id");

header('Location: registration-list.php?success=checked&user=' . urlencode($ticket['name']) . '&event=' . urlencode($ticket['title']));
exit();
?>