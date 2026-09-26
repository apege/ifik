/**
 * LAA Sidebar JS (Standalone) (Ultra-Polished Left Layout)
 * Converted from React + Framer Motion (sidebar.tsx)
 * Features:
 * - Dynamic Full-Height SVG Bezier Morphing Curve (Duration: 0.85s, Ease: [0.76, 0, 0.24, 1])
 * - Staggered Kinetic Word/Letter Spans with Preserved Spacing
 * - Backdrop Blur Overlay with ESC key support
 */
(function (window, document) {
    'use strict';

    // Cubic Bezier Easing Helper matching Framer Motion [0.76, 0, 0.24, 1]
    function cubicBezier(t) {
        return (1 - t) * (1 - t) * (1 - t) * 0 +
               3 * (1 - t) * (1 - t) * t * 0.76 +
               3 * (1 - t) * t * t * 0.24 +
               t * t * t * 1;
    }

    class LaaSidebar {
        constructor(options = {}) {
            this.toggleBtnId = options.toggleBtnId || 'curvedSidebarToggle';
            this.panelId = options.panelId || 'curvedSidebarPanel';
            this.backdropId = options.backdropId || 'curvedSidebarBackdrop';
            this.pathId = options.pathId || 'curvedSidebarPath';
            this.svgId = options.svgId || 'curvedSidebarSvg';
            
            this.isDesktop = window.innerWidth >= 1024;
            this.isOpen = typeof options.defaultOpen !== 'undefined' ? options.defaultOpen : true; // Always default open (never hidden)
            this.animFrameId = null;
            this.animDuration = 750; // ms

            this.init();
        }

        init() {
            this.toggleBtn = document.getElementById(this.toggleBtnId);
            this.panel = document.getElementById(this.panelId);
            this.backdrop = document.getElementById(this.backdropId);
            this.path = document.getElementById(this.pathId);
            this.svg = document.getElementById(this.svgId);

            if (!this.panel || !this.toggleBtn) {
                return;
            }

            // Set initial open/collapsed state (Always default OPEN / not hidden)
            if (this.isOpen) {
                this.panel.classList.add('is-active');
                this.toggleBtn.classList.add('is-active');
                this.toggleBtn.setAttribute('aria-expanded', 'true');
                this.setPath(0);
                if (this.svg) this.svg.style.opacity = '0';
                // Push content on initial load (desktop only)
                if (window.innerWidth >= 1024) {
                    document.body.classList.add('laa-sidebar-pushed');
                }
            } else {
                this.panel.classList.remove('is-active');
                this.toggleBtn.classList.remove('is-active');
                this.toggleBtn.setAttribute('aria-expanded', 'false');
                if (this.backdrop) {
                    this.backdrop.classList.remove('is-active');
                }
                document.body.classList.remove('curved-sidebar-open');
                document.body.classList.remove('laa-sidebar-pushed');
                this.setPath(70);
                if (this.svg) this.svg.style.opacity = '0';
            }

            // Bind events
            this.toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });

            if (this.backdrop) {
                this.backdrop.addEventListener('click', () => this.close());
            }

            // Bind explicit close buttons inside panel
            const closeBtns = this.panel.querySelectorAll('.curved-sidebar-close-btn, #curvedSidebarCloseBtn');
            closeBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.close();
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            });

            window.addEventListener('resize', () => {
                this.updateSvgDimensions();
                if (this.isOpen) {
                    this.setPath(0);
                } else {
                    this.setPath(70);
                }
            });

            // Prepare letter spans for staggered kinetic wave
            this.initLetterSplit();

            // Inject content push wrapper (wraps all content below the navbar)
            this.setupContentWrapper();

            // Initial SVG path state
            this.updateSvgDimensions();
            this.setPath(this.isOpen ? 0 : 70);
        }

        updateSvgDimensions() {
            if (!this.svg) return;
            const h = window.innerHeight || document.documentElement.clientHeight || 900;
            this.svg.setAttribute('viewBox', `0 0 70 ${h}`);
        }

        initLetterSplit() {
            const headings = this.panel.querySelectorAll('.curved-nav-heading');
            headings.forEach(heading => {
                if (heading.dataset.split) return;
                heading.dataset.split = 'true';
                
                const rawText = heading.textContent.trim();
                const words = rawText.split(/\s+/);
                heading.innerHTML = '';

                let charCount = 0;
                words.forEach((word) => {
                    const wordSpan = document.createElement('span');
                    wordSpan.className = 'curved-nav-word';

                    word.split('').forEach((char) => {
                        const charSpan = document.createElement('span');
                        charSpan.className = 'curved-nav-letter';
                        charSpan.textContent = char;
                        charSpan.style.transitionDelay = `${charCount * 18}ms`;
                        wordSpan.appendChild(charSpan);
                        charCount++;
                    });

                    heading.appendChild(wordSpan);
                });
            });

            // Handle link click & smooth scrolling
            const links = this.panel.querySelectorAll('.curved-nav-item');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const href = link.getAttribute('href');
                    if (href && !href.startsWith('javascript:')) {
                        try {
                            const targetUrl = new URL(href, window.location.href);
                            // If it's a hash link on the current page
                            if (targetUrl.pathname === window.location.pathname && targetUrl.hash) {
                                e.preventDefault();
                                const hashKey = targetUrl.hash.replace(/^#/, '');
                                if (typeof window.switchDashboardTab === 'function') {
                                    window.switchDashboardTab(hashKey);
                                }
                                if (typeof window.scrollToSection === 'function') {
                                    window.scrollToSection(hashKey);
                                } else {
                                    const el = document.getElementById(hashKey) || document.getElementById('section-about') || document.getElementById('info_ruangan');
                                    if (el) {
                                        el.scrollIntoView({ behavior: 'smooth' });
                                    }
                                }
                                if (history.pushState) {
                                    history.pushState(null, '', targetUrl.hash);
                                } else {
                                    window.location.hash = targetUrl.hash;
                                }
                                if (typeof window.updateSidebarActiveTab === 'function') {
                                    window.updateSidebarActiveTab(targetUrl.hash);
                                }
                            }
                        } catch (err) {
                            // Fallback standard navigation
                        }
                    }
                });
            });
        }

        setPath(controlX) {
            if (!this.path) return;
            const h = window.innerHeight || document.documentElement.clientHeight || 900;
            const d = `M0 0 L0 ${h} Q${controlX} ${h / 2} 0 0`;
            this.path.setAttribute('d', d);
        }

        animateSvgCurve(fromX, toX, duration, onComplete) {
            if (this.animFrameId) {
                cancelAnimationFrame(this.animFrameId);
            }

            this.updateSvgDimensions();
            const startTime = performance.now();

            const step = (now) => {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = cubicBezier(progress);
                const currentX = fromX + (toX - fromX) * eased;

                this.setPath(currentX);

                if (progress < 1) {
                    this.animFrameId = requestAnimationFrame(step);
                } else {
                    this.animFrameId = null;
                    if (typeof onComplete === 'function') onComplete();
                }
            };

            this.animFrameId = requestAnimationFrame(step);
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            if (this.isOpen) return;
            this.isOpen = true;

            // Update DOM classes
            this.toggleBtn.classList.add('is-active');
            this.toggleBtn.setAttribute('aria-expanded', 'true');
            if (this.backdrop && window.innerWidth < 1024) {
                this.backdrop.classList.add('is-active');
            }
            if (window.innerWidth < 1024) {
                document.body.classList.add('curved-sidebar-open');
            }
            this.panel.classList.add('is-active');

            // Push page content to the right on desktop
            if (window.innerWidth >= 1024) {
                document.body.classList.add('laa-sidebar-pushed');
            }

            // Scroll inner menu to top
            const inner = this.panel ? this.panel.querySelector('.curved-sidebar-inner') : null;
            if (inner) inner.scrollTop = 0;

            if (this.svg) this.svg.style.opacity = '1';
            // Morph curve from bulging (70) to flat (0)
            this.animateSvgCurve(70, 0, 800, () => {
                if (this.svg) this.svg.style.opacity = '0';
            });
        }

        close() {
            if (!this.isOpen) return;
            this.isOpen = false;

            // Update DOM classes
            this.toggleBtn.classList.remove('is-active');
            this.toggleBtn.setAttribute('aria-expanded', 'false');
            if (this.backdrop) this.backdrop.classList.remove('is-active');
            document.body.classList.remove('curved-sidebar-open');
            document.body.classList.remove('laa-sidebar-pushed');
            this.panel.classList.remove('is-active');

            if (this.svg) this.svg.style.opacity = '1';
            // Morph curve from flat (0) back to bulging (70)
            this.animateSvgCurve(0, 70, 700, () => {
                if (this.svg) this.svg.style.opacity = '0';
            });
        }

        setupContentWrapper() {
            // Skip if wrapper already exists
            if (document.getElementById('laaMainContentWrapper')) return;

            // IDs/tags to keep directly in body (not wrapped)
            const skipIds = new Set([
                'curvedSidebarPanel',
                'curvedSidebarBackdrop',
                'curvedSidebarToggle',
                'laaMainContentWrapper'
            ]);
            // Keep non-visual head/script elements at body root level (don't wrap script/link/style tags)
            const skipTags = new Set(['SCRIPT', 'LINK', 'STYLE', 'META', 'SVG']);

            // Find all body children that should go into the wrapper
            const toWrap = Array.from(document.body.children).filter(el =>
                !skipIds.has(el.id) && !skipTags.has(el.tagName)
            );

            if (toWrap.length === 0) return;

            // Create wrapper div (plain full-width div, NO mx-auto)
            const wrapper = document.createElement('div');
            wrapper.id = 'laaMainContentWrapper';

            // Insert wrapper before the first element to be wrapped
            document.body.insertBefore(wrapper, toWrap[0]);

            // Move elements into wrapper
            toWrap.forEach(el => wrapper.appendChild(el));
        }
    }

    function initLaaSidebar() {
        if (document.getElementById('curvedSidebarPanel') && !window.laaSidebarInstance) {
            window.laaSidebarInstance = new LaaSidebar();
        }

        // Handle initial hash or hashchange for tabs/sections and update active state
        const updateSidebarActiveTab = (forcedHash) => {
            const currentHash = (forcedHash !== undefined && forcedHash !== null) ? forcedHash : (window.location.hash || '');
            const panel = document.getElementById('curvedSidebarPanel') || document.getElementById('laaSidebarPanel');
            if (!panel) return;
            const navItems = panel.querySelectorAll('.curved-nav-item');
            const isKoordinatorPage = window.location.pathname.includes('koordinatorta');
            const targetHash = currentHash || (isKoordinatorPage ? '#pendaftaran' : '');

            if (targetHash) {
                const cleanTarget = targetHash.startsWith('#') ? targetHash : '#' + targetHash;
                navItems.forEach(item => {
                    const href = item.getAttribute('href') || '';
                    if (href.includes('#')) {
                        if (href.endsWith(cleanTarget) || href.includes(cleanTarget)) {
                            item.classList.add('is-current');
                        } else {
                            item.classList.remove('is-current');
                        }
                    } else if (isKoordinatorPage && cleanTarget) {
                        item.classList.remove('is-current');
                    }
                });
            }
        };

        window.updateSidebarActiveTab = updateSidebarActiveTab;
        window.updateCurvedSidebarActive = updateSidebarActiveTab;

        const checkHash = () => {
            if (window.location.hash) {
                const hashKey = window.location.hash.replace(/^#/, '');
                if (typeof window.switchDashboardTab === 'function') {
                    window.switchDashboardTab(hashKey);
                }
            }
            updateSidebarActiveTab();
        };

        window.addEventListener('hashchange', checkHash);
        setTimeout(checkHash, 150);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLaaSidebar);
    } else {
        initLaaSidebar();
    }

})(window, document);

