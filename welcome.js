document.addEventListener('DOMContentLoaded', () => {
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const progress = circle.dataset.progress;
        circle.style.setProperty('--progress', progress + '%');
    });
});
