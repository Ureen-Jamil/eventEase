</main>

<button id="backToTop" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</button>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3><i class="fas fa-calendar-check"></i> EventEase</h3>
            <p>Smart Event Management System with QR Code Ticketing.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
        
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="all-events.php">Browse Events</a></li>
                <li><a href="calendar-view.php">Event Calendar</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Contact Info</h4>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> PAF-IAST, Haripur</li>
                <li><i class="fas fa-envelope"></i> info@eventease.com</li>
                <li><i class="fas fa-phone"></i> +92 123 4567890</li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2024 EventEase | Green Jamil, Umme Aiman, Muhammad Daud Latif | SE Blue</p>
    </div>
</footer>

<script src="assets/js/eventease-scripts.js"></script>
<script>
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('scroll', function() {
    const btn = document.getElementById('backToTop');
    if (window.scrollY > 300) {
        btn.style.display = 'block';
    } else {
        btn.style.display = 'none';
    }
});
</script>
</body>
</html>