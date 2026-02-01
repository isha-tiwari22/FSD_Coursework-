// assets/js/main.js
console.log('Harayo/Bhetiyo System Loaded');

// Theme Toggle Functionality
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
}

// Add any global UI interactions here
document.addEventListener('submit', function (e) {
    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn) {
        // Optional: Add loading state to buttons
        // submitBtn.disabled = true;
        // const originalText = submitBtn.innerHTML;
        // submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    }
});
