<?php 
include 'includes/page-header.php'; 
include 'config/db-connection.php';
?>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1><i class="fas fa-calendar-alt"></i> Event Calendar</h1>
        <div style="display: flex; gap: 1rem;">
            <button onclick="changeMonth(-1)" class="btn btn-outline">◀ Prev</button>
            <button onclick="goToToday()" class="btn btn-outline">Today</button>
            <button onclick="changeMonth(1)" class="btn btn-outline">Next ▶</button>
        </div>
    </div>
    
    <h2 id="currentMonth" style="text-align: center; margin-bottom: 1rem;"></h2>
    
    <div class="weekdays" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
        <div class="glass" style="text-align: center; padding: 0.8rem;">Mon</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Tue</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Wed</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Thu</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Fri</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Sat</div>
        <div class="glass" style="text-align: center; padding: 0.8rem;">Sun</div>
    </div>
    
    <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem;"></div>
</div>

<div id="eventModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 2000; align-items: center; justify-content: center;">
    <div class="glass" style="max-width: 500px; width: 90%; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between;">
            <h3 id="modalDate"></h3>
            <button onclick="closeModal()" style="background: none; border: none; color: white; font-size: 1.5rem;">&times;</button>
        </div>
        <div id="modalEvents"></div>
    </div>
</div>

<script>
let currentMonth = new Date().getMonth() + 1;
let currentYear = new Date().getFullYear();

function loadCalendar() {
    fetch(`api-handlers/calendar-data-api.php?month=${currentMonth}&year=${currentYear}`)
        .then(response => response.json())
        .then(data => {
            renderCalendar(data);
            document.getElementById('currentMonth').innerHTML = `${data.month_name} ${data.year}`;
        });
}

function renderCalendar(data) {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';
    
    for (let i = 1; i < data.first_day_of_month; i++) {
        const empty = document.createElement('div');
        empty.className = 'glass';
        empty.style.padding = '0.8rem';
        empty.style.minHeight = '100px';
        empty.style.opacity = '0.3';
        grid.appendChild(empty);
    }
    
    for (let day = 1; day <= data.total_days; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'glass';
        dayCell.style.padding = '0.8rem';
        dayCell.style.minHeight = '100px';
        dayCell.style.cursor = 'pointer';
        
        const hasEvents = data.events[day] && data.events[day].length > 0;
        if (hasEvents) {
            dayCell.style.border = '2px solid #6366f1';
        }
        
        dayCell.innerHTML = `<strong style="font-size: 1.2rem;">${day}</strong>`;
        
        if (hasEvents) {
            dayCell.innerHTML += `<div style="margin-top: 0.5rem;"><small>${data.events[day].length} event(s)</small></div>`;
            dayCell.onclick = () => showEvents(day, data.month_name, data.events[day]);
        }
        
        grid.appendChild(dayCell);
    }
}

function showEvents(day, month, events) {
    document.getElementById('modalDate').innerHTML = `${month} ${day}, ${currentYear}`;
    let html = '';
    events.forEach(event => {
        html += `
            <div style="background: rgba(15,23,42,0.8); padding: 1rem; margin: 0.5rem 0; border-radius: 12px;">
                <h4>${event.title}</h4>
                <p><i class="fas fa-clock"></i> ${event.time}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${event.venue}</p>
                <a href="single-event.php?id=${event.id}" class="btn btn-primary" style="padding: 0.3rem 0.8rem;">View Details</a>
            </div>
        `;
    });
    document.getElementById('modalEvents').innerHTML = html;
    document.getElementById('eventModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    } else if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    loadCalendar();
}

function goToToday() {
    const today = new Date();
    currentMonth = today.getMonth() + 1;
    currentYear = today.getFullYear();
    loadCalendar();
}

loadCalendar();
</script>

<?php include 'includes/page-footer.php'; ?>