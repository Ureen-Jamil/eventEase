<?php
// NO session_start() HERE!

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /eventease_project/login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /eventease_project/index.php');
        exit();
    }
}

function requireUser() {
    requireLogin();
    if (isAdmin()) {
        header('Location: /eventease_project/admin-panel/admin-dashboard.php');
        exit();
    }
}
?>