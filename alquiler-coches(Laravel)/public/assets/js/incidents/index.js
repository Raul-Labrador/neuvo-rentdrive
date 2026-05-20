document.getElementById('sidebarToggle').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});

setTimeout(() => {
  document.querySelectorAll('.flash-msg').forEach(el => {
    el.style.opacity = '0'; el.style.transition = 'opacity 0.4s';
    setTimeout(() => el.remove(), 400);
  });
}, 5000);