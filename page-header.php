<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventEase - Smart Event Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/eventease-styles.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-calendar-check"></i>
            <span>Event<span class="logo-highlight">Ease</span></span>
        </a>
        
        <button class="nav-toggle" id="navToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="all-events.php"><i class="fas fa-calendar"></i> Events</a></li>
            <li><a href="calendar-view.php"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin-panel/admin-dashboard.php"><i class="fas fa-chalkboard-user"></i> Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="user-panel/user-dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-btn"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="register.php" class="nav-btn nav-btn-primary"><i class="fas fa-user-plus"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main>

<script>
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');
if (navToggle) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}
</script>