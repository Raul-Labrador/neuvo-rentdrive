// Registro de  GSAP plugins
gsap.registerPlugin(ScrollTrigger);

function initPreloader() {
    const preloader = document.getElementById('preloader');
    if (!preloader) {
        document.body.classList.remove('is-loading');
        initAllAnimations();
        return;
    }

    // Omitir el precargador si ya se ha mostrado en esta sesión.
    if (sessionStorage.getItem('neuvo_loaded')) {
        preloader.style.display = 'none';
        document.body.classList.remove('is-loading');
        initAllAnimations();
        return;
    }

    // Marcar sesión como cargada
    sessionStorage.setItem('neuvo_loaded', 'true');

    // Crea promesas para la animación de introducción y la carga de la ventana.
    const introPromise = new Promise(resolve => {
        gsap.timeline({ onComplete: resolve })
            .to('.preloader-logo', { opacity: 1, scale: 1, duration: 0.8, ease: 'back.out(1.7)' })
            .to('.preloader-bar-fill', { width: '60%', duration: 1.5, ease: 'power2.out' }, '-=0.3');
    });

    const loadPromise = new Promise(resolve => {
        if (document.readyState === 'complete') {
            resolve();
        } else {
            window.addEventListener('load', resolve);
        }
    });

    // Cuando ambos estén completos, finalice el precargador.
    Promise.all([introPromise, loadPromise]).then(() => {
        gsap.timeline()
            .to('.preloader-bar-fill', { width: '100%', duration: 0.6, ease: 'power2.inOut' })
            .call(() => {
                // Inicializa las animaciones y elimina el estado de carga mientras el precargador
                // aún cubre completamente la pantalla. Esto oculta cualquier recálculo de diseño
                // o "FOUC" al usuario.
                document.body.classList.remove('is-loading');
                initAllAnimations();
            })
            .to(preloader, {
                yPercent: -100,
                duration: 1.0, // Haz que el deslizamiento hacia arriba sea un poco más lento y elegante.
                ease: 'power3.inOut',
                delay: 0.2, // Le da tiempo al navegador para pintar los estados de animación iniciales.
                onComplete: () => {
                    preloader.style.display = 'none';
                }
            });
    });
}

// Scroll Progress Bar
function initScrollProgress() {
    const progressBar = document.getElementById('scroll-progress');
    if (!progressBar) return;

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrollTop / docHeight) * 100;
        progressBar.style.width = progress + '%';
    });
}

// Botón Back to Top
function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    // Mostrar/ocultar al desplazarse
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            gsap.to(btn, { opacity: 1, scale: 1, pointerEvents: 'all', duration: 0.3, ease: 'back.out(1.7)' });
        } else {
            gsap.to(btn, { opacity: 0, scale: 0.5, pointerEvents: 'none', duration: 0.3, ease: 'power2.in' });
        }
    });

    // Controlador de clics con desplazamiento suave

    btn.addEventListener('click', () => {
        gsap.to(window, {
            scrollTo: { y: 0 },
            duration: 0.1,
            ease: 'power1.out',
            overwrite: true
        });
    });

    // Efecto de onda al pasar el cursor
    btn.addEventListener('mouseenter', () => {
        gsap.to(btn, { boxShadow: '0 0 0 8px rgba(10, 10, 10, 0.15)', duration: 0.3 });
    });
    btn.addEventListener('mouseleave', () => {
        gsap.to(btn, { boxShadow: '0 4px 20px rgba(0, 0, 0, 0.15)', duration: 0.3 });
    });
}

// Efecto de navegación del Navbar desactivado
function initNavbarScroll() {
    return;
}

// Animaciones del Hero Section
function initHeroAnimations() {
    const heroCard = document.querySelector('.hero-dark-card');
    const heroStrip = document.querySelector('.hero-info-strip');

    if (!heroCard) return;

    // Entrada de la tarjeta de Hero, usa clipPath para que la navegación interior no se vea afectada.
    gsap.from('.hero-dark-card', {
        clipPath: 'inset(5% 2% 5% 2% round 30px)',
        opacity: 0.3,
        duration: 2.0,
        ease: 'power3.out'
    });

    // Animación de la tira de información del Hero
    if (heroStrip) {
        gsap.from('.hero-redesign-tagline', {
            scrollTrigger: {
                trigger: '.hero-info-strip',
                start: 'top 85%',
                toggleActions: 'play none none reverse'
            },
            x: -60,
            opacity: 0,
            duration: 1.5,
            ease: 'power3.out'
        });

        gsap.from('.hero-redesign-desc', {
            scrollTrigger: {
                trigger: '.hero-info-strip',
                start: 'top 85%',
                toggleActions: 'play none none reverse'
            },
            y: 30,
            opacity: 0,
            duration: 1.5,
            delay: 0.4,
            ease: 'power3.out'
        });

        // Animar números de estadísticas
        gsap.from('.hero-stat-number-small', {
            scrollTrigger: {
                trigger: '.hero-info-strip',
                start: 'top 85%',
                toggleActions: 'play none none reverse'
            },
            y: 20,
            opacity: 0,
            duration: 1.0,
            stagger: 0.3,
            ease: 'power3.out'
        });
    }

    // Efecto parallax en el fondo del hero
    gsap.to('.hero-dark-card', {
        scrollTrigger: {
            trigger: '.hero-redesign-section',
            start: 'top top',
            end: 'bottom top',
            scrub: 1
        },
        backgroundPositionY: '30%',
        ease: 'none'
    });
}

// Contador animado
function initAnimatedCounters() {
    const counters = document.querySelectorAll('.hero-stat-number-small');

    counters.forEach(counter => {
        const text = counter.textContent;
        const number = parseInt(text);
        const suffix = text.replace(/[0-9]/g, '');

        if (isNaN(number)) return;

        ScrollTrigger.create({
            trigger: counter,
            start: 'top 90%',
            once: true,
            onEnter: () => {
                gsap.from(counter, {
                    textContent: 0,
                    duration: 3,
                    ease: 'power2.out',
                    snap: { textContent: 1 },
                    onUpdate: function () {
                        counter.textContent = Math.round(gsap.getProperty(counter, 'textContent')) + suffix;
                    },
                    onComplete: () => {
                        counter.textContent = text;
                    }
                });
            }
        });
    });
}

// Animaciones de Section Title
function initSectionTitles() {
    const titles = document.querySelectorAll('.section-title');

    titles.forEach(title => {
        gsap.from(title, {
            scrollTrigger: {
                trigger: title,
                start: 'top 85%',
                toggleActions: 'play none none reverse'
            },
            y: 50,
            opacity: 0,
            duration: 1.2,
            ease: 'power3.out'
        });
    });
}

// Animaciones de las cards de los coches
function initVehicleCards() {
    const cards = document.querySelectorAll('.vehicle-card');

    cards.forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 88%',
                toggleActions: 'play none none reverse'
            },
            y: 60,
            opacity: 0,
            duration: 1.2,
            delay: i * 0.2,
            ease: 'power3.out'
        });
    });
}

// Animaciones de las marcas de coches
function initBrandLogos() {
    const logos = document.querySelectorAll('.brand-logo-placeholder');

    logos.forEach((logo, i) => {
        gsap.from(logo, {
            scrollTrigger: {
                trigger: '.vehicle-brands',
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            y: 30,
            opacity: 0,
            scale: 0.8,
            duration: 1.0,
            delay: i * 0.15,
            ease: 'back.out(1.7)'
        });

        logo.addEventListener('mouseenter', () => {
            gsap.to(logo, { y: -5, scale: 1.1, duration: 0.3, ease: 'power2.out' });
        });
        logo.addEventListener('mouseleave', () => {
            gsap.to(logo, { y: 0, scale: 1, duration: 0.3, ease: 'power2.out' });
        });
    });
}

// Animación de Cards de confianza
function initTrustCards() {
    const cards = document.querySelectorAll('.trust-card');

    cards.forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            y: 50,
            opacity: 0,
            duration: 1.0,
            delay: (i % 3) * 0.2,
            ease: 'power3.out'
        });

        const icon = card.querySelector('.trust-icon');
        if (icon) {
            card.addEventListener('mouseenter', () => {
                gsap.to(icon, { scale: 1.15, rotation: 5, duration: 0.3, ease: 'back.out(1.7)' });
            });
            card.addEventListener('mouseleave', () => {
                gsap.to(icon, { scale: 1, rotation: 0, duration: 0.3, ease: 'power2.out' });
            });
        }
    });

    const subtitle = document.querySelector('.trust-subtitle');
    if (subtitle) {
        gsap.from(subtitle, {
            scrollTrigger: {
                trigger: subtitle,
                start: 'top 88%',
                toggleActions: 'play none none reverse'
            },
            y: 30,
            opacity: 0,
            duration: 0.7,
            ease: 'power3.out'
        });
    }
}

// Animaciones de la sección de alquileres
function initRentalHits() {
    // Featured section
    const featuredImage = document.querySelector('.rental-featured-image');
    const featuredInfo = document.querySelector('.rental-featured-info');

    if (featuredImage) {
        gsap.from(featuredImage, {
            scrollTrigger: {
                trigger: '.rental-featured',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            x: -80,
            opacity: 0,
            duration: 1.5,
            ease: 'power3.out'
        });
    }

    if (featuredInfo) {
        gsap.from(featuredInfo, {
            scrollTrigger: {
                trigger: '.rental-featured',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            x: 80,
            opacity: 0,
            duration: 1.5,
            delay: 0.4,
            ease: 'power3.out'
        });
    }

    const premiumCards = document.querySelectorAll('.premium-car-card');
    premiumCards.forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            y: 70,
            opacity: 0,
            scale: 0.95,
            duration: 1.2,
            delay: i * 0.2,
            ease: 'power3.out'
        });

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 25;
            const rotateY = (centerX - x) / 25;

            gsap.to(card, {
                rotateX: rotateX,
                rotateY: rotateY,
                transformPerspective: 800,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.5,
                ease: 'elastic.out(1, 0.5)'
            });
        });
    });
}

// Animaciones de la sección de Testimonials
function initTestimonials() {
    const cards = document.querySelectorAll('.testimonial-card');

    cards.forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            y: 60,
            opacity: 0,
            duration: 1.2,
            delay: i * 0.2,
            ease: 'power3.out'
        });

        const stars = card.querySelectorAll('.star-placeholder');
        ScrollTrigger.create({
            trigger: card,
            start: 'top 88%',
            once: true,
            onEnter: () => {
                gsap.from(stars, {
                    scale: 0,
                    opacity: 0,
                    duration: 0.3,
                    stagger: 0.08,
                    ease: 'back.out(2)',
                    delay: 0.4 + i * 0.1
                });
            }
        });
    });
}

// Animaciones del footer
function initFooterAnimations() {
    const footer = document.querySelector('.footer');
    if (!footer) return;

    gsap.from('.footer-brand', {
        scrollTrigger: {
            trigger: footer,
            start: 'top 90%',
            toggleActions: 'play none none reverse'
        },
        y: 30,
        opacity: 0,
        duration: 0.6,
        ease: 'power3.out'
    });

    gsap.from('.footer-title', {
        scrollTrigger: {
            trigger: footer,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
        },
        y: 25,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        ease: 'power3.out'
    });

    gsap.from('.footer-links li', {
        scrollTrigger: {
            trigger: footer,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
        },
        x: -20,
        opacity: 0,
        duration: 0.4,
        stagger: 0.05,
        ease: 'power3.out'
    });

    const socialIcons = document.querySelectorAll('.social-icon');
    socialIcons.forEach((icon, i) => {
        gsap.from(icon, {
            scrollTrigger: {
                trigger: '.footer-bottom',
                start: 'top 95%',
                toggleActions: 'play none none reverse'
            },
            y: 20,
            opacity: 0,
            scale: 0.5,
            duration: 0.4,
            delay: i * 0.1,
            ease: 'back.out(1.7)'
        });

        // Bounce on hover
        icon.addEventListener('mouseenter', () => {
            gsap.to(icon, { y: -4, scale: 1.15, duration: 0.25, ease: 'back.out(2)' });
        });
        icon.addEventListener('mouseleave', () => {
            gsap.to(icon, { y: 0, scale: 1, duration: 0.25, ease: 'power2.out' });
        });
    });
}

// Desplazamiento suave para enlaces
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                gsap.to(window, {
                    scrollTo: { y: target, offsetY: 80 },
                    duration: 1,
                    ease: 'power3.inOut'
                });
            }
        });
    });
}

// Microinteracciones al pasar el cursor sobre los botones
function initButtonEffects() {
    const buttons = document.querySelectorAll('.btn-cta, .btn-book, .btn-primary-neuvo, .btn-outline-neuvo, .btn-rent-floating');

    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            gsap.to(btn, { scale: 1.05, duration: 0.25, ease: 'power2.out' });
        });
        btn.addEventListener('mouseleave', () => {
            gsap.to(btn, { scale: 1, duration: 0.25, ease: 'power2.out' });
        });
    });
}

// Efecto de cursor para tarjetas premium
function initCardGlow() {
    const cards = document.querySelectorAll('.premium-car-card');

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            card.style.setProperty('--glow-x', x + 'px');
            card.style.setProperty('--glow-y', y + 'px');
        });
    });
}

// Animaciones del encabezado de página
function initPageHeader() {
    const pageHeader = document.querySelector('.page-header');
    if (!pageHeader) return;

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.from('.page-header-title', {
        y: 40,
        opacity: 0,
        duration: 0.8,
    })
        .from('.page-header-breadcrumb', {
            y: 20,
            opacity: 0,
            duration: 0.5,
        }, '-=0.3');
}

// Animaciones de las tarjetas de Blogs
function initBlogCards() {
    const featured = document.querySelector('.blog-featured');
    if (featured) {
        gsap.from(featured, {
            scrollTrigger: {
                trigger: featured,
                start: 'top 85%',
                toggleActions: 'play none none reverse'
            },
            y: 60,
            opacity: 0,
            duration: 0.8,
            ease: 'power3.out'
        });
    }

    const blogCards = document.querySelectorAll('.blog-card:not(.blog-featured)');
    blogCards.forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            y: 50,
            opacity: 0,
            duration: 0.6,
            delay: (i % 3) * 0.12,
            ease: 'power3.out'
        });
    });
}

// Animaciones del contact
function initContactAnimations() {
    const contactInfo = document.querySelector('.contact-info-card');
    const contactForm = document.querySelector('.contact-form-card');

    if (contactInfo) {
        gsap.from(contactInfo, {
            scrollTrigger: {
                trigger: '.contact-section',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            x: -60,
            opacity: 0,
            duration: 0.8,
            ease: 'power3.out'
        });

        const items = contactInfo.querySelectorAll('.contact-info-item');
        items.forEach((item, i) => {
            gsap.from(item, {
                scrollTrigger: {
                    trigger: '.contact-section',
                    start: 'top 75%',
                    toggleActions: 'play none none reverse'
                },
                x: -30,
                opacity: 0,
                duration: 0.5,
                delay: 0.2 + i * 0.1,
                ease: 'power3.out'
            });
        });
    }

    if (contactForm) {
        gsap.from(contactForm, {
            scrollTrigger: {
                trigger: '.contact-section',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            x: 60,
            opacity: 0,
            duration: 0.8,
            delay: 0.2,
            ease: 'power3.out'
        });
    }

    const map = document.querySelector('.map-placeholder');
    if (map) {
        gsap.from(map, {
            scrollTrigger: {
                trigger: map,
                start: 'top 90%',
                toggleActions: 'play none none reverse'
            },
            opacity: 0,
            scale: 0.98,
            duration: 0.8,
            ease: 'power3.out'
        });
    }
}

// Animaciones FAQ
function initFAQAnimations() {
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach((item, i) => {
        gsap.from(item, {
            scrollTrigger: {
                trigger: item,
                start: 'top 92%',
                toggleActions: 'play none none reverse'
            },
            y: 30,
            opacity: 0,
            duration: 0.5,
            delay: i * 0.08,
            ease: 'power3.out'
        });
    });
}

// Inicializamos todas las animaciones
function initAllAnimations() {
    initScrollProgress();
    initBackToTop();
    initHeroAnimations();

    initAnimatedCounters();
    initSectionTitles();
    initVehicleCards();
    initBrandLogos();
    initTrustCards();
    initRentalHits();
    initTestimonials();
    initFooterAnimations();
    initSmoothScroll();
    initButtonEffects();
    initCardGlow();
    initPageHeader();
    initBlogCards();
    initContactAnimations();
    initFAQAnimations();
}

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('is-loading');
    initPreloader();
});
