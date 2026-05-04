<?php 
session_start();
include 'config/db-connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header('Location: admin-panel/admin-dashboard.php');
                exit();
            } else {
                header('Location: user-panel/user-dashboard.php');
                exit();
            }
        } else {
            $error = 'Invalid password!';
        }
    } else {
        $error = 'Email not found!';
    }
}

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin-panel/admin-dashboard.php');
    } else {
        header('Location: user-panel/user-dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventEase</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/eventease-styles.css">
</head>
<body>

<div class="form-container">
    <div class="glass" style="padding: 2rem;">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">Login to EventEase</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        
        <p style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: #64748b;">
            Demo Admin: admin@eventease.com / admin123
        </p>
    </div>
</div>

</body>
</html>