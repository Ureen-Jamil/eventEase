// Mobile Navigation Toggle
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

if (navToggle) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Image Slideshow
let slideIndex = 0;

function showSlides() {
    const slides = document.getElementsByClassName('slide');
    const dots = document.getElementsByClassName('dot');
    
    if (slides.length === 0) return;
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = 'none';
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1; }
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(' active', '');
    }
    if (slides[slideIndex - 1]) {
        slides[slideIndex - 1].style.display = 'block';
        if (dots[slideIndex - 1]) {
            dots[slideIndex - 1].className += ' active';
        }
    }
    setTimeout(showSlides, 5000);
}

if (document.getElementsByClassName('slide').length > 0) {
    showSlides();
}

function changeSlide(n) {
    const slides = document.getElementsByClassName('slide');
    slideIndex += n;
    if (slideIndex > slides.length) slideIndex = 1;
    if (slideIndex < 1) slideIndex = slides.length;
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = 'none';
    }
    slides[slideIndex - 1].style.display = 'block';
    
    const dots = document.getElementsByClassName('dot');
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(' active', '');
    }
    if (dots[slideIndex - 1]) {
        dots[slideIndex - 1].className += ' active';
    }
}

// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');
faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    if (question) {
        question.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    }
});

// AJAX Live Search
const searchInput = document.getElementById('liveSearch');
const categoryFilter = document.getElementById('categoryFilter');
const statusFilter = document.getElementById('statusFilter');

function performSearch() {
    const searchTerm = searchInput ? searchInput.value : '';
    const category = categoryFilter ? categoryFilter.value : '';
    const status = statusFilter ? statusFilter.value : 'upcoming';
    
    fetch(`/eventbase_project/api-handlers/event-search-api.php?search=${encodeURIComponent(searchTerm)}&category=${category}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('eventsContainer');
            if (container) {
                container.innerHTML = data.html;
            }
        })
        .catch(error => console.error('Error:', error));
}

let searchTimeout;
if (searchInput) {
    searchInput.addEventListener('keyup', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
}
if (categoryFilter) {
    categoryFilter.addEventListener('change', performSearch);
}
if (statusFilter) {
    statusFilter.addEventListener('change', performSearch);
}

// Multi-step form
let currentStep = 1;
const totalSteps = 3;

function updateSteps() {
    for (let i = 1; i <= totalSteps; i++) {
        const stepForm = document.getElementById(`step${i}`);
        const stepCircle = document.querySelector(`.step[data-step="${i}"] .step-circle`);
        
        if (stepForm) {
            if (i === currentStep) {
                stepForm.classList.add('active');
                if (stepCircle) stepCircle.classList.add('active');
            } else {
                stepForm.classList.remove('active');
            }
            if (i < currentStep) {
                if (stepCircle) stepCircle.classList.add('completed');
            }
        }
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        updateSteps();
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateSteps();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('eventModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
});
// Back to top button
window.addEventListener('scroll', function() {
    const btn = document.getElementById('backToTop');
    if (btn) {
        if (window.scrollY > 300) {
            btn.style.display = 'block';
        } else {
            btn.style.display = 'none';
        }
    }
});

// Prevent form resubmission on back button
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}