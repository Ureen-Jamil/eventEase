<?php 
include 'includes/page-header.php'; 
include 'config/db-connection.php';
?>

<style>
.search-hero {
    background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(236,72,153,0.15));
    border-radius: 32px;
    padding: 3rem 2rem;
    margin-bottom: 2rem;
    text-align: center;
}

.search-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.search-hero p {
    color: #64748b;
    margin-bottom: 2rem;
}

.search-container {
    max-width: 800px;
    margin: 0 auto;
}

.search-input-wrapper {
    display: flex;
    gap: 1rem;
    background: rgba(15, 23, 42, 0.8);
    border-radius: 60px;
    padding: 0.5rem;
    border: 1px solid rgba(255,255,255,0.2);
}

.search-input-wrapper input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 1rem 1.5rem;
    color: white;
    font-size: 1rem;
}

.search-input-wrapper input:focus {
    outline: none;
}

.search-input-wrapper button {
    background: #6366f1;
    border: none;
    padding: 0 2rem;
    border-radius: 50px;
    color: white;
    cursor: pointer;
}

.filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    justify-content: center;
    margin-top: 1.5rem;
}

.filter-chip {
    background: rgba(99,102,241,0.15);
    border: 1px solid rgba(99,102,241,0.3);
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    cursor: pointer;
    font-size: 0.9rem;
    color: white;
}

.filter-chip.active {
    background: #6366f1;
}

.filter-chip i {
    margin-right: 0.5rem;
}

.results-count {
    text-align: center;
    margin: 1.5rem 0;
    color: #64748b;
}
</style>

<section class="search-hero">
    <div class="search-container">
        <h1><i class="fas fa-search"></i> Find Your Perfect Event</h1>
        <p>Discover conferences, workshops, seminars and more</p>
        
        <div class="search-input-wrapper">
            <input type="text" id="liveSearch" placeholder="Search by title, venue or description...">
            <button onclick="loadEvents()"><i class="fas fa-search"></i> Search</button>
        </div>
        
        <div class="filter-chips" id="categoryChips">
            <div class="filter-chip active" data-cat="0">
                <i class="fas fa-globe"></i> All
            </div>
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            while($cat = $cats->fetch_assoc()):
            ?>
            <div class="filter-chip" data-cat="<?php echo $cat['id']; ?>">
                <i class="<?php echo $cat['icon']; ?>"></i> <?php echo $cat['name']; ?>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="filter-chips" id="statusChips">
            <div class="filter-chip active" data-status="upcoming">
                <i class="fas fa-calendar-day"></i> Upcoming
            </div>
            <div class="filter-chip" data-status="ongoing">
                <i class="fas fa-play-circle"></i> Ongoing
            </div>
            <div class="filter-chip" data-status="completed">
                <i class="fas fa-check-circle"></i> Completed
            </div>
        </div>
    </div>
</section>

<div class="results-count" id="resultsCount">Loading events...</div>
<div class="events-grid" id="eventsContainer"></div>

<script>
let selectedCategory = 0;
let selectedStatus = 'upcoming';

// Category filter clicks
document.querySelectorAll('#categoryChips .filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('#categoryChips .filter-chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        selectedCategory = parseInt(this.dataset.cat);
        loadEvents();
    });
});

// Status filter clicks
document.querySelectorAll('#statusChips .filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('#statusChips .filter-chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        selectedStatus = this.dataset.status;
        loadEvents();
    });
});

function loadEvents() {
    const search = document.getElementById('liveSearch')?.value || '';
    
    fetch(`api-handlers/event-search-api.php?search=${encodeURIComponent(search)}&category=${selectedCategory}&status=${selectedStatus}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('eventsContainer').innerHTML = data.html;
            document.getElementById('resultsCount').innerHTML = `<i class="fas fa-calendar-alt"></i> ${data.count} events found`;
        })
        .catch(error => {
            document.getElementById('eventsContainer').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Error loading events</h3><p>Please check your database connection</p></div>';
            document.getElementById('resultsCount').innerHTML = 'Error loading events';
        });
}

let searchTimeout;
const searchInput = document.getElementById('liveSearch');
if (searchInput) {
    searchInput.addEventListener('keyup', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadEvents, 500);
    });
}

loadEvents();
</script>

<?php include 'includes/page-footer.php'; ?>