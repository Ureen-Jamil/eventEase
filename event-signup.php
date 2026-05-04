<?php
// NO SPACES BEFORE THIS LINE - START SESSION FIRST
session_start();

include '../config/db-connection.php';
include '../includes/session-auth.php';
requireUser();

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = $conn->query("SELECT e.*, c.name as cat_name FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = $event_id")->fetch_assoc();

if (!$event) {
    header('Location: ../all-events.php');
    exit();
}

$check = $conn->query("SELECT id FROM registrations WHERE user_id = {$_SESSION['user_id']} AND event_id = $event_id");
if ($check->num_rows > 0) {
    header('Location: my-registrations.php');
    exit();
}

$regCount = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE event_id = $event_id AND attendance != 'cancelled'")->fetch_assoc()['count'];
$availableSeats = $event['capacity'] - $regCount;
$seatPercentage = ($regCount / $event['capacity']) * 100;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $ticket_type = $_POST['ticket_type'];
    $special_requests = $_POST['special_requests'];
    
    $qr_data = "Ticket_ID:" . uniqid() . "|Event:" . $event['title'] . "|User:" . $_SESSION['user_id'] . "|Name:" . $full_name;
    
    $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id, ticket_type, qr_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $_SESSION['user_id'], $event_id, $ticket_type, $qr_data);
    
    if ($stmt->execute()) {
        $reg_id = $stmt->insert_id;
        header("Location: ../qr-generator.php?reg_id=$reg_id&success=1");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for <?php echo htmlspecialchars($event['title']); ?> | EventEase</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            color: white;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.3), transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(236, 72, 153, 0.3), transparent 50%);
            animation: bgMove 20s ease-in-out infinite;
        }

        @keyframes bgMove {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-5%, -5%) rotate(2deg); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .registration-wrapper {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
            background: rgba(15, 25, 45, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 48px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .event-info {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(236, 72, 153, 0.2));
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .category-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.2rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .event-info h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin: 2rem 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s;
        }

        .info-item:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .info-text h4 {
            font-size: 0.7rem;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }

        .info-text p {
            font-size: 1rem;
            font-weight: 600;
        }

        .seats-section {
            margin-top: 2rem;
            padding: 1.2rem;
            background: rgba(0,0,0,0.3);
            border-radius: 20px;
        }

        .seats-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .progress-bar {
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 10px;
            transition: width 0.5s ease;
            width: <?php echo $seatPercentage; ?>%;
        }

        .form-section {
            padding: 2.5rem;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 40px;
            right: 40px;
            height: 2px;
            background: rgba(255,255,255,0.1);
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            border-color: transparent;
            box-shadow: 0 0 20px rgba(99,102,241,0.5);
        }

        .step.completed .step-circle {
            background: #10b981;
            border-color: transparent;
        }

        .step-label {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .step.active .step-label {
            color: #a5b4fc;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .ticket-card {
            background: rgba(255,255,255,0.05);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            border-color: #6366f1;
        }

        .ticket-card.selected {
            background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(236,72,153,0.2));
            border-color: #6366f1;
            box-shadow: 0 0 30px rgba(99,102,241,0.3);
        }

        .ticket-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .ticket-card h4 {
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .form-step {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            margin-bottom: 1.2rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            color: white;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: #6366f1;
            background: rgba(99,102,241,0.1);
        }

        .summary-card {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(236,72,153,0.15));
            border-radius: 24px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .form-nav {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-prev, .btn-next, .btn-submit {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.9rem;
        }

        .btn-prev {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .btn-prev:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(-3px);
        }

        .btn-next {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
        }

        .btn-next:hover {
            transform: translateX(3px);
            box-shadow: 0 5px 20px rgba(99,102,241,0.4);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16,185,129,0.5);
        }

        @media (max-width: 968px) {
            .registration-wrapper {
                grid-template-columns: 1fr;
            }
            .ticket-grid {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="bg-animation"></div>

<div class="container">
    <div class="registration-wrapper">
        <div class="event-info">
            <span class="category-badge">
                <i class="fas fa-tag"></i> <?php echo $event['cat_name']; ?>
            </span>
            <h1><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="info-text">
                        <h4>DATE</h4>
                        <p><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-text">
                        <h4>TIME</h4>
                        <p><?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <h4>VENUE</h4>
                        <p><?php echo htmlspecialchars($event['venue']); ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-users"></i></div>
                    <div class="info-text">
                        <h4>CAPACITY</h4>
                        <p><?php echo number_format($event['capacity']); ?> people</p>
                    </div>
                </div>
            </div>

            <div class="seats-section">
                <div class="seats-header">
                    <span><i class="fas fa-chair"></i> Seats Available</span>
                    <span><?php echo $availableSeats; ?> / <?php echo $event['capacity']; ?></span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <?php if($availableSeats <= 20): ?>
                <div style="margin-top: 0.8rem; font-size: 0.75rem; color: #f59e0b; text-align: center;">
                    <i class="fas fa-exclamation-triangle"></i> Only <?php echo $availableSeats; ?> seats left! Hurry up!
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-section">
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">TICKET</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">DETAILS</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">CONFIRM</div>
                </div>
            </div>

            <form method="POST" id="registerForm">
                <div class="form-step active" id="step1">
                    <h3 style="margin-bottom: 1rem;">🎫 Choose Your Ticket</h3>
                    <div class="ticket-grid">
                        <div class="ticket-card selected" data-ticket="Standard">
                            <i class="fas fa-ticket-alt"></i>
                            <h4>Standard</h4>
                            <div class="price">FREE</div>
                        </div>
                        <div class="ticket-card" data-ticket="VIP">
                            <i class="fas fa-crown"></i>
                            <h4>VIP</h4>
                            <div class="price">PREMIUM</div>
                        </div>
                        <div class="ticket-card" data-ticket="Early Bird">
                            <i class="fas fa-bird"></i>
                            <h4>Early Bird</h4>
                            <div class="price">LIMITED</div>
                        </div>
                    </div>
                    <input type="hidden" name="ticket_type" id="ticket_type" value="Standard">
                </div>

                <div class="form-step" id="step2">
                    <h3 style="margin-bottom: 1.5rem;">👤 Your Information</h3>
                    <div class="input-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" id="email" placeholder="you@example.com" required>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" id="phone" placeholder="+92 XXX XXXXXXX">
                    </div>
                    <div class="input-group">
                        <label>Special Requests</label>
                        <textarea name="special_requests" id="special_requests" rows="3" placeholder="Dietary restrictions, accessibility needs, etc..."></textarea>
                    </div>
                </div>

                <div class="form-step" id="step3">
                    <h3 style="margin-bottom: 1.5rem;">✅ Review & Confirm</h3>
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Event</span>
                            <span><strong><?php echo $event['title']; ?></strong></span>
                        </div>
                        <div class="summary-row">
                            <span>Date & Time</span>
                            <span><?php echo date('M d, Y', strtotime($event['event_date'])); ?> • <?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Ticket Type</span>
                            <span id="summaryTicket">Standard</span>
                        </div>
                        <div class="summary-row">
                            <span>Attendee Name</span>
                            <span id="summaryName">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Email</span>
                            <span id="summaryEmail">-</span>
                        </div>
                    </div>
                    <div style="background: rgba(99,102,241,0.1); border-radius: 16px; padding: 1rem; font-size: 0.8rem;">
                        <i class="fas fa-info-circle"></i> By completing this registration, you agree to our Terms of Service.
                    </div>
                </div>

                <div class="form-nav">
                    <button type="button" class="btn-prev" id="prevBtn" style="display: none;">← Back</button>
                    <button type="button" class="btn-next" id="nextBtn">Continue →</button>
                    <button type="submit" class="btn-submit" id="submitBtn" style="display: none;">✓ Confirm Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Ticket selection
document.querySelectorAll('.ticket-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.ticket-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('ticket_type').value = this.dataset.ticket;
        document.getElementById('summaryTicket').innerHTML = this.dataset.ticket;
    });
});

// Multi-step form
let currentStep = 1;
const totalSteps = 3;

function updateForm() {
    for (let i = 1; i <= totalSteps; i++) {
        const step = document.querySelector(`.step[data-step="${i}"]`);
        const formStep = document.getElementById(`step${i}`);
        
        if (i === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
            formStep.classList.add('active');
        } else if (i < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
            formStep.classList.remove('active');
        } else {
            step.classList.remove('active', 'completed');
            formStep.classList.remove('active');
        }
    }
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (currentStep === 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    } else if (currentStep === totalSteps) {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
    
    if (currentStep === totalSteps) {
        document.getElementById('summaryName').innerHTML = document.getElementById('full_name').value || 'Not provided';
        document.getElementById('summaryEmail').innerHTML = document.getElementById('email').value || 'Not provided';
    }
}

function validateStep() {
    if (currentStep === 1) return true;
    if (currentStep === 2) {
        const name = document.getElementById('full_name').value;
        const email = document.getElementById('email').value;
        if (!name.trim()) { alert('Please enter your full name'); return false; }
        if (!email.trim()) { alert('Please enter your email'); return false; }
        if (!email.includes('@')) { alert('Enter valid email'); return false; }
    }
    return true;
}

function nextStep() { if (validateStep() && currentStep < totalSteps) { currentStep++; updateForm(); } }
function prevStep() { if (currentStep > 1) { currentStep--; updateForm(); } }

document.getElementById('nextBtn').addEventListener('click', nextStep);
document.getElementById('prevBtn').addEventListener('click', prevStep);
updateForm();
</script>

</body>
</html>