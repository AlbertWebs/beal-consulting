import './precision-cursor.js';
import './enquiry-dialog.js';
import './scorecard-assessment.js';
import './scorecard-showcase.js';
import './partnership-enquiry.js';
const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('[data-navigation]');

document.querySelectorAll('[data-copy-url]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(new URL(button.dataset.copyUrl, window.location.origin).href);
            button.textContent = 'Copied';
            window.setTimeout(() => { button.textContent = 'Copy URL'; }, 1800);
        } catch {
            button.textContent = 'Copy unavailable';
        }
    });
});

const initializeRichTextEditors = async () => {
    const [core, starterKit, textAlign, image, table, tableRow, tableHeader, tableCell] = await Promise.all([
        import('@tiptap/core'), import('@tiptap/starter-kit'), import('@tiptap/extension-text-align'),
        import('@tiptap/extension-image'), import('@tiptap/extension-table'), import('@tiptap/extension-table-row'),
        import('@tiptap/extension-table-header'), import('@tiptap/extension-table-cell'),
    ]);
    const { Editor } = core;
    const StarterKit = starterKit.default;
    const TextAlign = textAlign.default;
    const Image = image.default;
    const { Table } = table;
    const TableRow = tableRow.default;
    const TableHeader = tableHeader.default;
    const TableCell = tableCell.default;

    document.querySelectorAll('[data-rich-editor]').forEach((element) => {
    const input = document.querySelector(element.dataset.input);
    const editor = new Editor({
        element,
        extensions: [StarterKit.configure({ link: { openOnClick: false } }), TextAlign.configure({ types: ['heading', 'paragraph'] }), Image, Table.configure({ resizable: false }), TableRow, TableHeader, TableCell],
        content: input?.value || '',
        onUpdate: ({ editor: updatedEditor }) => {
            if (input) {
                input.value = updatedEditor.getHTML();
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },
    });

    element.closest('[data-rich-field]')?.querySelectorAll('[data-editor-command]').forEach((button) => {
        button.addEventListener('click', () => {
            const command = button.dataset.editorCommand;
            const chain = editor.chain().focus();

            if (command === 'bold') chain.toggleBold().run();
            if (command === 'italic') chain.toggleItalic().run();
            if (command === 'underline') chain.toggleUnderline().run();
            if (command === 'strike') chain.toggleStrike().run();
            if (command === 'bulletList') chain.toggleBulletList().run();
            if (command === 'orderedList') chain.toggleOrderedList().run();
            if (command === 'blockquote') chain.toggleBlockquote().run();
            if (command === 'undo') chain.undo().run();
            if (command === 'redo') chain.redo().run();
            if (command === 'clear') chain.clearNodes().unsetAllMarks().run();
            if (command === 'paragraph') chain.setParagraph().run();
            if (command.startsWith('heading')) chain.toggleHeading({ level: Number(command.slice(7)) }).run();
            if (command === 'alignLeft') chain.setTextAlign('left').run();
            if (command === 'alignCenter') chain.setTextAlign('center').run();
            if (command === 'alignRight') chain.setTextAlign('right').run();
            if (command === 'link') {
                const url = window.prompt('Link URL');
                if (url) chain.extendMarkRange('link').setLink({ href: url }).run();
            }
            if (command === 'image') {
                const src = window.prompt('Image URL (choose a URL from the media library)');
                if (src) chain.setImage({ src, alt: window.prompt('Alternative text') || '' }).run();
            }
            if (command === 'table') chain.insertTable({ rows: 2, cols: 3, withHeaderRow: true }).run();
        });
    });
    });
};

if (document.querySelector('[data-rich-editor]')) {
    initializeRichTextEditors();
}

document.querySelectorAll('[data-autosave-form]').forEach((form) => {
    let timer;
    let pending = false;
    const status = form.querySelector('[data-save-status]');
    const save = async () => {
        status.textContent = 'Saving…';
        pending = true;
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'X-CMS-Autosave': 'true', 'Accept': 'application/json' },
                body: new FormData(form),
            });
            if (!response.ok) throw new Error('save failed');
            status.textContent = `Saved ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
            pending = false;
        } catch {
            status.textContent = 'Autosave failed. Use Save section to retry.';
            pending = false;
        }
    };

    form.addEventListener('input', () => {
        status.textContent = 'Unsaved changes';
        window.clearTimeout(timer);
        timer = window.setTimeout(save, 1800);
    });
    form.addEventListener('submit', () => window.clearTimeout(timer));
    window.addEventListener('beforeunload', (event) => {
        if (pending || status.textContent === 'Unsaved changes') {
            event.preventDefault();
            event.returnValue = '';
        }
    });
});

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
    '.secondary-about-hero .secondary-hero-inner',
    '.secondary-about-overview > .secondary-index',
    '.secondary-about-overview .secondary-intro-grid > *',
    '.secondary-about-approach > .secondary-index',
    '.secondary-approach-heading > *',
    '.secondary-approach-grid article',
    '.secondary-approach-objective',
    '.secondary-about-values > .secondary-index',
    '.secondary-about-values > h2',
    '.secondary-about-values .secondary-values-grid article',
    '.secondary-about-values .secondary-commitment',
    '.secondary-about-values .secondary-principles',
    '.secondary-leadership > .secondary-index',
    '.secondary-leadership > .secondary-intro-grid > *',
    '.secondary-team-card',
    '.secondary-team-network',
    '.secondary-experience-section > .secondary-index',
    '.secondary-experience-heading',
    '.secondary-experience-areas',
    '.secondary-project-exposure',
    '.secondary-experience-areas li',
    '.secondary-project-exposure li',
    '.secondary-about-cta .secondary-index',
    '.secondary-about-cta .secondary-cta-layout > *',
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

const projectGallery = document.querySelector('.secondary-project-gallery');

if (projectGallery) {
    const imageFrames = Array.from(projectGallery.querySelectorAll('.project-gallery-image-frame'));
    const zoomButtons = imageFrames.flatMap((frame) => {
        const image = frame.querySelector('img');

        if (!image) {
            return [];
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'project-gallery-image-zoom';
        button.setAttribute('aria-label', `View larger image: ${image.alt || 'Project gallery image'}`);
        button.dataset.galleryImage = image.currentSrc || image.src;
        button.dataset.galleryAlt = image.alt;
        frame.replaceChild(button, image);
        button.append(image);

        return [button];
    });

    if (zoomButtons.length > 0) {
        const lightbox = document.createElement('dialog');
        lightbox.className = 'project-gallery-lightbox';
        lightbox.setAttribute('aria-label', 'Project gallery image viewer');

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'project-gallery-lightbox-close';
        closeButton.setAttribute('aria-label', 'Close image viewer');
        closeButton.textContent = '×';

        const previousButton = document.createElement('button');
        previousButton.type = 'button';
        previousButton.className = 'project-gallery-lightbox-previous';
        previousButton.setAttribute('aria-label', 'View previous image');
        previousButton.textContent = '←';

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'project-gallery-lightbox-next';
        nextButton.setAttribute('aria-label', 'View next image');
        nextButton.textContent = '→';

        const figure = document.createElement('figure');
        const lightboxImage = document.createElement('img');
        const caption = document.createElement('figcaption');
        figure.append(lightboxImage, caption);
        lightbox.append(closeButton, previousButton, figure, nextButton);
        document.body.append(lightbox);

        let activeImageIndex = 0;

        const showImage = (index) => {
            activeImageIndex = (index + zoomButtons.length) % zoomButtons.length;
            const activeButton = zoomButtons[activeImageIndex];
            lightboxImage.src = activeButton.dataset.galleryImage;
            lightboxImage.alt = activeButton.dataset.galleryAlt || '';
            caption.textContent = activeButton.dataset.galleryAlt || `Image ${activeImageIndex + 1} of ${zoomButtons.length}`;
        };

        zoomButtons.forEach((button, index) => {
            button.addEventListener('click', () => {
                showImage(index);
                lightbox.showModal();
                closeButton.focus();
            });
        });

        closeButton.addEventListener('click', () => lightbox.close());
        previousButton.addEventListener('click', () => showImage(activeImageIndex - 1));
        nextButton.addEventListener('click', () => showImage(activeImageIndex + 1));
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                lightbox.close();
            }
        });
        lightbox.addEventListener('close', () => zoomButtons[activeImageIndex]?.focus());
        lightbox.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                showImage(activeImageIndex - 1);
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                showImage(activeImageIndex + 1);
            }
        });

        if (zoomButtons.length < 2) {
            previousButton.hidden = true;
            nextButton.hidden = true;
        }
    }
}

if (projectGallery && !prefersReducedMotion && 'IntersectionObserver' in window) {
    const stageLinks = Array.from(projectGallery.querySelectorAll('.project-gallery-path a[href^="#project-stage-"]'));
    const stageElements = stageLinks
        .map((link) => projectGallery.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (stageElements.length > 0) {
        document.documentElement.classList.add('has-project-gallery-motion');

        const setCurrentStage = (stage) => {
            const activeIndex = stageElements.indexOf(stage);

            stageLinks.forEach((link, index) => {
                const isCurrent = index === activeIndex;
                link.closest('li')?.classList.toggle('is-current', isCurrent);

                if (isCurrent) {
                    link.setAttribute('aria-current', 'step');
                } else {
                    link.removeAttribute('aria-current');
                }
            });

            stageElements.forEach((stageElement) => {
                stageElement.classList.toggle('is-active', stageElement === stage);
            });

            const progress = stageElements.length > 1 ? activeIndex / (stageElements.length - 1) : 0;
            projectGallery.querySelector('.project-gallery-path')?.style.setProperty('--gallery-progress', `${progress * 100}%`);
        };

        const stageObserver = new IntersectionObserver((entries) => {
            entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
                entry.target.classList.add('is-visible');
            });
        }, {
            threshold: 0.12,
            rootMargin: '-22% 0px -58% 0px',
        });

        stageElements.forEach((stage) => {
            stageObserver.observe(stage);
            stage.querySelectorAll('.project-gallery-image-card').forEach((card, index) => {
                card.style.setProperty('--gallery-card-delay', `${Math.min(index, 3) * 110}ms`);
            });
        });

        const cardObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.16,
            rootMargin: '0px 0px -8% 0px',
        });

        projectGallery.querySelectorAll('.project-gallery-image-card').forEach((card) => cardObserver.observe(card));

        let galleryProgressFrame = 0;
        const updateCurrentStage = () => {
            if (galleryProgressFrame) {
                return;
            }

            galleryProgressFrame = window.requestAnimationFrame(() => {
                galleryProgressFrame = 0;
                const activationLine = Math.min(window.innerHeight * 0.46, 320);
                let currentStage = stageElements[0];

                stageElements.forEach((stage) => {
                    if (stage.getBoundingClientRect().top <= activationLine) {
                        currentStage = stage;
                    }
                });

                setCurrentStage(currentStage);
            });
        };

        window.addEventListener('scroll', updateCurrentStage, { passive: true });
        window.addEventListener('resize', updateCurrentStage);
        updateCurrentStage();
    }
}
