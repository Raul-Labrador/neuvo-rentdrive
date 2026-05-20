document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.legal-nav-link');
    const sections = document.querySelectorAll('.legal-block');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        links.forEach(link => {
            link.classList.remove('active');
            if (current && link.getAttribute('data-target').includes(current)) {
                link.classList.add('active');
            }
        });
    });

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetSection = document.querySelector(targetId);

            if (targetSection && window.gsap) {
                gsap.to(window, {
                    scrollTo: { y: targetSection, offsetY: 120 },
                    duration: 1.2,
                    ease: 'expo.inOut'
                });
            } else if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 120,
                    behavior: 'smooth'
                });
            }
        });
    });
});