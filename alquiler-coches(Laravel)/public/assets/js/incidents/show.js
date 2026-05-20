document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});

function openImageOverlay(src) {
    document.getElementById('imgOverlaySrc').src = src;
    document.getElementById('imgOverlay').classList.add('show');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('imgOverlay').classList.remove('show');
});

setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
        el.style.opacity = '0'; el.style.transition = 'opacity 0.4s';
        setTimeout(() => el.remove(), 400);
    });
}, 5000);