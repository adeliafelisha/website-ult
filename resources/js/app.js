import './bootstrap';

const qs = (selector) => document.querySelector(selector);
const qsa = (selector) => document.querySelectorAll(selector);

qs('.nav-toggle')?.addEventListener('click', (event) => {
    const menu = qs('.nav-menu');
    menu.classList.toggle('open');
    event.currentTarget.setAttribute('aria-expanded', menu.classList.contains('open'));
});

qs('.search-toggle')?.addEventListener('click', () => {
    const drawer = qs('.search-drawer');
    drawer.classList.toggle('open');
    drawer.setAttribute('aria-hidden', !drawer.classList.contains('open'));
    if (drawer.classList.contains('open')) setTimeout(() => qs('#global-search')?.focus(), 100);
});

const accessibilityDefaults = {
    font: 2, contrast: false, dark: false, links: false, spacing: false,
    motion: false, images: false, dyslexia: false, cursor: false,
    lineHeight: 0, align: 0, saturation: 0,
};

let accessibility = { ...accessibilityDefaults };
try {
    accessibility = { ...accessibilityDefaults, ...JSON.parse(localStorage.getItem('ult-accessibility') || '{}') };
} catch (_) { /* Ignore malformed local settings. */ }

const bodyClassMap = {
    contrast: 'a11y-high-contrast', dark: 'a11y-dark', links: 'a11y-highlight-links',
    spacing: 'a11y-text-spacing', motion: 'reduce-motion', images: 'a11y-hide-images',
    dyslexia: 'a11y-dyslexia', cursor: 'a11y-big-cursor',
};

function applyAccessibility() {
    const root = document.documentElement;
    root.classList.remove('a11y-font-0', 'a11y-font-1', 'a11y-font-2', 'a11y-font-3', 'a11y-font-4', 'a11y-line-1', 'a11y-line-2', 'a11y-align-1', 'a11y-align-2', 'a11y-saturation-1', 'a11y-saturation-2');
    root.classList.add(`a11y-font-${accessibility.font}`);
    if (accessibility.lineHeight) root.classList.add(`a11y-line-${accessibility.lineHeight}`);
    if (accessibility.align) root.classList.add(`a11y-align-${accessibility.align}`);
    if (accessibility.saturation) root.classList.add(`a11y-saturation-${accessibility.saturation}`);

    Object.entries(bodyClassMap).forEach(([setting, className]) => document.body.classList.toggle(className, Boolean(accessibility[setting])));
    qs('meta[name="theme-color"]')?.setAttribute('content', accessibility.dark ? '#171217' : '#92278f');
    document.documentElement.style.colorScheme = accessibility.dark ? 'dark' : 'light';
    localStorage.setItem('ult-accessibility', JSON.stringify(accessibility));

    qsa('.a11y-option').forEach((button) => {
        const setting = button.dataset.a11y;
        let active = false;
        let level = '';
        if (setting === 'font-increase' || setting === 'font-decrease') {
            active = accessibility.font !== 2;
            level = `${Math.round((0.8 + accessibility.font * 0.1) * 100)}%`;
        } else if (setting === 'line-height') {
            active = accessibility.lineHeight > 0;
            level = accessibility.lineHeight ? `${accessibility.lineHeight}/2` : '';
        } else if (setting === 'align') {
            active = accessibility.align > 0;
            level = ['', 'Kiri', 'Tengah'][accessibility.align];
        } else if (setting === 'saturation') {
            active = accessibility.saturation > 0;
            level = ['', 'Rendah', 'Tinggi'][accessibility.saturation];
        } else {
            active = Boolean(accessibility[setting]);
        }
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', String(active));
        const indicator = button.querySelector('.setting-level');
        if (indicator) indicator.textContent = level;
    });

    const status = qs('.a11y-status');
    if (status) {
        status.textContent = accessibility.dark
            ? status.dataset.darkActive
            : status.dataset.lightActive;
    }
}

const panel = qs('.a11y-panel');
const backdrop = qs('.a11y-backdrop');
const toggle = qs('.a11y-toggle');

function openAccessibility() {
    panel?.classList.add('open');
    panel?.setAttribute('aria-hidden', 'false');
    toggle?.setAttribute('aria-expanded', 'true');
    if (backdrop) backdrop.hidden = false;
    document.body.classList.add('a11y-panel-open');
    qs('main')?.setAttribute('inert', '');
    qs('footer')?.setAttribute('inert', '');
    setTimeout(() => panel?.focus(), 50);
}

function closeAccessibility() {
    panel?.classList.remove('open');
    panel?.setAttribute('aria-hidden', 'true');
    toggle?.setAttribute('aria-expanded', 'false');
    if (backdrop) backdrop.hidden = true;
    document.body.classList.remove('a11y-panel-open');
    qs('main')?.removeAttribute('inert');
    qs('footer')?.removeAttribute('inert');
    toggle?.focus();
}

toggle?.addEventListener('click', () => panel?.classList.contains('open') ? closeAccessibility() : openAccessibility());
qs('.a11y-close')?.addEventListener('click', closeAccessibility);
backdrop?.addEventListener('click', closeAccessibility);

document.addEventListener('keydown', (event) => {
    if (event.ctrlKey && event.key.toLowerCase() === 'u') {
        event.preventDefault();
        panel?.classList.contains('open') ? closeAccessibility() : openAccessibility();
    }
    if (event.key === 'Escape' && panel?.classList.contains('open')) closeAccessibility();
    if (event.key === 'Tab' && panel?.classList.contains('open')) {
        const focusable = [...panel.querySelectorAll('button:not([disabled]), a[href], input, select, [tabindex]:not([tabindex="-1"])')];
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }
});

qsa('[data-a11y]').forEach((button) => button.addEventListener('click', () => {
    const setting = button.dataset.a11y;
    if (setting === 'reset') accessibility = { ...accessibilityDefaults };
    else if (setting === 'font-increase') accessibility.font = Math.min(4, accessibility.font + 1);
    else if (setting === 'font-decrease') accessibility.font = Math.max(0, accessibility.font - 1);
    else if (setting === 'line-height') accessibility.lineHeight = (accessibility.lineHeight + 1) % 3;
    else if (setting === 'align') accessibility.align = (accessibility.align + 1) % 3;
    else if (setting === 'saturation') accessibility.saturation = (accessibility.saturation + 1) % 3;
    else {
        accessibility[setting] = !accessibility[setting];
        if (setting === 'dark' && accessibility.dark) accessibility.contrast = false;
        if (setting === 'contrast' && accessibility.contrast) accessibility.dark = false;
    }
    applyAccessibility();
}));

applyAccessibility();

const scrollTopButton = qs('.scroll-top');
const syncScrollTopButton = () => scrollTopButton?.classList.toggle('visible', window.scrollY > 500);
window.addEventListener('scroll', syncScrollTopButton, { passive: true });
scrollTopButton?.addEventListener('click', () => window.scrollTo({
    top: 0,
    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
}));
syncScrollTopButton();

const profileLinks = qsa('.profile-subnav a');
if (profileLinks.length) {
    const sections = [...profileLinks].map((link) => qs(link.getAttribute('href'))).filter(Boolean);
    const updateProfileNavigation = () => {
        const active = [...sections].reverse().find((section) => section.getBoundingClientRect().top <= 170) || sections[0];
        profileLinks.forEach((link) => link.classList.toggle('active', link.getAttribute('href') === `#${active?.id}`));
    };
    window.addEventListener('scroll', updateProfileNavigation, { passive: true });
    updateProfileNavigation();
}

qsa('.external-link[data-track]').forEach((link) => link.addEventListener('click', () => {
    fetch('/analytics/outbound', {
        method: 'POST', keepalive: true,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': qs('meta[name="csrf-token"]')?.content },
        body: JSON.stringify({ label: link.dataset.track, url: link.href, source: location.pathname }),
    }).catch(() => {});
}));

const articleCarousel = qs('[data-article-carousel]');
if (articleCarousel) {
    const track = articleCarousel.querySelector('.article-carousel-track');
    const slides = [...articleCarousel.querySelectorAll('.article-slide')];
    const previous = qs('.article-prev');
    const next = qs('.article-next');
    const current = qs('[data-carousel-current]');

    const activeSlide = () => {
        if (!slides.length) return 0;
        return slides.reduce((closest, slide, index) => (
            Math.abs((slide.offsetLeft - track.offsetLeft) - track.scrollLeft) < Math.abs((slides[closest].offsetLeft - track.offsetLeft) - track.scrollLeft) ? index : closest
        ), 0);
    };
    const updateCarousel = () => {
        const index = activeSlide();
        if (current) current.textContent = String(index + 1);
        if (previous) previous.disabled = index === 0;
        if (next) next.disabled = index === slides.length - 1;
    };
    const go = (direction) => {
        const target = Math.max(0, Math.min(slides.length - 1, activeSlide() + direction));
        const slide = slides[target];
        if (slide) track.scrollTo({ left: slide.offsetLeft - track.offsetLeft, behavior: accessibility.motion ? 'auto' : 'smooth' });
    };
    previous?.addEventListener('click', () => go(-1));
    next?.addEventListener('click', () => go(1));
    track?.addEventListener('scroll', updateCarousel, { passive: true });
    window.addEventListener('resize', updateCarousel);
    updateCarousel();
}

qs('[data-satisfaction-year]')?.addEventListener('change', (event) => {
    qsa('[data-satisfaction-panel]').forEach((panelElement) => {
        panelElement.hidden = panelElement.dataset.satisfactionPanel !== event.currentTarget.value;
    });
});

const revealSelectors = [
    'main .section-head', 'main .page-hero .container', 'main .profile-hero-grid > *',
    'main .category-card', 'main .service-card', 'main .article-card',
    'main .split > *', 'main .timeline article', 'main .purpose-grid article',
    'main .pasti-grid article', 'main .satisfaction-grid article',
    'main .legal-grid > *', 'main .team-grid > *', 'main .profile-gallery figure',
    'main .contact-card', 'main .finder-panel', 'main .results-summary',
    'main .faq-layout > *', 'main .quick-links .link-grid a', 'main .service-category-card',
    'main .category-hero .container', 'main .category-directory > *',
    'main .service-detail-directory > *', 'main .detail-grid > *',
    'main .detail-content > *', 'main .article-detail > *',
];

const revealElements = [...new Set(revealSelectors.flatMap((selector) => [...qsa(selector)]))];
revealElements.forEach((element, index) => {
    element.classList.add('scroll-reveal');
    element.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 70}ms`);
    if (element.matches('.split > :first-child, .legal-grid > :first-child, .team-grid > :first-child')) element.classList.add('reveal-from-left');
    if (element.matches('.split > :last-child, .legal-grid > :last-child, .team-grid > :last-child')) element.classList.add('reveal-from-right');
});

if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -45px' });
    revealElements.forEach((element) => revealObserver.observe(element));
} else {
    revealElements.forEach((element) => element.classList.add('is-revealed'));
}
