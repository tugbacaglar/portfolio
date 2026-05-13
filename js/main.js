// ===== DARK MODE =====
const darkToggle = document.getElementById('darkToggle');
const body = document.body;

if (localStorage.getItem('darkMode') === 'true') {
    body.classList.add('dark');
    darkToggle.textContent = '☀️';
}

darkToggle.addEventListener('click', () => {
    body.classList.toggle('dark');
    const isDark = body.classList.contains('dark');
    localStorage.setItem('darkMode', isDark);
    darkToggle.textContent = isDark ? '☀️' : '🌙';
});

// ===== IMAGE SLIDER =====
let currentSlide = 0;
const slides = document.querySelector('.slides');
const dots = document.querySelectorAll('.dot');
const totalSlides = document.querySelectorAll('.slide').length;

function goToSlide(n) {
    currentSlide = (n + totalSlides) % totalSlides;
    slides.style.transform = `translateX(-${currentSlide * 100}%)`;
    dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
}

document.querySelector('.slider-btn.prev').addEventListener('click', () => goToSlide(currentSlide - 1));
document.querySelector('.slider-btn.next').addEventListener('click', () => goToSlide(currentSlide + 1));
dots.forEach((dot, i) => dot.addEventListener('click', () => goToSlide(i)));
setInterval(() => goToSlide(currentSlide + 1), 4000);

// ===== LOAD PROJECTS VIA AJAX =====
function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

async function loadProjects() {
    const container = document.getElementById('projects-container');
    const loading = document.getElementById('projects-loading');

    try {
        const response = await fetch('php/get_projects.php');
        const projects = await response.json();
        loading.style.display = 'none';

        if (projects.error || !Array.isArray(projects)) {
            container.innerHTML = '<p style="color:#e74c3c;text-align:center">Projects could not be loaded.</p>';
            return;
        }

        container.innerHTML = projects.map(p => `
            <div class="project-card">
                <h3>${escapeHtml(p.title)}</h3>
                <p>${escapeHtml(p.description)}</p>
                <div class="tech-tags">
                    ${p.technologies ? p.technologies.split(',').map(t =>
            `<span class="tech-tag">${escapeHtml(t.trim())}</span>`
        ).join('') : ''}
                </div>
                <a href="${escapeHtml(p.github_url)}" target="_blank" class="project-link">View on GitHub →</a>
            </div>
        `).join('');
    } catch (err) {
        if (loading) loading.textContent = 'An error occurred while loading projects.';
        console.error(err);
    }
}

loadProjects();

// ===== CONTACT FORM VALIDATION & AJAX =====
const contactForm = document.getElementById('contactForm');

contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    let valid = true;

    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const message = document.getElementById('message');

    document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.form-group input, .form-group textarea')
        .forEach(el => el.style.borderColor = '#eee');

    if (name.value.trim().length < 2) {
        showError('name-error', 'Name must be at least 2 characters.');
        name.style.borderColor = '#e74c3c';
        valid = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
        showError('email-error', 'Enter a valid email address.');
        email.style.borderColor = '#e74c3c';
        valid = false;
    }

    if (message.value.trim().length < 10) {
        showError('message-error', 'Message must be at least 10 characters.');
        message.style.borderColor = '#e74c3c';
        valid = false;
    }

    if (!valid) return;

    const submitBtn = contactForm.querySelector('.form-submit');
    submitBtn.textContent = 'Sending...';
    submitBtn.disabled = true;

    try {
        const response = await fetch('php/save_contact.php', {
            method: 'POST',
            body: new FormData(contactForm)
        });
        const result = await response.json();
        const resultDiv = document.getElementById('form-result');
        resultDiv.style.display = 'block';
        resultDiv.className = result.success ? 'success' : 'error';
        resultDiv.textContent = result.message;
        if (result.success) contactForm.reset();
    } catch (err) {
        const resultDiv = document.getElementById('form-result');
        resultDiv.style.display = 'block';
        resultDiv.className = 'error';
        resultDiv.textContent = 'Something went wrong, please try again.';
    } finally {
        submitBtn.textContent = 'Send ✈️';
        submitBtn.disabled = false;
    }
});

function showError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = 'block'; }
}

// ===== SMOOTH NAV HIGHLIGHT =====
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-links a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        if (window.scrollY >= section.offsetTop - 80) current = section.getAttribute('id');
    });
    navLinks.forEach(link => {
        link.style.color = link.getAttribute('href') === `#${current}` ? '#ff6b35' : '';
    });
});