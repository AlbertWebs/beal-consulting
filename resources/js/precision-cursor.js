import '../css/precision-cursor.css';

const cursor = document.createElement('div');
cursor.className = 'precision-cursor';
cursor.dataset.precisionCursor = '';
cursor.setAttribute('aria-hidden', 'true');
cursor.innerHTML = `
    <span class="precision-cursor__field"></span>
    <span class="precision-cursor__guide precision-cursor__guide--horizontal"></span>
    <span class="precision-cursor__guide precision-cursor__guide--vertical"></span>
    <svg class="precision-cursor__svg" viewBox="0 0 52 52" focusable="false">
        <circle class="precision-cursor__ring" cx="26" cy="26" r="20" />
        <path class="precision-cursor__arc" d="M26 3.5a22.5 22.5 0 0 1 16.1 6.8M48.5 26a22.5 22.5 0 0 1-6.8 16.1M26 48.5a22.5 22.5 0 0 1-16.1-6.8M3.5 26a22.5 22.5 0 0 1 6.8-16.1" />
        <path class="precision-cursor__tick" d="M26 0v7M26 45v7M0 26h7M45 26h7" />
        <path class="precision-cursor__gold" d="M26 3.5a22.5 22.5 0 0 1 7.2 1.2" />
        <path class="precision-cursor__brackets" d="M15 20v-5h5m12 0h5v5M37 32v5h-5m-12 0h-5v-5" />
        <path class="precision-cursor__crosshair" d="M26 19v14M19 26h14" />
        <path class="precision-cursor__direction" d="M21 31l10-10m-8 0h8v8" />
    </svg>`;
document.body.append(cursor);

const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

if (cursor && finePointer.matches && !reducedMotion.matches) {
    let pointerX = window.innerWidth / 2;
    let pointerY = window.innerHeight / 2;
    let cursorX = pointerX;
    let cursorY = pointerY;
    let animationFrame = 0;
    let draftingTimeout;

    const renderCursor = () => {
        cursorX += (pointerX - cursorX) * 0.22;
        cursorY += (pointerY - cursorY) * 0.22;
        cursor.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0)`;

        if (Math.abs(pointerX - cursorX) > 0.1 || Math.abs(pointerY - cursorY) > 0.1) {
            animationFrame = window.requestAnimationFrame(renderCursor);
        } else {
            animationFrame = 0;
        }
    };

    const updateCursorMode = (target) => {
        const serviceCard = target.closest('.image-service-card');
        const button = target.closest('a.button, button, [role="button"]');
        const projectImage = target.closest('.image-intro-photo, .image-service-photo, .image-insights-feature, .image-experience-photo, .image-hero, .image-sectors, .service-detail-hero');
        const link = target.closest('a');

        cursor.dataset.mode = serviceCard ? 'service' : button ? 'button' : projectImage ? 'image' : link ? 'link' : 'default';

        const darkSection = target.closest('.topline, .image-hero, .image-sectors, .image-contact, .service-detail-hero');
        const photo = target.closest('.image-intro-photo, .image-service-photo, .image-insights-feature, .image-experience-photo');
        cursor.dataset.theme = darkSection ? 'dark' : photo ? 'photo' : 'light';
    };

    const moveCursor = (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;
        cursor.style.setProperty('--cursor-direction-x', `${Math.max(-1, Math.min(1, event.movementX / 12))}px`);
        cursor.style.setProperty('--cursor-direction-y', `${Math.max(-1, Math.min(1, event.movementY / 12))}px`);

        if (animationFrame === 0) {
            animationFrame = window.requestAnimationFrame(renderCursor);
        }

        cursor.classList.add('is-visible', 'is-drafting');
        window.clearTimeout(draftingTimeout);
        draftingTimeout = window.setTimeout(() => cursor.classList.remove('is-drafting'), 180);
    };

    document.addEventListener('pointermove', (event) => {
        if (event.pointerType !== 'mouse' || !finePointer.matches || reducedMotion.matches) {
            return;
        }

        moveCursor(event);
        updateCursorMode(event.target);
    }, { passive: true });

    document.addEventListener('pointerover', (event) => {
        if (event.pointerType === 'mouse' && event.target instanceof Element) {
            updateCursorMode(event.target);
        }
    }, { passive: true });

    document.addEventListener('scroll', () => {
        const elementUnderCursor = document.elementFromPoint(pointerX, pointerY);

        if (elementUnderCursor instanceof Element) {
            updateCursorMode(elementUnderCursor);
        }
    }, { passive: true, capture: true });

    document.addEventListener('pointerleave', () => cursor.classList.remove('is-visible'));
    document.addEventListener('pointerenter', () => cursor.classList.add('is-visible'));
    window.addEventListener('blur', () => cursor.classList.remove('is-visible'));

    finePointer.addEventListener('change', (event) => {
        if (!event.matches) {
            cursor.classList.remove('is-visible');
            window.cancelAnimationFrame(animationFrame);
        }
    });

    reducedMotion.addEventListener('change', (event) => {
        if (event.matches) {
            cursor.classList.remove('is-visible', 'is-drafting');
            window.cancelAnimationFrame(animationFrame);
        }
    });
}
