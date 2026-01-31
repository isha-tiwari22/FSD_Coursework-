// assets/js/main.js
console.log('Harayo/Bhetiyo System Loaded');

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
