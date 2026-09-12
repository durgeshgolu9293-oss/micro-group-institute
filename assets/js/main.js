// Micro Group of Computer Institute - Main JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Counter Animation for Stats
    const counters = document.querySelectorAll('.counter-anim');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        let count = 0;
        const speed = target > 100 ? 30 : 60;
        const inc = Math.max(1, Math.ceil(target / speed));
        
        const updateCount = () => {
            count += inc;
            if (count < target) {
                counter.innerText = count;
                setTimeout(updateCount, 25);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch(e) {}
        }, 5000);
    });
});