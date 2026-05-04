<?php
include 'config/db-connection.php';
include 'includes/session-auth.php';
requireLogin();

$just_registered = isset($_GET['success']) ? true : false;
$registration_id = isset($_GET['reg_id']) ? (int)$_GET['reg_id'] : 0;

$reg = $conn->query("SELECT r.*, e.title, e.event_date, e.event_time, e.venue, u.name as user_name, u.email 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = $registration_id AND r.user_id = {$_SESSION['user_id']}")->fetch_assoc();

if (!$reg) {
    header('Location: user-panel/my-registrations.php');
    exit();
}

$qrData = "Ticket ID: " . $reg['id'] . " | Event: " . $reg['title'] . " | Attendee: " . $reg['user_name'];
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket - <?php echo $reg['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/eventease-styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .btn, button, .btn-outline, .btn-primary {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div id="ticket" style="max-width: 450px; width: 100%;">
    <?php if($just_registered): ?>
    <div style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; padding: 1rem; margin-bottom: 1rem; text-align: center; animation: slideDown 0.5s ease;">
        <i class="fas fa-check-circle"></i> ✅ Registration Successful! Your ticket has been generated.
    </div>
    <?php endif; ?>  
    
    <div class="glass" style="text-align: center; padding: 2rem;">
        <i class="fas fa-calendar-check" style="font-size: 3rem; color: #6366f1;"></i>
        <h2>EventEase Ticket</h2>
        <h3><?php echo htmlspecialchars($reg['title']); ?></h3>
        
        <div style="margin: 1rem 0; text-align: left;">
            <p><strong><i class="fas fa-user"></i> Attendee:</strong> <?php echo htmlspecialchars($reg['user_name']); ?></p>
            <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($reg['email']); ?></p>
            <p><strong><i class="fas fa-ticket-alt"></i> Ticket Type:</strong> <span style="background: #6366f1; padding: 0.2rem 0.5rem; border-radius: 20px;"><?php echo $reg['ticket_type']; ?></span></p>
            <p><strong><i class="fas fa-hashtag"></i> Ticket ID:</strong> #<?php echo str_pad($reg['id'], 6, '0', STR_PAD_LEFT); ?></p>
            <p><strong><i class="fas fa-calendar"></i> Date:</strong> <?php echo date('F d, Y', strtotime($reg['event_date'])); ?> at <?php echo date('h:i A', strtotime($reg['event_time'])); ?></p>
            <p><strong><i class="fas fa-map-marker-alt"></i> Venue:</strong> <?php echo htmlspecialchars($reg['venue']); ?></p>
        </div>
        
        <div style="margin: 1rem 0;">
            <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 180px; border-radius: 12px;">
            <p style="font-size: 0.7rem; color: #64748b; margin-top: 0.5rem;">Scan this QR code at the event entrance</p>
        </div>
        
        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
            <button onclick="downloadPDF()" class="btn btn-primary"><i class="fas fa-download"></i> Download PDF</button>
            <button onclick="window.print()" class="btn btn-outline"><i class="fas fa-print"></i> Print</button>
            <a href="user-panel/my-qrcodes.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> My Tickets</a>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('ticket');
    const originalDisplay = element.style.display;
    
    // Temporarily hide buttons for PDF
    const buttons = element.querySelectorAll('.btn, button');
    buttons.forEach(btn => btn.style.display = 'none');
    
    html2canvas(element, {
        scale: 2,
        backgroundColor: '#0f172a'
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        const imgWidth = 190;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let yPosition = (297 - imgHeight) / 2;
        if (yPosition < 10) yPosition = 10;
        
        pdf.addImage(imgData, 'PNG', 10, yPosition, imgWidth, imgHeight);
        pdf.save('event-ticket-<?php echo $reg['id']; ?>.pdf');
        
        // Restore buttons
        buttons.forEach(btn => btn.style.display = '');
    }).catch(error => {
        console.error('PDF generation error:', error);
        alert('Error generating PDF. Please try again.');
        buttons.forEach(btn => btn.style.display = '');
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        downloadPDF();
    }
});
</script>

</body>
</html>