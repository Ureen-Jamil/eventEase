<?php
include '../config/db-connection.php';
include '../includes/session-auth.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn->query("DELETE FROM registrations WHERE event_id = $id");
$conn->query("DELETE FROM events WHERE id = $id");

header('Location: event-list.php?msg=deleted');
exit();
?>