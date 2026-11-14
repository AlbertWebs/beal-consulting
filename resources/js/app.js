import './precision-cursor.js';

const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('[data-navigation]');

if (menuToggle && navigation) {
    const closeMenu = () => {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open navigation');
        navigation.classList.remove('is-open');
    };

    menuToggle.addEventListener('click', () => {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isExpanded));
        menuToggle.setAttribute('aria-label', isExpanded ? 'Open navigation' : 'Close navigation');
        navigation.classList.toggle('is-open', !isExpanded);
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
            menuToggle.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!navigation.contains(event.target) && !menuToggle.contains(event.target)) {
            closeMenu();
        }
    });
}

const navigationLinks = navigation ? Array.from(navigation.querySelectorAll('a[href^="#"]')) : [];
const contactCta = document.querySelector('[data-contact-cta]');
const navigationSections = navigationLinks
    .map((link) => document.querySelector(link.getAttribute('href')))
    .filter(Boolean);
const contactSection = document.querySelector('#contact');

if (navigationLinks.length > 0 && navigationSections.length > 0) {
    const activeLinks = [...navigationLinks, ...(contactCta ? [contactCta] : [])];
    const updateActiveNavigation = () => {
        const activationLine = (document.querySelector('[data-header]')?.getBoundingClientRect().height ?? 0) + 48;
        const currentSection = [...navigationSections, ...(contactSection ? [contactSection] : [])]
            .find((section) => {
                const bounds = section.getBoundingClientRect();
                return bounds.top <= activationLine && bounds.bottom > activationLine;
            }) ?? navigationSections[0];

        activeLinks.forEach((link) => {
            if (link.getAttribute('href') === `#${currentSection.id}`) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    let activeNavigationFrame = 0;
    const scheduleActiveNavigationUpdate = () => {
        if (activeNavigationFrame === 0) {
            activeNavigationFrame = window.requestAnimationFrame(() => {
                updateActiveNavigation();
                activeNavigationFrame = 0;
            });
        }
    };

    window.addEventListener('scroll', scheduleActiveNavigationUpdate, { passive: true });
    window.addEventListener('resize', scheduleActiveNavigationUpdate);
    window.addEventListener('hashchange', scheduleActiveNavigationUpdate);
    updateActiveNavigation();
}

const revealSelectors = [
    '.section-index',
    '.intro-layout',
    '.principle-strip',
    '.section-heading-row',
    '.service-item',
    '.sectors-topline',
    '.sectors-heading',
    '.sector-cell',
    '.insights-layout',
    '.insights-note',
    '.experience-copy',
    '.experience-visual',
    '.contact-layout',
    '.contact-bottom',
    '.service-scope-item',
    '.service-related-heading',
    '.service-related-card',
    '.service-detail-cta-row',
];

const revealElements = document.querySelectorAll(revealSelectors.join(','));
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (revealElements.length > 0 && !prefersReducedMotion && 'IntersectionObserver' in window) {
    document.documentElement.classList.add('has-scroll-reveal');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -7% 0px',
    });

    revealElements.forEach((element, index) => {
        element.classList.add('scroll-reveal');

        if (element.matches('.service-item')) {
            const itemIndex = Array.from(element.parentElement.children).indexOf(element);
            element.style.setProperty('--reveal-delay', `${itemIndex * 75}ms`);
        } else if (element.matches('.sector-cell')) {
            const itemIndex = Array.from(element.parentElement.children).indexOf(element);
            element.style.setProperty('--reveal-delay', `${itemIndex * 65}ms`);
        } else {
            element.style.setProperty('--reveal-delay', `${Math.min(index % 3, 2) * 55}ms`);
        }

        revealObserver.observe(element);
    });
}
